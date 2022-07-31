<?php
class ModelCatalogFaq extends Model {
	public function addInformation($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "faq SET sort_order = '" . (int)$data['sort_order'] . "', category = '" . (int)$data['category_id'] . "', status = '" . (int)$data['status'] . "'");

		$faq_id = $this->db->getLastId();

		foreach ($data['faq_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "faq_description SET faq_id = '" . (int)$faq_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', `text` = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('faq');

		return $faq_id;
	}

	public function editInformation($faq_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "faq SET sort_order = '" . (int)$data['sort_order'] . "', category = '" . (int)$data['category_id'] . "', status = '" . (int)$data['status'] . "' WHERE faq_id = '" . (int)$faq_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "faq_description WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($data['faq_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "faq_description SET faq_id = '" . (int)$faq_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', `text` = '" . $this->db->escape($value['description']) . "'");
		}

		$this->cache->delete('faq');
	}

	public function deleteInformation($faq_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "faq WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "faq_description WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "faq_to_store WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "faq_to_layout WHERE faq_id = '" . (int)$faq_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'faq_id=" . (int)$faq_id . "'");

		$this->cache->delete('faq');
	}

	public function getInformation($faq_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'faq_id=" . (int)$faq_id . "') AS keyword FROM " . DB_PREFIX . "faq WHERE faq_id = '" . (int)$faq_id . "'");

		return $query->row;
	}

	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "faq i LEFT JOIN " . DB_PREFIX . "faq_description id ON (i.faq_id = id.faq_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			if (isset($data['filter_name'])) {
				$sql .= " AND id.title like '%" . $this->db->escape($data['filter_name']) . "%' ";
			}

			if (!isset($data['full_list'])) {
				$sql .= " AND i.faq_id > 0 ";
			}

			$sort_data = array(
				'id.title',
				'i.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY id.title";
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

			$query = $this->db->query($sql);

			$faq_categories = $this->getCategories();
			foreach ($query->rows as &$row) {
				foreach ($faq_categories as $category) {
					if ($row['category'] == $category['faq_category_id'])
						$row['category'] = $category['name'];
				}
			}

			return $query->rows;
		} else {
			$faq_data = $this->cache->get('faq.' . (int)$this->config->get('config_language_id'));

			if (!$faq_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "faq i LEFT JOIN " . DB_PREFIX . "faq_description id ON (i.faq_id = id.faq_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

				$faq_data = $query->rows;

				$this->cache->set('faq.' . (int)$this->config->get('config_language_id'), $faq_data);
			}

			return $faq_data;
		}
	}

	public function getInformationDescriptions($faq_id) {
		$faq_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "faq_description WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($query->rows as $result) {
			$faq_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'description'      => $result['text'],
				// 'meta_title'       => $result['meta_title'],
				// 'meta_description' => $result['meta_description'],
				// 'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $faq_description_data;
	}

	public function getInformationStores($faq_id) {
		$faq_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "faq_to_store WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($query->rows as $result) {
			$faq_store_data[] = $result['store_id'];
		}

		return $faq_store_data;
	}

	public function getInformationLayouts($faq_id) {
		$faq_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "faq_to_layout WHERE faq_id = '" . (int)$faq_id . "'");

		foreach ($query->rows as $result) {
			$faq_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $faq_layout_data;
	}

	public function getTotalInformations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "faq");

		return $query->row['total'];
	}

	public function getTotalInformationsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "faq_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
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
				'image'						=> 'faq1.png',
			),
			array(
				'faq_category_id'	=> '4',
				'name' 						=> 'Оплата',
				'image'						=> 'faq1.png',
			),
			array(
				'faq_category_id'	=> '5',
				'name' 						=> 'Сервис',
				'image'						=> 'faq1.png',
			),
			array(
				'faq_category_id'	=> '6',
				'name' 						=> 'Другое',
				'image'						=> 'faq1.png',
			),
		);
	}

	public function editSidebar($data) {
		if (isset($data['sidebar'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "faq_sidebar");
			foreach ($data['sidebar'] as $bar) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "faq_sidebar SET
					`faq_id` = '" . (int)$bar['faq_id'] . "',
					`name` = '" . $this->db->escape($bar['name']) . "',
					`type` = '" . (int)$bar['type'] . "',
					`position` = '" . (int)$bar['position'] . "',
					`status` = '" . (int)$bar['status'] . "'
				");
				//var_dump($bar);
			}
		}

	}

}
