<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * FaqModel — FAQs sorted by display order.
 */
class FaqModel extends BaseModel {

    /**
     * Get all active FAQs sorted by sort_order.
     */
    public function getActive(): array {
        $stmt = $this->db->query(
            'SELECT id, question, answer, sort_order
             FROM faqs
             WHERE is_active = 1
             ORDER BY sort_order ASC, id ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
