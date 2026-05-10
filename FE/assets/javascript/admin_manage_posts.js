// Admin Manage Posts JavaScript

let allPosts = [];
let currentFilter = null;
let postModal, reactionsModal, commentsModal;
let currentPage = 1;
let totalPages = 1;
const postsPerPage = 5;

// Format text in content editor - Global function
function formatText(command) {
    const editor = document.getElementById('post-content');
    
    if (!editor) {
        console.error('Content editor not found');
        return false;
    }
    
    // Focus on the editor
    editor.focus();
    
    // Execute the command
    let success = false;
    try {
        switch(command) {
            case 'h1':
            case 'h2':
                // Check if already in this heading format
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    let node = selection.anchorNode;
                    while (node && node.nodeType !== 1) {
                        node = node.parentNode;
                    }
                    
                    const currentTag = node && node.tagName ? node.tagName.toLowerCase() : '';
                    
                    // If already in this heading, convert to paragraph
                    if (currentTag === command) {
                        success = document.execCommand('formatBlock', false, 'p');
                    } else {
                        success = document.execCommand('formatBlock', false, command);
                    }
                } else {
                    success = document.execCommand('formatBlock', false, command);
                }
                break;
            case 'unorderedList':
                success = document.execCommand('insertUnorderedList', false, null);
                break;
            case 'orderedList':
                success = document.execCommand('insertOrderedList', false, null);
                break;
            case 'indent':
                success = document.execCommand('indent', false, null);
                break;
            case 'outdent':
                success = document.execCommand('outdent', false, null);
                break;
            case 'undo':
                success = document.execCommand('undo', false, null);
                break;
            case 'redo':
                success = document.execCommand('redo', false, null);
                break;
            case 'bold':
            case 'italic':
            case 'underline':
            case 'strikeThrough':
            case 'strikethrough':
                success = document.execCommand(command === 'strikethrough' ? 'strikeThrough' : command, false, null);
                break;
            default:
                success = document.execCommand(command, false, null);
        }
        
        console.log(`Format command '${command}' executed:`, success);
        
        // Update hidden input after formatting
        const contentHidden = document.getElementById('post-content-hidden');
        if (contentHidden) {
            contentHidden.value = editor.innerHTML;
        }
        
    } catch (e) {
        console.error('Format command failed:', e);
    }
    
    // Keep focus on editor
    editor.focus();
    
    // Update toolbar button states after a short delay to ensure DOM is updated
    setTimeout(updateToolbarState, 50);
    
    return false; // Prevent default button behavior
}

// Update toolbar button states based on current selection
function updateToolbarState() {
    const commands = [
        { name: 'bold', execCommand: 'bold' },
        { name: 'italic', execCommand: 'italic' },
        { name: 'underline', execCommand: 'underline' },
        { name: 'strikethrough', execCommand: 'strikeThrough' }
    ];
    
    // Check text formatting commands
    commands.forEach(cmd => {
        try {
            const isActive = document.queryCommandState(cmd.execCommand);
            const button = document.querySelector(`.format-btn[data-command="${cmd.name}"]`);
            
            if (button) {
                if (isActive) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
        } catch (e) {
            console.error(`Error checking command state for ${cmd.name}:`, e);
        }
    });
    
    // Check heading tags (H1, H2)
    try {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            let node = selection.anchorNode;
            
            // Traverse up to find the parent element
            while (node && node.nodeType !== 1) {
                node = node.parentNode;
            }
            
            // Check if we're in a heading
            let currentTag = '';
            if (node) {
                const tagName = node.tagName ? node.tagName.toLowerCase() : '';
                if (tagName === 'h1' || tagName === 'h2') {
                    currentTag = tagName;
                }
            }
            
            // Update H1 and H2 button states
            const h1Button = document.querySelector('.format-btn[data-command="h1"]');
            const h2Button = document.querySelector('.format-btn[data-command="h2"]');
            
            if (h1Button) {
                if (currentTag === 'h1') {
                    h1Button.classList.add('active');
                } else {
                    h1Button.classList.remove('active');
                }
            }
            
            if (h2Button) {
                if (currentTag === 'h2') {
                    h2Button.classList.add('active');
                } else {
                    h2Button.classList.remove('active');
                }
            }
        }
    } catch (e) {
        console.error('Error checking heading states:', e);
    }
}

