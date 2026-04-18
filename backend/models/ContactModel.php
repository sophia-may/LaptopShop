<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ContactModel — CRUD for contacts table.
 * Public users submit contacts. Admin manages them with status transitions.
 */
class ContactModel extends BaseModel {

    /**
     * Create a new contact submission.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO contacts (customer_name, customer_email, subject, message)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['customer_name'],
            $data['customer_email'],
            $data['subject'],
            $data['message'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get paginated contacts with optional search and status filter.
     */
    public function getPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        // Search by name, email, or subject
        if (!empty($filters['search'])) {
            $where .= ' AND (customer_name LIKE ? OR customer_email LIKE ? OR subject LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Filter by status
        if (!empty($filters['status']) && in_array($filters['status'], ['unread', 'read', 'replied'])) {
            $where .= ' AND status = ?';
            $params[] = $filters['status'];
        }

        // Count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM contacts WHERE $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch
        $sql = "SELECT * FROM contacts WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
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
     * Find a contact by ID.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Update contact status (unread → read → replied).
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE contacts SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    /**
     * Delete a contact.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM contacts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Count contacts by status (for dashboard badges).
     */
    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM contacts WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Count total contacts.
     */
    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM contacts');
        return (int) $stmt->fetchColumn();
    }
}
