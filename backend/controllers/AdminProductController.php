<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';

/**
 * AdminProductController — CRUD for products and variants.
 * Supports multi-variant transactions and image uploads.
 */
class AdminProductController extends BaseController {

    private ProductModel $model;

    public function __construct() {
        $this->model = new ProductModel();
    }

    /**
     * GET /api/admin/products
     * Admin: List all products with management filters.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = $this->pick($_GET, ['category_id', 'brand_id', 'search', 'sort', 'dir']);

        $result = $this->model->getPaginated($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * GET /api/admin/products/:id
     * Admin: Get full product detail + all variants.
     */
    public function show(string $id): void {
        AuthMiddleware::requireAdmin();
        $product = $this->model->findBySlug($id); // Using slug find as it's robust

        if (!$product) {
            $this->jsonError('Product not found.', 404);
        }

        $this->jsonResponse($product);
    }

    /**
     * POST /api/admin/products
     * Admin: Create product with optional initial variants.
     * Transactional.
     */
    public function store(): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData(['detail_description']);

        $errors = $this->validate($data, [
            'name' => 'required',
            'slug' => 'required'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        try {
            $this->model->db->beginTransaction();

            // Create product
            $productId = $this->model->create($data);

            // Create variants if provided
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    $this->model->addVariant($productId, $v);
                }
            }

            $this->model->db->commit();
            $this->jsonResponse(['id' => $productId, 'message' => 'Product created.'], 201);

        } catch (Exception $e) {
            $this->model->db->rollBack();
            $this->jsonError('Failed to create product. Maybe slug or SKU is duplicate.', 400);
        }
    }

    /**
     * PUT /api/admin/products/:id
     * Admin: Update product info.
     */
    public function update(string $id): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData(['detail_description']);

        $success = $this->model->update((int)$id, $data);
        if ($success) {
            $this->jsonResponse(['message' => 'Product updated.']);
        } else {
            $this->jsonError('Update failed.', 400);
        }
    }

    /**
     * DELETE /api/admin/products/:id
     * Admin: Delete product.
     */
    public function destroy(string $id): void {
        AuthMiddleware::requireAdmin();
        $this->model->delete((int)$id);
        $this->jsonResponse(['message' => 'Product deleted.']);
    }

    /**
     * POST /api/admin/products/:id/variants
     * Admin: Add a variant to an existing product.
     */
    public function addVariant(string $id): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData();
        
        $vId = $this->model->addVariant((int)$id, $data);
        $this->jsonResponse(['id' => $vId, 'message' => 'Variant added.']);
    }

    /**
     * POST /api/admin/products/upload
     * Admin: Upload product variant image.
     */
    public function uploadImage(): void {
        AuthMiddleware::requireAdmin();

        if (empty($_FILES['image'])) {
            $this->jsonError('No image uploaded.', 400);
        }

        $url = ImageUploader::upload($_FILES['image'], 'products');
        if ($url) {
            $this->jsonResponse(['url' => $url]);
        } else {
            $this->jsonError('Image upload failed.', 400);
        }
    }
}
