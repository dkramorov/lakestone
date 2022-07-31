<?php
final class Locking {
	private $registry;
	private $installed_modules = array();
	public $installed_markets = array();

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function lock($tag, $time=300) {
		$query = $this->db->query("SELECT `not_before` < unix_timestamp() AS res FROM `" . DB_PREFIX . "locking` WHERE `lock_tag` = '" . $this->db->escape($tag) . "'");
		if ( $query->num_rows == 0 or $query->row['res'] ) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "locking` SET `lock_tag` = '" . $this->db->escape($tag) . "', `not_before` = unix_timestamp() + '" . (int) $time . "' ON DUPLICATE KEY UPDATE `not_before` = unix_timestamp() + '" . (int) $time . "'");
			return true;
		} else {
			return false;
		}
	}

	public function unlock($tag) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "locking` WHERE `lock_tag` = '" . $this->db->escape($tag) . "'");
	}

	public function __get($name) {
		return $this->registry->get($name);
	}

}
