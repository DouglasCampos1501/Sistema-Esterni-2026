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
$translatableFields = ['name', 'summary', 'description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $lineId = (int) ($_POST['line_id'] ?? 0);
        $typeId = (int) ($_POST['type_id'] ?? 0);
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
        $errors = [];

        if ($name === '') {
            $errors[] = 'O nome é obrigatório.';
        }
        if ($lineId <= 0) {
            $errors[] = 'Selecione a linha.';
        }
        if ($typeId <= 0) {
            $errors[] = 'Selecione o tipo.';
        }
        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        }
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors[] = 'Slug inválido — use só letras minúsculas, números e hífen.';
        }
        if (!$errors) {
            $dup = db()->prepare('SELECT id FROM products WHERE slug = ? AND id != ?');
            $dup->execute([$slug, $id]);
            if ($dup->fetch()) {
                $errors[] = 'Já existe um produto com esse slug.';
            }
        }

        $featuredId = null;
        $dimensionsId = null;
        if (!$errors) {
            try {
                if (!empty($_FILES['featured_image']['name'])) {
                    $featuredId = store_uploaded_image($_FILES['featured_image'], $name);
                }
                if (!empty($_FILES['dimensions_image']['name'])) {
                    $dimensionsId = store_uploaded_image($_FILES['dimensions_image'], $name . ' - dimensões');
                }
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            if ($id > 0) {
                $sets = ['name=?', 'slug=?', 'line_id=?', 'type_id=?', 'summary=?', 'description=?', 'status=?'];
                $params = [$name, $slug, $lineId, $typeId, $summary, $description, $status];
                if ($featuredId) {
                    $sets[] = 'featured_image_id=?';
                    $params[] = $featuredId;
                }
                if ($dimensionsId) {
                    $sets[] = 'dimensions_image_id=?';
                    $params[] = $dimensionsId;
                }
                $params[] = $id;
                db()->prepare('UPDATE products SET ' . implode(', ', $sets) . ' WHERE id=?')->execute($params);
                log_activity('update', 'product', $id, $name);
                flash_set('Produto atualizado.');
            } else {
                $maxOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) FROM products')->fetchColumn();
                $stmt = db()->prepare(
                    'INSERT INTO products (line_id, type_id, slug, name, summary, description, featured_image_id, dimensions_image_id, status, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([$lineId, $typeId, $slug, $name, $summary, $description, $featuredId, $dimensionsId, $status, $maxOrder + 1]);
                $id = (int) db()->lastInsertId();
                log_activity('create', 'product', $id, $name);
                flash_set('Produto criado.');
            }
            save_entity_translations('product_translations', 'product_id', $id, $_POST['translations'] ?? [], $translatableFields);
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/products/index.php' . ($id ? '?edit=' . $id : ''));
    }

    if ($action === 'toggle_status') {
        $id = (int) $_POST['id'];
        db()->prepare("UPDATE products SET status = IF(status='published','draft','published') WHERE id = ?")->execute([$id]);
        redirect('/admin/products/index.php');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        log_activity('delete', 'product', $id, '');
        flash_set('Produto removido.');
        redirect('/admin/products/index.php');
    }
}

$pageTitle = 'Produtos';
require __DIR__ . '/../partials/layout-top.php';

$lines = db()->query('SELECT id, name, slug FROM product_lines ORDER BY sort_order, id')->fetchAll();
$types = db()->query('SELECT id, name, slug FROM product_types ORDER BY sort_order, id')->fetchAll();
$linesById = array_column($lines, null, 'id');
$typesById = array_column($types, null, 'id');

$filterLine = (int) ($_GET['line'] ?? 0);
$filterType = (int) ($_GET['type'] ?? 0);

