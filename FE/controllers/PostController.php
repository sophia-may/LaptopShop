<?php
// controllers/PostController.php

require_once __DIR__ . '/../models/PostModel.php';

class PostController {
    private $postModel;
    
    public function __construct() {
        $this->postModel = new PostModel(); // auto-connects via BaseModel
    }
    
    /**
     * Display posts list page
     */
    public function index() {
        // Get categories with post count for sidebar
        $categories = $this->postModel->getCategories();
        
        // Include the view
        include 'views/client/post.php';
    }
    
    /**
     * Display single post detail page
     */
    public function detail() {
        // Get post ID from URL
        $postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($postId <= 0) {
            header("Location: ?page=post");
            exit();
        }
        
        // Get post details
        $post = $this->postModel->getPostById($postId);
        
        if (!$post) {
            header("Location: ?page=post");
            exit();
        }
        
        // Update view count
        $this->postModel->incrementViewCount($postId);
        
        // Get related posts (same category)
        $relatedPosts = $this->postModel->getRelatedPosts($post['category_id'], $postId);
        
        // Include the view
        include 'views/client/post_detail.php';
    }
}
?>
