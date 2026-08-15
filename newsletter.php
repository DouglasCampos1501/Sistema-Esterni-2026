<?php
declare(strict_types=1);
// Handler do formulário "Receba por e-mail" (aparece no rodapé de toda página pública).
// Sem CSRF token dedicado aqui de propósito — o formulário é compartilhado por header/footer
// sem um <form> de admin por trás, mas valida honeypot + rate limit + duplicidade.

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/i18n.php';
require_once __DIR__ . '/includes/auth.php';

start_session();
$lang = current_language();

$back = $_SERVER['HTTP_REFERER'] ?? home_url();
// só aceita voltar pra uma URL do próprio site, nunca redireciona pra fora
if (!str_starts_with($back, SITE_URL) && !str_starts_with($back, '/')) {
    $back = home_url();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($back);
}

// Honeypot: se um segundo campo de e-mail escondido (armadilha pra bot) veio preenchido, finge sucesso e sai.
if (trim($_POST['website'] ?? '') !== '') {
    redirect($back);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

$recentCount = 0;
if ($ip !== '') {
    $stmt = db()->prepare('SELECT COUNT(*) FROM newsletter_subscribers WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
    $stmt->execute([$ip]);
    $recentCount = (int) $stmt->fetchColumn();
}

if ($recentCount >= 5 || $name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set(t('newsletter.error'), 'error');
    redirect($back);
}

$stmt = db()->prepare(
    'INSERT INTO newsletter_subscribers (name, email, language_code, ip_address) VALUES (?,?,?,?)
     ON DUPLICATE KEY UPDATE name = VALUES(name), language_code = VALUES(language_code)'
);
$stmt->execute([$name, $email, $lang, $ip]);

flash_set(t('newsletter.success'));
redirect($back);
