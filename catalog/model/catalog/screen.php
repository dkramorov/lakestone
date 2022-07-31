<?php

class ModelCatalogScreen extends Model {
	public function getScreensByProductId($product_id, $start = 0, $limit = 8) {
		if ($start < 0) $start = 0;
		if ($limit < 1) $limit = 8;
/*
		$query = $this->db->query("SELECT
				w.screen_id,
				w.product_id,
				w.author_name,
				w.screen,
				w.date_screen,
				w.date_added
			FROM " . DB_PREFIX . "whatsapp_screen w
			WHERE w.status = '1'
			ORDER BY RAND()
			LIMIT " . (int)$start . "," . (int)$limit
		);
*/

		$query = $this->db->query("SELECT
				w.screen_id,
				w.product_id,
				w.author_name,
				w.screen,
				w.date_screen,
				w.date_added
			FROM " . DB_PREFIX . "whatsapp_screen w
			WHERE w.status = '1'
			ORDER BY RAND()");
		
		// ORDER BY w.date_added DESC

		return $query->rows;
	}

	public function getTotalScreeensByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "whatsapp_screen w WHERE w.status = '1'");

		return $query->row['total'];
	}
}