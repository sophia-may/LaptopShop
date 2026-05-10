<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; max-width: 1400px;">
        
        <!-- Main Products Section -->
        <div>
            <!-- Header -->
            <div style="display: flex; align-items: center; gap: 15px; background: #e11b22; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px;">
                <i class="bi bi-box-seam" style="font-size: 24px;"></i>
                <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Quản lý sản phẩm</h2>
                <button class="btn btn-light" onclick="showCreateModal()" style="margin-left: auto; gap: 8px;">
                    <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                </button>
            </div>

            <!-- Products Table -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #333;">Sản phẩm</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #333;">Giá</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #333;">Loại/RAM</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #333;">Kho</th>
                            <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #333;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="5" style="padding: 30px; text-align: center; color: #999;">Không có sản phẩm nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr style="border-bottom: 1px solid #eee; transition: background 0.2s;">
                                    <td style="padding: 15px; display: flex; gap: 12px; align-items: center;">
                                        <img src="<?= !empty($product['image']) && (strpos($product['image'], 'http') === 0 || file_exists($product['image'])) ? htmlspecialchars($product['image']) : 'assets/img/placeholder.png' ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <div style="font-weight: 600; color: #333; font-size: 14px;"><?= htmlspecialchars($product['name']) ?></div>
                                            <div style="color: #999; font-size: 12px;"><?= htmlspecialchars($product['brand'] ?? '') ?></div>
                                        </div>
                                    </td>
                                    <td style="padding: 15px; color: #333; font-weight: 600;">₫<?= number_format($product['price']) ?></td>
                                    <td style="padding: 15px; color: #666; font-size: 13px;">
                                        <div><?= htmlspecialchars($product['storage'] ?? '-') ?></div>
                                        <div><?= htmlspecialchars($product['ram'] ?? '-') ?></div>
                                    </td>
                                    <td style="padding: 15px; text-align: center;">
                                        <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; <?= $product['stock'] > 0 ? 'background: #e8f5e9; color: #2e7d32;' : 'background: #ffebee; color: #c62828;' ?>">
                                            <?= $product['stock'] > 0 ? 'Còn: ' . $product['stock'] : 'Hết' ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; display: flex; gap: 8px; justify-content: center;">
                                        <button class="btn btn-sm" onclick="editProduct(<?= $product['id'] ?>)" style="background: #f39c12; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm" onclick="deleteProduct(<?= $product['id'] ?>)" style="background: #dc3545; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
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
            <div style="display: flex; justify-content: center; gap: 5px; margin-top: 20px; flex-wrap: wrap;">
                <a href="?page=manage_products&product_page=1" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666; <?= $page <= 1 ? 'opacity: 0.5; pointer-events: none;' : '' ?>">&laquo;</a>
                <?php 
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                if ($startPage > 1) echo '<a href="?page=manage_products&product_page=1" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666;">1</a>';
                if ($startPage > 2) echo '<span style="padding: 8px 12px;">...</span>';
                
                for ($i = $startPage; $i <= $endPage; $i++) {
                    $isActive = $i === $page;
                    echo '<a href="?page=manage_products&product_page=' . $i . '" style="padding: 8px 12px; border: 1px solid ' . ($isActive ? '#e11b22' : '#ddd') . '; border-radius: 4px; text-decoration: none; color: ' . ($isActive ? 'white' : '#666') . '; background: ' . ($isActive ? '#e11b22' : 'transparent') . ';">' . $i . '</a>';
                }
                
                if ($endPage < $totalPages - 1) echo '<span style="padding: 8px 12px;">...</span>';
                if ($endPage < $totalPages) echo '<a href="?page=manage_products&product_page=' . $totalPages . '" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666;">' . $totalPages . '</a>';
                ?>
                <a href="?page=manage_products&product_page=<?= $totalPages ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #666; <?= $page >= $totalPages ? 'opacity: 0.5; pointer-events: none;' : '' ?>">&raquo;</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Stats -->
        <div>
            <div style="background: #e11b22; color: white; padding: 15px 20px; border-radius: 8px 8px 0 0;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Tổng hàng hóa</h3>
            </div>
            <div style="background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; padding: 20px; border-left: 3px solid #e11b22;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <span style="color: #666; font-size: 14px;">Tổng sản phẩm:</span>
                    <span style="color: #e11b22; font-size: 24px; font-weight: 700;"><?= number_format($totalProducts) ?></span>
                </div>
                <div style="text-align: center;">
                    <button class="btn btn-primary" onclick="showCreateModal()" style="width: 100%; background: #e11b22; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #e11b22; color: white; border: none;">
                <h5 class="modal-title" id="modalTitle">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 25px;">
                    <input type="hidden" id="productId" name="id">
                    <input type="hidden" id="existingImage" name="existing_image">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Tên sản phẩm *</label>
                            <input type="text" id="productName" name="name" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Hãng sản xuất *</label>
                            <input type="text" id="productBrand" name="brand" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Giá *</label>
                            <input type="number" id="productPrice" name="price" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Dung lượng</label>
                            <select id="productStorage" name="storage" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="64GB">64GB</option>
                                <option value="128GB">128GB</option>
                                <option value="256GB">256GB</option>
                                <option value="512GB">512GB</option>
                                <option value="1TB">1TB</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Số lượng kho *</label>
                            <input type="number" id="productStock" name="stock" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">RAM</label>
                            <select id="productRam" name="ram" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Chọn RAM</option>
                                <option value="4GB">4GB</option>
                                <option value="6GB">6GB</option>
                                <option value="8GB">8GB</option>
                                <option value="12GB">12GB</option>
                                <option value="16GB">16GB</option>
                                <option value="18GB">18GB</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Danh mục</label>
                            <select id="productCategory" name="category" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="Smartphone">Smartphone</option>
                                <option value="Tablet">Tablet</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Accessory">Phụ kiện</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Mô tả</label>
                        <textarea id="productDescription" name="description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Ảnh sản phẩm</label>
                        <div id="dropZone" style="border: 2px dashed #ddd; border-radius: 4px; padding: 30px; text-align: center; cursor: pointer;">
                            <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #999; display: block; margin-bottom: 10px;"></i>
                            <p style="margin: 0; color: #666; font-size: 14px; margin-bottom: 5px;">Kéo & thả ảnh hoặc nhấn để chọn</p>
                            <small style="color: #999;">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</small>
                            <input type="file" id="productImage" name="image" accept="image/*" style="display: none;">
                        </div>
                        <div id="imagePreview" style="margin-top: 10px;"></div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f9f9f9; border-top: 1px solid #eee; padding: 15px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" style="background: #e11b22; color: white; border: none;">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/javascript/admin_manage_products.js"></script>

