<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ReviewModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * AdminReviewController — Moderation for product reviews.
 */
class AdminReviewController extends BaseController {

    private ReviewModel $model;

    public function __construct() {
        $this->model = new ReviewModel();
    }

    /**
     * GET /api/admin/reviews
     * Admin: Paginated list of reviews (pending/approved/rejected).
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = $this->pick($_GET, ['status', 'search', 'sort', 'dir']);

        $result = $this->model->adminGetPaginated($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * PUT /api/admin/reviews/:id/status
     * Admin: Approve or Reject a review.
     */
    public function updateStatus(string $id): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'status' => 'required|in:pending,approved,reject'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $success = $this->model->updateStatus((int)$id, $data['status']);
        if ($success) {
            $this->jsonResponse(['message' => "Review " . $data['status'] . "."]);
        } else {
            $this->jsonError('Status update failed.', 400);
        }
    }

    /**
     * DELETE /api/admin/reviews/:id
     * Admin: Delete a review.
     */
    public function destroy(string $id): void {
        AuthMiddleware::requireAdmin();
        $this->model->delete((int)$id);
        $this->jsonResponse(['message' => 'Review deleted.']);
    }
}
