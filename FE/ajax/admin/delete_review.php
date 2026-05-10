<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {   
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$reviewId = $input['review_id'] ?? null;

if (!$reviewId) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

require_once __DIR__ . '/../../models/ProductReviewModel.php';

$reviewModel = new ProductReviewModel(); // auto-connects via BaseModel

$success = $reviewModel->deleteReview($reviewId);

echo json_encode([
    'success' => $success,
    'message' => $success ? 'Review deleted successfully' : 'Failed to delete review'
]);
