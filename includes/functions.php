<?php
declare(strict_types=1);

const ACCENT_MAP = [
    'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
    'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
    'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
    'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
    'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
    'ç' => 'c', 'ñ' => 'n', 'ý' => 'y',
    'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
    'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
    'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
    'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
    'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
    'Ç' => 'C', 'Ñ' => 'N', 'Ý' => 'Y',
];

// Não usa iconv('...//TRANSLIT') porque o transliterador do Windows produz
// resultados inconsistentes com acentos do português (ex: "ô" virava "^o").
function slugify(string $text): string
{
    $text = strtr($text, ACCENT_MAP);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash_set(string $message, string $type = 'success'): void
{
    start_session();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get(): ?array
{
    start_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Monta um link "tel:" a partir de um telefone formatado para exibição
 * (ex: "+55 (41) 3195-4348" -> "tel:+554131954348"), pra funcionar como
 * discagem em um clique/toque em qualquer aparelho.
 */
function phone_tel_href(string $displayPhone): string
{
    $clean = preg_replace('/[^0-9+]/', '', $displayPhone) ?? '';
    return 'tel:' . $clean;
}

/**
 * Formata uma data como tempo relativo ("há 5 minutos", "ontem", "há 3 dias"),
 * caindo pra data absoluta (dd/mm/aaaa) depois de 30 dias. Usado no feed de
 * atividade recente do dashboard.
 */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) {
        return 'agora mesmo';
    }
    if ($diff < 3600) {
        $m = (int) floor($diff / 60);
        return 'há ' . $m . ' ' . ($m === 1 ? 'minuto' : 'minutos');
    }
    if ($diff < 86400) {
        $h = (int) floor($diff / 3600);
        return 'há ' . $h . ' ' . ($h === 1 ? 'hora' : 'horas');
    }
    if ($diff < 172800) {
        return 'ontem';
    }
    if ($diff < 2592000) {
        $d = (int) floor($diff / 86400);
        return 'há ' . $d . ' dias';
    }
    return date('d/m/Y', strtotime($datetime));
}

/**
 * Converte texto simples (digitado no dashboard) em HTML: parágrafos separados
 * por linha em branco, com suporte a **negrito**, *itálico*, [link](url),
 * títulos (linhas começando com "## " ou "### ") e listas com marcadores
 * (linhas começando com "- "). Usado em produtos, notícias e páginas institucionais.
 * Só deve ser usado com texto vindo do dashboard, nunca de entrada de usuário público.
 */
function format_rich_text(string $text): string
{
    $blocks = preg_split('/\n\s*\n/', trim($text)) ?: [];
    $html = '';
    $inline = function (string $t): string {
        $escaped = e($t);
        $escaped = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $escaped) ?? $escaped;
        return $escaped;
    };
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        if (preg_match('/^(#{2,4})\s+(.*)$/', $block, $m)) {
            $tag = 'h' . strlen($m[1]);
            $html .= "<$tag>" . $inline(trim($m[2])) . "</$tag>";
            continue;
        }
        $lines = explode("\n", $block);
        $isList = count(array_filter($lines, fn($l) => !str_starts_with(trim($l), '- '))) === 0;
        if ($isList) {
            $html .= '<ul>' . implode('', array_map(fn($l) => '<li>' . $inline(ltrim(trim($l), '- ')) . '</li>', $lines)) . '</ul>';
        } else {
            $html .= '<p>' . nl2br($inline($block)) . '</p>';
        }
    }
    return $html;
}

/**
 * Negociação de conteúdo em Markdown: agentes de IA podem pedir a versão
 * markdown de uma página (mais barata de processar que o HTML completo)
 * via header "Accept: text/markdown" ou "?format=md" na URL.
 */
function wants_markdown(): bool
{
    if (($_GET['format'] ?? '') === 'md') {
        return true;
    }
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return str_contains($accept, 'text/markdown');
}

/**
 * Imprime uma página em markdown puro e encerra a execução. $bodyMarkdown
 * já deve estar na sintaxe markdown-lite usada no site (##, **, listas com "- "),
 * já que é basicamente o mesmo texto salvo no banco (ver format_rich_text()).
 */
function render_markdown_page(string $title, string $description, string $bodyMarkdown, string $canonicalUrl): never
{
    $full = "# $title\n\n";
    if (trim($description) !== '') {
        $full .= '> ' . trim($description) . "\n\n";
    }
    $full .= trim($bodyMarkdown) . "\n\n---\nFonte: $canonicalUrl\n";

    header('Content-Type: text/markdown; charset=UTF-8');
    header('Vary: Accept');
    // estimativa aproximada (~4 caracteres por token), só como referência pro agente
    header('X-Markdown-Tokens: ' . (int) ceil(strlen($full) / 4));

    echo $full;
    exit;
}
