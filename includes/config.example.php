<?php
// Configuração central do site — copie este arquivo para config.php e
// preencha com os dados reais do seu ambiente (local ou produção).
// config.php NÃO é versionado (está no .gitignore) porque guarda credenciais.

declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'esterni');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'https://esterni.ind.br');
define('BASE_PATH', dirname(__DIR__));
define('UPLOADS_PATH', BASE_PATH . '/uploads/media');
define('UPLOADS_URL', '/uploads/media');

// Alguns ambientes Windows não vêm com um bundle de certificados CA configurado
// para o cURL, o que quebra downloads HTTPS (usado na importação de conteúdo).
// Em produção (Linux/cPanel) isso normalmente já vem configurado pelo sistema.
define('CURL_CA_BUNDLE', file_exists(__DIR__ . '/cacert.pem') ? __DIR__ . '/cacert.pem' : null);

error_reporting(E_ALL);
ini_set('display_errors', '0'); // trocar para '1' apenas em ambiente local de desenvolvimento

date_default_timezone_set('America/Sao_Paulo');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    // 'secure' automático: exige HTTPS para o cookie quando o site já estiver em HTTPS
    // (em produção deve ser sempre o caso), mas não quebra o acesso local por HTTP puro.
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? null) === '443'
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
]);
