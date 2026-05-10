<?php
require_once 'helpers/settings_helper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - <?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'views/layouts/header.php'; ?>
    
    <section class="post-page-section">
        <div class="container-default">
            <div class="post-page-layout">
                
                <!-- Left side: Posts list -->
                <div class="posts-list-container">
                    <div id="postsContainer" class="posts-grid">
                        <!-- Posts will be loaded here by JavaScript -->
                    </div>
                    
                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="loading-indicator" style="display: none;">
                        <div class="spinner"></div>
                        <p>Đang tải bài viết...</p>
                    </div>
                </div>
                
                <!-- Right side: Search and Categories -->
                <div class="post-sidebar">
                    <!-- Search bar -->
                    <div class="search-box">
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input" 
                            placeholder="" 
                        />
                        <button id="searchBtn" class="search-btn">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Categories sidebar -->
                    <div class="categories-sidebar">
                        <h3 class="categories-title">Categories</h3>
                        
                        <div class="categories-list">
                            <?php foreach ($categories as $category): ?>
                            <button class="category-btn" data-category="<?php echo $category['id']; ?>">
                                <span class="category-name"><?php echo htmlspecialchars($category['name']); ?></span>
                                <span class="category-count"><?php echo $category['post_count']; ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    
    <?php include 'views/layouts/footer.php'; ?>
    
    <script>
        // Set base URL for API calls
        window.POST_API_URL = 'ajax/get_posts.php';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/javascript/post.js"></script>
</body>
</html>