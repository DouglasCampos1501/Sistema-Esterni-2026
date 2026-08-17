<?php
declare(strict_types=1);

$bodyClass = 'home page-template-template-homepage';
$activeMenu = 'home';

require __DIR__ . '/includes/header-public.php';

$linesStmt = db()->prepare(
    'SELECT l.slug, m.path AS img, COALESCE(lt.name, l.name) AS name
     FROM product_lines l
     LEFT JOIN media m ON m.id = l.featured_image_id
     LEFT JOIN product_line_translations lt ON lt.line_id = l.id AND lt.language_code = ?
     WHERE l.active = 1 ORDER BY l.sort_order, l.id'
);
$linesStmt->execute([$lang]);
$lines = $linesStmt->fetchAll();

$typesStmt = db()->prepare(
    'SELECT t.slug, m.path AS img, COALESCE(tt.name, t.name) AS name
     FROM product_types t
     LEFT JOIN media m ON m.id = t.icon_image_id
     LEFT JOIN product_type_translations tt ON tt.type_id = t.id AND tt.language_code = ?
     WHERE t.active = 1 ORDER BY t.sort_order, t.id'
);
$typesStmt->execute([$lang]);
$types = $typesStmt->fetchAll();

$postsStmt = db()->prepare(
    "SELECT p.slug, p.published_at, m.path AS image_path,
            COALESCE(t.title, p.title) AS title, COALESCE(t.excerpt, p.excerpt) AS excerpt
     FROM posts p
     LEFT JOIN media m ON m.id = p.featured_image_id
     LEFT JOIN post_translations t ON t.post_id = p.id AND t.language_code = ?
     WHERE p.status = 'published'
     ORDER BY p.published_at DESC
     LIMIT 3"
);
$postsStmt->execute([$lang]);
$homePosts = $postsStmt->fetchAll();


$home = '/uploads/media/home/';
?>

<div class="slick main-slider" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "autoplay": true, "autoplaySpeed": 6000, "pauseOnHover": false, "fade": true, "cssEase": "linear"}'>
<div class="slide">
<div class="card">
<div class="card-image wide"><img src="<?= $home ?>CENA-5305-P.jpg" alt=""></div>
<div class="slide-caption">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="cell large-6 medium-7 small-12">
<div class="slide-caption-inner">
<div class="subtitle"><?= e(t('home.hero1.subtitle')) ?></div>
<h1 class="title large-text"><?= e(t('home.hero1.title')) ?></h1>
<a href="<?= e(home_url()) ?>sobre/" class="button secondary hollow"><?= e(t('home.hero1.cta')) ?> <i class="fas fa-arrow-right"></i></a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="slide">
<div class="card">
<div class="card-image wide"><img src="<?= $home ?>CENA-5311-P.jpg" alt=""></div>
<div class="slide-caption">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="cell large-6 medium-7 small-12">
<div class="slide-caption-inner">
<div class="subtitle"><?= e(t('home.hero2.subtitle')) ?></div>
<h1 class="title large-text"><?= e(t('home.hero2.title')) ?></h1>
<a href="<?= e(home_url()) ?>linhas/" class="button secondary hollow"><?= e(t('home.hero2.cta')) ?> <i class="fas fa-arrow-right"></i></a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="slide">
<div class="card">
<div class="card-image wide"><img src="<?= $home ?>IMG_5882-1920x1440.jpeg" alt=""></div>
<div class="slide-caption">
<div class="grid-container">
<div class="grid-x grid-padding-x">
<div class="cell large-6 medium-7 small-12">
<div class="slide-caption-inner">
<div class="subtitle"><?= e(t('home.hero3.subtitle')) ?></div>
<h1 class="title large-text"><?= e(t('home.hero3.title')) ?></h1>
<a href="<?= e(home_url()) ?>linhas/" class="button secondary hollow"><?= e(t('home.hero3.cta')) ?> <i class="fas fa-arrow-right"></i></a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<div class="block">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle">
<div class="auto cell text-center">
<div class="spacer6"></div>
<a href="<?= e(home_url()) ?>linhas/" class="block-title-link">
<div class="block-title large-text text-center side-lines bottom-line"><?= e(t('home.lines.title')) ?><div class="see-more"><?= e(t('home.see_more')) ?></div></div>
</a>
<div class="spacer2"></div>
</div>
</div>
</div>
</div>

<div class="block">
<div class="feats smaller" data-equalizer="feats-s">
<div class="grid-container">
<div class="card-slider slick spaced arrows-out color-arrows" data-slick='{"slidesToShow": 3, "slidesToScroll": 1, "autoplay": true, "autoplaySpeed": 2000, "infinite": true, "responsive": [{"breakpoint": 1024, "settings": {"slidesToShow": 3, "slidesToScroll": 1}}, {"breakpoint": 540, "settings": {"slidesToShow": 1, "slidesToScroll": 1}}]}'>
<?php foreach ($lines as $line): ?>
<div class="slide">
<a href="<?= e(home_url()) ?>linhas/<?= e($line['slug']) ?>/" class="card" data-equalizer-watch="feats-s">
<div class="card-image contain horizontal"><?php if ($line['img']): ?><img src="<?= e($line['img']) ?>" alt="<?= e($line['name']) ?>"><?php endif; ?></div>
<div class="card-section"><strong class="upper"><?= e(t('line.prefix')) ?> <?= e($line['name']) ?></strong></div>
</a>
</div>
<?php endforeach; ?>
</div>
</div>
<div class="spacer5"></div>
</div>
</div>

