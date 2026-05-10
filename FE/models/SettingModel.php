<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * SettingModel — reads/writes from the `site_settings` MySQL table (key-value).
 * Extends BaseModel for singleton PDO connection.
 */
class SettingModel extends BaseModel {
    private $cache = null;

    /**
     * Load ALL settings into an associative array, grouped by prefix.
     * Falls back to JSON file on first run (migration helper).
     */
    private function loadAll() {
        if ($this->cache !== null) return $this->cache;

        $stmt = $this->db->query("SELECT `key`, `value` FROM site_settings ORDER BY `key`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If table is empty, try to seed from legacy JSON file
        if (empty($rows)) {
            $this->migrateFromJson();
            $stmt = $this->db->query("SELECT `key`, `value` FROM site_settings ORDER BY `key`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $grouped = [];
        foreach ($rows as $row) {
            $parts = explode('.', $row['key'], 2);
            if (count($parts) === 2) {
                $grouped[$parts[0]][$parts[1]] = $row['value'];
            } else {
                $grouped[$row['key']] = $row['value'];
            }
        }
        $this->cache = $grouped;
        return $grouped;
    }

    private function migrateFromJson() {
        $jsonPath = __DIR__ . '/../config/site_settings.json';
        if (!file_exists($jsonPath)) return;

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data) return;

        $stmt = $this->db->prepare("INSERT IGNORE INTO site_settings (`key`, `value`) VALUES (?, ?)");
        foreach ($data as $group => $entries) {
            if (is_array($entries)) {
                foreach ($entries as $k => $v) {
                    $stmt->execute(["$group.$k", (string)$v]);
                }
            } else {
                $stmt->execute([$group, (string)$entries]);
            }
        }
    }

    public function get($key, $default = '') {
        $all = $this->loadAll();
        $parts = explode('.', $key, 2);
        if (count($parts) === 2) {
            return $all[$parts[0]][$parts[1]] ?? $default;
        }
        return $all[$key] ?? $default;
    }

    public function getAll($group = null) {
        $all = $this->loadAll();
        if ($group) return $all[$group] ?? [];
        return $all;
    }

    public function getByGroup($group) {
        return $this->getAll($group);
    }

    public function set($key, $value) {
        $sql = "INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$key, (string)$value]);
        $this->cache = null;
        return $result;
    }

    public function updateMultiple($settings) {
        $sql = "INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)";
        $stmt = $this->db->prepare($sql);

        foreach ($settings as $rawKey => $value) {
            $dotKey = preg_replace('/^([a-z]+)_/', '$1.', $rawKey, 1);
            $stmt->execute([$dotKey, (string)$value]);
        }
        $this->cache = null;
        return true;
    }

    public function delete($key) {
        $stmt = $this->db->prepare("DELETE FROM site_settings WHERE `key` = ?");
        $this->cache = null;
        return $stmt->execute([$key]);
    }
}
