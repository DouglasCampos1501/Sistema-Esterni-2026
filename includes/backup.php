<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Gera um dump SQL completo do banco em PHP puro (sem depender do binário
 * mysqldump, que pode não estar disponível/liberado em hospedagem compartilhada).
 */
function generate_sql_dump(): string
{
    $pdo = db();
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    $sql = "-- Backup Esterni — gerado em " . date('Y-m-d H:i:s') . "\n";
    $sql .= "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql .= $createStmt['Create Table'] . ";\n\n";

        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            continue;
        }

        $columns = array_keys($rows[0]);
        $columnList = '`' . implode('`, `', $columns) . '`';

        $sql .= "INSERT INTO `$table` ($columnList) VALUES\n";
        $valueLines = [];
        foreach ($rows as $row) {
            $values = array_map(function ($value) use ($pdo) {
                if ($value === null) {
                    return 'NULL';
                }
                return $pdo->quote((string) $value);
            }, $row);
            $valueLines[] = '(' . implode(', ', $values) . ')';
        }
        $sql .= implode(",\n", $valueLines) . ";\n\n";
    }

    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

    return $sql;
}

/**
 * Monta o .zip de backup (banco + mídia) num arquivo temporário e retorna o caminho.
 * Quem chamar é responsável por apagar o arquivo depois de enviar ao navegador.
 */
function build_backup_zip(): string
{
    $zipPath = sys_get_temp_dir() . '/esterni-backup-' . date('Ymd-His') . '.zip';

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Não foi possível criar o arquivo de backup.');
    }

    $zip->addFromString('database.sql', generate_sql_dump());

    foreach (['uploads/media', 'uploads/downloads'] as $dir) {
        $fullDir = BASE_PATH . '/' . $dir;
        if (!is_dir($fullDir)) {
            continue;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            if ($file->isFile() && $file->getFilename() !== '.gitkeep') {
                $localPath = $dir . '/' . substr($file->getPathname(), strlen($fullDir) + 1);
                $zip->addFile($file->getPathname(), str_replace('\\', '/', $localPath));
            }
        }
    }

    $zip->close();

    return $zipPath;
}
