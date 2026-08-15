<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/i18n.php';
require_once __DIR__ . '/../../includes/activity.php';

$languages = get_languages();
$defaultLang = default_language();
$translatableFields = ['label', 'message'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $label = trim($_POST['label'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $phone = preg_replace('/\D+/', '', $_POST['phone_number'] ?? '');
        $errors = [];

        if ($label === '') {
            $errors[] = 'O texto do balão é obrigatório.';
        }
        if ($message === '') {
            $errors[] = 'A mensagem é obrigatória.';
        }
        if ($phone === '' || strlen($phone) < 10) {
            $errors[] = 'Informe o número com DDI e DDD, só números (ex: 5541999998888).';
        }

        if (!$errors) {
            if ($id > 0) {
                $stmt = db()->prepare('UPDATE whatsapp_options SET label=?, message=?, phone_number=? WHERE id=?');
                $stmt->execute([$label, $message, $phone, $id]);
                log_activity('update', 'whatsapp_option', $id, $label);
                flash_set('Opção de WhatsApp atualizada.');
            } else {
                $maxOrder = (int) db()->query('SELECT COALESCE(MAX(sort_order), 0) FROM whatsapp_options')->fetchColumn();
                $stmt = db()->prepare('INSERT INTO whatsapp_options (label, message, phone_number, sort_order, active) VALUES (?,?,?,?,1)');
                $stmt->execute([$label, $message, $phone, $maxOrder + 1]);
                $id = (int) db()->lastInsertId();
                log_activity('create', 'whatsapp_option', $id, $label);
                flash_set('Opção de WhatsApp criada.');
            }
            save_entity_translations('whatsapp_option_translations', 'whatsapp_option_id', $id, $_POST['translations'] ?? [], $translatableFields);
        } else {
            flash_set(implode(' ', $errors), 'error');
        }
        redirect('/admin/whatsapp/index.php');
    }

    if ($action === 'toggle_active') {
        $id = (int) ($_POST['id'] ?? 0);
        db()->prepare('UPDATE whatsapp_options SET active = NOT active WHERE id = ?')->execute([$id]);
        redirect('/admin/whatsapp/index.php');
    }

    if ($action === 'move') {
        $id = (int) ($_POST['id'] ?? 0);
        $dir = $_POST['dir'] ?? '';
        $rows = db()->query('SELECT id, sort_order FROM whatsapp_options ORDER BY sort_order, id')->fetchAll();
        $ids = array_column($rows, 'id');
        $pos = array_search($id, $ids, true);
        if ($pos !== false) {
            $swapWith = $dir === 'up' ? $pos - 1 : $pos + 1;
            if (isset($ids[$swapWith])) {
                $a = $rows[$pos];
                $b = $rows[$swapWith];
                db()->prepare('UPDATE whatsapp_options SET sort_order = ? WHERE id = ?')->execute([$b['sort_order'], $a['id']]);
                db()->prepare('UPDATE whatsapp_options SET sort_order = ? WHERE id = ?')->execute([$a['sort_order'], $b['id']]);
            }
        }
        redirect('/admin/whatsapp/index.php');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM whatsapp_options WHERE id = ?')->execute([$id]);
        log_activity('delete', 'whatsapp_option', $id, '');
        flash_set('Opção removida.');
        redirect('/admin/whatsapp/index.php');
    }
}

$pageTitle = 'WhatsApp';
require __DIR__ . '/../partials/layout-top.php';

$options = db()->query('SELECT * FROM whatsapp_options ORDER BY sort_order, id')->fetchAll();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;
$editTranslations = [];
if ($editId > 0) {
    foreach ($options as $o) {
        if ((int) $o['id'] === $editId) {
            $editing = $o;
            break;
        }
    }
    if ($editing) {
        $editTranslations = get_entity_translations('whatsapp_option_translations', 'whatsapp_option_id', $editId);
    }
}
?>
<style>
.wa-table td { vertical-align: top; }
.wa-message-preview { color: #667085; font-size: .85rem; max-width: 360px; }
.wa-order-btns { display: flex; flex-direction: column; gap: .15rem; }
.wa-order-btns button { border: 1px solid #d0d5dd; background: #fff; border-radius: 4px; cursor: pointer; line-height: 1; padding: .1rem .35rem; }
</style>

<div class="content-header">
    <h1>WhatsApp</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    Cada balão do widget flutuante de WhatsApp (canto do site) pode ter seu próprio texto, mensagem
    pré-preenchida e número — útil pra rotear "Atendimento", "Financeiro", "Projetos" etc. para setores
    ou pessoas diferentes. O texto e a mensagem podem ser traduzidos por idioma; o número é o mesmo em
    qualquer idioma.
</p>

<table class="admin-table wa-table" style="margin-bottom:2rem;">
    <thead>
        <tr><th></th><th>Balão</th><th>Mensagem (pt-BR)</th><th>Número</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($options as $i => $opt): ?>
        <tr>
            <td>
                <div class="wa-order-btns">
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $opt['id'] ?>"><input type="hidden" name="dir" value="up">
                        <button type="submit" <?= $i === 0 ? 'disabled' : '' ?> title="Mover para cima">▲</button>
                    </form>
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="id" value="<?= (int) $opt['id'] ?>"><input type="hidden" name="dir" value="down">
                        <button type="submit" <?= $i === count($options) - 1 ? 'disabled' : '' ?> title="Mover para baixo">▼</button>
                    </form>
                </div>
            </td>
            <td><strong><?= e($opt['label']) ?></strong></td>
            <td class="wa-message-preview"><?= e($opt['message']) ?></td>
            <td>
                <a href="https://wa.me/<?= e($opt['phone_number']) ?>" target="_blank" rel="noopener">+<?= e($opt['phone_number']) ?></a>
            </td>
            <td>
                <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_active">
                    <input type="hidden" name="id" value="<?= (int) $opt['id'] ?>">
                    <button type="submit" class="badge badge-<?= $opt['active'] ? 'published' : 'draft' ?>" style="border:none;cursor:pointer;">
                        <?= $opt['active'] ? 'ativo' : 'inativo' ?>
                    </button>
                </form>
            </td>
            <td>
                <a href="/admin/whatsapp/index.php?edit=<?= (int) $opt['id'] ?>">Editar</a>
                &nbsp;
                <form method="post" style="display:inline;" onsubmit="return confirm('Excluir esta opção de WhatsApp?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $opt['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$options): ?>
        <tr><td colspan="6" style="color:#98a2b3;">Nenhuma opção cadastrada ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h2 style="font-size:1.1rem;"><?= $editing ? 'Editar opção' : 'Nova opção' ?></h2>
<form method="post" class="admin-form" style="max-width:640px;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">

    <label>
        Texto do balão (pt-BR)
        <input type="text" name="label" placeholder="Ex: Atendimento / Informações" required value="<?= e($editing['label'] ?? '') ?>">
    </label>
    <label>
        Mensagem pré-preenchida (pt-BR)
        <textarea name="message" rows="2" placeholder="Ex: Olá, gostaria de falar com um consultor da Esterni." required><?= e($editing['message'] ?? '') ?></textarea>
    </label>
    <label>
        Número do WhatsApp deste setor (com DDI e DDD, só números)
        <input type="text" name="phone_number" placeholder="Ex: 5541999998888" required value="<?= e($editing['phone_number'] ?? '') ?>">
    </label>

    <h3 style="font-size:.95rem;margin:1.5rem 0 .5rem;">Traduções (opcional)</h3>
    <p class="i18n-field-hint" style="margin:0 0 .75rem;">Deixe em branco pra usar o texto em português naquele idioma.</p>
    <div class="i18n-tabs">
        <div class="i18n-tab-buttons">
            <?php foreach ($languages as $i => $lang): ?>
                <?php if ($lang['is_default']) continue; ?>
                <button type="button" class="i18n-tab-btn<?= $i === 1 ? ' active' : '' ?>" data-lang="<?= e($lang['code']) ?>">
                    <?php if ($lang['flag_image']): ?><img src="<?= e($lang['flag_image']) ?>" alt=""><?php endif; ?>
                    <span class="i18n-tab-name"><?= e($lang['code']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($languages as $i => $lang): ?>
            <?php if ($lang['is_default']) continue; ?>
            <div class="i18n-tab-panel" data-lang="<?= e($lang['code']) ?>" <?= $i === 1 ? '' : 'hidden' ?>>
                <label>
                    Texto do balão
                    <input type="text" name="translations[<?= e($lang['code']) ?>][label]" value="<?= e($editTranslations[$lang['code']]['label'] ?? '') ?>">
                </label>
                <label>
                    Mensagem pré-preenchida
                    <textarea name="translations[<?= e($lang['code']) ?>][message]" rows="2"><?= e($editTranslations[$lang['code']]['message'] ?? '') ?></textarea>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn" style="margin-top:1rem;"><?= $editing ? 'Salvar alterações' : 'Adicionar opção' ?></button>
    <?php if ($editing): ?>
        <a href="/admin/whatsapp/index.php" class="btn" style="background:#eee;color:#333;">Cancelar</a>
    <?php endif; ?>
</form>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
