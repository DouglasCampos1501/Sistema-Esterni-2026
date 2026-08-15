<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';
require_once __DIR__ . '/../includes/auth.php'; // csrf_field()/verify_csrf_token() — não exige login, só sessão
require_once __DIR__ . '/../includes/mail.php';

start_session(); // precisa iniciar antes de qualquer HTML — csrf_field() é chamado só lá embaixo, no meio da página

$lang = current_language();

// Processa o envio ANTES de montar a página (padrão do resto do site: valida,
// grava em contact_messages, tenta notificar por e-mail, e sempre volta pra
// esta mesma página com uma mensagem de sucesso/erro via flash).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    // Honeypot: campo escondido via CSS que só um bot preencheria.
    if (trim($_POST['website'] ?? '') !== '') {
        redirect($_SERVER['REQUEST_URI']);
    }

    // Limite simples contra spam/flood: no máx. 5 envios por IP a cada hora.
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $recentCount = 0;
    if ($ip !== '') {
        $stmt = db()->prepare('SELECT COUNT(*) FROM contact_messages WHERE ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
        $stmt->execute([$ip]);
        $recentCount = (int) $stmt->fetchColumn();
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $consent = isset($_POST['consent']);

    $errors = [];
    if ($recentCount >= 5) {
        $errors[] = true;
    }
    if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '' || !$consent) {
        $errors[] = true;
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO contact_messages (name, email, phone, company, message, language_code, ip_address) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([$name, $email, $phone, $company, $message, $lang, $ip]);
        $sent = send_contact_email($name, $email, $phone, $message, $company);
        db()->prepare('UPDATE contact_messages SET email_sent = ? WHERE id = ?')->execute([$sent ? 1 : 0, db()->lastInsertId()]);
        flash_set(t('contato.success'));
    } else {
        flash_set(t('contato.error'), 'error');
    }
    redirect($_SERVER['REQUEST_URI']);
}

$pageTitle = t('contato.page_title') . ' — Esterni Design e Mobiliário Urbano';
$pageDescription = t('contato.form_intro');
$bodyClass = 'inner-page';
$activeMenu = 'contact';

require __DIR__ . '/../includes/header-public.php';

$fields = db()->query('SELECT * FROM contact_form_fields ORDER BY sort_order, id')->fetchAll();
$fieldTranslations = [];
foreach (db()->query('SELECT * FROM contact_form_field_translations WHERE language_code = ' . db()->quote($lang))->fetchAll() as $row) {
    $fieldTranslations[$row['field_id']] = $row;
}
$flash = flash_get();
$home = '/uploads/media/home/';
?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h1 class="block-title side-lines bottom-line larger-text"><?= e(t('contato.page_title')) ?></h1>
</div>
</div>
</div>
</div>

<div class="spacer5"></div>
<div class="block">
<div class="grid-container feats" style="position: relative;">
<div class="grid-x grid-padding-x align-top align-center">
<div class="large-7 medium-auto small-12 cell justify small-center">
<div class="left-column">
<div class="block-title medium-text side-lines bottom-line small-text-center"><strong><?= e(t('contato.form_title')) ?></strong></div>
<p><?= t('contato.form_intro') ?></p>

<?php if ($flash): ?>
<div class="callout <?= $flash['type'] === 'error' ? 'alert' : 'success' ?>" style="padding:1rem;border-radius:.5rem;margin-bottom:1rem;background:<?= $flash['type'] === 'error' ? '#fdecea' : '#e6f7ec' ?>;color:<?= $flash['type'] === 'error' ? '#b2071d' : '#1a7a3c' ?>;">
<?= e($flash['message']) ?>
</div>
<?php endif; ?>

<form method="post" class="contact-form">
<?= csrf_field() ?>
<input type="text" name="website" value="" autocomplete="off" tabindex="-1" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">

<?php foreach ($fields as $field): ?>
<?php
$tr = $fieldTranslations[$field['id']] ?? null;
$label = ($tr['label'] ?? '') !== '' ? $tr['label'] : $field['label'];
$placeholder = ($tr['placeholder'] ?? '') !== '' ? $tr['placeholder'] : $field['placeholder'];
$required = (bool) $field['is_required'];
?>
<div class="ff-el-group">
<div class="ff-el-input--label<?= $required ? ' ff-el-is-required asterisk-right' : '' ?>">
<label for="field-<?= e($field['field_key']) ?>"><?= e($label) ?></label>
</div>
<div class="ff-el-input--content">
<?php if ($field['field_type'] === 'textarea'): ?>
<textarea name="<?= e($field['field_key']) ?>" id="field-<?= e($field['field_key']) ?>" class="ff-el-form-control" rows="4" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>></textarea>
<?php else: ?>
<input type="<?= e($field['field_type']) ?>" name="<?= e($field['field_key']) ?>" id="field-<?= e($field['field_key']) ?>" class="ff-el-form-control" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>

<div class="ff-el-group ff-el-input--content">
<label class="newsletter-consent">
<input type="checkbox" name="consent" required>
<span><?= t('contato.consent') ?></span>
</label>
</div>

<div class="ff-el-group ff-text-left" style="margin-top:1rem;">
<button type="submit" class="button secondary"><?= e(t('contato.submit')) ?></button>
</div>
</form>
</div>
</div>

<div class="large-5 medium-6 small-12 cell">
<div class="right-column">
<div class="spacer3 hide-for-medium"></div>
<div class="block-title medium-text side-lines bottom-line small-text-center"><strong><?= e(t('contato.location_title')) ?></strong></div>
<a href="https://goo.gl/maps/Dg7wqdM2CdxDr4cq8" target="_blank" rel="noopener"><img src="<?= $home ?>mapa-w.png" alt="<?= e(t('contato.location_title')) ?>"></a>
<div class="spacer1"></div>
<p>GRUPO TECHNOMAST<br>
Rod. PR 423, KM 24.3, Jardim das Acácias<br>
Campo Largo, Paraná, Brasil<br>
CEP: 83603-000</p>
<div class="spacer1 hide-for-medium"></div>
<div class="spacer2"></div>
<div class="block-title medium-text side-lines bottom-line small-text-center"><strong><?= e(t('contato.phones_title')) ?></strong></div>
<div>(41) 3195-4348 <span class="smaller-text"> - Grupo Technomast</span></div>
</div>
</div>
</div>
</div>
</div>
<div class="spacer5"></div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
