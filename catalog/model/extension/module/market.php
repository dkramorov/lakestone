<?
class ModelExtensionModuleMarket extends Model {

    private $CURL;
    private $limit = 30;
    private $req_limit = 3;
    private $max_trying = 10;

    protected function request($data) {
        $ch = $this->getCURL();
        if ( $data['method'] == 'POST' ) {
            curl_setopt($ch, CURLOPT_URL, YANDEX_MARKET_CONTENT_BASE . $data['endpoint']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data['fields']));
        } elseif ( isset($data['fields']) and ! empty($data['fields']) ) {
            curl_setopt($ch, CURLOPT_URL, YANDEX_MARKET_CONTENT_BASE . $data['endpoint'] . '?' . http_build_query($data['fields']));
        }
        $ret_raw = curl_exec($ch);
        if ($ret_raw === FALSE) {
          return $this->getError(curl_error($ch));
        }
        $res = json_decode($ret_raw);
        if ( $res != NULL ) {
            return $res;
        } else {
            return $this->getError($ret_raw);
        }
    }

    protected function getError($text = '') {
        $error = new stdClass();
        $error->error = 'Ошибка коммуникации с системой "Яндекс Маркет API"';
        $ret = new stdClass();
        $ret->errors = array($error);
        $this->log->write('The error of communication with YandexMarketAPI:' . $text);
        return $ret;
    }

    protected function getRes($req, $lim = 0) {
        if ($lim == 0) $lim = $this->req_limit;
        while ($lim-- > 0) {
            $res = $this->request($req);
            if ($res instanceof stdClass and isset($res->status) and $res->status == 'OK' and !property_exists($res, 'errors'))
                return $res;
        }
        return $this->getError(var_export($res, true));
    }

    protected function getObjects($req, $issue_name) {
        $trying = $this->max_trying;
        if ( ! isset($req['fields']) )
            $req['fields'] = array();
        $req['fields']['page'] = 1;
        $req['fields']['count'] = $this->limit;
        $res = $this->getRes($req);
        if (property_exists($res, 'errors'))
            return array();
        $objects = $res->{$issue_name};
        if (property_exists($res->context, 'page')) {
            $trying += $res->context->page->total;
            while (
                !( property_exists($res->context->page, 'last') and $res->context->page->last )
                or $trying-- > 0
            ) {
                if (sizeof($res->{$issue_name}) == 0 )
                    break;
                $req['fields']['page']++;
                $res = $this->getRes($req);
                if (property_exists($res, 'errors'))
                    return $objects;
                $objects = array_merge($objects, $res->{$issue_name});
            }
        }
        return $objects;
    }

    public function getShopsOpinions($shop_id) {
        $Cache = new Cache('file', 3600*24);
        $ret = $Cache->get('shop_opinions_' . $shop_id);
        if ($ret)
            return $ret;
        $req = array(
            'method'		=> 'GET',
            'endpoint'	=> '/shops/' . (int)$shop_id . '/opinions',
            'fields'		=> array(
                'max_comments' 	=> 0,
                'how'						=> 'DESC',
            ),
        );

        $ret = $this->getObjects($req, 'opinions');
        $Cache->set('shop_opinions_' . $shop_id, $ret);
        return json_decode(json_encode($ret), true);
    }

    protected function getCURL() {
        if ( $this->CURL === NULL ) {
            $this->CURL = curl_init();
            curl_setopt($this->CURL, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: ' . YANDEX_MARKET_CONTENT_KEY,
            ));
            curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
        }
        return $this->CURL;
    }
}
?>
