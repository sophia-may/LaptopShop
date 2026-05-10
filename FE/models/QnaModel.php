<?php
require_once __DIR__ . '/../config/db.php';

class QnaModel {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Get all Q&A items
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM faqs ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    // Get single Q&A
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Create new Q&A
    public function create($question, $answer) {
        $stmt = $this->conn->prepare("INSERT INTO faqs (question, answer) VALUES (?, ?)");
        return $stmt->execute([$question, $answer]);
    }

    // Update Q&A
    public function update($id, $question, $answer) {
        $stmt = $this->conn->prepare("UPDATE faqs SET question = ?, answer = ? WHERE id = ?");
        return $stmt->execute([$question, $answer, $id]);
    }

    // Delete Q&A
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM faqs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