// Helper functions for content sync
function syncContent() {
    const contentEditor = document.getElementById('post-content');
    const contentHidden = document.getElementById('post-content-hidden');
    if (contentEditor && contentHidden) {
        contentHidden.value = contentEditor.innerHTML;
    }
}

function syncContentDelayed() {
    setTimeout(syncContent, 10);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    postModal = new bootstrap.Modal(document.getElementById('postModal'));
    reactionsModal = new bootstrap.Modal(document.getElementById('reactionsModal'));
    commentsModal = new bootstrap.Modal(document.getElementById('commentsModal'));
    
    // Load initial data
    loadCategories();
    loadPosts();
    
    // Filter button event listeners
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Setup content editor when modal is shown
    const postModalElement = document.getElementById('postModal');
    postModalElement.addEventListener('shown.bs.modal', function() {
        const contentEditor = document.getElementById('post-content');
        if (contentEditor) {
            // Remove any existing listeners to prevent duplicates
            contentEditor.removeEventListener('input', syncContent);
            contentEditor.removeEventListener('paste', syncContentDelayed);
            contentEditor.removeEventListener('keyup', updateToolbarState);
            contentEditor.removeEventListener('mouseup', updateToolbarState);
            contentEditor.removeEventListener('keydown', handleKeyboardShortcuts);
            
            // Add event listeners
            contentEditor.addEventListener('input', syncContent);
            contentEditor.addEventListener('paste', syncContentDelayed);
            contentEditor.addEventListener('keyup', updateToolbarState);
            contentEditor.addEventListener('mouseup', updateToolbarState);
            contentEditor.addEventListener('keydown', handleKeyboardShortcuts);
            
            // Focus on editor
            contentEditor.focus();
            
            // Initial toolbar state update
            updateToolbarState();
        }
        
        // Setup drag & drop for image upload
        setupImageUpload();
    });
    
    // Handle modal close event to blur focused elements
    postModalElement.addEventListener('hide.bs.modal', function() {
        // Blur any focused element to prevent aria-hidden warning
        if (document.activeElement && postModalElement.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
});

// Setup image upload with drag & drop
function setupImageUpload() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('post-image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (!dropZone || !fileInput) return;
    
    // Click to open file browser
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files[0]) {
            handleImageFile(e.target.files[0]);
        }
    });
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop zone when dragging over
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('dragover');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('dragover');
        }, false);
    });
    
    // Handle dropped files
    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files && files[0]) {
            // Set the file to the input
            fileInput.files = files;
            handleImageFile(files[0]);
        }
    }, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleImageFile(file) {
    const dropZone = document.getElementById('drop-zone');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showAlert('Please select an image file', 'danger');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('Image size must be less than 5MB', 'danger');
        return;
    }
    
    // Show preview and hide drop zone
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImg.src = e.target.result;
        dropZone.style.display = 'none';
        imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeImagePreview() {
    const fileInput = document.getElementById('post-image');
    const dropZone = document.getElementById('drop-zone');
    const imagePreview = document.getElementById('image-preview');
    
    fileInput.value = '';
    imagePreview.style.display = 'none';
    dropZone.style.display = 'block';
}

// Handle keyboard shortcuts
function handleKeyboardShortcuts(e) {
    // Check for Ctrl/Cmd + key combinations
    if ((e.ctrlKey || e.metaKey) && !e.shiftKey && !e.altKey) {
        // Let browser handle the formatting, but update toolbar after
        setTimeout(updateToolbarState, 10);
    }
}

