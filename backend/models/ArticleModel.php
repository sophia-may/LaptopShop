<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * ArticleModel — Articles (blog posts) published by admins.
 */
class ArticleModel extends BaseModel {

    /**
     * Get paginated published articles.
     */
    public function getPaginated(int $page = 1, int $limit = 10): array {
        $offset = ($page - 1) * $limit;

        $countStmt = $this->db->query(
            'SELECT COUNT(*) FROM articles WHERE published_at IS NOT NULL AND published_at <= NOW()'
        );
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT a.id, a.title, a.slug, a.thumbnail_url,
                    a.meta_title, a.meta_description,
                    a.published_at, a.created_at,
                    u.fullname as author_name
             FROM articles a
             LEFT JOIN users u ON a.admin_id = u.id
             WHERE a.published_at IS NOT NULL AND a.published_at <= NOW()
             ORDER BY a.published_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
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
}
