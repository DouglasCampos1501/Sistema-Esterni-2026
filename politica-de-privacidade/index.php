<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();

$pageTitle = t('privacy.page_title') . ' — Esterni Design e Mobiliário Urbano';
$pageDescription = t('privacy.page_title') === 'Política de privacidade'
    ? 'Conheça a Política de Privacidade da Esterni.'
    : t('privacy.page_title');
$bodyClass = 'inner-page';
$activeMenu = '';

require __DIR__ . '/../includes/header-public.php';
?>

<!-- Cabeçalho padrão de página interna (título centralizado sobre fundo escuro) —
     reaproveitar essa mesma estrutura .color-box nas próximas páginas internas
     (Sobre, Contato, Linhas, Produtos, Notícias). -->
<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h1 class="block-title side-lines bottom-line larger-text"><?= e(t('privacy.page_title')) ?></h1>
</div>
</div>
</div>
</div>

<div class="block">
<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle align-center">
<div class="large-10 medium-11 small-12 cell justify">
<?= t('privacy.content') ?>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
