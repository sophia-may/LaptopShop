<?php
require_once __DIR__ . '/../models/ContactModel.php';

class ContactController {

    private $model;

    public function __construct() {
        $this->model = new ContactModel();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case "create":
                $this->create();
                break;

            case "list":
                $this->getList();
                break;

            case "get":
                $this->getSingle();
                break;

            case "updateStatus":
                $this->updateStatus();
                break;

            case "delete":
                $this->delete();
                break;

            default:
                echo json_encode([
                    "success" => false,
                    "message" => "Invalid action"
                ]);
        }
    }

    // ====== PUBLIC: Submit contact form ======
    private function create() {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Server-side validation
        $errors = [];
        if (empty($name) || mb_strlen($name) < 2)        $errors[] = "Name must be at least 2 characters.";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
        if (empty($message) || mb_strlen($message) < 10)  $errors[] = "Message must be at least 10 characters.";

        if (!empty($errors)) {
            echo json_encode(["success" => false, "message" => implode(' ', $errors)]);
            return;
        }

        if ($this->model->create($name, $email, $subject, $message)) {
            echo json_encode(["success" => true, "message" => "Message sent successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to submit contact."]);
        }
    }

    // ====== ADMIN: Paginated list ======
    private function getList() {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $result = $this->model->getPaginated($page, $limit, $search, $status);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // ====== ADMIN: Get single ======
    private function getSingle() {
        $id = $_GET['id'] ?? 0;
        $data = $this->model->getById($id);

        if ($data) {
            echo json_encode(["success" => true, "data" => $data]);
        } else {
            echo json_encode(["success" => false, "message" => "Contact not found"]);
        }
    }

    // ====== ADMIN: Update status ======
    private function updateStatus() {
        $id     = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        if ($this->model->updateStatus($id, $status)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update status"]);
        }
    }

    // ====== ADMIN: Delete ======
    private function delete() {
        $id = $_POST['id'] ?? 0;

        if ($this->model->delete($id)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete"]);
        }
    }
}

$controller = new ContactController();
$controller->handleRequest();
