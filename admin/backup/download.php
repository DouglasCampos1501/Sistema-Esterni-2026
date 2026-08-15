<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/backup.php';
require_once __DIR__ . '/../../includes/activity.php';

require_login();

set_time_limit(300);

$zipPath = build_backup_zip();

log_activity('create', 'backup', null, basename($zipPath));

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="esterni-backup-' . date('Y-m-d-His') . '.zip"');
header('Content-Length: ' . filesize($zipPath));
readfile($zipPath);
unlink($zipPath);
exit;
