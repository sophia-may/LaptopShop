<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ReviewModel — Product reviews by members.
 */
class ReviewModel extends BaseModel {

    /**
     * Get approved reviews for a product.
     */
    public function getByProduct(int $productId, int $page = 1, int $limit = 10): array {
        $offset = ($page - 1) * $limit;

        $countStmt = $this->db->prepare(
            'SELECT COUNT(*) FROM reviews WHERE product_id = ? AND status = ?'
        );
        $countStmt->execute([$productId, 'approved']);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT r.id, r.rating, r.comment, r.created_at,
                    u.fullname as reviewer_name, u.avatar_url as reviewer_avatar
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.product_id = ? AND r.status = 'approved'
             ORDER BY r.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$productId, $limit, $offset]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Average rating
        $avgStmt = $this->db->prepare(
            "SELECT AVG(rating) FROM reviews WHERE product_id = ? AND status = 'approved'"
        );
        $avgStmt->execute([$productId]);
        $avgRating = round((float) $avgStmt->fetchColumn(), 1);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
            'avg_rating'  => $avgRating,
        ];
    }

    /**
     * Check if a user has already reviewed a product.
     */
    public function hasReviewed(int $userId, int $productId): bool {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ?'
        );
        $stmt->execute([$userId, $productId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Create a new review.
     */
    public function create(int $userId, int $productId, int $rating, ?string $comment): int {
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $productId, $rating, $comment]);
        return (int) $this->db->lastInsertId();
    }
}
