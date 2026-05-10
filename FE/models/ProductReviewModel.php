<?php
require_once __DIR__ . '/BaseModel.php';

class ProductReviewModel extends BaseModel {

    
    // ========== USER-SIDE METHODS ==========
    
    /**
     * Tạo review mới
     */
    public function createReview($data) {
        try {
            $sql = "INSERT INTO reviews 
                    (product_id, user_id, rating, review_title, review_text, review_images, is_verified_purchase) 
                    VALUES (:product_id, :user_id, :rating, :review_title, :review_text, :review_images, :is_verified_purchase)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':product_id' => $data['product_id'],
                ':user_id' => $data['user_id'],
                ':rating' => $data['rating'],
                ':review_title' => $data['review_title'],
                ':review_text' => $data['review_text'],
                ':review_images' => $data['review_images'], // JSON string
                ':is_verified_purchase' => $data['is_verified_purchase']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra user đã mua sản phẩm chưa (verified purchase)
     */
    public function hasUserPurchasedProduct($userId, $productId) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM order_items oi
                    INNER JOIN orders o ON oi.order_id = o.id
                    WHERE o.user_id = :user_id 
                    AND oi.product_id = :product_id 
                    AND o.status IN ('completed', 'delivered')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Check purchase error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra user đã review sản phẩm chưa
     */
    public function hasUserReviewedProduct($userId, $productId) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM reviews 
                    WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Check reviewed error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy tất cả reviews đã approve của 1 sản phẩm
     */
    public function getApprovedReviewsByProduct($productId, $limit = 10, $offset = 0) {
    try {
        $query = "SELECT 
                    r.*,
                    u.fullname as user_name
                  FROM reviews r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.product_id = ?
                  AND r.is_approved = 1
                  ORDER BY r.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId, $limit, $offset]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug log
        error_log("Product ID: " . $productId . " - Reviews found: " . count($results));
        
        return $results;
        
    } catch (PDOException $e) {
        error_log("Get reviews error: " . $e->getMessage());
        return [];
    }
}
    
    /**
     * Lấy thống kê reviews của 1 sản phẩm
     */
    public function getReviewStats($productId) {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM reviews 
                    WHERE product_id = :product_id AND is_approved = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get review stats error: " . $e->getMessage());
            return null;
        }
    }
    
    // ========== ADMIN-SIDE METHODS ==========
    
