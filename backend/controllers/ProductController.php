<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProductModel.php';

/**
 * ProductController — Public product catalog endpoints.
 *
 * Endpoints:
 *   GET /api/products          — Paginated list with filters
 *   GET /api/products/featured — Featured products for homepage
 *   GET /api/products/:slug    — Product detail with variants
 */
class ProductController extends BaseController {

    private ProductModel $model;

    public function __construct() {
        $this->model = new ProductModel();
    }

    /**
     * GET /api/products
     * Public: paginated product list.
     * Query params: page, limit, category_id, category_slug, brand_id, search, price_min, price_max
     */
    public function index(): void {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(50, max(1, (int) ($_GET['limit'] ?? 12)));

        $filters = [
            'category_id'   => $_GET['category_id'] ?? '',
            'category_slug' => $_GET['category_slug'] ?? '',
            'brand_id'      => $_GET['brand_id'] ?? '',
            'search'        => $_GET['search'] ?? '',
            'price_min'     => $_GET['price_min'] ?? '',
            'price_max'     => $_GET['price_max'] ?? '',
        ];

        $result = $this->model->getPaginated($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * GET /api/products/featured
     * Public: featured products for homepage.
     */
    public function featured(): void {
        $limit = min(20, max(1, (int) ($_GET['limit'] ?? 8)));
        $products = $this->model->getFeatured($limit);
        $this->jsonResponse($products);
    }

    /**
     * GET /api/products/:slug
     * Public: product detail with variants.
     */
    public function show(string $slug): void {
        $product = $this->model->findBySlug($slug);
        if (!$product) {
            $this->jsonError('Product not found.', 404);
        }
        $this->jsonResponse($product);
    }
}
