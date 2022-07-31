<?php

class ModelCatalogWhatsappScreen extends Model {
	public function addScreen($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "whatsapp_screen SET 
			author_name = '" . $this->db->escape($data['author_name']) . "', 
			date_screen = '" . $this->db->escape($data['date_screen']) . "',
			`screen` = '" . $this->db->escape(strip_tags($data['image'])) . "',
			`status` = '" . (int)$data['status'] . "',
			date_added = now()"
		);

		return $this->db->getLastId();
	}

	public function getTotalScreens($data = array()) {
		$sql = "SELECT count(*) total FROM " . DB_PREFIX . "whatsapp_screen w";
		$where = '';

		if (!empty($data['filter_author'])) {
			$where .= " AND w.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where .= " AND w.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_screen'])) {
			$where .= " AND DATE(w.date_screen) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($where)) {
			$sql .= " WHERE " . substr($where, 4);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getScreens($data = array()) {
		$sql = "SELECT screen_id, date_screen, author_name, screen, status FROM " . DB_PREFIX . "whatsapp_screen w";
		$where = '';

		if (!empty($data['filter_author'])) {
			$where .= " AND w.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where .= " AND w.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_screen'])) {
			$where .= " AND DATE(w.date_screen) = DATE('" . $this->db->escape($data['filter_date_screen']) . "')";
		}

		$sort_data = array(
			'w.author_name',
			'w.status',
			'w.date_screen'
		);

		if (!empty($where)) {
			$sql .= " WHERE " . substr($where, 4);
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY w.date_screen";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
//    dd($sql, $data);

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function editScreen($screen_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "whatsapp_screen SET 
			author_name = '" . $this->db->escape($data['author_name']) . "', 
			date_screen = '" . $this->db->escape($data['date_screen']) . "', 
			screen = '" . $this->db->escape($data['image']) . "', 
			`status` = '" . (int)$data['status'] . "'
			WHERE screen_id = '" . (int)$screen_id . "'"
		);
	}

	public function getScreen($screen_id) {
		$query = $this->db->query("SELECT * from " . DB_PREFIX . "whatsapp_screen w WHERE w.screen_id = '" . (int)$screen_id . "'");

		return $query->row;
	}

	public function deleteScreen($screen_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "whatsapp_screen WHERE screen_id = '" . (int)$screen_id . "'");

		$this->cache->delete('product');
	}
}