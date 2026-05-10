<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/ImageUploader.php';

/**
 * AuthController — Authentication, profile, password, avatar endpoints.
 *
 * Endpoints:
 *   POST /api/auth/register       — Register new member
 *   POST /api/auth/login          — Login member
 *   POST /api/auth/admin/login    — Login admin
 *   GET  /api/auth/me             — Get current user info
 *   PUT  /api/auth/profile        — Update profile (fullname, phone)
 *   PUT  /api/auth/password       — Change password
 *   POST /api/auth/avatar         — Upload avatar
 */
class AuthController extends BaseController {

    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    /**
     * POST /api/auth/register
     * Register a new member account.
     */
    public function register(): void {
        $data = $this->getPostData(['password', 'password_confirmation']);

        $errors = $this->validate($data, [
            'fullname' => 'required|min:2|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:6|max:255',
        ]);

        // Optional phone validation
        if (!empty($data['phone'])) {
            $phoneErrors = $this->validate($data, ['phone' => 'phone']);
            $errors = array_merge($errors, $phoneErrors);
        }

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        // Check unique email
        if ($this->userModel->emailExists($data['email'])) {
            $this->jsonError('Email is already registered.', 409);
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $userId = $this->userModel->createMember(
                $data['fullname'],
                $data['email'],
                $hash,
                $data['phone'] ?? null
            );
            $this->jsonResponse(['id' => $userId, 'message' => 'Registration successful.'], 201);
        } catch (\Exception $e) {
            $this->jsonError('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/auth/login
     * Login as member. Returns JWT token + user info.
     */
    public function login(): void {
        $data = $this->getPostData(['password']);

        $errors = $this->validate($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $user = $this->userModel->findByEmail($data['email']);

        // Verify user exists, is a member, password matches
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            $this->jsonError('Invalid email or password.', 401);
        }

        // Check if user is actually a member
        $role = $this->userModel->detectRole($user['id']);
        if ($role !== 'member') {
            $this->jsonError('Invalid email or password.', 401);
        }

        // Check is_active — locked accounts cannot login
        if (!$user['is_active']) {
            $this->jsonError('Your account has been locked. Please contact admin.', 403);
        }

        // Generate JWT
        $token = JwtHelper::generate(['id' => $user['id'], 'role' => 'member']);

        // BẢN VÁ LEAKAGE: Luôn xóa hash trước khi trả về
        unset($user['password_hash']);
        $user['role'] = 'member';

        $this->jsonResponse(['token' => $token, 'user' => $user]);
    }

    /**
     * POST /api/auth/admin/login
     * Login as admin. Returns JWT token + admin info.
     */
    public function loginAdmin(): void {
        $data = $this->getPostData(['password']);

        $errors = $this->validate($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            $this->jsonError('Invalid email or password.', 401);
        }

        // Check if user is actually admin
        $role = $this->userModel->detectRole($user['id']);
        if ($role !== 'admin') {
            $this->jsonError('Invalid email or password.', 401);
        }

        // Check is_active
        if (!$user['is_active']) {
            $this->jsonError('Admin account has been locked. Please contact support.', 403);
        }

        $token = JwtHelper::generate(['id' => $user['id'], 'role' => 'admin']);

        // BẢN VÁ LEAKAGE: Luôn xóa hash trước khi trả về
        unset($user['password_hash']);
        $user['role'] = 'admin';

        $this->jsonResponse(['token' => $token, 'user' => $user]);
    }

    /**
     * GET /api/auth/me
     * Get current user info from JWT token.
     */
    public function me(): void {
        $payload = AuthMiddleware::requireAuth();

        $user = $this->userModel->findById($payload['id']);
        if (!$user) {
            $this->jsonError('User not found.', 404);
        }

        // BẢN VÁ LEAKAGE
        unset($user['password_hash']);

        $this->jsonResponse($user);
    }

    /**
     * PUT /api/auth/profile
     * Update fullname and phone for current user.
     */
    public function updateProfile(): void {
        $payload = AuthMiddleware::requireAuth();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'fullname' => 'required|min:2|max:100',
        ]);

        if (!empty($data['phone'])) {
            $phoneErrors = $this->validate($data, ['phone' => 'phone']);
            $errors = array_merge($errors, $phoneErrors);
        }

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $this->userModel->updateProfile(
            $payload['id'],
            $data['fullname'],
            $data['phone'] ?? null
        );

        $user = $this->userModel->findById($payload['id']);
        // BẢN VÁ LEAKAGE
        unset($user['password_hash']);

        $this->jsonResponse($user);
    }

    /**
     * PUT /api/auth/password
     * Change password. Must verify old password first.
     */
    public function changePassword(): void {
        $payload = AuthMiddleware::requireAuth();
        $data = $this->getPostData(['old_password', 'new_password', 'confirm_password']);

        $errors = $this->validate($data, [
            'old_password'     => 'required',
            'new_password'     => 'required|min:6|max:255',
            'confirm_password' => 'required',
        ]);
        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        // Check confirm matches
        if ($data['new_password'] !== $data['confirm_password']) {
            $this->jsonError('New password and confirmation do not match.', 422);
        }

        // Verify old password
        $currentHash = $this->userModel->getPasswordHash($payload['id']);
        if (!$currentHash || !password_verify($data['old_password'], $currentHash)) {
            $this->jsonError('Current password is incorrect.', 401);
        }

        // Update
        $newHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
        $this->userModel->updatePassword($payload['id'], $newHash);

        $this->jsonResponse(['message' => 'Password changed successfully.']);
    }

    /**
     * POST /api/auth/avatar
     * Upload avatar image for current user.
     */
    public function uploadAvatar(): void {
        $payload = AuthMiddleware::requireAuth();

        if (empty($_FILES['avatar'])) {
            $this->jsonError('No avatar file uploaded.', 400);
        }

        $path = ImageUploader::upload($_FILES['avatar'], 'avatars');
        if (!$path) {
            $this->jsonError('Upload failed. Check file type (jpg/png/webp/gif) and size (max 5MB).', 400);
        }

        // Delete old avatar if exists
        $user = $this->userModel->findById($payload['id']);
        if ($user && !empty($user['avatar_url'])) {
            ImageUploader::delete($user['avatar_url']);
        }

        $this->userModel->updateAvatar($payload['id'], $path);

        $this->jsonResponse(['avatar_url' => $path]);
    }
}
