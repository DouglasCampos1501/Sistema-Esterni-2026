<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';

require_once __DIR__ . '/../../includes/auth.php';

require_login(); // exige login ANTES de qualquer processamento de POST (create/update/delete)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/media.php';
require_once __DIR__ . '/../../includes/activity.php';

function media_usage_count(int $mediaId): int
{
    $queries = [
        'SELECT COUNT(*) FROM pages WHERE featured_image_id = ? OR og_image_id = ?',
        'SELECT COUNT(*) FROM products WHERE featured_image_id = ?',
        'SELECT COUNT(*) FROM product_images WHERE media_id = ?',
        'SELECT COUNT(*) FROM posts WHERE featured_image_id = ?',
        'SELECT COUNT(*) FROM portfolio_items WHERE featured_image_id = ?',
        'SELECT COUNT(*) FROM portfolio_images WHERE media_id = ?',
    ];
    $total = 0;
    foreach ($queries as $sql) {
        $paramCount = substr_count($sql, '?');
        $stmt = db()->prepare($sql);
        $stmt->execute(array_fill(0, $paramCount, $mediaId));
        $total += (int) $stmt->fetchColumn();
    }
    return $total;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../includes/auth.php';
    verify_csrf_token();
    $admin = current_admin();

    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        try {
            $altText = trim($_POST['alt_text'] ?? '') ?: null;
            $description = trim($_POST['description'] ?? '') ?: null;
            $mediaId = store_uploaded_image($_FILES['file'] ?? [], $altText, $admin['id']);
            if ($mediaId && $description) {
                db()->prepare('UPDATE media SET description = ? WHERE id = ?')->execute([$description, $mediaId]);
            }
            if ($mediaId) {
                log_activity('create', 'media', $mediaId, $altText ?: 'Imagem enviada');
            }
            flash_set($mediaId ? 'Imagem enviada.' : 'Selecione um arquivo.', $mediaId ? 'success' : 'error');
        } catch (RuntimeException $e) {
            flash_set($e->getMessage(), 'error');
        }
    }

    if ($action === 'update_details') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = db()->prepare('UPDATE media SET alt_text = ?, description = ? WHERE id = ?');
        $stmt->execute([trim($_POST['alt_text'] ?? '') ?: null, trim($_POST['description'] ?? '') ?: null, $id]);
        log_activity('update', 'media', $id, trim($_POST['alt_text'] ?? '') ?: 'Imagem #' . $id);
        flash_set('Detalhes da imagem atualizados.');
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if (media_usage_count($id) > 0) {
            flash_set('Não é possível excluir: essa imagem está em uso em alguma página, produto ou notícia.', 'error');
        } else {
            $stmt = db()->prepare('SELECT path, alt_text FROM media WHERE id = ?');
            $stmt->execute([$id]);
            $mediaRow = $stmt->fetch();
            db()->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
            if ($mediaRow && $mediaRow['path']) {
                $fullPath = BASE_PATH . $mediaRow['path'];
                if (is_file($fullPath)) {
                    unlink($fullPath);
                }
            }
            if ($mediaRow) {
                log_activity('delete', 'media', $id, $mediaRow['alt_text'] ?: 'Imagem #' . $id);
            }
            flash_set('Imagem excluída.');
        }
    }

    redirect('/admin/media/index.php');
}

$pageTitle = 'Mídia';
require __DIR__ . '/../partials/layout-top.php';

$media = db()->query('SELECT id, path, alt_text, description, filename, size_bytes, width, height, created_at FROM media ORDER BY created_at DESC')->fetchAll();
?>
<h1>Biblioteca de mídia</h1>

