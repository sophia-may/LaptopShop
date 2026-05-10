// Post page functionality - Lazy loading and filtering

let currentPage = 1;
let loading = false;
let hasMore = true;
let currentCategory = null;
let currentSearch = '';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPosts();
    setupInfiniteScroll();
    setupCategoryFilters();
    setupSearch();
});

// Load posts from server
function loadPosts() {
    if (loading || !hasMore) return;
    
    loading = true;
    showLoader();
    
    // Build query parameters
    const params = new URLSearchParams({
        page: currentPage,
        limit: 3
    });
    
    if (currentCategory) {
        params.append('category', currentCategory);
    }
    
    if (currentSearch) {
        params.append('search', currentSearch);
    }
    
    // Fetch posts from API
    const apiUrl = window.POST_API_URL || 'ajax/get_posts.php';
    fetch(`${apiUrl}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPosts(data.posts);
                hasMore = data.hasMore;
                currentPage++;
            }
            loading = false;
            hideLoader();
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            loading = false;
            hideLoader();
        });
}

// Render posts to the DOM
function renderPosts(posts) {
    const postsContainer = document.getElementById('postsContainer');
    
    if (posts.length === 0 && currentPage === 1) {
        postsContainer.innerHTML = '<div class="no-posts">Không tìm thấy bài viết nào</div>';
        return;
    }
    
    posts.forEach(post => {
        const postElement = createPostElement(post);
        postsContainer.appendChild(postElement);
    });
}

// Create a single post element
function createPostElement(post) {
    const postDiv = document.createElement('div');
    postDiv.className = 'post-card';
    postDiv.onclick = () => window.location.href = `?page=post_detail&id=${post.id}`;
    
    // Truncate content to 50 words
    const contentPreview = truncateWords(post.content, 50);
    
    // Format date
    const postDate = new Date(post.created_at);
    const formattedDate = formatDate(postDate);
    
    postDiv.innerHTML = `
        <div class="post-thumbnail">
            <img src="${post.image ? 'assets/img/posts/' + post.image : 'assets/img/placeholder.png'}" alt="${escapeHtml(post.title)}" onerror="this.src='assets/img/placeholder.png'" />
        </div>
        <div class="post-info-bar">
            <span class="post-author">${escapeHtml(post.author_name)}</span>
            <span class="post-date">${formattedDate}</span>
            <span class="post-category" style="background-color: ${post.category_color || '#3B82F6'}">
                ${escapeHtml(post.category_name || 'Uncategorized')}
            </span>
        </div>
        <h3 class="post-title">${escapeHtml(post.title)}</h3>
        <p class="post-preview">${contentPreview}</p>
        <div class="post-stats">
            <span class="post-views">👁 ${post.view_count} lượt xem</span>
        </div>
    `;
    
    return postDiv;
}

// Setup infinite scroll
function setupInfiniteScroll() {
    window.addEventListener('scroll', () => {
        if (loading || !hasMore) return;
        
        const scrollPosition = window.innerHeight + window.scrollY;
        const pageHeight = document.documentElement.scrollHeight;
        
        // Load more when user is 300px from bottom
        if (scrollPosition >= pageHeight - 300) {
            loadPosts();
        }
    });
}

// Setup category filters
function setupCategoryFilters() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get category ID
            currentCategory = this.dataset.category ? parseInt(this.dataset.category) : null;
            
            // Reset and reload
            resetPosts();
            loadPosts();
        });
    });
}

// Setup search functionality
function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    
    let searchTimeout;
    
    // Search on input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.trim();
            resetPosts();
            loadPosts();
        }, 500);
    });
    
    // Search on button click
    searchBtn.addEventListener('click', function() {
        currentSearch = searchInput.value.trim();
        resetPosts();
        loadPosts();
    });
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            currentSearch = this.value.trim();
            resetPosts();
            loadPosts();
        }
    });
}

// Reset posts (for filtering/searching)
function resetPosts() {
    currentPage = 1;
    hasMore = true;
    document.getElementById('postsContainer').innerHTML = '';
}

// Show loading indicator
function showLoader() {
    document.getElementById('loadingIndicator').style.display = 'block';
}

// Hide loading indicator
function hideLoader() {
    document.getElementById('loadingIndicator').style.display = 'none';
}

// Truncate text to specified number of words
function truncateWords(text, maxWords) {
    // Strip HTML tags
    const plainText = text.replace(/<[^>]*>/g, '');
    
    const words = plainText.trim().split(/\s+/);
    if (words.length <= maxWords) {
        return plainText;
    }
    return words.slice(0, maxWords).join(' ') + '...';
}

// Format date to readable format
function formatDate(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return `${diffMins} phút trước`;
    if (diffHours < 24) return `${diffHours} giờ trước`;
    if (diffDays < 7) return `${diffDays} ngày trước`;
    
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
