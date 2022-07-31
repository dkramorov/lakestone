<?php

class ModelExtensionKupivipApi extends Model {
  
  private $CURL;
  private $CURL_HTTP_HEADER;
  
  public function request($data) {
    $ch = $this->getCURL();
    $url = KUPIVIP_ENDPOINT;
    $url .= $data['endpoint'];
    $data['fields']['vendorCode'] = KUPIVIP_VENDOR_CODE;
    if (empty($data['ContentType'])) {
      $this->CURL_HTTP_HEADER[] = 'Content-Type: application/json;charset=UTF-8';
    } else {
      $this->CURL_HTTP_HEADER[] = 'Content-Type: ' . $data['ContentType'];
    }
    if (!empty($data['GET'])) {
      $query = http_build_query($data['fields']);
      if (!empty($query))
        $url .= '?' . $query;
      curl_setopt($ch, CURLOPT_HTTPGET, true);
    } else {
      curl_setopt($ch, CURLOPT_POST, true);
      if (empty($data['ContentType'])) {
        if (empty($data['fields']))
          $payload = json_encode($data['fields'], JSON_FORCE_OBJECT);
        else
          $payload = json_encode($data['fields']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      } else {
        $payload = $data['fields'];
      }
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => $this->CURL_HTTP_HEADER,
    ]);
    $rerequest_counter = 5;
    if (!empty($data['debug'])) {
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      echo "CURL will get url: $url\n";
      echo "Header:\n";
      var_dump($this->CURL_HTTP_HEADER);
      echo "Payload:\n";
      var_dump($payload);
    }
    do {
      $res_str = curl_exec($ch);
      $header = substr($res_str, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      $ah = explode("\n", $header);
      if (stripos($ah[0], '429') == true) {
        usleep(200000);
      }
    } while (stripos($ah[0], '429') == true and $rerequest_counter-- > 0);
    if (!empty($data['debug'])) {
      echo "CURL has returned: $res_str\nCURL getinfo: " . var_export(curl_getinfo($ch), 1);
    }
    $body = substr($res_str, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    if (
        stripos($ah[0], '100 Continue') == FALSE and
        stripos($ah[0], '200') == FALSE
    ) {
//      $this->log->write('Curl payload:' . $payload);
      throw new Exception("CURL has returned: $res_str\nCURL getinfo: " . json_encode(curl_getinfo($ch)));
    }
    try {
      $res = json_decode($body);
      return $res;
    } catch (\Exception $e) {
      throw new Exception("CURL has returned NO json-string: " . $body);
    }
  }
  
  public function getProducts() {
    $ret = $this->cache->get('kupivip_products');
    if (!$ret) {
      try {
        $total = 0;
        $counter = 1;
        $ret = [];
//        do {
          $req = [
              'GET' => true,
              'fields' => [
//                  'filter' => [
//                      'filter.visibility' => 'ALL',
//                  ],
//                  'page_size' => 100,
//                  'page' => $counter++,
              ],
              'endpoint' => 'products',
          ];
          $res = $this->request($req);
          if (!isset($res->products)) {
            $this->log->write('Unable to get products from Kupivip: ' . json_encode($res));
            return [];
          }
//          if ($total == 0) $total = $res->result->total;
//          $ret = array_merge($ret, $res->result->items);
//        } while (sizeof($ret) < $total);
        $ret = $res->products; // w/o paging
        $this->cache->set('kupivip_products', $ret);
        $ret = json_decode(json_encode($ret), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    return $ret;
  }
  
  public function getProduct(string $article, string $color) {
    $cache_name = 'kupivip_product_' . $article . '_' . $color;
    $ret = $this->cache->get($cache_name);
    if (!$ret) {
      try {
        $req = [
            'GET' => true,
            'fields' => [
                'vendorArticle' => $article,
                'vendorColor' => $color,
            ],
            'endpoint' => 'product',
        ];
        $res = $this->request($req);
        if (!isset($res->data)) {
          $this->log->write('Unable to get product from Kupivip: ' . json_encode($res));
          return [];
        }
        $ret = $res->data;
        $this->cache->set($cache_name, $ret);
        $ret = json_decode(json_encode($ret), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    return $ret;
  }
  
  public function hideProduct(string $article, string $color) {
      try {
        $fields = [
            'vendorCode' => KUPIVIP_VENDOR_CODE,
            'vendorArticle' => $article,
            'vendorColor' => $color,
        ];
        $req = [
            'ContentType' => 'application/x-www-form-urlencoded',
            'fields' => $fields,
            'endpoint' => 'partner/product/hide?' . http_build_query($fields),
        ];
        $res = $this->request($req);
        return $res;
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
      }
  }
  
  public function updateProductReserve(string $article, string $color, string $code, int $quantity) {
    try {
      $fields = [
          'vendorCode' => KUPIVIP_VENDOR_CODE,
          'vendorArticle' => $article,
          'vendorColor' => $color,
          'variantCode' => $code,
          'count' => $quantity,
      ];
      $req = [
          'ContentType' => 'application/x-www-form-urlencoded',
          'fields' => $fields,
          'endpoint' => 'reserves?' . http_build_query($fields),
      ];
      $res = $this->request($req);
      return $res;
    } catch (Exception $e) {
      $this->log->write('Exception: ' . $e->getMessage());
    }
  }
  
  public function updateProduct($data) {
    try {
      $req = [
//          'debug' => true,
          'fields' => $data,
          'endpoint' => 'product/edit?vendorCode=' . KUPIVIP_VENDOR_CODE,
      ];
      $res = $this->request($req);
      return $res;
    } catch (Exception $e) {
      $this->log->write('Exception: ' . $e->getMessage());
    }
  }
  
  protected function getCURL() {
    if ($this->CURL === NULL) {
      $this->CURL = curl_init();
      $this->CURL_HTTP_HEADER = [
          'Authorization: ' . KUPIVIP_APIKEY,
      ];
      curl_setopt_array($this->CURL, [
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_CONNECTTIMEOUT => 1,
          CURLOPT_CONNECTTIMEOUT_MS => 700,
      ]);
    }
    return $this->CURL;
  }
  
}
