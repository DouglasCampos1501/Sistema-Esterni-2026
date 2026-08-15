<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function current_admin(): ?array
{
    start_session();
    return $_SESSION['admin'] ?? null;
}

function require_login(): void
{
    start_session();
    if (!isset($_SESSION['admin'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCKOUT_MINUTES = 15;

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Proteção contra força bruta: bloqueia novas tentativas de login a partir do
 * mesmo IP depois de LOGIN_MAX_ATTEMPTS falhas dentro de LOGIN_LOCKOUT_MINUTES.
 */
function login_is_locked_out(): bool
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > (NOW() - INTERVAL ? MINUTE)'
    );
    $stmt->execute([client_ip(), LOGIN_LOCKOUT_MINUTES]);
    return (int) $stmt->fetchColumn() >= LOGIN_MAX_ATTEMPTS;
}

function register_failed_login(string $email): void
{
    db()->prepare('INSERT INTO login_attempts (ip_address, email) VALUES (?, ?)')->execute([client_ip(), $email]);
}

function clear_login_attempts(): void
{
    db()->prepare('DELETE FROM login_attempts WHERE ip_address = ?')->execute([client_ip()]);
}

function attempt_login(string $email, string $password): bool
{
    if (login_is_locked_out()) {
        return false;
    }

    $stmt = db()->prepare('SELECT id, name, email, password_hash FROM admin_users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        register_failed_login($email);
        return false;
    }

    start_session();
    session_regenerate_id(true);
    $_SESSION['admin'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];

    clear_login_attempts();

    db()->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);

    db()->prepare(
        'INSERT INTO activity_log (admin_user_id, admin_name, action, entity_type, entity_id, entity_label) VALUES (?,?,\'login\',\'session\',NULL,NULL)'
    )->execute([$user['id'], $user['name']]);

    return true;
}

function logout(): void
{
    start_session();
    $_SESSION = [];
    session_destroy();
}

function verify_csrf_token(): void
{
    start_session();
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Token de segurança inválido. Recarregue a página e tente novamente.');
    }
}

function csrf_field(): string
{
    start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}
