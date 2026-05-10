<?php
session_start();

require_once __DIR__ . '/../controllers/admin/ProductAdminController.php';

$controller = new ProductAdminController();
$controller->handleAjax();
