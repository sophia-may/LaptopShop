<?php 
// File: views/client/product_detail.php
include 'views/layouts/header.php'; 
$product = $product ?? [];
$productImages = $productImages ?? [];
$variants = $variants ?? ['storage' => [], 'color' => [], 'ram' => []];
$displayPrice = $displayPrice ?? ($product['price'] ?? 0);
$averageRating = $averageRating ?? 0;
$reviewCount = $reviewCount ?? 0;
$reviews = $reviews ?? [];
$reviewStats = $reviewStats ?? null;
function numberToWords($num) {
    $words = ['', 'one', 'two', 'three', 'four', 'five'];
    return $words[$num] ?? '';
}
?>
<style>
    .pd-page {
        background: #f7f7f9;
    }

    .pd-top {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #eceef2;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .pd-main-image-wrap {
        background: #f5f7fa;
        border: 1px solid #e8ebf1;
        border-radius: 12px;
        padding: 10px;
    }

    .pd-main-image {
        width: 100%;
        max-height: 360px;
        object-fit: cover;
        border-radius: 10px;
    }

    .pd-thumb {
        width: 88px;
        height: 62px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e9edf4;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pd-thumb.active {
        border-color: #dc2626;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.12);
    }

    .pd-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        color: #dc2626;
        background: #fee2e2;
        padding: 4px 8px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .pd-title {
        font-size: clamp(1.8rem, 3vw, 2.9rem);
        line-height: 1.1;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
    }

    .pd-desc {
        color: #4b5563;
        font-size: 0.95rem;
        margin-bottom: 18px;
    }

    .pd-config-block {
        border-top: 1px solid #e5e7eb;
        padding-top: 14px;
        margin-top: 14px;
    }

    .pd-config-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .pd-option {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 84px;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        color: #111827;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-check:checked + .pd-option,
    .pd-option:hover {
        border-color: #dc2626;
        color: #dc2626;
        background: #fff5f5;
    }

    .pd-price {
        color: #dc2626;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        line-height: 1;
        margin-top: 12px;
    }

    .pd-old-price {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .pd-rating {
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        padding: 10px 0;
        margin-top: 12px;
    }

    .pd-color-swatch {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        display: inline-block;
        cursor: pointer;
    }

    .color-swatch.active-color,
    .pd-color-swatch:hover {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
    }

    .pd-specs {
        margin-top: 22px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eceef2;
        overflow: hidden;
    }

    .pd-specs-head {
        padding: 16px 18px;
        font-size: 1.8rem;
        font-weight: 800;
        color: #111827;
        border-bottom: 2px solid #ef4444;
        background: #fff;
    }

    .pd-specs table {
        margin-bottom: 0;
    }

    .pd-specs th,
    .pd-specs td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: #edf1f5;
    }

    .pd-specs th {
        width: 33%;
        background: #fafafa;
        color: #374151;
        font-weight: 700;
    }

    .pd-bottom-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -6px 20px rgba(15, 23, 42, 0.06);
    }

    @media (max-width: 991px) {
        .pd-bottom-bar {
            position: static;
            margin-top: 16px;
            border-radius: 12px;
            border: 1px solid #eceef2;
        }
    }
</style>

<section class="breadcrumb-section py-3 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="?page=shop">Danh mục</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Product') ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="product-detail-section py-4 pd-page">
    <div class="container">
        <div class="pd-top">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="pd-main-image-wrap mb-2">
                        <?php $firstImage = !empty($productImages[0]['image_url']) ? $productImages[0]['image_url'] : ($product['image'] ?? ''); ?>
                        <img src="<?= htmlspecialchars($firstImage) ?>"
                             alt="<?= htmlspecialchars($product['name'] ?? 'Product image') ?>"
                             id="mainProductImage"
                             class="pd-main-image">
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <?php foreach ($productImages as $index => $image): ?>
                            <img src="<?= htmlspecialchars($image['image_url']) ?>"
                                 alt="Product view <?= $index + 1 ?>"
                                 class="pd-thumb <?= $index === 0 ? 'active' : '' ?>"
                                 onclick="changeMainImage('<?= htmlspecialchars($image['image_url']) ?>', this)"
                                 data-index="<?= $index ?>">
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h1 class="pd-title"><?= htmlspecialchars($product['name'] ?? 'Product') ?></h1>
                    <p class="pd-desc"><?= htmlspecialchars($product['description'] ?? '') ?></p>

                    <h5 class="fw-bold mb-1">Cấu hình</h5>

                    <?php if (!empty($variants['storage'])): ?>
                        <div class="pd-config-block">
                            <div class="pd-config-label">Dung lượng</div>
                            <div class="d-flex gap-2 flex-wrap" id="storageVariants">
                                <?php foreach ($variants['storage'] as $storage): ?>
                                    <input type="radio"
                                           class="btn-check variant-radio"
                                           name="storage"
                                           id="storage<?= $storage['id'] ?>"
                                           value="<?= $storage['id'] ?>"
                                           data-price-modifier="<?= $storage['price_modifier'] ?>"
                                           data-stock="<?= $storage['stock'] ?>"
                                           autocomplete="off"
                                           <?= $storage['is_default'] ? 'checked' : '' ?>>
                                    <label class="pd-option" for="storage<?= $storage['id'] ?>">
                                        <?= htmlspecialchars($storage['variant_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($variants['ram'])): ?>
                        <div class="pd-config-block">
                            <div class="pd-config-label">Bộ nhớ</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($variants['ram'] as $ram): ?>
                                    <input type="radio"
                                           class="btn-check variant-radio"
                                           name="ram"
                                           id="ram<?= $ram['id'] ?>"
                                           value="<?= $ram['id'] ?>"
                                           data-stock="<?= $ram['stock'] ?>"
                                           autocomplete="off"
                                           <?= $ram['is_default'] ? 'checked' : '' ?>>
                                    <label class="pd-option" for="ram<?= $ram['id'] ?>">
                                        <?= htmlspecialchars($ram['variant_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($variants['color'])): ?>
                        <div class="pd-config-block">
                            <div class="pd-config-label">Màu sắc<span id="selectedColorName" class="text-muted text-capitalize">- <?= htmlspecialchars($variants['color'][0]['variant_name']) ?></span></div>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($variants['color'] as $index => $color): ?>
                                    <input type="radio"
                                           class="btn-check color-radio"
                                           name="color"
                                           id="color<?= $color['id'] ?>"
                                           value="<?= $color['id'] ?>"
                                           data-color-name="<?= htmlspecialchars($color['variant_name']) ?>"
                                           data-stock="<?= $color['stock'] ?>"
                                           autocomplete="off"
                                           <?= $index === 0 ? 'checked' : '' ?>>
                                    <label class="pd-color-swatch color-swatch"
                                           for="color<?= $color['id'] ?>"
                                           style="background-color: <?= htmlspecialchars($color['variant_value']) ?>;"
                                           title="<?= htmlspecialchars($color['variant_name']) ?>"></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="pd-price" id="displayPrice"><?= number_format($displayPrice, 0, ',', '.') ?>đ</div>
                    <div class="pd-old-price">Giá gốc: <?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ</div>

                    <div class="pd-rating d-flex align-items-center gap-3 mt-2">
                        <div class="text-warning">
                            <?php
                            $fullStars = floor($averageRating);
                            $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                            $emptyStars = 5 - $fullStars - $halfStar;
                            for ($i = 0; $i < $fullStars; $i++) echo '<i class="bi bi-star-fill"></i>';
                            if ($halfStar) echo '<i class="bi bi-star-half"></i>';
                            for ($i = 0; $i < $emptyStars; $i++) echo '<i class="bi bi-star"></i>';
                            ?>
                        </div>
                        <div><strong><?= number_format($averageRating, 1) ?></strong> <span class="text-muted">(<?= $reviewCount ?> đánh giá)</span></div>
                    </div>

                    <div class="d-none">
                        <input type="number" id="productQuantity" value="1" min="1" max="<?= $product['stock'] ?? 1 ?>" readonly>
                        <span id="stockDisplay"><?= $product['stock'] ?? 0 ?></span>
                        <button type="button" id="decreaseQty">-</button>
                        <button type="button" id="increaseQty">+</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="pd-specs mt-4">
            <div class="pd-specs-head">Đặc tả kỹ thuật</div>
            <table class="table">
                <tbody>
                    <tr>
                        <th>Bộ xử lý</th>
                        <td><?= htmlspecialchars($product['name'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Bộ nhớ</th>
                        <td><?= htmlspecialchars($product['ram'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Lữu trữ</th>
                        <td>
                            <?php if (!empty($variants['storage'])): ?>
                                <?php echo implode(', ', array_map(fn($item) => $item['variant_name'], $variants['storage'])); ?>
                            <?php else: ?>
                                <?= htmlspecialchars($product['storage'] ?? 'N/A') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Không dây</th>
                        <td>Wi-Fi 6 / Bluetooth</td>
                    </tr>
                    <tr>
                        <th>I/O Ports</th>
                        <td>USB-C, HDMI, Audio jack</td>
                    </tr>
                    <tr>
                        <th>Danh mục</th>
                        <td><?= htmlspecialchars($product['category'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <th>Nhãn hàng</th>
                        <td><?= htmlspecialchars($product['brand'] ?? 'N/A') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pd-bottom-bar mt-3">
            <div class="d-flex align-items-center justify-content-between gap-3 p-3 p-lg-4">
                <div>
                    <div class="text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.8px;">Tổng tiền</div>
                    <div class="pd-price mb-0" style="font-size: 2rem;"><?= number_format($displayPrice, 0, ',', '.') ?>đ</div>
                </div>
                <button class="btn btn-lg add-to-cart-btn"
                        data-product-id="<?= $product['id'] ?? 0 ?>"
                        style="background: #dc2626; color: #fff; border: 0; min-width: 170px;">
                    <i class="bi bi-cart-plus-fill"></i> Thêm vào Giỏ
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     RELATED PRODUCTS
     ============================================ -->
<?php if (!empty($relatedProducts)): ?>
<section class="related-products-section py-5">
    <div class="container">
        <h3 class="text-center mb-4 fw-bold">Các sản phẩm khác</h3>
        <div class="row">
            <?php foreach ($relatedProducts as $relProd): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card product-card h-100 shadow-sm">
                        <a href="?page=product&id=<?= $relProd['id'] ?>">
                            <img src="<?= $relProd['primary_image'] ?? $relProd['image'] ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($relProd['name']) ?>"
                                 style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="?page=product&id=<?= $relProd['id'] ?>" 
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($relProd['name']) ?>
                                </a>
                            </h6>
                            <p class="card-text text-primary fw-bold mb-2">
                                <?= number_format($relProd['price'], 0, ',', '.') ?>đ
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php
                                    $relRating = $relProd['average_rating'] ?? 0;
                                    echo str_repeat('★', floor($relRating));
                                    echo str_repeat('☆', 5 - floor($relRating));
                                    ?>
                                </small>
                                <a href="?page=product&id=<?= $relProd['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    Xem thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white" data-bs-dismiss="modal" style="z-index: 10;"></button>
                <img src="" id="modalImage" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<script>
// Base price từ PHP
const basePrice = <?= $product['price'] ?>;
let currentPriceModifier = 0;

// ========== CHANGE MAIN IMAGE ON THUMBNAIL CLICK ==========
function changeMainImage(imageUrl, thumbnailElement) {
    document.getElementById('mainProductImage').src = imageUrl;
    document.querySelectorAll('.thumbnail-item img').forEach(img => img.classList.remove('active'));
    thumbnailElement.classList.add('active');
}

// ========== QUANTITY INCREASE/DECREASE ==========
document.getElementById('increaseQty').addEventListener('click', function() {
    const input = document.getElementById('productQuantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) input.value = current + 1;
});

document.getElementById('decreaseQty').addEventListener('click', function() {
    const input = document.getElementById('productQuantity');
    const current = parseInt(input.value);
    if (current > 1) input.value = current - 1;
});

// ========== UPDATE PRICE WHEN STORAGE VARIANT CHANGES ==========
document.querySelectorAll('.variant-radio[name="storage"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const priceModifier = parseFloat(this.dataset.priceModifier) || 0;
            currentPriceModifier = priceModifier;
            updateDisplayPrice();
            const stock = this.dataset.stock;
            document.getElementById('stockDisplay').textContent = stock;
            document.getElementById('productQuantity').max = stock;
        }
    });
});

// ========== UPDATE COLOR NAME WHEN COLOR CHANGES ==========
document.querySelectorAll('.color-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const colorName = this.dataset.colorName;
            document.getElementById('selectedColorName').textContent = colorName;
        }
    });
});

// ========== UPDATE PRICE DISPLAY ==========
function updateDisplayPrice() {
    const finalPrice = basePrice + currentPriceModifier;
    document.getElementById('displayPrice').textContent = finalPrice.toLocaleString('vi-VN') + 'đ';
}

// ========== COLOR SWATCH ACTIVE STATE ==========
document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', function() {
        document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active-color'));
        this.classList.add('active-color');
    });
});

