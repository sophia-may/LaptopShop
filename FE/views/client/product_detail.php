<?php 
// File: views/client/product_detail.php
include 'views/layouts/header.php'; 
function numberToWords($num) {
    $words = ['', 'one', 'two', 'three', 'four', 'five'];
    return $words[$num] ?? '';
}
?>
<!-- ============================================
     BREADCRUMB SECTION
     ============================================ -->
<section class="breadcrumb-section py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="?page=home">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="?page=shop">Shop</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($product['name']) ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<!-- ============================================
     PRODUCT DETAIL SECTION
     ============================================ -->
<section class="product-detail-section py-5">
    <div class="container">
        <div class="row">
            
            <!-- ============ LEFT: PRODUCT IMAGES ============ -->
            <div class="col-lg-6 mb-4">
                <div class="product-images-wrapper">
                    <!-- Main Image Display -->
                    <div class="main-image-container mb-3">
                        <img src="<?= htmlspecialchars($productImages[0]['image_url']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="img-fluid rounded shadow-sm"
                             id="mainProductImage"
                             class="main-product-img">
                    </div>
                    
                    <!-- Thumbnail Images Gallery -->
                    <div class="thumbnail-gallery d-flex gap-2 flex-wrap">
                        <?php foreach ($productImages as $index => $image): ?>
                            <div class="thumbnail-item">
                                <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                     alt="Product view <?= $index + 1 ?>" 
                                     class="img-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                     onclick="changeMainImage('<?= htmlspecialchars($image['image_url']) ?>', this)"
                                     data-index="<?= $index ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- ============ RIGHT: PRODUCT INFO ============ -->
            <div class="col-lg-6">
                <div class="product-info-wrapper">
                    
                    <!-- Product Title -->
                    <h1 class="product-title mb-3 fw-bold">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>
                    
                    <!-- Brand Badge -->
                    <div class="product-brand mb-3">
                        <span class="badge bg-secondary">
                            <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($product['brand']) ?>
                        </span>
                    </div>
                    
                    <!-- Price Display -->
                    <div class="product-pricing mb-3">
                        <h2 class="price-display text-primary fw-bold mb-0" id="displayPrice">
                            <?= number_format($displayPrice, 0, ',', '.') ?>đ
                        </h2>
                        <small class="text-muted">Base price: <?= number_format($product['price'], 0, ',', '.') ?>đ</small>
                    </div>
                    
                    <!-- ✅ Rating & Reviews - ĐÃ SỬA -->
                    <div class="product-rating mb-4 d-flex align-items-center gap-3 border-top border-bottom py-3">
                        <div class="stars-display text-warning fs-5">
                            <?php
                            $fullStars = floor($averageRating);
                            $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                            $emptyStars = 5 - $fullStars - $halfStar;
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="bi bi-star-fill"></i>';
                            }
                            if ($halfStar) {
                                echo '<i class="bi bi-star-half"></i>';
                            }
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="rating-text">
                            <strong><?= number_format($averageRating, 1) ?></strong>
                            <span class="text-muted">(<?= $reviewCount ?> reviews)</span>
                        </div>
                    </div>
                    
                    <!-- Short Description -->
                    <div class="product-description mb-4">
                        <p class="text-muted">
                            <?= htmlspecialchars($product['description']) ?>
                        </p>
                    </div>
                    
                    <!-- ============ STORAGE VARIANTS ============ -->
                    <?php if (!empty($variants['storage'])): ?>
                        <div class="variant-section mb-4">
                            <h6 class="variant-label fw-bold mb-2">
                                <i class="bi bi-hdd-fill text-primary"></i> Storage:
                            </h6>
                            <div class="btn-group flex-wrap" role="group" id="storageVariants">
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
                                    <label class="btn btn-outline-primary" for="storage<?= $storage['id'] ?>">
                                        <?= htmlspecialchars($storage['variant_name']) ?>
                                        <?php if ($storage['price_modifier'] > 0): ?>
                                            <small class="d-block text-success">
                                                +<?= number_format($storage['price_modifier'], 0, ',', '.') ?>đ
                                            </small>
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- ============ COLOR VARIANTS ============ -->
                    <?php if (!empty($variants['color'])): ?>
                        <div class="variant-section mb-4">
                            <h6 class="variant-label fw-bold mb-2">
                                <i class="bi bi-palette-fill text-primary"></i> Color:
                                <span id="selectedColorName" class="text-muted fw-normal">
                                    <?= $variants['color'][0]['variant_name'] ?>
                                </span>
                            </h6>
                            <div class="color-variants-wrapper d-flex gap-2">
                                <?php foreach ($variants['color'] as $index => $color): ?>
                                    <div class="color-option-wrapper">
                                        <input type="radio" 
                                               class="btn-check color-radio" 
                                               name="color" 
                                               id="color<?= $color['id'] ?>" 
                                               value="<?= $color['id'] ?>"
                                               data-color-name="<?= htmlspecialchars($color['variant_name']) ?>"
                                               data-stock="<?= $color['stock'] ?>"
                                               autocomplete="off"
                                               <?= $index === 0 ? 'checked' : '' ?>>
                                        <label class="color-swatch" 
                                               for="color<?= $color['id'] ?>"
                                               style="background-color: <?= htmlspecialchars($color['variant_value']) ?>;"
                                               title="<?= htmlspecialchars($color['variant_name']) ?>">
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- ============ RAM VARIANTS (if exists) ============ -->
                    <?php if (!empty($variants['ram'])): ?>
                        <div class="variant-section mb-4">
                            <h6 class="variant-label fw-bold mb-2">
                                <i class="bi bi-memory text-primary"></i> RAM:
                            </h6>
                            <div class="btn-group flex-wrap" role="group">
                                <?php foreach ($variants['ram'] as $ram): ?>
                                    <input type="radio" 
                                           class="btn-check variant-radio" 
                                           name="ram" 
                                           id="ram<?= $ram['id'] ?>" 
                                           value="<?= $ram['id'] ?>"
                                           data-stock="<?= $ram['stock'] ?>"
                                           autocomplete="off"
                                           <?= $ram['is_default'] ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="ram<?= $ram['id'] ?>">
                                        <?= htmlspecialchars($ram['variant_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- ============ QUANTITY SELECTOR ============ -->
                    <div class="quantity-section mb-4">
                        <h6 class="fw-bold mb-2">Quantity:</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group" style="width: 150px;">
                                <button class="btn btn-outline-secondary" type="button" id="decreaseQty">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" 
                                       class="form-control text-center fw-bold" 
                                       value="1" 
                                       min="1" 
                                       max="<?= $product['stock'] ?>" 
                                       id="productQuantity" 
                                       readonly>
                                <button class="btn btn-outline-secondary" type="button" id="increaseQty">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-box-seam"></i> 
                                Available: <strong id="stockDisplay"><?= $product['stock'] ?></strong> items
                            </small>
                        </div>
                    </div>
                    
                    <!-- ============ ACTION BUTTONS ============ -->
                    <div class="action-buttons mb-4 d-flex gap-2">
                        <button class="btn btn-primary btn-lg flex-grow-1 add-to-cart-btn" 
                                data-product-id="<?= $product['id'] ?>">
                            <i class="bi bi-cart-plus-fill"></i> Add To Cart
                        </button>
                        <button class="btn btn-outline-secondary btn-lg" 
                                title="Add to Wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-lg" 
                                title="Compare">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                    </div>
                    
                    <!-- ============ PRODUCT META INFO ============ -->
                    <div class="product-meta border-top pt-3">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 30%;">
                                        <strong>SKU:</strong>
                                    </td>
                                    <td>PROD<?= str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <strong>Category:</strong>
                                    </td>
                                    <td>
                                        <a href="?page=shop&category=<?= urlencode($product['category']) ?>" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($product['category']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <strong>Brand:</strong>
                                    </td>
                                    <td>
                                        <a href="?page=shop&brand[]=<?= urlencode($product['brand']) ?>" 
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($product['brand']) ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php if ($product['storage']): ?>
                                <tr>
                                    <td class="text-muted">
                                        <strong>Storage:</strong>
                                    </td>
                                    <td><?= htmlspecialchars($product['storage']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($product['ram']): ?>
                                <tr>
                                    <td class="text-muted">
                                        <strong>RAM:</strong>
                                    </td>
                                    <td><?= htmlspecialchars($product['ram']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- ============================================
     TABS: DESCRIPTION, ADDITIONAL INFO, REVIEWS
     ============================================ -->
<section class="product-tabs-section py-5 bg-light">
    <div class="container">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs nav-fill" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" 
                        id="description-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#description" 
                        type="button">
                    <i class="bi bi-file-text"></i> Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" 
                        id="additional-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#additional" 
                        type="button">
                    <i class="bi bi-info-circle"></i> Additional Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" 
                        id="reviews-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#reviews" 
                        type="button">
                    <i class="bi bi-chat-left-text"></i> Reviews (<?= $reviewCount ?>)
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content bg-white p-4 rounded-bottom shadow-sm" id="productTabsContent">
            
            <!-- ========== DESCRIPTION TAB ========== -->
            <div class="tab-pane fade show active" id="description" role="tabpanel">
                <h5 class="mb-3 fw-bold">Product Description</h5>
                <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                
                <?php if ($product['category'] == 'Phones'): ?>
                    <h6 class="mt-4 mb-3 fw-bold">Key Features:</h6>
                    <ul class="feature-list">
                        <li><strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?></li>
                        <?php if ($product['storage']): ?>
                            <li><strong>Storage:</strong> <?= htmlspecialchars($product['storage']) ?></li>
                        <?php endif; ?>
                        <?php if ($product['ram']): ?>
                            <li><strong>RAM:</strong> <?= htmlspecialchars($product['ram']) ?></li>
                        <?php endif; ?>
                        <li><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></li>
                        <li><strong>Stock:</strong> <?= $product['stock'] ?> units available</li>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- ========== ADDITIONAL INFORMATION TAB ========== -->
            <div class="tab-pane fade" id="additional" role="tabpanel">
                <h5 class="mb-3 fw-bold">Additional Information</h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%" class="bg-light">Brand</th>
                            <td><?= htmlspecialchars($product['brand']) ?></td>
                        </tr>
                        <?php if ($product['storage']): ?>
                        <tr>
                            <th class="bg-light">Storage Options</th>
                            <td>
                                <?php 
                                $storageOptions = array_column($variants['storage'], 'variant_name');
                                echo implode(', ', $storageOptions);
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($variants['color'])): ?>
                        <tr>
                            <th class="bg-light">Available Colors</th>
                            <td>
                                <?php 
                                $colorOptions = array_column($variants['color'], 'variant_name');
                                echo implode(', ', $colorOptions);
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($product['ram']): ?>
                        <tr>
                            <th class="bg-light">RAM</th>
                            <td><?= htmlspecialchars($product['ram']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="bg-light">Category</th>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Availability</th>
                            <td>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success">In Stock (<?= $product['stock'] ?> items)</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Average Rating</th>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= round($averageRating) ? '-fill' : '' ?> text-warning"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <strong><?= number_format($averageRating, 1) ?>/5.0</strong>
                                    <span class="text-muted">(<?= $reviewCount ?> reviews)</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- ========== REVIEWS TAB (PLACEHOLDER) ========== -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <div class="row">
                    <!-- Left: Stats & Form -->
                    <div class="col-lg-4 mb-4">
                        <!-- Review Stats -->
                        <div class="card shadow-sm mb-3">
                            <div class="card-body text-center">
                                <h2 class="display-4 fw-bold text-warning mb-2"><?= number_format($averageRating, 1) ?></h2>
                                <div class="stars-display text-warning fs-4 mb-2">
                                    <?php
                                    $fullStars = floor($averageRating);
                                    $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                                    $emptyStars = 5 - $fullStars - $halfStar;
                                    
                                    for ($i = 0; $i < $fullStars; $i++) echo '<i class="bi bi-star-fill"></i>';
                                    if ($halfStar) echo '<i class="bi bi-star-half"></i>';
                                    for ($i = 0; $i < $emptyStars; $i++) echo '<i class="bi bi-star"></i>';
                                    ?>
                                </div>
                                <p class="text-muted mb-0"><?= $reviewCount ?> reviews</p>
                            </div>
                        </div>
                        
                        <!-- Rating Breakdown -->
                        <?php if ($reviewStats && $reviewStats['total_reviews'] > 0): ?>
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <?php 
                                $starLabels = ['five_star' => 5, 'four_star' => 4, 'three_star' => 3, 'two_star' => 2, 'one_star' => 1];
                                foreach ($starLabels as $key => $starNum): 
                                    $starCount = $reviewStats[$key];
                                    $percentage = ($starCount / $reviewStats['total_reviews']) * 100;
                                ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span style="width: 50px; font-size: 13px;"><?= $starNum ?> ★</span>
                                    <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <span class="text-muted" style="width: 30px; font-size: 13px;"><?= $starCount ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Review Form -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-3">Write Your Review</h6>
                                <form id="reviewForm" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    
                                    <!-- Rating -->
                                    <div class="mb-3">
                                        <label class="form-label small">Rating <span class="text-danger">*</span></label>
                                        <div class="rating-input d-flex gap-1 fs-4">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star text-muted rating-star" data-rating="<?= $i ?>" style="cursor: pointer;"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingValue" required>
                                        <div class="invalid-feedback d-block" id="ratingError" style="display: none;"></div>
                                    </div>
                                    
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label class="form-label small">Title</label>
                                        <input type="text" class="form-control form-control-sm" name="review_title" maxlength="255">
                                    </div>
                                    
                                    <!-- Review Text -->
                                    <div class="mb-3">
                                        <label class="form-label small">Review <span class="text-danger">*</span></label>
                                        <textarea class="form-control form-control-sm" name="review_text" rows="4" required></textarea>
                                    </div>
                                    
                                    <!-- Images -->
                                    <div class="mb-3">
                                        <label class="form-label small">Images (Max 5)</label>
                                        <div id="imageDropzone" class="border border-dashed rounded p-3 text-center bg-light" style="cursor: pointer;">
                                            <i class="bi bi-cloud-upload fs-3 text-muted"></i>
                                            <p class="mb-0 small">Drop images or click</p>
                                        </div>
                                        <input type="file" id="reviewImagesInput" name="review_images[]" accept="image/*" multiple style="display: none;">
                                        <div id="imagePreview" class="mt-2"></div>
                                    </div>
                                    
                                    <div id="reviewAlert" style="display: none;"></div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100" id="submitReviewBtn">Submit Review</button>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info small">
                            <a href="index.php?page=login_signup">Login</a> to write a review
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Right: Reviews List -->
                    <div class="col-lg-8">
                        <?php
                        echo "<!-- DEBUG from View -->";
                        echo "<!-- isset(\$reviews): " . (isset($reviews) ? 'YES' : 'NO') . " -->";
                        echo "<!-- count(\$reviews): " . (isset($reviews) ? count($reviews) : '0') . " -->";
                        if (isset($reviews) && !empty($reviews)) {
                        echo "<!-- First Review Keys: " . implode(', ', array_keys($reviews[0])) . " -->";
                        }
                        ?>
                        <?php if (count($reviews) > 0): ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex gap-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?= strtoupper(mb_substr($review['user_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <?= htmlspecialchars($review['user_name']) ?>
                                                    <?php if ($review['is_verified_purchase']): ?>
                                                    <span class="badge bg-success" style="font-size: 10px;">✓ Verified</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                                            </div>
                                        </div>
                                        <div class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($review['review_title'])): ?>
                                    <h6><?= htmlspecialchars($review['review_title']) ?></h6>
                                    <?php endif; ?>
                                    
                                    <p class="mb-2">
                                        <?= nl2br(htmlspecialchars($review['review_text'] ?? '')) ?>
                                    </p>
                                    
                                    <?php 
                                    $reviewImages = json_decode($review['review_images'], true);
                                    if (!empty($reviewImages) && is_array($reviewImages)): 
                                    ?>
                                    <div class="d-flex gap-2 mb-2">
                                        <?php foreach ($reviewImages as $image): ?>
                                        <img src="assets/img/reviews/<?= htmlspecialchars($image) ?>" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                             onclick="openImageModal('assets/img/reviews/<?= htmlspecialchars($image) ?>')">
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($review['admin_reply'])): ?>
                                    <div class="bg-light p-2 rounded mt-2 border-start border-3 border-primary">
                                        <strong class="text-primary small">Shop Reply:</strong>
                                        <p class="mb-0 small"><?= nl2br(htmlspecialchars($review['admin_reply'])) ?></p>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($review['admin_reply_at'])) ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">No reviews yet. Be the first!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
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
        <h3 class="text-center mb-4 fw-bold">Related Products</h3>
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
                                    View
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
