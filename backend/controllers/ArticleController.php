<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ArticleModel.php';

/**
 * ArticleController — Public article endpoints.
 *
 * Endpoints:
 *   GET /api/articles       — Paginated published articles
 *   GET /api/articles/:slug — Article detail
 */
class ArticleController extends BaseController {

    private ArticleModel $model;

    public function __construct() {
        $this->model = new ArticleModel();
    }

    /**
     * GET /api/articles
     * Public: paginated published articles.
     */
    public function index(): void {
        // BẢN VÁ PHÂN TRANG
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));

        $result = $this->model->getPaginated($page, $limit);
        $this->jsonResponse($result);
    }

    /**
     * GET /api/articles/:slug
     * Public: article detail by slug.
     */
    public function show(string $slug): void {
        $article = $this->model->findBySlug($slug);
        if (!$article) {
            $this->jsonError('Article not found.', 404);
        }
        $this->jsonResponse($article);
    }
}
