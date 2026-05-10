<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SiteSettingsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';

/**
 * SiteSettingsController — Public settings read + Admin CRUD.
 *
 * Endpoints:
 *   GET  /api/site-settings/public          — Public: phone, email, address, logo, socials
 *   GET  /api/admin/site-settings           — Admin: all settings
 *   PUT  /api/admin/site-settings/:key      — Admin: upsert one key-value
 *   POST /api/admin/site-settings/upload    — Admin: upload image, save path as value
 */
class SiteSettingsController extends BaseController {

    private SiteSettingsModel $model;

    /**
     * Keys that are safe to expose publicly (no auth required).
     */
    private array $publicKeys = [
        'company_name', 'phone', 'email', 'address', 'working_hours',
        'logo', 'favicon',
        'homepage_intro_title', 'homepage_intro_text',
        'homepage_banner_1', 'homepage_banner_2',
        'social_facebook', 'social_youtube', 'zalo_phone',
    ];

    public function __construct() {
        $this->model = new SiteSettingsModel();
    }

    /**
     * GET /api/site-settings/public
     * Returns only public-facing settings. No auth required.
     */
    public function publicSettings(): void {
        $settings = $this->model->getByKeys($this->publicKeys);
        $this->jsonResponse($settings);
    }

    /**
     * GET /api/admin/site-settings
     * Returns all settings. Admin only.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();
        $settings = $this->model->getAll();
        $this->jsonResponse($settings);
    }

    /**
     * PUT /api/admin/site-settings/:key
     * Update (or create) a single setting value. Admin only.
     */
    public function update(string $key): void {
        AuthMiddleware::requireAdmin();

        $data = $this->getPostData(['value']);
        $value = $data['value'] ?? '';

        if ($key === '') {
            $this->jsonError('Setting key is required.', 400);
        }

        $this->model->upsert($key, $value);
        $this->jsonResponse(['key' => $key, 'value' => $value]);
    }

    /**
     * POST /api/admin/site-settings/upload
     * Upload an image file and store its path as a setting value. Admin only.
     *
     * Expects multipart form:
     *   file — the image file
     *   key  — the setting key to update
     */
    public function uploadImage(): void {
        AuthMiddleware::requireAdmin();

        if (empty($_FILES['file'])) {
            $this->jsonError('No file uploaded.', 400);
        }

        $path = ImageUploader::upload($_FILES['file'], 'settings');
        if (!$path) {
            $this->jsonError('Upload failed. Check file type (jpg/png/webp/gif) and size (max 5MB).', 400);
        }

        // If a key is provided, save the path as that setting's value
        $key = $_POST['key'] ?? '';
        if ($key !== '') {
            // Delete old image if setting already has a value
            $oldValue = $this->model->getByKey($key);
            if ($oldValue && strpos($oldValue, 'uploads/') === 0) {
                ImageUploader::delete($oldValue);
            }
            $this->model->upsert($key, $path);
        }

        $this->jsonResponse(['path' => $path, 'key' => $key]);
    }
}
