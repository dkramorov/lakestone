<?php
class ModelExtensionModulePote233 extends Model {
    public function getCategories() {
		$query = $this->db->query("SELECT pote233_id, (SELECT name FROM `" . DB_PREFIX . "pote233` gbc WHERE gbc.pote233_id = gbc2c.pote233_id) AS pote233, category_id, (SELECT name FROM `" . DB_PREFIX . "category_description` cd WHERE cd.category_id = gbc2c.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS category FROM `" . DB_PREFIX . "pote233_to_category` gbc2c ORDER BY pote233 ASC");

		return $query->rows;
    }

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pote233_to_category`");

		return $query->row['total'];
    }
}
