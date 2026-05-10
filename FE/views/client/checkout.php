<?php 
// File: views/client/checkout.php
include 'views/layouts/header.php';
?>

<!-- Hero Section -->
<div class="page-hero text-center">
    <div class="container">
        <h1 class="hero-title">Checkout</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                <li class="breadcrumb-item"><a href="?page=shop">Shop</a></li>
                <li class="breadcrumb-item"><a href="?page=cart">Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Checkout Content -->
<section class="checkout-section py-5">
    <div class="container">
        
        <form id="checkoutForm" method="POST">
            <div class="row">
                
                <!-- Left Column - Customer & Shipping Info -->
                <div class="col-lg-7 mb-4">
                    
                    <!-- Customer Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-fill"></i> Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="customer_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="shipping_address" class="form-label">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                           placeholder="123 Nguyen Trai Street" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_ward" class="form-label">Ward/Commune</label>
                                    <input type="text" class="form-control" id="shipping_ward" name="shipping_ward" 
                                           placeholder="Ward 5">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_district" class="form-label">District</label>
                                    <input type="text" class="form-control" id="shipping_district" name="shipping_district" 
                                           placeholder="District 1">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_city" class="form-label">City/Province <span class="text-danger">*</span></label>
                                    <select class="form-select" id="shipping_city" name="shipping_city" required>
                                        <option value="">Select city...</option>
                                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                        <option value="Hà Nội">Hà Nội</option>
                                        <option value="Đà Nẵng">Đà Nẵng</option>
                                        <option value="Cần Thơ">Cần Thơ</option>
                                        <option value="Hải Phòng">Hải Phòng</option>
                                        <option value="Khác">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-credit-card-fill"></i> Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                                <label class="form-check-label" for="payment_cod">
                                    <strong>Cash on Delivery (COD)</strong>
                                    <br><small class="text-muted">Pay when you receive the product</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                                <label class="form-check-label" for="payment_bank">
                                    <strong>Bank Transfer</strong>
                                    <br><small class="text-muted">Transfer to our bank account</small>
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
                                <label class="form-check-label" for="payment_momo">
                                    <strong>MoMo E-Wallet</strong>
                                    <br><small class="text-muted">Pay with MoMo app</small>
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="credit_card">
                                <label class="form-check-label" for="payment_card">
                                    <strong>Credit/Debit Card</strong>
                                    <br><small class="text-muted">Visa, Mastercard, JCB</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Order Notes (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Special delivery instructions, gift message, etc."></textarea>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Right Column - Order Summary -->
                <div class="col-lg-5">
                    <div class="card shadow-sm sticky-top" style="position: sticky; top: 100px; z-index: 500; max-height: calc(100vh - 120px); overflow-y: auto;">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-basket-fill"></i> Order Summary</h5>
                        </div>
                        <div class="card-body">
                            
                            <!-- Cart Items -->
                            <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($cartItems as $item): ?>
                                    <?php
                                    // ✅ Validation: Skip nếu thiếu thông tin quan trọng
                                    if (!isset($item['name']) || !isset($item['price']) || !isset($item['image']) || !isset($item['quantity'])) {
                                    error_log("Checkout.php - Skipping invalid item: " . print_r($item, true));
                                    continue;
                                    }
                                    ?>
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <?php 
                                            $imgUrl = (strpos($item['image'], 'http') === 0) 
                                                ? $item['image'] 
                                                : 'assets/img/' . $item['image']; 
                                        ?>
                                        <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;"
                                             class="me-3">
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
                                    <span>Subtotal:</span>
                                    <strong id="checkoutSubtotal"><?= number_format($subtotal, 0, ',', '.') ?>đ</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping Fee:</span>
                                    <span id="checkoutShipping" class="<?= $shippingFee == 0 ? 'text-success' : '' ?>">
                                        <?= $shippingFee == 0 ? 'Free' : number_format($shippingFee, 0, ',', '.') . 'đ' ?>
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span>Tax (0%):</span>
                                    <span>0đ</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-4">
                                    <h5 class="mb-0">Total:</h5>
                                    <h5 class="text-primary mb-0" id="checkoutTotal">
                                        <?= number_format($total, 0, ',', '.') ?>đ
                                    </h5>
                                </div>
                            </div>
                            
                            <!-- Place Order Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="placeOrderBtn">
                                    <i class="bi bi-check-circle"></i> Place Order
                                </button>
                                <a href="?page=cart" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Cart
                                </a>
                            </div>
                            
                            <!-- Security Notice -->
                            <div class="alert alert-info mt-3 mb-0" role="alert">
                                <small>
                                    <i class="bi bi-shield-check"></i> 
                                    Your information is secure and encrypted
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
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
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
            alert('✓ Order placed successfully!\nOrder ID: ' + data.order_id);
            
            // Redirect to order confirmation or home
            window.location.href = '?page=order_success&id=' + data.order_id;
        } else {
            alert('✗ Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('✗ Network error. Please try again.');
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
