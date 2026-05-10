<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * UserModel — Handles users table + role detection via admins/members tables.
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

    /**
     * Create a new user + member record (registration).
     * Returns the new user ID.
     */
    public function createMember(string $fullname, string $email, string $passwordHash, ?string $phone = null): int {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (fullname, email, phone, password_hash) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$fullname, $email, $phone, $passwordHash]);
            $userId = (int) $this->db->lastInsertId();

            // Insert into members with default tier (S-New, id=1)
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
}
