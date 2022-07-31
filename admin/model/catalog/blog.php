<?php
class ModelCatalogBlog extends Model {
	public function addInformation($data) {
		$this->db->query("INSERT INTO " .  DB_PREFIX .  "blog SET
			sort_order = '" .  (int)$data['sort_order'] .  "',
			top = '" .  (isset($data['top']) ?  (int)$data['top'] : 0) .  "',
			bottom = '" .  (isset($data['bottom']) ? (int)$data['bottom'] : 0) .  "',
			status = '" . (int)$data['status'] .  "',
			date_added = now()
		");

		$blog_id = $this->db->getLastId();

		foreach ($data['blog_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "blog_description SET 
				blog_id = '" .  (int)$blog_id .  "',
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

		if (isset($data['blog_store'])) {
			foreach ($data['blog_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_to_store SET blog_id = '" . (int)$blog_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['blog_layout'])) {
			foreach ($data['blog_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_to_layout SET blog_id = '" . (int)$blog_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'blog_id=" . (int)$blog_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('blog');

		return $blog_id;
	}

	public function editInformation($blog_id, $data) {
		$this->db->query("UPDATE " .  DB_PREFIX .  "blog SET
			sort_order = '" .  (int)$data['sort_order'] .  "',
			top = '".  (isset($data['top']) ?  (int)$data['top'] : 0) .  "',
			bottom = '" .  (isset($data['bottom']) ? (int)$data['bottom'] : 0) .  "',
			status = '" . (int)$data['status'] .  "' ,
			date_modified = now()
			WHERE blog_id = '" . (int)$blog_id .  "'
		");

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_description WHERE blog_id = '" . (int)$blog_id . "'");

		foreach ($data['blog_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "blog_description SET 
				blog_id = '" .  (int)$blog_id .  "', language_id = '" .  (int)$language_id .  "',
				title = '" .  $this->db->escape($value['title']) . "', 
				image = '" .  $this->db->escape($value['image']) . "', 
				announce = '" . $this->db->escape($value['announce']) .  "',
				description = '" . $this->db->escape($value['description']) .  "',
				meta_title = '" . $this->db->escape($value['meta_title']) .  "',
				meta_description = '" . $this->db->escape($value['meta_description']) .  "',
				meta_keyword = '" . $this->db->escape($value['meta_keyword']) .  "'
			");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_to_store WHERE blog_id = '" . (int)$blog_id . "'");

		if (isset($data['blog_store'])) {
			foreach ($data['blog_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_to_store SET blog_id = '" . (int)$blog_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_to_layout WHERE blog_id = '" . (int)$blog_id . "'");

		if (isset($data['blog_layout'])) {
			foreach ($data['blog_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_to_layout SET blog_id = '" . (int)$blog_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'blog_id=" . (int)$blog_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'blog_id=" . (int)$blog_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('blog');
	}

	public function deleteInformation($blog_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog WHERE blog_id = '" . (int)$blog_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_description WHERE blog_id = '" . (int)$blog_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_to_store WHERE blog_id = '" . (int)$blog_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_to_layout WHERE blog_id = '" . (int)$blog_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'blog_id=" . (int)$blog_id . "'");

		$this->cache->delete('blog');
	}

	public function getInformation($blog_id) {
		$query = $this->db->query("SELECT DISTINCT *,
			(SELECT keyword FROM " .  DB_PREFIX .  "url_alias WHERE query = 'blog_id=" .  (int)$blog_id .  "') AS keyword
			FROM " . DB_PREFIX .  "blog WHERE blog_id = '" .  (int)$blog_id . "'
		");

		return $query->row;
	}

	public function getInformations($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " .  DB_PREFIX .  "blog i 
				LEFT JOIN " .  DB_PREFIX .  "blog_description id ON (i.blog_id = id.blog_id) 
				WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'
			";

			if (!empty($data['filter_name'])) {
				$sql .= " AND id.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
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

			return $query->rows;
		} else {
			$blog_data = $this->cache->get('blog.' . (int)$this->config->get('config_language_id'));

			if (!$blog_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog i LEFT JOIN " . DB_PREFIX . "blog_description id ON (i.blog_id = id.blog_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");

				$blog_data = $query->rows;

				$this->cache->set('blog.' . (int)$this->config->get('config_language_id'), $blog_data);
			}

			return $blog_data;
		}
	}

	public function getInformationDescriptions($blog_id) {
		$blog_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_description WHERE blog_id = '" . (int)$blog_id . "'");

		foreach ($query->rows as $result) {
			$blog_description_data[$result['language_id']] = array(
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

		return $blog_description_data;
	}

	public function getInformationStores($blog_id) {
		$blog_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_to_store WHERE blog_id = '" . (int)$blog_id . "'");

		foreach ($query->rows as $result) {
			$blog_store_data[] = $result['store_id'];
		}

		return $blog_store_data;
	}

	public function getInformationLayouts($blog_id) {
		$blog_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_to_layout WHERE blog_id = '" . (int)$blog_id . "'");

		foreach ($query->rows as $result) {
			$blog_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $blog_layout_data;
	}

	public function getTotalInformations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog");

		return $query->row['total'];
	}

	public function getTotalInformationsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}