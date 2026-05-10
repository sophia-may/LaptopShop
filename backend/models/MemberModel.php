<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * MemberModel — Admin-facing queries for managing member accounts.
 * JOINs users + members + membership_tiers tables.
 */
class MemberModel extends BaseModel {

    /**
     * Get paginated list of members with optional search and status filter.
     *
     * @param int   $page    Current page (1-indexed)
     * @param int   $limit   Items per page
     * @param array $filters ['search' => '', 'status' => 'active|locked']
     */
    public function getPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        // Search by name or email
        if (!empty($filters['search'])) {
            $where .= ' AND (u.fullname LIKE ? OR u.email LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        // Filter by active status
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $where .= ' AND u.is_active = 1';
            } elseif ($filters['status'] === 'locked') {
                $where .= ' AND u.is_active = 0';
            }
        }

        // Safe Dynamic Sorting (Whitelist)
        $allowedSort = ['id', 'fullname', 'email', 'points', 'created_at', 'updated_at'];
        $sortBy = in_array($filters['sort'] ?? '', $allowedSort) ? $filters['sort'] : 'created_at';
        $direction = strtoupper($filters['dir'] ?? '') === 'ASC' ? 'ASC' : 'DESC';

        // Add table prefix to skip ambiguity errors
        $sortColumn = ($sortBy === 'points') ? "m.$sortBy" : "u.$sortBy";

        // Count total
        $countSql = "SELECT COUNT(*) FROM users u JOIN members m ON u.id = m.user_id WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch paginated list
        $sql = "SELECT u.id, u.fullname, u.email, u.phone, u.avatar_url, u.is_active,
                       u.created_at, u.updated_at,
                       m.points, m.tier_id, t.name as tier_name, t.discount_percent
                FROM users u
                JOIN members m ON u.id = m.user_id
                LEFT JOIN membership_tiers t ON m.tier_id = t.id
                WHERE $where
                ORDER BY $sortColumn $direction
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        // Bind parameters
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
        ];
    }

    /**
     * Get detailed member info by user ID.
     */
    public function getDetail(int $userId): ?array {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.fullname, u.email, u.phone, u.avatar_url, u.is_active,
                    u.created_at, u.updated_at,
                    m.points, m.tier_id, t.name as tier_name, t.discount_percent
             FROM users u
             JOIN members m ON u.id = m.user_id
             LEFT JOIN membership_tiers t ON m.tier_id = t.id
             WHERE u.id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Toggle user active status (lock/unlock).
     */
    public function toggleActive(int $userId): bool {
        $stmt = $this->db->prepare(
            'UPDATE users SET is_active = NOT is_active WHERE id = ?'
        );
        return $stmt->execute([$userId]);
    }

    /**
     * Get current is_active value.
     */
    public function getActiveStatus(int $userId): ?bool {
        $stmt = $this->db->prepare('SELECT is_active FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (bool) $row['is_active'] : null;
    }

    /**
     * Set password for a user (admin reset).
     */
    public function setPassword(int $userId, string $hash): bool {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        return $stmt->execute([$hash, $userId]);
    }

    /**
     * Delete a user completely (cascades to members via FK).
     */
    public function delete(int $userId): bool {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$userId]);
    }

    /**
     * Check if a user is a member (not admin).
     */
    public function isMember(int $userId): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM members WHERE user_id = ?');
        $stmt->execute([$userId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Count total members (for dashboard).
     */
    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM members');
        return (int) $stmt->fetchColumn();
    }
}
