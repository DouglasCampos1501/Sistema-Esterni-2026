<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

require_login();
$admin = current_admin();
$flash = flash_get();

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$navItems = [
    ['href' => '/admin/index.php', 'label' => 'Dashboard', 'icon' => 'grid'],
    ['href' => '/admin/lines/index.php', 'label' => 'Linhas', 'icon' => 'layout'],
    ['href' => '/admin/types/index.php', 'label' => 'Tipos', 'icon' => 'box'],
    ['href' => '/admin/products/index.php', 'label' => 'Produtos', 'icon' => 'box'],
    ['href' => '/admin/posts/index.php', 'label' => 'Notícias', 'icon' => 'newspaper'],
    // TODO: reativar conforme os módulos forem construídos para o Esterni:
    // ['href' => '/admin/pages/index.php', 'label' => 'Páginas', 'icon' => 'file'],
    ['href' => '/admin/media/index.php', 'label' => 'Mídia', 'icon' => 'image'],
    ['href' => '/admin/languages/index.php', 'label' => 'Idiomas', 'icon' => 'globe'],
    ['href' => '/admin/site-texts/index.php', 'label' => 'Textos do Site', 'icon' => 'type'],
    // ['href' => '/admin/menu/index.php', 'label' => 'Menu do Site', 'icon' => 'menu'],
    ['href' => '/admin/contact/index.php', 'label' => 'Formulário de Contato', 'icon' => 'mail'],
    ['href' => '/admin/whatsapp/index.php', 'label' => 'WhatsApp', 'icon' => 'whatsapp'],
    ['href' => '/admin/users/index.php', 'label' => 'Usuários', 'icon' => 'users'],
    ['href' => '/admin/settings/index.php', 'label' => 'Configurações', 'icon' => 'settings'],
    ['href' => '/admin/backup/index.php', 'label' => 'Backup', 'icon' => 'archive'],
];

$icons = [
    'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
    'file' => '<path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M14 2v5h5"/>',
    'box' => '<path d="M3 8l9-5 9 5-9 5-9-5z"/><path d="M3 8v9l9 5 9-5V8"/><path d="M12 13v9"/>',
    'newspaper' => '<rect x="3" y="5" width="14" height="15" rx="1.5"/><path d="M17 8h4v10a2 2 0 0 1-2 2h-2"/><path d="M6 9h8M6 12.5h8M6 16h5"/>',
    'download' => '<path d="M12 3v13"/><path d="M6.5 11.5L12 17l5.5-5.5"/><path d="M4 20h16"/>',
    'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.75"/><path d="M21 16l-5.5-5.5a1.5 1.5 0 0 0-2.1 0L4 20"/>',
    'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.8 2.6 4.2 5.7 4.2 9s-1.4 6.4-4.2 9c-2.8-2.6-4.2-5.7-4.2-9s1.4-6.4 4.2-9z"/>',
    'type' => '<path d="M5 5h14"/><path d="M12 5v14"/><path d="M9 19h6"/>',
    'layout' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>',
    'menu' => '<path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h10"/>',
    'users' => '<circle cx="9" cy="8" r="3.25"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16.5 5.5a3.25 3.25 0 0 1 0 6.3"/><path d="M18.5 14.2a6.5 6.5 0 0 1 3 5.8"/>',
    'settings' => '<circle cx="12" cy="12" r="3.25"/><path d="M19.4 13.5a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.9 2.9l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1.1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.9-2.9l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H4a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.6-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.9-2.9l.1.1a1.7 1.7 0 0 0 1.9.3H10a1.7 1.7 0 0 0 1-1.6V4a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.9 2.9l-.1.1a1.7 1.7 0 0 0-.3 1.9V10a1.7 1.7 0 0 0 1.6 1H20a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.6 1z"/>',
    'archive' => '<rect x="3" y="3" width="18" height="5" rx="1.5"/><path d="M4.5 8v10a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V8"/><path d="M10 13h4"/>',
    'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
    'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
    'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/>',
    'trash' => '<path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 13h10l1-13"/><path d="M10 11v6"/><path d="M14 11v6"/>',
    'key' => '<circle cx="8" cy="15" r="4"/><path d="M10.5 12.5L20 3"/><path d="M16 7l3 3"/><path d="M13 4l3 3"/>',
    'whatsapp' => '<path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3z"/><path d="M8.5 8.7c.2-.5.4-.5.6-.5h.5c.15 0 .35 0 .5.4.2.5.6 1.5.65 1.6.05.15.1.3 0 .5-.1.2-.15.3-.3.45-.15.15-.3.35-.45.45-.15.15-.3.3-.15.6.2.35.8 1.3 1.7 2.1 1.15 1 2.1 1.35 2.45 1.5.3.15.5.1.65-.05.2-.2.8-.9.95-1.2.2-.3.4-.25.65-.15s1.7.8 2 .95c.3.15.5.2.55.35.05.2.05 1-.3 1.9-.3.9-1.75 1.7-2.4 1.8-.65.1-1.2.15-3.7-.8-3.1-1.2-5.1-4.4-5.25-4.6-.15-.2-1.2-1.6-1.2-3.05 0-1.45.75-2.15 1-2.45z"/>',
];

function nav_icon(array $icons, string $name): string
{
    $inner = $icons[$name] ?? $icons['grid'];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Painel') ?> — Painel Esterni</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/assets/img/site/favicon-32.png" sizes="32x32">
    <link rel="icon" href="/assets/img/site/favicon-192.png" sizes="192x192">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script>
    // Aplica o estado recolhido antes do CSS pintar, pra não "piscar" a sidebar aberta.
    if (localStorage.getItem('admin_sidebar_collapsed') === '1') {
        document.documentElement.classList.add('sidebar-collapsed-init');
    }
    </script>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar-top">
            <a href="/admin/index.php" class="admin-logo">
                <img src="/assets/img/site/logo-esterni-white.png" alt="Esterni" class="admin-logo-full">
                <img src="/assets/img/site/symbol-esterni-white.png" alt="Esterni" class="admin-logo-mini">
            </a>
            <button type="button" class="sidebar-toggle" id="sidebar-toggle" title="Recolher menu" aria-label="Recolher menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
            </button>
        </div>
        <nav>
            <?php foreach ($navItems as $navItem): ?>
                <?php $isActive = str_starts_with($currentPath, $navItem['href']) || (dirname($navItem['href']) !== '/admin' && str_starts_with($currentPath, dirname($navItem['href']))); ?>
                <a href="<?= e($navItem['href']) ?>" class="<?= $isActive ? 'active' : '' ?>" title="<?= e($navItem['label']) ?>">
                    <span class="nav-icon"><?= nav_icon($icons, $navItem['icon']) ?></span>
                    <span class="nav-label"><?= e($navItem['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <form method="post" action="/admin/logout.php" class="logout-form">
            <button type="submit" title="Sair">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                <span class="nav-label">Sair (<?= e($admin['name']) ?>)</span>
            </button>
        </form>
    </aside>
    <main class="admin-content">
        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
