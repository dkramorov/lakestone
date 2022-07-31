<?php

class ControllerToolSyncIntegration extends Controller {
  
  private $DefaultFields = array(
      'getProduct' => array(
          'name' => array('undefined', 'name'),
          'model' => array('undefined', 'code'),
          'article' => array('undefined', 'article'),
          'quantity' => array(0, 'stock'),
          'price' => array(0, 'salePrice'),
          'price2' => array(0, 'price'),
      ),
  );
  
  private $SyncedStores = [];
  private $SummableStores = [];
  private $MainStore = 'Склад ИМ';
  
  protected function getValue($arr, $key, $obj) {
    $def = $this->DefaultFields[$arr];
    if (isset($obj->{$def[$key][1]}) and !empty($obj->{$def[$key][1]}))
      return $obj->{$def[$key][1]};
    else
      return $def[$key][0];
  }
  
  public function index() {
    
    $logger = new Log('sync_integration.log');
    $logger->write('The intergation is starting');
    
    if (!isset($this->request->get['key']) or $this->request->get['key'] != 'FwpEi6R2Rv0yQ2GeUJVd38r384R7VUv3') {
      echo 'нет доступа';
      $logger->write('access rejected');
      return;
    }
  
    if (!$this->config->get('dev')) {
      $this->registry->set('locking', new Locking($this->registry));
      if (!$this->locking->lock('SyncIntegration', 15 * 60)) {
        $logger->write('access temporary locked');
        echo 'Занято';
        return;
      }
    }
  
    $this->load->model('extension/module/mysklad');
    $this->load->model('extension/module/retailcrm');
    $this->load->model('extension/module/integration');
    $this->load->model('catalog/product');
    $this->load->model('tool/status');
  
    $this->model_tool_status->start('sync');
    
    $this->log = $logger;
  
  
    /**************/
    // get stores
    /**************/

    foreach ($this->model_extension_module_mysklad->getStores() as $store) {
      if ($store->pathName == $this->MainStore) {
        $this->SummableStores[] = [
            'id' => $store->id,
        ];
      }
      foreach ($store->attributes ?? [] as $item) {
        if ($item->id == 'd6eed667-9208-11eb-0a80-030a001cba3e') {
          $this->SyncedStores[] = [
              'id' => $store->id,
              'rcrm_id' => $item->value,
          ];
        }
      }
    }
  
    /**/
//    $mystore_stock = $this->model_extension_module_mysklad->getStock('search=Bartley Black');
//    $mystore_stock = $this->model_extension_module_mysklad->getStock('store=https://online.moysklad.ru/api/remap/1.1/entity/store/c0b509a8-8f7d-11e6-7a69-8f550042d398;search=Bartley Black');
//    dd($mystore_stock);
    /**/
  
    /**************/
    // get product
    /**************/

//    dd($this->model_extension_module_mysklad->getVariants());
//    dd($this->model_extension_module_mysklad->getProducts());
    $mystore_products = $this->model_extension_module_mysklad->getProducts();
    $logger->write('Have received from MoySklad the number of products: ' . sizeof($mystore_products));
    foreach ($mystore_products as $obj) {
      try {
        $this->model_extension_module_integration->updateIntegrationProduct(
            $obj->id,
            $this->model_extension_module_mysklad->parseObject($obj)
        );
      } catch (Exception $e) {
        $logger->write('Update integration error: ' . $e->getMessage());
        $logger->write("Object: \n" . var_export($obj, true));
      }
      $ProductName = $this->getValue('getProduct', 'name', $obj);
      if (preg_match('/^BLACKWOOD /', $ProductName)) {
//	        $logger->write('Пропущен товар: ' . $ProductName);
        continue;
      }
      $product_id = $this->model_extension_module_integration->findProductByModel($this->getValue('getProduct', 'model', $obj));
      $price = $price1 = $price2 = 0;
      $EAN = false;
      if (isset($obj->barcodes)) {
        foreach ($obj->barcodes as $bar) {
          if (isset($bar->ean13) and !empty($bar->ean13)) {
            $EAN = $bar->ean13;
            break;
          }
        }
      }
      if ($EAN)
        $this->model_extension_module_integration->updateProductEAN($product_id, $EAN);
      // if ($obj->id == 'b365675b-e27f-11e8-9107-50480006bf3b') {
      // 	var_dump($obj->salePrices[1]->priceType);
      // 	var_dump($obj);
      // }
      foreach ($obj->salePrices as $salePrice) {
        if (
            (isset($salePrice->priceType) and $salePrice->priceType == 'РРЦ') or
            (isset($salePrice->priceType->id) and $salePrice->priceType->id == 'c0b5453b-8f7d-11e6-7a69-8f550042d39e')
        ) {
          $price = $salePrice->value / 100;
        } elseif (
            (isset($salePrice->priceType) and $salePrice->priceType == 'Оптовая цена') or
            (isset($salePrice->priceType->id) and $salePrice->priceType->id == '419882c6-8f84-11e6-7a31-d0fd005b16ea')
        ) {
          $price1 = $salePrice->value / 100;
        }
      }
      if (isset($obj->buyPrice))
        $price2 = $obj->buyPrice->value / 100;
      if ($product_id !== FALSE) {
        $prod_ext = $this->model_extension_module_integration->getProductIDs($product_id);
        if (
            !preg_match('/^' . $obj->externalCode . '#[^#]+$/', $prod_ext['externalCode']) and
            isset($obj->externalCode)
        ) {
          $this->model_extension_module_integration->updateProductIDs($product_id, $obj->id, $obj->externalCode, $EAN);
        }
        $this->model_extension_module_integration->updateStock($product_id, array(
            'price' => $price,
            'price1' => $price1,
            'price2' => $price2,
        ));
      } else {
        $msg = 'Unknow product: ' . $this->getValue('getProduct', 'name', $obj) . ', (' . $this->getValue('getProduct', 'model', $obj) . ':' . $this->getValue('getProduct', 'article', $obj) . ")";
        $logger->write($msg);
        echo "$msg<br>";
        $product_id_new = $this->model_extension_module_integration->createProduct(array(
            'model' => $this->getValue('getProduct', 'model', $obj),
            'article' => $this->getValue('getProduct', 'article', $obj),
            'price' => $price,
            'name' => $this->getValue('getProduct', 'name', $obj),
        ));
        $this->model_extension_module_integration->updateProductIDs($product_id_new, $obj->id, $obj->externalCode, $EAN);
        $text = '<p>Название: <a href="' . HTTPS_SERVER . 'admin/index.php?route=catalog/product/edit&product_id=' . $product_id_new . '">' . $this->getValue('getProduct', 'name', $obj) . '</a><br>';
        $text .= 'модель: ' . $this->getValue('getProduct', 'model', $obj) . '<br>';
        $text .= 'артикул: ' . $this->getValue('getProduct', 'article', $obj) . '<br>';
        $text .= 'цена: ' . $price . '<br>';
        $text .= '</p>';
        $this->model_extension_module_integration->updateStock($product_id_new, array(
            'price1' => $price1,
            'price2' => $price2,
        ));
      }
    }
    if (!empty($text)) {
      $this->model_extension_module_integration->sendReport(array(
          'subject' => 'Из системы "Мой Склад" пришли новые товары',
          'text' => $text,
      ));
    }
    
    foreach ($this->model_extension_module_mysklad->getVariants() as $obj) {
      try {
        $this->model_extension_module_integration->updateIntegrationVariant(
            $obj->id,
            $this->model_extension_module_mysklad->parseObject($obj)
        );
      } catch (Exception $e) {
        $logger->write('Update variant integration error: ' . $e->getMessage());
        $logger->write("Object: \n" . var_export($obj, true));
      }
    }
    
    /*********************************/
    // get all stock for check empty
    /*********************************/
    $EmptyStores = [];
    foreach ($this->SyncedStores as $store) {
      $EmptyStores[$store['rcrm_id']] = 0;
    }
    
    $mystore_zero_stock = $this->model_extension_module_mysklad->getStock('store=https://online.moysklad.ru/api/remap/1.2/entity/store/302d6fb6-906f-11eb-0a80-06b60029fca1;stockMode=all;quantityMode=all');
//    dd($mystore_zero_stock);
    $logger->write('Have received from MoySklad the number of all stocks: ' . sizeof($mystore_zero_stock));
    foreach ($mystore_zero_stock as $obj) {
      if ($obj->meta->type != 'variant')
        continue;
      try {
        $path = explode('/', parse_url($obj->meta->href, PHP_URL_PATH));
        $variant_id = end($path);
        $VariantInfo = $this->model_extension_module_integration->getIntegrationVariant($variant_id);
        if (empty($VariantInfo)) throw new Exception('Variant not found in local db: ' . $variant_id);
        $fields = [
            'quantity' => $obj->quantity,
            'stores' => serialize($EmptyStores),
        ];
        $this->model_extension_module_integration->updateIntegrationVariant(
            $variant_id,
            $fields
        );
      } catch (Exception $e) {
        $logger->write('Update variant integration error: ' . $e->getMessage());
        $logger->write("Object: \n" . var_export($obj, true));
        continue;
      }
      // skip BlackWoods products
      if (preg_match('/^BLACKWOOD /', $VariantInfo['name'])) {
        continue;
      }
      $product_id = $this->model_extension_module_integration->findProductByModel($VariantInfo['code']);
      if ($product_id !== FALSE) {
        $this->model_extension_module_integration->updateStock($product_id, array(
            'quantity' => $fields['quantity'],
        ));
      } else {
        echo 'Unknown product in Stock: ' . $VariantInfo['name'] . ', (' . $VariantInfo['code'] . ':' . $VariantInfo['id'] . ")\n";
      }
    }
  
    /**************/
    // get stock
    /**************/
    $mystore_stock = $this->model_extension_module_mysklad->getStockByStore();
//    dd($mystore_stock);
    $logger->write('Have received from MoySklad the number of stocks: ' . sizeof($mystore_stock));
    foreach ($mystore_stock as $obj) {
      if ($obj->meta->type != 'variant')
        continue;
      try {
        $path = explode('/', parse_url($obj->meta->href, PHP_URL_PATH));
        $variant_id = end($path);
/*        if ($variant_id == 'f4b8bd59-f1fa-11e6-7a31-d0fd0029314f') {
          d($obj);
        }*/
        $VariantInfo = $this->model_extension_module_integration->getIntegrationVariant($variant_id);
        if (empty($VariantInfo)) throw new Exception('Variant not found in local db: ' . $variant_id);
        $fields = [
            'quantity' => 0,
            'stores' => '',
        ];
        $SyncedStores = [];
        foreach ($obj->stockByStore as $store) {
          $path = explode('/', parse_url($store->meta->href, PHP_URL_PATH));
          $store_id = end($path);
          $quantity = $store->stock - $store->reserve;
          if (
              ! preg_match('/^BLACKWOOD /', $VariantInfo['name'])
              and in_array($store_id, array_column($this->SummableStores, 'id'))
          ) {
            $fields['quantity'] += $quantity;
          } elseif (
            preg_match('/^BLACKWOOD /', $VariantInfo['name'])
            and $store_id == 'c0b509a8-8f7d-11e6-7a69-8f550042d398'
          ) {
            $fields['quantity'] = $quantity;
          }
          $SyncedStoreNum = array_search($store_id, array_column($this->SyncedStores, 'id'));
          if ($SyncedStoreNum !== false) {
            $SyncedStores[$this->SyncedStores[$SyncedStoreNum]['rcrm_id']] = $quantity;
          }
        }
        if (!empty($SyncedStores)) {
          $fields['stores'] = serialize($SyncedStores);
        }
        $this->model_extension_module_integration->updateIntegrationVariant(
            $variant_id,
            $fields
        );
      } catch (Exception $e) {
        $logger->write('Update variant integration error: ' . $e->getMessage());
        $logger->write("Object: \n" . var_export($obj, true));
        continue;
      }
      // skip BlackWoods products
      if (preg_match('/^BLACKWOOD /', $VariantInfo['name'])) {
        continue;
      }
      $product_id = $this->model_extension_module_integration->findProductByModel($VariantInfo['code']);
      $prod_ext = $this->model_extension_module_integration->getProductIDs($product_id);
      if (!preg_match('/^[^#]+#' . $VariantInfo['externalCode'] . '$/', $prod_ext['externalCode']))
        $this->model_extension_module_integration->updateProductIDs($product_id, $prod_ext['internalId'], $prod_ext['externalCode'] . '#' . $VariantInfo['externalCode'], $EAN);
      if ($product_id !== FALSE) {
        $this->model_extension_module_integration->updateStock($product_id, array(
            'quantity' => $fields['quantity'],
//            'price' => $this->getValue('getProduct', 'price', $obj) / 100,
//	    			'price2'	=>$this->getValue('getProduct', 'price2', $obj)/100,
        ));
      } else {
        echo 'Unknown product in Stock: ' . $VariantInfo['name'] . ', (' . $VariantInfo['code'] . ':' . $VariantInfo['id'] . ")\n";
      }
    }
  
    // sync stores
    $results = $this->model_extension_module_retailcrm->updateStock();
    foreach ($results as $site => $mres) {
      if (!$mres->success) {
        $logger->write('The request for update stocks for "' . $site . '" returns an error:');
        $logger->write($mres);
      }
    }

    // sync prices
    $mres = $this->model_extension_module_retailcrm->updatePrices();
    if (!$mres->success) {
      $logger->write('The request for update prices returns an error:');
      $logger->write($mres);
    }
    
    // sync order_status
    $orders = $this->model_extension_module_integration->getOrders(array(
        'filter' => array(
            'order_status_id' => 1,
        ),
    ));
    $logger->write('Have received from RetailCRM the number of completed orders: ' . sizeof($orders));
    
    if (sizeof($orders) > 0) {
      $orders4req = array();
      foreach ($orders as $order) {
        $orders4req[] = $order['crmID'];
      }
      
      $crm_orders = $this->model_extension_module_retailcrm->getOrders(array(
          'crm_ids' => $orders4req,
      ));
      
      foreach ($orders as $order_id => &$order) {
        $order['crmStatus'] = 14;
        foreach ($crm_orders as $obj) {
          if ($order['crmID'] == $obj->id) {
            switch ($obj->status) {
              case 'complete':
                $order['crmStatus'] = 5;  // Complete
                break;
              default :
                $order['crmStatus'] = 0;  // Unknown
                break;
            }
            break;
          }
        }
        if ($order['crmStatus'] > 0) {
          $this->model_extension_module_integration->updateOrderStatus($order_id, $order['crmStatus']);
        }
      }
    }
  
    if (!$this->config->get('dev')) {
      $this->locking->unlock('SyncIntegration');
      $logger->write('The intergation has finished, status is unlocked');
    }
    $this->model_tool_status->done('sync', 1);
    
  }
}
