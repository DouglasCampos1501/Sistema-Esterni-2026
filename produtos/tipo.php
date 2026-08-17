<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();
$slug = trim($_GET['slug'] ?? '');

$stmt = db()->prepare(
    'SELECT t.*, tt.name AS t_name FROM product_types t
     LEFT JOIN product_type_translations tt ON tt.type_id = t.id AND tt.language_code = ?
     WHERE t.slug = ? AND t.active = 1'
);
$stmt->execute([$lang, $slug]);
$type = $stmt->fetch();

if (!$type) {
    http_response_code(404);
    $pageTitle = t('error.not_found_title');
    $bodyClass = 'inner-page';
    require __DIR__ . '/../includes/header-public.php';
    echo '<div class="block"><div class="grid-container"><div class="grid-x grid-padding-x align-center"><div class="cell text-center" style="padding:4rem 0;"><h1>404</h1><p><a href="' . e(home_url()) . 'produtos/">' . e(t('produtos.page_title')) . '</a></p></div></div></div></div>';
    require __DIR__ . '/../includes/footer-public.php';
    exit;
}

$typeName = $type['t_name'] ?: $type['name'];

$pageTitle = t('produtos.type_prefix') . ' ' . $typeName . ' — Esterni Design e Mobiliário Urbano';
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
     WHERE p.status = 'published' AND p.type_id = ?
     ORDER BY p.line_id, p.sort_order, p.id"
);
$products->execute([$lang, $type['id']]);
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
<h1 class="block-title side-lines bottom-line medium-text"><?= e(t('produtos.type_prefix')) ?> <span><?= e($typeName) ?></span></h1>
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
<?php if (!$products): ?>
<p style="color:#8a8a8a;"><?= e(t('line.empty_products')) ?></p>
<?php endif; ?>
</div>
</div>
<?php $activeTypeSlug = $type['slug']; require __DIR__ . '/../includes/produtos-sidebar.php'; ?>
</div>
</div>
</div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
