<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * OrderController — API endpoints for orders (User & Admin).
 */
class OrderController extends BaseController {

    private OrderModel $model;

    public function __construct() {
        $this->model = new OrderModel();
    }

    /**
     * POST /api/orders/checkout
     * Create order from current cart.
     * Payload: {"shipping_address": "...", "payment_method": "cod"}
     */
    public function checkout(): void {
        $user = AuthMiddleware::requireMember();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'shipping_address' => 'required|min:10',
            'payment_method'   => 'required|in:cod,credit_card'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $result = $this->model->checkout(
            $user['id'], 
            $data['shipping_address'], 
            $data['payment_method']
        );

        if ($result['success']) {
            $this->jsonResponse([
                'message'    => 'Order placed successfully.',
                'order_code' => $result['order_code'],
                'total'      => $result['total']
            ], 201);
        } else {
            $this->jsonError($result['message'] ?? 'Checkout failed.', 400);
        }
    }

    /**
     * GET /api/orders
     * User order history.
     */
    public function index(): void {
        $user = AuthMiddleware::requireMember();
        $orders = $this->model->getUserOrders($user['id']);
        $this->jsonResponse(['items' => $orders]);
    }

    /**
     * GET /api/orders/:code
     * Order detail view for user.
     */
    public function show(string $code): void {
        $user = AuthMiddleware::requireMember();
        $order = $this->model->getOrderDetail($code, $user['id']);

        if (!$order) {
            $this->jsonError('Order not found.', 404);
        }

        $this->jsonResponse($order);
    }

    /**
     * ADMIN: GET /api/admin/orders
     * Paginated order list for admin.
     */
    public function adminList(): void {
        AuthMiddleware::requireAdmin();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int) ($_GET['limit'] ?? 10)));
        $filters = $this->pick($_GET, ['status', 'order_code']);

        $result = $this->model->getAllOrders($page, $limit, $filters);
        $this->jsonResponse($result);
    }

    /**
     * ADMIN: GET /api/admin/orders/:code
     * Detailed order view for admin.
     */
    public function adminShow(string $code): void {
        AuthMiddleware::requireAdmin();
        $order = $this->model->getOrderDetail($code);

        if (!$order) {
            $this->jsonError('Order not found.', 404);
        }

        $this->jsonResponse($order);
    }

    /**
     * ADMIN: PUT /api/admin/orders/:id/status
     * Update order or payment status.
     * Payload: {"status": "shipping", "payment_status": "paid"}
     */
    public function updateStatus(string $id): void {
        AuthMiddleware::requireAdmin();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'status' => 'required|in:pending,confirmed,shipping,completed,canceled'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $success = $this->model->updateStatus(
            (int)$id, 
            $data['status'], 
            $data['payment_status'] ?? null
        );

        if ($success) {
            $this->jsonResponse(['message' => 'Order status updated.']);
        } else {
            $this->jsonError('Failed to update status.', 400);
        }
    }
}