<div class="hero auto-hero light-hero small-center" data-parallax="scroll" data-image-src="<?= $home ?>aludra-cam02-prv1.jpg" data-position="center center" data-ios-fix="true">
<div class="overlay light-overlay"></div>
<div class="spacer4"></div>
<div class="hero-caption relative">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle align-center">
<div class="medium-6 cell small-order-2 medium-order-1">
<div class="left-column">
<h2 class="title large-text"><?= e(t('home.about.title')) ?></h2>
<div class="spacer1"></div>
<p class="justify"><?= e(t('home.about.text')) ?>
<div class="spacer1"></div>
<a href="<?= e(home_url()) ?>sobre/" class="button hollow"><?= e(t('home.about.cta')) ?> <i class="fas fa-arrow-right"></i></a>
</div>
</div>
<div class="medium-6 large-5 cell small-order-1 medium-order-2">
<div class="right-column">
<div class="svg-img"><img src="<?= $home ?>profile.png" style="display:block;width:18rem;max-width:50vw;height:auto;margin:0 auto;" alt=""></div>
<div class="spacer3 hide-for-large"></div>
</div>
</div>
</div>
</div>
</div>
<div class="spacer4"></div>
</div>

<div class="block">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle">
<div class="auto cell text-center">
<div class="spacer6"></div>
<a href="<?= e(home_url()) ?>produtos/" class="block-title-link">
<div class="block-title large-text text-center side-lines bottom-line"><?= e(t('home.products.title')) ?><div class="see-more"><?= e(t('home.see_more')) ?></div></div>
</a>
<div class="spacer4"></div>
</div>
</div>
<div class="grid-x grid-padding-x">
<div class="small-12 cell">
<div class="grid-x grid-padding-x align-center small-up-2 medium-up-3 large-up-5" data-equalizer="produtos">
<?php foreach ($types as $type): ?>
<div class="cell">
<a href="<?= e(home_url()) ?>tipos/<?= e($type['slug']) ?>/" class="card" data-equalizer-watch="produtos">
<div class="card-image contain wide shadow-img"><?php if ($type['img']): ?><img src="<?= e($type['img']) ?>" alt="<?= e($type['name']) ?>"><?php endif; ?></div>
<div class="card-section"><strong class="upper"><?= e($type['name']) ?></strong></div>
</a>
<div class="spacer1"></div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</div>
</div>

<div class="block">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle">
<div class="auto cell text-center">
<a href="<?= e(home_url()) ?>noticias/" class="block-title-link">
<div class="spacer6"></div>
<div class="block-title large-text text-center side-lines bottom-line"><?= e(t('home.news.title')) ?><div class="see-more"><?= e(t('home.see_more')) ?></div></div>
<div class="spacer4"></div>
</a>
</div>
</div>
</div>
</div>

<?php
$homePostDate = static function (?string $publishedAt): ?array {
    if (!$publishedAt) {
        return null;
    }
    return [substr($publishedAt, 8, 2), t_month_short(substr($publishedAt, 5, 2))];
};
$homeMainPost = $homePosts[0] ?? null;
$homeOtherPosts = array_slice($homePosts, 1);
?>
<?php if ($homeMainPost): ?>
<div class="grid-container">
<div class="grid-x grid-padding-x" data-equalizer="news">
<div class="small-12 cell">
<div class="news">
<div class="grid-x grid-padding-x">
<div class="small-12 medium-6 cell">
<?php $d = $homePostDate($homeMainPost['published_at']); ?>
<a class="item card large" href="<?= e(home_url()) ?>noticias/<?= e($homeMainPost['slug']) ?>/" data-equalizer-watch="news">
<div class="card-image wide overlay">
<?php if ($homeMainPost['image_path']): ?><img src="<?= e($homeMainPost['image_path']) ?>" alt="<?= e($homeMainPost['title']) ?>"><?php endif; ?>
<?php if ($d): ?><div class="date"><strong><?= e($d[0]) ?></strong> <span><?= e($d[1]) ?></span></div><?php endif; ?>
</div>
<div class="card-section"><div class="text">
<div class="title upper"><?= e($homeMainPost['title']) ?></div>
<div class="subtitle"><?= e($homeMainPost['excerpt'] ? mb_strimwidth($homeMainPost['excerpt'], 0, 110, '…') : '') ?></div>
</div></div>
</a>
<div class="spacer1 hide-for-medium"></div>
</div>
<?php if ($homeOtherPosts): ?>
<div class="small-12 medium-6 cell card-list" data-equalizer-watch="news">
<?php foreach ($homeOtherPosts as $post): ?>
<?php $d = $homePostDate($post['published_at']); ?>
<a class="item card" href="<?= e(home_url()) ?>noticias/<?= e($post['slug']) ?>/">
<div class="grid-x align-top">
<div class="small-4 cell"><div class="card-image h43 overlay"><?php if ($post['image_path']): ?><img src="<?= e($post['image_path']) ?>" alt="<?= e($post['title']) ?>"><?php endif; ?><?php if ($d): ?><div class="date"><strong><?= e($d[0]) ?></strong> <span><?= e($d[1]) ?></span></div><?php endif; ?></div></div>
<div class="small-8 cell"><div class="card-section"><div class="text">
<div class="title upper"><?= e($post['title']) ?></div>
<div class="subtitle"><?= e($post['excerpt'] ? mb_strimwidth($post['excerpt'], 0, 110, '…') : '') ?></div>
</div></div></div>
</div>
</a>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
<?php endif; ?>
<div class="spacer1"></div>
<div class="spacer6"></div>

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

<?php require __DIR__ . '/includes/footer-public.php'; ?>
