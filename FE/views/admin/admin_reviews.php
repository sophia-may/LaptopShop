
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php include 'views/layouts/admin_header.php'; ?>
<!-- Admin Layout Wrapper -->
<div class="admin-layout">
    <?php include 'views/layouts/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="admin-content">
        <div class="content-header mb-4">
            <h2><i class="bi bi-star-fill"></i> Product Reviews</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['total']) ?></div>
                <div class="stat-label">Total Reviews</div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-value"><?= number_format($stats['pending']) ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-value"><?= number_format($stats['approved']) ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card stat-danger">
                <div class="stat-value"><?= number_format($stats['rejected']) ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="content-card">
            <div class="card-tabs">
                <a href="?page=admin_reviews&status=all" class="tab-link <?= $status === 'all' ? 'active' : '' ?>">
                    All Reviews
                </a>
                <a href="?page=admin_reviews&status=pending" class="tab-link <?= $status === 'pending' ? 'active' : '' ?>">
                    Pending <?php if($stats['pending'] > 0): ?>
                        <span class="badge badge-warning"><?= $stats['pending'] ?></span>
                    <?php endif; ?>
                </a>
                <a href="?page=admin_reviews&status=approved" class="tab-link <?= $status === 'approved' ? 'active' : '' ?>">
                    Approved
                </a>
                <a href="?page=admin_reviews&status=rejected" class="tab-link <?= $status === 'rejected' ? 'active' : '' ?>">
                    Rejected
                </a>
            </div>

            <!-- Reviews Table -->
            <?php if (empty($reviews)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No reviews found</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Images</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <!-- Product -->
                                    <td>
                                        <div class="product-cell">
                                            <div class="product-info">
                                                <div class="product-name"><?= htmlspecialchars($review['product_name']) ?></div>
                                                <small class="text-muted">ID: <?= $review['product_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Customer -->
                                    <td>
                                        <div class="customer-name"><?= htmlspecialchars($review['user_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($review['user_email']) ?></small>
                                    </td>
                                    
                                    <!-- Rating -->
                                    <td>
                                        <div class="rating-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <small class="text-muted"><?= $review['rating'] ?>.0/5.0</small>
                                    </td>
                                    
                                    <!-- Review -->
                                    <td class="review-text">
                                        <?php if (!empty($review['review_title'])): ?>
                                            <strong><?= htmlspecialchars(substr($review['review_title'], 0, 40)) ?></strong><br>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars(substr($review['review_text'] ?? '', 0, 60)) ?>...
                                        </small>
                                    </td>
                                    
                                    <!-- Images -->
                                    <td>
                                        <?php 
                                        $images = !empty($review['review_images']) ? json_decode($review['review_images'], true) : [];
                                        if (!empty($images)): 
                                        ?>
                                            <div class="review-images">
                                                <?php foreach(array_slice($images, 0, 3) as $img): ?>
                                                    <img src="assets/img/reviews/<?= $img ?>" 
                                                         alt="Review" 
                                                         onclick="showImageModal('assets/img/reviews/<?= $img ?>')">
                                                <?php endforeach; ?>
                                                <?php if (count($images) > 3): ?>
                                                    <span class="image-more">+<?= count($images) - 3 ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td>
                                        <?php 
                                        $badges = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge badge-<?= $badges[$review['status']] ?>">
                                            <?= ucfirst($review['status']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Date -->
                                    <td>
                                        <div><?= date('M d, Y', strtotime($review['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($review['created_at'])) ?></small>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($review['status'] !== 'approved'): ?>
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="updateStatus(<?= $review['id'] ?>, 'approved')"
                                                        title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($review['status'] !== 'rejected'): ?>
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="updateStatus(<?= $review['id'] ?>, 'rejected')"
                                                        title="Reject">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteReview(<?= $review['id'] ?>)"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalReviews) ?> of <?= $totalReviews ?> entries
                        </div>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=admin_reviews&status=<?= $status ?>&current_page=<?= $page - 1 ?>" class="page-link">
                                    <i class="bi bi-chevron-left"></i> Prev
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=admin_reviews&status=<?= $status ?>&current_page=<?= $i ?>" 
                                   class="page-link <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=admin_reviews&status=<?= $status ?>&current_page=<?= $page + 1 ?>" class="page-link">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="modalImage" src="" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(reviewId, status) {
    if (!confirm(`Are you sure you want to ${status} this review?`)) return;
    
    fetch('ajax/admin/update_review_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({review_id: reviewId, status: status})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function deleteReview(reviewId) {
    if (!confirm('⚠️ Delete this review permanently?')) return;
    
    fetch('ajax/admin/delete_review.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({review_id: reviewId})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
