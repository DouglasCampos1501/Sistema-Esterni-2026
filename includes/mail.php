<?php
declare(strict_types=1);

require_once __DIR__ . '/settings.php';

/**
 * Envia o e-mail de notificação de uma mensagem de contato para o endereço
 * configurado em Configurações Gerais (setting_key = contact_recipient_email).
 * Usa a função mail() nativa do PHP (sem dependências externas) — se o
 * servidor não tiver um MTA configurado o envio pode falhar silenciosamente,
 * por isso toda mensagem também é salva em contact_messages independente
 * do resultado deste envio.
 */
function send_contact_email(string $name, string $email, string $phone, string $message, string $company = ''): bool
{
    $to = get_setting('contact_recipient_email');
    if ($to === '') {
        return false;
    }

    $siteName = get_setting('site_name', 'Esterni');
    $subject = '=?UTF-8?B?' . base64_encode("Novo contato pelo site - $siteName") . '?=';

    $lines = [
        "Nova mensagem recebida pelo formulário de contato do site $siteName:",
        '',
        "Nome: $name",
        "E-mail: $email",
    ];
    if ($phone !== '') {
        $lines[] = "Telefone: $phone";
    }
    if ($company !== '') {
        $lines[] = "Empresa: $company";
    }
    $lines[] = '';
    $lines[] = 'Mensagem:';
    $lines[] = $message;

    $body = implode("\r\n", $lines);

    $headers = [
        'From: ' . $siteName . ' <no-reply@' . preg_replace('/^www\./', '', (string) parse_url(SITE_URL, PHP_URL_HOST)) . '>',
        'Reply-To: ' . $email,
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
