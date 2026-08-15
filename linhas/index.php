<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();
$defaultLang = default_language();

$pageTitle = t('home.lines.title') . ' — Esterni Design e Mobiliário Urbano';
$bodyClass = 'inner-page';
$activeMenu = 'lines';

require __DIR__ . '/../includes/header-public.php';

$stmt = db()->prepare(
    'SELECT l.*, m.path AS image_path, t.name AS t_name
     FROM product_lines l
     LEFT JOIN media m ON m.id = l.featured_image_id
     LEFT JOIN product_line_translations t ON t.line_id = l.id AND t.language_code = ?
     WHERE l.active = 1
     ORDER BY l.sort_order, l.id'
);
$stmt->execute([$lang]);
$lines = $stmt->fetchAll();
?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h1 class="block-title side-lines bottom-line larger-text"><?= e(t('home.lines.title')) ?></h1>
</div>
</div>
</div>
</div>

<div class="spacer3"></div>
<div class="block">
<div class="spacer1"></div>
<div class="feats" data-equalizer="linhas">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<?php foreach ($lines as $line): ?>
<?php $name = $line['t_name'] ?: $line['name']; ?>
<div class="small-12 medium-6 large-4 cell">
<a href="<?= e(home_url()) ?>linhas/<?= e($line['slug']) ?>/" class="card" data-equalizer-watch="linhas">
<div class="grid-x align-center align-middle">
<div class="small-12 cell">
<div class="card-image contain horizontal shadow">
<?php if ($line['image_path']): ?><img src="<?= e($line['image_path']) ?>" alt="<?= e($name) ?>"><?php endif; ?>
</div>
</div>
</div>
<div class="grid-x grid-padding-x align-center align-middle">
<div class="small-12 medium-auto cell">
<div style="padding: 1.5rem"><strong class="title upper"><?= e(t('line.prefix')) ?> <?= e($name) ?></strong></div>
</div>
<div class="small-12 medium-shrink cell show-for-medium">
<div style="padding: .95rem"><span class="button hollow tiny" style="margin:0"><?= e(t('home.about.cta')) ?></span></div>
</div>
</div>
</a>
<div class="spacer1"></div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
