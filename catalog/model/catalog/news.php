<?php
class ModelCatalogNews extends Model {
	public function getInformation($news_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "news i LEFT JOIN " . DB_PREFIX . "news_description id ON (i.news_id = id.news_id) LEFT JOIN " . DB_PREFIX . "news_to_store i2s ON (i.news_id = i2s.news_id) WHERE i.news_id = '" . (int)$news_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations($limit = 0, $offset = 0) {

		$query = $this->db->query("SELECT count(*) AS `total` FROM " .  DB_PREFIX .  "news i
			LEFT JOIN " .  DB_PREFIX .  "news_description id ON (i.news_id = id.news_id)
			LEFT JOIN " .  DB_PREFIX .  "news_to_store i2s ON (i.news_id = i2s.news_id)
			WHERE id.language_id = '" . (int)$this->config->get('config_language_id') .  "' AND
			i2s.store_id = '" . (int)$this->config->get('config_store_id') .  "' AND
			i.status = '1'
		");
		$total = $query->row['total'];

		$sql = "SELECT * FROM " .  DB_PREFIX .  "news i
			LEFT JOIN " .  DB_PREFIX .  "news_description id ON (i.news_id = id.news_id)
			LEFT JOIN " .  DB_PREFIX .  "news_to_store i2s ON (i.news_id = i2s.news_id)
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

	public function getInformationLayoutId($news_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news_to_layout WHERE news_id = '" . (int)$news_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}
}