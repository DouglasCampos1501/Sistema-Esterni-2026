<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/i18n.php';
require_once __DIR__ . '/../../includes/activity.php';

$languages = get_languages();
$groupLabels = [
    'menu' => 'Menu de navegação',
    'home' => 'Home',
    'botoes' => 'Botões',
    'rodape' => 'Rodapé',
    'cookies' => 'Aviso de cookies',
    'whatsapp' => 'WhatsApp',
    'paginas' => 'Páginas de listagem (produtos, notícias, downloads)',
    'contato' => 'Página de Contato',
];
$groupIcons = [
    'menu' => '☰', 'home' => '🏠', 'botoes' => '🔘', 'rodape' => '▤',
    'cookies' => '🍪', 'whatsapp' => '💬', 'paginas' => '📄', 'contato' => '✉️',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();

    foreach ($_POST['values'] ?? [] as $stringId => $byLang) {
        foreach ($byLang as $langCode => $value) {
            $stmt = db()->prepare(
                'INSERT INTO ui_string_translations (ui_string_id, language_code, value) VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)'
            );
            $stmt->execute([(int) $stringId, $langCode, $value]);
        }
    }
    log_activity('update', 'site_texts', null, '');
    flash_set('Textos salvos com sucesso.');
    redirect('/admin/site-texts/index.php');
}

$pageTitle = 'Textos do Site';
require __DIR__ . '/../partials/layout-top.php';

$strings = db()->query('SELECT * FROM ui_strings ORDER BY group_name, sort_order')->fetchAll();
$translations = db()->query('SELECT * FROM ui_string_translations')->fetchAll();

$valuesByString = [];
foreach ($translations as $t) {
    $valuesByString[$t['ui_string_id']][$t['language_code']] = $t['value'];
}

$grouped = [];
foreach ($strings as $s) {
    $grouped[$s['group_name']][] = $s;
}
?>
<style>
.site-texts-toolbar {
    position: sticky; top: 0; z-index: 5; background: #f4f5f7; padding: 1rem 0 1.25rem;
    display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;
}
.site-texts-search {
    flex: 1; min-width: 240px; padding: .7rem 1rem; border: 1px solid #d0d5dd; border-radius: 999px;
    font: inherit; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2398a2b3' stroke-width='2'%3E%3Ccircle cx='7' cy='7' r='5.5'/%3E%3Cpath d='M15 15l-3.5-3.5'/%3E%3C/svg%3E") no-repeat 14px center;
    padding-left: 2.6rem;
}
.site-texts-search:focus { outline: none; border-color: #449bb6; box-shadow: 0 0 0 3px rgba(68,155,182,.15); }
.site-texts-group-title {
    display: flex; align-items: center; gap: .6rem; font-size: 1rem; font-weight: 700; color: #101828;
    margin: 2.25rem 0 1rem;
}
.site-texts-group-title .icon { font-size: 1.1rem; }
.site-texts-group-title .count { font-weight: 400; color: #98a2b3; font-size: .8rem; }
.i18n-field-row.is-hidden { display: none; }
.save-bar {
    position: sticky; bottom: 0; background: #fff; border-top: 1px solid #eaecf0; padding: 1rem 0;
    margin-top: 2rem; display: flex; justify-content: flex-end;
}
</style>

<div class="content-header">
    <h1>Textos do Site</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    Aqui ficam todos os textos fixos do site (menu, rodapé, botões, avisos). O Português é sempre a referência —
    se você deixar outro idioma em branco, o site mostra o texto em português naquele lugar até você traduzir.
    A bolinha verde na aba indica que aquele idioma já tem conteúdo preenchido.
</p>

<form method="post">
    <?= csrf_field() ?>

    <div class="site-texts-toolbar">
        <input type="search" id="site-texts-search" class="site-texts-search" placeholder="Buscar por texto ou chave (ex: contato, whatsapp, botão)...">
    </div>

    <?php foreach ($grouped as $groupName => $groupStrings): ?>
        <div class="site-texts-group" data-group>
            <h2 class="site-texts-group-title">
                <span class="icon"><?= e($groupIcons[$groupName] ?? '•') ?></span>
                <?= e($groupLabels[$groupName] ?? $groupName) ?>
                <span class="count">(<?= count($groupStrings) ?>)</span>
            </h2>

            <?php foreach ($groupStrings as $s): ?>
                <div class="i18n-field-card i18n-field-row" data-search="<?= e(mb_strtolower($s['description'] . ' ' . $s['string_key'])) ?>">
                    <strong><?= e($s['description'] ?: $s['string_key']) ?></strong>
                    <div class="i18n-tabs">
                        <div class="i18n-tab-buttons">
                            <?php foreach ($languages as $i => $lang): ?>
                                <button type="button" class="i18n-tab-btn<?= $i === 0 ? ' active' : '' ?>" data-lang="<?= e($lang['code']) ?>">
                                    <?php if ($lang['flag_image']): ?><img src="<?= e($lang['flag_image']) ?>" alt=""><?php endif; ?>
                                    <span class="i18n-tab-name"><?= e($lang['code']) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php foreach ($languages as $i => $lang): ?>
                            <div class="i18n-tab-panel" data-lang="<?= e($lang['code']) ?>" <?= $i === 0 ? '' : 'hidden' ?>>
                                <?php if (!$lang['is_default']): ?>
                                    <p class="i18n-field-hint">Deixe em branco para usar o texto em português.</p>
                                <?php endif; ?>
                                <textarea name="values[<?= (int) $s['id'] ?>][<?= e($lang['code']) ?>]" rows="2"
                                ><?= e($valuesByString[$s['id']][$lang['code']] ?? '') ?></textarea>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="save-bar">
        <button type="submit" class="btn">Salvar textos</button>
    </div>
</form>

<script>
document.getElementById('site-texts-search').addEventListener('input', function () {
    var term = this.value.trim().toLowerCase();
    document.querySelectorAll('.i18n-field-row').forEach(function (row) {
        row.classList.toggle('is-hidden', term !== '' && row.dataset.search.indexOf(term) === -1);
    });
    document.querySelectorAll('.site-texts-group').forEach(function (group) {
        var visible = group.querySelectorAll('.i18n-field-row:not(.is-hidden)').length;
        group.style.display = visible === 0 ? 'none' : '';
    });
});
</script>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
