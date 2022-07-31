<?php
class ModelExtensionModulePote233 extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "pote233` (
				`pote233_id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				PRIMARY KEY (`pote233_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "pote233_to_category` (
				`pote233_id` INT(11) NOT NULL,
				`category_id` INT(11) NOT NULL,
				PRIMARY KEY (`pote233_id`, `category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pote233`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "pote233_to_category`");
	}

    public function import($string) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "pote233");

        $lines = explode("\n", $string);

        foreach ($lines as $line) {
			if (substr($line, 0, 1) != '#') {
	            $part = explode(' - ', $line, 2);

	            if (isset($part[1])) {
	                $this->db->query("INSERT INTO " . DB_PREFIX . "pote233 SET pote233_id = '" . (int)$part[0] . "', name = '" . $this->db->escape($part[1]) . "'");
	            }
			}
        }
    }

    public function getGoogleBaseCategories($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "pote233` WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%' ORDER BY name ASC";

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

	public function addCategory($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pote233_to_category WHERE category_id = '" . (int)$data['category_id'] . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "pote233_to_category SET pote233_id = '" . (int)$data['pote233_id'] . "', category_id = '" . (int)$data['category_id'] . "'");
	}

	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pote233_to_category WHERE category_id = '" . (int)$category_id . "'");
	}

    public function getCategories($data = array()) {
        $sql = "SELECT pote233_id, (SELECT name FROM `" . DB_PREFIX . "pote233` gbc WHERE gbc.pote233_id = gbc2c.pote233_id) AS pote233, category_id, (SELECT name FROM `" . DB_PREFIX . "category_description` cd WHERE cd.category_id = gbc2c.category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS category FROM `" . DB_PREFIX . "pote233_to_category` gbc2c ORDER BY pote233 ASC";

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

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pote233_to_category`");

		return $query->row['total'];
    }
}
