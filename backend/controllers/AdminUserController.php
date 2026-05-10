<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/MemberModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * AdminUserController — Admin endpoints for managing member accounts.
 *
 * All endpoints require admin authentication.
 *
 * Endpoints:
 *   GET    /api/admin/members                  — List with search/filter/pagination
 *   GET    /api/admin/members/:id              — Detail view
 *   PUT    /api/admin/members/:id/lock         — Toggle lock/unlock
 *   PUT    /api/admin/members/:id/reset-password — Reset to random password
 *   DELETE /api/admin/members/:id              — Delete member
 */
class AdminUserController extends BaseController {

    private MemberModel $memberModel;

    public function __construct() {
        $this->memberModel = new MemberModel();
    }

    /**
     * GET /api/admin/members
     * Paginated member list with search and status filter.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'sort'   => $_GET['sort'] ?? 'created_at',
            'dir'    => $_GET['dir'] ?? 'DESC',
        ];

        $result = $this->memberModel->getPaginated($page, $limit, $filters);

        // BẢN VÁ LEAKAGE: Duyệt mảng và xóa hash (nếu có)
        foreach ($result['items'] as &$m) {
            unset($m['password_hash']);
        }

        $this->jsonResponse($result);
    }

    /**
     * GET /api/admin/members/:id
     * Get detailed member info.
     */
    public function show(string $id): void {
        AuthMiddleware::requireAdmin();

        $userId = (int) $id;
        $member = $this->memberModel->getDetail($userId);

        if (!$member) {
            $this->jsonError('Member not found.', 404);
        }

        // BẢN VÁ LEAKAGE
        unset($member['password_hash']);

        $this->jsonResponse($member);
    }

    /**
     * PUT /api/admin/members/:id/lock
     * Toggle member lock/unlock status.
     */
    public function toggleLock(string $id): void {
        AuthMiddleware::requireAdmin();

        $userId = (int) $id;

        // Verify user is a member
        if (!$this->memberModel->isMember($userId)) {
            $this->jsonError('Member not found.', 404);
        }

        $this->memberModel->toggleActive($userId);
        $newStatus = $this->memberModel->getActiveStatus($userId);

        $this->jsonResponse([
            'id'        => $userId,
            'is_active' => $newStatus,
            'message'   => $newStatus ? 'Member has been unlocked.' : 'Member has been locked.',
        ]);
    }

    /**
     * PUT /api/admin/members/:id/reset-password
     * Generate random password, hash, save, return plaintext once.
     */
    public function resetPassword(string $id): void {
        AuthMiddleware::requireAdmin();

        $userId = (int) $id;

        if (!$this->memberModel->isMember($userId)) {
            $this->jsonError('Member not found.', 404);
        }

        // Generate random password: 10 chars, alphanumeric + special
        $newPassword = $this->generateRandomPassword(10);
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->memberModel->setPassword($userId, $hash);

        $this->jsonResponse([
            'id'           => $userId,
            'new_password' => $newPassword,
            'message'      => 'Password has been reset. Please share this password with the member securely.',
        ]);
    }

    /**
     * DELETE /api/admin/members/:id
     * Delete a member account.
     */
    public function destroy(string $id): void {
        AuthMiddleware::requireAdmin();

        $userId = (int) $id;

        if (!$this->memberModel->isMember($userId)) {
            $this->jsonError('Member not found.', 404);
        }

        $this->memberModel->delete($userId);
        $this->jsonResponse(['message' => 'Member deleted successfully.']);
    }

    /**
     * Generate a random password with letters, digits, and special chars.
     */
    private function generateRandomPassword(int $length = 10): string {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $password = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        return $password;
    }
}
