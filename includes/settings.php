<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function get_setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    $value = $cache[$key] ?? null;
    return $value !== null && $value !== '' ? $value : $default;
}

function set_setting(string $key, string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

/**
 * Só os dígitos, para montar o link wa.me corretamente mesmo se o admin digitar com máscara.
 * Se o número tiver formato local brasileiro (10-11 dígitos, sem DDI), adiciona o 55 automaticamente.
 */
function whatsapp_digits(): string
{
    $digits = preg_replace('/\D/', '', get_setting('whatsapp')) ?? '';
    if ($digits !== '' && !str_starts_with($digits, '55') && in_array(strlen($digits), [10, 11], true)) {
        $digits = '55' . $digits;
    }
    return $digits;
}

/**
 * Número do WhatsApp formatado para exibição, com "+55" na frente fora do
 * pt-BR (mesma lógica já usada no telefone do rodapé — visitante estrangeiro
 * precisa do código do país pra discar; local não).
 */
function whatsapp_display(string $currentLang, string $defaultLang): string
{
    $raw = get_setting('whatsapp');
    if ($raw === '' || $currentLang === $defaultLang || str_starts_with($raw, '+')) {
        return $raw;
    }
    return '+55 ' . $raw;
}
