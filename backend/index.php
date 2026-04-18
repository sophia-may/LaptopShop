<?php
/**
 * LaptopShop API — Front Controller / Router
 *
 * ALL requests flow through this single entry point.
 * .htaccess rewrites everything to index.php.
 *
 * Design:
 *   1. CORS headers (centrally)
 *   2. Handle OPTIONS preflight
 *   3. Load core dependencies
 *   4. Parse URI + Method
 *   5. Match against static route map
 *   6. Lazy-load controller + call action with extracted params
 *   7. 404 fallback
 */

// ============================================================
// 1. CORS Headers
// ============================================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 2. Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================================
// 3. Load core dependencies
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/helpers/JwtHelper.php';
require_once __DIR__ . '/helpers/ImageUploader.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/controllers/BaseController.php';

JwtHelper::init();

// ============================================================
// 4. Parse URI and Method
// ============================================================
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Strip backend prefix if running via apache virtual host (e.g. /backend/index.php/api/...)
$uri = preg_replace('#^/backend/index\.php#', '', $uri);
// Ensure leading slash, remove trailing slash (except root)
$uri = '/' . trim($uri, '/');
if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}

$method = strtoupper($_SERVER['REQUEST_METHOD']);

// ============================================================
// 5. Static Route Map
//
// Format: 'METHOD /path/with/:params' => ['ControllerClass', 'methodName']
//
// :param placeholders are extracted as positional arguments.
// Routes are matched top-to-bottom; first match wins.
// More specific routes MUST come before generic ones.
// ============================================================
$routes = [

    // ===================== AUTH =====================
    'POST /api/auth/register'          => ['AuthController', 'register'],
    'POST /api/auth/login'             => ['AuthController', 'login'],
    'POST /api/auth/admin/login'       => ['AuthController', 'loginAdmin'],
    'GET  /api/auth/me'                => ['AuthController', 'me'],
    'PUT  /api/auth/profile'           => ['AuthController', 'updateProfile'],
    'PUT  /api/auth/password'          => ['AuthController', 'changePassword'],
    'POST /api/auth/avatar'            => ['AuthController', 'uploadAvatar'],

    // ===================== PUBLIC: SITE SETTINGS =====================
    'GET  /api/site-settings/public'   => ['SiteSettingsController', 'publicSettings'],

    // ===================== PUBLIC: CONTACTS =====================
    'POST /api/contacts'               => ['ContactController', 'store'],

    // ===================== PUBLIC: PRODUCTS =====================
    'GET  /api/products/featured'      => ['ProductController', 'featured'],
    'GET  /api/products'               => ['ProductController', 'index'],
    'GET  /api/products/:slug'         => ['ProductController', 'show'],

    // ===================== PUBLIC: REVIEWS =====================
    'GET  /api/products/:id/reviews'   => ['ReviewController', 'byProduct'],
    'POST /api/products/:id/reviews'   => ['ReviewController', 'store'],

    // ===================== PUBLIC: CATEGORIES =====================
    'GET  /api/categories'             => ['CategoryController', 'index'],

    // ===================== PUBLIC: BRANDS =====================
    'GET  /api/brands'                 => ['BrandController', 'index'],

    // ===================== PUBLIC: ARTICLES =====================
    'GET  /api/articles'               => ['ArticleController', 'index'],
    'GET  /api/articles/:slug'         => ['ArticleController', 'show'],

    // ===================== PUBLIC: FAQS =====================
    'GET  /api/faqs'                   => ['FaqController', 'index'],

    // ===================== ADMIN: DASHBOARD =====================
    'GET  /api/admin/dashboard'        => ['DashboardController', 'index'],

    // ===================== ADMIN: MEMBERS =====================
    'GET    /api/admin/members'                  => ['AdminUserController', 'index'],
    'GET    /api/admin/members/:id'              => ['AdminUserController', 'show'],
    'PUT    /api/admin/members/:id/lock'         => ['AdminUserController', 'toggleLock'],
    'PUT    /api/admin/members/:id/reset-password' => ['AdminUserController', 'resetPassword'],
    'DELETE /api/admin/members/:id'              => ['AdminUserController', 'destroy'],

    // ===================== ADMIN: CONTACTS =====================
    'GET    /api/admin/contacts'                 => ['ContactController', 'index'],
    'GET    /api/admin/contacts/:id'             => ['ContactController', 'show'],
    'PUT    /api/admin/contacts/:id/status'      => ['ContactController', 'updateStatus'],
    'DELETE /api/admin/contacts/:id'             => ['ContactController', 'destroy'],

    // ===================== ADMIN: SITE SETTINGS =====================
    'GET  /api/admin/site-settings'              => ['SiteSettingsController', 'index'],
    'PUT  /api/admin/site-settings/:key'         => ['SiteSettingsController', 'update'],
    'POST /api/admin/site-settings/upload'       => ['SiteSettingsController', 'uploadImage'],
];

// ============================================================
// 6. Route Matching Engine
//
// Converts route patterns like '/api/products/:slug' to regex
// and extracts named parameters as positional arguments.
// ============================================================
$matched = false;

foreach ($routes as $routePattern => $handler) {
    // Split pattern into method and path
    $parts = preg_split('/\s+/', trim($routePattern), 2);
    if (count($parts) !== 2) continue;

    $routeMethod = strtoupper($parts[0]);
    $routePath = $parts[1];

    // Check method first (fast rejection)
    if ($routeMethod !== $method) continue;

    // Convert route path to regex:
    //   :param  → ([^/]+)   (captures one segment)
    $regex = preg_replace('#:([a-zA-Z_][a-zA-Z0-9_-]*)#', '([^/]+)', $routePath);
    $regex = '#^' . $regex . '$#';

    if (preg_match($regex, $uri, $matches)) {
        // First match is the full string; remaining are captured params
        array_shift($matches);

        // Lazy-load the controller file
        $controllerClass = $handler[0];
        $actionMethod = $handler[1];
        $controllerFile = __DIR__ . '/controllers/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: controller not found.'
            ]);
            exit();
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: controller class not found.'
            ]);
            exit();
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $actionMethod)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Server error: action not found.'
            ]);
            exit();
        }

        // Call the action with extracted parameters
        call_user_func_array([$controller, $actionMethod], $matches);
        $matched = true;
        break;
    }
}

// ============================================================
// 7. 404 Fallback
// ============================================================
if (!$matched) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint not found: ' . $method . ' ' . $uri
    ]);
    exit();
}
