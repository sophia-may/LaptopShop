<?php 
require_once 'helpers/settings_helper.php';
include 'views/layouts/header.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<section class="shop-banner" style="background-image: url('assets/img/shop_banner.jpg');">
    <div class="shop-banner-content text-center">
        <h1><?php echo htmlspecialchars(getSetting('shop.page_title', 'Shop')); ?></h1>
        <?php if ($subtitle = getSetting('shop.page_subtitle')): ?>
        <p class="text-white-50"><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php?page=home">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars(getSetting('shop.page_title', 'Shop')); ?></li>
            </ol>
        </nav>
    </div>
</section>

<div class="filter-bar py-3 bg-light">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="filter-left d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterSidebar">
                <i class="bi bi-funnel"></i> <?php echo htmlspecialchars(getSetting('shop.filter_title', 'Filter')); ?>
            </button>
            <span class="text-muted small">Showing <?php echo count($products); ?> results</span>
        </div>
        <div class="filter-right d-flex align-items-center gap-3">
            <span class="small"><?php echo htmlspecialchars(getSetting('shop.sort_label', 'Sort by')); ?></span>
            <form id="sortForm" action="index.php" method="GET">
                <input type="hidden" name="page" value="shop">

                 <!-- ✅ THÊM PHẦN NÀY: Giữ lại tất cả filter parameters -->
                <?php
                // Giữ lại brand filters
                 if (isset($_GET['brand']) && is_array($_GET['brand'])) {
                    foreach ($_GET['brand'] as $brand) {
                    echo '<input type="hidden" name="brand[]" value="' . htmlspecialchars($brand) . '">';
                    }
                }
        
                // Giữ lại category filter
                if (isset($_GET['category'])) {
                    echo '<input type="hidden" name="category" value="' . htmlspecialchars($_GET['category']) . '">';
                }
        
                // Giữ lại storage filters
                if (isset($_GET['storage']) && is_array($_GET['storage'])) {
                foreach ($_GET['storage'] as $storage) {
                    echo '<input type="hidden" name="storage[]" value="' . htmlspecialchars($storage) . '">';
                    }
                }
        
                // Giữ lại price_min
                if (isset($_GET['price_min'])) {
                    echo '<input type="hidden" name="price_min" value="' . htmlspecialchars($_GET['price_min']) . '">';
                }
        
                // Giữ lại price_max
                if (isset($_GET['price_max'])) {
                    echo '<input type="hidden" name="price_max" value="' . htmlspecialchars($_GET['price_max']) . '">';
                }
                ?>
        
                <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="default" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'default') echo 'selected'; ?>>Default</option>
                    <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                    <option value="name_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Name: A-Z</option>
                    </select>
            </form>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="filterSidebar" aria-labelledby="filterSidebarLabel">
    <!-- Offcanvas Header -->
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="filterSidebarLabel">
            <i class="bi bi-funnel-fill text-primary"></i> <?php echo htmlspecialchars(getSetting('shop.filter_title', 'Filter Products')); ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <!-- Offcanvas Body -->
    <div class="offcanvas-body">
        <form method="GET" action="index.php" id="filterForm">
            <input type="hidden" name="page" value="shop">
            
            <!-- Giữ lại giá trị sort hiện tại -->
            <?php if(isset($_GET['sort'])): ?>
                <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
            <?php endif; ?>

            <!-- ========== 1. BRAND FILTER ========== -->
            <div class="filter-section mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-phone text-primary"></i> Brand
                </h6>
                <div class="filter-options">
                    <?php if (!empty($filterOptions['brands'])): ?>
                        <?php foreach($filterOptions['brands'] as $brand): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="brand[]" 
                                       value="<?= htmlspecialchars($brand) ?>" 
                                       id="brand-<?= htmlspecialchars($brand) ?>"
                                       <?= in_array($brand, $_GET['brand'] ?? []) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="brand-<?= htmlspecialchars($brand) ?>">
                                    <?= htmlspecialchars($brand) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">No brands available</p>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <!-- ========== 2. CATEGORY FILTER ========== -->
            <div class="filter-section mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-grid-3x3-gap text-primary"></i> Category
                </h6>
                <div class="filter-options">
                    <?php if (!empty($filterOptions['categories'])): ?>
                        <?php foreach($filterOptions['categories'] as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="category" 
                                       value="<?= htmlspecialchars($cat) ?>" 
                                       id="cat-<?= htmlspecialchars($cat) ?>"
                                       <?= (isset($_GET['category']) && $_GET['category'] == $cat) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat-<?= htmlspecialchars($cat) ?>">
                                    <?= htmlspecialchars($cat) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <!-- Option to show all categories -->
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="category" 
                                   value="" 
                                   id="cat-all"
                                   <?= !isset($_GET['category']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="cat-all">
                                All Categories
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <!-- ========== 3. STORAGE FILTER ========== -->
            <div class="filter-section mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-sd-card text-primary"></i> Storage
                </h6>
                <div class="filter-options">
                    <?php if (!empty($filterOptions['storages'])): ?>
                        <?php foreach($filterOptions['storages'] as $storage): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="storage[]" 
                                       value="<?= htmlspecialchars($storage) ?>" 
                                       id="storage-<?= htmlspecialchars($storage) ?>"
                                       <?= in_array($storage, $_GET['storage'] ?? []) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="storage-<?= htmlspecialchars($storage) ?>">
                                    <?= htmlspecialchars($storage) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <hr>

            <!-- ========== 4. PRICE RANGE FILTER ========== -->
            <div class="filter-section mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-currency-dollar text-primary"></i> Price Range
                </h6>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="number" 
                               class="form-control form-control-sm" 
                               name="price_min" 
                               placeholder="Min (đ)" 
                               value="<?= isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>"
                               min="0"
                               step="100">
                    </div>
                    <div class="col-6">
                        <input type="number" 
                               class="form-control form-control-sm" 
                               name="price_max" 
                               placeholder="Max (đ)" 
                               value="<?= isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>"
                               min="0"
                               step="100">
                    </div>
                </div>
                <small class="text-muted">Enter price in VND (đ)</small>
            </div>

            <hr>

            <!-- ========== ACTION BUTTONS ========== -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Apply Filters
                </button>
                <a href="?page=shop" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Clear All Filters
                </a>
            </div>
        </form>
    </div>
</div>

<section class="shop-page-section py-5">
    <div class="container">
        <div class="product-grid">
            
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        
                        <div class="product-image-wrapper">
                            <?php 
                                $imgUrl = (strpos($product['image'], 'http') === 0) 
                                    ? $product['image'] 
                                    : 'assets/img/' . $product['image']; 
                            ?>
                            <a href="?page=product&id=<?= $product['id'] ?>" class="product-image-link">
                                <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                    alt="<?= htmlspecialchars($product['name']) ?>" 
                                    class="product-img">
                             </a>
                        
                            <div class="product-overlay">
                                <button class="overlay-btn add-to-cart-btn" 
                                        data-product-id="<?= $product['id'] ?>"
                                        data-quantity="1"
                                        title="Add to cart">
                                    <i class="bi bi-cart-plus"></i> Add to cart
                                </button>
                                <div class="product-actions">
                                    <a href="#" class="action-link">
                                        <i class="bi bi-share-fill"></i> Share
                                    </a>
                                    <a href="#" class="action-link">
                                        <i class="bi bi-heart"></i> Like
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="product-info">
                            <!-- ✅ THÊM LINK VÀO TÊN SẢN PHẨM -->
                             <h3 class="product-name">
                                <a href="?page=product&id=<?= $product['id'] ?>" class="product-name-link">
                                <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>
        
                             <div class="product-category text-muted small mb-2">
                            <?= htmlspecialchars($product['category']) ?>
                             </div>
        
                            <div class="product-price">
                                <?= number_format($product['price'], 0, ',', '.') ?>đ
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted"><?php echo htmlspecialchars(getSetting('shop.no_products_message', 'No products found.')); ?></p>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-container mt-5 d-flex justify-content-center gap-2">
                <?php
                // Build query string giữ lại TẤT CẢ params trừ page number
                $queryParams = $_GET;
                unset($queryParams['p']);
                $baseQuery = http_build_query($queryParams);
                ?>
                
                <!-- Previous -->
                <?php if ($page > 1): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $page - 1 ?>" 
                       class="btn btn-light border">
                        <i class="bi bi-chevron-left"></i> Prev
                    </a>
                <?php endif; ?>
                
                <!-- Page Numbers with Smart Range -->
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                // First page + ellipsis
                if ($startPage > 1): ?>
                    <a href="?<?= $baseQuery ?>&p=1" class="btn btn-light border">1</a>
                    <?php if ($startPage > 2): ?>
                        <span class="btn btn-light border disabled">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Range pages -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $i ?>" 
                       class="btn <?= $i == $page ? 'btn-warning text-white fw-bold' : 'btn-light border' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <!-- Last page + ellipsis -->
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="btn btn-light border disabled">...</span>
                    <?php endif; ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $totalPages ?>" class="btn btn-light border">
                        <?= $totalPages ?>
                    </a>
                <?php endif; ?>
                
                <!-- Next -->
                <?php if ($page < $totalPages): ?>
                    <a href="?<?= $baseQuery ?>&p=<?= $page + 1 ?>" 
                       class="btn btn-light border">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<section class="features-bar py-5 bg-light mt-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <i class="bi bi-trophy fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">High Quality</h5>
                <p class="text-muted small">crafted from top materials</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-shield-check fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Warranty Protection</h5>
                <p class="text-muted small">Over 2 years</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-box-seam fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Free Shipping</h5>
                <p class="text-muted small">Order over 150 $</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-headset fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">24 / 7 Support</h5>
                <p class="text-muted small">Dedicated support</p>
            </div>
        </div>
    </div>
</section>
<script>
// Placeholder
function likeProduct(productId) {
    alert('Wishlist feature coming soon!\nProduct ID: ' + productId);
}
</script>

<?php include 'views/layouts/footer.php'; ?>
