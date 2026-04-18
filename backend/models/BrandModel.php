<?php
require_once __DIR__ . '/BaseModel.php';

class BrandModel extends BaseModel {
    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM brands ORDER BY name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('INSERT INTO brands (name, slug, logo_url) VALUES (?, ?, ?)');
        $stmt->execute([$data['name'], $data['slug'], $data['logo_url'] ?? null]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare('UPDATE brands SET name = ?, slug = ?, logo_url = ? WHERE id = ?');
        return $stmt->execute([$data['name'], $data['slug'], $data['logo_url'] ?? null, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM brands WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
