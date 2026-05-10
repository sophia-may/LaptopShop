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
<div class="page-hero text-center bg-success text-white">
    <div class="container">
        <div class="mb-3">
            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
        </div>
        <h1 class="hero-title">Order Placed Successfully!</h1>
        <p class="lead">Thank you for your purchase</p>
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
                        <h4>Order ID: <strong class="text-primary">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></h4>
                        <p class="text-muted mb-0">We've sent a confirmation email to <strong><?= htmlspecialchars($order['customer_email']) ?></strong></p>
                    </div>
                </div>
                
                <!-- Customer & Shipping Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Customer Name</label>
                                <p class="mb-0"><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Phone Number</label>
                                <p class="mb-0"><strong><?= htmlspecialchars($order['customer_phone']) ?></strong></p>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small">Shipping Address</label>
                                <p class="mb-0">
                                    <strong>
                                        <?= htmlspecialchars($order['shipping_address']) ?>, 
                                        <?= htmlspecialchars($order['shipping_ward']) ?>, 
                                        <?= htmlspecialchars($order['shipping_district']) ?>, 
                                        <?= htmlspecialchars($order['shipping_city']) ?>
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Subtotal</th>
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
                            <div class="col-6 text-muted">Subtotal:</div>
                            <div class="col-6 text-end"><?= number_format($order['subtotal'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-6 text-muted">Shipping Fee:</div>
                            <div class="col-6 text-end"><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-6 text-muted">Tax:</div>
                            <div class="col-6 text-end"><?= number_format($order['tax'], 0, ',', '.') ?>đ</div>
                            
                            <div class="col-12"><hr></div>
                            
                            <div class="col-6"><h5>Total:</h5></div>
                            <div class="col-6 text-end"><h5 class="text-primary"><?= number_format($order['total'], 0, ',', '.') ?>đ</h5></div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <strong>Payment Method:</strong> 
                        <?php
                            $paymentMethods = [
                                'cod' => 'Cash on Delivery (COD)',
                                'bank_transfer' => 'Bank Transfer',
                                'momo' => 'MoMo E-Wallet',
                                'credit_card' => 'Credit/Debit Card'
                            ];
                            echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                        ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="?page=shop" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-bag-fill"></i> Continue Shopping
                    </a>
                    <a href="?page=home" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-house-fill"></i> Go to Home
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php include 'views/layouts/footer.php'; ?>
