<?php
// Importa as 32 notícias reais (raspadas de esterni.ind.br) a partir de
// noticias-import-data.json, usando as imagens já baixadas em
// uploads/media/news/. Rodar via CLI: php database/import-noticias.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();

function register_news_media(PDO $pdo, string $sourceUrl): ?int
{
    if (!$sourceUrl) {
        return null;
    }
    $filename = basename(parse_url($sourceUrl, PHP_URL_PATH));
    $path = '/uploads/media/news/' . $filename;
    $fullPath = BASE_PATH . '/uploads/media/news/' . $filename;

    if (!file_exists($fullPath)) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id FROM media WHERE path = ?');
    $stmt->execute([$path]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        return (int) $existing;
    }

    $size = filesize($fullPath);
    [$width, $height] = getimagesize($fullPath) ?: [null, null];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png';

    $stmt = $pdo->prepare('INSERT INTO media (filename, path, mime_type, size_bytes, width, height) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$filename, $path, $mime, $size, $width, $height]);
    return (int) $pdo->lastInsertId();
}

$posts = json_decode(file_get_contents(__DIR__ . '/noticias-import-data.json'), true, 512, JSON_THROW_ON_ERROR);

$pdo->beginTransaction();
$created = 0;
$updated = 0;

foreach ($posts as $p) {
    $imageId = register_news_media($pdo, $p['image'] ?? '');

    $stmt = $pdo->prepare('SELECT id FROM posts WHERE slug = ?');
    $stmt->execute([$p['slug']]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $stmt = $pdo->prepare(
            'UPDATE posts SET title=?, excerpt=?, content=?, featured_image_id=?, status="published", published_at=? WHERE id=?'
        );
        $stmt->execute([$p['title'], $p['excerpt'], $p['content'], $imageId, $p['published_at'], $existingId]);
        $updated++;
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO posts (slug, title, excerpt, content, featured_image_id, status, published_at)
             VALUES (?,?,?,?,?,"published",?)'
        );
        $stmt->execute([$p['slug'], $p['title'], $p['excerpt'], $p['content'], $imageId, $p['published_at']]);
        $created++;
    }
}

$pdo->commit();
echo "Criados: $created | Atualizados: $updated | Total: " . count($posts) . "\n";
