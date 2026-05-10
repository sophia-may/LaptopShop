<?php
// ajax/checkout_handler_simple.php
// Test file cơ bản

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Ajax handler is working!',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
