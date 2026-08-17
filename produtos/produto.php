<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();
$slug = trim($_GET['slug'] ?? '');

$stmt = db()->prepare(
    "SELECT p.*, m.path AS image_path, d.path AS dim_path,
            t.name AS t_name, t.summary AS t_summary, t.description AS t_description,
            l.slug AS line_slug, l.name AS line_name, lt.name AS line_t_name,
            ty.slug AS type_slug, ty.name AS type_name, tyt.name AS type_t_name
     FROM products p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN media d ON d.id = p.dimensions_image_id
     LEFT JOIN product_translations t ON t.product_id = p.id AND t.language_code = ?
     JOIN product_lines l ON l.id = p.line_id
     LEFT JOIN product_line_translations lt ON lt.line_id = l.id AND lt.language_code = ?
     JOIN product_types ty ON ty.id = p.type_id
     LEFT JOIN product_type_translations tyt ON tyt.type_id = ty.id AND tyt.language_code = ?
     WHERE p.slug = ? AND p.status = 'published'"
);
$stmt->execute([$lang, $lang, $lang, $slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = t('error.not_found_title');
    $bodyClass = 'inner-page';
    require __DIR__ . '/../includes/header-public.php';
    echo '<div class="block"><div class="grid-container"><div class="grid-x grid-padding-x align-center"><div class="cell text-center" style="padding:4rem 0;"><h1>404</h1><p><a href="' . e(home_url()) . 'produtos/">' . e(t('produtos.page_title')) . '</a></p></div></div></div></div>';
    require __DIR__ . '/../includes/footer-public.php';
    exit;
}

$name = $product['t_name'] ?: $product['name'];
$summary = $product['t_summary'] ?: $product['summary'];
$description = $product['t_description'] ?: $product['description'];
$lineName = $product['line_t_name'] ?: $product['line_name'];
$typeName = $product['type_t_name'] ?: $product['type_name'];

$pageTitle = $name . ' — Esterni Design e Mobiliário Urbano';
$pageDescription = $summary ?: strip_tags($description ?? $name);
$bodyClass = 'inner-page';
$activeMenu = 'products';

require __DIR__ . '/../includes/header-public.php';
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

<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="small-12 medium-6 large-7 large-order-2 cell medium-order-1 small-order-1">
<div class="right-column">
<div class="card">
<div class="card-image contain wide shadow">
<?php if ($product['image_path']): ?><img src="<?= e($product['image_path']) ?>" alt="<?= e($name) ?>"><?php endif; ?>
</div>
</div>
<div class="spacer1"></div>
</div>
</div>
<div class="small-12 medium-6 large-5 large-order-1 cell medium-order-2 small-order-2">
<div class="left-column">
<h1 class="block-title large-text side-lines bottom-line upper"><?= e($name) ?></h1>
<div class="subtitle">
<?php if ($summary): ?><p><?= e($summary) ?></p><?php endif; ?>
<?= $description ?>
<ul>
<li><?= e(t('menu.lines')) ?>: <strong><?= e($lineName) ?></strong></li>
<li><?= e(t('produtos.type_prefix')) ?> <strong><?= e($typeName) ?></strong></li>
</ul>
</div>
<div class="spacer1"></div>
<?php if ($product['dim_path']): ?>
<div>
<div class="title"><?= e(t('produto.dimensions_title')) ?></div>
<div class="spacer05"></div>
<div class="image wide"><img src="<?= e($product['dim_path']) ?>" alt="<?= e(t('produto.dimensions_title')) ?>" style="object-fit: contain"></div>
</div>
<div class="spacer2"></div>
<?php endif; ?>
<div class="spacer05"></div>
<a href="<?= e(home_url()) ?>linhas/<?= e($product['line_slug']) ?>/" class="button hollow small"><?= sprintf(e(t('produto.line_cta')), e($lineName)) ?> ➧</a>
<br>
<a href="<?= e(home_url()) ?>tipos/<?= e($product['type_slug']) ?>/" class="button hollow small"><?= sprintf(e(t('produto.type_cta')), e($typeName)) ?> ➧</a>
</div>
</div>
</div>
</div>
<div class="spacer4"></div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
