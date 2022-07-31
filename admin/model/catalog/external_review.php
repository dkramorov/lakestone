<?php
class ModelCatalogExternalReview extends Model {
	public function addReview($data) {
		$this->db->query("INSERT INTO " .  DB_PREFIX .  "external_review SET 
			author_name = '" . $this->db->escape($data['author_name']) .  "', 
			author_avatar_link = '" . $this->db->escape($data['author_avatar_link']) .  "', 
			external_link = '" . $this->db->escape($data['external_link']) .  "', 
			author_region = '" . $this->db->escape($data['author_region']) .  "', 
			rating_text = '" . $this->db->escape($data['rating_text']) .  "', 
			date_review = '" . $this->db->escape($data['date_review']) .  "', 
			source_id = '" . (int)$data['source_id'] .  "', 
			`values` = '" . $this->db->escape(strip_tags($data['values'])) .  "', 
			defects = '" . $this->db->escape(strip_tags($data['defects'])) .  "', 
			`comment` = '" . $this->db->escape(strip_tags($data['comment'])) .  "', 
			rating_value = '" . (int)$data['rating_value'] .  "', 
			`status` = '" .  (int)$data['status'] .  "',
			date_added = now()"
		);

    return $this->db->getLastId();
	}

	public function editReview($review_id, $data) {
		$this->db->query("UPDATE " .  DB_PREFIX .  "external_review SET 
			author_name = '" . $this->db->escape($data['author_name']) .  "', 
			author_avatar_link = '" . $this->db->escape($data['author_avatar_link']) .  "', 
			external_link = '" . $this->db->escape($data['external_link']) .  "', 
			author_region = '" . $this->db->escape($data['author_region']) .  "', 
			rating_text = '" . $this->db->escape($data['rating_text']) .  "', 
			date_review = '" . $this->db->escape($data['date_review']) .  "', 
			source_id = '" . (int)$data['source_id'] .  "', 
			`values` = '" . $this->db->escape(strip_tags($data['values'])) .  "', 
			defects = '" . $this->db->escape(strip_tags($data['defects'])) .  "', 
			`comment` = '" . $this->db->escape(strip_tags($data['comment'])) .  "', 
			rating_value = '" . (int)$data['rating_value'] .  "', 
			`status` = '" .  (int)$data['status'] .  "',
			date_modified = NOW()
			WHERE review_id = '" .  (int)$review_id .  "'"
		);

	}

	public function deleteReview($review_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "external_review WHERE review_id = '" . (int)$review_id . "'");

		$this->cache->delete('product');
	}

	public function getReview($review_id) {
		$query = $this->db->query("SELECT * from oc_external_review r WHERE r.review_id = '" .  (int)$review_id .  "'");

		return $query->row;
	}

  public function getSources() {

    $query = $this->db->query("SELECT * FROM oc_external_review_source");
    return $query->rows;

  }

	public function getReviews($data = array()) {
		$sql = "SELECT review_id, s.name source, date_review, author_name, rating_value, rating_text, status FROM oc_external_review r LEFT JOIN oc_external_review_source s ON r.source_id = s.source_id";
		$where = '';
		
		if (isset($data['filter_source'])) {
			$where .= " AND r.source_id = '" . (int)$data['filter_source'] . "'";
		}

		if (!empty($data['filter_rating'])) {
			$where .= " AND r.rating_text LIKE '%" . $this->db->escape($data['filter_rating']) . "%'";
		}

		if (!empty($data['filter_author'])) {
			$where .= " AND r.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_review'])) {
			$where .= " AND DATE(r.date_review) = DATE('" . $this->db->escape($data['filter_date_review']) . "')";
		}

		$sort_data = array(
			'r.source_id',
			'r.author_name',
			'r.rating_value',
			'r.status',
			'r.date_review'
		);
		
		if (!empty($where)) {
			$sql .= " WHERE " . substr($where, 4);
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_review";
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

	public function getTotalReviews($data = array()) {
    $sql = "SELECT count(*) total FROM oc_external_review r";
    $where = '';

    if (isset($data['filter_review_source'])) {
      $where .= " AND r.source_id = '" . (int)$data['filter_review_source'] . "'";
    }

    if (!empty($data['filter_rating'])) {
      $where .= " AND r.rating_text LIKE '%" . $this->db->escape($data['filter_rating']) . "%'";
    }

    if (!empty($data['filter_author'])) {
      $where .= " AND r.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
    }

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
      $where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
    }

    if (!empty($data['filter_date_review'])) {
      $where .= " AND DATE(r.date_review) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }

    if (!empty($where)) {
      $sql .= " WHERE " . substr($where, 4);
    }

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReviewsAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "external_review WHERE status = '0'");

		return $query->row['total'];
	}
}