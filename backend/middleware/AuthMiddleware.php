<?php
require_once __DIR__ . '/../helpers/JwtHelper.php';

/**
 * AuthMiddleware — JWT verification and role enforcement.
 *
 * Usage in controllers:
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

        // BẢN VÁ ZOMBIE TOKEN: Kiểm tra real-time is_active trong DB
        require_once __DIR__ . '/../config/database.php';
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
     * Verify JWT + role must be 'member'. Exits with 403 if wrong role.
     */
    public static function requireMember(): array {
        $payload = self::requireAuth();
        if (($payload['role'] ?? '') !== 'member') {
            self::sendError('Access denied. Member role required.', 403);
        }
        return $payload;
    }

    /**
     * Verify JWT + role must be 'admin'. Exits with 403 if wrong role.
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
     * Does NOT exit on failure. For endpoints that work both authenticated and anonymous.
     */
    public static function optionalAuth(): ?array {
        $token = JwtHelper::getBearerToken();
        if (!$token) {
            return null;
        }
        return JwtHelper::verify($token);
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
