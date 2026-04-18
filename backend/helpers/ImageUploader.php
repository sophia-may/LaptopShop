<?php
require_once __DIR__ . '/../config/constants.php';

/**
 * ImageUploader — Centralized image upload with security validation.
 *
 * Security checks applied:
 * 1. MIME whitelist via finfo_file() (NOT trusting $_FILES['type'])
 * 2. Extension whitelist
 * 3. Max file size from UPLOAD_MAX_SIZE constant
 * 4. Randomized filename to prevent path traversal
 * 5. Stored in local server directory only
 */
class ImageUploader {

    private static array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Upload an image file to a subfolder under UPLOAD_DIR.
     *
     * @param array  $file       The $_FILES['field'] array
     * @param string $subfolder  Subfolder name: 'avatars', 'products', 'settings'
     * @return string|null       Relative path on success (e.g. 'uploads/avatars/abc123.jpg'), null on failure
     */
    public static function upload(array $file, string $subfolder): ?string {
        // Check for upload errors
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Check file size
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return null;
        }

        // Check MIME type using finfo (NOT trusting client-sent type)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ALLOWED_IMAGE_MIMES)) {
            return null;
        }

        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExtensions)) {
            return null;
        }

        // Generate unique filename
        $newName = uniqid() . '_' . time() . '.' . $ext;

        // Ensure directory exists
        $dir = UPLOAD_DIR . $subfolder . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $dir . $newName)) {
            return null;
        }

        // Return relative path (for storing in DB and serving via /uploads/)
        return 'uploads/' . $subfolder . '/' . $newName;
    }

    /**
     * Delete a previously uploaded file.
     *
     * @param string $relativePath  Relative path as stored in DB (e.g. 'uploads/avatars/abc123.jpg')
     * @return bool
     */
    public static function delete(string $relativePath): bool {
        if (empty($relativePath)) {
            return false;
        }

        // Resolve to absolute path from backend root
        $absolutePath = __DIR__ . '/../' . $relativePath;

        if (file_exists($absolutePath) && is_file($absolutePath)) {
            return unlink($absolutePath);
        }

        return false;
    }
}
