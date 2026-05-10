<?php
require_once __DIR__ . '/BaseModel.php';

class FaqModel extends BaseModel {
    public function getAll(bool $onlyActive = true): array {
        $sql = 'SELECT * FROM faqs';
        if ($onlyActive) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY sort_order ASC, created_at DESC';
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO faqs (question, answer, sort_order, is_active) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['question'],
            $data['answer'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE faqs SET question = ?, answer = ?, sort_order = ?, is_active = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['question'],
            $data['answer'],
            $data['sort_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM faqs WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