// Load all posts
function loadPosts(status = null, page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        action: 'get_posts',
        page: page,
        limit: postsPerPage
    });
    
    if (status) {
        params.append('status', status);
    }
    
    const url = `ajax/manage_posts_handler.php?${params.toString()}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allPosts = data.posts;
                totalPages = data.totalPages || 1;
                displayPosts(allPosts);
                updateStatistics(allPosts);
                renderPagination();
            } else {
                showAlert('Error loading posts: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to load posts', 'danger');
        });
}

// Display posts in table
function displayPosts(posts) {
    const tbody = document.getElementById('posts-table-body');
    
    if (posts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No posts found</td></tr>';
        return;
    }
    
    tbody.innerHTML = posts.map(post => {
        const statusBadge = getStatusBadge(post.status);
        const imagePreview = post.image 
            ? `<img src="assets/img/posts/${post.image}" class="avatar avatar-sm me-2" alt="Post image" onerror="this.src='assets/img/placeholder.png'">`
            : `<span class="avatar avatar-sm me-2 bg-secondary-lt"><i class="ti ti-photo"></i></span>`;
        
        return `
            <tr>
                <td><span class="text-muted">${post.id}</span></td>
                <td>
                    <div class="d-flex align-items-center">
                        ${imagePreview}
                        <div class="flex-fill">
                            <div class="post-title">${escapeHtml(post.title)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    ${post.category_name 
                        ? `<span class="badge" style="background-color: ${post.category_color}">${post.category_name}</span>` 
                        : '<span class="text-muted">—</span>'}
                </td>
                <td>${statusBadge}</td>
                <td class="text-center"><i class="ti ti-eye text-muted me-1"></i>${post.view_count}</td>
                <td class="text-center">
                    <a href="#" onclick="showReactions(${post.id}); return false;" class="btn btn-ghost-primary btn-sm">
                        <i class="ti ti-thumb-up me-1"></i>${post.reaction_count}
                    </a>
                </td>
                <td class="text-center">
                    <a href="#" onclick="showComments(${post.id}); return false;" class="btn btn-ghost-primary btn-sm">
                        <i class="ti ti-message me-1"></i>${post.comment_count}
                    </a>
                </td>
                <td class="text-muted">${formatDate(post.created_at)}</td>
                <td class="action-buttons">
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-icon btn-sm btn-primary" onclick="editPost(${post.id})" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-danger" onclick="deletePost(${post.id})" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Update statistics
function updateStatistics(posts) {
    const stats = {
        published: 0,
        draft: 0,
        archived: 0
    };
    
    posts.forEach(post => {
        if (stats.hasOwnProperty(post.status)) {
            stats[post.status]++;
        }
    });
    
    document.getElementById('stat-published').textContent = stats.published;
    document.getElementById('stat-draft').textContent = stats.draft;
    document.getElementById('stat-archived').textContent = stats.archived;
}

// Filter posts by status
function filterPosts(status) {
    currentFilter = status;
    loadPosts(status);
}

// Show create modal
function showCreateModal() {
    document.getElementById('postModalTitle').textContent = 'Create New Post';
    document.getElementById('postForm').reset();
    document.getElementById('post-id').value = '';
    document.getElementById('post-content').innerHTML = '';
    document.getElementById('post-content-hidden').value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('drop-zone').style.display = 'block';
    postModal.show();
}

// Edit post
function editPost(postId) {
    fetch(`ajax/manage_posts_handler.php?action=get_post&id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const post = data.post;
                document.getElementById('postModalTitle').textContent = 'Edit Post';
                document.getElementById('post-id').value = post.id;
                document.getElementById('post-title').value = post.title;
                document.getElementById('post-category').value = post.category_id || '';
                document.getElementById('post-content').innerHTML = post.content;
                document.getElementById('post-content-hidden').value = post.content;
                document.getElementById('post-status').value = post.status;
                
                // Show current image if exists
                const dropZone = document.getElementById('drop-zone');
                const imagePreview = document.getElementById('image-preview');
                const previewImg = document.getElementById('preview-img');
                
                if (post.image) {
                    previewImg.src = 'assets/img/posts/' + post.image;
                    dropZone.style.display = 'none';
                    imagePreview.style.display = 'block';
                } else {
                    dropZone.style.display = 'block';
                    imagePreview.style.display = 'none';
                }
                
                // Clear file input
                document.getElementById('post-image').value = '';
                
                postModal.show();
            } else {
                showAlert('Error loading post: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to load post', 'danger');
        });
}

// Save post (create or update)
function savePost() {
    const form = document.getElementById('postForm');
    
    // Get content from contenteditable div and store in hidden input
    const contentEditor = document.getElementById('post-content');
    const contentHidden = document.getElementById('post-content-hidden');
    contentHidden.value = contentEditor.innerHTML;
    
    // Validate required fields
    const title = document.getElementById('post-title').value.trim();
    if (!title) {
        showAlert('Title is required', 'danger');
        return;
    }
    if (!contentEditor.textContent.trim()) {
        showAlert('Content is required', 'danger');
        return;
    }
    
    const formData = new FormData(form);
    const postId = document.getElementById('post-id').value;
    formData.append('action', postId ? 'update_post' : 'create_post');
    
    fetch('ajax/manage_posts_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Blur any focused element before hiding modal
                if (document.activeElement) {
                    document.activeElement.blur();
                }
                postModal.hide();
                loadPosts(currentFilter);
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to save post', 'danger');
        });
}

// Delete post
function deletePost(postId) {
    if (!confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_post');
    formData.append('id', postId);
    
    fetch('ajax/manage_posts_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                loadPosts(currentFilter);
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to delete post', 'danger');
        });
}

// Show reactions
function showReactions(postId) {
    document.getElementById('reactions-list').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    reactionsModal.show();
    
    fetch(`ajax/manage_posts_handler.php?action=get_reactions&post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReactions(data.reactions);
            } else {
                document.getElementById('reactions-list').innerHTML = 
                    `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reactions-list').innerHTML = 
                '<div class="alert alert-danger">Failed to load reactions</div>';
        });
}

// Display reactions
function displayReactions(reactions) {
    const container = document.getElementById('reactions-list');
    
    if (reactions.length === 0) {
        container.innerHTML = '<div class="empty"><div class="empty-icon"><i class="ti ti-mood-sad icon"></i></div><p class="empty-title">No reactions yet</p></div>';
        return;
    }
    
    const likeReactions = reactions.filter(r => r.reaction_type === 'like');
    const dislikeReactions = reactions.filter(r => r.reaction_type === 'dislike');
    
    container.innerHTML = `
        <div class="mb-3">
            <h3 class="mb-3"><i class="ti ti-thumb-up text-success me-2"></i>Likes (${likeReactions.length})</h3>
            <div class="list-group list-group-flush">
                ${likeReactions.length > 0 ? likeReactions.map(r => `
                    <div class="list-group-item reaction-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-sm bg-success-lt"><i class="ti ti-thumb-up"></i></span>
                            </div>
                            <div class="col">
                                <strong>${escapeHtml(r.display_name || r.user_name || 'Anonymous')}</strong>
                                <div class="text-muted small">${formatDate(r.created_at)}</div>
                            </div>
                        </div>
                    </div>
                `).join('') : '<div class="text-muted text-center py-3">No likes yet</div>'}
            </div>
        </div>
        <div>
            <h3 class="mb-3"><i class="ti ti-thumb-down text-danger me-2"></i>Dislikes (${dislikeReactions.length})</h3>
            <div class="list-group list-group-flush">
                ${dislikeReactions.length > 0 ? dislikeReactions.map(r => `
                    <div class="list-group-item reaction-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-sm bg-danger-lt"><i class="ti ti-thumb-down"></i></span>
                            </div>
                            <div class="col">
                                <strong>${escapeHtml(r.display_name || r.user_name || 'Anonymous')}</strong>
                                <div class="text-muted small">${formatDate(r.created_at)}</div>
                            </div>
                        </div>
                    </div>
                `).join('') : '<div class="text-muted text-center py-3">No dislikes yet</div>'}
            </div>
        </div>
    `;
}

// Show comments
function showComments(postId) {
    document.getElementById('comments-list').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    commentsModal.show();
    
    fetch(`ajax/manage_posts_handler.php?action=get_comments&post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComments(data.comments);
            } else {
                document.getElementById('comments-list').innerHTML = 
                    `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('comments-list').innerHTML = 
                '<div class="alert alert-danger">Failed to load comments</div>';
        });
}

// Display comments
function displayComments(comments) {
    const container = document.getElementById('comments-list');
    
    if (comments.length === 0) {
        container.innerHTML = '<div class="empty"><div class="empty-icon"><i class="ti ti-message-off icon"></i></div><p class="empty-title">No comments yet</p></div>';
        return;
    }
    
    container.innerHTML = `<div class="list-group list-group-flush">${comments.map(comment => `
        <div class="list-group-item comment-item">
            <div class="row">
                <div class="col-auto">
                    <span class="avatar avatar-sm bg-blue-lt">${(comment.display_name || comment.user_name || 'A').charAt(0).toUpperCase()}</span>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${escapeHtml(comment.display_name || comment.user_name)}</strong>
                            ${comment.status !== 'approved' ? `<span class="badge bg-warning ms-2">${comment.status}</span>` : ''}
                            <div class="text-muted small">${formatDate(comment.created_at)}</div>
                        </div>
                        <button class="btn btn-icon btn-sm btn-danger" onclick="deleteComment(${comment.id})" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                    <div class="mt-2">${escapeHtml(comment.comment)}</div>
                </div>
            </div>
        </div>
    `).join('')}</div>`;
}

// Delete comment
function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_comment');
    formData.append('id', commentId);
    
    fetch('ajax/manage_posts_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                // Reload comments - get post_id from the currently open modal
                const postId = allPosts.find(p => p.comment_count > 0)?.id;
                if (postId) {
                    showComments(postId);
                }
                loadPosts(currentFilter); // Refresh post list to update comment count
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Failed to delete comment', 'danger');
        });
}

// Load categories
function loadCategories() {
    fetch('ajax/manage_posts_handler.php?action=get_categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('post-category');
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading categories:', error));
}

// Helper functions
function getStatusBadge(status) {
    const badges = {
        'published': '<span class="badge bg-success">Published</span>',
        'draft': '<span class="badge bg-warning">Draft</span>',
        'archived': '<span class="badge bg-secondary">Archived</span>'
    };
    return badges[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAlert(message, type = 'info') {
    // Create toast container if doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const iconClass = type === 'success' ? 'ti-check' : type === 'danger' ? 'ti-alert-circle' : 'ti-info-circle';
    const bgClass = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-info';
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bgClass} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">

            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    toast.querySelector('.toast-body').innerHTML = `<i class="ti ${iconClass} me-2"></i>${message}`;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// Render pagination controls
function renderPagination() {
    const paginationContainer = document.getElementById('posts-pagination');
    if (!paginationContainer) return;
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let html = '<nav aria-label="Posts pagination" class="mt-3"><ul class="pagination justify-content-center">';
    
    // First page button
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadPosts(currentFilter, 1); return false;" aria-label="First">
            <span aria-hidden="true">&laquo;&laquo;</span>
        </a>
    </li>`;
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPosts(currentFilter, 1); return false;">1</a></li>`;
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadPosts(currentFilter, ${i}); return false;">${i}</a>
        </li>`;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadPosts(currentFilter, ${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    // Last page button
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadPosts(currentFilter, ${totalPages}); return false;" aria-label="Last">
            <span aria-hidden="true">&raquo;&raquo;</span>
        </a>
    </li>`;
    
    html += '</ul></nav>';
    paginationContainer.innerHTML = html;
}

// Show toast notification
function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const iconClass = type === 'success' ? 'ti-check' : type === 'danger' ? 'ti-alert-circle' : 'ti-info-circle';
    const bgClass = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : 'bg-info';
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bgClass} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="ti ${iconClass} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
