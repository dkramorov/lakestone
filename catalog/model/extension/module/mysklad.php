<?php

class ModelExtensionModuleMysklad extends Model {
  
  private $CURL;
  private $limit = 1000;
  private $req_limit = 5;
  
  /*    private $DefaultFields = array(
          'orderCreate' => array(
              'paymentType'	=> array('cash', 'payment_method'), // cash/bank-card
              'orderMethod'	=> array('one-click', 'order_method'), // one-click/shopping-cart
              'customerComment'	=> array('', 'comment'),
              'phone'		=> array('красный', 'telephone'),
              'firstName'		=> array('Вова', 'firstname'),
              'email'		=> array('admin@lakestone.ru', 'email'),
              'call'		=> array(false, 'callback'),
          ),
      );*/
  
  protected function request($data) {
    $ch = $this->getCURL();
    if ($data['method'] == 'POST') {
      curl_setopt($ch, CURLOPT_URL, MYSKLAD_BASE . $data['endpoint']);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data['fields']));
    } elseif (isset($data['fields']) and !empty($data['fields'])) {
      curl_setopt($ch, CURLOPT_URL, MYSKLAD_BASE . $data['endpoint'] . '?' . http_build_query($data['fields']));
    }
    if ($data['debug'] ?? false) {
      d(curl_getinfo($ch));
    }
    $ret_raw = curl_exec($ch);
    if ($data['debug'] ?? false) {
      d($ret_raw);
    }
    $res = json_decode($ret_raw);
    if ($res != NULL) {
      return $res;
    } else {
      return $this->getError($ret_raw);
    }
  }
  
  protected function getError($text = '') {
    $error = new stdClass();
    $error->error = 'Ошибка коммуникации с системой "Мой Склад"';
    $ret = new stdClass();
    $ret->errors = array($error);
    $this->log->write('The error of communication with MySklad:' . $text);
    d('The error of communication with MySklad:' . $text);
    return $ret;
  }
  
  protected function getRes($req, $lim = 0) {
    if ($lim == 0) $lim = $this->req_limit;
    while ($lim-- > 0) {
      $res = $this->request($req);
      if (
          $res instanceof stdClass
          and isset($res->rows)
          and isset($res->meta->size)
      )
        return $res;
      if (
          $res instanceof stdClass
          and isset($res->errors)
      )
        return $this->getError(var_export($res, true));
    }
    return $this->getError(var_export($res, true));
  }
  
  protected function getObjects($req, int $limit = null) {
    if (!isset($req['fields']))
      $req['fields'] = array();
    $req['fields']['offset'] = 0;
    $req['fields']['limit'] = $limit ?? $this->limit;
    $res = $this->getRes($req);
    if (isset($res->errors))
      return array();
    $objects = $res->rows;
    while (sizeof($objects) < $res->meta->size) {
      if (sizeof($res->rows) == 0)
        break;
      $req['fields']['offset'] += $this->limit;
      $res = $this->getRes($req);
      if (isset($res->errors))
        return $objects;
      $objects = array_merge($objects, $res->rows);
    }
    return $objects;
  }
  
  public function getProducts() {
    $req = array(
        'method' => 'GET',
        'endpoint' => 'entity/product',
    );
    
    return $this->getObjects($req);
  }
  
  public function getVariants() {
    $req = array(
        'method' => 'GET',
        'endpoint' => 'entity/variant',
    );
    
    return $this->getObjects($req);
  }
  
  public function getStock(string $filter = '') {
    $req = array(
        'method' => 'GET',
        'endpoint' => 'report/stock/all',
        'fields' => array(
            'filter' => $filter,
        ),
    );
    
    return $this->getObjects($req);
  }
  
  public function getStores(string $search = '') {
    $req = array(
        'method' => 'GET',
        'endpoint' => 'entity/store',
//        'debug' => true,
        'fields' => [
            'search' => $search,
        ],
    );
    
    return $this->getObjects($req);
  }
  
  public function getStockByStore() {
    $req = array(
        'method' => 'GET',
        'endpoint' => 'report/stock/bystore',
        'fields' => [],
    );
    
    return $this->getObjects($req);
  }
  
  public function createOrder($data) {
    $req = array(
        'method' => 'POST',
        'endpoint' => 'entity/customerorder',
    );
    $this->load->model('extension/module/integration');
    $positions = array();
    foreach ($data['products'] as $product) {
      $prod_info = $this->model_extension_module_integration->getProductIDs($product['product_id']);
      $positions[] = array(
          'quantity' => (float)sprintf('%0.2f', $product['quantity']),
          'reserve' => (float)sprintf('%0.2f', $product['quantity']),
          'price' => (float)sprintf('%0.2f', $product['price'] * 100),
          'discount' => 0,
          'vat' => 0,
          'assortment' => array(
              'meta' => array(
                  'href' => MYSKLAD_BASE . 'entity/product/' . $prod_info['internalId'],
                  'type' => 'product',
                  'mediaType' => 'application/json',
              ),
          ),
      );
    }
    $req['fields'] = array(
        'name' => (string)$data['order_id'],
        'code' => (string)$data['order_id'],
        'vatEnabled' => false,
        'positions' => $positions,
        'organization' => array(
            'meta' => array(
                'href' => MYSKLAD_BASE . 'entity/organization/' . MYSKLAD_ORG,
                'type' => 'organization',
                'mediaType' => 'application/json',
            ),
        ),
        'agent' => array(
            'meta' => array(
                'href' => MYSKLAD_BASE . 'entity/counterparty/' . MYSKLAD_AGENT,
                'type' => 'counterparty',
                'mediaType' => 'application/json',
            ),
        ),
    );
    $res = $this->request($req);
    if (!isset($res->errors)) {
      $this->model_extension_module_integration->updateOrderIDs($data['order_id'], $res->id, $res->externalCode);
    } else {
      $text = '<p>При передаче заказа ' . $data['order_id'] . ' возникли ошибки:</p><ol>';
      foreach ($res->errors as $error) {
        $text .= '<li>' . $error->error . '</li>';
      }
      $text .= '</ol><p>Пожалуйста, обработайте заказ ' . $data['order_id'] . ' самостоятельно.</p>';
      $this->model_extension_module_integration->sendReport(array(
          'subject' => 'Ошибка при передеаче заказа ' . $data['order_id'] . ' в систему "МойСклад"',
          'text' => $text,
      ));
    }
    return $res;
  }
  
  public function parseObject(object $obj): array {
    
    $ret = [
        'name' => $obj->name,
        'externalCode' => $obj->externalCode,
    ];
    
    if (!empty($obj->buyPrice->value)) {
      $ret['price2'] = $obj->buyPrice->value / 100 ?? 0;
    }
    foreach ($obj->attributes ?? [] as $item) {
      if ($item->id == '375ad9d3-782a-11eb-0a80-0666000aceb5') {
        $ret['storage_cell'] = $item->value;
      }
    }
    if (!empty($obj->salePrices) and is_array($obj->salePrices)) {
      foreach ($obj->salePrices as $salePrice) {
        if (
            (isset($salePrice->priceType) and $salePrice->priceType == 'РРЦ') or
            (isset($salePrice->priceType->id) and $salePrice->priceType->id == 'c0b5453b-8f7d-11e6-7a69-8f550042d39e')
        ) {
          $ret['price'] = $salePrice->value / 100;
        } elseif (
            (isset($salePrice->priceType) and $salePrice->priceType == 'Оптовая цена') or
            (isset($salePrice->priceType->id) and $salePrice->priceType->id == '419882c6-8f84-11e6-7a31-d0fd005b16ea')
        ) {
          $ret['price1'] = $salePrice->value / 100;
        }
      }
    }
    if (isset($obj->stock)) {
      $ret['quantity'] = $obj->stock ?? 0;
    }
    if (!empty($obj->code)) {
      $ret['code'] = $obj->code;
    }
    if ($obj->meta->type == 'variant' and !empty($obj->product->meta->href)) {
      $path = explode('/', $obj->product->meta->href);
      $ret['parentId'] = end($path);
    }
    
    return $ret;
    
  }
  
  protected function getCURL() {
    if ($this->CURL === NULL) {
      $this->CURL = curl_init();
      curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($this->CURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($this->CURL, CURLOPT_USERPWD, MYSKLAD_LOGIN . ':' . MYSKLAD_PASSWORD);
    }
    return $this->CURL;
  }
  
}
