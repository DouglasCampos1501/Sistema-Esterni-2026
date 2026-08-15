<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();
$slug = trim($_GET['slug'] ?? '');

$stmt = db()->prepare(
    'SELECT l.*, m.path AS image_path, t.name AS t_name, t.description AS t_description
     FROM product_lines l
     LEFT JOIN media m ON m.id = l.featured_image_id
     LEFT JOIN product_line_translations t ON t.line_id = l.id AND t.language_code = ?
     WHERE l.slug = ? AND l.active = 1'
);
$stmt->execute([$lang, $slug]);
$line = $stmt->fetch();

if (!$line) {
    http_response_code(404);
    $pageTitle = 'Página não encontrada — Esterni';
    $bodyClass = 'inner-page';
    require __DIR__ . '/../includes/header-public.php';
    echo '<div class="block"><div class="grid-container"><div class="grid-x grid-padding-x align-center"><div class="cell text-center" style="padding:4rem 0;"><h1>404</h1><p><a href="' . e(home_url()) . 'linhas/">' . e(t('home.lines.title')) . '</a></p></div></div></div></div>';
    require __DIR__ . '/../includes/footer-public.php';
    exit;
}

$name = $line['t_name'] ?: $line['name'];
$description = $line['t_description'] ?: $line['description'];

$pageTitle = t('line.prefix') . ' ' . $name . ' — Esterni Design e Mobiliário Urbano';
$pageDescription = $description ?: $pageTitle;
$bodyClass = 'inner-page';
$activeMenu = 'lines';

require __DIR__ . '/../includes/header-public.php';

$prodStmt = db()->prepare(
    "SELECT p.*, m.path AS image_path, t.name AS t_name
     FROM products p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN product_translations t ON t.product_id = p.id AND t.language_code = ?
     WHERE p.line_id = ? AND p.status = 'published'
     ORDER BY p.sort_order, p.id"
);
$prodStmt->execute([$lang, $line['id']]);
$products = $prodStmt->fetchAll();
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

<div class="spacer5"></div>
<div class="block">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="small-12 medium-12 large-5 cell large-order-1 medium-order-2 small-order-2 small-center">
<div class="block-title large-text side-lines bottom-line small-center"><?= e(t('line.prefix')) ?> <?= e($name) ?></div>
<div class="spacer1"></div>
<?php if ($description): ?><div class="subtitle"><p><?= e($description) ?></p></div><div class="spacer05"></div><?php endif; ?>
<a href="#produtos" class="button hollow small" style="margin:0"><?= e(t('line.jump_button')) ?> 🡇</a>
</div>
<div class="auto cell large-order-1 medium-order-1 small-order-1">
<div class="right-column">
<div class="card">
<div class="card-image contain horizontal">
<?php if ($line['image_path']): ?><img src="<?= e($line['image_path']) ?>" alt="<?= e($name) ?>"><?php endif; ?>
</div>
</div>
<div class="spacer2 hide-for-large"></div>
</div>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>

<div style="box-shadow: inset 0 3rem 3rem rgba(0,0,0,0.05);">
<div id="produtos" style="position: relative; top: -3rem;"></div>
<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="small-12">
<div class="block-title large-text text-center side-lines bottom-line"><?= sprintf(e(t('line.products_heading')), e($name)) ?></div>
<div class="spacer1"></div>
</div>
<div class="small-12 cell">
<?php if ($products): ?>
<div class="grid-x grid-padding-x">
<?php foreach ($products as $p): ?>
<?php $pname = $p['t_name'] ?: $p['name']; ?>
<div class="cell small-6 medium-4 large-3">
<a href="<?= e(home_url()) ?>produtos/<?= e($p['slug']) ?>/" class="card">
<div class="card-image contain h43">
<?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" alt="<?= e($pname) ?>"><?php endif; ?>
</div>
<div class="card-section"><strong class="upper"><?= e($pname) ?></strong></div>
</a>
<div class="spacer1"></div>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p class="text-center" style="color:#8a8a8a;"><?= e(t('line.empty_products')) ?></p>
<?php endif; ?>
</div>
</div>
</div>
<div class="spacer5"></div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
