<?php
// models/PostModel.php

require_once __DIR__ . '/BaseModel.php';

class PostModel extends BaseModel {

    
    /**
     * Get all active categories with post count
     * @return array
     */
    public function getCategories() {
        $query = "SELECT pc.*, COUNT(p.id) as post_count 
            FROM post_categories pc 
            LEFT JOIN posts p ON pc.id = p.category_id AND p.status = 'published'
            WHERE pc.status = 'active' 
            GROUP BY pc.id 
            ORDER BY pc.display_order ASC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get post by ID
     * @param int $postId
     * @return array|null
     */
    public function getPostById($postId) {
        $query = "SELECT 
            p.id,
            p.title,
            p.content,
            p.image,
            p.view_count,
            p.created_at,
            p.category_id,
            u.username as author_name,
            pc.name as category_name,
            pc.color as category_color
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN post_categories pc ON p.category_id = pc.id
        WHERE p.id = ? AND p.status = 'published'";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$postId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting post: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get posts with pagination and filters
     * @param int $page
     * @param int $limit
     * @param int|null $categoryId
     * @param string $search
     * @return array
     */
    public function getPosts($page = 1, $limit = 3, $categoryId = null, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT 
            p.id,
            p.title,
            p.content,
            p.image,
            p.view_count,
            p.created_at,
            u.username as author_name,
            pc.name as category_name,
            pc.color as category_color
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN post_categories pc ON p.category_id = pc.id
        WHERE p.status = 'published'";
        
        $params = [];
        
        // Add category filter
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        // Add search filter
        if ($search) {
            $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Order by newest first
        $sql .= " ORDER BY p.created_at DESC";
        
        // Add limit and offset
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total posts with filters
     * @param int|null $categoryId
     * @param string $search
     * @return int
     */
    public function countPosts($categoryId = null, $search = '') {
        $sql = "SELECT COUNT(*) as total FROM posts p WHERE p.status = 'published'";
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($search) {
            $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get related posts by category
     * @param int $categoryId
     * @param int $excludePostId
     * @param int $limit
     * @return array
     */
    public function getRelatedPosts($categoryId, $excludePostId, $limit = 5) {
        if (!$categoryId) {
            return [];
        }
        
        $query = "SELECT 
            p.id,
            p.title,
            p.image,
            p.view_count,
            p.created_at
        FROM posts p
        WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
        ORDER BY p.created_at DESC
        LIMIT ?";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$categoryId, $excludePostId, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting related posts: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Increment post view count
     * @param int $postId
     * @return bool
     */
    public function incrementViewCount($postId) {
        $query = "UPDATE posts SET view_count = view_count + 1 WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$postId]);
        } catch (PDOException $e) {
            error_log("Error incrementing view count: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new post
     * @param array $data
     * @return int|false Post ID or false on failure
     */
    public function createPost($data) {
        $query = "INSERT INTO posts (user_id, category_id, title, content, image, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $data['user_id'],
                $data['category_id'] ?? null,
                $data['title'],
                $data['content'],
                $data['image'] ?? null,
                $data['status'] ?? 'draft'
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update post
     * @param int $postId
     * @param array $data
     * @return bool
     */
    public function updatePost($postId, $data) {
        $query = "UPDATE posts SET 
                  category_id = ?, 
                  title = ?, 
                  content = ?, 
                  image = ?, 
                  status = ?
                  WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['category_id'] ?? null,
                $data['title'],
                $data['content'],
                $data['image'] ?? null,
                $data['status'] ?? 'draft',
                $postId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating post: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete post
     * @param int $postId
     * @return bool
     */
    public function deletePost($postId) {
        $query = "DELETE FROM posts WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$postId]);
        } catch (PDOException $e) {
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all posts for admin (including drafts and archived)
     * @param string $status Filter by status (optional)
     * @return array
     */
    public function getAllPostsForAdmin($status = null, $limit = null, $offset = 0) {
        $query = "SELECT 
            p.id,
            p.title,
            p.content,
            p.image,
            p.view_count,
            p.status,
            p.created_at,
            p.updated_at,
            u.username as author_name,
            pc.name as category_name,
            pc.color as category_color,
            (SELECT COUNT(*) FROM post_reactions WHERE post_id = p.id) as reaction_count,
            (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN post_categories pc ON p.category_id = pc.id";
        
        if ($status) {
            $query .= " WHERE p.status = ?";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT ? OFFSET ?";
        }
        
        try {
            $stmt = $this->db->prepare($query);
            
            if ($status && $limit !== null) {
                $stmt->execute([$status, $limit, $offset]);
            } elseif ($status) {
                $stmt->execute([$status]);
            } elseif ($limit !== null) {
                $stmt->execute([$limit, $offset]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting posts for admin: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get post by ID for admin (any status)
     * @param int $postId
     * @return array|null
     */
    public function getPostByIdForAdmin($postId) {
        $query = "SELECT 
            p.*,
            u.username as author_name,
            pc.name as category_name
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN post_categories pc ON p.category_id = pc.id
        WHERE p.id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$postId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting post for admin: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get reactions for a post
     * @param int $postId
     * @return array
     */
    public function getPostReactions($postId) {
        $query = "SELECT 
            pr.*,
            CASE 
                WHEN pr.user_type = 'customer' THEN u.username
                WHEN pr.user_type = 'admin' THEN a.username
                ELSE pr.user_name
            END as display_name
        FROM post_reactions pr
        LEFT JOIN users u ON pr.user_type = 'customer' AND pr.user_id = u.id
        LEFT JOIN admins a ON pr.user_type = 'admin' AND pr.user_id = a.id
        WHERE pr.post_id = ?
        ORDER BY pr.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$postId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting post reactions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get comments for a post
     * @param int $postId
     * @return array
     */
    public function getPostComments($postId) {
        $query = "SELECT 
            pc.*,
            CASE 
                WHEN pc.user_type = 'customer' THEN u.username
                WHEN pc.user_type = 'admin' THEN a.username
                ELSE pc.user_name
            END as display_name
        FROM post_comments pc
        LEFT JOIN users u ON pc.user_type = 'customer' AND pc.user_id = u.id
        LEFT JOIN admins a ON pc.user_type = 'admin' AND pc.user_id = a.id
        WHERE pc.post_id = ?
        ORDER BY pc.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([$postId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting post comments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count posts by status
     * @param string|null $status
     * @return int
     */
    public function countPostsByStatus($status = null) {
        $query = "SELECT COUNT(*) FROM posts";
        
        if ($status) {
            $query .= " WHERE status = ?";
        }
        
        try {
            $stmt = $this->db->prepare($query);
            if ($status) {
                $stmt->execute([$status]);
            } else {
                $stmt->execute();
            }
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Delete comment
     * @param int $commentId
     * @return bool
     */
    public function deleteComment($commentId) {
        $query = "DELETE FROM post_comments WHERE id = ?";
        
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$commentId]);
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }
}
?>
