<?php
class ModelCatalogFilter extends Model {

	public function askFilterTag($filter_tag) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "filter f WHERE f.filter_tag = '" . $this->db->escape($filter_tag) . "'");

		return $query->row;
	}

	public function translateFilterTag($data) {
	    if ( ! empty($data['filter_tags']) ) {
	        $f_tags = '';
	        foreach ( $data['filter_tags'] as $f_tag )
	            $f_tags .= ", '" . $this->db->escape($f_tag) . "'";
		$query = $this->db->query("
		    SELECT filter_id FROM " . DB_PREFIX . "filter f WHERE
		    f.filter_tag IN ( " . substr($f_tags, 2) . ")
                ");
                
                $ret = '';
                
                foreach ($query->rows as $row) {
		    $ret .= ',' . $row['filter_id'];
		}
		return substr($ret, 1);
            }
	}

	public function searchFilter($data) {
	    if ( ! empty($data['filter']) and
	    	 ! empty($data['category_id']) 
	    ) {
		$query_str = "SELECT sl.seo_link_id, sld.meta_title, sld.meta_description, sld.meta_keyword, sld.description, sld.name, sld.tag_h1,
		f.filter_tag, COUNT(*) AS flen
		FROM " . DB_PREFIX . "seo_link as sl
		LEFT JOIN " . DB_PREFIX . "seo_link_filter as slf ON sl.seo_link_id = slf.seo_link_id
		LEFT JOIN " . DB_PREFIX . "filter as f ON slf.filter_id = f.filter_id
		LEFT JOIN " . DB_PREFIX . "seo_link_description as sld ON sl.seo_link_id = sld.seo_link_id
		WHERE sl.status = 1 AND sl.category_id = " . (int)$data['category_id'] . " AND slf.filter_id in (";
                
	        $f_arr = explode(',', $data['filter']);
	        foreach ( $f_arr as $num => $filter_id ) {
	            $query_str .= (int)$filter_id;
	            if ($num < sizeof($f_arr) - 1) {
	            	$query_str .= ", ";
	            }
		}
		$query_str .= ") GROUP BY slf.seo_link_id HAVING flen = 
		(SELECT COUNT(*) FROM " . DB_PREFIX . "seo_link_filter AS slf2 WHERE slf2.seo_link_id = slf.seo_link_id)
		AND flen = " . sizeof($f_arr);
		$query = $this->db->query($query_str);
		//var_dump($query_str, sizeof($f_arr), $query->row['flen']);
		return $query->row;
            }
	}

}