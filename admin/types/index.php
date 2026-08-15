<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/i18n.php';
require_once __DIR__ . '/../../includes/media.php';
require_once __DIR__ . '/../../includes/activity.php';

$languages = get_languages();
$translatableFields = ['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors[] = 'O nome é obrigatório.';
        }
        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        }
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors[] = 'Slug inválido — use só letras minúsculas, números e hífen.';
        }
        if (!$errors) {
            $dup = db()->prepare('SELECT id FROM product_types WHERE slug = ? AND id != ?');
            $dup->execute([$slug, $id]);
            if ($dup->fetch()) {
                $errors[] = 'Já existe um tipo com esse slug.';
            }
        }

        $imageId = null;
        if (!$errors && !empty($_FILES['icon_image']['name'])) {
            try {
                $imageId = store_uploaded_image($_FILES['icon_image'], $name);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            if ($id > 0) {
                if ($imageId) {
                    db()->prepare('UPDATE product_types SET name=?, slug=?, icon_image_id=? WHERE id=?')->execute([$name, $slug, $imageId, $id]);
                } else {
                    db()->prepare('UPDATE product_types SET name=?, slug=? WHERE id=?')->execute([$name, $slug, $id]);
                }
                log_activity('update', 'product_type', $id, $name);
                flash_set('Tipo atualizado.');
            } else {
                $maxOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) FROM product_types')->fetchColumn();
                $stmt = db()->prepare('INSERT INTO product_types (slug, name, icon_image_id, sort_order, active) VALUES (?,?,?,?,1)');
                $stmt->execute([$slug, $name, $imageId, $maxOrder + 1]);
                $id = (int) db()->lastInsertId();
                log_activity('create', 'product_type', $id, $name);
                flash_set('Tipo criado.');
            }
            save_entity_translations('product_type_translations', 'type_id', $id, $_POST['translations'] ?? [], $translatableFields);
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/types/index.php');
    }

    if ($action === 'toggle_active') {
        db()->prepare('UPDATE product_types SET active = NOT active WHERE id = ?')->execute([(int) $_POST['id']]);
        redirect('/admin/types/index.php');
    }

    if ($action === 'move') {
        $id = (int) $_POST['id'];
        $dir = $_POST['dir'] ?? '';
        $rows = db()->query('SELECT id, sort_order FROM product_types ORDER BY sort_order, id')->fetchAll();
        $ids = array_column($rows, 'id');
        $pos = array_search($id, $ids, true);
        if ($pos !== false) {
            $swapWith = $dir === 'up' ? $pos - 1 : $pos + 1;
            if (isset($ids[$swapWith])) {
                db()->prepare('UPDATE product_types SET sort_order = ? WHERE id = ?')->execute([$rows[$swapWith]['sort_order'], $rows[$pos]['id']]);
                db()->prepare('UPDATE product_types SET sort_order = ? WHERE id = ?')->execute([$rows[$pos]['sort_order'], $rows[$swapWith]['id']]);
            }
        }
        redirect('/admin/types/index.php');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        $stmt = db()->prepare('SELECT COUNT(*) FROM products WHERE type_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            flash_set('Não é possível excluir: existem produtos cadastrados nesse tipo.', 'error');
        } else {
            db()->prepare('DELETE FROM product_types WHERE id = ?')->execute([$id]);
            log_activity('delete', 'product_type', $id, '');
            flash_set('Tipo removido.');
        }
        redirect('/admin/types/index.php');
    }
}

$pageTitle = 'Tipos';
require __DIR__ . '/../partials/layout-top.php';

