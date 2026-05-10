<?php 
// File: views/client/cart.php
include 'views/layouts/header.php';

// Cart items from session
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$cartTotal = 0;
$itemCount = 0;

foreach ($cartItems as $productId => $item) {
    // Đảm bảo có price và quantity
    $price = isset($item['price']) ? floatval($item['price']) : 0;
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
    
    $subtotal = $price * $quantity;
    $cartTotal += $subtotal;
    $itemCount += $quantity;
    
    $cartItems[$productId]['subtotal'] = $subtotal;
}

// Calculate discount if coupon is applied
$discountAmount = 0;
$appliedCoupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
if ($appliedCoupon) {
    $discountPercent = floatval($appliedCoupon['discount_percent']);
    $discountAmount = $cartTotal * ($discountPercent / 100);
}
$finalTotal = $cartTotal - $discountAmount;
?>



<!-- Cart Content -->
<section class="cart-section py-5">
    <div class="container">
        
        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart Message -->
            <div class="empty-cart-container text-center py-5">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: #ccc;"></i>
                <h3 class="mt-4 mb-3">Giỏ hàng của bạn đang trống</h3>
                <p class="text-muted mb-4">Thêm sản phẩm vào giỏ để bắt đầu mua sắm!</p>
                <a href="?page=shop" class="btn btn-danger btn-lg">
                    <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
        
        <?php else: ?>
            <!-- Cart Table -->
            <div class="row">
                <!-- Cart Items (Left Column) -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-cart3"></i> Giỏ hàng 
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Giá</th>
                                            <th style="width: 150px;">Số lượng</th>
                                            <th>Tạm tính</th>
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
                                                            $imgUrl = resolveImagePath($item['image']); 
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
                                                                <br><small class="text-muted">Thương hiệu: <?= htmlspecialchars($item['brand']) ?></small>
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
                                                    <small class="text-muted">Còn hàng: <?= $item['stock'] ?></small>
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
                                                            title="Xóa">
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
                        <a href="?page=shop" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <button class="btn btn-danger" id="clearCartBtn">
                            <i class="bi bi-trash"></i> Xóa Giỏ hàng
                        </button>
                    </div>
                </div>
                
                <!-- Cart Totals (Right Column) -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top cart-summary-card" style="position: sticky; top: 100px; z-index: 500; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Tổng đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Tạm tính:</span>
                                <strong id="cartSubtotal"><?= number_format($cartTotal, 0, ',', '.') ?>đ</strong>
                            </div>
                            
                            <!-- Shipping -->
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            
                            <!-- Discount -->
                            <?php if ($appliedCoupon): ?>
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span>Mã giảm giá (<?= htmlspecialchars($appliedCoupon['code']) ?> - <?= $appliedCoupon['discount_percent'] ?>%):</span>
                                <span class="text-success">-<?= number_format($discountAmount, 0, ',', '.') ?>đ</span>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Tax -->
                            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                                <span>Thuế (0%):</span>
                                <span>0đ</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="mb-0">Tổng cộng:</h5>
                                <h5 class="text-danger mb-0" id="cartTotal">
                                    <?= number_format($finalTotal, 0, ',', '.') ?>đ
                                </h5>
                            </div>
                            
                            <!-- Checkout Button -->
                            <div class="d-grid gap-2">
                                <a href="?page=checkout" class="btn btn-danger btn-lg">
                                    <i class="bi bi-credit-card"></i> Tiến hành thanh toán
                                </a>
                            </div>
                            
                            <!-- Coupon Code (Optional) -->
                            <div class="mt-4">
                                <label class="form-label small">Bạn có mã giảm giá?</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" placeholder="Mã giảm giá" id="couponInput">
                                    <button class="btn btn-outline-danger" type="button" id="applyCouponBtn">
                                        Áp dụng
                                    </button>
                                </div>
                                <div id="couponMessage" class="form-text mt-2 text-danger" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods Info -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block mb-2">Chúng tôi chấp nhận</small>
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
 * Cart Page - Quantity, Remove, and Clear Handlers
 */

document.addEventListener('click', function(e) {
    const target = e.target;

    const increaseBtn = target.closest('.btn-increase');
    if (increaseBtn) {
        e.preventDefault();
        const productId = increaseBtn.getAttribute('data-product-id');
        const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
        if (input) {
            const maxStock = parseInt(increaseBtn.getAttribute('data-max-stock')) || Infinity;
            let quantity = parseInt(input.value) || 1;
            if (quantity < maxStock) {
                quantity += 1;
                input.value = quantity;
                updateCartQuantity(productId, quantity);
            }
        }
        return;
    }

    const decreaseBtn = target.closest('.btn-decrease');
    if (decreaseBtn) {
        e.preventDefault();
        const productId = decreaseBtn.getAttribute('data-product-id');
        const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
        if (input) {
            let quantity = parseInt(input.value) || 1;
            if (quantity > 1) {
                quantity -= 1;
                input.value = quantity;
                updateCartQuantity(productId, quantity);
            }
        }
        return;
    }

    const removeBtn = target.closest('.btn-remove');
    if (removeBtn) {
        e.preventDefault();
        const productId = removeBtn.getAttribute('data-product-id');
        if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ?')) {
            removeCartItem(productId);
        }
        return;
    }

    const clearBtn = target.closest('#clearCartBtn');
    if (clearBtn) {
        e.preventDefault();
        if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ?')) {
            clearCart();
        }
    }
});

const couponInput = document.getElementById('couponInput');
const applyCouponBtn = document.getElementById('applyCouponBtn');
const couponMessage = document.getElementById('couponMessage');

applyCouponBtn?.addEventListener('click', async function () {
    const code = couponInput?.value.trim() || '';
    if (!code) {
        showCouponMessage('Vui lòng nhập mã giảm giá.', false);
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'apply_coupon');
        formData.append('coupon_code', code);

        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showCouponMessage(data.message || 'Mã giảm giá hợp lệ.', true);
            // Reload to update totals with discount
            setTimeout(() => location.reload(), 1000);
        } else {
            showCouponMessage(data.message || 'Mã giảm giá không tồn tại.', false);
        }
    } catch (error) {
        console.error('Error applying coupon:', error);
        showCouponMessage('Lỗi mạng. Vui lòng thử lại.', false);
    }
});

function showCouponMessage(message, isSuccess) {
    if (!couponMessage) return;
    couponMessage.textContent = message;
    couponMessage.style.display = 'block';
    couponMessage.classList.toggle('text-success', isSuccess);
    couponMessage.classList.toggle('text-danger', !isSuccess);
}

async function updateCartQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Cập nhật giỏ hàng không thành công: ' + (data.message || 'Lỗi không xác định'));
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        alert('Lỗi mạng. Vui lòng thử lại.');
    }
}

async function removeCartItem(productId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);

    try {
        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert('Xóa sản phẩm không thành công: ' + (data.message || 'Lỗi không xác định'));
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Lỗi mạng. Vui lòng thử lại.');
    }
}

async function clearCart() {
    const formData = new FormData();
    formData.append('action', 'clear');

    try {
        const response = await fetch('ajax/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert('Xóa giỏ hàng không thành công: ' + (data.message || 'Lỗi không xác định'));
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        alert('Lỗi mạng. Vui lòng thử lại.');
    }
}
</script>

<?php include 'views/layouts/footer.php'; ?>
