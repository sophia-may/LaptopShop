<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';

/**
 * AdminArticleController — CRUD for news and blog posts.
 * Supports WYSIWYG by excluding 'content' from global XSS sanitization.
 */
class AdminArticleController extends BaseController {

    private ArticleModel $model;

    public function __construct() {
        $this->model = new ArticleModel();
    }

    /**
     * GET /api/admin/articles
     * Admin: Paginated list of all articles with management filters.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = $this->pick($_GET, ['search', 'sort', 'dir']);

        $result = $this->model->adminGetPaginated($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * GET /api/admin/articles/:id
     * Admin: Get article detail (including unpublished).
     */
    public function show(string $id): void {
        AuthMiddleware::requireAdmin();
        $article = $this->model->findBySlug($id); // Custom: can be updated to findById if needed

        if (!$article) {
            $this->jsonError('Article not found.', 404);
        }

        $this->jsonResponse($article);
    }

    /**
     * POST /api/admin/articles
     * Admin: Create article.
     */
    public function store(): void {
        $admin = AuthMiddleware::requireAdmin();
        
        // 🎯 WYSIWYG Support: Exclude 'content' from XSS sanitization
        $data = $this->getPostData(['content']);

        $errors = $this->validate($data, [
            'title' => 'required',
            'slug'  => 'required',
            'content' => 'required'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        try {
            $data['admin_id'] = $admin['id'];
            $articleId = $this->model->create($data);
            $this->jsonResponse(['id' => $articleId, 'message' => 'Article created.'], 201);
        } catch (Exception $e) {
            $this->jsonError('Failed to create article. Likely a duplicate slug.', 400);
        }
    }

    /**
     * PUT /api/admin/articles/:id
     * Admin: Update article.
     */
    public function update(string $id): void {
        AuthMiddleware::requireAdmin();
        
        // WYSIWYG Support
        $data = $this->getPostData(['content']);

        try {
            $success = $this->model->update((int)$id, $data);
            if ($success) {
                $this->jsonResponse(['message' => 'Article updated.']);
            } else {
                $this->jsonError('Update failed.', 400);
            }
        } catch (Exception $e) {
            $this->jsonError('Database error during update.', 400);
        }
    }

    /**
     * DELETE /api/admin/articles/:id
     */
    public function destroy(string $id): void {
        AuthMiddleware::requireAdmin();
        try {
            $this->model->delete((int)$id);
            $this->jsonResponse(['message' => 'Article deleted.']);
        } catch (Exception $e) {
            $this->jsonError('Failed to delete article.', 400);
        }
    }

    /**
     * POST /api/admin/articles/upload
     * Admin: Upload article thumbnail.
     */
    public function uploadThumbnail(): void {
        AuthMiddleware::requireAdmin();

        if (empty($_FILES['image'])) {
            $this->jsonError('No image uploaded.', 400);
        }

        $url = ImageUploader::upload($_FILES['image'], 'articles');
        if ($url) {
            $this->jsonResponse(['url' => $url]);
        } else {
            $this->jsonError('Image upload failed.', 400);
        }
    }
}
