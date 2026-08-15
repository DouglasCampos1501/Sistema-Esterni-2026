<?php
// Importa os 78 produtos reais (raspados de esterni.ind.br) a partir de
// products-import-data.json, usando as imagens já baixadas em
// uploads/media/products/. Rodar via CLI: php database/import-products.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();

function register_product_media(PDO $pdo, string $sourceUrl): ?int
{
    if (!$sourceUrl) {
        return null;
    }
    $filename = basename(parse_url($sourceUrl, PHP_URL_PATH));
    $path = '/uploads/media/products/' . $filename;
    $fullPath = BASE_PATH . '/uploads/media/products/' . $filename;

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

$products = json_decode(file_get_contents(__DIR__ . '/products-import-data.json'), true, 512, JSON_THROW_ON_ERROR);

$lineIds = [];
foreach ($pdo->query('SELECT id, slug FROM product_lines') as $row) {
    $lineIds[$row['slug']] = (int) $row['id'];
}
$typeIds = [];
foreach ($pdo->query('SELECT id, slug FROM product_types') as $row) {
    $typeIds[$row['slug']] = (int) $row['id'];
}

$pdo->beginTransaction();
$created = 0;
$updated = 0;
$skipped = [];

foreach ($products as $i => $p) {
    $lineId = $lineIds[$p['line']] ?? null;
    $typeId = $typeIds[$p['type_slug']] ?? null;

    if (!$lineId || !$typeId) {
        $skipped[] = $p['slug'] . ' (linha ou tipo não encontrado)';
        continue;
    }

    $featuredId = register_product_media($pdo, $p['featured_image'] ?? '');
    $dimensionsId = register_product_media($pdo, $p['dimensions_image'] ?? '');

    $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = ?');
    $stmt->execute([$p['slug']]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $stmt = $pdo->prepare(
            'UPDATE products SET line_id=?, type_id=?, name=?, description=?, featured_image_id=?, dimensions_image_id=?, status="published", sort_order=?
             WHERE id=?'
        );
        $stmt->execute([$lineId, $typeId, $p['name'], $p['description'], $featuredId, $dimensionsId, $i + 1, $existingId]);
        $updated++;
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO products (line_id, type_id, slug, name, description, featured_image_id, dimensions_image_id, status, sort_order)
             VALUES (?,?,?,?,?,?,?,"published",?)'
        );
        $stmt->execute([$lineId, $typeId, $p['slug'], $p['name'], $p['description'], $featuredId, $dimensionsId, $i + 1]);
        $created++;
    }
}

$pdo->commit();

echo "Criados: $created | Atualizados: $updated | Total: " . count($products) . "\n";
if ($skipped) {
    echo "Pulados:\n" . implode("\n", $skipped) . "\n";
}
