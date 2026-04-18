<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ContactModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * ContactController — Public contact submission + Admin contact management.
 *
 * Endpoints:
 *   POST   /api/contacts                     — Public: submit contact form
 *   GET    /api/admin/contacts               — Admin: paginated list
 *   GET    /api/admin/contacts/:id           — Admin: detail view
 *   PUT    /api/admin/contacts/:id/status    — Admin: update status
 *   DELETE /api/admin/contacts/:id           — Admin: delete
 */
class ContactController extends BaseController {

    private ContactModel $model;

    public function __construct() {
        $this->model = new ContactModel();
    }

    /**
     * POST /api/contacts
     * Public: submit a contact form. No auth required.
     */
    public function store(): void {
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'customer_name'  => 'required|min:2|max:100',
            'customer_email' => 'required|email',
            'subject'        => 'required|min:2|max:255',
            'message'        => 'required|min:10',
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        // Data is already sanitized by getPostData() — pass directly
        $id = $this->model->create([
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'subject'        => $data['subject'],
            'message'        => $data['message'],
        ]);
        $this->jsonResponse([
            'id'      => $id,
            'message' => 'Contact submitted successfully. We will reply soon.',
        ], 201);
    }

    /**
     * GET /api/admin/contacts
     * Admin: paginated contact list with search and status filter.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        // BẢN VÁ PHÂN TRANG
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];

        $result = $this->model->getPaginated($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * GET /api/admin/contacts/:id
     * Admin: view contact detail.
     */
    public function show(string $id): void {
        AuthMiddleware::requireAdmin();

        $contact = $this->model->findById((int) $id);
        if (!$contact) {
            $this->jsonError('Contact not found.', 404);
        }

        $this->jsonResponse($contact);
    }

    /**
     * PUT /api/admin/contacts/:id/status
     * Admin: update contact status. Valid transitions: unread → read → replied.
     */
    public function updateStatus(string $id): void {
        AuthMiddleware::requireAdmin();

        $data = $this->getPostData();
        $status = $data['status'] ?? '';

        if (!in_array($status, ['unread', 'read', 'replied'])) {
            $this->jsonError('Invalid status. Must be one of: unread, read, replied.', 422);
        }

        $contact = $this->model->findById((int) $id);
        if (!$contact) {
            $this->jsonError('Contact not found.', 404);
        }

        $this->model->updateStatus((int) $id, $status);

        $this->jsonResponse([
            'id'      => (int) $id,
            'status'  => $status,
            'message' => 'Contact status updated.',
        ]);
    }

    /**
     * DELETE /api/admin/contacts/:id
     * Admin: delete a contact.
     */
    public function destroy(string $id): void {
        AuthMiddleware::requireAdmin();

        $contact = $this->model->findById((int) $id);
        if (!$contact) {
            $this->jsonError('Contact not found.', 404);
        }

        $this->model->delete((int) $id);
        $this->jsonResponse(['message' => 'Contact deleted successfully.']);
    }
}
