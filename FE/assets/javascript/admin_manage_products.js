// assets/javascript/admin_manage_products.js

const productModal = new bootstrap.Modal(document.getElementById('productModal'));
const productForm = document.getElementById('productForm');
const modalTitle = document.getElementById('modalTitle');
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('productImage');
const imagePreview = document.getElementById('imagePreview');

// Drag and Drop handlers
dropZone.addEventListener('click', () => {
    fileInput.click();
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary', 'bg-light');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary', 'bg-light');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary', 'bg-light');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please upload an image file');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }
        
        // Set file to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
        
        // Show preview
        previewImage(file);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            fileInput.value = '';
            return;
        }
        
        previewImage(file);
    }
});

function previewImage(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        // Hide drop zone
        dropZone.style.display = 'none';
        
        imagePreview.innerHTML = `
            <div class="position-relative d-inline-block">
                <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px;" class="border rounded">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="clearImagePreview()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <p class="text-success small mt-2"><i class="bi bi-check-circle"></i> ${file.name}</p>
        `;
    };
    reader.readAsDataURL(file);
}

function clearImagePreview() {
    imagePreview.innerHTML = '';
    fileInput.value = '';
    // Show drop zone again
    dropZone.style.display = 'block';
}

// Show create modal
function showCreateModal() {
    productForm.reset();
    document.getElementById('productId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('currentImagePreview').innerHTML = '';
    imagePreview.innerHTML = '';
    fileInput.value = '';
    // Show drop zone for new product
    dropZone.style.display = 'block';
    modalTitle.textContent = 'Add New Product';
    productModal.show();
}

// Edit product
async function editProduct(id) {
    try {
        const response = await fetch(`ajax/product_admin_handler.php?action=get&id=${id}`);
        const result = await response.json();

        if (result.success) {
            const product = result.data;
            
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productBrand').value = product.brand;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStorage').value = product.storage;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productRam').value = product.ram;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('existingImage').value = product.image;
            
            // Clear new image preview
            imagePreview.innerHTML = '';
            fileInput.value = '';
            // Show drop zone for editing
            dropZone.style.display = 'block';
            
            // Show current image
            if (product.image) {
                document.getElementById('currentImagePreview').innerHTML = `
                    <div class="alert alert-info">
                        <strong>Current image:</strong>
                        <img src="${product.image}" alt="Current image" style="max-width: 200px; display: block; margin-top: 10px;" class="border rounded">
                        <small class="d-block mt-2">Upload a new image to replace it, or leave empty to keep current image</small>
                    </div>
                `;
            }
            
            modalTitle.textContent = 'Edit Product';
            productModal.show();
        } else {
            alert('Failed to load product data');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading product');
    }
}

// Delete product
async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        const response = await fetch('ajax/product_admin_handler.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Product deleted successfully');
            location.reload();
        } else {
            alert('Failed to delete product: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting product');
    }
}

// Submit form
productForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(productForm);
    const productId = document.getElementById('productId').value;
    
    formData.append('action', productId ? 'update' : 'create');

    try {
        const response = await fetch('ajax/product_admin_handler.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            productModal.hide();
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving product');
    }
});
