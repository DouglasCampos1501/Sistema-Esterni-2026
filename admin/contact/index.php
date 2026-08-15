<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/i18n.php';
require_once __DIR__ . '/../../includes/settings.php';
require_once __DIR__ . '/../../includes/activity.php';

$languages = get_languages();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();

    set_setting('contact_recipient_email', trim($_POST['contact_recipient_email'] ?? ''));

    $stmtBase = db()->prepare('UPDATE contact_form_fields SET label = ?, placeholder = ? WHERE id = ?');
    $stmtTr = db()->prepare(
        'INSERT INTO contact_form_field_translations (field_id, language_code, label, placeholder)
         VALUES (?,?,?,?)
         ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder)'
    );

    foreach ($_POST['fields'] ?? [] as $fieldId => $data) {
        $fieldId = (int) $fieldId;
        $stmtBase->execute([trim($data['label'] ?? ''), trim($data['placeholder'] ?? ''), $fieldId]);

        foreach ($data['translations'] ?? [] as $langCode => $tr) {
            $label = trim($tr['label'] ?? '');
            $placeholder = trim($tr['placeholder'] ?? '');
            if ($label === '' && $placeholder === '') {
                continue;
            }
            $stmtTr->execute([$fieldId, $langCode, $label, $placeholder]);
        }
    }

    log_activity('update', 'settings', null, 'formulário de contato');
    flash_set('Formulário de contato salvo com sucesso.');
    redirect('/admin/contact/index.php');
}

$pageTitle = 'Formulário de Contato';
require __DIR__ . '/../partials/layout-top.php';

$fields = db()->query('SELECT * FROM contact_form_fields ORDER BY sort_order, id')->fetchAll();
$translationsByField = [];
foreach (db()->query('SELECT * FROM contact_form_field_translations')->fetchAll() as $row) {
    $translationsByField[$row['field_id']][$row['language_code']] = $row;
}

$typeLabels = ['text' => 'Texto', 'email' => 'E-mail', 'tel' => 'Telefone', 'textarea' => 'Área de texto (mensagem)'];
?>
<div class="content-header">
    <h1>Formulário de Contato</h1>
    <a class="btn btn-secondary" href="/admin/contact/messages.php">Mensagens recebidas</a>
</div>

<form method="post" class="admin-form">
    <?= csrf_field() ?>

    <fieldset>
        <legend>Destinatário</legend>
        <label>
            E-mail que recebe as mensagens enviadas pelo formulário
            <input type="email" name="contact_recipient_email" value="<?= e(get_setting('contact_recipient_email')) ?>" placeholder="ex: contato@esterni.ind.br">
        </label>
        <p style="font-size:.8rem;color:#667085;margin-top:-.5rem;">
            Toda mensagem também fica salva em <a href="/admin/contact/messages.php">Mensagens recebidas</a>, mesmo se o e-mail não puder ser entregue (útil em ambiente de testes, sem servidor de e-mail configurado).
        </p>
    </fieldset>

    <fieldset>
        <legend>Campos do formulário (rótulo e placeholder, por idioma)</legend>
        <p style="font-size:.8rem;color:#667085;">O tipo de cada campo e se ele é obrigatório são fixos (definidos pela estrutura do formulário); apenas os textos são editáveis.</p>

        <?php foreach ($fields as $field): ?>
            <div style="border:1px solid #eaecf0;border-radius:8px;padding:1.25rem;margin-bottom:1.25rem;">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                    <strong><?= e($typeLabels[$field['field_type']] ?? $field['field_type']) ?></strong>
                    <?php if ($field['is_required']): ?><span class="badge">obrigatório</span><?php endif; ?>
                </div>

                <div class="i18n-tabs">
                    <div class="i18n-tab-buttons">
                        <?php foreach ($languages as $i => $lang): ?>
                            <button type="button" class="i18n-tab-btn<?= $i === 0 ? ' active' : '' ?>" data-lang="<?= e($lang['code']) ?>">
                                <?php if ($lang['flag_image']): ?><img src="<?= e($lang['flag_image']) ?>" alt=""><?php endif; ?>
                                <?= e($lang['code']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($languages as $i => $lang): ?>
                        <div class="i18n-tab-panel" data-lang="<?= e($lang['code']) ?>" <?= $i === 0 ? '' : 'hidden' ?> style="display:flex;flex-direction:column;gap:1rem;">
                            <?php if ($lang['is_default']): ?>
                                <label>
                                    Rótulo
                                    <input type="text" name="fields[<?= (int) $field['id'] ?>][label]" value="<?= e($field['label']) ?>" required>
                                </label>
                                <label>
                                    Placeholder
                                    <input type="text" name="fields[<?= (int) $field['id'] ?>][placeholder]" value="<?= e($field['placeholder'] ?? '') ?>">
                                </label>
                            <?php else: ?>
                                <?php $tr = $translationsByField[$field['id']][$lang['code']] ?? []; ?>
                                <p style="font-size:.8rem;color:#667085;margin:0;">Deixe em branco para manter o texto em português nesta seção.</p>
                                <label>
                                    Rótulo
                                    <input type="text" name="fields[<?= (int) $field['id'] ?>][translations][<?= e($lang['code']) ?>][label]" value="<?= e($tr['label'] ?? '') ?>">
                                </label>
                                <label>
                                    Placeholder
                                    <input type="text" name="fields[<?= (int) $field['id'] ?>][translations][<?= e($lang['code']) ?>][placeholder]" value="<?= e($tr['placeholder'] ?? '') ?>">
                                </label>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <button type="submit" class="btn">Salvar</button>
</form>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
