<?php
// Script de seed único: popula product_lines/product_types (+ traduções) com o
// conteúdo real do site, reaproveitando as imagens já baixadas em
// uploads/media/home/. Rodar via CLI: php database/seed-lines-types.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();

function register_media(PDO $pdo, string $filename): int
{
    $path = '/uploads/media/home/' . $filename;
    $fullPath = BASE_PATH . '/uploads/media/home/' . $filename;

    $stmt = $pdo->prepare('SELECT id FROM media WHERE path = ?');
    $stmt->execute([$path]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        return (int) $existing;
    }

    $size = file_exists($fullPath) ? filesize($fullPath) : 0;
    [$width, $height] = file_exists($fullPath) ? (getimagesize($fullPath) ?: [null, null]) : [null, null];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        default => 'application/octet-stream',
    };

    $stmt = $pdo->prepare(
        'INSERT INTO media (filename, path, mime_type, size_bytes, width, height) VALUES (?,?,?,?,?,?)'
    );
    $stmt->execute([$filename, $path, $mime, $size, $width, $height]);
    return (int) $pdo->lastInsertId();
}

$lines = [
    ['slug' => 'misan', 'name' => 'Misan', 'img' => 'Conjunto-Misan-v2-1024x470.png'],
    ['slug' => 'vega', 'name' => 'Vega', 'img' => 'Conjunto-Vega-1024x470.png'],
    ['slug' => 's-park', 'name' => 'S.Park', 'img' => 'Linha-S.Park_-1.png'],
    ['slug' => 'capella', 'name' => 'Capella', 'img' => 'Conjunto-Capella-1-1024x470.png'],
    ['slug' => 'betria', 'name' => 'Betria', 'img' => 'betria-1024x393.png'],
    ['slug' => 'adhara', 'name' => 'Adhara', 'img' => 'Conjunto-Adhara-1024x470.png'],
    ['slug' => 'naos-colors', 'name' => 'Naos Colors', 'img' => 'naos-colors-1024x470.png'],
    ['slug' => 'naos-metalico', 'name' => 'Naos Metálico', 'img' => 'naos-metalico-1024x470.png'],
    ['slug' => 'naos-madeira', 'name' => 'Naos Madeira', 'img' => 'Conjunto-Naos-Madeira-1024x470.png'],
    ['slug' => 'aludra', 'name' => 'Aludra', 'img' => 'Conjunto-Aludra-v2-1024x557.png'],
];

$types = [
    ['slug' => 'balizador', 'name' => 'Balizadores', 'img' => 'balizadores-480x270.jpg'],
    ['slug' => 'banco', 'name' => 'Bancos', 'img' => 'bancos-480x270.jpg'],
    ['slug' => 'bicicletario', 'name' => 'Bicicletários', 'img' => 'bicicletarios-480x270.jpg'],
    ['slug' => 'cerca', 'name' => 'Cercas', 'img' => 'cercas-480x270.jpg'],
    ['slug' => 'cinzeiro', 'name' => 'Cinzeiros', 'img' => 'cinzeiros-480x270.jpg'],
    ['slug' => 'floreira', 'name' => 'Floreiras', 'img' => 'floreiras-480x270.jpg'],
    ['slug' => 'lixeira', 'name' => 'Lixeiras', 'img' => 'lixeiras-480x270.jpg'],
    ['slug' => 'mesa', 'name' => 'Mesa', 'img' => 'Categoria-mesa.png'],
    ['slug' => 'ponto-de-informacoes', 'name' => 'Pontos de Informações', 'img' => 'pontos-de-informacao-480x270.jpg'],
    ['slug' => 'ponto-de-onibus', 'name' => 'Pontos de Ônibus', 'img' => 'pontos-de-onibus-480x270.jpg'],
    ['slug' => 'poste', 'name' => 'Postes', 'img' => 'postes-480x270.jpg'],
];

// Nomes de linhas são nomes próprios — não traduzidos. Traduções de tipos reaproveitam
// os mesmos textos já usados na home (ui_strings type.*), repetidos aqui pra manter
// product_types autossuficiente (a home ainda usa o array fixo, mas o admin/CRUD passa
// a ser a fonte de verdade a partir de agora).
$typeTranslations = [
    'balizador' => ['en' => 'Bollards', 'es' => 'Balizas', 'it' => 'Delimitatori'],
    'banco' => ['en' => 'Benches', 'es' => 'Bancos', 'it' => 'Panchine'],
    'bicicletario' => ['en' => 'Bike Racks', 'es' => 'Aparcabicicletas', 'it' => 'Portabiciclette'],
    'cerca' => ['en' => 'Fences', 'es' => 'Cercas', 'it' => 'Recinzioni'],
    'cinzeiro' => ['en' => 'Ashtrays', 'es' => 'Ceniceros', 'it' => 'Posacenere'],
    'floreira' => ['en' => 'Planters', 'es' => 'Jardineras', 'it' => 'Fioriere'],
    'lixeira' => ['en' => 'Trash Bins', 'es' => 'Papeleras', 'it' => 'Cestini'],
    'mesa' => ['en' => 'Table', 'es' => 'Mesa', 'it' => 'Tavolo'],
    'ponto-de-informacoes' => ['en' => 'Information Points', 'es' => 'Puntos de Información', 'it' => 'Punti Informativi'],
    'ponto-de-onibus' => ['en' => 'Bus Stops', 'es' => 'Paradas de Autobús', 'it' => 'Fermate Autobus'],
    'poste' => ['en' => 'Poles', 'es' => 'Postes', 'it' => 'Pali'],
];

$pdo->beginTransaction();

foreach ($lines as $i => $line) {
    $mediaId = register_media($pdo, $line['img']);
    $stmt = $pdo->prepare(
        'INSERT INTO product_lines (slug, name, featured_image_id, sort_order, active) VALUES (?,?,?,?,1)
         ON DUPLICATE KEY UPDATE name = VALUES(name), featured_image_id = VALUES(featured_image_id), sort_order = VALUES(sort_order)'
    );
    $stmt->execute([$line['slug'], $line['name'], $mediaId, $i + 1]);
    echo "Linha: {$line['name']}\n";
}

foreach ($types as $i => $type) {
    $mediaId = register_media($pdo, $type['img']);
    $stmt = $pdo->prepare(
        'INSERT INTO product_types (slug, name, icon_image_id, sort_order, active) VALUES (?,?,?,?,1)
         ON DUPLICATE KEY UPDATE name = VALUES(name), icon_image_id = VALUES(icon_image_id), sort_order = VALUES(sort_order)'
    );
    $stmt->execute([$type['slug'], $type['name'], $mediaId, $i + 1]);

    $typeId = (int) $pdo->lastInsertId();
    if ($typeId === 0) {
        $find = $pdo->prepare('SELECT id FROM product_types WHERE slug = ?');
        $find->execute([$type['slug']]);
        $typeId = (int) $find->fetchColumn();
    }

    foreach ($typeTranslations[$type['slug']] as $langCode => $name) {
        $stmtTr = $pdo->prepare(
            'INSERT INTO product_type_translations (type_id, language_code, name) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE name = VALUES(name)'
        );
        $stmtTr->execute([$typeId, $langCode, $name]);
    }
    echo "Tipo: {$type['name']}\n";
}

$pdo->commit();
echo "OK — " . count($lines) . " linhas e " . count($types) . " tipos.\n";
