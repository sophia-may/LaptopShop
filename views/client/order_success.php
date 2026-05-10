<?php 
// File: views/client/order_success.php
include 'views/layouts/header.php';

$orderId = $_GET['id'] ?? 0;

// Load order details
require_once 'models/OrderModel.php';

$orderModel = new OrderModel();

$order = $orderModel->getOrderById($orderId);
$orderItems = $orderModel->getOrderItems($orderId);

if (!$order) {
    header('Location: ?page=home');
    exit;
}
?>

<!-- Hero Section -->
<div class="page-hero text-center text-white" style="background: #2ecc71 !important; background-image: none !important;">
    <div class="container">
        <div class="mb-3">
            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
        </div>
        <h1 class="hero-title">Đặt hàng thành công!</h1>
        <p class="lead">Cảm ơn bạn đã mua hàng</p>
    </div>
</div>

<!-- Order Details -->
<section class="order-success-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Order Info Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-4">
                        <h4>Mã đơn hàng: <strong class="text-danger">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></h4>
                        <p class="text-muted mb-0">Chúng tôi đã gửi xác nhận đơn hàng đến <strong><?= htmlspecialchars($order['customer_email']) ?></strong></p>
                    </div>
                </div>
                
                <!-- Customer & Shipping Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Người nhận</label>
                                <p class="mb-0"><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Số điện thoại</label>
                                <p class="mb-0"><strong><?= htmlspecialchars($order['customer_phone']) ?></strong></p>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small">Địa chỉ giao hàng</label>
                                <p class="mb-0">
                                    <strong>
                                        <?= htmlspecialchars($order['shipping_address']) ?>
                                        <?= $order['shipping_ward'] ? ', ' . htmlspecialchars($order['shipping_ward']) : '' ?>
                                        <?= $order['shipping_district'] ? ', ' . htmlspecialchars($order['shipping_district']) : '' ?>
                                        <?= $order['shipping_city'] ? ', ' . htmlspecialchars($order['shipping_city']) : '' ?>
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Sản phẩm trong đơn</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td><?= number_format($item['product_price'], 0, ',', '.') ?>đ</td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td class="text-end"><strong><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-6 text-muted">Tạm tính:</div>
                            <div class="col-6 text-end"><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-6 text-muted">Phí vận chuyển:</div>
                            <div class="col-6 text-end"><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-6 text-muted">Thuế:</div>
                            <div class="col-6 text-end"><?= number_format($order['tax'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-12"><hr></div>
                            
                            <div class="col-6"><h5>Tổng cộng:</h5></div>
                            <div class="col-6 text-end"><h5 class="text-danger"><?= number_format($order['total'], 0, ',', '.') ?>đ</h5></div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <strong>Phương thức thanh toán:</strong> 
                        <?php
                            $paymentMethods = [
                                'cod' => 'Thanh toán khi nhận hàng (COD)',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                'momo' => 'Ví MoMo',
                                'credit_card' => 'Thẻ tín dụng/ghi nợ'
                            ];
                            echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                        ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="?page=shop" class="btn btn-danger btn-lg me-2">
                        <i class="bi bi-bag-fill"></i> Tiếp tục mua sắm
                    </a>
                    <a href="?page=home" class="btn btn-outline-danger btn-lg">
                        <i class="bi bi-house-fill"></i> Về trang chủ
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php include 'views/layouts/footer.php'; ?>
