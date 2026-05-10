<?php
// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login_signup");
    exit;
}
require_once 'models/UserModel.php';
$userModel = new UserModel();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    die("<div class='alert alert-danger'>Access denied. Admin only.</div>");
}
?>

<?php include 'views/layouts/admin_header.php'; ?>

<div class="main-content-inner">

            <div class="content-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="bi bi-file-text"></i> Manage Posts</h2>
                        <p>Create, edit, and manage blog posts</p>
                    </div>
                    <button class="btn btn-primary" onclick="showCreateModal()">
                        <i class="ti ti-plus me-1"></i>Create New Post
                    </button>
                </div>
            </div>

            <div class="content-card">
                    <!-- Statistics Cards -->
                    <div class="row row-cards mb-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Published</div>
                                    </div>
                                    <div class="h1 mb-0" id="stat-published">0</div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-success">
                                        <i class="ti ti-check"></i> Active posts
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Drafts</div>
                                    </div>
                                    <div class="h1 mb-0" id="stat-draft">0</div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-warning">
                                        <i class="ti ti-clock"></i> Pending posts
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Archived</div>
                                    </div>
                                    <div class="h1 mb-0" id="stat-archived">0</div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-muted">
                                        <i class="ti ti-archive"></i> Old posts
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Posts Table Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Posts List</h3>
                            <div class="card-actions">
                                <div class="btn-list">
                                    <button type="button" class="btn btn-sm active" onclick="filterPosts(null)">All</button>
                                    <button type="button" class="btn btn-sm btn-success" onclick="filterPosts('published')">Published</button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="filterPosts('draft')">Drafts</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="filterPosts('archived')">Archived</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Post</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th class="text-center">Views</th>
                                        <th class="text-center">Reactions</th>
                                        <th class="text-center">Comments</th>
                                        <th>Date</th>
                                        <th class="w-1">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="posts-table-body">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="posts-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Post Modal -->
    <div class="modal modal-blur fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalTitle">Create New Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="postForm">
                        <input type="hidden" id="post-id" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label required">Title</label>
                            <input type="text" class="form-control" id="post-title" name="title" placeholder="Enter post title" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" id="post-category" name="category_id">
                                    <option value="">-- Select Category --</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="post-status" name="status">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <div id="drop-zone" class="drop-zone">
                                <div class="drop-zone-content">
                                    <i class="ti ti-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                    <p class="mb-2">Drag & drop image here or click to browse</p>
                                    <small class="text-muted">JPG, PNG, GIF, max 5MB</small>
                                </div>
                                <input type="file" class="form-control d-none" id="post-image" name="image" accept="image/*">
                            </div>
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <div class="position-relative d-inline-block">
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 300px; display: block;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removeImagePreview()" title="Remove image">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Content</label>
                            <!-- Formatting toolbar -->
                            <div class="content-toolbar border rounded-top p-2 bg-light">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="bold" onclick="formatText('bold')" title="Bold">
                                        <i class="ti ti-bold"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="italic" onclick="formatText('italic')" title="Italic">
                                        <i class="ti ti-italic"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="underline" onclick="formatText('underline')" title="Underline">
                                        <i class="ti ti-underline"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="strikethrough" onclick="formatText('strikethrough')" title="Strikethrough">
                                        <i class="ti ti-strikethrough"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="h1" onclick="formatText('h1')" title="Heading 1">
                                        <i class="ti ti-h-1"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary format-btn" data-command="h2" onclick="formatText('h2')" title="Heading 2">
                                        <i class="ti ti-h-2"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('unorderedList')" title="Bullet List">
                                        <i class="ti ti-list"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('orderedList')" title="Numbered List">
                                        <i class="ti ti-list-numbers"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('indent')" title="Indent">
                                        <i class="ti ti-indent-increase"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('outdent')" title="Outdent">
                                        <i class="ti ti-indent-decrease"></i>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('undo')" title="Undo">
                                        <i class="ti ti-arrow-back-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('redo')" title="Redo">
                                        <i class="ti ti-arrow-forward-up"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="post-content" class="form-control content-editor" contenteditable="true" style="min-height: 300px; max-height: 500px; overflow-y: auto;" placeholder="Write your post content here..."></div>
                            <input type="hidden" id="post-content-hidden" name="content">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" onclick="savePost()">
                        <i class="ti ti-device-floppy me-1"></i>Save Post
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reactions Modal -->
    <div class="modal modal-blur fade" id="reactionsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-thumb-up me-2"></i>Post Reactions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reactions-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div class="modal modal-blur fade" id="commentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-message me-2"></i>Post Comments
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="comments-list">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (required for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tabler JS -->
    
    <script src="assets/javascript/admin_manage_posts.js"></script>
<?php include 'views/layouts/admin_footer.php'; ?>
