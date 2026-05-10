<?php
// FE/config/db.php — Unified database connection
// Provides both: Database singleton (pro) + legacy $conn (MySQLi) for backward compat

require_once __DIR__ . '/constants.php';

// --- Singleton PDO (used by all new models via BaseModel) ---
class Database {
    private static $instance = null;
    private $pdo;

    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Singleton getter — used by BaseModel and all pro models.
     */
    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }

    /**
     * Legacy compatibility: new Database() then ->connect()
     * Kept so old code that calls $db = new Database(); $pdo = $db->connect(); still works.
     */
    public function connect() {
        return self::getConnection();
    }
}

// --- Legacy MySQLi (for login_signup.php backward compat) ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("MySQLi Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");