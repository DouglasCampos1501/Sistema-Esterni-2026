<?php
// Traduz as descrições dos 78 produtos pra EN/ES/IT reaproveitando um dicionário de
// parágrafos (a maioria das descrições é montada a partir de um conjunto pequeno de
// parágrafos reaproveitados entre produtos — só 18 textos únicos entre os 78 produtos,
// 22 parágrafos únicos entre esses 18). Rodar via CLI: php database/translate-products.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$dictPath = 'C:/Users/Douglas/AppData/Local/Temp/esterni-translate/paragraph-translations.json';
$dict = json_decode(file_get_contents($dictPath), true, 512, JSON_THROW_ON_ERROR);

$pdo = db();
$products = $pdo->query('SELECT id, slug, description FROM products')->fetchAll();

$languages = ['en', 'es', 'it'];
$missing = [];
$updated = 0;

$pdo->beginTransaction();

foreach ($products as $p) {
    if (!$p['description']) {
        continue;
    }
    preg_match_all('/<p>(.*?)<\/p>/s', $p['description'], $m);
    $paragraphs = $m[1];

    foreach ($languages as $lang) {
        $translatedParas = [];
        $ok = true;
        foreach ($paragraphs as $para) {
            $key = trim($para);
            if (!isset($dict[$key][$lang])) {
                $missing[] = "$lang :: {$p['slug']} :: " . mb_substr($key, 0, 60);
                $ok = false;
                continue;
            }
            $translatedParas[] = '<p>' . $dict[$key][$lang] . '</p>';
        }
        if (!$ok || !$translatedParas) {
            continue;
        }
        $translatedDescription = implode("\n", $translatedParas);

        $stmt = $pdo->prepare(
            'INSERT INTO product_translations (product_id, language_code, description) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE description = VALUES(description)'
        );
        $stmt->execute([$p['id'], $lang, $translatedDescription]);
        $updated++;
    }
}

$pdo->commit();

echo "Traduções gravadas: $updated\n";
if ($missing) {
    echo "Parágrafos sem tradução no dicionário:\n" . implode("\n", array_unique($missing)) . "\n";
}
