<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * OrderModel — Handles order placement, history, and admin management.
 * Ported from /backend with hardened checkout (Transaction + Row Locking).
 */
class OrderModel extends BaseModel {

    /**
     * Create a new order from the current user's cart.
     * Hardened with Pessimistic Locking (FOR UPDATE) and Transaction.
     */
    public function checkout(int $userId, string $address, string $paymentMethod): array {
        try {
            $this->db->beginTransaction();

            // 1. Get cart items
            $stmt = $this->db->prepare(
                'SELECT ci.variant_id, ci.quantity, ci.unit_price, c.id as cart_id
                 FROM carts c
                 JOIN cart_items ci ON c.id = ci.cart_id
                 WHERE c.user_id = ?'
            );
            $stmt->execute([$userId]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Empty Cart Check
            if (empty($cartItems)) {
                throw new Exception('Your cart is empty.');
            }

            $cartId = $cartItems[0]['cart_id'];
            $totalAmount = 0;
            $itemsToProcess = [];

            // 2. Lock and Validate Variants (Price & Stock)
            foreach ($cartItems as $item) {
                // Pessimistic Locking — prevent race conditions
                $vStmt = $this->db->prepare(
                    'SELECT base_price, quantity FROM product_variants WHERE id = ? FOR UPDATE'
                );
                $vStmt->execute([$item['variant_id']]);
                $variant = $vStmt->fetch(PDO::FETCH_ASSOC);

                if (!$variant) {
                    throw new Exception("Product variant ID {$item['variant_id']} no longer exists.");
                }

                if ($variant['quantity'] < $item['quantity']) {
                    throw new Exception("Not enough stock for one of your items. Only {$variant['quantity']} left.");
                }

                // ZERO-TRUST: Use base_price from DB, NOT from cart or Frontend
                $unitPrice = (float) $variant['base_price'];
                $totalAmount += ($unitPrice * $item['quantity']);

                $itemsToProcess[] = [
                    'variant_id'  => $item['variant_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $unitPrice,
                    'total_price' => $unitPrice * $item['quantity'],
                    'new_stock'   => $variant['quantity'] - $item['quantity']
                ];
            }

            // 3. Calculate Discount based on Membership Tier
            $discountAmount = 0;
            $mStmt = $this->db->prepare(
                'SELECT m.tier_id, t.discount_percent
                 FROM members m
                 LEFT JOIN membership_tiers t ON m.tier_id = t.id
                 WHERE m.user_id = ?'
            );
            $mStmt->execute([$userId]);
            $member = $mStmt->fetch(PDO::FETCH_ASSOC);

            if ($member && $member['discount_percent'] > 0) {
                $discountAmount = $totalAmount * ($member['discount_percent'] / 100);
            }

            $finalAmount = $totalAmount - $discountAmount;
            $orderCode = 'ORD-' . strtoupper(substr(uniqid(), -8));

            // 4. Create Order Record
            $oStmt = $this->db->prepare(
                'INSERT INTO orders (user_id, order_code, shipping_address, total_amount, 
                                   discount_amount, final_amount, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $oStmt->execute([
                $userId, $orderCode, $address, $totalAmount, 
                $discountAmount, $finalAmount, $paymentMethod
            ]);
            $orderId = (int) $this->db->lastInsertId();

            // 5. Create Order Items and Update Inventory
            $oiStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, variant_id, quantity, unit_price, total_price)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stockStmt = $this->db->prepare('UPDATE product_variants SET quantity = ? WHERE id = ?');

            foreach ($itemsToProcess as $item) {
                $oiStmt->execute([
                    $orderId, $item['variant_id'], $item['quantity'], 
                    $item['unit_price'], $item['total_price']
                ]);
                $stockStmt->execute([$item['new_stock'], $item['variant_id']]);
            }

            // 6. Add Loyalty Points (1 point = 100,000 VND)
            $pointsEarned = (int) floor($finalAmount / 100000);
            if ($pointsEarned > 0) {
                $pStmt = $this->db->prepare('UPDATE members SET points = points + ? WHERE user_id = ?');
                $pStmt->execute([$pointsEarned, $userId]);
            }

            // 7. Clear Cart
            $this->db->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([$cartId]);

            $this->db->commit();

            return [
                'success'    => true,
                'order_id'   => $orderId,
                'order_code' => $orderCode,
                'total'      => $finalAmount
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get orders for a specific member.
     */
    public function getUserOrders(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT id, order_code, final_amount, status, payment_status, created_at 
             FROM orders WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order detail with items.
     */
    public function getOrderDetail(string $code, ?int $userId = null): ?array {
        $sql = 'SELECT * FROM orders WHERE order_code = ?';
        $params = [$code];
        
        if ($userId !== null) {
            $sql .= ' AND user_id = ?';
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) return null;

        $stmt = $this->db->prepare(
            'SELECT oi.*, pv.sku_code, pv.ram, pv.color, pv.storage, pv.img_url,
                    p.name as product_name, p.id as product_id
             FROM order_items oi
             JOIN product_variants pv ON oi.variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    /**
     * Admin: Get all orders with pagination and filters.
     */
    public function getAllOrders(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $where .= ' AND o.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (o.order_code LIKE ? OR u.fullname LIKE ? OR u.email LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        $countSql = "SELECT COUNT(*) FROM orders o JOIN users u ON o.user_id = u.id WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT o.id, o.order_code, o.final_amount, o.status, o.payment_status, 
                       o.created_at, o.shipping_address, u.fullname as customer_name, u.email as customer_email
                FROM orders o JOIN users u ON o.user_id = u.id
                WHERE $where ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) { $stmt->bindValue($i++, $p); }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items'       => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
        ];
    }

    /**
     * Admin: Update order status. Includes Stock Restoration on cancel.
     */
    public function updateStatus(int $orderId, string $status, ?string $paymentStatus = null): bool {
        try {
            $stmt = $this->db->prepare('SELECT status FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $currentStatus = $stmt->fetchColumn();
            if (!$currentStatus) return false;

            $this->db->beginTransaction();

            // Restore stock if canceling
            if ($status === 'canceled' && $currentStatus !== 'canceled') {
                $iStmt = $this->db->prepare('SELECT variant_id, quantity FROM order_items WHERE order_id = ?');
                $iStmt->execute([$orderId]);
                $items = $iStmt->fetchAll(PDO::FETCH_ASSOC);

                $stockStmt = $this->db->prepare('UPDATE product_variants SET quantity = quantity + ? WHERE id = ?');
                foreach ($items as $item) {
                    if ($item['variant_id']) {
                        $stockStmt->execute([$item['quantity'], $item['variant_id']]);
                    }
                }
            }

            $sql = 'UPDATE orders SET status = ?' . ($paymentStatus ? ', payment_status = ?' : '') . ' WHERE id = ?';
            $params = [$status];
            if ($paymentStatus) $params[] = $paymentStatus;
            $params[] = $orderId;

            $uStmt = $this->db->prepare($sql);
            $uStmt->execute($params);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Count orders by status (for dashboard).
     */
    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get total revenue (sum of final_amount for completed orders).
     */
    public function getTotalRevenue(): float {
        $stmt = $this->db->query("SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE status = 'completed'");
        return (float) $stmt->fetchColumn();
    }

    // ===== Legacy Backward-Compat Methods (used by FE views) =====

    /**
     * Get order by ID (legacy compat).
     */
    public function getOrderById($orderId) {
        $stmt = $this->db->prepare('SELECT o.*, u.fullname AS customer_name, u.email AS customer_email, u.phone AS customer_phone
            FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) return null;

        // Map new schema fields to legacy view expectations
        $order['total'] = $order['final_amount'] ?? $order['total_amount'] ?? 0;
        $order['subtotal'] = $order['total_amount'] ?? 0;
        $order['shipping_fee'] = 0;
        $order['tax'] = 0;
        $order['shipping_city'] = '';
        $order['shipping_district'] = '';
        $order['shipping_ward'] = '';
        $order['notes'] = '';
        return $order;
    }

    /**
     * Get order items by order ID (legacy compat).
     */
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare(
            'SELECT oi.*, p.name AS product_name, p.id AS product_id, 
                    oi.unit_price AS product_price, (oi.unit_price * oi.quantity) AS subtotal
             FROM order_items oi
             JOIN product_variants pv ON oi.variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create order from FE checkout (legacy compat).
     */
    public function createOrder($data) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, order_code, shipping_address, total_amount, 
                                    discount_amount, final_amount, payment_method)
                 VALUES (?, ?, ?, ?, 0, ?, ?)"
            );
            $userId = $_SESSION['user_id'] ?? 0;
            $orderCode = 'ORD-' . strtoupper(substr(uniqid(), -8));
            $total = $data['total'] ?? $data['subtotal'] ?? 0;

            $stmt->execute([
                $userId, $orderCode, $data['shipping_address'] ?? '',
                $total, $total, $data['payment_method'] ?? 'cod'
            ]);
            $orderId = (int) $this->db->lastInsertId();

            // Insert items
            if (!empty($data['items'])) {
                $iStmt = $this->db->prepare(
                    "INSERT INTO order_items (order_id, variant_id, quantity, unit_price, total_price)
                     VALUES (?, ?, ?, ?, ?)"
                );
                foreach ($data['items'] as $item) {
                    // Try to find a variant for this product
                    $vStmt = $this->db->prepare('SELECT id FROM product_variants WHERE product_id = ? LIMIT 1');
                    $vStmt->execute([$item['product_id']]);
                    $variantId = $vStmt->fetchColumn() ?: 0;

                    $iStmt->execute([
                        $orderId, $variantId, $item['quantity'],
                        $item['product_price'], $item['subtotal']
                    ]);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("createOrder error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update order status (legacy compat).
     */
    public function updateOrderStatus($orderId, $status) {
        return $this->updateStatus((int)$orderId, $status);
    }

    /**
     * Admin: Get all orders (legacy compat — no pagination).
     */
    /**
     * Admin: Get all orders (legacy compat — no pagination).
     */
    public function getAllOrdersAdmin($status = 'all') {
        return $this->getPaginatedOrders(1, 999999, $status)['orders'];
    }

    /**
     * Admin: Get paginated orders with optional status filter.
     */
    public function getPaginatedOrders($page = 1, $limit = 10, $status = 'all') {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if ($status !== 'all') {
            $where .= " AND o.status = ?";
            $params[] = $status;
        }

        $sql = "SELECT o.*, u.fullname AS customer_name, u.email AS customer_email, u.phone AS customer_phone
                FROM orders o JOIN users u ON o.user_id = u.id
                WHERE $where
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k + 1, $v);
        }
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map fields for legacy views
        foreach ($orders as &$o) {
            $o['total'] = $o['final_amount'] ?? $o['total_amount'] ?? 0;
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM orders o WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        return [
            'orders' => $orders,
            'total' => $total,
            'totalPages' => ceil($total / $limit)
        ];
    }
}
