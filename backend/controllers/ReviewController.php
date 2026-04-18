<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * ReviewController — Product reviews (public read + member create).
 *
 * Endpoints:
 *   GET  /api/products/:id/reviews — Public: approved reviews for a product
 *   POST /api/products/:id/reviews — Member: submit a review
 */
class ReviewController extends BaseController {

    private ReviewModel $model;

    public function __construct() {
        $this->model = new ReviewModel();
    }

    /**
     * GET /api/products/:id/reviews
     * Public: get approved reviews for a product.
     */
    public function byProduct(string $id): void {
        $productId = (int) $id;
        // BẢN VÁ PHÂN TRANG
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));

        $result = $this->model->getByProduct($productId, $page, $limit);
        $this->jsonResponse($result);
    }

    /**
     * POST /api/products/:id/reviews
     * Member: submit a review. One review per user per product.
     */
    public function store(string $id): void {
        $payload = AuthMiddleware::requireMember();
        $productId = (int) $id;
        $data = $this->getPostData();

        // Validate
        $errors = $this->validate($data, [
            'rating' => 'required|integer',
        ]);
        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $rating = (int) $data['rating'];
        if ($rating < 1 || $rating > 5) {
            $this->jsonError('Rating must be between 1 and 5.', 422);
        }

        // Check duplicate
        if ($this->model->hasReviewed($payload['id'], $productId)) {
            $this->jsonError('You have already reviewed this product.', 409);
        }

        // Comment is already sanitized by getPostData()
        $comment = isset($data['comment']) ? $data['comment'] : null;

        $reviewId = $this->model->create($payload['id'], $productId, $rating, $comment);

        $this->jsonResponse([
            'id'      => $reviewId,
            'message' => 'Review submitted. It will appear after admin approval.',
        ], 201);
    }
}