// ========== ADD TO CART WITH VARIANTS & QUANTITY ==========
document.querySelector('.add-to-cart-btn')?.addEventListener('click', function() {
    const productId = this.getAttribute('data-product-id');
    const quantity = document.getElementById('productQuantity').value;
    const selectedStorage = document.querySelector('.variant-radio[name="storage"]:checked');
    const storageId = selectedStorage ? selectedStorage.value : null;
    const selectedColor = document.querySelector('.color-radio:checked');
    const colorId = selectedColor ? selectedColor.value : null;
    const selectedRam = document.querySelector('.variant-radio[name="ram"]:checked');
    const ramId = selectedRam ? selectedRam.value : null;
    
    if (typeof cart !== 'undefined') {
        cart.addToCart(productId, quantity, {
            storage_id: storageId,
            color_id: colorId,
            ram_id: ramId
        });
    } else {
        console.error('Cart object not found. Make sure cart.js is loaded.');
    }
});

// ========== REVIEW SYSTEM ==========
const ratingStars = document.querySelectorAll('.rating-star');
const ratingValue = document.getElementById('ratingValue');
const ratingError = document.getElementById('ratingError');

if (ratingStars.length > 0) {
    ratingStars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            highlightStars(parseInt(this.dataset.rating));
        });
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingValue.value = rating;
            if (ratingError) ratingError.style.display = 'none';
        });
    });
    
    document.querySelector('.rating-input')?.addEventListener('mouseleave', function() {
        highlightStars(parseInt(ratingValue.value) || 0);
    });
}

