<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/settings.php';
require_once __DIR__ . '/../../includes/activity.php';

$fields = [
    'whatsapp' => ['label' => 'Número do WhatsApp', 'placeholder' => 'Ex: (41) 99596-7801'],
    'social_instagram' => ['label' => 'Instagram (URL completa)', 'placeholder' => 'https://instagram.com/esterni'],
    'social_linkedin' => ['label' => 'LinkedIn (URL completa)', 'placeholder' => 'https://linkedin.com/company/esterni'],
    'social_facebook' => ['label' => 'Facebook (URL completa)', 'placeholder' => 'https://facebook.com/esterni'],
    'social_youtube' => ['label' => 'YouTube (URL completa)', 'placeholder' => 'https://youtube.com/@esterni'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();

    foreach (array_keys($fields) as $key) {
        set_setting($key, trim($_POST[$key] ?? ''));
    }
    log_activity('update', 'settings', null, '');
    flash_set('Configurações salvas.');
    redirect('/admin/settings/index.php');
}

$pageTitle = 'Configurações Gerais';
require __DIR__ . '/../partials/layout-top.php';
?>
<div class="content-header"><h1>Configurações Gerais</h1></div>
<p style="color:#667085;max-width:640px;margin-top:-1rem;">
    O telefone e o endereço exibidos no site (com tradução por idioma) ficam em <a href="/admin/site-texts/index.php">Textos do Site</a>.
    Aqui ficam os contatos e redes sociais que são os mesmos em qualquer idioma.
</p>

<form method="post" class="admin-form" style="max-width:520px;">
    <?= csrf_field() ?>
    <?php foreach ($fields as $key => $f): ?>
        <label>
            <?= e($f['label']) ?>
            <input type="text" name="<?= e($key) ?>" value="<?= e(get_setting($key)) ?>" placeholder="<?= e($f['placeholder']) ?>">
        </label>
    <?php endforeach; ?>
    <p style="font-size:.8rem;color:#667085;">Deixe em branco qualquer rede social que a Esterni ainda não tenha — o ícone só aparece no site quando o link está preenchido.</p>
    <button type="submit" class="btn">Salvar</button>
</form>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
