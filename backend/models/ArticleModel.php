<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ArticleModel — Articles (blog posts) published by admins.
 */
class ArticleModel extends BaseModel {

    /**
     * Get paginated published articles.
     */
    public function getPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;

        $countStmt = $this->db->query(
            'SELECT COUNT(*) FROM articles WHERE published_at IS NOT NULL AND published_at <= NOW()'
        );
        $total = (int) $countStmt->fetchColumn();

        // Safe Dynamic Sorting (Whitelist)
        $allowedSort = ['id', 'title', 'published_at', 'created_at'];
        $sortBy = in_array($filters['sort'] ?? '', $allowedSort) ? $filters['sort'] : 'published_at';
        $direction = strtoupper($filters['dir'] ?? '') === 'ASC' ? 'ASC' : 'DESC';

        $stmt = $this->db->prepare(
            "SELECT a.id, a.title, a.slug, a.thumbnail_url,
                    a.meta_title, a.meta_description,
                    a.published_at, a.created_at,
                    u.fullname as author_name
             FROM articles a
             LEFT JOIN users u ON a.admin_id = u.id
             WHERE a.published_at IS NOT NULL AND a.published_at <= NOW()
             ORDER BY a.$sortBy $direction
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'limit'       => $limit,
            'total_pages' => $limit > 0 ? (int) ceil($total / $limit) : 0,
        ];
    }

    /**
     * Get article detail by slug.
     */
    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.fullname as author_name
             FROM articles a
             LEFT JOIN users u ON a.admin_id = u.id
             WHERE a.slug = ? AND a.published_at IS NOT NULL'
        );
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Admin: Get all articles (including unpublished) with filters and sorting.
     */
    public function adminGetPaginated(int $page = 1, int $limit = 10, array $filters = []): array {
        $offset = ($page - 1) * $limit;
        $where = '1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $where .= ' AND (a.title LIKE ? OR a.content LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        // Safe Dynamic Sorting (Whitelist)
        $allowedSort = ['id', 'title', 'published_at', 'created_at', 'updated_at'];
        $sortBy = in_array($filters['sort'] ?? '', $allowedSort) ? $filters['sort'] : 'created_at';
        $direction = strtoupper($filters['dir'] ?? '') === 'ASC' ? 'ASC' : 'DESC';

        $countSql = "SELECT COUNT(*) FROM articles a WHERE $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT a.*, u.fullname as author_name 
                FROM articles a 
                LEFT JOIN users u ON a.admin_id = u.id
                WHERE $where
                ORDER BY a.$sortBy $direction
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) { $stmt->bindValue($i++, $p); }
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ];
    }

    /**
     * Admin: Create a new article.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO articles (admin_id, title, slug, content, meta_title, meta_description, meta_keywords, thumbnail_url, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['admin_id'],
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['meta_keywords'] ?? null,
            $data['thumbnail_url'] ?? null,
            $data['published_at'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Admin: Update an article.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE articles SET title = ?, slug = ?, content = ?, 
                               meta_title = ?, meta_description = ?, meta_keywords = ?, 
                               thumbnail_url = ?, published_at = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['meta_title'] ?? null,
            $data['meta_description'] ?? null,
            $data['meta_keywords'] ?? null,
            $data['thumbnail_url'] ?? null,
            $data['published_at'] ?? null,
            $id
        ]);
    }

    /**
     * Admin: Delete an article.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Count total articles (for dashboard).
     */
    public function countTotal(): int {
        $stmt = $this->db->query('SELECT COUNT(*) FROM articles');
        return (int) $stmt->fetchColumn();
    }
}