function highlightStars(rating) {
    ratingStars.forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        star.classList.remove('bi-star', 'bi-star-fill', 'text-muted', 'text-warning');
        if (starRating <= rating) {
            star.classList.add('bi-star-fill', 'text-warning');
        } else {
            star.classList.add('bi-star', 'text-muted');
        }
    });
}

// ========== IMAGE UPLOAD - ĐÃ SỬA ==========
const dropzone = document.getElementById('imageDropzone');
const fileInput = document.getElementById('reviewImagesInput');
const imagePreview = document.getElementById('imagePreview');
let selectedFiles = []; // ✅ Lưu files vào array

if (dropzone && fileInput) {
    // Click to select files
    dropzone.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });
    
    // ✅ QUAN TRỌNG: Ngăn browser mở ảnh
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault(); // ✅ Bắt buộc phải có
        e.stopPropagation();
        dropzone.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    
    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    
    // ✅ SỬA: Drop event handler
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault(); // ✅ QUAN TRỌNG: Ngăn browser mở ảnh
        e.stopPropagation();
        dropzone.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        
        const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
        handleImageFiles(files);
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleImageFiles(files);
    });
}

// ✅ SỬA: Lưu files vào biến global
function handleImageFiles(files) {
    if (files.length > 5) {
        showAlert('danger', 'Maximum 5 images allowed');
        return;
    }
    
    // Validate file size (5MB each)
    const maxSize = 5 * 1024 * 1024;
    for (let file of files) {
        if (file.size > maxSize) {
            showAlert('danger', `File ${file.name} is too large (max 5MB)`);
            return;
        }
    }
    
    // ✅ Lưu files vào biến
    selectedFiles = files;
    
    // Preview images
    imagePreview.innerHTML = '';
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'd-inline-block position-relative me-1 mb-1';
            div.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0" 
                        style="width: 20px; height: 20px; font-size: 12px;" data-index="${index}">×</button>
            `;
            
            // Delete button handler
            div.querySelector('button').addEventListener('click', function() {
                const fileIndex = parseInt(this.dataset.index);
                selectedFiles = Array.from(selectedFiles).filter((_, i) => i !== fileIndex);
                div.remove();
            });
            
            imagePreview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// ✅ SỬA: Form submission với AJAX
const reviewForm = document.getElementById('reviewForm');
if (reviewForm) {
    reviewForm.addEventListener('submit', async function(e) {
        e.preventDefault(); // ✅ QUAN TRỌNG: Ngăn form submit thông thường
        
        if (!ratingValue.value) {
            ratingError.style.display = 'block';
            ratingError.textContent = 'Please select rating';
            return;
        }
        
        const submitBtn = document.getElementById('submitReviewBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
        
        try {
            // ✅ Tạo FormData mới và add files từ biến
            const formData = new FormData();
            formData.append('product_id', document.querySelector('input[name="product_id"]').value);
            formData.append('rating', ratingValue.value);
            formData.append('review_title', document.querySelector('input[name="review_title"]').value);
            formData.append('review_text', document.querySelector('textarea[name="review_text"]').value);
            
            // ✅ Add selected images
            selectedFiles.forEach((file, index) => {
                formData.append('review_images[]', file);
            });
            
            console.log('Sending review...'); // Debug
            
            const response = await fetch('ajax/submit_review.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response received:', response); // Debug
            
            const result = await response.json();
            console.log('Result:', result); // Debug
            
            if (result.success) {
                showAlert('success', result.message);
                reviewForm.reset();
                imagePreview.innerHTML = '';
                selectedFiles = [];
                ratingValue.value = '';
                highlightStars(0);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('danger', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('danger', 'Error occurred: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

function showAlert(type, message) {
    const alertDiv = document.getElementById('reviewAlert');
    if (alertDiv) {
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        alertDiv.style.display = 'block';
        setTimeout(() => alertDiv.style.display = 'none', 5000);
    }
}

function openImageModal(imageSrc) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    document.getElementById('modalImage').src = imageSrc;
    modal.show();
}
</script>


<?php include 'views/layouts/footer.php'; ?>


<div data-base-price="<?= $product['price'] ?>" style="display:none;"></div>
