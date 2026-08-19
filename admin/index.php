<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/activity.php';

$pageTitle = 'Dashboard';
require __DIR__ . '/partials/layout-top.php';

$counts = [
    'products' => db()->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'posts' => db()->query('SELECT COUNT(*) FROM posts')->fetchColumn(),
    'messages' => db()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn(),
];

$activity = db()->query('SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 12')->fetchAll();

$stats = [
    ['key' => 'products', 'label' => 'Produtos', 'href' => '/admin/products/index.php', 'icon' => 'box'],
    ['key' => 'posts', 'label' => 'Notícias', 'href' => '/admin/posts/index.php', 'icon' => 'newspaper'],
    ['key' => 'messages', 'label' => 'Mensagens de contato', 'href' => '/admin/contact/messages.php', 'icon' => 'mail'],
];

$quickActions = [
    ['label' => 'Novo produto', 'href' => '/admin/products/edit.php', 'icon' => 'box'],
    ['label' => 'Nova notícia', 'href' => '/admin/posts/edit.php', 'icon' => 'newspaper'],
    ['label' => 'Enviar mídia', 'href' => '/admin/media/index.php', 'icon' => 'image'],
];

$activityIcons = ['create' => 'plus', 'update' => 'edit', 'delete' => 'trash', 'login' => 'key'];

$hour = (int) date('H');
$greeting = $hour < 12 ? 'Bom dia' : ($hour < 18 ? 'Boa tarde' : 'Boa noite');

$weekdays = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
$months = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
$today = date('w') !== false
    ? $weekdays[(int) date('w')] . ', ' . (int) date('j') . ' de ' . $months[(int) date('n')] . ' de ' . date('Y')
    : '';
?>
<style>
.dash-header { margin-bottom: 1.75rem; }
.dash-header h1 { font-size: 1.5rem; margin: 0 0 .2rem; }
.dash-header p { color: #667085; margin: 0; font-size: .9rem; text-transform: capitalize; }

.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card {
    background: #fff; border-radius: 12px; padding: 1.35rem 1.5rem; display: flex; align-items: center; gap: 1rem;
    box-shadow: 0 1px 2px rgba(16,24,40,.06); border: 1px solid #eaecf0; transition: transform .12s ease, box-shadow .12s ease;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(16,24,40,.12); text-decoration: none; }
.stat-icon {
    flex-shrink: 0; width: 44px; height: 44px; border-radius: 10px; border: 1px solid #eaecf0; color: #667085;
    display: flex; align-items: center; justify-content: center;
}
.stat-icon svg { width: 22px; height: 22px; }
.stat-card .stat-number { font-size: 1.65rem; font-weight: 700; color: #101828; line-height: 1.1; }
.stat-card .stat-label { color: #667085; font-size: .8rem; margin-top: .15rem; }

.quick-actions { display: flex; flex-wrap: wrap; gap: .65rem; margin-bottom: 2.5rem; }
.quick-action {
    display: inline-flex; align-items: center; gap: .5rem; padding: .55rem 1rem; background: #fff;
    border: 1px solid #d7dade; border-radius: 999px; font-size: .85rem; font-weight: 600; color: #344054;
    transition: border-color .12s ease, color .12s ease;
}
.quick-action:hover { border-color: #449bb6; color: #449bb6; text-decoration: none; }
.quick-action svg { width: 15px; height: 15px; }

.dash-section-title { font-size: 1rem; font-weight: 700; margin: 0 0 1rem; color: #101828; }

.activity-feed { background: #fff; border-radius: 12px; border: 1px solid #eaecf0; overflow: hidden; }
.activity-row { display: flex; align-items: flex-start; gap: .85rem; padding: .9rem 1.25rem; border-bottom: 1px solid #f2f4f7; }
.activity-row:last-child { border-bottom: none; }
.activity-icon { flex-shrink: 0; width: 18px; height: 18px; color: #98a2b3; margin-top: .1rem; }
.activity-icon svg { width: 100%; height: 100%; }
.activity-text { font-size: .875rem; color: #344054; }
.activity-text strong { color: #101828; }
.activity-time { font-size: .78rem; color: #98a2b3; white-space: nowrap; margin-left: auto; padding-left: 1rem; }
.activity-empty { padding: 2rem 1.25rem; text-align: center; color: #98a2b3; font-size: .875rem; }
</style>

<div class="dash-header">
    <h1><?= e($greeting) ?>, <?= e($admin['name'] ?? '') ?></h1>
    <p><?= e($today) ?></p>
</div>

<div class="stat-grid">
    <?php foreach ($stats as $s): ?>
        <a class="stat-card" href="<?= e($s['href']) ?>">
            <span class="stat-icon"><?= nav_icon($icons, $s['icon']) ?></span>
            <span>
                <span class="stat-number"><?= (int) $counts[$s['key']] ?></span>
                <span class="stat-label"><?= e($s['label']) ?></span>
            </span>
        </a>
    <?php endforeach; ?>
</div>

<h2 class="dash-section-title">Ações rápidas</h2>
<div class="quick-actions">
    <?php foreach ($quickActions as $qa): ?>
        <a class="quick-action" href="<?= e($qa['href']) ?>">
            <?= nav_icon($icons, $qa['icon']) ?>
            <?= e($qa['label']) ?>
        </a>
    <?php endforeach; ?>
</div>

<h2 class="dash-section-title">Atividade recente</h2>
<div class="activity-feed">
    <?php foreach ($activity as $row): ?>
        <div class="activity-row">
            <span class="activity-icon"><?= nav_icon($icons, $activityIcons[$row['action']] ?? 'grid') ?></span>
            <span class="activity-text"><strong><?= e($row['admin_name'] ?: 'Sistema') ?></strong> <?= e(format_activity_row($row)) ?></span>
            <span class="activity-time"><?= e(time_ago($row['created_at'])) ?></span>
        </div>
    <?php endforeach; ?>
    <?php if (!$activity): ?>
        <div class="activity-empty">Nenhuma atividade registrada ainda.</div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/layout-bottom.php'; ?>
