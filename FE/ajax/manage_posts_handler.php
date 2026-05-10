<?php
session_start();
require_once '../models/PostModel.php';
require_once '../models/UserModel.php';

// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userModel = new UserModel();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin only.']);
    exit;
}

$postModel = new PostModel();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_posts':
        $status = $_GET['status'] ?? null;
        $page = intval($_GET['page'] ?? 1);
        $limit = intval($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        $posts = $postModel->getAllPostsForAdmin($status, $limit, $offset);
        $totalPosts = $postModel->countPostsByStatus($status);
        $totalPages = ceil($totalPosts / $limit);
        
        echo json_encode([
            'success' => true, 
            'posts' => $posts,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalPosts' => $totalPosts
        ]);
        break;
    
    case 'get_post':
        $postId = intval($_GET['id'] ?? 0);
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        $post = $postModel->getPostByIdForAdmin($postId);
        if ($post) {
            echo json_encode(['success' => true, 'post' => $post]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found']);
        }
        break;
    
    case 'create_post':
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $categoryId = intval($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        
        // Check if content has actual text (strip HTML tags for validation)
        $contentText = trim(strip_tags($content));
        if (empty($title) || empty($contentText)) {
            echo json_encode(['success' => false, 'message' => 'Title and content are required']);
            exit;
        }
        
        // Handle file upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image']);
            if ($uploadResult['success']) {
                $image = $uploadResult['filename'];
            } else {
                echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                exit;
            }
        }
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'title' => $title,
            'content' => $content,
            'image' => $image,
            'status' => $status
        ];
        
        $postId = $postModel->createPost($data);
        if ($postId) {
            echo json_encode(['success' => true, 'message' => 'Post created successfully', 'post_id' => $postId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create post']);
        }
        break;
    
    case 'update_post':
        $postId = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $categoryId = intval($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';
        
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        // Check if content has actual text (strip HTML tags for validation)
        $contentText = trim(strip_tags($content));
        if (empty($title) || empty($contentText)) {
            echo json_encode(['success' => false, 'message' => 'Title and content are required']);
            exit;
        }
        
        // Get current post to keep existing image if no new upload
        $currentPost = $postModel->getPostByIdForAdmin($postId);
        $image = $currentPost['image'];
        
        // Handle file upload if new image provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image']);
            if ($uploadResult['success']) {
                // Delete old image if exists
                if ($image && file_exists('../assets/img/posts/' . $image)) {
                    unlink('../assets/img/posts/' . $image);
                }
                $image = $uploadResult['filename'];
            } else {
                echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                exit;
            }
        }
        
        $data = [
            'category_id' => $categoryId > 0 ? $categoryId : null,
            'title' => $title,
            'content' => $content,
            'image' => $image,
            'status' => $status
        ];
        
        $success = $postModel->updatePost($postId, $data);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update post']);
        }
        break;
    
    case 'delete_post':
        $postId = intval($_POST['id'] ?? 0);
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        
        $success = $postModel->deletePost($postId);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
        }
        break;
    
    case 'get_reactions':
        $postId = intval($_GET['post_id'] ?? 0);
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        $reactions = $postModel->getPostReactions($postId);
        echo json_encode(['success' => true, 'reactions' => $reactions]);
        break;
    
    case 'get_comments':
        $postId = intval($_GET['post_id'] ?? 0);
        if ($postId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
            exit;
        }
        $comments = $postModel->getPostComments($postId);
        echo json_encode(['success' => true, 'comments' => $comments]);
        break;
    
    case 'delete_comment':
        $commentId = intval($_POST['id'] ?? 0);
        if ($commentId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid comment ID']);
            exit;
        }
        
        $success = $postModel->deleteComment($commentId);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
        break;
    
    case 'get_categories':
        $categories = $postModel->getCategories();
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle image upload
 * @param array $file - $_FILES['image']
 * @return array - ['success' => bool, 'filename' => string, 'message' => string]
 */
function handleImageUpload($file) {
    // Check if file is uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Check file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'post_' . time() . '_' . uniqid() . '.' . $extension;
    
    // Create upload directory if not exists
    $uploadDir = '../assets/img/posts/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    $destination = $uploadDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}
?>
