<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <h2><i class="bi bi-box-seam"></i> Manage Products</h2>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    <i class="bi bi-plus-circle"></i> Add New Product
                </button>
            </div>

            <div class="content-card">
                <!-- Statistics -->
                <div class="stats-grid mb-4">
                    <div class="stat-card">
                        <div class="stat-value"><?= number_format($totalProducts) ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Storage</th>
                                <th>RAM</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No products found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $imageSrc =  'assets/img/products/' . $product['name']. '.jpg';
                                            // Check if it's a local path or URL
                                            if (!empty($imageSrc) && (strpos($imageSrc, 'http') === 0 || file_exists($imageSrc))) {
                                                echo '<img src="' . $imageSrc . '" alt="' . htmlspecialchars($product['name']) . '" style="width: 50px; height: 50px; object-fit: cover;">';
                                            } else {
                                                echo '<img src="assets/img/placeholder.png" alt="No image" style="width: 50px; height: 50px; object-fit: cover;">';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['brand']) ?></td>
                                        <td>$<?= number_format($product['price']) ?></td>
                                        <td><?= htmlspecialchars($product['storage']) ?></td>
                                        <td><?= htmlspecialchars($product['ram'] ?? '') ?></td>
                                        <td>
                                            <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $product['stock'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($product['category']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editProduct(<?= $product['id'] ?>)">Edit
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">Delete
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- First Page Button -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=manage_products&product_page=1" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=manage_products&product_page=1">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=manage_products&product_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=manage_products&product_page=<?= $totalPages ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Last Page Button -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=manage_products&product_page=<?= $totalPages ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="productId" name="id">
                        <input type="hidden" id="existingImage" name="existing_image">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand *</label>
                                <input type="text" class="form-control" id="productBrand" name="brand" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price *</label>
                                <input type="number" class="form-control" id="productPrice" name="price" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Storage</label>
                                <select class="form-select" id="productStorage" name="storage">
                                    <option value="64GB">64GB</option>
                                    <option value="128GB">128GB</option>
                                    <option value="256GB">256GB</option>
                                    <option value="512GB">512GB</option>
                                    <option value="1TB">1TB</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="productStock" name="stock" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">RAM</label>
                                <select class="form-select" id="productRam" name="ram">
                                    <option value="">Select RAM</option>
                                    <option value="4GB">4GB</option>
                                    <option value="6GB">6GB</option>
                                    <option value="8GB">8GB</option>
                                    <option value="12GB">12GB</option>
                                    <option value="16GB">16GB</option>
                                    <option value="18GB">18GB</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="productCategory" name="category">
                                    <option value="Smartphone">Smartphone</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Accessory">Accessory</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <div id="dropZone" class="border rounded p-4 text-center" style="cursor: pointer; border-style: dashed !important;">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                <p class="mb-2">Drag & drop image here or click to browse</p>
                                <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                                <input type="file" class="d-none" id="productImage" name="image" accept="image/*">
                            </div>
                            <div id="currentImagePreview" class="mt-2"></div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/javascript/admin_manage_products.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
