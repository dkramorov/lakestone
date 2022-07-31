<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'";

		if (!isset($data['full_list'])) {
			$sql .= " AND i.information_id > 0 ";
		}

		$sql .= " ORDER BY i.sort_order, LCASE(id.title) ASC";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getSidebar() {
		$query = $this->db->query("SELECT i.*, id.title FROM " . DB_PREFIX . "information_sidebar AS i
		LEFT JOIN " . DB_PREFIX . "information AS ii ON (i.information_id = ii.information_id AND ii.status = 1)
		LEFT JOIN " . DB_PREFIX . "information_description AS id ON (i.information_id = id.information_id)
		WHERE i.status = '1' ORDER BY i.position, LCASE(i.name) ASC");

		return $query->rows;
	}
}
