<?php
class ModelCatalogFaq extends Model {
	public function getInformation($faq_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "faq i LEFT JOIN " . DB_PREFIX . "faq_description id ON (i.faq_id = id.faq_id) LEFT JOIN " . DB_PREFIX . "faq_to_store i2s ON (i.faq_id = i2s.faq_id) WHERE i.faq_id = '" . (int)$faq_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "faq i LEFT JOIN " . DB_PREFIX . "faq_description id ON (i.faq_id = id.faq_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1'";

		if (isset($data['faq_category'])) {
			$sql .= ' AND `category` = "' . (int)$data['faq_category'] . '"';
		}

		$sql .= " ORDER BY i.sort_order, LCASE(id.title) ASC";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getInformationLayoutId($faq_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "faq_to_layout WHERE faq_id = '" . (int)$faq_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories() {
		return array(
			array(
				'faq_category_id'	=> '1',
				'name' 						=> 'Продукция',
				'image'						=> 'faq1.png',
			),
			array(
				'faq_category_id'	=> '2',
				'name' 						=> 'Доставка',
				'image'						=> 'faq2.png',
			),
			array(
				'faq_category_id'	=> '3',
				'name' 						=> 'Возврат',
				'image'						=> 'faq3.png',
			),
			array(
				'faq_category_id'	=> '4',
				'name' 						=> 'Оплата',
				'image'						=> 'faq4.png',
			),
			array(
				'faq_category_id'	=> '5',
				'name' 						=> 'Сервис',
				'image'						=> 'faq5.png',
			),
			array(
				'faq_category_id'	=> '6',
				'name' 						=> 'Другое',
				'image'						=> 'faq6.png',
			),
		);
	}

}
