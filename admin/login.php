<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

start_session();

if (current_admin()) {
    redirect('/admin/index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_is_locked_out()) {
        $error = 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.';
    } elseif (attempt_login($email, $password)) {
        redirect('/admin/index.php');
    } else {
        $error = 'E-mail ou senha inválidos.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login — Painel Esterni</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/assets/img/site/favicon-32.png" sizes="32x32">
    <link rel="icon" href="/assets/img/site/favicon-192.png" sizes="192x192">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-page">
    <form class="login-box" method="post" action="/admin/login.php">
        <img src="/assets/img/site/logo-esterni.png" alt="Esterni" class="login-logo">
        <p class="login-subtitle">Acesse o painel administrativo</p>
        <?php if ($error): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>
        <?= csrf_field() ?>
        <label>
            E-mail
            <input type="email" name="email" required autofocus placeholder="seu@email.com">
        </label>
        <label>
            Senha
            <input type="password" name="password" required placeholder="••••••••">
        </label>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
