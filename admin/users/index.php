<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();
    $currentAdmin = current_admin();

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $total = (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

        if ($id === (int) $currentAdmin['id']) {
            flash_set('Você não pode excluir o seu próprio usuário.', 'error');
        } elseif ($total <= 1) {
            flash_set('Não é possível excluir o único usuário do painel.', 'error');
        } else {
            $stmt = db()->prepare('SELECT name FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            $name = $stmt->fetchColumn();
            db()->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
            if ($name) {
                log_activity('delete', 'user', $id, $name);
            }
            flash_set('Usuário removido.');
        }
    }

    redirect('/admin/users/index.php');
}

$pageTitle = 'Usuários do Painel';
require __DIR__ . '/../partials/layout-top.php';

$users = db()->query('SELECT * FROM admin_users ORDER BY name')->fetchAll();
?>
<div class="content-header">
    <h1>Usuários do Painel</h1>
    <a class="btn" href="/admin/users/edit.php">+ Novo usuário</a>
</div>

<table class="admin-table">
    <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Último acesso</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['name']) ?> <?= (int) $u['id'] === (int) $admin['id'] ? '<span class="badge badge-published">você</span>' : '' ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= $u['last_login_at'] ? e(date('d/m/Y H:i', strtotime($u['last_login_at']))) : '—' ?></td>
            <td class="actions">
                <a href="/admin/users/edit.php?id=<?= (int) $u['id'] ?>">Editar</a>
                <?php if ((int) $u['id'] !== (int) $admin['id']): ?>
                    <form method="post" onsubmit="return confirm('Excluir este usuário?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button type="submit" class="link-danger">Excluir</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
