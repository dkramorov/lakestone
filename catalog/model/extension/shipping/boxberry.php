<?php
class ModelExtensionShippingBoxberry extends Model {

    private $CURL;

    public function request($data) {
        $ch = $this->getCURL();
        $param = array_merge(array('token' => BOXBERRY_KEY), $data['fields']);
        //http://api.boxberry.de/json.php?token=ВашТокен&method=ListCities
/*        if ( $data['method'] == 'POST' ) {
            foreach ($param as $k => $v) {
                if ( is_array($v) )
                    $param[$k] = json_encode($v);
            }
        }*/
        $query = http_build_query($param);
        curl_setopt($ch, CURLOPT_URL, BOXBERRY_BASE);
        if ( $data['method'] == 'POST' ) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        } else {
            curl_setopt($ch, CURLOPT_URL, BOXBERRY_BASE . '?' . $query);
        }

        $res = json_decode(curl_exec($ch));

        if ( $res != NULL ) {
            return $res;
        } else {
            $err = new stdClass();
            $err->success = false;
            $err->err = 'Ошибка коммуникации с системой "Boxberry"';
            $ret = new stdClass();
            $ret->errors = array($err);
            return $ret;
        }

    }

    public function calcDelivery($city_code) {
        $cache_name = 'boxberry_delivery_' . $city_code;
        $ret = $this->cache->get($cache_name);
        if ( ! empty($ret) )
            return $ret;
        $delivery_period = false;
        $pp_id = false;
        foreach ($this->ListPoints(array('city_code' => $city_code)) as $pp) {
          if (isset($pp['DeliveryPeriod']) and $pp['DeliveryPeriod'] > $delivery_period) {
            $delivery_period = $pp['DeliveryPeriod'];
          }
          if (!$pp_id and isset($pp['Code'])) $pp_id = $pp['Code'];
        }
        if ($delivery_period !== false) {
          $this->cache->set($cache_name, $delivery_period);
          return $delivery_period;
        }
        if ($pp_id === false) {
          return false;
        }
        $req = array(
            'fields'	=> array(
                'method'	=> 'DeliveryCosts',
                'weight'  => '1200',
                'target'  => $pp_id,
            ),
            'method'	=> 'GET',
        );
        $ret = $this->request($req);
        if ( isset($ret->delivery_period) ) {
            $this->cache->set($cache_name, $ret->delivery_period);
            return $ret->delivery_period;
        } else {
            $this->log->write($ret->errors[0]->err);
            return false;
        }
    }

    public function ListCities() {
        $Cache = new Cache('file', 3600*24);
        $ret = $Cache->get('boxberry_ListCities');
        if ( ! empty($ret) )
            return $ret;
        $req = array(
            'fields'	=> array(
                'method'	=> 'ListCities',
            ),
            'method'	=> 'GET',
        );
        $ret = $this->request($req);
        if ( is_array($ret) ) {
            $Cache->set('boxberry_ListCities', $ret);
            foreach ($ret as $i => $o) { $ret[$i] = get_object_vars($o);}
            return $ret;
        } else {
            $this->log->write($ret->errors[0]->err);
            return array();
        }
    }

    public function ListPoints($data) {
        $cache_name = 'boxberry_ListPoints';
        $req = array(
            'fields'	=> array(
                'method'	=> 'ListPoints',
            ),
            'method'	=> 'GET',
        );
        if ( isset($data['city_code']) ) {
            $cache_name .= (int) $data['city_code'];
            $req['fields']['CityCode'] = $data['city_code'];
        }
        $ret = $this->cache->get($cache_name);
        if ( ! empty($ret) )
            return $ret;
        $ret = $this->request($req);
        if ( is_array($ret) ) {
            $this->cache->set($cache_name, $ret);
            foreach ($ret as $i => $o) { $ret[$i] = get_object_vars($o);}
            return $ret;
        } else {
            $this->log->write($ret->errors[0]->err);
            return array();
        }
    }

    protected function getCURL() {
        if ( $this->CURL === NULL ) {
            $this->CURL = curl_init();
            curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($this->CURL, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($this->CURL, CURLOPT_CONNECTTIMEOUT_MS, 700);
        }
        return $this->CURL;
    }

}
