<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();

$pageTitle = t('sobre.page_title');
$pageDescription = t('sobre.intro_title');
$bodyClass = 'inner-page';
$activeMenu = 'about';

require __DIR__ . '/../includes/header-public.php';

$features = [
    ['title' => t('sobre.feat1_title'), 'text' => t('sobre.feat1_text')],
    ['title' => t('sobre.feat2_title'), 'text' => t('sobre.feat2_text')],
    ['title' => t('sobre.feat3_title'), 'text' => t('sobre.feat3_text')],
    ['title' => t('sobre.feat4_title'), 'text' => t('sobre.feat4_text')],
    ['title' => t('sobre.feat5_title'), 'text' => t('sobre.feat5_text')],
    ['title' => t('sobre.feat6_title'), 'text' => t('sobre.feat6_text')],
];
$home = '/uploads/media/home/';
?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h1 class="block-title side-lines bottom-line larger-text"><?= e(t('sobre.page_title')) ?></h1>
</div>
</div>
</div>
</div>

<div class="block">
<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle align-center">
<div class="large-5 medium-6 cell small-center">
<div class="spacer1"></div>
<p class="subtitle dark-text upper"><?= e(t('sobre.intro_subtitle')) ?></p>
<p class="title dark-text larger-text upper side-lines bottom-line"><?= e(t('sobre.intro_title')) ?></p>
<p class="justify"><?= t('sobre.intro_text') ?></p>
</div>
<div class="small-11 medium-auto cell justify">
<div class="spacer1 hide-for-medium"></div>
<div class="right-column">
<div class="image wide"><img src="<?= $home ?>aludra-cam02-prv1-1-1024x768.jpg" alt="<?= e(t('sobre.intro_title')) ?>"></div>
</div>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>

<div class="block">
<div class="color-box grey">
<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle align-center">
<div class="large-9 medium-10 small-11 cell">
<div class="text-center">
<h2 class="title large-text upper side-lines bottom-line text-center"><?= e(t('sobre.group_title')) ?></h2>
<p class="justify"><?= e(t('sobre.group_text')) ?></p>
<div class="spacer05"></div>
<a href="https://technomast.com.br/" target="_blank" rel="noopener" class="button hollow"><?= e(t('sobre.group_cta')) ?> ➧</a>
</div>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>
</div>

<div class="block" style="box-shadow: inset 0 5rem 8rem -5rem rgba(0,0,0,0.05);">
<div class="spacer5"></div>
<div class="icon-text">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle align-center">
<div class="medium-12 large-12 cell text-center">
<div class="spacer05"></div>
<h1 class="block-title side-lines bottom-line large-text upper"><?= e(t('sobre.why_title')) ?></h1>
<div class="spacer3"></div>
</div>
</div>
<div class="grid-x grid-padding-x align-top align-center">
<?php foreach ($features as $feat): ?>
<div class="large-4 medium-4 cell">
<div class="small-center">
<div class="item">
<div class="grid-x align-top align-center">
<div class="small-12 medium-shrink cell text-center">
<div class="ico"><i class="icon-ok huge-text"></i></div>
</div>
<div class="small-11 medium-auto cell">
<strong class="upper"><?= e($feat['title']) ?></strong><br>
<?= e($feat['text']) ?>
</div>
</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
<div class="spacer6"></div>
</div>

<div class="hero auto-hero light-hero overlay lighter-overlay small-center white-text" data-parallax="scroll" data-image-src="<?= $home ?>CENA-5305-P-1-1024x576.jpg" data-position="center center" data-ios-fix="true">
<div class="spacer5"></div>
<div class="hero-caption">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="small-11 medium-9 large-8 cell text-center">
<h2 class="title large-text"><?= e(t('home.cta.title')) ?></h2>
<p><?= t('home.cta.text') ?></p>
<div class="spacer05"></div>
<a class="button hollow white" href="<?= e(home_url()) ?>contato/" style="margin:0"><i class="fas fa-envelope left"></i> <?= e(t('home.cta.button')) ?></a>
</div>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
