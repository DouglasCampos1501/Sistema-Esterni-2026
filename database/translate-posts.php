<?php
// Traduz as 32 notícias reais pra EN/ES/IT a partir de um dicionário de traduções
// (título + resumo + conteúdo, escritos do zero por serem conteúdo editorial único,
// diferente das descrições de produto que reaproveitam textos entre si).
// Rodar via CLI: php database/translate-posts.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$dictPath = 'C:/Users/Douglas/AppData/Local/Temp/esterni-translate/posts-translations.json';
$dict = json_decode(file_get_contents($dictPath), true, 512, JSON_THROW_ON_ERROR);

$pdo = db();
$posts = $pdo->query('SELECT id, slug FROM posts')->fetchAll();

$languages = ['en', 'es', 'it'];
$updated = 0;
$missing = [];

$pdo->beginTransaction();

foreach ($posts as $p) {
    if (!isset($dict[$p['slug']])) {
        $missing[] = $p['slug'];
        continue;
    }
    foreach ($languages as $lang) {
        if (!isset($dict[$p['slug']][$lang])) {
            continue;
        }
        $tr = $dict[$p['slug']][$lang];
        $stmt = $pdo->prepare(
            'INSERT INTO post_translations (post_id, language_code, title, excerpt, content) VALUES (?,?,?,?,?)
             ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content)'
        );
        $stmt->execute([$p['id'], $lang, $tr['title'], $tr['excerpt'], $tr['content']]);
        $updated++;
    }
}

$pdo->commit();

echo "Traduções gravadas: $updated\n";
if ($missing) {
    echo "Slugs sem tradução no dicionário:\n" . implode("\n", $missing) . "\n";
}
