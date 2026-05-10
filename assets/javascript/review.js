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
