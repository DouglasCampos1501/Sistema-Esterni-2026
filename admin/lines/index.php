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
$translatableFields = ['name', 'description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
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
            $dup = db()->prepare('SELECT id FROM product_lines WHERE slug = ? AND id != ?');
            $dup->execute([$slug, $id]);
            if ($dup->fetch()) {
                $errors[] = 'Já existe uma linha com esse slug.';
            }
        }

        $imageId = null;
        if (!$errors && !empty($_FILES['featured_image']['name'])) {
            try {
                $imageId = store_uploaded_image($_FILES['featured_image'], $name);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            if ($id > 0) {
                if ($imageId) {
                    db()->prepare('UPDATE product_lines SET name=?, slug=?, description=?, featured_image_id=? WHERE id=?')
                        ->execute([$name, $slug, $description, $imageId, $id]);
                } else {
                    db()->prepare('UPDATE product_lines SET name=?, slug=?, description=? WHERE id=?')
                        ->execute([$name, $slug, $description, $id]);
                }
                log_activity('update', 'product_line', $id, $name);
                flash_set('Linha atualizada.');
            } else {
                $maxOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) FROM product_lines')->fetchColumn();
                $stmt = db()->prepare('INSERT INTO product_lines (slug, name, description, featured_image_id, sort_order, active) VALUES (?,?,?,?,?,1)');
                $stmt->execute([$slug, $name, $description, $imageId, $maxOrder + 1]);
                $id = (int) db()->lastInsertId();
                log_activity('create', 'product_line', $id, $name);
                flash_set('Linha criada.');
            }
            save_entity_translations('product_line_translations', 'line_id', $id, $_POST['translations'] ?? [], $translatableFields);
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/lines/index.php');
    }

    if ($action === 'toggle_active') {
        db()->prepare('UPDATE product_lines SET active = NOT active WHERE id = ?')->execute([(int) $_POST['id']]);
        redirect('/admin/lines/index.php');
    }

    if ($action === 'move') {
        $id = (int) $_POST['id'];
        $dir = $_POST['dir'] ?? '';
        $rows = db()->query('SELECT id, sort_order FROM product_lines ORDER BY sort_order, id')->fetchAll();
        $ids = array_column($rows, 'id');
        $pos = array_search($id, $ids, true);
        if ($pos !== false) {
            $swapWith = $dir === 'up' ? $pos - 1 : $pos + 1;
            if (isset($ids[$swapWith])) {
                db()->prepare('UPDATE product_lines SET sort_order = ? WHERE id = ?')->execute([$rows[$swapWith]['sort_order'], $rows[$pos]['id']]);
                db()->prepare('UPDATE product_lines SET sort_order = ? WHERE id = ?')->execute([$rows[$pos]['sort_order'], $rows[$swapWith]['id']]);
            }
        }
        redirect('/admin/lines/index.php');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        $stmt = db()->prepare('SELECT COUNT(*) FROM products WHERE line_id = ?');
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            flash_set('Não é possível excluir: existem produtos cadastrados nessa linha.', 'error');
        } else {
            db()->prepare('DELETE FROM product_lines WHERE id = ?')->execute([$id]);
            log_activity('delete', 'product_line', $id, '');
            flash_set('Linha removida.');
        }
        redirect('/admin/lines/index.php');
    }
}

$pageTitle = 'Linhas';
require __DIR__ . '/../partials/layout-top.php';

$lines = db()->query('SELECT l.*, m.path AS image_path FROM product_lines l LEFT JOIN media m ON m.id = l.featured_image_id ORDER BY l.sort_order, l.id')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editTranslations = [];
if ($editId > 0) {
    foreach ($lines as $l) {
        if ((int) $l['id'] === $editId) {
            $editing = $l;
            break;
        }
    }
    if ($editing) {
        $editTranslations = get_entity_translations('product_line_translations', 'line_id', $editId);
    }
}
?>
<style>
.pl-table img { width: 70px; height: 32px; object-fit: cover; border-radius: 4px; }
.pl-order-btns { display: flex; flex-direction: column; gap: .15rem; }
.pl-order-btns button { border: 1px solid #d0d5dd; background: #fff; border-radius: 4px; cursor: pointer; line-height: 1; padding: .1rem .35rem; }
</style>

<div class="content-header">
    <h1>Linhas de Produtos</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    Linhas são as coleções de design da Esterni (Misan, Vega, S.Park...). Cada produto pertence a exatamente
    uma linha + um <a href="/admin/types/index.php">tipo</a>.
</p>

<table class="admin-table pl-table" style="margin-bottom:2rem;">
    <thead><tr><th></th><th></th><th>Nome</th><th>Slug</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($lines as $i => $line): ?>
        <tr>
            <td>
                <div class="pl-order-btns">
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $line['id'] ?>"><input type="hidden" name="dir" value="up">
                        <button type="submit" <?= $i === 0 ? 'disabled' : '' ?>>▲</button>
                    </form>
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $line['id'] ?>"><input type="hidden" name="dir" value="down">
                        <button type="submit" <?= $i === count($lines) - 1 ? 'disabled' : '' ?>>▼</button>
                    </form>
                </div>
            </td>
            <td><?php if ($line['image_path']): ?><img src="<?= e($line['image_path']) ?>" alt=""><?php endif; ?></td>
            <td><strong><?= e($line['name']) ?></strong></td>
            <td><code><?= e($line['slug']) ?></code></td>
            <td>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_active">
                    <input type="hidden" name="id" value="<?= (int) $line['id'] ?>">
                    <button type="submit" class="badge badge-<?= $line['active'] ? 'published' : 'draft' ?>" style="border:none;cursor:pointer;">
                        <?= $line['active'] ? 'ativa' : 'inativa' ?>
                    </button>
                </form>
            </td>
            <td>
                <a href="/admin/lines/index.php?edit=<?= (int) $line['id'] ?>">Editar</a> &nbsp;
                <a href="/linhas/<?= e($line['slug']) ?>/" target="_blank">Ver</a> &nbsp;
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir esta linha?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $line['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;"><?= $editing ? 'Editar linha' : 'Nova linha' ?></h2>
<form method="post" class="admin-form" enctype="multipart/form-data" style="max-width:640px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

    <label>Nome (pt-BR)
        <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
    </label>
    <label>Slug (usado na URL /linhas/slug/ — deixe em branco pra gerar a partir do nome)
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="ex: misan">
    </label>
    <label>Descrição (pt-BR)
        <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
    </label>
    <label>Imagem de destaque
        <input type="file" name="featured_image" accept="image/png,image/jpeg,image/webp">
        <?php if (!empty($editing['image_path'])): ?>
            <img src="<?= e($editing['image_path']) ?>" alt="" style="display:block;margin-top:.5rem;max-width:220px;">
        <?php endif; ?>
    </label>

    <h3 style="font-size:.95rem;margin:1.5rem 0 .5rem;">Traduções (opcional)</h3>
    <p class="i18n-field-hint" style="margin:0 0 .75rem;">O nome da linha geralmente não é traduzido (é nome próprio) — a tradução é mais útil pra descrição.</p>
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
                <label>Descrição
                    <textarea name="translations[<?= e($lang['code']) ?>][description]" rows="3"><?= e($editTranslations[$lang['code']]['description'] ?? '') ?></textarea>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1rem;"><?= $editing ? 'Salvar alterações' : 'Adicionar linha' ?></button>
    <?php if ($editing): ?><a href="/admin/lines/index.php" class="btn" style="background:#eee;color:#333;">Cancelar</a><?php endif; ?>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
