<?php
require_once 'helpers/settings_helper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo htmlspecialchars(getSetting('general.site_name', 'PhoneStore')); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'views/layouts/header.php'; ?>
    
    <section class="post-detail-section">
        <div class="container-default">
            <div class="post-detail-layout">
                
                <!-- Left side: Post content -->
                <article class="post-detail-content">
                    <!-- Thumbnail -->
                    <?php if ($post['image']): ?>
                    <div class="post-detail-thumbnail">
                        <img src="assets/img/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" onerror="this.src='assets/img/placeholder.png'" />
                    </div>
                    <?php endif; ?>
                    
                    <!-- Post meta info -->
                    <div class="post-detail-meta">
                        <span class="meta-author">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                            </svg>
                            <?php echo htmlspecialchars($post['author_name']); ?>
                        </span>
                        <span class="meta-date">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                            <?php 
                            $date = new DateTime($post['created_at']);
                            echo $date->format('d/m/Y H:i');
                            ?>
                        </span>
                        <span class="meta-views">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                            <?php echo number_format($post['view_count'] + 1); ?> lượt xem
                        </span>
                        <?php if ($post['category_name']): ?>
                        <span class="meta-category" style="background-color: <?php echo htmlspecialchars($post['category_color'] ?? '#3B82F6'); ?>">
                            <?php echo htmlspecialchars($post['category_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Post title -->
                    <h1 class="post-detail-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <!-- Post content -->
                    <div class="post-detail-body">
                        <?php echo $post['content']; ?>
                    </div>
                </article>
                
                <!-- Right side: Related posts -->
                <aside class="post-detail-sidebar">
                    <div class="related-posts-container">
                        <h3 class="related-posts-title">Bài viết liên quan</h3>
                        
                        <?php if (!empty($relatedPosts)): ?>
                        <div class="related-posts-list">
                            <?php foreach ($relatedPosts as $related): ?>
                            <a href="?page=post_detail&id=<?php echo $related['id']; ?>" class="related-post-item">
                                <?php if ($related['image']): ?>
                                <div class="related-post-thumbnail">
                                    <img src="assets/img/posts/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" onerror="this.src='assets/img/placeholder.png'" />
                                </div>
                                <?php endif; ?>
                                <div class="related-post-info">
                                    <h4 class="related-post-title"><?php echo htmlspecialchars($related['title']); ?></h4>
                                    <div class="related-post-meta">
                                        <span class="related-post-date">
                                            <?php 
                                            $relatedDate = new DateTime($related['created_at']);
                                            echo $relatedDate->format('d/m/Y');
                                            ?>
                                        </span>
                                        <span class="related-post-views">
                                            👁 <?php echo number_format($related['view_count']); ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="no-related-posts">Không có bài viết liên quan</p>
                        <?php endif; ?>
                    </div>
                </aside>
                
            </div>
        </div>
    </section>
    
    <?php include 'views/layouts/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
