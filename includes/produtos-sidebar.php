<?php
declare(strict_types=1);
// Sidebar de filtro compartilhada entre /produtos/ e /tipos/{slug}/.
// Espera $lang, $types (com t_name já resolvido), $lines (com t_name já resolvido)
// e opcionalmente $activeTypeSlug definidos por quem inclui este partial.
$activeTypeSlug = $activeTypeSlug ?? null;
?>
<div id="sidebar1" class="sidebar small-12 medium-auto cell small-order-1" role="complementary">
<div id="filter-menu">
<div class="widget widget_nav_menu left-column">
<h4 class="widgettitle"><?= e(t('produtos.sidebar_types')) ?></h4>
<ul class="menu">
<li class="menu-item<?= $activeTypeSlug === null ? ' current-menu-item active' : '' ?>">
<a href="<?= e(home_url()) ?>produtos/"><?= e(t('produtos.all_products')) ?></a>
</li>
<?php foreach ($types as $type): ?>
<li class="menu-item<?= $activeTypeSlug === $type['slug'] ? ' current-menu-item active' : '' ?>">
<a href="<?= e(home_url()) ?>tipos/<?= e($type['slug']) ?>/"><?= e($type['t_name'] ?: $type['name']) ?></a>
</li>
<?php endforeach; ?>
</ul>
</div>
<div class="widget widget_nav_menu left-column">
<h4 class="widgettitle"><?= e(t('produtos.sidebar_lines')) ?></h4>
<ul class="menu">
<li class="menu-item"><a href="<?= e(home_url()) ?>linhas/"><?= e(t('produtos.all_lines')) ?></a></li>
<?php foreach ($lines as $line): ?>
<li class="menu-item"><a href="<?= e(home_url()) ?>linhas/<?= e($line['slug']) ?>/"><?= e($line['t_name'] ?: $line['name']) ?></a></li>
<?php endforeach; ?>
</ul>
</div>
</div>
</div>
