<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CategoryModel.php';

/**
 * CategoryController — Public category list.
 *
 * Endpoints:
 *   GET /api/categories — List all categories with product counts
 */
class CategoryController extends BaseController {

    private CategoryModel $model;

    public function __construct() {
        $this->model = new CategoryModel();
    }

    /**
     * GET /api/categories
     * Public: list all categories.
     */
    public function index(): void {
        $categories = $this->model->getAll();
        $this->jsonResponse($categories);
    }
}
