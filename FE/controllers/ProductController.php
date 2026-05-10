<?php
// controllers/ProductController.php — Uses BaseModel-powered models

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ProductReviewModel.php';

class ProductController {
    
    public function show() {
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($productId <= 0) {
            header('Location: ?page=shop');
            exit();
        }
        
        $productModel = new ProductModel();
        $reviewModel  = new ProductReviewModel();
        
        // Get product details
        $product = $productModel->getProductById($productId);
        
        if (!$product) {
            header('Location: ?page=shop');
            exit();
        }
        
        // Get product images
        $productImages = $productModel->getProductImages($productId);
        if (empty($productImages)) {
            $productImages = [[
                'image_url'     => $product['image'],
                'is_primary'    => 1,
                'display_order' => 0
            ]];
        }
        
        // Get product variants (grouped by type)
        $variants = $productModel->getProductVariantsGrouped($productId);
        
        // Get default variants
        $defaultVariants = $productModel->getDefaultVariants($productId);
        
        // Get related products
        $relatedProducts = $productModel->getRelatedProducts($productId, $product['category'], 4);
        
        // Get review stats
        $reviewStats = $reviewModel->getReviewStats($productId);
        $averageRating = $reviewStats ? round($reviewStats['average_rating'], 1) : 0;
        $reviewCount   = $reviewStats ? $reviewStats['total_reviews'] : 0;

        // Get approved reviews
        $reviews = $reviewModel->getApprovedReviewsByProduct($productId, 10, 0);

        // Calculate default price
        $displayPrice = $product['price'];
        foreach ($defaultVariants as $defaultVar) {
            $displayPrice += $defaultVar['price_modifier'];
        }
        
        // Load view
        include 'views/client/product_detail.php';
    }
}
?>
