<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * CartController — API endpoints for managing the shopping cart.
 * All endpoints require member authentication.
 */
class CartController extends BaseController {

    private CartModel $model;

    public function __construct() {
        $this->model = new CartModel();
    }

    /**
     * GET /api/cart
     * Get current user's cart items and summary.
     */
    public function index(): void {
        $user = AuthMiddleware::requireMember();
        $items = $this->model->getItems($user['id']);
        $summary = $this->model->getSummary($user['id']);

        $this->jsonResponse([
            'items'   => $items,
            'summary' => $summary
        ]);
    }

    /**
     * POST /api/cart/add
     * Add a variant to the cart.
     * Payload: {"variant_id": 1, "quantity": 1}
     */
    public function add(): void {
        $user = AuthMiddleware::requireMember();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'variant_id' => 'required|integer',
            'quantity'   => 'required|integer|min:1'
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $success = $this->model->addItem($user['id'], (int)$data['variant_id'], (int)$data['quantity']);

        if ($success) {
            $this->jsonResponse(['message' => 'Product added to cart.']);
        } else {
            $this->jsonError('Failed to add product. Please check if the product variant exists.', 400);
        }
    }

    /**
     * PUT /api/cart/update
     * Update quantity of an item in the cart.
     * Payload: {"variant_id": 1, "quantity": 2}
     */
    public function update(): void {
        $user = AuthMiddleware::requireMember();
        $data = $this->getPostData();

        $errors = $this->validate($data, [
            'variant_id' => 'required|integer',
            'quantity'   => 'required|integer' // Can be 0 to remove
        ]);

        if (!empty($errors)) {
            $this->jsonError('Validation failed.', 422, $errors);
        }

        $this->model->updateItem($user['id'], (int)$data['variant_id'], (int)$data['quantity']);
        $this->jsonResponse(['message' => 'Cart updated.']);
    }

    /**
     * DELETE /api/cart/remove
     * Remove an item from the cart.
     * Payload: {"variant_id": 1}
     */
    public function remove(): void {
        $user = AuthMiddleware::requireMember();
        $data = $this->getPostData();

        if (empty($data['variant_id'])) {
            $this->jsonError('Variant ID is required.', 400);
        }

        $this->model->removeItem($user['id'], (int)$data['variant_id']);
        $this->jsonResponse(['message' => 'Item removed from cart.']);
    }

    /**
     * DELETE /api/cart/clear
     * Clear the entire cart.
     */
    public function clear(): void {
        $user = AuthMiddleware::requireMember();
        $this->model->clear($user['id']);
        $this->jsonResponse(['message' => 'Cart cleared.']);
    }
}
