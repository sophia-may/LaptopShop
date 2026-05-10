<?php 
// File: views/client/cart.php
include 'views/layouts/header.php';

// Cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// ✅ Tính total MANUALLY (reliable)
$cartTotal = 0;
$itemCount = 0;

foreach ($cartItems as $productId => $item) {
    // Đảm bảo có price và quantity
    $price = isset($item['price']) ? floatval($item['price']) : 0;
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
    
    $subtotal = $price * $quantity;
    $cartTotal += $subtotal;
    $itemCount += $quantity;
    
    // ✅ Thêm subtotal vào item để dùng trong view
    $cartItems[$productId]['subtotal'] = $subtotal;
}
?>

<!-- Hero Section -->
<div class="page-hero text-center">
    <div class="container">
        <h1 class="hero-title">Cart</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                <li class="breadcrumb-item"><a href="?page=shop">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Cart Content -->
<section class="cart-section py-5">
    <div class="container">
        
        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart Message -->
            <div class="empty-cart-container text-center py-5">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: #ccc;"></i>
                <h3 class="mt-4 mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Add some products to get started!</p>
                <a href="?page=shop" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
            </div>
        
        <?php else: ?>
            <!-- Cart Table -->
            <div class="row">
                <!-- Cart Items (Left Column) -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-cart3"></i> Shopping Cart 
                                <span class="badge bg-light text-dark ms-2"><?= $itemCount ?> items</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th style="width: 150px;">Quantity</th>
                                            <th>Subtotal</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $productId => $item): ?>
                                            <tr data-product-id="<?= $productId ?>">
                                                <!-- Product Info -->
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php 
                                                            $imgUrl = (strpos($item['image'], 'http') === 0) 
                                                                ? $item['image'] 
                                                                : 'assets/img/' . $item['image']; 
                                                        ?>
                                                        <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                                             class="me-3">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <a href="?page=product&id=<?= $item['id'] ?>" 
                                                                   class="text-decoration-none text-dark">
                                                                    <?= htmlspecialchars($item['name']) ?>
                                                                </a>
                                                            </h6>
                                                            <small class="text-muted">
                                                                <?= htmlspecialchars($item['category']) ?>
                                                            </small>
                                                            <?php if (!empty($item['brand'])): ?>
                                                                <br><small class="text-muted">Brand: <?= htmlspecialchars($item['brand']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <!-- Price -->
                                                <td class="align-middle">
                                                    <strong><?= number_format($item['price'], 0, ',', '.') ?>đ</strong>
                                                </td>
                                                
                                                <!-- Quantity Controls -->
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary btn-decrease" 
                                                                type="button" 
                                                                data-product-id="<?= $productId ?>">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" 
                                                               class="form-control text-center quantity-input" 
                                                               value="<?= $item['quantity'] ?>" 
                                                               min="1" 
                                                               max="<?= $item['stock'] ?>"
                                                               data-product-id="<?= $productId ?>"
                                                               readonly>
                                                        <button class="btn btn-outline-secondary btn-increase" 
                                                                type="button" 
                                                                data-product-id="<?= $productId ?>"
                                                                data-max-stock="<?= $item['stock'] ?>">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Stock: <?= $item['stock'] ?></small>
                                                </td>
                                                
                                                <!-- Subtotal -->
                                                <td class="align-middle">
                                                    <strong class="item-subtotal">
                                                        <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                                                    </strong>
                                                </td>
                                                
                                                <!-- Remove Button -->
                                                <td class="align-middle">
                                                    <button class="btn btn-sm btn-danger btn-remove" 
                                                            data-product-id="<?= $productId ?>"
                                                            title="Remove">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="?page=shop" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" id="clearCartBtn">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
                
                <!-- Cart Totals (Right Column) -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="position: sticky; top: 100px; z-index: 500; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Cart Totals</h5>
                        </div>
                        <div class="card-body">
                            <!-- ✅ SUBTOTAL -->
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Subtotal:</span>
                                <strong id="cartSubtotal"><?= number_format($cartTotal, 0, ',', '.') ?>đ</strong>
                            </div>
                            
                            <!-- Shipping -->
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Shipping:</span>
                                <span class="text-success">Free</span>
                            </div>
                            
                            <!-- Tax -->
                            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                                <span>Tax (0%):</span>
                                <span>0đ</span>
                            </div>
                            
                            <!-- ✅ TOTAL -->
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="mb-0">Total:</h5>
                                <h5 class="text-primary mb-0" id="cartTotal">
                                    <?= number_format($cartTotal, 0, ',', '.') ?>đ
                                </h5>
                            </div>
                            
                            <!-- Checkout Button -->
                            <div class="d-grid gap-2">
                                <a href="?page=checkout" class="btn btn-primary btn-lg">
                                    <i class="bi bi-credit-card"></i> Proceed to Checkout
                                </a>
                            </div>
                            
                            <!-- Coupon Code (Optional) -->
                            <div class="mt-4">
                                <label class="form-label small">Have a coupon?</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" placeholder="Coupon code" id="couponInput">
                                    <button class="btn btn-outline-secondary" type="button" id="applyCouponBtn">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods Info -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block mb-2">We accept</small>
                            <div>
                                <i class="bi bi-credit-card fs-4 text-muted me-2"></i>
                                <i class="bi bi-paypal fs-4 text-muted me-2"></i>
                                <i class="bi bi-wallet2 fs-4 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</section>

<!-- Cart Page JavaScript -->
<script>
/**
 * Cart Page - Quantity & Remove Item Handlers
 * Sử dụng event delegation để tránh duplicate listeners
 */

// ✅ Event Delegation: Dùng 1 listener cho toàn bộ document
document.addEventListener('click', function(e) {
    const target = e.target;
    
    // ✅ Handle qty-plus button (hoặc icon bên trong)
    const plusBtn = target.closest('.qty-plus');
    if (plusBtn) {
        e.preventDefault();
        const productId = plusBtn.getAttribute('data-product-id');
        const input = document.querySelector(`.qty-value[data-product-id="${productId}"]`);
        
        if (input) {
            let quantity = parseInt(input.value) || 1;
            quantity++; // Tăng 1
            input.value = quantity;
            updateCartQuantity(productId, quantity);
        }
        return;
    }
    
    // ✅ Handle qty-minus button
    const minusBtn = target.closest('.qty-minus');
    if (minusBtn) {
        e.preventDefault();
        const productId = minusBtn.getAttribute('data-product-id');
        const input = document.querySelector(`.qty-value[data-product-id="${productId}"]`);
        
        if (input) {
            let quantity = parseInt(input.value) || 1;
            if (quantity > 1) {
                quantity--; // Giảm 1
                input.value = quantity;
                updateCartQuantity(productId, quantity);
            }
        }
        return;
    }
    
    // ✅ Handle remove button
    const removeBtn = target.closest('.btn-remove-item');
    if (removeBtn) {
        e.preventDefault();
        const productId = removeBtn.getAttribute('data-product-id');
        if (confirm('Remove this product from cart?')) {
            removeCartItem(productId);
        }
        return;
    }
});

/**
 * Update cart quantity via AJAX
 */
async function updateCartQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('productid', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // ✅ Reload page để update totals
            location.reload();
        } else {
            alert('Failed to update cart: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        alert('Network error. Please try again.');
    }
}

/**
 * Remove cart item via AJAX
 */
async function removeCartItem(productId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('productid', productId);

    try {
        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Failed to remove item: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Network error. Please try again.');
    }
}
</script>

<?php include 'views/layouts/footer.php'; ?>
