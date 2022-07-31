<?php

class ModelCatalogSeoLink extends Model {

  public function getLinks() {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_link sl
		LEFT JOIN " . DB_PREFIX . "seo_link_description sld ON sl.seo_link_id = sld.seo_link_id
		LEFT JOIN " . DB_PREFIX . "seo_link_filter slf ON sl.seo_link_id = slf.seo_link_id
		LEFT JOIN " . DB_PREFIX . "filter f ON f.filter_id = slf.filter_id
		WHERE sl.status = 1
		GROUP BY slf.seo_link_id
		HAVING COUNT(*) = 1
		");

    return $query->rows;
  }

  public function getCategoryLinks($category_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_link sl
		LEFT JOIN " . DB_PREFIX . "seo_link_description sld ON sl.seo_link_id = sld.seo_link_id
		LEFT JOIN " . DB_PREFIX . "seo_link_filter slf ON sl.seo_link_id = slf.seo_link_id
		LEFT JOIN " . DB_PREFIX . "filter f ON f.filter_id = slf.filter_id
		WHERE sl.status = 1
                AND sl.category_id = $category_id
		GROUP BY slf.seo_link_id
		HAVING COUNT(*) = 1
		");

    return $query->rows;
  }

  public function getLink($seo_link_id) {
    $query = $this->db->query("SELECT sl.*, sld.*, GROUP_CONCAT(f.filter_tag) filter_tag FROM " . DB_PREFIX . "seo_link sl
		LEFT JOIN " . DB_PREFIX . "seo_link_description sld ON sl.seo_link_id = sld.seo_link_id
		LEFT JOIN " . DB_PREFIX . "seo_link_filter slf ON sl.seo_link_id = slf.seo_link_id
		LEFT JOIN " . DB_PREFIX . "filter f ON f.filter_id = slf.filter_id
		WHERE sl.status = 1 AND
		sl.seo_link_id = '" . (int) $seo_link_id . "' GROUP BY sl.seo_link_id");
    
    return $query->row;
  }

  public function getLinkFilters($seo_link_id) {
    $query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "seo_link_filter
		WHERE seo_link_id = '" . (int) $seo_link_id . "'");

    $res = '';

    if ($query->num_rows == 0)
      return '';

    foreach ($query->rows as $result) {
      $res .= ',' . $result['filter_id'];
    }

    return substr($res, 1);
  }

  public function getCategory($category_id) {
    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int) $category_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1'");

    return $query->row;
  }

  public function getCategories($parent_id = 0) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

    return $query->rows;
  }

  public function getCategoryLayoutId($category_id) {
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int) $category_id . "' AND store_id = '" . (int) $this->config->get('config_store_id') . "'");

    if ($query->num_rows) {
      return $query->row['layout_id'];
    } else {
      return 0;
    }
  }

  public function getTotalCategoriesByCategoryId($parent_id = 0) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1'");

    return $query->row['total'];
  }

}
