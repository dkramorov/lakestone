<?php


class ModelExtensionOzonOrder extends Model {
  
  public function getOrder(string $id) {

    $res = $this->db->query("SELECT * FROM `ozon_order` WHERE id = '". $this->db->escape($id) ."' ");
    return $res->row;

  }
  
  public function updateOrder(string $id, array $data) {
    
    $sql = "";
    foreach ($data as $key => $val) {
      $sql .= "`$key` = '" . $this->db->escape($val) . "',";
    }
    $sql = substr($sql, 0, -1);
    $this->db->query("INSERT INTO `ozon_order` SET " .
        "`id` = '" . $this->db->escape($id) . "', " .
        $sql .
        " ON DUPLICATE KEY UPDATE " .
        $sql
    );
  
  }

}