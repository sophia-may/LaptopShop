<?php
// Suppress all output before JSON response
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

/**
 * Image Upload Handler
 * Handles image uploads with archiving and automatic renaming
 * 
 * Expected POST parameters:
 * - image: File upload
 * - field_name: The setting field name (e.g., 'site_logo', 'hero_image')
 * - current_path: Current image path to archive
 */

$response = [
    'success' => false,
    'message' => '',
    'new_path' => ''
];

try {
    // Validate request
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'No file uploaded';
        if (isset($_FILES['image']['error'])) {
            switch ($_FILES['image']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = 'File size too large';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = 'File upload incomplete';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = 'No file selected';
                    break;
                default:
                    $errorMsg = 'Upload error occurred (code: ' . $_FILES['image']['error'] . ')';
            }
        }
        throw new Exception($errorMsg);
    }

    if (!isset($_POST['field_name']) || empty($_POST['field_name'])) {
        throw new Exception('Field name is required');
    }

    $fieldName = $_POST['field_name'];
    $uploadedFile = $_FILES['image'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($uploadedFile['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed');
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($uploadedFile['size'] > $maxSize) {
        throw new Exception('File size exceeds 5MB limit');
    }

    // Get file extension - but we'll convert everything to PNG
    $extension = 'png'; // Force PNG format
    
    // Define image name mapping based on field name
    $imageNameMap = [
        'site_logo' => 'logo',
        'banner_1_image' => 'banner1',
        'banner_2_image' => 'banner2',
        'home_banner_1_image' => 'banner1',
        'home_banner_2_image' => 'banner2',
        'general_site_logo' => 'logo'
    ];

    // Get target filename
    $targetName = $imageNameMap[$fieldName] ?? 'image_' . time();
    $targetFileName = $targetName . '.' . $extension;
    
    // Define paths using __DIR__ for reliability
    $uploadDir = dirname(__DIR__) . '/assets/img/';
    $archiveDir = dirname(__DIR__) . '/assets/img/archived_img/';
    $targetPath = $uploadDir . $targetFileName;
    $relativePath = 'assets/img/' . $targetFileName;

    // Archive existing file if it exists
    if (isset($_POST['current_path']) && !empty($_POST['current_path'])) {
        $currentPath = $_POST['current_path'];
        $currentFullPath = dirname(__DIR__) . '/' . $currentPath;
        
        if (file_exists($currentFullPath)) {
            $currentFileName = basename($currentFullPath);
            $timestamp = date('Y-m-d_H-i-s');
            $archiveFileName = pathinfo($currentFileName, PATHINFO_FILENAME) . '_' . $timestamp . '.' . pathinfo($currentFileName, PATHINFO_EXTENSION);
            $archivePath = $archiveDir . $archiveFileName;
            
            // Move old file to archive
            if (rename($currentFullPath, $archivePath)) {
                $response['archived'] = 'archived_img/' . $archiveFileName;
            }
        }
    } else {
        // If no current_path provided, try to find existing file with same name
        $existingFiles = glob($uploadDir . $targetName . '.*');
        if (!empty($existingFiles)) {
            foreach ($existingFiles as $existingFile) {
                if (file_exists($existingFile)) {
                    $currentFileName = basename($existingFile);
                    $timestamp = date('Y-m-d_H-i-s');
                    $archiveFileName = pathinfo($currentFileName, PATHINFO_FILENAME) . '_' . $timestamp . '.' . pathinfo($currentFileName, PATHINFO_EXTENSION);
                    $archivePath = $archiveDir . $archiveFileName;
                    
                    if (rename($existingFile, $archivePath)) {
                        $response['archived'] = 'archived_img/' . $archiveFileName;
                    }
                }
            }
        }
    }

    // Check if GD is available for image conversion
    $useGD = extension_loaded('gd');
    
    if ($useGD) {
        // Convert and save image as PNG
        $sourceImage = null;
        $originalType = exif_imagetype($uploadedFile['tmp_name']);
        
        switch ($originalType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($uploadedFile['tmp_name']);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($uploadedFile['tmp_name']);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($uploadedFile['tmp_name']);
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($uploadedFile['tmp_name']);
                break;
            default:
                throw new Exception('Unsupported image format');
        }
        
        if (!$sourceImage) {
            throw new Exception('Failed to process image');
        }
        
        // Save as PNG with max compression
        if (imagepng($sourceImage, $targetPath, 9)) {
            imagedestroy($sourceImage);
            $response['success'] = true;
            $response['message'] = 'Image uploaded and converted to PNG successfully';
            $response['new_path'] = $relativePath;
            $response['filename'] = $targetFileName;
        } else {
            imagedestroy($sourceImage);
            throw new Exception('Failed to save image as PNG');
        }
    } else {
        // Fallback: just move the file without conversion
        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $response['success'] = true;
            $response['message'] = 'Image uploaded successfully (no conversion - GD library not available)';
            $response['new_path'] = $relativePath;
            $response['filename'] = $targetFileName;
        } else {
            throw new Exception('Failed to move uploaded file');
        }
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
