<?php
/*
CREATE TABLE IF NOT EXISTS `session` (
  `session_id` varchar(32) NOT NULL,
  `data` text NOT NULL,
  `expire` datetime NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
*/
namespace Session;
final class DB extends \SessionHandler {
	public $data = array();
	public int $expire;
	
	public function __construct($registry) {
		$this->db = $registry->get('db');
		
//		register_shutdown_function(array($this, 'close'));
		
		$this->expire = ini_get('session.gc_maxlifetime');
		if (defined('DEV_MODE') and DEV_MODE) {
      $this->debug = fopen(DIR_LOGS . 'debug.log', 'a');
      if ($this->debug === false) throw new \Exception('Unable to open debug.log');
    }
	}
  
  private function debug($data) {
    if (defined('DEV_MODE') and DEV_MODE) {
      fwrite($this->debug, date('H:m') . ' ' . (string)$data . "\n");
    }
	}
  
  public function create_sid() {
    return parent::create_sid();
  }
  
  public function open($path, $name) {
    return parent::open($path, $name);
  }

	public function close() {
//    $this->debug('close');
		return true;
	}
	
	public function read($session_id): string {
//    $this->debug('read');
		$query = $this->db->query("SELECT `data` FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "' AND expire > " . (int)time());
		
		if ($query->num_rows) {
//		  $this->debug('data: ' . $query->row['data']);
			return json_decode($query->row['data']) ?? '';
		} else {
			return '';
		}
	}
	
	public function write($session_id, $data): bool {
//    $this->debug('write: ' . (string) $data);
    if ($session_id and !empty($data)) {
      $this->db->query("REPLACE INTO `" . DB_PREFIX . "session` SET `session_id` = '" . $this->db->escape($session_id) . "', `data` = '" . $this->db->escape(json_encode($data)) . "', `expire` = '" . $this->db->escape(date('Y-m-d H:i:s', time() + (int)$this->expire)) . "'");
    }
		return true;
	}
	
	public function destroy($session_id) {
//    $this->debug('destroy');
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE session_id = '" . $this->db->escape($session_id) . "'");
		
		return true;
	}
	
	public function gc($expire) {
//    $this->debug('gc: ' . $expire);
//    $this->debug($expire + time());
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE expire > from_unixtime(" . ((int)time() + $expire) . ')');
//    $this->debug(var_export($this->db, 1));
		return true;
	}
}
