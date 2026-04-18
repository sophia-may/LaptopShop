<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * BrandModel — Brands list.
 */
class BrandModel extends BaseModel {

    /**
     * Get all brands with product counts.
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT b.id, b.name, b.slug, b.logo_url, b.created_at,
                    COUNT(p.id) as product_count
             FROM brands b
             LEFT JOIN products p ON p.brand_id = b.id
             GROUP BY b.id
             ORDER BY b.name ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
