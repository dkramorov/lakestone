<?php
class ModelCatalogNews extends Model {
	public function addInformation($data) {
		$this->db->query("INSERT INTO " .  DB_PREFIX .  "news SET
			sort_order = '" .  (int)$data['sort_order'] .  "',
			top = '" .  (isset($data['top']) ?  (int)$data['top'] : 0) .  "',
			bottom = '" .  (isset($data['bottom']) ? (int)$data['bottom'] : 0) .  "',
			status = '" . (int)$data['status'] .  "',
			date_added = now()
		");

		$news_id = $this->db->getLastId();

		foreach ($data['news_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "news_description SET 
				news_id = '" .  (int)$news_id .  "',
				language_id = '" .  (int)$language_id .  "', 
				title = '" . $this->db->escape($value['title']) .  "', 
				image = '" .  $this->db->escape($value['image']) . "', 
				announce = '" . $this->db->escape($value['announce']) .  "', 
				description = '" . $this->db->escape($value['description']) .  "', 
				meta_title = '" .  $this->db->escape($value['meta_title']) .  "',
				meta_description = '" . $this->db->escape($value['meta_description']) .  "',
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) .  "'
			"); 
		}

		if (isset($data['news_store'])) {
			foreach ($data['news_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "news_to_store SET news_id = '" . (int)$news_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['news_layout'])) {
			foreach ($data['news_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "news_to_layout SET news_id = '" . (int)$news_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'news_id=" . (int)$news_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('news');

		return $news_id;
	}

	public function editInformation($news_id, $data) {
		$this->db->query("UPDATE " .  DB_PREFIX .  "news SET
			sort_order = '" .  (int)$data['sort_order'] .  "',
			top = '".  (isset($data['top']) ?  (int)$data['top'] : 0) .  "',
			bottom = '" .  (isset($data['bottom']) ? (int)$data['bottom'] : 0) .  "',
			status = '" . (int)$data['status'] .  "' ,
			date_modified = now()
			WHERE news_id = '" . (int)$news_id .  "'
		");

		$this->db->query("DELETE FROM " . DB_PREFIX . "news_description WHERE news_id = '" . (int)$news_id . "'");

		foreach ($data['news_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "news_description SET 
				news_id = '" .  (int)$news_id .  "', language_id = '" .  (int)$language_id .  "',
				title = '" .  $this->db->escape($value['title']) . "', 
				image = '" .  $this->db->escape($value['image']) . "', 
				announce = '" . $this->db->escape($value['announce']) .  "',
				description = '" . $this->db->escape($value['description']) .  "',
				meta_title = '" . $this->db->escape($value['meta_title']) .  "',
				meta_description = '" . $this->db->escape($value['meta_description']) .  "',
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) .  "'
			");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "news_to_store WHERE news_id = '" . (int)$news_id . "'");

		if (isset($data['news_store'])) {
			foreach ($data['news_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "news_to_store SET news_id = '" . (int)$news_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "news_to_layout WHERE news_id = '" . (int)$news_id . "'");

		if (isset($data['news_layout'])) {
			foreach ($data['news_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "news_to_layout SET news_id = '" . (int)$news_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'news_id=" . (int)$news_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'news_id=" . (int)$news_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('news');
	}

	public function deleteInformation($news_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "news WHERE news_id = '" . (int)$news_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "news_description WHERE news_id = '" . (int)$news_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "news_to_store WHERE news_id = '" . (int)$news_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "news_to_layout WHERE news_id = '" . (int)$news_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'news_id=" . (int)$news_id . "'");

		$this->cache->delete('news');
	}

	public function getInformation($news_id) {
		$query = $this->db->query("SELECT DISTINCT *,
			(SELECT keyword FROM " .  DB_PREFIX .  "url_alias WHERE query = 'news_id=" .  (int)$news_id .  "') AS keyword
			FROM " . DB_PREFIX .  "news WHERE news_id = '" .  (int)$news_id . "'
		");

		return $query->row;
	}

	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " .  DB_PREFIX .  "news i 
				LEFT JOIN " .  DB_PREFIX .  "news_description id ON (i.news_id = id.news_id) 
				WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'
			";

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

			return $query->rows;
		} else {
			$news_data = $this->cache->get('news.' . (int)$this->config->get('config_language_id'));

			if (!$news_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news i LEFT JOIN " . DB_PREFIX . "news_description id ON (i.news_id = id.news_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

				$news_data = $query->rows;

				$this->cache->set('news.' . (int)$this->config->get('config_language_id'), $news_data);
			}

			return $news_data;
		}
	}

	public function getInformationDescriptions($news_id) {
		$news_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news_description WHERE news_id = '" . (int)$news_id . "'");

		foreach ($query->rows as $result) {
			$news_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				//'image'		   => (empty($result['image']) ? '/image/cache/no_image-100x100.png' : $result['image']),
				'image'		   => $result['image'],
				'description'      => $result['description'],
				'announce'	   => $result['announce'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $news_description_data;
	}

	public function getInformationStores($news_id) {
		$news_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news_to_store WHERE news_id = '" . (int)$news_id . "'");

		foreach ($query->rows as $result) {
			$news_store_data[] = $result['store_id'];
		}

		return $news_store_data;
	}

	public function getInformationLayouts($news_id) {
		$news_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news_to_layout WHERE news_id = '" . (int)$news_id . "'");

		foreach ($query->rows as $result) {
			$news_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $news_layout_data;
	}

	public function getTotalInformations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "news");

		return $query->row['total'];
	}

	public function getTotalInformationsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "news_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}