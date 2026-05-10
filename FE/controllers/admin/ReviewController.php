<?php
// controllers/admin/ReviewController.php — Uses BaseModel-powered ReviewModel

require_once __DIR__ . '/../../models/ProductReviewModel.php';

class ReviewController {
    private $reviewModel;

    public function __construct() {
        // Check admin permission
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: index.php?page=home');
            exit;
        }

        $this->reviewModel = new ProductReviewModel(); // auto-connects via BaseModel
    }

    public function index() {
        $status      = $_GET['status'] ?? 'all';
        $currentPage = isset($_GET['current_page']) ? (int)$_GET['current_page'] : 1;
        $limit       = 15;
        $offset      = ($currentPage - 1) * $limit;

        $reviews      = $this->reviewModel->getAllReviewsAdmin($status, $limit, $offset);
        $totalReviews = $this->reviewModel->countReviewsByStatus($status);
        $totalPages   = ceil($totalReviews / $limit);

        $stats = [
            'total'    => $this->reviewModel->countReviewsByStatus('all'),
            'pending'  => $this->reviewModel->countReviewsByStatus('pending'),
            'approved' => $this->reviewModel->countReviewsByStatus('approved'),
            'rejected' => $this->reviewModel->countReviewsByStatus('rejected')
        ];

        include 'views/admin/manage_reviews.php';
    }
}