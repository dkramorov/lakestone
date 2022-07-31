<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ozon
 *
 * @author mic
 */
class ControllerToolOzon extends Controller {

  private $OzonProducts = [];
  /**
   * @var Log
   */
  private $Log;
  
  public function __construct($registry) {
    parent::__construct($registry);
    $this->Log = new Log('ozon.log');
  }
  
  public function index() {
    
  }

  public function sync() {


    if (!$this->user->isLogged() and ! (
            !empty($this->request->get['key']) and
            $this->request->get['key'] == 'GMbRZckWzLm4yjQEcPZ0fFSx1EKwwTV7'
            )) {
      echo 'Access deny';
      return;
    }

    if (
        !$this->config->get('dev')
        and $this->cache->get('OzonSyncLock')
    ) {
      echo "block";
      return False;
    }

    set_time_limit(0);
    ignore_user_abort(TRUE);

    $this->cache->set('OzonSyncLock', true);

    $this->Log->write('Ozon updater has started');

    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('catalog/attribute');
    $this->load->model('extension/ozon/api');
    $this->load->model('extension/module/integration');
    $this->load->model('tool/status');
  
    $this->model_tool_status->start('ozon');

    $this->OzonStocks = $this->model_extension_ozon_api->getStocks([], false);
    $this->OzonPrices = $this->model_extension_ozon_api->getPrices([], false);
//    $this->OzonProducts = $this->model_extension_ozon_api->getProducts();
//    $Warehouses = $this->model_extension_ozon_api->getWarehouses();
  
    $products = $prices = $stocks = [];
    $Now = new DateTime();

    /*foreach ($this->model_catalog_product->getProducts() as $product) {
      $prod = $this->findProduct($this->OzonProducts, $product['model']);
      if ($prod) {
        $special = false;
        $specials = $this->model_catalog_product->getProductSpecials($product['product_id']);
        foreach ($specials as $s) {
          $from = new DateTime($s['date_start']);
          $to = new DateTime($s['date_end']);
          if ($s['date_start'] != '0000-00-00' and new DateTime($s['date_start']) > $Now)
            continue;
          if ($s['date_end'] != '0000-00-00' and new DateTime($s['date_end']) < $Now)
            continue;
          $special = $s['price'];
          break;
        }
        $products[] = [
            'prod' => $prod,
            'model' => $product['model'],
            'status' => $product['status'] and !$special,
        ];
      }
    }*/
    foreach ($this->model_extension_module_integration->getIntegrationVariants() as $product) {
      $prod = $this->findProduct($this->OzonStocks, $product['code']);
      if ($prod) {
          $stock = $product['quantity'] > 1 ? $product['quantity'] : 0;
          if ($prod['stock']['present'] != $stock) {
            $this->Log->write('Update stock for ' . $product['code'] . ': ' . $prod['stock']['present'] . ' > ' . $stock);
            $stocks[] = [
//              'warehouse_id' => $Warehouses[0]['warehouse_id'],
                'product_id' => (int)$prod['product_id'],
                'offer_id' => $product['code'],
                'stock' => $stock,
            ];
          }
      }
      $prod = $this->findProduct($this->OzonPrices, $product['code']);

      if ($prod) {
          # RU 14092021 По просьбе Юли подымаем РРЦ на 67% (задача https://lakestone.bitrix24.ru/extranet/contacts/personal/user/61/tasks/task/view/2619/)
          $oldPrice = strval(round($product['price'] * 1.67));
          $ozonOldPrice = !empty($prod['price']['old_price']) ? $prod['price']['old_price'] : 0;
          $newPrice = strval(round($product['price']));
          $ozonPrice = $prod['price']['price'];

        if ($ozonOldPrice != $oldPrice) {
          $this->Log->write('Update price for ' . $product['code'] . ': ' . $ozonOldPrice . ' > ' . $oldPrice);
          $prices[] = [
              'product_id' => (int)$prod['product_id'],
              'offer_id' => $product['code'],
              'old_price' => $oldPrice,
              // RU 15092021 отключаем обновление цены товара, т.к. оно будет перекрывать ручную выгрузку цен из файла Юли
              //'price' => $newPrice,
              'vat' => '0.2'
          ];
        }
      }
    }
    if (sizeof($stocks) > 0) {
      $faults = $this->model_extension_ozon_api->updateStocks($stocks);
      foreach ($faults as $item) {
        $this->Log->write('Unable to update stock: ' . json_encode($item));
      }
    }
    if (sizeof($prices) > 0) {
      $faults = $this->model_extension_ozon_api->updatePrices($prices);
      foreach ($faults as $item) {
        $this->Log->write('Unable to update price: ' . json_encode($item));
      }
    }
    $this->Log->write('Ozon updater has finished');
    $this->cache->set('OzonSyncLock', false);
    $this->model_tool_status->done('ozon', 1);
  
  }