$types = db()->query('SELECT t.*, m.path AS image_path FROM product_types t LEFT JOIN media m ON m.id = t.icon_image_id ORDER BY t.sort_order, t.id')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editTranslations = [];
if ($editId > 0) {
    foreach ($types as $t) {
        if ((int) $t['id'] === $editId) {
            $editing = $t;
            break;
        }
    }
    if ($editing) {
        $editTranslations = get_entity_translations('product_type_translations', 'type_id', $editId);
    }
}
?>
<style>
.pt-table img { width: 60px; height: 40px; object-fit: cover; border-radius: 4px; }
.pt-order-btns { display: flex; flex-direction: column; gap: .15rem; }
.pt-order-btns button { border: 1px solid #d0d5dd; background: #fff; border-radius: 4px; cursor: pointer; line-height: 1; padding: .1rem .35rem; }
</style>

<div class="content-header">
    <h1>Tipos de Produto</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    Tipos são as categorias funcionais (banco, lixeira, bicicletário...). Cada produto pertence a exatamente
    uma <a href="/admin/lines/index.php">linha</a> + um tipo.
</p>

<table class="admin-table pt-table" style="margin-bottom:2rem;">
    <thead><tr><th></th><th></th><th>Nome</th><th>Slug</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($types as $i => $type): ?>
        <tr>
            <td>
                <div class="pt-order-btns">
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $type['id'] ?>"><input type="hidden" name="dir" value="up">
                        <button type="submit" <?= $i === 0 ? 'disabled' : '' ?>>▲</button>
                    </form>
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $type['id'] ?>"><input type="hidden" name="dir" value="down">
                        <button type="submit" <?= $i === count($types) - 1 ? 'disabled' : '' ?>>▼</button>
                    </form>
                </div>
            </td>
            <td><?php if ($type['image_path']): ?><img src="<?= e($type['image_path']) ?>" alt=""><?php endif; ?></td>
            <td><strong><?= e($type['name']) ?></strong></td>
            <td><code><?= e($type['slug']) ?></code></td>
            <td>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_active">
                    <input type="hidden" name="id" value="<?= (int) $type['id'] ?>">
                    <button type="submit" class="badge badge-<?= $type['active'] ? 'published' : 'draft' ?>" style="border:none;cursor:pointer;">
                        <?= $type['active'] ? 'ativo' : 'inativo' ?>
                    </button>
                </form>
            </td>
            <td>
                <a href="/admin/types/index.php?edit=<?= (int) $type['id'] ?>">Editar</a> &nbsp;
                <a href="/tipos/<?= e($type['slug']) ?>/" target="_blank">Ver</a> &nbsp;
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir este tipo?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $type['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;"><?= $editing ? 'Editar tipo' : 'Novo tipo' ?></h2>
<form method="post" class="admin-form" enctype="multipart/form-data" style="max-width:640px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

    <label>Nome (pt-BR)
        <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
    </label>
    <label>Slug (usado na URL /tipos/slug/ — deixe em branco pra gerar a partir do nome)
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="ex: banco">
    </label>
    <label>Imagem/ícone
        <input type="file" name="icon_image" accept="image/png,image/jpeg,image/webp">
        <?php if (!empty($editing['image_path'])): ?>
            <img src="<?= e($editing['image_path']) ?>" alt="" style="display:block;margin-top:.5rem;max-width:160px;">
        <?php endif; ?>
    </label>

    <h3 style="font-size:.95rem;margin:1.5rem 0 .5rem;">Traduções</h3>
    <div class="i18n-tabs">
        <div class="i18n-tab-buttons">
            <?php foreach ($languages as $i => $lang): ?>
                <?php if ($lang['is_default']) continue; ?>
                <button type="button" class="i18n-tab-btn<?= $i === 1 ? ' active' : '' ?>" data-lang="<?= e($lang['code']) ?>">
                    <?php if ($lang['flag_image']): ?><img src="<?= e($lang['flag_image']) ?>" alt=""><?php endif; ?>
                    <span class="i18n-tab-name"><?= e($lang['code']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($languages as $i => $lang): ?>
            <?php if ($lang['is_default']) continue; ?>
            <div class="i18n-tab-panel" data-lang="<?= e($lang['code']) ?>" <?= $i === 1 ? '' : 'hidden' ?>>
                <label>Nome
                    <input type="text" name="translations[<?= e($lang['code']) ?>][name]" value="<?= e($editTranslations[$lang['code']]['name'] ?? '') ?>">
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1rem;"><?= $editing ? 'Salvar alterações' : 'Adicionar tipo' ?></button>
    <?php if ($editing): ?><a href="/admin/types/index.php" class="btn" style="background:#eee;color:#333;">Cancelar</a><?php endif; ?>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
