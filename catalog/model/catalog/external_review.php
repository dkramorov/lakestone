<?php
class ModelCatalogExternalReview extends Model {

  public function getReviews($data = array()) {
    $sql = "SELECT r.*, s.name source, unix_timestamp(r.date_review) date_review_unixtime FROM oc_external_review r LEFT JOIN oc_external_review_source s ON r.source_id = s.source_id";
    $where = '';

    if (isset($data['filter_source'])) {
      $where .= " AND r.source_id = '" . (int)$data['filter_source'] . "'";
    }

    if (!empty($data['filter_rating'])) {
      $where .= " AND r.rating_text LIKE '%" . $this->db->escape($data['filter_rating']) . "%'";
    }

    if (!empty($data['filter_author'])) {
      $where .= " AND r.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
    }

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
      $where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
    }

    if (!empty($data['filter_date_review'])) {
      $where .= " AND DATE(r.date_review) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }

    $sort_data = array(
      'r.source_id',
      'r.author_name',
      'r.rating_value',
      'r.status',
      'r.date_review'
    );

    if (!empty($where)) {
      $sql .= " WHERE " . substr($where, 4);
    }

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $data['sort'];
    } else {
      $sql .= " ORDER BY r.date_review";
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
//    dd($sql, $data);

    $query = $this->db->query($sql);

    return $query->rows;
  }

  public function getTotalReviews($data = array()) {
    $sql = "SELECT count(*) total FROM oc_external_review r";
    $where = '';

    if (isset($data['filter_review_source'])) {
      $where .= " AND r.source_id = '" . (int)$data['filter_review_source'] . "'";
    }

    if (!empty($data['filter_rating'])) {
      $where .= " AND r.rating_text LIKE '%" . $this->db->escape($data['filter_rating']) . "%'";
    }

    if (!empty($data['filter_author'])) {
      $where .= " AND r.author_name LIKE '%" . $this->db->escape($data['filter_author']) . "%'";
    }

    if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
      $where .= " AND r.status = '" . (int)$data['filter_status'] . "'";
    }

    if (!empty($data['filter_date_review'])) {
      $where .= " AND DATE(r.date_review) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
    }

    if (!empty($where)) {
      $sql .= " WHERE " . substr($where, 4);
    }

    $query = $this->db->query($sql);

    return $query->row['total'];
  }

}