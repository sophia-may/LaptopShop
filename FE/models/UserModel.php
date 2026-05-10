<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * UserModel — Handles users table + role detection via admins/members tables.
 * Ported from /backend with additional session-compat methods for FE monolith.
 */
class UserModel extends BaseModel {

    /**
     * Find user by email. Returns row with password_hash (for login verification).
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Find user by ID with role detection and member tier info.
     * Does NOT include password_hash.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.fullname, u.email, u.phone, u.avatar_url, u.is_active,
                    u.created_at, u.updated_at
             FROM users u WHERE u.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return null;

        // Detect role
        $user['role'] = $this->detectRole($id);

        // If member, attach tier info
        if ($user['role'] === 'member') {
            $stmt = $this->db->prepare(
                'SELECT m.points, m.tier_id, t.name as tier_name, t.discount_percent
                 FROM members m
                 LEFT JOIN membership_tiers t ON m.tier_id = t.id
                 WHERE m.user_id = ?'
            );
            $stmt->execute([$id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($member) {
                $user['points'] = (int) $member['points'];
                $user['tier_id'] = $member['tier_id'] ? (int) $member['tier_id'] : null;
                $user['tier_name'] = $member['tier_name'];
                $user['discount_percent'] = $member['discount_percent'];
            }
        }

        return $user;
    }

    /**
     * Check if user is admin, member, or unknown.
     */
    public function detectRole(int $userId): string {
        $stmt = $this->db->prepare('SELECT 1 FROM admins WHERE user_id = ?');
        $stmt->execute([$userId]);
        if ($stmt->fetch()) return 'admin';

        $stmt = $this->db->prepare('SELECT 1 FROM members WHERE user_id = ?');
        $stmt->execute([$userId]);
        if ($stmt->fetch()) return 'member';

        return 'unknown';
    }

    /**
     * Check if email is already registered.
     */
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    // Backward compat alias
    public function userExists($email) {
        return $this->emailExists($email);
    }

    /**
     * Create a new user + member record (registration).
     * Uses transaction for data integrity.
     */
    public function createMember(string $fullname, string $email, string $passwordHash, ?string $phone = null): int {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (fullname, email, phone, password_hash) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$fullname, $email, $phone, $passwordHash]);
            $userId = (int) $this->db->lastInsertId();

            // Insert into members with default tier
            $stmt = $this->db->prepare(
                'INSERT INTO members (user_id, tier_id, points) VALUES (?, 1, 0)'
            );
            $stmt->execute([$userId]);

            $this->db->commit();
            return $userId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Legacy compat: createUser (used by old AuthController)
     */
    public function createUser($email, $password, $full_name = null, $is_admin = 0) {
        if (empty($full_name)) {
            $parts = explode('@', $email);
            $full_name = $parts[0] ?? 'User';
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (email, fullname, password_hash, is_active) VALUES (?, ?, ?, 1)"
            );
            $stmt->execute([$email, $full_name, $hash]);
            $userId = (int) $this->db->lastInsertId();

            if ($is_admin) {
                $stmtAdmin = $this->db->prepare("INSERT INTO admins (user_id) VALUES (?)");
                $stmtAdmin->execute([$userId]);
            } else {
                $stmtMember = $this->db->prepare("INSERT INTO members (user_id, tier_id, points) VALUES (?, 1, 0)");
                $stmtMember->execute([$userId]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Authenticate user — used by FE session-based login.
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Add compat keys for FE controllers
            $user['username'] = $user['email'];
            $user['is_admin'] = ($this->detectRole($user['id']) === 'admin') ? 1 : 0;
            return $user;
        }
        return false;
    }

    /**
     * Check if user is admin (legacy compat).
     */
    public function isAdmin($userId) {
        if (!is_numeric($userId)) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$userId]);
            $userId = $stmt->fetchColumn();
        }
        if (!$userId) return false;
        return $this->detectRole((int)$userId) === 'admin';
    }

    /**
     * Update user profile fields.
     */
    public function updateProfile(int $id, string $fullname, ?string $phone): bool {
        $stmt = $this->db->prepare(
            'UPDATE users SET fullname = ?, phone = ? WHERE id = ?'
        );
        return $stmt->execute([$fullname, $phone, $id]);
    }

    /**
     * Update user password hash.
     */
    public function updatePassword(int $id, string $hash): bool {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Update user avatar path.
     */
    public function updateAvatar(int $id, string $path): bool {
        $stmt = $this->db->prepare('UPDATE users SET avatar_url = ? WHERE id = ?');
        return $stmt->execute([$path, $id]);
    }

    /**
     * Get password hash for a user (for password change verification).
     */
    public function getPasswordHash(int $id): ?string {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['password_hash'] : null;
    }

    /**
     * Admin: Get paginated user list with search and role filter.
     */
    public function getPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $where .= ' AND (u.fullname LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        if (!empty($filters['role'])) {
            if ($filters['role'] === 'admin') {
                $where .= ' AND EXISTS (SELECT 1 FROM admins WHERE user_id = u.id)';
            } elseif ($filters['role'] === 'member') {
                $where .= ' AND EXISTS (SELECT 1 FROM members WHERE user_id = u.id)';
            }
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where .= ' AND u.is_active = ?';
            $params[] = (int)$filters['is_active'];
        }

        // Count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users u WHERE $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch
        $sql = "SELECT u.id, u.fullname, u.email, u.phone, u.avatar_url, u.is_active, u.created_at 
                FROM users u WHERE $where ORDER BY u.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) { $stmt->bindValue($i++, $p); }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Attach role to each user
        foreach ($items as &$u) {
            $u['role'] = $this->detectRole($u['id']);
        }

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
        ];
    }

    /**
     * Admin: Lock/Unlock user account.
     */
    public function setActive(int $id, bool $active): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        return $stmt->execute([$active ? 1 : 0, $id]);
    }

    /**
     * Admin: Reset user password (generates random password).
     */
    public function resetPassword(int $id): string {
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->updatePassword($id, $hash);
        return $newPassword;
    }
}
