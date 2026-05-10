/**
 * Product Detail Page JavaScript
 * Handles: Image gallery, variants, quantity, add to cart
 */

class ProductDetailPage {
    constructor() {
        // Check if we're on product detail page
        if (!document.querySelector('.product-detail-section')) {
            return; // Exit if not on product page
        }
        
        this.basePrice = parseFloat(document.querySelector('[data-base-price]')?.dataset.basePrice || 0);
        this.currentPriceModifier = 0;
        
        this.init();
    }
    
    init() {
        this.initImageGallery();
        this.initQuantityControls();
        this.initVariantSelection();
        this.initAddToCart();
    }
    
    /**
     * Image Gallery - Thumbnail click
     */
    initImageGallery() {
        const thumbnails = document.querySelectorAll('.thumbnail-item img');
        const mainImage = document.getElementById('mainProductImage');
        
        if (!mainImage) return;
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                // Update main image
                mainImage.src = thumbnail.src;
                
                // Update active state
                thumbnails.forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            });
        });
    }
    
    /**
     * Quantity +/- Controls
     */
    initQuantityControls() {
        const increaseBtn = document.getElementById('increaseQty');
        const decreaseBtn = document.getElementById('decreaseQty');
        const quantityInput = document.getElementById('productQuantity');

        if (!quantityInput) return;

    // ✅ Remove old listeners nếu có (bằng cách clone node)
        if (increaseBtn) {
            const newIncreaseBtn = increaseBtn.cloneNode(true);
            increaseBtn.parentNode.replaceChild(newIncreaseBtn, increaseBtn);
        
            newIncreaseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const max = parseInt(quantityInput.max) || 999;
                const current = parseInt(quantityInput.value) || 1;
            
            if (current < max) {
                quantityInput.value = current + 1; // ✅ Tăng 1
            }
        });
    }

    if (decreaseBtn) {
        const newDecreaseBtn = decreaseBtn.cloneNode(true);
        decreaseBtn.parentNode.replaceChild(newDecreaseBtn, decreaseBtn);
        
        newDecreaseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const min = parseInt(quantityInput.min) || 1;
            const current = parseInt(quantityInput.value) || 1;
            
            if (current > min) {
                quantityInput.value = current - 1; // ✅ Giảm 1
            }
        });
    }

    // ✅ Prevent manual input outside min-max
    quantityInput?.addEventListener('input', () => {
        const min = parseInt(quantityInput.min) || 1;
        const max = parseInt(quantityInput.max) || 999;
        let value = parseInt(quantityInput.value) || min;

        if (value < min) quantityInput.value = min;
        if (value > max) quantityInput.value = max;
    });
    }
    
    /**
     * Variant Selection (Storage, Color, RAM)
     */
    initVariantSelection() {
        // Storage variants - update price
        const storageRadios = document.querySelectorAll('.variant-radio[name="storage"]');
        
        storageRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    const priceModifier = parseFloat(radio.dataset.priceModifier) || 0;
                    this.currentPriceModifier = priceModifier;
                    this.updateDisplayPrice();
                    
                    // Update stock
                    const stock = radio.dataset.stock;
                    const stockDisplay = document.getElementById('stockDisplay');
                    const quantityInput = document.getElementById('productQuantity');
                    
                    if (stockDisplay) stockDisplay.textContent = stock;
                    if (quantityInput) {
                        quantityInput.max = stock;
                        // Reset quantity if exceeds new stock
                        if (parseInt(quantityInput.value) > parseInt(stock)) {
                            quantityInput.value = stock;
                        }
                    }
                }
            });
        });
        
        // Color variants - update label
        const colorRadios = document.querySelectorAll('.color-radio');
        
        colorRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    const colorName = radio.dataset.colorName;
                    const colorLabel = document.getElementById('selectedColorName');
                    if (colorLabel) {
                        colorLabel.textContent = colorName;
                    }
                }
            });
        });
    }
    
    /**
     * Update display price
     */
    updateDisplayPrice() {
        const finalPrice = this.basePrice + this.currentPriceModifier;
        const displayPriceEl = document.getElementById('displayPrice');
        
        if (displayPriceEl) {
            displayPriceEl.textContent = finalPrice.toLocaleString('vi-VN') + 'đ';
        }
    }
    
    /**
     * Add to Cart Button
     */
    initAddToCart() {
        const addToCartBtn = document.querySelector('.product-detail-section .add-to-cart-btn');
        
        if (!addToCartBtn) {
            console.warn('[ProductDetail.js] Add to cart button not found');
            return;
        }
        
        // ✅ Remove existing event listeners (prevent duplicate)
        const newBtn = addToCartBtn.cloneNode(true);
        addToCartBtn.parentNode.replaceChild(newBtn, addToCartBtn);
        
        // ✅ Add single event listener
        newBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent bubbling
            
            const productId = newBtn.getAttribute('data-product-id');
            const quantityInput = document.getElementById('productQuantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            // Validate
            if (!productId || quantity < 1) {
                alert('Invalid product or quantity');
                return;
            }
            
            // Get selected variants
            const selectedStorage = document.querySelector('.variant-radio[name="storage"]:checked');
            const selectedColor = document.querySelector('.color-radio:checked');
            const selectedRam = document.querySelector('.variant-radio[name="ram"]:checked');
            
            const variantData = {
                storage: selectedStorage ? selectedStorage.value : null,
                color: selectedColor ? selectedColor.value : null,
                ram: selectedRam ? selectedRam.value : null
            };
            
            console.log('Add to cart:', {
                productId,
                quantity,
                variants: variantData
            });
            
            // Call cart manager
            if (typeof cart !== 'undefined') {
                cart.addToCart(productId, quantity);
            } else {
                console.error('Cart object not initialized');
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ProductDetailPage();
});
