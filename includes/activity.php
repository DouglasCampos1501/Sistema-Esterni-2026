<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * Registra uma ação no log de atividades do dashboard.
 * $action: 'create' | 'update' | 'delete' | 'login'
 * $entityType: identificador curto do módulo (ex: 'page', 'product', 'media', 'user')
 * $label: texto legível pra exibir no log (ex: nome do produto)
 */
function log_activity(string $action, string $entityType, ?int $entityId, string $label): void
{
    $admin = current_admin();
    $stmt = db()->prepare(
        'INSERT INTO activity_log (admin_user_id, admin_name, action, entity_type, entity_id, entity_label)
         VALUES (?,?,?,?,?,?)'
    );
    $stmt->execute([
        $admin['id'] ?? null,
        $admin['name'] ?? 'Sistema',
        $action,
        $entityType,
        $entityId,
        $label,
    ]);
}

const ACTIVITY_LABELS = [
    'create' => 'criou',
    'update' => 'editou',
    'delete' => 'excluiu',
    'login' => 'entrou no painel',
];

const ACTIVITY_ENTITY_LABELS = [
    'page' => 'a página',
    'product' => 'o produto',
    'product_category' => 'a categoria de produto',
    'post' => 'a notícia',
    'post_category' => 'a categoria de notícia',
    'download' => 'o download',
    'download_category' => 'a categoria de download',
    'media' => 'a imagem',
    'language' => 'o idioma',
    'user' => 'o usuário',
    'menu_item' => 'o item de menu',
    'hero_slide' => 'o slide',
    'home_block' => 'o bloco da Home',
    'landing_page' => 'a página institucional',
    'landing_page_block' => 'o bloco da página institucional',
    'settings' => 'as configurações',
    'site_texts' => 'os textos do site',
    'backup' => 'um backup',
];

function format_activity_row(array $row): string
{
    $verb = ACTIVITY_LABELS[$row['action']] ?? $row['action'];
    if ($row['action'] === 'login') {
        return $verb;
    }
    $entity = ACTIVITY_ENTITY_LABELS[$row['entity_type']] ?? $row['entity_type'];
    $label = $row['entity_label'] ? ' "' . $row['entity_label'] . '"' : '';
    return trim($entity . $label) !== '' ? "$verb $entity$label" : $verb;
}
