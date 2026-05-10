<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ContactModel — CRUD for contacts table.
 * Extends BaseModel for singleton PDO connection.
 */
class ContactModel extends BaseModel {

    public function create($name, $email, $subject, $message) {
        $sql = "INSERT INTO contacts (customer_name, customer_email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, $subject, $message]);
    }

    public function getPaginated($page = 1, $limit = 10, $search = '', $status = '') {
        $where = "1=1";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (customer_name LIKE ? OR customer_email LIKE ? OR subject LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        $countSql = "SELECT COUNT(*) AS total FROM contacts WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM contacts WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) { $stmt->bindValue($i++, $p); }
        $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int)ceil($total / $limit) : 0,
        ];
    }

    public function getAll() {
        $sql = "SELECT * FROM contacts ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM contacts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $allowed = ['unread', 'read', 'replied'];
        if (!in_array($status, $allowed)) return false;
        $sql = "UPDATE contacts SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM contacts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function countByStatus(string $status): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM contacts WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM contacts');
        return (int) $stmt->fetchColumn();
    }
}
