<?php
header('Content-Type: application/json');

require_once '../models/PostModel.php';

$postModel = new PostModel(); // auto-connects via BaseModel

// Get parameters
$page       = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit      = isset($_GET['limit']) ? intval($_GET['limit']) : 3;
$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get posts
$posts = $postModel->getPosts($page, $limit, $categoryId, $search);

// Get total count
$totalPosts = $postModel->countPosts($categoryId, $search);

// Calculate if there are more posts
$offset  = ($page - 1) * $limit;
$hasMore = ($offset + $limit) < $totalPosts;

echo json_encode([
    'success' => true,
    'posts'   => $posts,
    'hasMore' => $hasMore,
    'total'   => $totalPosts,
    'page'    => $page
]);
