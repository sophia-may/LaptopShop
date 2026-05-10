/**
 * Shopping Cart JavaScript
 * Handles: Add to cart, Update cart, Remove item, Mini cart dropdown
 */

class ShoppingCart {
    constructor() {
        this.cartIcon = document.getElementById('cartIconBtn');
        this.cartDropdown = document.getElementById('cartDropdown');
        this.cartBadge = document.getElementById('cartBadge');
        this.cartItemCount = document.getElementById('cartItemCount');
        this.cartDropdownBody = document.getElementById('cartDropdownBody');
        this.cartTotalAmount = document.getElementById('cartTotalAmount');
        
        this.init();
    }
    
    init() {
        // Load cart on page load
        this.updateMiniCart();
        
        // (Dropdown handled by Bootstrap 5 now)
        
        // Bind "Add to cart" buttons
        

        this.bindShopPageButtons();

    }

    bindShopPageButtons() {
        // Wait for DOM to be ready
        setTimeout(() => {
            // Only select buttons in product-card (shop grid)
            const shopButtons = document.querySelectorAll('.product-card .add-to-cart-btn');
            
            console.log(`[Cart.js] Binding ${shopButtons.length} shop buttons`);
            
            shopButtons.forEach(btn => {
                // Remove any existing listeners by cloning
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                // Add single event listener
                newBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const productId = newBtn.getAttribute('data-product-id');
                    const quantity = newBtn.getAttribute('data-quantity') || 1;
                    
                    console.log(`[Cart.js] Shop button clicked: Product ${productId}, Qty ${quantity}`);
                    
                    this.addToCart(productId, quantity);
                });
            });
        }, 100);
    }

    
    /**
     * Toggle cart dropdown
     */
    toggleDropdown() {
        this.cartDropdown.classList.toggle('show');
    }
    
    /**
     * Close cart dropdown
     */
    closeDropdown() {
        this.cartDropdown.classList.remove('show');
    }
    
    


    
    /**
     * Thêm sản phẩm vào giỏ hàng (AJAX)
     */
    async addToCart(productId, quantity = 1) {
        // ✅ Parse quantity thành số nguyên
        quantity = parseInt(quantity) || 1;
        
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);  // ✅ Gửi đúng quantity
        
        try {
            const response = await fetch('ajax/cart_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update mini cart
                this.updateMiniCart();
                
                // Show success message
                this.showNotification('Product added to cart!', 'success');
            } else {
                this.showNotification('Failed to add product', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('Network error', 'error');
        }
    }
    
    /**
     * Cập nhật mini cart dropdown
     */
    async updateMiniCart() {
        try {
            const formData = new FormData();
            formData.append('action', 'get');
            
            const response = await fetch('ajax/cart_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update badge
                this.cartBadge.textContent = data.itemCount;
                this.cartItemCount.textContent = `${data.itemCount} items`;
                
                // Update total
                this.cartTotalAmount.textContent = `${this.formatNumber(data.total)}`;
                
                // Render cart items
                this.renderMiniCartItems(data.cart);
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }
    
    /**
     * Render cart items trong dropdown
     */
    renderMiniCartItems(cart) {
        if (Object.keys(cart).length === 0) {
            // Cart empty
            this.cartDropdownBody.innerHTML = `
                <div class="cart-empty-message">
                    <i class="bi bi-cart-x"></i>
                    <p>Your cart is empty</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        for (let id in cart) {
            const item = cart[id];
            html += `
                <div class="mini-cart-item">
                    <img src="${item.image}" alt="${this.escapeHtml(item.name)}">
                    <div class="mini-cart-item-info">
                        <div class="mini-cart-item-name">${this.escapeHtml(item.name)}</div>
                        <div class="mini-cart-item-price">
                            <span class="mini-cart-item-quantity">${item.quantity}x</span>
                            ${this.formatNumber(item.price)}
                        </div>
                    </div>
                    <button class="mini-cart-item-remove" onclick="cart.removeItem(${item.id})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
        }
        
        this.cartDropdownBody.innerHTML = html;
    }
    
    /**
     * Xóa sản phẩm khỏi cart
     */
    async removeItem(productId) {
        try {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('product_id', productId);
            
            const response = await fetch('ajax/cart_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Product removed!', 'success');
                this.updateMiniCart();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    /**
     * Show notification (simple alert, có thể dùng Toastify.js)
     */
    showNotification(message, type = 'success') {
        // Simple implementation - có thể thay bằng Toastify
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} cart-notification`;
        alertDiv.textContent = message;
        alertDiv.style.cssText = `
            position: fixed;
            top: 120px;
            right: 20px;
            z-index: 9999;
            padding: 12px 24px;
            border-radius: 8px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            alertDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }
    
    /**
     * Format number with commas
     */
    formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    /**
     * Escape HTML để tránh XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize cart khi DOM loaded
let cart;
document.addEventListener('DOMContentLoaded', () => {
    cart = new ShoppingCart();
});

// Thêm CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
