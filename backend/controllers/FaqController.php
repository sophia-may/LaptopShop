<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/FaqModel.php';

/**
 * FaqController — Public FAQ list.
 *
 * Endpoints:
 *   GET /api/faqs — List active FAQs
 */
class FaqController extends BaseController {

    private FaqModel $model;

    public function __construct() {
        $this->model = new FaqModel();
    }

    /**
     * GET /api/faqs
     * Public: list all active FAQs.
     */
    public function index(): void {
        $faqs = $this->model->getActive();
        $this->jsonResponse($faqs);
    }
}
