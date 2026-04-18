<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('INSERT INTO categories (name, slug, is_featured) VALUES (?, ?, ?)');
        $stmt->execute([$data['name'], $data['slug'], $data['is_featured'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, slug = ?, is_featured = ? WHERE id = ?');
        return $stmt->execute([$data['name'], $data['slug'], $data['is_featured'] ?? 0, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
