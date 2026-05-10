<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * CartModel — Handles shopping cart state for members.
 * Ported from /backend with Zero-Trust pricing.
 * Cart items are linked to product_variants.
 */
class CartModel extends BaseModel {

    /**
     * Get or create a cart ID for a specific user.
     */
    public function getOrCreateCartId(int $userId): int {
        $stmt = $this->db->prepare('SELECT id FROM carts WHERE user_id = ?');
        $stmt->execute([$userId]);
        $id = $stmt->fetchColumn();

        if ($id) return (int) $id;

        $stmt = $this->db->prepare('INSERT INTO carts (user_id) VALUES (?)');
        $stmt->execute([$userId]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all items in the user's cart with variant and product info.
     */
    public function getItems(int $userId): array {
        $cartId = $this->getOrCreateCartId($userId);

        $stmt = $this->db->prepare(
            'SELECT ci.id, ci.variant_id, ci.quantity, ci.unit_price,
                    pv.sku_code, pv.ram, pv.color, pv.storage, pv.img_url,
                    pv.base_price, pv.quantity as stock,
                    p.id as product_id, p.name as product_name, p.slug as product_slug
             FROM cart_items ci
             JOIN product_variants pv ON ci.variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE ci.cart_id = ?'
        );
        $stmt->execute([$cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add an item to the cart or update quantity if it exists.
     * Uses the variant's current price (Zero-Trust — never trust client price).
     */
    public function addItem(int $userId, int $variantId, int $quantity = 1): bool {
        $cartId = $this->getOrCreateCartId($userId);

        // Fetch current price from DB (ZERO-TRUST)
        $stmt = $this->db->prepare('SELECT base_price FROM product_variants WHERE id = ?');
        $stmt->execute([$variantId]);
        $price = $stmt->fetchColumn();
        if (!$price) return false;

        $stmt = $this->db->prepare(
            'INSERT INTO cart_items (cart_id, variant_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
                quantity = quantity + VALUES(quantity),
                unit_price = VALUES(unit_price)'
        );
        return $stmt->execute([$cartId, $variantId, $quantity, $price]);
    }

    /**
     * Update item quantity directly.
     */
    public function updateItem(int $userId, int $variantId, int $quantity): bool {
        $cartId = $this->getOrCreateCartId($userId);
        
        if ($quantity <= 0) {
            return $this->removeItem($userId, $variantId);
        }

        $stmt = $this->db->prepare(
            'UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND variant_id = ?'
        );
        return $stmt->execute([$quantity, $cartId, $variantId]);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $userId, int $variantId): bool {
        $cartId = $this->getOrCreateCartId($userId);
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE cart_id = ? AND variant_id = ?');
        return $stmt->execute([$cartId, $variantId]);
    }

    /**
     * Clear all items in the cart.
     */
    public function clear(int $userId): bool {
        $cartId = $this->getOrCreateCartId($userId);
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE cart_id = ?');
        return $stmt->execute([$cartId]);
    }

    /**
     * Get cart summary (total items and total amount).
     */
    public function getSummary(int $userId): array {
        $cartId = $this->getOrCreateCartId($userId);
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as unique_items, SUM(quantity) as total_quantity, 
                    SUM(quantity * unit_price) as total_amount 
             FROM cart_items WHERE cart_id = ?'
        );
        $stmt->execute([$cartId]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'unique_items'   => (int) ($summary['unique_items'] ?? 0),
            'total_quantity' => (int) ($summary['total_quantity'] ?? 0),
            'total_amount'   => (float) ($summary['total_amount'] ?? 0),
        ];
    }

    /**
     * Get item count for header badge.
     */
    public function getCartCount(int $userId): int {
        $cartId = $this->getOrCreateCartId($userId);
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?');
        $stmt->execute([$cartId]);
        return (int) $stmt->fetchColumn();
    }
}
