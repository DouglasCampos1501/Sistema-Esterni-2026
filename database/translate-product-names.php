<?php
// Traduz os NOMES dos 78 produtos (não só as descrições) pra EN/ES/IT, substituindo
// as palavras de "tipo" (Banco, Lixeira, Poste...) e qualificadores (com/sem encosto,
// unitário, de Piso, Suspensa) por um dicionário — nomes de linha (Misan, Aludra,
// Naos Colors...) são nomes próprios e ficam como estão em qualquer idioma.
// Rodar via CLI: php database/translate-product-names.php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$phrasesPath = 'C:/Users/Douglas/AppData/Local/Temp/esterni-translate/name-phrases.json';
$phrases = json_decode(file_get_contents($phrasesPath), true, 512, JSON_THROW_ON_ERROR);

// já vem ordenado do mais específico (frases longas) pro mais genérico (uma palavra),
// que é a ordem certa pra preg_replace não quebrar uma frase maior no meio.

function translate_name(string $name, string $lang, array $phrases): string
{
    foreach ($phrases as $p) {
        $pattern = '/' . preg_quote($p['pt'], '/') . '/u';
        $name = preg_replace($pattern, $p[$lang], $name);
    }
    return $name;
}

$pdo = db();
$products = $pdo->query('SELECT id, slug, name FROM products')->fetchAll();

$languages = ['en', 'es', 'it'];
$updated = 0;

$pdo->beginTransaction();
foreach ($products as $p) {
    foreach ($languages as $lang) {
        $translated = translate_name($p['name'], $lang, $phrases);
        $stmt = $pdo->prepare(
            'INSERT INTO product_translations (product_id, language_code, name) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE name = VALUES(name)'
        );
        $stmt->execute([$p['id'], $lang, $translated]);
        $updated++;
    }
}
$pdo->commit();

echo "Nomes traduzidos: $updated\n";

// Mostra uma amostra pra conferência visual
$sample = $pdo->query(
    "SELECT p.slug, p.name AS pt, t.name AS en
     FROM products p JOIN product_translations t ON t.product_id = p.id AND t.language_code = 'en'
     ORDER BY p.slug LIMIT 15"
)->fetchAll();
foreach ($sample as $s) {
    echo "{$s['slug']}: {$s['pt']} -> {$s['en']}\n";
}
