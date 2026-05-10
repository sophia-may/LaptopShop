<?php
// models/ProductModel.php
// Extends BaseModel for singleton PDO. Queries follow ERD schema exactly.

require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {


    /**
     * Central base SELECT that JOINs products → brands, categories, product_variants
     * and aliases columns to match what FE views expect (brand, price, stock, image, etc.)
     */
    private function getBaseSelect() {
        return "SELECT 
            p.id, 
            p.name,
            p.slug,
            p.short_description,
            p.detail_description as description,
            p.is_featured,
            p.created_at,
            b.name   AS brand, 
            c.name   AS category,
            c.slug   AS category_slug,
            (SELECT base_price FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) AS price,
            (SELECT storage    FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) AS storage,
            (SELECT ram        FROM product_variants WHERE product_id = p.id ORDER BY base_price ASC LIMIT 1) AS ram,
            (SELECT img_url    FROM product_variants WHERE product_id = p.id AND img_url IS NOT NULL LIMIT 1)  AS image,
            (SELECT SUM(quantity) FROM product_variants WHERE product_id = p.id)                                AS stock
        FROM products p
        LEFT JOIN brands     b ON p.brand_id    = b.id
        LEFT JOIN categories c ON p.category_id = c.id";
    }

    // ========================= PUBLIC LIST =========================

    public function getAllProducts($limit = 12, $offset = 0, $sort = 'default') {
        try {
            $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t";
            switch ($sort) {
                case 'price_asc':  $query .= " ORDER BY price ASC";  break;
                case 'price_desc': $query .= " ORDER BY price DESC"; break;
                case 'name_asc':   $query .= " ORDER BY name ASC";   break;
                default:           $query .= " ORDER BY created_at DESC";
            }
            $query .= " LIMIT :lim OFFSET :off";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':lim', (int)$limit,  PDO::PARAM_INT);
            $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllProducts: " . $e->getMessage());
            return [];
        }
    }

    public function countProducts() {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM products");
        return (int) $stmt->fetch()['total'];
    }

    // ========================= SINGLE PRODUCT =========================

    /**
     * Get product by ID
     * @param int $id
     * @return array|false
     */
    public function getProductById($id) {
        $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // ========================= FILTERED LIST =========================

    public function getFilteredProducts($filters = [], $limit = 12, $offset = 0, $sort = 'default') {
        $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t WHERE 1=1";
        $params = [];

        if (!empty($filters['brand'])) {
            $ph = implode(',', array_fill(0, count($filters['brand']), '?'));
            $query .= " AND brand IN ($ph)";
            $params = array_merge($params, $filters['brand']);
        }
        if (!empty($filters['price_min'])) {
            $query .= " AND price >= ?"; $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $query .= " AND price <= ?"; $params[] = $filters['price_max'];
        }
        if (!empty($filters['storage'])) {
            $ph = implode(',', array_fill(0, count($filters['storage']), '?'));
            $query .= " AND storage IN ($ph)";
            $params = array_merge($params, $filters['storage']);
        }
        if (!empty($filters['category'])) {
            $query .= " AND category = ?"; $params[] = $filters['category'];
        }

        switch ($sort) {
            case 'price_asc':  $query .= " ORDER BY price ASC";  break;
            case 'price_desc': $query .= " ORDER BY price DESC"; break;
            case 'name_asc':   $query .= " ORDER BY name ASC";   break;
            default:           $query .= " ORDER BY created_at DESC";
        }
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFilteredProducts($filters = []) {
        try {
            $query = "SELECT COUNT(*) AS total FROM (" . $this->getBaseSelect() . ") AS t WHERE 1=1";
            $params = [];

            if (!empty($filters['brand']) && is_array($filters['brand'])) {
                $ph = implode(',', array_fill(0, count($filters['brand']), '?'));
                $query .= " AND brand IN ($ph)";
                $params = array_merge($params, $filters['brand']);
            }
            if (!empty($filters['category'])) {
                $query .= " AND category = ?"; $params[] = $filters['category'];
            }
            if (!empty($filters['storage']) && is_array($filters['storage'])) {
                $ph = implode(',', array_fill(0, count($filters['storage']), '?'));
                $query .= " AND storage IN ($ph)";
                $params = array_merge($params, $filters['storage']);
            }
            if (!empty($filters['price_min']) && is_numeric($filters['price_min'])) {
                $query .= " AND price >= ?"; $params[] = (float)$filters['price_min'];
            }
            if (!empty($filters['price_max']) && is_numeric($filters['price_max'])) {
                $query .= " AND price <= ?"; $params[] = (float)$filters['price_max'];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return (int)$stmt->fetch()['total'];
        } catch (PDOException $e) {
            error_log("countFilteredProducts: " . $e->getMessage());
            return 0;
        }
    }

    public function getFilterOptions() {
        return [
            'brands'     => $this->db->query("SELECT name FROM brands ORDER BY name")->fetchAll(PDO::FETCH_COLUMN),
            'storages'   => $this->db->query("SELECT DISTINCT storage FROM product_variants WHERE storage IS NOT NULL ORDER BY storage")->fetchAll(PDO::FETCH_COLUMN),
            'categories' => $this->db->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN),
        ];
    }

    // ========================= PRODUCT IMAGES =========================
    // View expects: ['image_url' => '...']

    public function getProductImages($productId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT img_url AS image_url 
                 FROM product_variants 
                 WHERE product_id = ? AND img_url IS NOT NULL"
            );
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ========================= VARIANTS (Grouped) =========================
    // The view expects each variant to have:
    //   id, variant_name, variant_value, price_modifier, stock, is_default

    public function getProductVariantsGrouped($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = ?");
            $stmt->execute([$productId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get the cheapest variant's base_price as reference
            $basePrice = PHP_FLOAT_MAX;
            foreach ($rows as $r) {
                if ((float)$r['base_price'] < $basePrice) $basePrice = (float)$r['base_price'];
            }

            $grouped = ['storage' => [], 'color' => [], 'ram' => []];
            $seenStorage = []; $seenColor = []; $seenRam = [];
            $isFirst = ['storage' => true, 'color' => true, 'ram' => true];

            foreach ($rows as $r) {
                $modifier = (float)$r['base_price'] - $basePrice;

                // Storage variants
                if (!empty($r['storage']) && !in_array($r['storage'], $seenStorage)) {
                    $seenStorage[] = $r['storage'];
                    $grouped['storage'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $r['storage'],
                        'variant_value'  => $r['storage'],
                        'price_modifier' => $modifier,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['storage'] ? 1 : 0,
                    ];
                    $isFirst['storage'] = false;
                }

                // Color variants
                if (!empty($r['color']) && !in_array($r['color'], $seenColor)) {
                    $seenColor[] = $r['color'];
                    $grouped['color'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $r['color'],
                        'variant_value'  => $r['color'],
                        'price_modifier' => 0,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['color'] ? 1 : 0,
                    ];
                    $isFirst['color'] = false;
                }

                // RAM variants
                if (!empty($r['ram']) && !in_array($r['ram'], $seenRam)) {
                    $seenRam[] = $r['ram'];
                    $grouped['ram'][] = [
                        'id'             => $r['id'],
                        'variant_name'   => $r['ram'],
                        'variant_value'  => $r['ram'],
                        'price_modifier' => 0,
                        'stock'          => (int)$r['quantity'],
                        'is_default'     => $isFirst['ram'] ? 1 : 0,
                    ];
                    $isFirst['ram'] = false;
                }
            }
            return $grouped;
        } catch (PDOException $e) {
            return ['storage' => [], 'color' => [], 'ram' => []];
        }
    }

    // ========================= DEFAULT VARIANTS =========================
    // View expects: [{id, price_modifier}]

    public function getDefaultVariants($productId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM product_variants WHERE product_id = ? ORDER BY base_price ASC LIMIT 1"
            );
            $stmt->execute([$productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return [];
            // The cheapest variant has price_modifier = 0
            $row['price_modifier'] = 0;
            return [$row];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getProductVariants($productId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_variants WHERE product_id = ?");
            $stmt->execute([$productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function calculatePrice($productId, $selectedVariants = []) {
        $p = $this->getProductById($productId);
        return $p ? (float)$p['price'] : 0;
    }

    public function getProductReviews($productId, $limit = 10) {
        return []; // Handled by ProductReviewModel
    }

    public function getRelatedProducts($currentProductId, $category, $limit = 4) {
        try {
            $query = "SELECT * FROM (" . $this->getBaseSelect() . ") AS t 
                      WHERE category = ? AND id != ? ORDER BY RAND() LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$category, $currentProductId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ========================= ADMIN STUBS =========================
    public function createProduct($data) { return false; }
    public function updateProduct($id, $data) { return false; }
    public function deleteProduct($id) { return false; }
    public function getAllBrands() {
        return $this->db->query("SELECT * FROM brands ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
