<?php
/**
 * AJAX Handler for Shopping Cart
 * Uses session-based cart (for guest users) + BaseModel-powered ProductModel
 */
session_start();

require_once '../models/ProductModel.php';

header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$productModel = new ProductModel(); // auto-connects via BaseModel

switch ($action) {
    
    case 'add':
        $productId = (int)($_POST['product_id'] ?? ($_POST['productid'] ?? 0));
        $quantity  = max(1, (int)($_POST['quantity'] ?? 1));
        
        if ($productId > 0) {
            $product = $productModel->getProductById($productId);
            
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => floatval($product['price']),
                    'image'    => $product['image'],
                    'category' => $product['category'] ?? 'Uncategorized',
                    'brand'    => $product['brand'] ?? '',
                    'stock'    => intval($product['stock']),
                    'quantity' => $quantity
                ];
            }

            echo json_encode([
                'success'   => true,
                'message'   => 'Product added to cart',
                'itemCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        }
        break;
    
    case 'update':
        $productId = (int)($_POST['product_id'] ?? ($_POST['productid'] ?? 0));
        $quantity  = (int)($_POST['quantity'] ?? 1);
        
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                echo json_encode(['success' => true]);
            } else {
                unset($_SESSION['cart'][$productId]);
                echo json_encode(['success' => true, 'message' => 'Item removed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not in cart']);
        }
        break;
    
    case 'remove':
        $productId = (int)($_POST['product_id'] ?? ($_POST['productid'] ?? 0));
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not in cart']);
        }
        break;
    
    case 'get':
        $cartItems = [];
        $total = 0;
        
        foreach ($_SESSION['cart'] as $productId => $item) {
            $product = $productModel->getProductById($productId);
            if ($product) {
                $cartItems[$productId] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'category' => $product['category'],
                    'quantity' => $item['quantity']
                ];
                $total += $product['price'] * $item['quantity'];
            }
        }
        
        echo json_encode([
            'success'   => true,
            'cart'      => $cartItems,
            'itemCount' => array_sum(array_column($_SESSION['cart'], 'quantity')),
            'total'     => $total
        ]);
        break;
    
    case 'apply_coupon':
        $couponCode = trim($_POST['coupon_code'] ?? '');
        if ($couponCode === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            break;
        }

        try {
            $stmt = $productModel->db->prepare(
                'SELECT id, code, discount_percent, is_active FROM coupons WHERE UPPER(code) = UPPER(?) LIMIT 1'
            );
            $stmt->execute([$couponCode]);
            $coupon = $stmt->fetch();

            if ($coupon && intval($coupon['is_active']) === 1) {
                // Store coupon in session for later checkout use if needed
                $_SESSION['applied_coupon'] = [
                    'id' => $coupon['id'],
                    'code' => $coupon['code'],
                    'discount_percent' => floatval($coupon['discount_percent'])
                ];

                echo json_encode([
                    'success' => true,
                    'message' => 'Mã giảm giá hợp lệ: ' . htmlspecialchars($coupon['code']),
                    'coupon' => $_SESSION['applied_coupon']
                ]);
            } elseif ($coupon) {
                echo json_encode(['success' => false, 'message' => 'Mã giảm giá đã tồn tại nhưng hiện không khả dụng.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Mã giảm giá không có trong database.']);
            }
        } catch (Exception $e) {
            error_log('apply_coupon error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi kiểm tra mã giảm giá. Vui lòng thử lại sau.']);
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'message' => 'Cart cleared!']);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action!']);
        break;
}
?>
