<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../models/OrderModel.php';

$orderModel = new OrderModel();

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$order = $orderModel->getOrderById($orderId);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Build HTML
ob_start();
?>
<div class="row">
    <div class="col-md-6">
        <h6>Customer Information</h6>
        <p>
            <strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?>
        </p>
    </div>
    <div class="col-md-6">
        <h6>Shipping Address</h6>
        <p>
            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
            <?= htmlspecialchars($order['shipping_city']) ?>
        </p>
    </div>
</div>

<hr>

<h6>Order Items</h6>
<table class="table table-sm">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order['items'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= number_format($item['product_price']) ?> ₫</td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['subtotal']) ?> ₫</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
            <td><strong><?= number_format($order['subtotal']) ?> ₫</strong></td>
        </tr>
        <tr>
            <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
            <td><strong><?= number_format($order['shipping_fee']) ?> ₫</strong></td>
        </tr>
        <tr>
            <td colspan="3" class="text-end"><strong>Total:</strong></td>
            <td><strong class="text-primary"><?= number_format($order['total']) ?> ₫</strong></td>
        </tr>
    </tfoot>
</table>

<hr>

<p>
    <strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?><br>
    <strong>Status:</strong> <span class="badge bg-info"><?= ucfirst($order['status']) ?></span><br>
    <strong>Order Date:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
</p>

<?php if (!empty($order['notes'])): ?>
<p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
<?php endif; ?>

<?php
$html = ob_get_clean();
echo json_encode(['success' => true, 'html' => $html]);
?>
