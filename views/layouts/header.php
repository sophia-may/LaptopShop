<?php
// Start session to access user info and cart count
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'helpers/settings_helper.php';
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : null;

// Get settings
$siteName = getSetting('general.site_name', 'PhoneStore');
$logoPath = getSetting('general.site_logo', 'assets/img/logo.png');
$siteLogo = getImageUrl($logoPath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($siteLogo); ?>">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time() ?>">
</head>
<body>
<div id="page-overlay" class="page-overlay"></div>

<!-- CellphoneS-style Header -->
<header id="header" class="cellphones-header sticky-top">
    <div class="header-promo d-none d-lg-flex">
        <div class="container">
            <div class="promo-marquee">
                <div class="promo-track">
                    <?php 
                    $promoItems = [
                        ['icon' => 'bi-check-circle-fill', 'text' => 'Sản phẩm <strong>Chính hãng - Xuất VAT đầy đủ</strong>'],
                        ['icon' => 'bi-truck', 'text' => '<strong>Giao nhanh - Miễn phí</strong> cho đơn 300k'],
                        ['icon' => 'bi-arrow-repeat', 'text' => '<strong>Thu cũ</strong> giá ngon - Lên đời tiết kiệm']
                    ];
                    // Duplicate for smooth infinite scroll
                    $scrollItems = array_merge($promoItems, $promoItems);
                    foreach ($scrollItems as $item): ?>
                        <div class="promo-item">
                            <i class="bi <?= $item['icon'] ?>" aria-hidden="true"></i>
                            <span><?= $item['text'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="promo-links d-none d-xl-flex">
                <a href="index.php?page=contact" class="promo-link">
                    <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                    <span>Cửa hàng gần bạn</span>
                </a>
                <a href="index.php?page=profile" class="promo-link">
                    <i class="bi bi-file-earmark-check" aria-hidden="true"></i>
                    <span>Tra cứu đơn hàng</span>
                </a>
                <a href="index.php?page=contact" class="promo-link">
                    <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                    <span>1800 2097</span>
                </a>
            </div>
        </div>
    </div>

    <nav class="header-main">
        <div class="container">
            <a href="index.php?page=home" class="brand-logo me-3" aria-label="cellphoneS home">
                <img src="<?= htmlspecialchars($siteLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="logo-image">
            </a>

            <div class="header-nav-desktop flex-grow-1 align-items-center">
                <div class="category-dropdown-wrapper">
                    <button type="button" class="btn-header category-btn" id="categoryBtn">
                        <i class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
                        <span>Danh mục</span>
                        <i class="bi bi-chevron-down ms-auto" aria-hidden="true"></i>
                    </button>
                    
                    <div class="category-menu-dropdown shadow-lg" id="categoryMenu">
                        <?php 
                        $headerCats = [
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-mobile.svg', 'text' => 'Điện thoại, Tablet'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-laptop.svg', 'text' => 'Laptop'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-audio-2.svg', 'text' => 'Âm thanh'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-watch.svg', 'text' => 'Đồng hồ'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-home-appliances.svg', 'text' => 'Đồ gia dụng'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-accessories.svg', 'text' => 'Phụ kiện'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-pc.svg', 'text' => 'PC, Màn hình'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tv.svg', 'text' => 'Tivi'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-trade-in.svg', 'text' => 'Thu cũ đổi mới'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-used-goods.svg', 'text' => 'Hàng cũ'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-promotions.svg', 'text' => 'Khuyến mãi'],
                            ['src' => 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tech-news.svg', 'text' => 'Tin công nghệ']
                        ];
                        foreach ($headerCats as $c): ?>
                            <a href="index.php?page=shop" class="category-item">
                                <img src="<?= $c['src'] ?>" width="24" height="24" alt="">
                                <span><?= $c['text'] ?></span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="location-select dropdown ms-2">
                    <button type="button" class="btn-header location-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <span class="btn-location-text">Hồ Chí Minh</span>
                    </button>
                    <ul class="dropdown-menu location-menu shadow-sm">
                        <li><a class="dropdown-item location-menu-item active" href="#"><i class="bi bi-geo-alt"></i> Hồ Chí Minh <i class="bi bi-check2 ms-auto"></i></a></li>
                        <li><a class="dropdown-item location-menu-item" href="#"><i class="bi bi-geo-alt"></i> Hà Nội</a></li>
                        <li><a class="dropdown-item location-menu-item" href="#"><i class="bi bi-geo-alt"></i> Đà Nẵng</a></li>
                    </ul>
                </div>

                <div class="header-search ms-2">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <form action="index.php" method="GET" class="w-100 mb-0">
                        <input type="hidden" name="page" value="shop">
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Bạn muốn mua gì hôm nay?" required class="w-100 border-0 bg-transparent outline-none">
                    </form>
                </div>

                <div class="header-actions ms-2">
                    <!-- Cart Dropdown -->
                    <div class="dropdown">
                        <a href="index.php?page=cart" class="action-link">
                            <span>Giỏ hàng</span>
                            <span class="icon-with-badge">
                                <i class="bi bi-cart3" aria-hidden="true"></i>
                                <span class="badge" id="cartBadge"><?= $cartCount ?></span>
                            </span>
                        </a>
                        
                        <?php if ($isLoggedIn): ?>
                        <div class="dropdown-menu dropdown-menu-end shadow p-0 mt-2" style="width: 320px; border-radius: 8px; border: 1px solid #eee; z-index: 1060;">
                            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center" style="border-radius: 8px 8px 0 0;">
                                <h6 class="m-0 text-dark">Shopping Cart</h6>
                                <span class="badge bg-secondary" id="cartItemCount"><?= $cartCount ?> items</span>
                            </div>
                            
                            <div id="cartDropdownBody" style="max-height: 300px; overflow-y: auto;">
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-cart-x fs-1"></i>
                                    <p class="mt-2 mb-0">Your cart is empty</p>
                                </div>
                            </div>
                            
                            <div class="p-3 border-top bg-light" style="border-radius: 0 0 8px 8px;">
                                <div class="d-flex justify-content-between mb-3 text-dark">
                                    <span class="fw-bold">Total:</span>
                                    <strong id="cartTotalAmount" class="text-danger fs-5">0.00</strong>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="?page=cart" class="btn btn-outline-secondary w-50">View Cart</a>
                                    <a href="?page=checkout" class="btn btn-danger w-50">Checkout</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <div class="dropdown">
                            <button type="button" class="action-link account-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="border:none">
                                <span class="text-truncate" style="max-width: 80px;"><?= htmlspecialchars($username) ?></span>
                                <span class="icon-with-badge">
                                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                                <li><a class="dropdown-item" href="index.php?page=profile"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                                    <li><a class="dropdown-item text-danger fw-bold" href="index.php?page=admin_dashboard"><i class="bi bi-shield-lock me-2"></i>Trang quản trị</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?page=logout" onclick="return confirm('Bạn có chắc muốn đăng xuất?')"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login_signup" class="action-link account-box text-decoration-none">
                            <span>Đăng nhập</span>
                            <span class="icon-with-badge">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-mobile-actions d-lg-none ms-auto">
                <a href="index.php?page=cart" class="mobile-cart-link position-relative" aria-label="Giỏ hàng">
                    <i class="bi bi-cart3" aria-hidden="true"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white" style="font-size: 10px; margin-top: 5px; margin-left: -5px;">
                        <?= $cartCount ?>
                    </span>
                </a>

                <button type="button" class="mobile-menu-toggle ms-2" data-bs-toggle="collapse" data-bs-target="#mobileMenu" aria-label="Mở menu">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div class="collapse d-lg-none bg-white p-3 shadow-sm border-top" id="mobileMenu" style="position: absolute; width: 100%; z-index: 1000;">
            <div class="header-search mb-3 w-100" style="background: #f1f2f4;">
                <i class="bi bi-search ms-2 text-dark" aria-hidden="true"></i>
                <form action="index.php" method="GET" class="w-100 mb-0 d-flex">
                    <input type="hidden" name="page" value="shop">
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Bạn tìm gì hôm nay?" class="w-100 border-0 bg-transparent outline-none p-2" style="color: #111;">
                </form>
            </div>

            <div class="d-grid gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="index.php?page=profile" class="btn-header justify-content-center text-dark bg-light text-decoration-none">
                        <i class="bi bi-person-circle text-danger" aria-hidden="true"></i>
                        <span>Tài khoản (<?= htmlspecialchars($username) ?>)</span>
                    </a>
                <?php else: ?>
                    <a href="index.php?page=login_signup" class="btn-header justify-content-center text-dark bg-light text-decoration-none">
                        <i class="bi bi-person-circle text-danger" aria-hidden="true"></i>
                        <span>Đăng nhập</span>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn-header justify-content-center text-dark bg-light mt-1">
                    <i class="bi bi-grid-3x3-gap-fill text-danger" aria-hidden="true"></i>
                    <span>Danh mục sản phẩm</span>
                </button>
            </div>
        </div>
    </nav>
</header>
<script>
// Handle sticky header scroll hide/show effect
document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const header = document.getElementById('header');
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > 50) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
        lastScrollTop = scrollTop;
    });

    // Category Toggle Logic
    const categoryBtn = document.getElementById('categoryBtn');
    const categoryMenu = document.getElementById('categoryMenu');
    const pageOverlay = document.getElementById('page-overlay');

    if (categoryBtn && categoryMenu && pageOverlay) {
        categoryBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            categoryMenu.classList.toggle('active');
            pageOverlay.classList.toggle('active');
        });

        // Close when clicking overlay
        pageOverlay.addEventListener('click', function() {
            categoryMenu.classList.remove('active');
            pageOverlay.classList.remove('active');
        });

        // Close when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!categoryMenu.contains(e.target) && !categoryBtn.contains(e.target)) {
                categoryMenu.classList.remove('active');
                pageOverlay.classList.remove('active');
            }
        });
    }
});
</script>
</script>

<script src="assets/javascript/cart.js"></script>

<!-- Logout confirmation script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm("Are you sure you want to logout?")) {
                // Redirect to logout route
                window.location.href = "index.php?page=logout";
            }
        });
    }
});
</script>
