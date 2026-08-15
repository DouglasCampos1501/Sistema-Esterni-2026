<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Codifica corretamente uma URL com caracteres acentuados (ex: nomes de
 * arquivo com Unicode em forma decomposta/NFD), preservando os bytes
 * exatos — sem isso o servidor de origem retorna 404 para esses arquivos.
 */
function safe_url(string $url): string
{
    $parts = explode('/', $url);
    $encoded = array_map('rawurlencode', $parts);
    return str_replace(['https%3A', 'http%3A'], ['https:', 'http:'], implode('/', $encoded));
}

const ALLOWED_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

/**
 * Converte um JPG/PNG recém-salvo para WebP (bem mais leve, sem perda visível
 * de qualidade em foto) e apaga o original. Se a conversão falhar por qualquer
 * motivo (função indisponível, arquivo corrompido etc.), mantém o arquivo original
 * intacto. Retorna [novoFilename, novoDestination, novoMime].
 */
function convert_to_webp(string $filename, string $destination, string $mime, string $ext): array
{
    if ($mime === 'image/webp' || !function_exists('imagewebp')) {
        return [$filename, $destination, $mime];
    }

    try {
        $img = $mime === 'image/png' ? @imagecreatefrompng($destination) : @imagecreatefromjpeg($destination);
        if (!$img) {
            return [$filename, $destination, $mime];
        }
        if ($mime === 'image/png') {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }

        $webpFilename = substr($filename, 0, -strlen($ext)) . 'webp';
        $webpDestination = UPLOADS_PATH . '/' . $webpFilename;

        if (!imagewebp($img, $webpDestination, 82)) {
            imagedestroy($img);
            return [$filename, $destination, $mime];
        }
        imagedestroy($img);
        unlink($destination);

        return [$webpFilename, $webpDestination, 'image/webp'];
    } catch (\Throwable) {
        return [$filename, $destination, $mime];
    }
}

/**
 * Salva um arquivo enviado (upload $_FILES[...]) em uploads/media e registra na tabela media.
 * Retorna o id do registro criado, ou null se nenhum arquivo válido foi enviado.
 */
function store_uploaded_image(array $file, ?string $altText = null, ?int $uploadedBy = null): ?int
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no upload do arquivo (código ' . $file['error'] . ').');
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!isset(ALLOWED_IMAGE_TYPES[$mime])) {
        throw new RuntimeException('Tipo de arquivo não permitido. Envie JPG, PNG ou WebP.');
    }

    $ext = ALLOWED_IMAGE_TYPES[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = UPLOADS_PATH . '/' . $filename;

    if (!is_dir(UPLOADS_PATH)) {
        mkdir(UPLOADS_PATH, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Não foi possível salvar o arquivo enviado.');
    }

    // Converte JPG/PNG pra WebP automaticamente (bem mais leve, sem perda visível
    // de qualidade) — só mantém o formato original se a conversão falhar por algum motivo.
    [$filename, $destination, $mime] = convert_to_webp($filename, $destination, $mime, $ext);

    $dimensions = @getimagesize($destination);

    $stmt = db()->prepare(
        'INSERT INTO media (filename, path, alt_text, mime_type, size_bytes, width, height, uploaded_by)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $file['name'],
        UPLOADS_URL . '/' . $filename,
        $altText,
        $mime,
        filesize($destination),
        $dimensions[0] ?? null,
        $dimensions[1] ?? null,
        $uploadedBy,
    ]);

    return (int) db()->lastInsertId();
}

/**
 * Baixa uma imagem de uma URL remota e registra na biblioteca de mídia.
 * Usado pelo script de migração de conteúdo (scripts/import-content.php).
 */
function store_image_from_url(string $url, ?string $altText = null, ?int $uploadedBy = null): ?int
{
    $ch = curl_init(safe_url($url));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; EsterniMigration/1.0)',
        CURLOPT_CAINFO => CURL_CA_BUNDLE,
    ]);
    $data = curl_exec($ch);
    $ok = $data !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
    curl_close($ch);

    if (!$ok || !$data) {
        return null;
    }

    $tmpFile = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($tmpFile, $data);

    $mime = mime_content_type($tmpFile);
    if (!isset(ALLOWED_IMAGE_TYPES[$mime])) {
        unlink($tmpFile);
        return null;
    }

    $ext = ALLOWED_IMAGE_TYPES[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = UPLOADS_PATH . '/' . $filename;

    if (!is_dir(UPLOADS_PATH)) {
        mkdir(UPLOADS_PATH, 0755, true);
    }

    rename($tmpFile, $destination);
    $dimensions = @getimagesize($destination);

    $stmt = db()->prepare(
        'INSERT INTO media (filename, path, alt_text, mime_type, size_bytes, width, height, uploaded_by)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        basename($url),
        UPLOADS_URL . '/' . $filename,
        $altText,
        $mime,
        filesize($destination),
        $dimensions[0] ?? null,
        $dimensions[1] ?? null,
        $uploadedBy,
    ]);

    return (int) db()->lastInsertId();
}

/**
 * Baixa um documento (PDF) de uma URL remota para uploads/downloads.
 * Usado pelo script de migração de conteúdo.
 */
function store_document_from_url(string $url): ?string
{
    $ch = curl_init(safe_url($url));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; EsterniMigration/1.0)',
        CURLOPT_CAINFO => CURL_CA_BUNDLE,
    ]);
    $data = curl_exec($ch);
    $ok = $data !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
    curl_close($ch);

    if (!$ok || !$data) {
        return null;
    }

    $uploadsDir = BASE_PATH . '/uploads/downloads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.pdf';
    file_put_contents($uploadsDir . '/' . $filename, $data);

    return '/uploads/downloads/' . $filename;
}

const ALLOWED_DOCUMENT_TYPES = [
    'application/pdf' => 'pdf',
];

/**
 * Salva um documento enviado (ex: PDF de catálogo) em uploads/downloads.
 * Retorna o caminho público relativo (para gravar em downloads.file_path), ou null se nada foi enviado.
 */
function store_uploaded_document(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no upload do arquivo (código ' . $file['error'] . ').');
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!isset(ALLOWED_DOCUMENT_TYPES[$mime])) {
        throw new RuntimeException('Tipo de arquivo não permitido. Envie um PDF.');
    }

    $ext = ALLOWED_DOCUMENT_TYPES[$mime];
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadsDir = BASE_PATH . '/uploads/downloads';
    $destination = $uploadsDir . '/' . $filename;

    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Não foi possível salvar o arquivo enviado.');
    }

    return '/uploads/downloads/' . $filename;
}