  private function findAttribute(array $attributes, array $attribute, array &$product_attribute) {
    $attribute_id = false;
    switch (true) {
      case ($attribute['name'] == 'Объем, мл'):
        $attribute_id = 30;
        break;
      case ($attribute['name'] == 'Кол-во внутренних отделений'):
        $attribute_id = 22;
        break;
      case ($attribute['name'] == 'Вес товара, г'):
      case ($attribute['name'] == 'Цвет'):
      case ($attribute['name'] == 'Видеоролик'):
      case ($attribute['name'] == 'Размеры, мм'):
      case ($attribute['name'] == 'Комплектация'):
      case ($attribute['name'] == 'Гарантия'):
      case ($attribute['name'] == 'Страна-изготовитель'):
      case ($attribute['name'] == 'Количество в упаковке, шт.'):
      case ($attribute['name'] == 'Форма выпуска'):
      case ($attribute['name'] == 'Назначение'):
      case ($attribute['name'] == 'Вес, кг'):
      case ($attribute['name'] == 'Особенности применения'):
      case ($attribute['name'] == 'Название модели'):
      case ($attribute['name'] == 'Товар бывший в употреблении'):
      case ($attribute['name'] == 'Год выпуска'):
      case ($attribute['name'] == 'Партномер'):
//      case ($attribute['name'] == ''):
        break;
      default:
        echo 'Unusage attribute: ' . $attribute['name'] . ", (" . $attribute['type'] . ")\n";
        if ($attribute['type'] == 'option') {
          foreach ($attribute['option'] as $opt) {
            echo $opt['value'] . ' (' . $opt['id'] . '); ';
          }
          echo "\n";
        }
    }
    if ($attribute_id) {
      foreach ($attributes as $attr) {
        if ($attribute_id == $attr['attribute_id']) {
          $product_attribute['value'] = $attr['product_attribute_description'][2]['text'];
          $product_attribute['name'] = $attribute['name'];
          $product_attribute['id'] = $attribute['id'];
        }
      }
    }
  }
  
  private function pushOzon($products, $prices, $stocks) {
//    var_dump($products);
    // products
//    if (!empty($products)) {
//      $ret = $this->model_extension_ozon_api->importProducts($products);
//      if (isset($ret->result)) {
//        $this->Log->write('Added ' . sizeof($products) . ' products. Task ID=' . $ret->result->task_id);
//      } else {
//        $this->Log->write('Ozon error: ' . json_encode($ret));
//      }
//    }
    $active = $inactive = [];
    foreach ($products as $product) {
      $log = '';
      if ($product['status']) {
        $log = 'Product ' . $product['model'] . ' is activating: ';
        $active[] = $product['prod']['product_id'];
      } else {
        $log = 'Product ' . $product['model'] . ' is deactivating: ';
        $inactive[] = $product['prod']['product_id'];
      }
    }
    $ret = $this->model_extension_ozon_api->activateProduct($active);
    if (isset($ret->result)) {
      $this->Log->write('Activated:' . $ret->result);
    } else {
      $this->Log->write('An activating error: ' . json_encode($ret));
    }
    $ret = $this->model_extension_ozon_api->deactivateProduct($inactive);
    if (isset($ret->result)) {
      $this->Log->write('Inactivated:' . $ret->result);
    } else {
      $this->Log->write('An inactivating error: ' . json_encode($ret));
    }

//    var_dump($prices);
//    return;
    // prices
    if (!empty($prices)) {
      $ret = $this->model_extension_ozon_api->updatePrices($prices);
      if (isset($ret->result)) {
        $counter  = 0;
        foreach ($ret->result as $item) {
          if ($item->updated) $counter++;
          if (!empty($item->errors)) $this->Log->write('Ozon price update errors:' . json_encode($item));
        }
        $this->Log->write('Updated ' . $counter . ' prices for ' . sizeof($prices) . ' products.');
      } else {
        $this->Log->write('Ozon error: ' . json_encode($ret));
      }
    }
    // stocks
    if (!empty($stocks)) {
      $ret = $this->model_extension_ozon_api->updateStocks($stocks);
      if (isset($ret->result)) {
        $counter  = 0;
        foreach ($ret->result as $item) {
          if ($item->updated) $counter++;
          if (!empty($item->errors)) $this->Log->write('Ozon stocks update errors:' . json_encode($item));
        }
        $this->Log->write('Updated ' . $counter . ' stocks for ' . sizeof($prices) . ' products.');
      } else {
        $this->Log->write('Ozon error: ' . json_encode($ret));
      }
    }
  }

  private function findProduct($source, $model) {
    $res = array_search($model, array_column($this->OzonPrices, 'offer_id'));
    if ($res !== false)
      return $source[$res];
    else
      return false;
  }

  private function findOzonCat($needle, $haystack, $text = '') {
    static $result = [];
    foreach ($haystack as $item) {
      if (sizeof($result) > 5)
        break;
      $ct = '';
      if (!empty($text))
        $ct = $text . '&nbsp;>&nbsp;';
      $ct .= $item['title'];
      if (
              (is_string($needle) and mb_stripos($item['title'], $needle) !== false) or ( is_int($needle) and $needle == $item['category_id'])
      ) {
        $result[] = [$item['category_id'], $ct];
      }
      if (!empty($item['children'])) {
        $this->findOzonCat($needle, $item['children'], $ct);
      }
    }
    return $result;
  }

}
