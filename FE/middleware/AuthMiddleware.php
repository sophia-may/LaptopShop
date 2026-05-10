<?php
require_once __DIR__ . '/../helpers/JwtHelper.php';

/**
 * AuthMiddleware — JWT verification and role enforcement.
 * Ported from /backend. Also supports session-based auth for FE monolith pages.
 *
 * Usage:
 *   $payload = AuthMiddleware::requireAuth();   // any authenticated user
 *   $payload = AuthMiddleware::requireMember(); // member role only
 *   $payload = AuthMiddleware::requireAdmin();  // admin role only
 *   $payload = AuthMiddleware::optionalAuth();  // returns null if no token
 */
class AuthMiddleware {

    /**
     * Verify JWT token. Returns payload on success, exits with 401 on failure.
     */
    public static function requireAuth(): array {
        $token = JwtHelper::getBearerToken();
        if (!$token) {
            self::sendError('Authentication required. No token provided.', 401);
        }

        $payload = JwtHelper::verify($token);
        if (!$payload) {
            self::sendError('Token invalid or expired.', 401);
        }

        // Zombie token check: verify is_active in DB in real-time
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$payload['id']]);
        $isActive = $stmt->fetchColumn();

        if (!$isActive) {
            self::sendError('Your account has been locked by administrator.', 403);
        }

        return $payload;
    }

    /**
     * Verify JWT + role must be 'member'.
     */
    public static function requireMember(): array {
        $payload = self::requireAuth();
        if (($payload['role'] ?? '') !== 'member') {
            self::sendError('Access denied. Member role required.', 403);
        }
        return $payload;
    }

    /**
     * Verify JWT + role must be 'admin'.
     */
    public static function requireAdmin(): array {
        $payload = self::requireAuth();
        if (($payload['role'] ?? '') !== 'admin') {
            self::sendError('Access denied. Admin role required.', 403);
        }
        return $payload;
    }

    /**
     * Optional authentication — returns payload or null.
     */
    public static function optionalAuth(): ?array {
        $token = JwtHelper::getBearerToken();
        if (!$token) {
            return null;
        }
        return JwtHelper::verify($token);
    }

    /**
     * Session-based auth check — for FE monolith pages that render views.
     * This is the FE-specific bridge: checks $_SESSION for logged-in state.
     */
    public static function requireSessionAuth(bool $requireAdmin = false): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login_signup');
            exit();
        }

        if ($requireAdmin && (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1)) {
            header('Location: index.php?page=home');
            exit();
        }
    }

    /**
     * Send JSON error and exit.
     */
    private static function sendError(string $message, int $status): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $message]);
        exit();
    }
}
