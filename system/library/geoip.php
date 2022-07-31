<?php
class Geoip {

	private $CURL;
	private $default='Unknown';

	public function __construct($default='') {
	    if ( ! empty($default) )
	    	$this->default = $default;
            $this->CURL = curl_init();
            curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
	}

	public function getCity($ip) {
	    //http://ipgeobase.ru:7020/geo?ip=144.206.192.6
	    curl_setopt($this->CURL, CURLOPT_URL, 'http://ipgeobase.ru:7020/geo?ip=' . $ip);
	    $XRes = @DOMDocument::loadXML(curl_exec($this->CURL));
	    if ( $XRes === FALSE ) {
	    	return $this->default;
	    } else {
	    	$NL = $XRes->getElementsByTagName('city');
		if (
			$NL->length > 0 and
			! empty($NL->item(0)->nodeValue)
		)
	    		return $NL->item(0)->nodeValue;
		else
			return $this->default;
	    }
	}
}
