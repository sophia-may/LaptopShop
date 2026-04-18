<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/MemberModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/ContactModel.php';

/**
 * DashboardController — Admin dashboard statistics.
 *
 * Endpoints:
 *   GET /api/admin/dashboard — Summary stats for admin panel
 */
class DashboardController extends BaseController {

    /**
     * GET /api/admin/dashboard
     * Admin: summary statistics.
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $memberModel = new MemberModel();
        $productModel = new ProductModel();
        $contactModel = new ContactModel();

        // Gather stats
        $stats = [
            'total_members'    => $memberModel->countTotal(),
            'total_products'   => $productModel->countTotal(),
            'total_contacts'   => $contactModel->countTotal(),
            'unread_contacts'  => $contactModel->countByStatus('unread'),
        ];

        // Recent contacts (latest 5)
        $recentContacts = $contactModel->getPaginated(1, 5);

        $this->jsonResponse([
            'stats'           => $stats,
            'recent_contacts' => $recentContacts['items'],
        ]);
    }
}
