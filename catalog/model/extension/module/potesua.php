<?php
class ModelExtensionModulePotesua extends Model {
    public function getCustomIcons($product_id=0) {
        $sql = "SELECT id, product_id, image, sort_order, name from " . DB_PREFIX . "product_custom_icons WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC";
        $query = $this->db->query($sql);
        return$query->rows;
    }
}
