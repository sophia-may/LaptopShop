<?php
// controllers/ShopController.php — Uses BaseModel-powered ProductModel

require_once __DIR__ . '/../models/ProductModel.php';

class ShopController {
    public function index() {
        $productModel = new ProductModel(); // auto-connects via BaseModel

        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

        // Pagination
        $limit  = 8;
        $page   = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Filters
        $filters = [
            'brand'     => isset($_GET['brand']) && is_array($_GET['brand']) ? $_GET['brand'] : [],
            'category'  => isset($_GET['category']) ? $_GET['category'] : null,
            'storage'   => isset($_GET['storage']) && is_array($_GET['storage']) ? $_GET['storage'] : [],
            'price_min' => isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float)$_GET['price_min'] : null,
            'price_max' => isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float)$_GET['price_max'] : null,
        ];

        $products      = $productModel->getFilteredProducts($filters, $limit, $offset, $sort);
        $totalProducts = $productModel->countFilteredProducts($filters);
        $totalPages    = ceil($totalProducts / $limit);
        $filterOptions = $productModel->getFilterOptions();

        include 'views/client/shop.php';
    }
}
?>