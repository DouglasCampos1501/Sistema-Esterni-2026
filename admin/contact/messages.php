<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();
    $id = (int) ($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
    log_activity('delete', 'settings', null, 'mensagem de contato #' . $id);
    flash_set('Mensagem excluída.');
    redirect('/admin/contact/messages.php');
}

$pageTitle = 'Mensagens Recebidas';
require __DIR__ . '/../partials/layout-top.php';

$messages = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<div class="content-header">
    <h1>Mensagens Recebidas</h1>
    <a class="btn btn-secondary" href="/admin/contact/index.php">Editar formulário</a>
</div>
<p style="color:#667085;max-width:640px;margin-top:-1rem;">
    Toda mensagem enviada pelo formulário de <a href="/contato/" target="_blank">/contato/</a> fica registrada aqui, mesmo quando o envio do e-mail de notificação falha.
</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Contato</th>
            <th>Mensagem</th>
            <th>Idioma</th>
            <th>E-mail enviado</th>
            <th>Recebida em</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($messages as $m): ?>
        <tr>
            <td><?= e($m['name']) ?></td>
            <td>
                <?= e($m['email']) ?>
                <?php if ($m['phone']): ?><br><span style="color:#667085;"><?= e($m['phone']) ?></span><?php endif; ?>
            </td>
            <td style="max-width:320px;white-space:pre-line;"><?= e($m['message']) ?></td>
            <td><?= e($m['language_code']) ?></td>
            <td><?= $m['email_sent'] ? '✅' : '⚠️ não' ?></td>
            <td><?= e($m['created_at']) ?></td>
            <td class="actions">
                <form method="post" onsubmit="return confirm('Excluir esta mensagem?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button type="submit" class="link-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$messages): ?>
        <tr><td colspan="7">Nenhuma mensagem recebida ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
