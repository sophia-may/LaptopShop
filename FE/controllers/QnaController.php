<?php
require_once __DIR__ . '/../models/QnaModel.php';

class QnaController {
    private $model;
    
    // Getter function since $model is private type
    public function getModel() {
        return $this->model;
    }   

    public function __construct() {
        $this->model = new QnaModel();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case "list":       // fetch all Q&A
                $this->getAll();
                break;

            case "get":        // fetch single Q&A
                $this->getSingle();
                break;

            case "create":     // add new Q&A
                $this->add();
                break;

            case "update":     // edit existing Q&A
                $this->edit();
                break;

            case "delete":     // delete Q&A
                $this->delete();
                break;

            default:
                echo json_encode([
                    "success" => false,
                    "message" => "Invalid action"
                ]);
        }
    }

    // Handler functions
    
    public function getAll() {
        $data = $this->model->getAll();
        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
    }

    public function getSingle() {
        $id = $_GET['id'] ?? 0;
        $data = $this->model->getById($id);
        if ($data) {
            echo json_encode([
                "success" => true,
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Q&A not found"
            ]);
        }
    }

    public function add() {
        $question = $_POST['question'] ?? '';
        $answer   = $_POST['answer'] ?? '';

        if ($this->model->create($question, $answer)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add Q&A"]);
        }
    }

    public function edit() {
        $id       = $_POST['id'] ?? 0;
        $question = $_POST['question'] ?? '';
        $answer   = $_POST['answer'] ?? '';

        if ($this->model->update($id, $question, $answer)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update Q&A"]);
        }
    }

    public function delete() {
        $id = $_POST['id'] ?? 0;

        if ($this->model->delete($id)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete Q&A"]);
        }
    }
}

// Run controller for AJAX requests only
if (isset($_GET['action']) || isset($_POST['action'])) {
    $controller = new QnaController();
    $controller->handleRequest();
}

?>