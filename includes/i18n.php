<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

const LANG_PREFIXES = ['en', 'es', 'it']; // pt-BR não tem prefixo (fica na raiz)

/**
 * Lista de idiomas ativos, ordenados. Resultado cacheado por request.
 */
function get_languages(): array
{
    static $languages = null;
    if ($languages === null) {
        $languages = db()->query('SELECT * FROM languages WHERE active = 1 ORDER BY sort_order')->fetchAll();
    }
    return $languages;
}

function default_language(): string
{
    static $default = null;
    if ($default === null) {
        $stmt = db()->query('SELECT code FROM languages WHERE is_default = 1 LIMIT 1');
        $default = $stmt->fetchColumn() ?: 'pt-BR';
    }
    return $default;
}

/**
 * Resolve e valida o idioma da requisição atual (setado pelo .htaccess via ?lang=).
 * Se o código não existir/estiver inativo, cai para o idioma padrão.
 *
 * Na primeiríssima visita (sem cookie de preferência ainda) e sem idioma explícito
 * na URL, tenta detectar o idioma do navegador (Accept-Language) e redireciona pra
 * a versão correspondente do site, se ela existir. Depois disso, a escolha (seja
 * automática ou manual, via o seletor de idioma) fica salva num cookie e não volta
 * a ser sobrescrita — navegar direto pra uma URL com prefixo de idioma sempre respeita
 * essa URL.
 */
function current_language(): string
{
    static $current = null;
    if ($current !== null) {
        return $current;
    }

    $valid = array_column(get_languages(), 'code');
    $explicit = $_GET['lang'] ?? null;

    if ($explicit !== null) {
        $current = in_array($explicit, $valid, true) ? $explicit : default_language();
        remember_language_preference($current);
        return $current;
    }

    // Sem prefixo de idioma na URL: só tenta auto-detectar se ainda não existe
    // uma preferência salva (primeira visita) — depois disso, a raiz do site
    // (pt-BR) é sempre servida como pt-BR mesmo, sem redirecionar de novo.
    if (!isset($_COOKIE['lang_pref']) && $_SERVER['REQUEST_METHOD'] === 'GET' && !is_probably_bot()) {
        $detected = detect_browser_language($valid);
        if ($detected !== null && $detected !== default_language()) {
            remember_language_preference($detected);
            header('Location: ' . lang_url($detected));
            exit;
        }
    }

    $current = default_language();
    remember_language_preference($current);
    return $current;
}

/**
 * Lê o header Accept-Language do navegador e retorna o idioma suportado com maior
 * prioridade (q), ou null se nenhum dos idiomas ativos do site combinar.
 */
function detect_browser_language(array $supportedCodes): ?string
{
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($header === '') {
        return null;
    }

    $preferences = [];
    foreach (explode(',', $header) as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        [$tag, $q] = array_pad(explode(';q=', $part, 2), 2, '1');
        $preferences[strtolower(trim($tag))] = (float) $q;
    }
    arsort($preferences);

    $codesByPrimary = [];
    foreach ($supportedCodes as $code) {
        $codesByPrimary[strtolower(explode('-', $code)[0])] = $code;
    }

    foreach (array_keys($preferences) as $tag) {
        $primary = explode('-', $tag)[0];
        if (isset($codesByPrimary[$primary])) {
            return $codesByPrimary[$primary];
        }
    }

    return null;
}

/**
 * Heurística simples pra não disparar o redirecionamento de idioma pra
 * crawlers/bots (Google, Bing, redes sociais etc.), que devem sempre ver a
 * versão pt-BR "canônica" da URL que pediram.
 */
function is_probably_bot(): bool
{
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '') {
        return true;
    }
    foreach (['bot', 'spider', 'crawl', 'slurp', 'facebookexternalhit', 'whatsapp', 'preview'] as $needle) {
        if (str_contains($ua, $needle)) {
            return true;
        }
    }
    return false;
}

function remember_language_preference(string $lang): void
{
    if (headers_sent() || (isset($_COOKIE['lang_pref']) && $_COOKIE['lang_pref'] === $lang)) {
        return;
    }
    setcookie('lang_pref', $lang, [
        'expires' => time() + 60 * 60 * 24 * 365,
        'path' => '/',
        'samesite' => 'Lax',
    ]);
}

/**
 * Monta a URL equivalente à página atual em outro idioma, preservando o path.
 * Assume que o slug é o mesmo em todos os idiomas (não traduzimos URLs).
 */
function lang_url(string $targetLang): string
{
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $path = preg_replace('#^/(en|es|it)(/|$)#', '/', $path);
    $path = '/' . ltrim($path, '/');

    if ($targetLang === default_language()) {
        return $path;
    }
    return '/' . $targetLang . ($path === '/' ? '/' : $path);
}

