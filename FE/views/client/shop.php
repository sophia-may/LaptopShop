<?php 
require_once 'helpers/settings_helper.php';
include 'views/layouts/header.php';
$isLoggedIn = isset($_SESSION['user_id']);
$products = $products ?? [];
$totalProducts = $totalProducts ?? count($products);
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
$filterOptions = $filterOptions ?? ['brands' => [], 'storages' => [], 'categories' => []];
?>

<div class="filter-bar py-4" style="background: white; border-bottom: 2px solid #f0f0f0; position: sticky; top: 0; z-index: 99;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Filter Button -->
            <div class="col-auto">
                <button class="btn btn-sm fw-bold px-3" type="button" data-bs-toggle="modal" data-bs-target="#filterSidebar" style="border-width: 2px; border-radius: 6px; background: linear-gradient(135deg, #DC143C 0%, #B22222 100%); color: #fff; border: 0; box-shadow: 0 8px 20px rgba(220, 20, 60, 0.25);">
                    <i class="bi bi-funnel-fill"></i> Lọc
                </button>
            </div>
            
            <!-- Sort Dropdown -->
            <div class="col-auto ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted fw-bold" style="letter-spacing: 0.3px; margin-bottom: 0;">Sắp xếp:</label>
                    <form id="sortForm" action="index.php" method="GET" class="d-inline-block">
                        <input type="hidden" name="page" value="shop">
                        <?php
                        if (isset($_GET['search']) && $_GET['search'] !== '') {
                            echo '<input type="hidden" name="search" value="' . htmlspecialchars($_GET['search']) . '">';
                        }
                        if (isset($_GET['brand']) && is_array($_GET['brand'])) {
                            foreach ($_GET['brand'] as $brand) {
                                echo '<input type="hidden" name="brand[]" value="' . htmlspecialchars($brand) . '">';
                            }
                        }
                        if (isset($_GET['category'])) {
                            echo '<input type="hidden" name="category" value="' . htmlspecialchars($_GET['category']) . '">';
                        }
                        if (isset($_GET['storage']) && is_array($_GET['storage'])) {
                            foreach ($_GET['storage'] as $storage) {
                                echo '<input type="hidden" name="storage[]" value="' . htmlspecialchars($storage) . '">';
                            }
                        }
                        if (isset($_GET['price_min'])) {
                            echo '<input type="hidden" name="price_min" value="' . htmlspecialchars($_GET['price_min']) . '">';
                        }
                        if (isset($_GET['price_max'])) {
                            echo '<input type="hidden" name="price_max" value="' . htmlspecialchars($_GET['price_max']) . '">';
                        }
                        ?>
                        <select name="sort" class="form-select form-select-sm" style="width: 180px; border-radius: 6px; border: 1px solid #ddd; font-weight: 500; font-size: 13px;" onchange="this.form.submit()">
                            <option value="default" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'default') echo 'selected'; ?>>Nổi Bật</option>
                            <option value="price_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Giá: Cao đến Thấp</option>
                            <option value="name_asc" <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Tên: A-Z</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop-banner-hero" style="background: linear-gradient(135deg, #DC143C 0%, #B22222 100%); padding: 60px 20px; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-md-6 z-1">
                <div style="color: white;">
                    <span class="badge badge-danger mb-3" style="background-color: white; color: #DC143C; padding: 8px 16px; font-weight: 600; font-size: 12px; letter-spacing: 1px;">CÔNG NGHỆ HÀNG ĐẦU</span>
                    <h1 class="display-3 fw-bold mb-3" style="line-height: 1.1; font-size: 48px;">
                        Laptop
                    </h1>
                    <p class="lead mb-4" style="font-size: 16px; line-height: 1.6; opacity: 0.95;">
                        Khám phá thế hệ laptop tiếp theo với hiệu suất tuyệt vời. Tìm kiếm bộ sưu tập premium của chúng tôi gồm ultrabook cao cấp, laptop gaming mạnh mẽ và workstation chuyên nghiệp.
                    </p>
                </div>
            </div>
            
            <!-- Right Image -->
            <div class="col-md-6 text-end">
                <img src="<?php echo resolveImagePath('assets/img/products/product_1.webp'); ?>" 
                     alt="Laptop Premium" 
                     style="max-width: 100%; height: auto; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3)); transform: translateZ(0);">
            </div>
        </div>
    </div>
    
    <!-- Decorative Shape (Background) -->
    <div style="position: absolute; right: -100px; top: -50px; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%; z-index: 0;"></div>
</section>


<div class="modal fade" id="filterSidebar" tabindex="-1" aria-labelledby="filterSidebarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border: 0; border-radius: 18px; overflow: hidden; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);">
            <div class="modal-header" style="background: linear-gradient(135deg, #DC143C 0%, #B22222 100%); color: #fff; border-bottom: 0; padding: 18px 22px;">
                <h5 class="modal-title fw-bold" id="filterSidebarLabel">
                    <i class="bi bi-funnel-fill"></i> <?php echo htmlspecialchars(getSetting('shop.filter_title', 'Lọc Sản Phẩm')); ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: #fff; padding: 24px 22px;">
                <form method="GET" action="index.php" id="filterForm">
                    <input type="hidden" name="page" value="shop">
                    
                    <!-- Giữ lại giá trị sort hiện tại -->
                    <?php if(isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                    <?php endif; ?>

                    <!-- ========== 0. SEARCH KEYWORD ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3" style="color: #2b2f36;">
                            <i class="bi bi-search" style="color: #DC143C;"></i> Tìm Kiếm
                        </h6>
                        <input type="text"
                               class="form-control form-control-sm"
                               name="search"
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                               placeholder="Nhập từ khóa sản phẩm"
                               style="border-color: #e2e6ea; border-radius: 10px;">
                        <small class="text-muted d-block mt-2">Tìm theo tên, thương hiệu, danh mục hoặc mô tả.</small>
                    </div>

                    <hr>

                    <!-- ========== 1. BRAND FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3" style="color: #2b2f36;">
                            <i class="bi bi-phone" style="color: #DC143C;"></i> Thương Hiệu
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['brands'])): ?>
                                <?php foreach($filterOptions['brands'] as $brand): ?>
                                    <div class="form-check mb-2">
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
                                <p class="text-muted small">Không có thương hiệu nào</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- ========== 2. CATEGORY FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3" style="color: #2b2f36;">
                            <i class="bi bi-grid-3x3-gap" style="color: #DC143C;"></i> Danh Mục
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['categories'])): ?>
                                <?php foreach($filterOptions['categories'] as $cat): ?>
                                    <div class="form-check mb-2">
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
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="category" 
                                           value="" 
                                           id="cat-all"
                                           <?= !isset($_GET['category']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cat-all">
                                        Tất Cả Danh Mục
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- ========== 3. STORAGE FILTER ========== -->
                    <div class="filter-section mb-4">
                        <h6 class="fw-bold mb-3" style="color: #2b2f36;">
                            <i class="bi bi-hdd" style="color: #DC143C;"></i> Dung Lượng Lưu Trữ
                        </h6>
                        <div class="filter-options">
                            <?php if (!empty($filterOptions['storages'])): ?>
                                <?php foreach($filterOptions['storages'] as $storage): ?>
                                    <div class="form-check mb-2">
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
                        <h6 class="fw-bold mb-3" style="color: #2b2f36;">
                            <i class="bi bi-currency-dollar" style="color: #DC143C;"></i> Khoảng Giá
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="price_min" 
                                       placeholder="Min (đ)" 
                                       value="<?= isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : '' ?>"
                                       min="0"
                                       step="100"
                                       style="border-color: #e2e6ea; border-radius: 10px;">
                            </div>
                            <div class="col-6">
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       name="price_max" 
                                       placeholder="Max (đ)" 
                                       value="<?= isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : '' ?>"
                                       min="0"
                                       step="100"
                                       style="border-color: #e2e6ea; border-radius: 10px;">
                            </div>
                        </div>
                        <small class="text-muted">Nhập giá theo VND (đ)</small>
                    </div>

                    <hr>

                    <!-- ========== ACTION BUTTONS ========== -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn fw-bold" style="background: linear-gradient(135deg, #DC143C 0%, #B22222 100%); color: #fff; border-radius: 10px; box-shadow: 0 8px 20px rgba(220, 20, 60, 0.18);">
                            <i class="bi bi-check-circle"></i> Áp Dụng Bộ Lọc
                        </button>
                        <a href="?page=shop" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px;">
                            <i class="bi bi-x-circle"></i> Xóa Tất Cả Bộ Lọc
                        </a>
                    </div>
                </form>
            </div>
        </div>
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
                                $imgUrl = resolveImagePath($product['image']); 
                            ?>
                            <a href="?page=product&id=<?= $product['id'] ?>" class="product-image-link">
                                <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                    alt="<?= htmlspecialchars($product['name']) ?>" 
                                    class="product-img">
                             </a>
                        
                            <div class="product-overlay">
                                <button type="button" class="btn-add-cart overlay-btn add-to-cart-btn" 
                                        data-product-id="<?= $product['id'] ?>"
                                        data-quantity="1"
                                        title="Add to cart">
                                    Thêm Vào Giỏ
                                </button>
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
                    <p class="text-muted"><?php echo htmlspecialchars(getSetting('shop.no_products_message', 'Không tìm thấy sản phẩm nào.')); ?></p>
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
                        <i class="bi bi-chevron-left"></i> Trước
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
                        Tiếp theo <i class="bi bi-chevron-right"></i>
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
                <h5 class="fw-bold mt-2">Chất Lượng Cao</h5>
                <p class="text-muted small">Chế tạo từ những vật liệu hàng đầu</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-shield-check fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Bảo Hành Toàn Diện</h5>
                <p class="text-muted small">Trên 2 năm</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-box-seam fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Miễn Phí Vận Chuyển</h5>
                <p class="text-muted small">Mẫu hàng từ 5 triệu</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-headset fs-2 text-dark"></i>
                <h5 class="fw-bold mt-2">Hỗ Trợ 24/7</h5>
                <p class="text-muted small">Hỗ trợ tự độc lập</p>
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
