<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * SiteSettingsModel — Key-value store for site configuration.
 * Table: site_settings (key VARCHAR PK, value TEXT)
 */
class SiteSettingsModel extends BaseModel {

    /**
     * Get all settings as array of {key, value, updated_at}.
     */
    public function getAll(): array {
        $stmt = $this->db->query('SELECT `key`, `value`, updated_at FROM site_settings ORDER BY `key`');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get settings filtered by specific keys.
     * Returns associative array: key => value.
     */
    public function getByKeys(array $keys): array {
        if (empty($keys)) return [];

        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $stmt = $this->db->prepare(
            "SELECT `key`, `value` FROM site_settings WHERE `key` IN ($placeholders)"
        );
        $stmt->execute($keys);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * Get a single setting value by key.
     */
    public function getByKey(string $key): ?string {
        $stmt = $this->db->prepare('SELECT `value` FROM site_settings WHERE `key` = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['value'] : null;
    }

    /**
     * Insert or update a setting.
     * Uses INSERT ... ON DUPLICATE KEY UPDATE for atomic upsert.
     */
    public function upsert(string $key, string $value): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        return $stmt->execute([$key, $value]);
    }
}
