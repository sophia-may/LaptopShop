<?php
// controllers/admin/ProductAdminController.php

require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../helpers/ImageUploader.php';

class ProductAdminController {
    private $productModel;

    public function __construct() {
        // Check admin permission
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
            header('Location: index.php?page=home');
            exit;
        }

        $this->productModel = new ProductModel();
    }

    public function index() {
        // Get all products for admin
        $page = isset($_GET['product_page']) ? (int)$_GET['product_page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productModel->getAllProducts($limit, $offset);
        $totalProducts = $this->productModel->countProducts();
        $totalPages = ceil($totalProducts / $limit);

        // Load view
        include 'views/admin/manage_products.php';
    }

    public function handleAjax() {
        header('Content-Type: application/json');
        
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'get':
                $this->getProduct();
                break;
            case 'create':
                $this->createProduct();
                break;
            case 'update':
                $this->updateProduct();
                break;
            case 'delete':
                $this->deleteProduct();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function getProduct() {
        $id = $_GET['id'] ?? 0;
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    }

    private function createProduct() {
        $imagePath = $this->handleImageUpload();
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'storage' => $_POST['storage'] ?? '',
            'ram' => $_POST['ram'] ?? '',
            'description' => $_POST['description'] ?? '',
            'image' => $imagePath ?: 'placeholder.png',
            'stock' => $_POST['stock'] ?? 0,
            'category' => $_POST['category'] ?? ''
        ];

        if ($this->productModel->createProduct($data)) {
            echo json_encode(['success' => true, 'message' => 'Product created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create product']);
        }
    }

    private function updateProduct() {
        $id = $_POST['id'] ?? 0;
        $imagePath = $this->handleImageUpload();
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'storage' => $_POST['storage'] ?? '',
            'ram' => $_POST['ram'] ?? '',
            'description' => $_POST['description'] ?? '',
            'image' => $imagePath ?: ($_POST['existing_image'] ?: 'placeholder.png'),
            'stock' => $_POST['stock'] ?? 0,
            'category' => $_POST['category'] ?? ''
        ];

        if ($this->productModel->updateProduct($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        }
    }

    private function deleteProduct() {
        $id = $_POST['id'] ?? 0;

        if ($this->productModel->deleteProduct($id)) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
    }

    private function handleImageUpload() {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['image'];
        $uploadDir = __DIR__ . '/../../assets/img/products/';
        $uploadDirWeb = 'assets/img/products/';
        
        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        $webpath = $uploadDirWeb . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $webpath;
        }

        return null;
    }
}
