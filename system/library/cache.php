<?php
class Cache {
	private $adaptor;

	public function __construct($adaptor, $expire = 3600) {
		$class = 'Cache\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($expire);
		} else {
			throw new \Exception('Error: Could not load cache adaptor ' . $adaptor . ' cache!');
		}
	}
	
	public function get($key) {
		return $this->adaptor->get($key);
	}

	public function set($key, $value) {
		return $this->adaptor->set($key, $value);
	}

	public function delete($key) {
		return $this->adaptor->delete($key);
	}
  
  public function check(string $key, callable $making) {
    if (defined('OUT_CACHE') and OUT_CACHE) {
      $ret = $this->get($key);
      if ($ret === false) {
        $ret = $making();
        $this->set($key, $ret);
      }
    } else {
      $ret = $making();
    }
    return $ret;
  }
}
