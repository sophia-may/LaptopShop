<?php
session_start();

require_once 'config/db.php';

// Database Connection for Controllers that need it
$database = new Database();
$db = $database->connect();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    // ---- AUTH ----
    case 'login_signup':
        include 'views/client/login_signup.php';
        break;
    case 'logout':
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        $authController->logout();
        break;

    // ---- CLIENT PAGES (Controllers) ----
    case 'shop':
        require_once 'controllers/ShopController.php';
        $controller = new ShopController();
        $controller->index();
        break;
    case 'product':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        $controller->show();
        break;
    case 'checkout':
        require_once 'controllers/CheckoutController.php';
        $controller = new CheckoutController();
        $controller->index();
        break;
    case 'post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->index();
        break;
    case 'post_detail':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->detail();
        break;

    // ---- CLIENT PAGES (Direct View) ----
    case 'home':
        include 'views/client/home.php';
        break;
    case 'about':
        include 'views/client/about.php';
        break;
    case 'cart':
        include 'views/client/cart.php';
        break;
    case 'contact':
        include 'views/client/contact.php';
        break;
    case 'profile':
        include 'views/client/profile.php';
        break;
    case 'qna':
        include 'views/client/qna.php';
        break;
    case 'order_success':
        include 'views/client/order_success.php';
        break;

    // ---- ADMIN PAGES (Controllers) ----
    case 'manage_products':
        require_once 'controllers/admin/ProductAdminController.php';
        $controller = new ProductAdminController();
        $controller->index();
        break;
    case 'admin_reviews':
        require_once 'controllers/admin/ReviewController.php';
        $controller = new ReviewController();
        $controller->index();
        break;

    // ---- ADMIN PAGES (Direct View) ----
    case 'admin_dashboard':
        include 'views/admin/admin_dashboard.php';
        break;
    case 'manage_orders':
        include 'views/admin/manage_orders.php';
        break;
    case 'order_details':
        include 'views/admin/order_details.php';
        break;
    case 'manage_about_info':
        include 'views/admin/manage_about_info.php';
        break;
    case 'manage_contacts':
        include 'views/admin/manage_contacts.php';
        break;
    case 'manage_info':
        include 'views/admin/manage_info.php';
        break;
    case 'manage_posts':
        include 'views/admin/manage_posts.php';
        break;
    case 'manage_profile':
        include 'views/admin/manage_profile.php';
        break;
    case 'manage_qna':
        include 'views/admin/manage_qna.php';
        break;

    // ---- DEFAULT ----
    default:
        include 'views/client/home.php';
        break;
}
