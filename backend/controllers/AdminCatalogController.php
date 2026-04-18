<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/BrandModel.php';
require_once __DIR__ . '/../models/FaqModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';

/**
 * AdminCatalogController — Shared CRUD for categories, brands, and FAQs.
 */
class AdminCatalogController extends BaseController {

    private CategoryModel $categoryModel;
    private BrandModel $brandModel;
    private FaqModel $faqModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->faqModel = new FaqModel();
    }

    // --- CATEGORIES ---
    public function listCategories(): void {
        AuthMiddleware::requireAdmin();
        $this->jsonResponse($this->categoryModel->getAll());
    }

    public function storeCategory(): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData();
        try {
            $id = $this->categoryModel->create($data);
            $this->jsonResponse(['id' => $id, 'message' => 'Category created.'], 201);
        } catch (Exception $e) {
            $this->jsonError('Failed to create category. Likely a duplicate slug.', 400);
        }
    }

    public function updateCategory(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->categoryModel->update((int)$id, $this->getPostData());
            $this->jsonResponse(['message' => 'Category updated.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to update category.', 400);
        }
    }

    public function destroyCategory(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->categoryModel->delete((int)$id);
            $this->jsonResponse(['message' => 'Category deleted.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to delete category. Ensure it has no products linked.', 400);
        }
    }

    // --- BRANDS ---
    public function listBrands(): void {
        AuthMiddleware::requireAdmin();
        $this->jsonResponse($this->brandModel->getAll());
    }

    public function storeBrand(): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData();
        try {
            $id = $this->brandModel->create($data);
            $this->jsonResponse(['id' => $id, 'message' => 'Brand created.'], 201);
        } catch (Exception $e) {
            $this->jsonError('Failed to create brand. Likely a duplicate slug.', 400);
        }
    }

    public function updateBrand(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->brandModel->update((int)$id, $this->getPostData());
            $this->jsonResponse(['message' => 'Brand updated.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to update brand.', 400);
        }
    }

    public function destroyBrand(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->brandModel->delete((int)$id);
            $this->jsonResponse(['message' => 'Brand deleted.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to delete brand. Ensure it has no products linked.', 400);
        }
    }

    // --- FAQS ---
    public function listFaqs(): void {
        AuthMiddleware::requireAdmin();
        $this->jsonResponse($this->faqModel->getAll(false));
    }

    public function storeFaq(): void {
        AuthMiddleware::requireAdmin();
        try {
            $id = $this->faqModel->create($this->getPostData());
            $this->jsonResponse(['id' => $id, 'message' => 'FAQ created.'], 201);
        } catch (Exception $e) {
            $this->jsonError('Failed to create FAQ.', 400);
        }
    }

    public function updateFaq(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->faqModel->update((int)$id, $this->getPostData());
            $this->jsonResponse(['message' => 'FAQ updated.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to update FAQ.', 400);
        }
    }

    public function destroyFaq(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->faqModel->delete((int)$id);
            $this->jsonResponse(['message' => 'FAQ deleted.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to delete FAQ.', 400);
        }
    }

    // --- COMMON: LOGO UPLOAD ---
    public function uploadBrandLogo(): void {
        AuthMiddleware::requireAdmin();
        if (empty($_FILES['image'])) $this->jsonError('No image.', 400);

        $url = ImageUploader::upload($_FILES['image'], 'brands');
        $this->jsonResponse(['url' => $url]);
    }
}
