<?php
/**
 * ajax/checkout_handler.php — Handles checkout AJAX requests
 */
header('Content-Type: application/json');
session_start();

ini_set('display_errors', 0);
error_reporting(0);

try {
    require_once dirname(__DIR__) . '/controllers/CheckoutController.php';

    $action = $_POST['action'] ?? '';

    if ($action === 'place_order') {
        $controller = new CheckoutController();
        $result = $controller->placeOrder();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
