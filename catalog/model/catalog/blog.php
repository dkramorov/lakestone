<?php
class ModelCatalogBlog extends Model {
	public function getInformation($blog_id) {
		$query = $this->db->query("SELECT DISTINCT *,
			(SELECT AVG(rating) AS total 
				FROM " .  DB_PREFIX .  "review r1 
				WHERE r1.product_id = i.blog_id 
				AND r1.status = '1' 
				AND r1.review_type = '2'
				AND r1.rating > 0
				GROUP BY r1.product_id
			) AS rating,
			(SELECT COUNT(*) AS total 
				FROM " .  DB_PREFIX .  "review r2 
				WHERE r2.product_id = i.blog_id 
				AND r2.status = '1'
				AND r2.review_type = '2'
				AND r2.rating > 0
				GROUP BY r2.product_id
			) AS reviews
		FROM " . DB_PREFIX . "blog i
		LEFT JOIN " . DB_PREFIX . "blog_description id ON (i.blog_id = id.blog_id)
		LEFT JOIN " . DB_PREFIX . "blog_to_store i2s ON (i.blog_id = i2s.blog_id)
		WHERE i.blog_id = '" . (int)$blog_id . "'
		AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
		AND i.status = '1'
		");

		return $query->row;
	}

	public function getInformations($limit = 0, $offset = 0) {

		$query = $this->db->query("SELECT count(*) AS `total` FROM " .  DB_PREFIX .  "blog i
			LEFT JOIN " .  DB_PREFIX .  "blog_description id ON (i.blog_id = id.blog_id)
			LEFT JOIN " .  DB_PREFIX .  "blog_to_store i2s ON (i.blog_id = i2s.blog_id)
			WHERE id.language_id = '" . (int)$this->config->get('config_language_id') .  "' AND
			i2s.store_id = '" . (int)$this->config->get('config_store_id') .  "' AND
			i.status = '1'
		");
		$total = $query->row['total'];

		$sql = "SELECT * FROM " .  DB_PREFIX .  "blog i
			LEFT JOIN " .  DB_PREFIX .  "blog_description id ON (i.blog_id = id.blog_id)
			LEFT JOIN " .  DB_PREFIX .  "blog_to_store i2s ON (i.blog_id = i2s.blog_id)
			WHERE id.language_id = '" . (int)$this->config->get('config_language_id') .  "' AND
			i2s.store_id = '" . (int)$this->config->get('config_store_id') .  "' AND
			i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC";
		
		if ($limit)
			$sql .= " LIMIT " . (int)$limit;
		if ($offset)
			$sql .= " OFFSET " . (int)$offset;
		
		$query = $this->db->query($sql);
		
		return array(
			'total' => $total,
			'rows'	=> $query->rows,
		);
	}

	public function getInformationLayoutId($blog_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_to_layout WHERE blog_id = '" . (int)$blog_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}
}