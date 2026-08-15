<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$pageTitle = 'Backup';
require __DIR__ . '/../partials/layout-top.php';

$mediaCount = (int) db()->query('SELECT COUNT(*) FROM media')->fetchColumn();
$dbSizeMb = (float) db()->query(
    "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1)
     FROM information_schema.tables WHERE table_schema = DATABASE()"
)->fetchColumn();

function dir_size_mb(string $dir): float
{
    if (!is_dir($dir)) {
        return 0;
    }
    $bytes = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->isFile()) {
            $bytes += $file->getSize();
        }
    }
    return round($bytes / 1024 / 1024, 1);
}

$mediaSizeMb = dir_size_mb(BASE_PATH . '/uploads/media') + dir_size_mb(BASE_PATH . '/uploads/downloads');
?>
<div class="content-header">
    <h1>Backup</h1>
</div>
<p style="color:#667085;max-width:680px;margin-top:-1rem;">
    Gera um arquivo .zip com uma cópia completa do banco de dados (todas as páginas, produtos, notícias,
    traduções, configurações...) e de todos os arquivos enviados (imagens e PDFs). Guarde esse arquivo em
    um lugar seguro — ele é o que permite restaurar o site caso algo dê errado.
</p>

<div class="stat-grid" style="max-width:640px;margin-bottom:2rem;">
    <div class="stat-card">
        <span class="stat-number"><?= $dbSizeMb ?> MB</span>
        <span class="stat-label">Banco de dados</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= $mediaSizeMb ?> MB</span>
        <span class="stat-label">Arquivos de mídia (<?= $mediaCount ?>)</span>
    </div>
</div>

<a href="/admin/backup/download.php" class="btn">⬇ Baixar backup agora</a>

<div style="margin-top:2.5rem;max-width:680px;">
    <h2 style="font-size:1rem;">Como restaurar, se precisar</h2>
    <ol style="color:#667085;font-size:.9rem;line-height:1.7;">
        <li>Extraia o .zip baixado.</li>
        <li>Importe o arquivo <code>database.sql</code> no seu banco MySQL (via phpMyAdmin, HeidiSQL ou linha de comando).</li>
        <li>Copie as pastas <code>uploads/media</code> e <code>uploads/downloads</code> de volta para o servidor, dentro da pasta do site.</li>
    </ol>
</div>
<?php require __DIR__ . '/../partials/layout-bottom.php'; ?>
