<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../models/OrderModel.php';

$orderModel = new OrderModel();

$orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$newStatus = isset($_POST['status']) ? $_POST['status'] : '';

$validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$result = $orderModel->updateOrderStatus($orderId, $newStatus);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>
