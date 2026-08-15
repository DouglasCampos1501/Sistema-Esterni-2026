<?php
declare(strict_types=1);
// Widget flutuante de WhatsApp (canto da tela, com balões de assunto configuráveis
// em /admin/whatsapp/). Espera que header-public.php já tenha rodado ($lang disponível).
// Reaproveita a marcação/CSS ".whatsapp-floater" já existente no theme.css (extraído do
// próprio site real, que já tinha esse widget pronto).

$lang = $lang ?? current_language();
$defaultLang = default_language();

$stmt = db()->prepare(
    'SELECT o.*, t.label AS t_label, t.message AS t_message
     FROM whatsapp_options o
     LEFT JOIN whatsapp_option_translations t ON t.whatsapp_option_id = o.id AND t.language_code = ?
     WHERE o.active = 1
     ORDER BY o.sort_order, o.id'
);
$stmt->execute([$lang]);
$whatsappOptions = $stmt->fetchAll();
?>
<?php if ($whatsappOptions): ?>
<div class="whatsapp-floater" id="whatsapp-floater">
<a href="javascript:;" class="icon clickable">
<span onclick="jQuery('#whatsapp-floater').addClass('open'); jQuery('#chat-box').slideDown(50);" style="display: inline-block; padding: 1rem;">
<i class="fab fa-whatsapp"></i> <?= e(t('whatsapp.button_label')) ?></span>
<span class="close clickable" onclick="jQuery('#whatsapp-floater').removeClass('open'); jQuery('#chat-box').slideUp(0);">
<i class="fas fa-times-circle fa-rotate-90"></i>
</span>
</a>
<div class="chat-box" id="chat-box">
<div class="chat">
<div class="chat-inner">
<div class="line left">
<div class="bubble"><?= t('whatsapp.welcome') ?></div>
</div>
<?php foreach ($whatsappOptions as $opt): ?>
<?php
$label = $opt['t_label'] ?: $opt['label'];
$message = $opt['t_message'] ?: $opt['message'];
$waUrl = 'https://wa.me/' . rawurlencode($opt['phone_number']) . '?text=' . rawurlencode($message);
?>
<div class="line">
<a class="bubble clickable" href="<?= e($waUrl) ?>" target="_blank" rel="noopener"><?= e($label) ?></a>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</div>
<?php endif; ?>