$sql = 'SELECT p.*, m.path AS image_path FROM products p LEFT JOIN media m ON m.id = p.featured_image_id WHERE 1=1';
$params = [];
if ($filterLine) {
    $sql .= ' AND p.line_id = ?';
    $params[] = $filterLine;
}
if ($filterType) {
    $sql .= ' AND p.type_id = ?';
    $params[] = $filterType;
}
$sql .= ' ORDER BY p.line_id, p.sort_order, p.id';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editTranslations = [];
if ($editId > 0) {
    $stmt = db()->prepare('SELECT p.*, m.path AS image_path, d.path AS dim_path FROM products p LEFT JOIN media m ON m.id = p.featured_image_id LEFT JOIN media d ON d.id = p.dimensions_image_id WHERE p.id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch() ?: null;
    if ($editing) {
        $editTranslations = get_entity_translations('product_translations', 'product_id', $editId);
    }
}
?>
<style>
.pp-table img { width: 50px; height: 50px; object-fit: contain; border-radius: 4px; background: #f4f4f4; }
.pp-filters { display: flex; gap: 1rem; margin: -0.5rem 0 1rem; }
.pp-filters select { padding: .4rem .6rem; border-radius: 6px; border: 1px solid #d0d5dd; }
</style>

<div class="content-header">
    <h1>Produtos</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    <?= count($products) ?> produto(s)<?= $filterLine || $filterType ? ' (filtrado)' : '' ?> —
    cada produto pertence a uma <a href="/admin/lines/index.php">linha</a> + um <a href="/admin/types/index.php">tipo</a>.
</p>

<form method="get" class="pp-filters">
    <select name="line" onchange="this.form.submit()">
        <option value="">Todas as linhas</option>
        <?php foreach ($lines as $l): ?>
            <option value="<?= (int) $l['id'] ?>" <?= $filterLine === (int) $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="type" onchange="this.form.submit()">
        <option value="">Todos os tipos</option>
        <?php foreach ($types as $t): ?>
            <option value="<?= (int) $t['id'] ?>" <?= $filterType === (int) $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<table class="admin-table pp-table" style="margin-bottom:2rem;">
    <thead><tr><th></th><th>Nome</th><th>Linha</th><th>Tipo</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" alt=""><?php endif; ?></td>
            <td><strong><?= e($p['name']) ?></strong></td>
            <td><?= e($linesById[$p['line_id']]['name'] ?? '—') ?></td>
            <td><?= e($typesById[$p['type_id']]['name'] ?? '—') ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="badge badge-<?= $p['status'] === 'published' ? 'published' : 'draft' ?>" style="border:none;cursor:pointer;">
                        <?= $p['status'] === 'published' ? 'publicado' : 'rascunho' ?>
                    </button>
                </form>
            </td>
            <td>
                <a href="/admin/products/index.php?edit=<?= (int) $p['id'] ?>">Editar</a> &nbsp;
                <a href="/produtos/<?= e($p['slug']) ?>/" target="_blank">Ver</a> &nbsp;
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir este produto?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$products): ?>
        <tr><td colspan="6" style="color:#98a2b3;">Nenhum produto encontrado.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;"><?= $editing ? 'Editar produto' : 'Novo produto' ?></h2>
<form method="post" class="admin-form" enctype="multipart/form-data" style="max-width:640px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

    <label>Nome (pt-BR)
        <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>">
    </label>
    <label>Slug (usado na URL /produtos/slug/)
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>" placeholder="ex: banco-misan">
    </label>
    <label>Linha
        <select name="line_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($lines as $l): ?>
                <option value="<?= (int) $l['id'] ?>" <?= (int) ($editing['line_id'] ?? 0) === (int) $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Tipo
        <select name="type_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($types as $t): ?>
                <option value="<?= (int) $t['id'] ?>" <?= (int) ($editing['type_id'] ?? 0) === (int) $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Resumo curto (opcional, usado em listagens)
        <input type="text" name="summary" value="<?= e($editing['summary'] ?? '') ?>">
    </label>
    <label>Descrição (materiais, acabamento, fixação...) — aceita HTML simples
        <textarea name="description" rows="6"><?= e($editing['description'] ?? '') ?></textarea>
    </label>
    <label>Imagem de destaque
        <input type="file" name="featured_image" accept="image/png,image/jpeg,image/webp">
        <?php if (!empty($editing['image_path'])): ?><img src="<?= e($editing['image_path']) ?>" alt="" style="display:block;margin-top:.5rem;max-width:160px;"><?php endif; ?>
    </label>
    <label>Desenho técnico "Dimensões e Medidas"
        <input type="file" name="dimensions_image" accept="image/png,image/jpeg,image/webp">
        <?php if (!empty($editing['dim_path'])): ?><img src="<?= e($editing['dim_path']) ?>" alt="" style="display:block;margin-top:.5rem;max-width:160px;"><?php endif; ?>
    </label>
    <label>Status
        <select name="status">
            <option value="draft" <?= ($editing['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
            <option value="published" <?= ($editing['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
        </select>
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
                <p class="i18n-field-hint">Deixe em branco pra usar o texto em português.</p>
                <label>Nome
                    <input type="text" name="translations[<?= e($lang['code']) ?>][name]" value="<?= e($editTranslations[$lang['code']]['name'] ?? '') ?>">
                </label>
                <label>Resumo
                    <input type="text" name="translations[<?= e($lang['code']) ?>][summary]" value="<?= e($editTranslations[$lang['code']]['summary'] ?? '') ?>">
                </label>
                <label>Descrição
                    <textarea name="translations[<?= e($lang['code']) ?>][description]" rows="5"><?= e($editTranslations[$lang['code']]['description'] ?? '') ?></textarea>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1rem;"><?= $editing ? 'Salvar alterações' : 'Adicionar produto' ?></button>
    <?php if ($editing): ?><a href="/admin/products/index.php" class="btn" style="background:#eee;color:#333;">Cancelar</a><?php endif; ?>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
