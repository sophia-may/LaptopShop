<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/BrandModel.php';

/**
 * BrandController — Public brand list.
 *
 * Endpoints:
 *   GET /api/brands — List all brands
 */
class BrandController extends BaseController {

    private BrandModel $model;

    public function __construct() {
        $this->model = new BrandModel();
    }

    /**
     * GET /api/brands
     * Public: list all brands.
     */
    public function index(): void {
        $brands = $this->model->getAll();
        $this->jsonResponse($brands);
    }
}
