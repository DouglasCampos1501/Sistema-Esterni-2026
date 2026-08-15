<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();

$pageTitle = t('produtos.page_title') . ' — Esterni Design e Mobiliário Urbano';
$bodyClass = 'inner-page';
$activeMenu = 'products';

require __DIR__ . '/../includes/header-public.php';

$types = db()->prepare(
    'SELECT t.*, tt.name AS t_name FROM product_types t
     LEFT JOIN product_type_translations tt ON tt.type_id = t.id AND tt.language_code = ?
     WHERE t.active = 1 ORDER BY t.sort_order, t.id'
);
$types->execute([$lang]);
$types = $types->fetchAll();

$lines = db()->prepare(
    'SELECT l.*, lt.name AS t_name FROM product_lines l
     LEFT JOIN product_line_translations lt ON lt.line_id = l.id AND lt.language_code = ?
     WHERE l.active = 1 ORDER BY l.sort_order, l.id'
);
$lines->execute([$lang]);
$lines = $lines->fetchAll();

$products = db()->prepare(
    "SELECT p.*, m.path AS image_path, t.name AS t_name FROM products p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN product_translations t ON t.product_id = p.id AND t.language_code = ?
     WHERE p.status = 'published'
     ORDER BY p.line_id, p.sort_order, p.id"
);
$products->execute([$lang]);
$products = $products->fetchAll();
?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h2 class="block-title side-lines bottom-line larger-text"><?= e(t('produtos.page_title')) ?></h2>
</div>
</div>
</div>
</div>

<div class="spacer3"></div>
<div class="block">
<div class="spacer1"></div>
<div class="feats" data-equalizer="produtos">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="small-12 medium-8 large-9 cell small-order-2">
<h1 class="block-title side-lines bottom-line medium-text"><?= e(t('produtos.page_title')) ?></h1>
<div class="spacer1"></div>
<div class="grid-x grid-padding-x">
<?php foreach ($products as $p): ?>
<?php $pname = $p['t_name'] ?: $p['name']; ?>
<div class="small-6 medium-6 large-4 cell">
<a href="<?= e(home_url()) ?>produtos/<?= e($p['slug']) ?>/" class="card" data-equalizer-watch="produtos">
<div class="card-image contain h43 shadow">
<?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" alt="<?= e($pname) ?>"><?php endif; ?>
</div>
<div class="card-section"><strong class="title small-text upper"><?= e($pname) ?></strong></div>
</a>
<div class="spacer1"></div>
</div>
<?php endforeach; ?>
</div>
</div>
<?php $activeTypeSlug = null; require __DIR__ . '/../includes/produtos-sidebar.php'; ?>
</div>
</div>
</div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
