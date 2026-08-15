<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/i18n.php';

$lang = current_language();
$languages = get_languages();
$currentLangRow = null;
foreach ($languages as $l) {
    if ($l['code'] === $lang) {
        $currentLangRow = $l;
        break;
    }
}
$pageTitle = $pageTitle ?? 'Esterni Design e Mobiliário Urbano';
$pageDescription = $pageDescription ?? 'Esterni Design e Mobiliário Urbano. Harmonia entre postes e mobiliário, criando equilíbrio para praças, parques, beira-mares, calçadões e condomínios fechados.';
$bodyClass = $bodyClass ?? '';
$activeMenu = $activeMenu ?? '';

function nav_active(string $key, string $activeMenu): string
{
    return $key === $activeMenu ? ' current-menu-item active' : '';
}
?>
<!doctype html>
<html class="no-js" dir="ltr" lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#191919">
<link rel="shortcut icon" href="/assets/img/site/favicon-32.png" type="image/x-icon">
<link rel="icon" href="/assets/img/site/favicon-32.png">
<link href="/assets/img/site/favicon-192.png" rel="apple-touch-icon">
<title><?= e($pageTitle) ?></title>
<link rel="canonical" href="<?= e(SITE_URL . lang_url($lang)) ?>">
<meta name="description" content="<?= e($pageDescription) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:type" content="website">
<meta property="og:description" content="<?= e($pageDescription) ?>">
<meta property="og:url" content="<?= e(SITE_URL . lang_url($lang)) ?>">
<meta property="og:locale" content="<?= e(str_replace('-', '_', $lang)) ?>">
<meta property="og:site_name" content="Esterni Design e Mobiliário Urbano">
<?= hreflang_tags() ?>
<link rel="stylesheet" href="/assets/public/css/theme.css">
<link rel="stylesheet" href="/assets/public/css/lang-switcher.css">
</head>
<body class="<?= e($bodyClass) ?>">
<div class="scrollhide-nav-holder">
<nav class="scrollhide-nav" data-sticky data-margin-top="0" data-sticky-on="small" data-dynamic-height="true">
<div class="top-line">
<div class="top-bar-container">
<div class="top-bar">
<div class="top-bar-title">
<a class="top-logo" href="<?= e(home_url()) ?>">
<img src="/assets/img/site/logo-esterni.png" class="primary" alt="Esterni Design e Mobiliário Urbano">
<img src="/assets/img/site/logo-esterni.png" class="secondary" alt="Esterni Design e Mobiliário Urbano">
</a>
<span class="hide-for-large" data-responsive-toggle="topbar-responsive" data-hide-for="large">
<button class="menu-icon" type="button" onclick="jQuery(this).toggleClass('opened');" data-toggle></button>
</span>
</div>
<div class="top-bar-left show-for-large">
<ul class="dropdown menu align-middle contact-menu" data-closing-time="0">
<li><a href="https://www.linkedin.com/company/esterni-mobiliario-urbano" target="_blank" rel="noopener"><i class="fab fa-linkedin"></i></a></li>
<li><a href="https://www.instagram.com/esterni_design/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
<li><a href="https://www.youtube.com/channel/UC9bHyin4rQilgr68zh_5uug" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>
<li><a href="https://wa.me/5541995967801?text=Ol%C3%A1%2C+gostaria+de+falar+com+um+consultor+da+Esterni." target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a></li>
</ul>
</div>
<div class="top-bar-right" id="topbar-responsive">
<div class="grid-x grid-padding-x">
<div class="cell small-12 top-line-menu">
<ul id="main-nav" class="dropdown menu align-middle top-menu align-right align-center-small" data-closing-time="0" data-responsive-menu="drilldown large-dropdown">
<li class="menu-item<?= nav_active('home', $activeMenu) ?>"><a href="<?= e(home_url()) ?>"><?= e(t('menu.home')) ?></a></li>
<li class="menu-item<?= nav_active('about', $activeMenu) ?>"><a href="<?= e(home_url()) ?>sobre/"><?= e(t('menu.about')) ?></a></li>
<li class="menu-item<?= nav_active('lines', $activeMenu) ?>"><a href="<?= e(home_url()) ?>linhas/"><?= e(t('menu.lines')) ?></a></li>
<li class="menu-item<?= nav_active('products', $activeMenu) ?>"><a href="<?= e(home_url()) ?>produtos/"><?= e(t('menu.products')) ?></a></li>
<li class="menu-item<?= nav_active('news', $activeMenu) ?>"><a href="<?= e(home_url()) ?>noticias/"><?= e(t('menu.news')) ?></a></li>
<li class="menu-item<?= nav_active('contact', $activeMenu) ?>"><a href="<?= e(home_url()) ?>contato/"><?= e(t('menu.contact')) ?></a></li>
<li class="hl menu-item"><a target="_blank" rel="noopener" href="https://technomast.com.br/"><?= e(t('menu.group')) ?></a></li>
<!-- Item novo: seletor de idiomas (não existe no site original; bandeiras no mesmo
     padrão do site da Technomast — /assets/img/flags/{código}.png) -->
<li class="menu-item lang-switcher">
<a href="#" class="lang-switcher-toggle" aria-label="<?= e(t('menu.language')) ?>">
<?php if ($currentLangRow && $currentLangRow['flag_image']): ?>
<img src="<?= e($currentLangRow['flag_image']) ?>" alt="<?= e($currentLangRow['name']) ?>" class="lang-flag">
<?php endif; ?>
<i class="fas fa-chevron-down"></i>
</a>
<ul class="lang-switcher-menu">
<?php foreach ($languages as $l): ?>
<li class="<?= $l['code'] === $lang ? 'is-active' : '' ?>">
<a href="<?= e(lang_url($l['code'])) ?>">
<?php if ($l['flag_image']): ?><img src="<?= e($l['flag_image']) ?>" alt="" class="lang-flag"><?php endif; ?>
<?= e($l['name']) ?>
</a>
</li>
<?php endforeach; ?>
</ul>
</li>
</ul>
</div>
<div class="cell auto top-line-contact hide-for-large">
<div class="spacer2"></div>
<ul class="dropdown menu align-middle top-menu contact-menu" data-closing-time="0">
<li><a href="https://www.linkedin.com/company/esterni-mobiliario-urbano" target="_blank" rel="noopener"><i class="fab fa-linkedin"></i></a></li>
<li><a href="https://www.instagram.com/esterni_design/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
<li><a href="https://www.youtube.com/channel/UC9bHyin4rQilgr68zh_5uug" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>
<li><a href="https://wa.me/5541995967801?text=Ol%C3%A1%2C+gostaria+de+falar+com+um+consultor+da+Esterni." target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a></li>
</ul>
</div>
</div>
</div>
<div class="sh-toggle animate">&#8964;</div>
</div>
</nav>
</div>
<div id="page-anchor"></div>
