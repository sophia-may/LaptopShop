<?php
require_once __DIR__ . '/../config/db.php';

/**
 * BaseModel — Abstract base for all models.
 * Provides PDO connection via Database singleton.
 * Each child model writes its own queries — no generic CRUD abstraction.
 */
abstract class BaseModel {
    public PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}
