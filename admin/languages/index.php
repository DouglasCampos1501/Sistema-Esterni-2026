<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/media.php';
require_once __DIR__ . '/../../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $errors = [];

        if ($code === '' || !preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $code)) {
            $errors[] = 'Código inválido. Use o formato "en" ou "pt-BR".';
        }
        if ($name === '') {
            $errors[] = 'O nome é obrigatório.';
        }
        if (!$errors) {
            $dup = db()->prepare('SELECT code FROM languages WHERE code = ?');
            $dup->execute([$code]);
            if ($dup->fetch()) {
                $errors[] = 'Já existe um idioma com esse código.';
            }
        }

        $flagPath = null;
        if (!$errors) {
            try {
                $mediaId = store_uploaded_image($_FILES['flag_image'] ?? [], $name);
                if ($mediaId) {
                    $stmt = db()->prepare('SELECT path FROM media WHERE id = ?');
                    $stmt->execute([$mediaId]);
                    $flagPath = $stmt->fetchColumn();
                }
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!$errors) {
            $maxOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), -1) FROM languages')->fetchColumn();
            $stmt = db()->prepare('INSERT INTO languages (code, name, flag_emoji, flag_image, sort_order, active) VALUES (?,?,?,?,?,1)');
            $stmt->execute([$code, $name, '🏳', $flagPath, $maxOrder + 1]);
            log_activity('create', 'language', null, "$name ($code)");
            flash_set('Idioma adicionado.');
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/languages/index.php');
    }

    if ($action === 'toggle_active') {
        $code = $_POST['code'] ?? '';
        db()->prepare('UPDATE languages SET active = NOT active WHERE code = ? AND is_default = 0')->execute([$code]);
        redirect('/admin/languages/index.php');
    }

    if ($action === 'set_default') {
        $code = $_POST['code'] ?? '';
        db()->beginTransaction();
        db()->exec('UPDATE languages SET is_default = 0');
        db()->prepare('UPDATE languages SET is_default = 1, active = 1 WHERE code = ?')->execute([$code]);
        db()->commit();
        flash_set('Idioma padrão atualizado.');
        redirect('/admin/languages/index.php');
    }

    if ($action === 'delete') {
        $code = $_POST['code'] ?? '';
        $stmt = db()->prepare('SELECT is_default FROM languages WHERE code = ?');
        $stmt->execute([$code]);
        if ((int) $stmt->fetchColumn() === 1) {
            flash_set('Não é possível excluir o idioma padrão.', 'error');
        } else {
            db()->prepare('DELETE FROM languages WHERE code = ?')->execute([$code]);
            log_activity('delete', 'language', null, $code);
            flash_set('Idioma removido. As traduções associadas a ele também foram apagadas.');
        }
        redirect('/admin/languages/index.php');
    }
}

$pageTitle = 'Idiomas';
require __DIR__ . '/../partials/layout-top.php';

$languages = db()->query('SELECT * FROM languages ORDER BY sort_order')->fetchAll();
?>
<div class="content-header">
    <h1>Idiomas</h1>
</div>
<p style="color:#667085;max-width:640px;margin-top:-1rem;">
    O idioma padrão é o conteúdo "base" (hoje Português) — ele nunca precisa de tradução, pois é o que já existe
    em Páginas, Produtos e Notícias. Os demais idiomas aparecem como abas nos formulários de conteúdo e como
    bandeiras no menu do site.
</p>

<table class="admin-table" style="margin-bottom:2rem;">
    <thead>
        <tr><th></th><th>Código</th><th>Nome</th><th>Padrão</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($languages as $lang): ?>
        <tr>
            <td><?php if ($lang['flag_image']): ?><img src="<?= e($lang['flag_image']) ?>" alt="" style="width:24px;height:18px;object-fit:cover;border-radius:2px;"><?php endif; ?></td>
            <td><?= e($lang['code']) ?></td>
            <td><?= e($lang['name']) ?></td>
            <td>
                <?php if ($lang['is_default']): ?>
                    <span class="badge badge-published">padrão</span>
                <?php else: ?>
                    <form method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="set_default">
                        <input type="hidden" name="code" value="<?= e($lang['code']) ?>">
                        <button type="submit" class="link-danger" style="color:#1a56db;">tornar padrão</button>
                    </form>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($lang['is_default']): ?>
                    <span class="badge badge-published">sempre ativo</span>
                <?php else: ?>
                    <form method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle_active">
                        <input type="hidden" name="code" value="<?= e($lang['code']) ?>">
                        <button type="submit" class="badge badge-<?= $lang['active'] ? 'published' : 'draft' ?>" style="border:none;cursor:pointer;">
                            <?= $lang['active'] ? 'ativo' : 'inativo' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$lang['is_default']): ?>
                    <form method="post" onsubmit="return confirm('Excluir este idioma e todas as traduções associadas?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="code" value="<?= e($lang['code']) ?>">
                        <button type="submit" class="link-danger">Excluir</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;">Adicionar novo idioma</h2>
<form method="post" class="admin-form" enctype="multipart/form-data" style="max-width:480px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <label>
        Código (ex: fr, de, pt-BR)
        <input type="text" name="code" placeholder="fr" required>
    </label>
    <label>
        Nome
        <input type="text" name="name" placeholder="Français" required>
    </label>
    <label>
        Bandeira (imagem PNG/JPG pequena, ex: 40x30px)
        <input type="file" name="flag_image" accept="image/png,image/jpeg,image/webp">
    </label>
    <button type="submit" class="btn">Adicionar idioma</button>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
