<?php
class ModelExtensionModulePotesua extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "product_custom_icons` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`product_id` INT(11) NOT NULL,
				`image` varchar(255) NOT NULL,
				`name` varchar(255) NOT NULL,
				`sort_order` INT(11) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_custom_icons`");
	}

    public function import($string) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_icons");
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
			if (substr($line, 0, 1) != '#') {
	            $part = explode(' - ', $line, 2);

	            if (isset($part[1])) {
	                $this->db->query(
	                    "INSERT INTO " . DB_PREFIX . "product_custom_icons SET id = '" . (int)$part[0]
                        . "', image = '" . $this->db->escape($part[1])
                        . "', sort_order = '" . (int)part[2]
                        . "', name = '" . $this->db->escape($part[3]) . "'"
                    );
	            }
			}
        }
    }

	public function deleteCustomIcons($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_custom_icons WHERE product_id = '" . (int)$product_id . "'");
	}

    public function getCustomIcons($product_id) {
        $sql = "SELECT id, product_id, image, sort_order, name from " . DB_PREFIX . "product_custom_icons WHERE product_id = '"
            . (int)$product_id . "' ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		return $query->rows;
    }


    public function addCustomIcons($data, $product_id=0) {
        if (isset($data['product_custom_icon'])) {
            $this->deleteCustomIcons($product_id);
            foreach ($data['product_custom_icon'] as $product_custom_icon) {
                $this->db->query(
                    "INSERT INTO " . DB_PREFIX . "product_custom_icons SET product_id = '" . (int)$product_id
                    . "', image = '" . $this->db->escape($product_custom_icon['image'])
                    . "', sort_order = '" . (int) $product_custom_icon['sort_order']
                    . "', name = '" . $this->db->escape($product_custom_icon['name']) . "'"
                );
            }
        }
    }
}
