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
$translatableFields = ['title', 'excerpt', 'content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
        $publishedAt = trim($_POST['published_at'] ?? '') ?: date('Y-m-d H:i:s');
        $errors = [];

        if ($title === '') {
            $errors[] = 'O título é obrigatório.';
        }
        if ($slug === '') {
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        }
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $errors[] = 'Slug inválido — use só letras minúsculas, números e hífen.';
        }
        if (!$errors) {
            $dup = db()->prepare('SELECT id FROM posts WHERE slug = ? AND id != ?');
            $dup->execute([$slug, $id]);
            if ($dup->fetch()) {
                $errors[] = 'Já existe uma notícia com esse slug.';
            }
        }

        $imageId = null;
        if (!$errors && !empty($_FILES['featured_image']['name'])) {
            try {
                $imageId = store_uploaded_image($_FILES['featured_image'], $title);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            if ($id > 0) {
                $sets = ['title=?', 'slug=?', 'excerpt=?', 'content=?', 'status=?', 'published_at=?'];
                $params = [$title, $slug, $excerpt, $content, $status, $publishedAt];
                if ($imageId) {
                    $sets[] = 'featured_image_id=?';
                    $params[] = $imageId;
                }
                $params[] = $id;
                db()->prepare('UPDATE posts SET ' . implode(', ', $sets) . ' WHERE id=?')->execute($params);
                log_activity('update', 'post', $id, $title);
                flash_set('Notícia atualizada.');
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO posts (slug, title, excerpt, content, featured_image_id, status, published_at) VALUES (?,?,?,?,?,?,?)'
                );
                $stmt->execute([$slug, $title, $excerpt, $content, $imageId, $status, $publishedAt]);
                $id = (int) db()->lastInsertId();
                log_activity('create', 'post', $id, $title);
                flash_set('Notícia criada.');
            }
            save_entity_translations('post_translations', 'post_id', $id, $_POST['translations'] ?? [], $translatableFields);
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/posts/index.php' . ($id ? '?edit=' . $id : ''));
    }

    if ($action === 'toggle_status') {
        $id = (int) $_POST['id'];
        db()->prepare("UPDATE posts SET status = IF(status='published','draft','published') WHERE id = ?")->execute([$id]);
        redirect('/admin/posts/index.php');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        db()->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        log_activity('delete', 'post', $id, '');
        flash_set('Notícia removida.');
        redirect('/admin/posts/index.php');
    }
}

$pageTitle = 'Notícias';
require __DIR__ . '/../partials/layout-top.php';

$posts = db()->query('SELECT p.*, m.path AS image_path FROM posts p LEFT JOIN media m ON m.id = p.featured_image_id ORDER BY p.published_at DESC')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editTranslations = [];
if ($editId > 0) {
    foreach ($posts as $p) {
        if ((int) $p['id'] === $editId) {
            $editing = $p;
            break;
        }
    }
    if ($editing) {
        $editTranslations = get_entity_translations('post_translations', 'post_id', $editId);
    }
}
?>
<style>
.pn-table img { width: 60px; height: 40px; object-fit: cover; border-radius: 4px; }
</style>

<div class="content-header">
    <h1>Notícias</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;"><?= count($posts) ?> notícia(s) cadastrada(s).</p>

<table class="admin-table pn-table" style="margin-bottom:2rem;">
    <thead><tr><th></th><th>Título</th><th>Data</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
        <tr>
            <td><?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" alt=""><?php endif; ?></td>
            <td><strong><?= e($p['title']) ?></strong></td>
            <td><?= e($p['published_at'] ? date('d/m/Y', strtotime($p['published_at'])) : '—') ?></td>
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
                <a href="/admin/posts/index.php?edit=<?= (int) $p['id'] ?>">Editar</a> &nbsp;
                <a href="/noticias/<?= e($p['slug']) ?>/" target="_blank">Ver</a> &nbsp;
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir esta notícia?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$posts): ?>
        <tr><td colspan="5" style="color:#98a2b3;">Nenhuma notícia cadastrada ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;"><?= $editing ? 'Editar notícia' : 'Nova notícia' ?></h2>
<form method="post" class="admin-form" enctype="multipart/form-data" style="max-width:640px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

    <label>Título (pt-BR)
        <input type="text" name="title" required value="<?= e($editing['title'] ?? '') ?>">
    </label>
    <label>Slug (usado na URL /noticias/slug/)
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>">
    </label>
    <label>Resumo (aparece nas listagens)
        <textarea name="excerpt" rows="2"><?= e($editing['excerpt'] ?? '') ?></textarea>
    </label>
    <label>Conteúdo — aceita HTML simples (parágrafos)
        <textarea name="content" rows="8"><?= e($editing['content'] ?? '') ?></textarea>
    </label>
    <label>Imagem de destaque
        <input type="file" name="featured_image" accept="image/png,image/jpeg,image/webp">
        <?php if (!empty($editing['image_path'])): ?><img src="<?= e($editing['image_path']) ?>" alt="" style="display:block;margin-top:.5rem;max-width:220px;"><?php endif; ?>
    </label>
    <label>Data de publicação
        <input type="datetime-local" name="published_at" value="<?= e($editing['published_at'] ? str_replace(' ', 'T', substr($editing['published_at'], 0, 16)) : '') ?>">
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
                <label>Título
                    <input type="text" name="translations[<?= e($lang['code']) ?>][title]" value="<?= e($editTranslations[$lang['code']]['title'] ?? '') ?>">
                </label>
                <label>Resumo
                    <textarea name="translations[<?= e($lang['code']) ?>][excerpt]" rows="2"><?= e($editTranslations[$lang['code']]['excerpt'] ?? '') ?></textarea>
                </label>
                <label>Conteúdo
                    <textarea name="translations[<?= e($lang['code']) ?>][content]" rows="6"><?= e($editTranslations[$lang['code']]['content'] ?? '') ?></textarea>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1rem;"><?= $editing ? 'Salvar alterações' : 'Adicionar notícia' ?></button>
    <?php if ($editing): ?><a href="/admin/posts/index.php" class="btn" style="background:#eee;color:#333;">Cancelar</a><?php endif; ?>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
