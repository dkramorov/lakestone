<?php

use GuzzleHttp\Client;

class ModelExtensionOzonApi extends Model {

  private $CURL;
  private Client $client;

  public function request($data) {
    $ch = $this->getCURL();
    $url = OZON_ENDPOINT;
    $url .= $data['endpoint'];
    if (!empty($data['GET'])) {
      $query = http_build_query($data['fields']);
      if (!empty($query))
        $url .= '?' . $query;
      curl_setopt($ch, CURLOPT_HTTPGET, true);
    } else {
      curl_setopt($ch, CURLOPT_POST, true);
      if (empty($data['fields']))
        $payload = json_encode($data['fields'], JSON_FORCE_OBJECT);
      else
        $payload = json_encode($data['fields']);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $rerequest_counter = 5;
    if (!empty($data['debug'])) {
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      echo "CURL will get url: $url\n";
    }
    do {
      $res_str = curl_exec($ch);
      $header = substr($res_str, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      $ah = explode("\n", $header);
      if (stripos($ah[0], '429') == true) {
        usleep(200000);
      }
    } while (
        (
            stripos($ah[0], '429') == true // Too Many Requests
            or stripos($ah[0], '408') == true // timeout
        )
        and $rerequest_counter-- > 0
    );
    if (!empty($data['debug'])) {
      echo "Payload: ". var_export($payload, 1) . "\nCURL has returned: $res_str\nCURL getinfo: " . json_encode(curl_getinfo($ch));
    }
    $body = substr($res_str, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    if (
            stripos($ah[0], '100 Continue') == FALSE and
            stripos($ah[0], '200') == FALSE
    ) {
//      $this->log->write('Curl payload:' . $payload);
      throw new Exception("Payload: ". var_export($payload, 1) . "\nCURL has returned: $res_str\nCURL getinfo: " . json_encode(curl_getinfo($ch)));
    }
    try {
      $res = json_decode($body);
      return $res;
    } catch (\Exception $e) {
      throw new Exception("CURL has returned NO json-string: " . $body);
    }
  }
  
  /**
   * @param array $stocks
   * @return array faults of stocks updating
   */
  public function updateStocks(array $stocks): array {
    $limit = 100;
    $offset = 0;
    $faults = [];
    while ($part = array_slice($stocks, $offset, $limit)) {
      try {
        $req = [
            'fields' => [
                'stocks' => $part,
            ],
//          'endpoint' => 'v2/products/stocks',
            'endpoint' => 'v1/product/import/stocks',
        ];
        $res = $this->request($req);
        if (empty($res->result)) throw new Exception('Ozon returned bad result: ' . json_encode($res));
        foreach ($res->result as $item) {
          if (!$item->updated) $faults[] = $item;
        }
        $offset += $limit;
      } catch (Exception $e) {
        echo $e->getMessage();
        return [];
      }
      return $faults;
    }
  }

  public function activateProduct(array $product_id) {
    try {
      $req = [
          'fields' => [
              'product_id' => $product_id,
          ],
          'endpoint' => 'v1/product/unarchive',
      ];
      return $this->request($req);
    } catch (Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  public function deactivateProduct(array $product_id) {
    try {
      $req = [
          'fields' => [
              'product_id' => $product_id,
          ],
          'endpoint' => 'v1/product/archive',
      ];
      return $this->request($req);
    } catch (Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  public function importProducts($products) {
    try {
      $req = [
          'fields' => [
              'items' => $products,
          ],
          'endpoint' => 'v1/product/import',
      ];
//      echo json_encode($req['fields']);exit;
      return $this->request($req);
    } catch (Exception $e) {
      $this->log->write('Exception: ' . $e->getMessage());
      return false;
    }
  }

  public function updatePrices($prices) {
    $limit = 100;
    $offset = 0;
    $faults = [];
    while ($part = array_slice($prices, $offset, $limit)) {
      try {
        $req = [
            'fields' => [
                'prices' => $part,
            ],
            'endpoint' => 'v1/product/import/prices',
        ];
        $res = $this->request($req);
        if (empty($res->result)) throw new Exception('Ozon returned bad result: ' . json_encode($res));
        foreach ($res->result as $item) {
          if (!$item->updated) $faults[] = $item;
        }
        $offset += $limit;
      } catch (Exception $e) {
        echo $e->getMessage();
        return [];
      }
    }
    return $faults;
  }
  
  public function getProducts(array $filter = [], bool $use_cache = true) {
    if (empty($filter)) {
      $filter = [
          'filter.visibility' => 'ALL',
      ];
    }
    $cache_name = 'ozon_products_' . md5(json_encode($filter));
    if ($use_cache) {
      $ret = $this->cache->get($cache_name);
    } else {
      $ret = false;
    }
    
    if (!$ret) {
      try {
        $total = 0;
        $counter = 1;
        $ret = [];
        do {
          $req = [
              'fields' => [
                  'filter' => $filter,
                  'page_size' => 1000,
                  'page' => $counter++,
              ],
              'endpoint' => 'v1/product/list',
          ];
          $res = $this->request($req);
          if (!isset($res->result->items)) {
            $this->log->write('Unable to get prices from Ozon: ' . json_encode($res));
            return [];
          }
          if ($total == 0) $total = $res->result->total;
          $ret = array_merge($ret, $res->result->items);
        } while (sizeof($ret) < $total);
        $this->cache->set($cache_name, $ret);
        $ret = json_decode(json_encode($ret), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    return $ret;
  }

  public function getPrices(array $filter = [], bool $use_cache = true) {
    if (empty($filter)) {
      $filter = [
          'filter.visibility' => 'ALL',
      ];
    }
    $cache_name = 'ozon_prices_' . md5(json_encode($filter));
    if ($use_cache) {
      $ret = $this->cache->get($cache_name);
    } else {
      $ret = false;
    }
    if (!$ret) {
      try {
        $total = 0;
        $counter = 1;
        $ret = [];
        do {
          $req = [
              'fields' => [
                  'filter' => $filter,
                  'page_size' => 1000,
                  'page' => $counter++,
              ],
              'endpoint' => 'v1/product/info/prices',
          ];
          $res = $this->request($req);
          if (!isset($res->result->items)) {
            $this->log->write('Unable to get prices from Ozon: ' . json_encode($res));
          }
          if ($total == 0) $total = $res->result->total;
          $ret = array_merge($ret, $res->result->items);
        } while (sizeof($ret) < $total);
        $this->cache->set($cache_name, $ret);
        $ret = json_decode(json_encode($ret), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    return $ret;
  }

  public function getStocks(array $filter = [], bool $use_cache = true) {
    if (empty($filter)) {
      $filter = [
          'filter.visibility' => 'ALL',
      ];
    }
    $cache_name = 'ozon_stocks_' . md5(json_encode($filter));
    if ($use_cache) {
      $ret = $this->cache->get($cache_name);
    } else {
      $ret = false;
    }
    if (!$ret) {
      try {
        $total = 0;
        $counter = 1;
        $ret = [];
        do {
          $req = [
              'fields' => [
                  'filter' => $filter,
                  'page_size' => 1000,
                  'page' => $counter++,
              ],
              'endpoint' => 'v1/product/info/stocks',
          ];
          $res = $this->request($req);
          if (!isset($res->result->items)) {
            $this->log->write('Unable to get products from Ozon: ' . json_encode($res));
          }
          if ($total == 0) $total = $res->result->total;
          $ret = array_merge($ret, $res->result->items);
        } while (sizeof($ret) < $total);
        $this->cache->set($cache_name, $ret);
        $ret = json_decode(json_encode($ret), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    return $ret;
  }

  public function getOrder($posting_number) {
    try {
      $req = [
//          'debug' => true,
          'fields' => [
            'posting_number' => $posting_number
          ],
//          'GET'   => true,
          'endpoint' => 'v2/posting/fbs/get',
      ];
      $ret = $this->request($req);
      if (!empty($ret->result)) {
        return json_decode(json_encode($ret->result), true);
      } else {
        return [];
      }
    } catch (Exception $e) {
      return [];
    }
  }
  
  public function getWarehouses() {
    try {
      $req = [
//          'debug' => true,
//          'GET'   => true,
          'endpoint' => 'v1/warehouse/list',
      ];
      $ret = $this->request($req);
      if (!empty($ret->result)) {
        return json_decode(json_encode($ret->result), true);
      } else {
        return [];
      }
    } catch (Exception $e) {
      return [];
    }
  }

  public function getOrders($date = null) {
    if (!$date) $date = time();
    try {
      $limit = 50;
      $total = 0;
      $counter = 0;
      $ret = [];
      do {
        $req = [
            'fields' => [
              'dir' => 'asc',
              'filter' => [
                'since' => date('c', $date - 3600*24),
                'to' => date('c', $date),
              ],
              'limit' => $limit,
              'offset' => $counter++ * $limit,
            ],
            'endpoint' => 'v2/posting/fbs/list',
        ];
        $res = $this->request($req);
        $ret = array_merge($ret, $res->result);
      } while (sizeof($res->result) > 0 and sizeof($res->result) == $limit);
      $ret = json_decode(json_encode($ret), true);
    } catch (Exception $e) {
      $this->log->write('Exception: ' . $e->getMessage());
      return [];
    }
    return $ret;
  }
  
  public function getOrdersV3(string $date = null, string $period = '-1 day', int $limit = 50, string $status = '', $arrayResult = false): array {

      //if (!$date) $date = time();
    $ret = [];
    try {
      $counter = 0;

      $dateFormat = 'Y-m-d\TH:i:s.u\Z';

        if (!$date) {
            $dateFrom = (new DateTime($period))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format($dateFormat);
            $dateTo = (new DateTime())
                ->setTimezone(new DateTimeZone('UTC'))
                ->format($dateFormat);
        } else {
            $dateFrom = (new DateTime($date))
                ->modify($period)
                ->setTimezone(new DateTimeZone('UTC'))
                ->format($dateFormat);
            $dateTo = (new DateTime($date))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format($dateFormat);
        }

      do {
        $response = $this->getClient()->post('v3/posting/fbs/list', [
//            'debug' => true,
            'json' => [
                'dir' => 'asc',
                'filter' => [
                    'since' => $dateFrom,
                    'to' => $dateTo,
                    'status' => $status,
                ],
                'with' => [
                    'analytics_data' => true,
                ],
                'limit' => $limit,
                'offset' => $counter * $limit,
            ]
        ]);
        $res = json_decode($response->getBody()->getContents())->result;
        if ($arrayResult) {
          $ret += json_decode(json_encode($res->postings), true);
        } else {
          $ret += $res->postings;
        }
        $counter++;
      } while ($res->has_next);
    }
    catch (\Throwable $e) {
      $this->log->write('Exception: ' . $e->getMessage());
    }
    return $ret;

  }
  
  public function getCategories() {
    $ret = $this->cache->get('ozon_categories');
    if (!$ret) {
      try {
        $req = [
            'fields' => [],
            'endpoint' => 'v1/category/tree',
        ];
        $res = $this->request($req);
        $this->cache->set('ozon_categories', $res);
        $ret = json_decode(json_encode($res), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    if (isset($ret['result']))
      return $ret['result'];
    else
      return [];
  }

  public function getAttributes($category) {
    $ret = $this->cache->get('ozon_attributes_' . (int) $category);
    if (!$ret) {
      try {
        $req = [
            'fields' => [
                'category_id' => $category,
//                'attribute_type' => 'required',
//                'language' => 'EN',
            ],
            'endpoint' => 'v1/category/attribute',
        ];
        $res = $this->request($req);
        $this->cache->set('ozon_attributes' . (int) $category, $res);
        $ret = json_decode(json_encode($res), true);
      } catch (Exception $e) {
        $this->log->write('Exception: ' . $e->getMessage());
        return [];
      }
    }
    if (isset($ret['result']))
      return $ret['result'];
    else
      return [];
  }

  protected function getCURL() {
    if ($this->CURL === NULL) {
      $this->CURL = curl_init();
      curl_setopt_array($this->CURL, [
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_CONNECTTIMEOUT => 1,
          CURLOPT_CONNECTTIMEOUT_MS => 700,
          CURLOPT_HTTPHEADER => [
              'Client-Id: ' . OZON_CLIENT_ID,
              'Api-Key: ' . OZON_SECRET_KEY,
              'Content-Type: application/json',
          ]
      ]);
    }
    return $this->CURL;
  }

  protected function getClient(): Client {
    if (empty($this->client)) {
      $this->client = new Client([
          'base_uri' => OZON_ENDPOINT,
          'headers' => [
              'Client-Id' => OZON_CLIENT_ID,
              'Api-Key' => OZON_SECRET_KEY,
              'Content-Type' => 'application/json',
          ],
      ]);
    }
    return $this->client;
  }
  
}
