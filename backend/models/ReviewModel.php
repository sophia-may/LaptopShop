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
        $stmt->bindValue(1, $productId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
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

    /**
     * Admin: Get paginated reviews with product and user info.
     */
    public function adminGetPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $where .= ' AND r.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (r.comment LIKE ? OR u.fullname LIKE ? OR p.name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Safe Dynamic Sorting (Whitelist)
        $allowedSort = ['id', 'rating', 'created_at', 'status'];
        $sortBy = in_array($filters['sort'] ?? '', $allowedSort) ? $filters['sort'] : 'created_at';
        $direction = strtoupper($filters['dir'] ?? '') === 'ASC' ? 'ASC' : 'DESC';

        $countSql = "SELECT COUNT(*) FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT r.*, u.fullname as reviewer_name, p.name as product_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE $where
                ORDER BY r.$sortBy $direction
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) { $stmt->bindValue($i++, $p); }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ];
    }

    /**
     * Admin: Update review status.
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE reviews SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    /**
     * Admin: Delete review.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Count total reviews (for dashboard).
     */
    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM reviews');
        return (int) $stmt->fetchColumn();
    }
}
