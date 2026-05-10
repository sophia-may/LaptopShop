<?php
// File: views/admin/order_details.php
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php?page=home");
    exit();
}

require_once __DIR__ . '/../../models/OrderModel.php';

$orderModel = new OrderModel();
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    die("Invalid order ID");
}

// Lấy thông tin order và items từ database
$order = $orderModel->getOrderById($orderId);
$items = $orderModel->getOrderItems($orderId);

if (!$order) {
    die("Order not found");
}

function getStatusBadge($status) {
    switch($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'processing': return 'bg-info';
        case 'shipped': return 'bg-primary';
        case 'completed': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getPaymentBadge($method) {
    switch($method) {
        case 'cod': return 'bg-warning text-dark';
        case 'bank_transfer': return 'bg-info';
        case 'momo': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?= $orderId ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            font-weight: 600;
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .print-btn {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="order-header no-print">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="bi bi-receipt"></i> Chi Tiết Đơn Hàng #<?= $orderId ?></h2>
                    <p class="mb-0 mt-2 opacity-75">
                        <i class="bi bi-calendar3"></i> Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div>
                    <a href="index.php?page=manage_orders" class="btn btn-light me-2">
                        <i class="bi bi-arrow-left"></i> Quay Lại
                    </a>
                    <button onclick="window.print()" class="btn btn-light">
                        <i class="bi bi-printer"></i> In
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            <!-- Thông Tin Khách Hàng -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle"></i> Thông Tin Khách Hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><span class="info-label">Họ tên:</span> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p><span class="info-label">Email:</span> <?= htmlspecialchars($order['customer_email']) ?></p>
                        <p><span class="info-label">Số điện thoại:</span> <?= htmlspecialchars($order['customer_phone']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Địa Chỉ Giao Hàng -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Địa Chỉ Giao Hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><?= htmlspecialchars($order['shipping_address']) ?></p>
                        <p class="mb-0 text-muted">
                            <?= $order['shipping_ward'] ? htmlspecialchars($order['shipping_ward']) . ', ' : '' ?>
                            <?= $order['shipping_district'] ? htmlspecialchars($order['shipping_district']) . ', ' : '' ?>
                            <?= htmlspecialchars($order['shipping_city']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh Sách Sản Phẩm -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-basket3-fill"></i> Sản Phẩm Đã Đặt (<?= count($items) ?> sản phẩm)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%">Sản phẩm</th>
                                <th class="text-end" style="width: 20%">Đơn giá</th>
                                <th class="text-center" style="width: 10%">SL</th>
                                <th class="text-end" style="width: 20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                    <br>
                                    <small class="text-muted">ID: #<?= $item['product_id'] ?></small>
                                </td>
                                <td class="text-end"><?= number_format($item['product_price'], 0, ',', '.') ?> ₫</td>
                                <td class="text-center"><span class="badge bg-secondary"><?= $item['quantity'] ?></span></td>
                                <td class="text-end"><strong><?= number_format($item['subtotal'], 0, ',', '.') ?> ₫</strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tổng Tiền & Thông Tin Đơn -->
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-info-circle-fill"></i> Thông Tin Đơn Hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <span class="info-label">Trạng thái:</span>
                                <span class="badge <?= getStatusBadge($order['status']) ?> fs-6 ms-2">
                                    <i class="bi bi-circle-fill"></i> <?= ucfirst($order['status']) ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="info-label">Thanh toán:</span>
                                <span class="badge <?= getPaymentBadge($order['payment_method']) ?> fs-6 ms-2">
                                    <?php 
                                    $paymentNames = [
                                        'cod' => 'COD - Tiền mặt',
                                        'bank_transfer' => 'Chuyển khoản',
                                        'momo' => 'Ví MoMo'
                                    ];
                                    echo $paymentNames[$order['payment_method']] ?? ucfirst($order['payment_method']);
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                        <div class="alert alert-light border">
                            <strong><i class="bi bi-sticky-fill"></i> Ghi chú:</strong><br>
                            <?= nl2br(htmlspecialchars($order['notes'])) ?>
                        </div>
                        <?php endif; ?>

                        <p class="mb-0 text-muted">
                            <small>
                                <i class="bi bi-clock-history"></i> 
                                Cập nhật lần cuối: <?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tổng Tiền -->
            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Chi Tiết Thanh Toán</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="border-0 pb-2">Tạm tính:</td>
                                <td class="text-end border-0 pb-2"><?= number_format($order['subtotal'], 0, ',', '.') ?> ₫</td>
                            </tr>
                            <tr>
                                <td class="border-0 py-2">Phí giao hàng:</td>
                                <td class="text-end border-0 py-2"><?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</td>
                            </tr>
                            <tr>
                                <td class="border-0 py-2">Thuế (VAT):</td>
                                <td class="text-end border-0 py-2"><?= number_format($order['tax'], 0, ',', '.') ?> ₫</td>
                            </tr>
                            <tr class="border-top">
                                <td class="pt-3"><strong class="fs-5">TỔNG CỘNG:</strong></td>
                                <td class="text-end pt-3">
                                    <strong class="fs-4 text-danger">
                                        <?= number_format($order['total'], 0, ',', '.') ?> ₫
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
