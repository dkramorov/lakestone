<?php
class ModelCatalogReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " .  DB_PREFIX .  "review SET 
			author = '" . $this->db->escape($data['author']) .  "', 
			product_id = '" . (int)$data['product_id'] .  "', 
			text = '" . $this->db->escape(strip_tags($data['text'])) .  "', 
			rating = '" . (int)$data['rating'] .  "', 
			useful_photo = '" . (int)$data['rating_photo'] .  "', 
			useful_description = '" . (int)$data['rating_description'] .  "', 
			review_type = '" .  (int)$data['review_type'] .  "',
			status = '" .  (int)$data['status'] .  "',
			date_added = '" .  $this->db->escape($data['date_added']) .  "'"
		);
		$review_id = $this->db->getLastId();
		if (!empty($data['answer'])) {
			$this->db->query("INSERT INTO " .  DB_PREFIX .  "respond SET 
				review_id = '" .  (int)$review_id .  "',
				respond = '" . $this->db->escape(strip_tags($data['answer'])) .  "', 
				status = '" .  (int)$data['answer_status'] .  "',
				date_responded = NOW()
				ON DUPLICATE KEY UPDATE
				respond = '" . $this->db->escape(strip_tags($data['answer'])) .  "', 
				status = '" .  (int)$data['answer_status'] .  "',
				date_responded = NOW()
			");
		}


		$this->cache->delete('product');

		return $review_id;
	}

	public function editReview($review_id, $data) {
		$this->db->query("UPDATE " .  DB_PREFIX .  "review SET 
			author = '" . $this->db->escape($data['author']) .  "', 
			product_id = '" . (int)$data['product_id'] .  "', 
			text = '" . $this->db->escape(strip_tags($data['text'])) .  "', 
			rating = '" . (int)$data['rating'] .  "', 
			useful_photo = '" . (int)$data['rating_photo'] .  "', 
			useful_description = '" . (int)$data['rating_description'] .  "', 
			review_type = '" .  (int)$data['review_type'] .  "',
			status = '" .  (int)$data['status'] .  "',
			date_added = '" .  $this->db->escape($data['date_added']) .  "',
			date_modified = NOW()
			WHERE review_id = '" .  (int)$review_id .  "'"
		);
		if (!empty($data['image_status'])) {
      $this->db->query("UPDATE " .  DB_PREFIX .  "review_image SET status = 1 WHERE review_id = " . (int) $review_id . " and review_image_id IN (" . $this->db->escape(implode(',', array_keys($data['image_status']))) . ");");
      $this->db->query("UPDATE " .  DB_PREFIX .  "review_image SET status = 0 WHERE review_id = " . (int) $review_id . " and review_image_id NOT IN (" . $this->db->escape(implode(',', array_keys($data['image_status']))) . ");");
    }
		if (!empty($data['answer'])) {
			$this->db->query("INSERT INTO " .  DB_PREFIX .  "respond SET 
				review_id = '" .  (int)$review_id .  "',
				respond = '" . $this->db->escape(strip_tags($data['answer'])) .  "', 
				status = '" .  (int)$data['answer_status'] .  "',
				date_responded = NOW()
				ON DUPLICATE KEY UPDATE
				respond = '" . $this->db->escape(strip_tags($data['answer'])) .  "', 
				status = '" .  (int)$data['answer_status'] .  "',
				date_responded = NOW()
			");
		}

		$this->cache->delete('product');
	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review_image WHERE review_id = '" . (int)$review_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "respond WHERE review_id = '" . (int)$review_id . "'");

		$this->cache->delete('product');
	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT DISTINCT *, r.product_id, r.status, 
			IF (r.review_type = 2, bd.title, pd.name) AS product,
			re.respond AS answer, re.status AS answer_status
			FROM " .  DB_PREFIX .  "review r
			LEFT JOIN " . DB_PREFIX . "respond AS re ON (r.review_id = re.review_id)
			LEFT JOIN " . DB_PREFIX . "blog_description bd ON (r.review_type = 2 AND r.product_id = bd.blog_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.review_type < 2 AND r.product_id = pd.product_id)
			WHERE r.review_id = '" .  (int)$review_id .  "'"
		);

		return $query->row;
	}

	public function getReviewImages($review_id) {
		$query = $this->db->query("SELECT * FROM oc_review_image
			WHERE review_id = '" .  (int)$review_id .  "'"
		);

		return $query->rows;
	}

	public function getReviews($data = array()) {
		$sql = "SELECT r.review_id, r.author, r.rating, r.status, r.date_added,
			IF (r.review_type = 2, bd.title, pd.name) AS name 
			FROM " . DB_PREFIX . "review r 
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "blog_description bd ON (r.review_type = 2 AND r.product_id = bd.blog_id)
		";
		
		$where = '';
		
		if (isset($data['filter_review_type'])) {
			if ($data['filter_review_type'] < 2) {
				$where .= " AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			}
			$where .= " AND r.review_type = '" . (int)$data['filter_review_type'] . "'";
		}

		if (!empty($data['filter_product'])) {
		  if (!empty($data['filter_review_type']) and $data['filter_review_type'] == 2)
        $where .= " AND bd.title LIKE '" . $this->db->escape($data['filter_product']) . "%'";
      else
        $where .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$where .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$where .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			'pd.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		);
		
		if (!empty($where)) {
			$sql .= " WHERE " . substr($where, 4);
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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
		//var_dump($sql);exit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReviews($data = array()) {
		$sql = "SELECT COUNT(*) AS total, IF (r.review_type = 2, bd.title, pd.name) AS name 
			FROM " . DB_PREFIX . "review r 
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "blog_description bd ON (r.review_type = 2 AND r.product_id = bd.blog_id)
		";
		
		$where = '';

		if (isset($data['filter_review_type'])) {
			if ($data['filter_review_type'] < 2) {
				$where .= " AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			}
			$where .= " AND r.review_type = '" . (int)$data['filter_review_type'] . "'";
		}

		if (!empty($data['filter_review_type'])) {
			$where .= " AND r.review_type = '" . (int)$data['filter_review_type'] . "'";
		}

    if (!empty($data['filter_product'])) {
      if (!empty($data['filter_review_type']) and $data['filter_review_type'] == 2)
        $where .= " AND bd.title LIKE '" . $this->db->escape($data['filter_product']) . "%'";
      else
        $where .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
    }

		if (!empty($data['filter_author'])) {
			$where .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$where .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($where)) {
			$sql .= " WHERE " . substr($where, 4);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");

		return $query->row['total'];
	}
}