<form method="post" class="admin-form" enctype="multipart/form-data" style="margin-bottom:1.5rem;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload">
    <label>
        Arquivo (JPG, PNG ou WebP)
        <input type="file" name="file" accept="image/jpeg,image/png,image/webp" required>
    </label>
    <label>
        Texto alternativo (ALT — descrição curta para leitores de tela)
        <input type="text" name="alt_text" placeholder="ex: Poste cônico instalado em rodovia">
    </label>
    <label>
        Descrição para deficientes visuais (opcional — mais detalhada que o ALT)
        <textarea name="description" rows="2" placeholder="ex: Foto noturna de três postes cônicos metálicos iluminando uma avenida de quatro faixas."></textarea>
    </label>
    <button type="submit" class="btn">Enviar</button>
</form>

<div class="media-grid">
    <?php foreach ($media as $m): ?>
        <button type="button" class="media-thumb" data-media-id="<?= (int) $m['id'] ?>">
            <img src="<?= e($m['path']) ?>" alt="<?= e($m['alt_text'] ?? '') ?>">
            <?php if (!$m['alt_text']): ?><span class="media-thumb-warning" title="Sem texto ALT">⚠</span><?php endif; ?>
        </button>
    <?php endforeach; ?>
    <?php if (!$media): ?>
        <p>Nenhuma imagem na biblioteca ainda.</p>
    <?php endif; ?>
</div>

<!-- Modal de visualização/edição -->
<div id="media-modal" class="media-modal" hidden>
    <div class="media-modal-backdrop" data-close-modal></div>
    <div class="media-modal-dialog">
        <button type="button" class="media-modal-close" data-close-modal>&times;</button>
        <div class="media-modal-body">
            <div class="media-modal-image">
                <img id="media-modal-img" src="" alt="">
            </div>
            <div class="media-modal-form">
                <h3 id="media-modal-filename" style="margin-top:0;"></h3>
                <p id="media-modal-meta" style="font-size:.8rem;color:#667085;"></p>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_details">
                    <input type="hidden" name="id" id="media-modal-id" value="">
                    <label>
                        Texto alternativo (ALT)
                        <input type="text" name="alt_text" id="media-modal-alt">
                    </label>
                    <label>
                        Descrição para deficientes visuais
                        <textarea name="description" id="media-modal-description" rows="4"></textarea>
                    </label>
                    <div style="display:flex;gap:.75rem;margin-top:1rem;">
                        <button type="submit" class="btn">Salvar</button>
                        <a id="media-modal-open-original" href="#" target="_blank" class="btn btn-secondary">Abrir original</a>
                    </div>
                </form>
                <form method="post" onsubmit="return confirm('Excluir esta imagem?');" style="margin-top:1rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="media-modal-delete-id" value="">
                    <button type="submit" class="link-danger">Excluir imagem</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var mediaData = <?= json_encode(array_map(fn($m) => [
        'id' => $m['id'], 'path' => $m['path'], 'filename' => $m['filename'],
        'alt_text' => $m['alt_text'], 'description' => $m['description'],
        'width' => $m['width'], 'height' => $m['height'], 'size_bytes' => $m['size_bytes'],
    ], $media), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var byId = {};
    mediaData.forEach(function (m) { byId[m.id] = m; });

    var modal = document.getElementById('media-modal');

    function openModal(id) {
        var m = byId[id];
        if (!m) return;
        document.getElementById('media-modal-img').src = m.path;
        document.getElementById('media-modal-filename').textContent = m.filename;
        document.getElementById('media-modal-meta').textContent =
            (m.width && m.height ? m.width + '×' + m.height + 'px · ' : '') + Math.round(m.size_bytes / 1024) + ' KB';
        document.getElementById('media-modal-id').value = m.id;
        document.getElementById('media-modal-delete-id').value = m.id;
        document.getElementById('media-modal-alt').value = m.alt_text || '';
        document.getElementById('media-modal-description').value = m.description || '';
        document.getElementById('media-modal-open-original').href = m.path;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.media-thumb').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn.dataset.mediaId); });
    });
    modal.querySelectorAll('[data-close-modal]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
</script>

<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
