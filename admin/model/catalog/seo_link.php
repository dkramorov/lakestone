<?php
class ModelCatalogSeoLink extends Model {
	public function addLink($data, $seo_link_id = 0) {
		$q = $this->db->query("INSERT INTO " .  DB_PREFIX .  "seo_link SET 
		category_id = '" .  (int)$data['category_id'] .  "',
		status = '" . (int)$data['status'] .  "',
		date_modified = NOW(),
		date_added = NOW()");
    
    if ($seo_link_id) {
      $this->db->query("UPDATE " . DB_PREFIX . "seo_link SET seo_link_id = '" . (int) $seo_link_id . "' WHERE seo_link_id = '" . (int) $this->db->getLastId() . "'");
    } else {
      $seo_link_id = $this->db->getLastId();
    }
    
/*		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}*/

		foreach ($data['seo_link_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "seo_link_description SET 
			seo_link_id = '" . (int)$seo_link_id .  "',
			name = '" . $this->db->escape($value['name']) .  "',
			description = '" . $this->db->escape($value['description']) .  "',
                        tag_h1 = '" . $this->db->escape($value['tag_h1']) . "',
			meta_title = '" .  $this->db->escape($value['meta_title']) .  "',
			meta_description = '" . $this->db->escape($value['meta_description']) .  "',
			meta_keyword = '" . $this->db->escape($value['meta_keyword']) .  "'");
		}

		if (isset($data['seo_link_filter'])) {
			foreach ($data['seo_link_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " .  DB_PREFIX .  "seo_link_filter SET
				seo_link_id = '" . (int)$seo_link_id .  "',
				filter_id = '" . (int)$filter_id .  "'"); 
			}
		}

/*
		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// Set which layout to use with this category
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
*/
		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "url_alias SET 
			query = 'seo_link_id=" .  (int)$seo_link_id . "',
			keyword = '" .  $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('seo_link');

		return $seo_link_id;
	}

	public function editLink($seo_link_id, $data) {
		$this->db->query("UPDATE " .  DB_PREFIX .  "seo_link SET
		category_id = '" .  (int)$data['category_id'] .  "',
		status = '" . (int)$data['status'] .  "', 
		date_modified = NOW() 
		WHERE
		seo_link_id = '" .  (int)$seo_link_id .  "'");

/*		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_link_description WHERE seo_link_id = '" . (int)$seo_link_id . "'");

		foreach ($data['seo_link_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " .  DB_PREFIX . "seo_link_description SET 
			seo_link_id = '" . (int)$seo_link_id .  "',
			name = '" . $this->db->escape($value['name']) .  "',
			description = '" . $this->db->escape($value['description']) .  "',
                        tag_h1 = '" . $this->db->escape($value['tag_h1']) . "',
			meta_title = '" .  $this->db->escape($value['meta_title']) .  "',
			meta_description = '" . $this->db->escape($value['meta_description']) .  "',
			meta_keyword = '" . $this->db->escape($value['meta_keyword']) .  "'");
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_link_filter WHERE seo_link_id = '" . (int)$seo_link_id . "'");

		if (isset($data['seo_link_filter'])) {
			foreach ($data['seo_link_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " .  DB_PREFIX .  "seo_link_filter SET
				seo_link_id = '" . (int)$seo_link_id .  "',
				filter_id = '" . (int)$filter_id .  "'"); 
			}
		}

/*		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
*/
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seo_link_id=" . (int)$seo_link_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seo_link_id=" . (int)$seo_link_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('seo_link');
	}

	public function deleteLink($seo_link_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_link WHERE seo_link_id = '" . (int)$seo_link_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_link_description WHERE seo_link_id = '" . (int)$seo_link_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_link_filter WHERE seo_link_id = '" . (int)$seo_link_id . "'");
/*		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE category_id = '" . (int)$category_id . "'");
*/		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seo_link_id=" . (int)$seo_link_id . "'");
//		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE category_id = '" . (int)$category_id . "'");

		$this->cache->delete('seo_link');
	}

	public function getLink($seo_link_id) {
		$query = $this->db->query("SELECT DISTINCT *, cd.name as path,
		(SELECT DISTINCT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'seo_link_id=" . (int) $seo_link_id .  "') AS keyword
		FROM " .  DB_PREFIX . "seo_link sl
		LEFT JOIN " .  DB_PREFIX .  "category_description as cd ON sl.category_id = cd.category_id
		LEFT JOIN " .  DB_PREFIX . "seo_link_description sld ON sl.seo_link_id = sld.seo_link_id
		WHERE sl.seo_link_id = '" . (int)$seo_link_id .  "'");

		return $query->row;
	}

	public function getLinks($data = array()) {
		$sql = "
			SELECT sl.seo_link_id, sld.name, ua.keyword as url
			FROM " .  DB_PREFIX .  "seo_link as sl
			LEFT JOIN " .  DB_PREFIX .  "seo_link_description as sld ON sl.seo_link_id = sld.seo_link_id
			LEFT JOIN " .  DB_PREFIX .  "url_alias as ua ON ua.query = CONCAT('seo_link_id=', sl.seo_link_id)";

		$where = '';
		if (!empty($data['filter_name'])) {
			$where .= " AND sld.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_url'])) {
			$where .= " AND url LIKE '%" . $this->db->escape($data['filter_url']) . "%'";
		}

		if ( !empty($where) )
			$sql .= ' WHERE ' . $where;

		$sort_data = array(
			'name',
			'url'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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
	}

	public function getLinkDescriptions($seo_link_id) {
		$seo_link_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_link_description WHERE seo_link_id = '" . (int)$seo_link_id . "'");

		foreach ($query->rows as $result) {
			$seo_link_description_data[2] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'tag_h1'       		 => $result['tag_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $seo_link_description_data;
	}
	
	public function getCategoryPath($category_id) {
		$query = $this->db->query("SELECT category_id, path_id, level FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

		return $query->rows;
	}
	
	public function getLinkFilters($seo_link_id) {
		$seo_link_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_link_filter WHERE seo_link_id = '" . (int)$seo_link_id . "'");

		foreach ($query->rows as $result) {
			$seo_link_filter_data[] = $result['filter_id'];
		}

		return $seo_link_filter_data;
	}

/*	public function getCategoryStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	public function getCategoryLayouts($category_id) {
		$category_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}*/

	public function getTotalLinks() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "seo_link");

		return $query->row['total'];
	}
	
/*	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}*/
}
