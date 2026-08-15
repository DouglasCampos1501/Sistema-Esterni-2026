<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/activity.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$user = ['id' => null, 'name' => '', 'email' => ''];
$errors = [];

if ($id) {
    $stmt = db()->prepare('SELECT id, name, email FROM admin_users WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash_set('Usuário não encontrado.', 'error');
        redirect('/admin/users/index.php');
    }
    $user = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();

    $user['name'] = trim($_POST['name'] ?? '');
    $user['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($user['name'] === '') {
        $errors[] = 'O nome é obrigatório.';
    }
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Informe um e-mail válido.';
    }
    if (!$id && $password === '') {
        $errors[] = 'A senha é obrigatória para um novo usuário.';
    }
    if ($password !== '' && strlen($password) < 8) {
        $errors[] = 'A senha precisa ter pelo menos 8 caracteres.';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'A confirmação de senha não confere.';
    }

    if (!$errors) {
        $dupStmt = db()->prepare('SELECT id FROM admin_users WHERE email = ? AND id != ?');
        $dupStmt->execute([$user['email'], $id ?? 0]);
        if ($dupStmt->fetch()) {
            $errors[] = 'Já existe um usuário com esse e-mail.';
        }
    }

    if (!$errors) {
        if ($id) {
            if ($password !== '') {
                $stmt = db()->prepare('UPDATE admin_users SET name=?, email=?, password_hash=? WHERE id=?');
                $stmt->execute([$user['name'], $user['email'], password_hash($password, PASSWORD_DEFAULT), $id]);
            } else {
                $stmt = db()->prepare('UPDATE admin_users SET name=?, email=? WHERE id=?');
                $stmt->execute([$user['name'], $user['email'], $id]);
            }
            log_activity('update', 'user', $id, $user['name']);
            flash_set('Usuário atualizado com sucesso.');
        } else {
            $stmt = db()->prepare('INSERT INTO admin_users (name, email, password_hash) VALUES (?,?,?)');
            $stmt->execute([$user['name'], $user['email'], password_hash($password, PASSWORD_DEFAULT)]);
            $id = (int) db()->lastInsertId();
            log_activity('create', 'user', $id, $user['name']);
            flash_set('Usuário criado com sucesso.');
        }
        redirect('/admin/users/index.php');
    }
}

$pageTitle = $id ? 'Editar usuário' : 'Novo usuário';
require __DIR__ . '/../partials/layout-top.php';
?>
<h1><?= e($pageTitle) ?></h1>

<?php if ($errors): ?>
    <div class="flash flash-error">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="admin-form" style="max-width:480px;">
    <?= csrf_field() ?>
    <label>
        Nome
        <input type="text" name="name" value="<?= e($user['name']) ?>" required>
    </label>
    <label>
        E-mail
        <input type="email" name="email" value="<?= e($user['email']) ?>" required>
    </label>
    <label>
        <?= $id ? 'Nova senha (deixe em branco para manter a atual)' : 'Senha' ?>
        <input type="password" name="password" autocomplete="new-password">
    </label>
    <label>
        Confirmar senha
        <input type="password" name="password_confirm" autocomplete="new-password">
    </label>
    <button type="submit" class="btn">Salvar</button>
    <a href="/admin/users/index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
