<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/i18n.php';

$lang = current_language();
$slug = trim($_GET['slug'] ?? '');

$stmt = db()->prepare(
    "SELECT p.*, m.path AS image_path, t.title AS t_title, t.excerpt AS t_excerpt, t.content AS t_content
     FROM posts p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN post_translations t ON t.post_id = p.id AND t.language_code = ?
     WHERE p.slug = ? AND p.status = 'published'"
);
$stmt->execute([$lang, $slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Página não encontrada — Esterni';
    $bodyClass = 'inner-page';
    require __DIR__ . '/../includes/header-public.php';
    echo '<div class="block"><div class="grid-container"><div class="grid-x grid-padding-x align-center"><div class="cell text-center" style="padding:4rem 0;"><h1>404</h1><p><a href="' . e(home_url()) . 'noticias/">' . e(t('noticias.page_title')) . '</a></p></div></div></div></div>';
    require __DIR__ . '/../includes/footer-public.php';
    exit;
}

$title = $post['t_title'] ?: $post['title'];
$excerpt = $post['t_excerpt'] ?: $post['excerpt'];
$content = $post['t_content'] ?: $post['content'];

$pageTitle = $title . ' — Esterni Design e Mobiliário Urbano';
$pageDescription = $excerpt ?: strip_tags((string) $content);
$bodyClass = 'inner-page';
$activeMenu = 'news';

require __DIR__ . '/../includes/header-public.php';

$othersStmt = db()->prepare(
    "SELECT p.*, m.path AS image_path, t.title AS t_title
     FROM posts p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN post_translations t ON t.post_id = p.id AND t.language_code = ?
     WHERE p.status = 'published' AND p.id != ?
     ORDER BY p.published_at DESC LIMIT 3"
);
$othersStmt->execute([$lang, $post['id']]);
$others = $othersStmt->fetchAll();

$dateFormatted = $post['published_at'] ? date('d \d\e m \d\e Y', strtotime($post['published_at'])) : '';
?>

<div class="color-box padding white">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center">
<div class="medium-12 large-12 cell text-center">
<h2 class="block-title side-lines bottom-line larger-text"><?= e(t('noticias.page_title')) ?></h2>
</div>
</div>
</div>
</div>

<div class="spacer5"></div>
<div class="block news">
<div class="grid-container" style="position: relative;">
<div class="grid-x grid-padding-x align-top align-center">
<div class="large-9 small-12 cell">
<div class="left-column">
<h1 class="title large-text upper"><?= e($title) ?></h1>
<div class="spacer05"></div>
<?php if ($dateFormatted): ?><div class="date-line" style="color:#8a8a8a;"><?= e($dateFormatted) ?></div><?php endif; ?>
<div class="spacer1"></div>
<?php if ($post['image_path']): ?>
<div class="image wide"><img src="<?= e($post['image_path']) ?>" alt="<?= e($title) ?>"></div>
<div class="spacer1"></div>
<?php endif; ?>
<div class="justify"><?= $content ?></div>
</div>

<?php if ($others): ?>
<div class="spacer4"></div>
<h2 class="title large-text side-lines bottom-line small-text-center upper"><?= e(t('noticias.more_posts')) ?></h2>
<div class="spacer1"></div>
<div class="grid-x grid-padding-x align-top news" data-equalizer="news2">
<?php foreach ($others as $o): ?>
<?php $oTitle = $o['t_title'] ?: $o['title']; ?>
<div class="large-4 medium-4 small-12 cell">
<div class="item card" data-equalizer-watch="news2">
<a href="<?= e(home_url()) ?>noticias/<?= e($o['slug']) ?>/">
<div class="grid-x align-middle">
<div class="small-5 medium-12 large-12 cell">
<div class="card-image h43 overlay">
<?php if ($o['image_path']): ?><img src="<?= e($o['image_path']) ?>" alt="<?= e($oTitle) ?>"><?php endif; ?>
</div>
</div>
<div class="small-7 medium-12 large-12 cell">
<div class="card-section"><div class="text"><div class="title upper"><?= e($oTitle) ?></div></div></div>
</div>
</div>
</a>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
</div>
<div class="spacer4"></div>
</div>

<?php require __DIR__ . '/../includes/footer-public.php'; ?>