    /**
     * Lấy tất cả reviews (cho admin)
     */
    public function getAllReviews($filters = [], $limit = 20, $offset = 0) {
        try {
            $sql = "SELECT pr.*, u.fullname as user_name, u.email as user_email, 
                    p.name as product_name, 
                    (SELECT img_url FROM product_variants WHERE product_id = p.id AND img_url IS NOT NULL LIMIT 1) as product_image 
                    FROM reviews pr
                    INNER JOIN users u ON pr.user_id = u.id
                    INNER JOIN products p ON pr.product_id = p.id
                    WHERE 1=1";
            
            $params = [];
            
            // Filter by approval status
            if (isset($filters['status'])) {
                if ($filters['status'] === 'pending') {
                    $sql .= " AND pr.is_approved = 0";
                } elseif ($filters['status'] === 'approved') {
                    $sql .= " AND pr.is_approved = 1";
                }
            }
            
            // Filter by rating
            if (isset($filters['rating']) && $filters['rating'] > 0) {
                $sql .= " AND pr.rating = :rating";
                $params[':rating'] = $filters['rating'];
            }
            
            // Search by keyword
            if (!empty($filters['search'])) {
                $sql .= " AND (pr.review_text LIKE :search OR pr.review_title LIKE :search OR u.fullname LIKE :search OR p.name LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            $sql .= " ORDER BY pr.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all reviews error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Đếm tổng số reviews (cho pagination)
     */
    public function countReviews($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM reviews pr
                    INNER JOIN users u ON pr.user_id = u.id
                    INNER JOIN products p ON pr.product_id = p.id
                    WHERE 1=1";
            
            $params = [];
            
            if (isset($filters['status'])) {
                if ($filters['status'] === 'pending') {
                    $sql .= " AND pr.is_approved = 0";
                } elseif ($filters['status'] === 'approved') {
                    $sql .= " AND pr.is_approved = 1";
                }
            }
            
            if (isset($filters['rating']) && $filters['rating'] > 0) {
                $sql .= " AND pr.rating = :rating";
                $params[':rating'] = $filters['rating'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (pr.review_text LIKE :search OR pr.review_title LIKE :search OR u.fullname LIKE :search OR p.name LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Count reviews error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Lấy 1 review theo ID (cho admin moderate)
     */
    public function getReviewById($id) {
        try {
            $sql = "SELECT pr.*, u.fullname as user_name, u.email as user_email, 
                    p.name as product_name, 
                    (SELECT img_url FROM product_variants WHERE product_id = p.id AND img_url IS NOT NULL LIMIT 1) as product_image,
                    (SELECT base_price FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) as product_price
                    FROM reviews pr
                    INNER JOIN users u ON pr.user_id = u.id
                    INNER JOIN products p ON pr.product_id = p.id
                    WHERE pr.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get review by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Approve review
     */
    public function approveReview($id) {
        try {
            $sql = "UPDATE reviews SET is_approved = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Approve review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reject review (set is_approved = 0)
     */
    public function rejectReview($id) {
        try {
            $sql = "UPDATE reviews SET is_approved = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Reject review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Admin reply to review
     */
    public function replyToReview($id, $replyText) {
        try {
            $sql = "UPDATE reviews 
                    SET admin_reply = :reply, admin_reply_at = NOW() 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':reply' => $replyText
            ]);
        } catch (PDOException $e) {
            error_log("Reply to review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete review
     */
    public function deleteReview($id) {
        try {
            // Lấy review images trước khi xóa
            $review = $this->getReviewById($id);
            if ($review && !empty($review['review_images'])) {
                $images = json_decode($review['review_images'], true);
                // Xóa files ảnh
                foreach ($images as $image) {
                    $filePath = __DIR__ . '/../assets/img/reviews/' . $image;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            
            $sql = "DELETE FROM reviews WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete review error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bulk approve reviews
     */
    public function bulkApproveReviews($ids) {
        try {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $sql = "UPDATE reviews SET is_approved = 1 WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($ids);
        } catch (PDOException $e) {
            error_log("Bulk approve error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bulk delete reviews
     */
    public function bulkDeleteReviews($ids) {
        try {
            // Xóa files ảnh trước
            foreach ($ids as $id) {
                $review = $this->getReviewById($id);
                if ($review && !empty($review['review_images'])) {
                    $images = json_decode($review['review_images'], true);
                    foreach ($images as $image) {
                        $filePath = __DIR__ . '/../assets/img/reviews/' . $image;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }
            
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $sql = "DELETE FROM reviews WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($ids);
            } catch (PDOException $e) {
            error_log("Bulk delete error: " . $e->getMessage());
            return false;
            }
        }
        // ==== ADMIN MANAGEMENT METHODS ====

public function getAllReviewsAdmin($status = 'all', $limit = 20, $offset = 0) {
    $sql = "SELECT 
                pr.*,
                u.fullname as user_name,
                u.email as user_email,
                p.name as product_name,
                (SELECT img_url FROM product_variants WHERE product_id = p.id AND img_url IS NOT NULL LIMIT 1) as product_image
            FROM reviews pr
            JOIN users u ON pr.user_id = u.id
            JOIN products p ON pr.product_id = p.id";
    
    if ($status !== 'all') {
        $sql .= " WHERE pr.status = :status";
    }
    
    $sql .= " ORDER BY pr.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $this->db->prepare($sql);
    
    if ($status !== 'all') {
        $stmt->bindParam(':status', $status);
    }
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

public function countReviewsByStatus($status = 'all') {
    $sql = "SELECT COUNT(*) as count FROM reviews";
    
    if ($status !== 'all') {
        $sql .= " WHERE status = :status";
    }
    
    $stmt = $this->db->prepare($sql);
    
    if ($status !== 'all') {
        $stmt->bindParam(':status', $status);
    }
    
    $stmt->execute();
    $result = $stmt->fetch();
    
    return $result['count'];
}

public function updateReviewStatus($reviewId, $status) {
    $isApproved = ($status === 'approved') ? 1 : 0;
    $sql = "UPDATE reviews 
            SET status = :status, 
                is_approved = :is_approved 
            WHERE id = :id";    $stmt = $this->db->prepare($sql);
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':is_approved', $isApproved, PDO::PARAM_INT);
    $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

    }
?>