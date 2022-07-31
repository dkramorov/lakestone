<?php
class ModelLocalisationLocality extends Model {
	public function updateType($data) {
		$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "locality_type` SET
			`name` = '" . $this->db->escape($data['name']) . "',
			`description` = '" . $this->db->escape($data['description'])  . "'
			ON DUPLICATE KEY UPDATE
			`description` = '" . $this->db->escape($data['description'])  . "'
		");

		return $this->db->getLastId();
	}

	public function updateLocality($data) {
		$query = $this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "locality` SET
			`name` = '" . $this->db->escape($data['name']) . "',
			`locality_type_id` = '" . $this->db->escape($data['locality_type_id'])  . "'
		");

		return $this->db->getLastId();
	}

	public function updateGeoIPCity($data) {
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "geoip_cities` SET
			`city_id` = '" . $this->db->escape($data['city_id']) . "',
			`name` = '" . $this->db->escape($data['name']) . "',
			`area` = '" . $this->db->escape($data['area']) . "',
			`region` = '" . $this->db->escape($data['region']) . "',
			`Lat` = '" . $this->db->escape($data['Lat']) . "',
			`Lng` = '" . $this->db->escape($data['Lng']) . "'
		");
	}

	public function resetGeoIPCidr() {
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "geoip_cidrs` SET `status` = 0");
	}

	public function clearGeoIPCidr() {
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "geoip_cidrs` WHERE `status` = 0");
	}

	public function countGeoIPCidr() {
		$query = $this->db->query("SELECT COUNT(*) AS C FROM `" . DB_PREFIX . "geoip_cidrs` WHERE `status` = 1");
		if ($query->num_rows == 1)
			return $query->row['C'];
		else
			return 0;
	}

	public function updateGeoIPCidr($data) {
		$query = $this->db->query("INSERT INTO `" . DB_PREFIX . "geoip_cidrs` SET
			`from` = '" . $this->db->escape($data['from']) . "',
			`end` = '" . $this->db->escape($data['end']) . "',
			`cc` = '" . $this->db->escape($data['cc']) . "',
			`city_id` = '" . $this->db->escape($data['city_id']) . "',
			`status` = 1
			ON DUPLICATE KEY UPDATE
			`cc` = '" . $this->db->escape($data['cc']) . "',
			`city_id` = '" . $this->db->escape($data['city_id']) . "',
			`status` = 1
		");
	}

	public function findCity($ip) {
		$iip = sprintf("%u", ip2long($ip));
		$query = $this->db->query("
		SELECT c.`name` from `" . DB_PREFIX . "geoip_cidrs` AS cd
		LEFT JOIN `" . DB_PREFIX . "geoip_cities` AS c ON cd.`city_id` = c.`city_id`
		WHERE '" . $this->db->escape($iip) . "' >= cd.`from` AND
		'" . $this->db->escape($iip) . "' <= cd.`end` LIMIT 2
		");
		if ($query->num_rows == 1)
			return $query->row['name'];
		else
			return '';
	}

	public function findType($name) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "locality_type` WHERE `name` = '" . $this->db->escape($name) . "'");

		return $query->row;
	}

	public function searchLocality($data) {
		$ret = array();
		if ( ! isset($data['limit']) )
			$data['limit'] = 10;
		$query = $this->db->query("SELECT l.`name`, lt.`name` AS locality_type FROM `" . DB_PREFIX . "locality` AS l
			LEFT JOIN `" . DB_PREFIX . "locality_type` AS lt ON l.`locality_type_id` = lt.`locality_type_id`
			WHERE l.`status` = 1 AND l.`name` LIKE '" . $this->db->escape($data['name']) . "' ORDER BY l.`priority` DESC LIMIT " . (int)$data['limit']);
		foreach ($query->rows as $row) {
			$ret[] = $row['locality_type'] . ' ' . $row['name'];
		}
		return $ret;
	}

	public function getLikesLocality($limit=30) {
		$ret = array();
		$query = $this->db->query("SELECT l.`name`, lt.`name` AS locality_type, l.priority FROM `" . DB_PREFIX . "locality` AS l
			LEFT JOIN `" . DB_PREFIX . "locality_type` AS lt ON l.`locality_type_id` = lt.`locality_type_id`
			WHERE l.`status` = 1 AND l.`priority` > 8 ORDER BY l.`priority` DESC, l.`name` LIMIT " . (int)$limit);
		foreach ($query->rows as $row) {
			$ret[] = array(
				'Locality'	=> $row['locality_type'] . ' ' . $row['name'],
				'Priority'	=> $row['priority'],
			);
		}
		return $ret;
	}

	public function getNameTypes() {
		$ret = array();
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "locality_type`");
		foreach ($query->rows as $row) {
			$ret[$row['locality_type_id']] = $row['name'];
		}

		return $ret;
	}
}
