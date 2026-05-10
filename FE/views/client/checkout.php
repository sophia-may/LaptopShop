<?php 
// File: views/client/checkout.php
include 'views/layouts/header.php';

$cartItems      = $cartItems ?? [];
$subtotal       = $subtotal ?? 0;
$shippingFee    = $shippingFee ?? 0;
$tax            = $tax ?? 0;
$discountAmount = $discountAmount ?? 0;
$couponCode     = $couponCode ?? '';
$appliedCoupon  = $appliedCoupon ?? null;
$total          = $total ?? 0;
?>



<!-- Checkout Content -->
<section class="checkout-section py-5">
    <div class="container checkout-container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning">
                <strong>Chú ý:</strong> Bạn cần đăng nhập để đặt hàng. Vui lòng đăng nhập trước khi hoàn tất thanh toán.
                <a href="index.php?page=login_signup" class="btn btn-sm btn-outline-danger ms-2">Đăng nhập ngay</a>
            </div>
        <?php endif; ?>
        
        <form id="checkoutForm" method="POST">
            <div class="row">
                
                <!-- Left Column - Customer & Shipping Info -->
                <div class="col-lg-7 mb-4">
                    
                    <!-- Customer Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-person-fill"></i> Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="customer_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Địa chỉ giao hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="shipping_address" class="form-label">Địa chỉ đường, số nhà <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                           placeholder="123 Nguyễn Trãi" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_ward" class="form-label">Phường/Xã</label>
                                    <input type="text" class="form-control" id="shipping_ward" name="shipping_ward" 
                                           placeholder="Phường 5">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_district" class="form-label">Quận/Huyện</label>
                                    <input type="text" class="form-control" id="shipping_district" name="shipping_district" 
                                           placeholder="Quận 1">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_city" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select class="form-select" id="shipping_city" name="shipping_city" required>
                                        <option value="">Chọn tỉnh/thành phố...</option>
                                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                        <option value="Hà Nội">Hà Nội</option>
                                        <option value="Đà Nẵng">Đà Nẵng</option>
                                        <option value="Cần Thơ">Cần Thơ</option>
                                        <option value="Hải Phòng">Hải Phòng</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-credit-card-fill"></i> Phương thức thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                                <label class="form-check-label" for="payment_cod">
                                    <strong>Thanh toán khi nhận hàng (COD)</strong>
                                    <br><small class="text-muted">Thanh toán sau khi nhận hàng</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                                <label class="form-check-label" for="payment_bank">
                                    <strong>Chuyển khoản ngân hàng</strong>
                                    <br><small class="text-muted">Chuyển khoản vào tài khoản của chúng tôi</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
                                <label class="form-check-label" for="payment_momo">
                                    <strong>Ví MoMo</strong>
                                    <br><small class="text-muted">Thanh toán qua ứng dụng MoMo</small>
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="credit_card">
                                <label class="form-check-label" for="payment_card">
                                    <strong>Thẻ tín dụng/ghi nợ</strong>
                                    <br><small class="text-muted">Visa, Mastercard, JCB</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Ghi chú đơn hàng (tùy chọn)</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú giao hàng, yêu cầu đặc biệt, quà tặng..."></textarea>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Right Column - Order Summary -->
                <div class="col-lg-5">
                    <div class="card shadow-sm checkout-summary-card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-basket-fill"></i> Tóm tắt đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            
                            <!-- Cart Items -->
                            <div class="order-items mb-3">
                                <?php foreach ($cartItems as $item): ?>
                                    <?php
                                    if (!isset($item['name']) || !isset($item['price']) || !isset($item['image']) || !isset($item['quantity'])) {
                                    error_log("Checkout.php - Skipping invalid item: " . print_r($item, true));
                                    continue;
                                    }
                                    ?>
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                            <small class="text-muted">
                                                <?= number_format($item['price'], 0, ',', '.') ?>đ × <?= $item['quantity'] ?>
                                            </small>
                                        </div>
                                        <strong><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <strong id="checkoutSubtotal"><?= number_format($subtotal, 0, ',', '.') ?>đ</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span id="checkoutShipping" class="<?= $shippingFee == 0 ? 'text-success' : '' ?>">
                                        <?= $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee, 0, ',', '.') . 'đ' ?>
                                    </span>
                                </div>
                                
                                <?php if (!empty($discountAmount) && $discountAmount > 0): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giảm giá mã <?= htmlspecialchars($couponCode ?? '') ?> (<?= number_format($appliedCoupon['discount_percent'] ?? 0, 0) ?>%):</span>
                                    <span class="text-success">-<?= number_format($discountAmount, 0, ',', '.') ?>đ</span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span>Thuế (0%):</span>
                                    <span>0đ</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-4">
                                    <h5 class="mb-0">Tổng cộng:</h5>
                                    <h5 class="text-danger mb-0" id="checkoutTotal">
                                        <?= number_format($total, 0, ',', '.') ?>đ
                                    </h5>
                                </div>
                            </div>
                            
                            <!-- Place Order Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger btn-lg" id="placeOrderBtn">
                                    <i class="bi bi-check-circle"></i> Đặt hàng
                                </button>
                                <a href="?page=cart" class="btn btn-outline-danger">
                                    <i class="bi bi-arrow-left"></i> Quay lại giỏ hàng
                                </a>
                            </div>
                            
                            <!-- Security Notice -->
                            <div class="alert alert-danger mt-3 mb-0" role="alert">
                                <small>
                                    <i class="bi bi-shield-check"></i> 
                                    Thông tin của bạn được bảo mật.
                                </small>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </form>
        
    </div>
</section>

<!-- Checkout JavaScript -->
<script>
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('placeOrderBtn');
    const originalText = submitBtn.innerHTML;
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
    
    // Get form data
    const formData = new FormData(form);
    formData.append('action', 'place_order');
    
    try {
        const response = await fetch('ajax/checkout_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            alert('✓ Đặt hàng thành công!\nMã đơn hàng: ' + data.order_id);
            
            // Redirect to order confirmation or home
            window.location.href = '?page=order_success&id=' + data.order_id;
        } else {
            alert('✗ Lỗi: ' + data.message);
            if (data.code === 'auth_required') {
                window.location.href = 'index.php?page=login_signup';
                return;
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('✗ Lỗi kết nối. Vui lòng thử lại.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Phone number formatting
document.getElementById('customer_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substr(0, 10);
    e.target.value = value;
});
</script>

<?php include 'views/layouts/footer.php'; ?>
