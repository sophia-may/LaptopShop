<?php
/**
 * CheckoutController.php
 * ✅ Fixed: Use absolute paths for require_once
 */

// ✅ Get root directory
$rootPath = dirname(__DIR__); // Từ controllers/ lên 1 cấp = root

// ✅ Load models với absolute path
require_once $rootPath . '/models/CartModel.php';
require_once $rootPath . '/models/OrderModel.php';

class CheckoutController {

    public function __construct() {
        // Models auto-connect via BaseModel
    }

    /**
     * Xử lý đặt hàng
     */
    public function placeOrder() {
        try {
            // Validate cart from session
            $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

            if (empty($cartItems)) {
                return [
                    'success' => false,
                    'message' => 'Cart is empty'
                ];
            }

            // ✅ Validate POST data
            $required = ['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'shipping_city', 'payment_method'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field '$field' is required"
                    ];
                }
            }

            // ✅ Tính totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                if (!isset($item['price']) || !isset($item['quantity']) || !isset($item['id']) || !isset($item['name'])) {
                    continue;
                }

                $itemSubtotal = floatval($item['price']) * intval($item['quantity']);
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal
                ];
            }

            if (empty($orderItems)) {
                return [
                    'success' => false,
                    'message' => 'No valid items in cart'
                ];
            }

            $shippingFee = ($subtotal >= 1500000) ? 0 : 30000;
            $tax = 0;
            $total = $subtotal + $shippingFee + $tax;

            // ✅ Tạo order data
            $orderData = [
                'customer_name' => trim($_POST['customer_name']),
                'customer_email' => trim($_POST['customer_email']),
                'customer_phone' => trim($_POST['customer_phone']),
                'shipping_address' => trim($_POST['shipping_address']),
                'shipping_city' => trim($_POST['shipping_city']),
                'shipping_district' => trim($_POST['shipping_district'] ?? ''),
                'shipping_ward' => trim($_POST['shipping_ward'] ?? ''),
                'payment_method' => $_POST['payment_method'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => $tax,
                'total' => $total,
                'notes' => trim($_POST['notes'] ?? ''),
                'items' => $orderItems
            ];

            // ✅ Tạo order
            $orderModel = new OrderModel();
            $orderId = $orderModel->createOrder($orderData);

            if ($orderId) {
                // Clear session cart
                $_SESSION['cart'] = [];

                return [
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'order_id' => $orderId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to create order in database'
                ];
            }

        } catch (Exception $e) {
            error_log("CheckoutController::placeOrder error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hiển thị trang checkout
     */
    public function index() {
        $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if (empty($cartItems)) {
            header('Location: ?page=shop');
            exit;
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            if (isset($item['price']) && isset($item['quantity'])) {
                $subtotal += floatval($item['price']) * intval($item['quantity']);
            }
        }

        $shippingFee = ($subtotal >= 1500000) ? 0 : 30000;
        $tax = 0;
        $total = $subtotal + $shippingFee + $tax;

        include 'views/client/checkout.php';
    }
}
?>
