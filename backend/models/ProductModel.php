<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ProductModel — Products + variants queries for public catalog.
 */
class ProductModel extends BaseModel {

    /**
     * Get paginated products with optional filters.
     * Each product includes min_price from its variants.
     */
    public function getPaginated(int $page = 1, int $limit = 12, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        // Filter by category
        if (!empty($filters['category_id'])) {
            $where .= ' AND p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }

        // Filter by category slug
        if (!empty($filters['category_slug'])) {
            $where .= ' AND c.slug = ?';
            $params[] = $filters['category_slug'];
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $where .= ' AND p.brand_id = ?';
            $params[] = (int) $filters['brand_id'];
        }

        // Search by name
        if (!empty($filters['search'])) {
            $where .= ' AND (p.name LIKE ? OR p.short_description LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        // Price range (based on min variant price)
        $havingClause = '';
        $havingParams = [];
        if (!empty($filters['price_min'])) {
            $havingClause .= ' AND min_price >= ?';
            $havingParams[] = (float) $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $havingClause .= ' AND min_price <= ?';
            $havingParams[] = (float) $filters['price_max'];
        }

        // Count (simplified — without price filter for performance)
        $countSql = "SELECT COUNT(DISTINCT p.id) FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Safe Dynamic Sorting (Whitelist)
        $allowedSort = ['base_price', 'created_at', 'name'];
        $sortBy = in_array($filters['sort'] ?? '', $allowedSort) ? $filters['sort'] : 'created_at';
        $direction = strtoupper($filters['dir'] ?? '') === 'ASC' ? 'ASC' : 'DESC';

        // Map 'base_price' to the calculated 'min_price' column in the query
        $sortColumn = ($sortBy === 'base_price') ? "min_price" : "p.$sortBy";

        // Fetch with aggregated min price
        $sql = "SELECT p.id, p.name, p.slug, p.short_description, p.is_featured,
                       p.category_id, p.brand_id, p.created_at,
                       c.name as category_name, c.slug as category_slug,
                       b.name as brand_name,
                       MIN(pv.base_price) as min_price,
                       (SELECT pv2.img_url FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.img_url IS NOT NULL LIMIT 1) as image_url
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_variants pv ON pv.product_id = p.id
                WHERE $where
                GROUP BY p.id
                HAVING 1=1 $havingClause
                ORDER BY $sortColumn $direction
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        // Bind parameters
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        foreach ($havingParams as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
        ];
    }

    /**
     * Get featured products for homepage.
     */
    public function getFeatured(int $limit = 8): array {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.name, p.slug, p.short_description,
                    c.name as category_name, b.name as brand_name,
                    MIN(pv.base_price) as min_price,
                    (SELECT pv2.img_url FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.img_url IS NOT NULL LIMIT 1) as image_url
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN product_variants pv ON pv.product_id = p.id
             WHERE p.is_featured = 1
             GROUP BY p.id
             ORDER BY p.created_at DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get product detail by slug with all variants.
     */
    public function findBySlug(string $slug): ?array {
        // Get product
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name as category_name, c.slug as category_slug,
                    b.name as brand_name, b.slug as brand_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.slug = ?'
        );
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) return null;

        // Get variants
        $stmt = $this->db->prepare(
            'SELECT id, sku_code, ram, color, storage, quantity, base_price, img_url
             FROM product_variants WHERE product_id = ? ORDER BY base_price ASC'
        );
        $stmt->execute([$product['id']]);
        $product['variants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    /**
     * Admin: Create a new product.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO products (category_id, brand_id, name, slug, short_description, detail_description, is_featured)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['category_id'] ?? null,
            $data['brand_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['short_description'] ?? null,
            $data['detail_description'] ?? null,
            $data['is_featured'] ?? 0
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Admin: Update product info.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE products SET category_id = ?, brand_id = ?, name = ?, slug = ?, 
                               short_description = ?, detail_description = ?, is_featured = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['category_id'] ?? null,
            $data['brand_id'] ?? null,
            $data['name'],
            $data['slug'],
            $data['short_description'] ?? null,
            $data['detail_description'] ?? null,
            $data['is_featured'] ?? 0,
            $id
        ]);
    }

    /**
     * Admin: Delete product and all variants (cascade).
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Admin: Add a variant to a product.
     */
    public function addVariant(int $productId, array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO product_variants (product_id, sku_code, ram, color, storage, quantity, base_price, img_url)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $productId,
            $data['sku_code'],
            $data['ram'] ?? null,
            $data['color'] ?? null,
            $data['storage'] ?? null,
            $data['quantity'] ?? 0,
            $data['base_price'],
            $data['img_url'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Admin: Update a variant.
     */
    public function updateVariant(int $variantId, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE product_variants SET sku_code = ?, ram = ?, color = ?, storage = ?, 
                                      quantity = ?, base_price = ?, img_url = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['sku_code'],
            $data['ram'] ?? null,
            $data['color'] ?? null,
            $data['storage'] ?? null,
            $data['quantity'] ?? 0,
            $data['base_price'],
            $data['img_url'] ?? null,
            $variantId
        ]);
    }

    /**
     * Admin: Delete a variant.
     */
    public function deleteVariant(int $variantId): bool {
        $stmt = $this->db->prepare('DELETE FROM product_variants WHERE id = ?');
        return $stmt->execute([$variantId]);
    }

    /**
     * Count total products (for dashboard).
     */
    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM products');
        return (int) $stmt->fetchColumn();
    }
}
