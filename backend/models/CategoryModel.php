<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * CategoryModel — Categories with product counts.
 */
class CategoryModel extends BaseModel {

    /**
     * Get all categories with product counts.
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT c.id, c.name, c.slug, c.is_featured, c.created_at,
                    COUNT(p.id) as product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY c.name ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
