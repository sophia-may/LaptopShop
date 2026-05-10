<?php
// ✅ 1. Start session để lấy user_id
session_start();

// ✅ 2. Set JSON header NGAY ĐẦU FILE
header('Content-Type: application/json; charset=utf-8');

// ✅ 3. Tắt error display để tránh HTML leak
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must login to submit review']);
        exit;
    }

    $userId = $_SESSION['user_id'];

    // Validate input
    if (!isset($_POST['product_id']) || !isset($_POST['rating']) || !isset($_POST['review_text'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $productId = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $reviewTitle = $_POST['review_title'] ?? '';
    $reviewText = trim($_POST['review_text']);

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
        exit;
    }

    // Validate review text
    if (empty($reviewText)) {
        echo json_encode(['success' => false, 'message' => 'Review text cannot be empty']);
        exit;
    }

    // Handle image uploads
    $uploadedImages = [];
    if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
        $uploadDir = __DIR__ . '/../assets/img/reviews/';
        
        // Create folder if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        foreach ($_FILES['review_images']['tmp_name'] as $key => $tmpName) {
            if (empty($tmpName)) continue;
            
            $fileName = $_FILES['review_images']['name'][$key];
            $fileSize = $_FILES['review_images']['size'][$key];
            $fileType = $_FILES['review_images']['type'][$key];
            $fileError = $_FILES['review_images']['error'][$key];
            
            // Validate
            if ($fileError !== UPLOAD_ERR_OK) {
                continue;
            }
            
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type: ' . $fileName]);
                exit;
            }
            
            if ($fileSize > $maxSize) {
                echo json_encode(['success' => false, 'message' => 'File too large: ' . $fileName]);
                exit;
            }
            
            // Generate unique filename
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'review_' . $productId . '_' . $userId . '_' . time() . '_' . uniqid() . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedImages[] = $newFileName;
            }
        }
    }

    // Save to database
    require_once __DIR__ . '/../models/ProductReviewModel.php';

    $reviewModel = new ProductReviewModel(); // auto-connects via BaseModel

    $reviewData = [
        'product_id' => $productId,
        'user_id' => $userId,
        'rating' => $rating,
        'review_title' => $reviewTitle,
        'review_text' => $reviewText,
        'review_images' => !empty($uploadedImages) ? json_encode($uploadedImages) : null,
        'status' => 'pending'
    ];

    $success = $reviewModel->createReview($reviewData);

    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your review has been submitted and is awaiting approval.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review. Please try again.']);
    }

} catch (Exception $e) {
    // Catch any unexpected errors
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
 ?>