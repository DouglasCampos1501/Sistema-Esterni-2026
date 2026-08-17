<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();

$pageTitle = t('noticias.page_title') . ' — Esterni Design e Mobiliário Urbano';
$bodyClass = 'inner-page';
$activeMenu = 'news';

require __DIR__ . '/../includes/header-public.php';

$stmt = db()->prepare(
    "SELECT p.*, m.path AS image_path, t.title AS t_title, t.excerpt AS t_excerpt
     FROM posts p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN post_translations t ON t.post_id = p.id AND t.language_code = ?
     WHERE p.status = 'published'
     ORDER BY p.published_at DESC"
);
$stmt->execute([$lang]);
$posts = $stmt->fetchAll();

?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h1 class="block-title side-lines bottom-line larger-text"><?= e(t('noticias.page_title')) ?></h1>
</div>
</div>
</div>
</div>

<div class="spacer5"></div>
<div class="grid-container">
<div class="grid-x grid-padding-x align-top news" data-equalizer="news">
<?php foreach ($posts as $p): ?>
<?php
$title = $p['t_title'] ?: $p['title'];
$excerpt = $p['t_excerpt'] ?: $p['excerpt'];
$d = $p['published_at'] ? explode('-', substr($p['published_at'], 8, 2) . '-' . substr($p['published_at'], 5, 2)) : null;
?>
<div class="large-4 medium-6 small-12 cell">
<a class="item card" href="<?= e(home_url()) ?>noticias/<?= e($p['slug']) ?>/" data-equalizer-watch="news">
<div class="card-image wide overlay">
<?php if ($p['image_path']): ?><img src="<?= e($p['image_path']) ?>" alt="<?= e($title) ?>"><?php endif; ?>
<?php if ($d): ?><div class="date"><strong><?= e($d[0]) ?></strong> <span><?= e(t_month_short($d[1])) ?></span></div><?php endif; ?>
</div>
<div class="card-section"><div class="text">
<div class="title upper"><?= e($title) ?></div>
<?php if ($excerpt): ?><div class="subtitle"><?= e(mb_strimwidth($excerpt, 0, 140, '…')) ?></div><?php endif; ?>
</div></div>
</a>
<div class="spacer1"></div>
</div>
<?php endforeach; ?>
<?php if (!$posts): ?>
<p style="color:#8a8a8a;padding:2rem 0;"><?= e(t('line.empty_products')) ?></p>
<?php endif; ?>
</div>
</div>
<div class="spacer5"></div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