/**
 * Prefixo pra montar links pra OUTRAS páginas do site no idioma atual
 * (ex: home_url() . 'sobre/' -> '/sobre/' em pt-BR, '/en/sobre/' em inglês).
 * Diferente de lang_url(), que é só pra montar a URL da PÁGINA ATUAL num
 * idioma diferente (usado no seletor de idiomas) — não serve como prefixo
 * de raiz porque preserva o path da página em que já se está.
 */
function home_url(): string
{
    $lang = current_language();
    return $lang === default_language() ? '/' : '/' . $lang . '/';
}

/**
 * Gera as tags <link rel="alternate" hreflang="..."> para a página atual,
 * uma por idioma ativo + x-default (aponta pro idioma padrão). Ajuda tanto
 * buscadores quanto agentes de IA a entenderem as variações de idioma de uma
 * mesma página em vez de tratá-las como conteúdo duplicado/não relacionado.
 * Deve ser chamada dentro de <head>, em toda página pública do site.
 */
function hreflang_tags(): string
{
    $html = '';
    foreach (get_languages() as $l) {
        $html .= '<link rel="alternate" hreflang="' . e($l['code']) . '" href="' . e(SITE_URL . lang_url($l['code'])) . '">' . "\n";
    }
    $html .= '<link rel="alternate" hreflang="x-default" href="' . e(SITE_URL . lang_url(default_language())) . '">' . "\n";
    return $html;
}

/**
 * Busca um texto fixo do site (menu, rodapé, botões...) no idioma atual,
 * com fallback automático para o idioma padrão (pt-BR) se não houver tradução.
 */
function t(string $key): string
{
    static $cache = [];
    $lang = current_language();

    if (isset($cache[$lang][$key])) {
        return $cache[$lang][$key];
    }

    $stmt = db()->prepare(
        'SELECT COALESCE(t.value, td.value, s.string_key) AS value
         FROM ui_strings s
         LEFT JOIN ui_string_translations t ON t.ui_string_id = s.id AND t.language_code = ? AND t.value IS NOT NULL AND t.value != \'\'
         LEFT JOIN ui_string_translations td ON td.ui_string_id = s.id AND td.language_code = ? AND td.value IS NOT NULL AND td.value != \'\'
         WHERE s.string_key = ?'
    );
    $stmt->execute([$lang, default_language(), $key]);
    $value = $stmt->fetchColumn();

    if ($value === false) {
        $value = $key; // string não cadastrada — mostra a chave para facilitar debug
    }

    $cache[$lang][$key] = $value;
    return $value;
}

/**
 * Aplica a tradução (se existir e não estiver vazia) sobre os campos base de
 * uma entidade (página, produto, notícia...). Campos base = conteúdo em pt-BR.
 */
function apply_translation(array $base, ?array $translation, array $fields): array
{
    if (!$translation) {
        return $base;
    }
    foreach ($fields as $field) {
        if (!empty($translation[$field])) {
            $base[$field] = $translation[$field];
        }
    }
    return $base;
}

/**
 * Busca as traduções (não-padrão) de uma entidade, indexadas por idioma.
 * Ex: get_entity_translations('page_translations', 'page_id', 12) -> ['en' => [...], 'es' => [...]]
 */
function get_entity_translations(string $table, string $idColumn, int $id): array
{
    $stmt = db()->prepare("SELECT * FROM $table WHERE $idColumn = ?");
    $stmt->execute([$id]);
    $byLang = [];
    foreach ($stmt->fetchAll() as $row) {
        $byLang[$row['language_code']] = $row;
    }
    return $byLang;
}

/**
 * Salva/atualiza as traduções de uma entidade a partir de $_POST['translations'].
 * $fields = lista de colunas traduzíveis (ex: ['title','content','meta_title','meta_description']).
 * Idiomas com todos os campos vazios não geram linha (evita lixo no banco).
 */
function save_entity_translations(string $table, string $idColumn, int $id, array $postTranslations, array $fields): void
{
    $defaultLang = default_language();
    $validCodes = array_column(get_languages(), 'code');

    foreach ($postTranslations as $langCode => $values) {
        if ($langCode === $defaultLang || !in_array($langCode, $validCodes, true)) {
            continue; // pt-BR fica nos campos base da própria tabela, não aqui
        }

        $clean = [];
        $hasContent = false;
        foreach ($fields as $field) {
            $value = trim((string) ($values[$field] ?? ''));
            $clean[$field] = $value !== '' ? $value : null;
            if ($value !== '') {
                $hasContent = true;
            }
        }

        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $updateClause = implode(', ', array_map(fn($f) => "$f = VALUES($f)", $fields));

        $stmt = db()->prepare(
            "INSERT INTO $table ($idColumn, language_code, $columns) VALUES (?, ?, $placeholders)
             ON DUPLICATE KEY UPDATE $updateClause"
        );
        $stmt->execute([$id, $langCode, ...array_values($clean)]);

        if (!$hasContent) {
            // sem nenhum campo preenchido: remove a linha para não sujar o admin com abas "vazias mas salvas"
            db()->prepare("DELETE FROM $table WHERE $idColumn = ? AND language_code = ?")->execute([$id, $langCode]);
        }
    }
}
