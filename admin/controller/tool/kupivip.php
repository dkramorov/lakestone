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
class ControllerToolKupivip extends Controller {

  private $KipivipProducts = [];
  
  public function __construct($registry) {
    parent::__construct($registry);
    $this->log = new Log('kupivip.log');
  }

  public function index() {
    
    if (!$this->user->isLogged() and ! (
            !empty($this->request->get['key']) and
            $this->request->get['key'] == 'ib0MZr41nFx1IFRCmWtVUBUZhfHCpyZw'
        )) {
      echo 'Access deny';
      return;
    }
  
    switch ($this->request->get['call']) {
      case 'test':
        echo "OK\n";
        break;
      case 'sync':
        return $this->sync();
      default:
        echo "Method unknown\n";
    }
    
  }

  private function sync() {
  
    if (
        !$this->config->get('dev')
        and $this->cache->get('KupivipSyncLock')
    ) {
      echo "Locked\n";
      return false;
    }
  
    set_time_limit(0);
    ignore_user_abort(TRUE);
    $this->cache->set('KupivipSyncLock', true);
  
  
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('catalog/attribute');
    $this->load->model('extension/kupivip/api');
    $this->load->model('tool/status');
  
    $this->model_tool_status->start('kupivip');
    
    $this->log->write('Kupivip updater has started');

//    $this->load->model('tool/image');
  
    $this->KipivipProducts = $this->model_extension_kupivip_api->getProducts();
  
    $Now = new DateTime();

    foreach ($this->model_catalog_product->getProducts() as $product) {
      $prod = $this->findProduct($product['model']);
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
//      var_dump($product);
      if ($prod) {
        echo 'need to update: ' . $product['model'] . "\n";
        $prod_info = $this->model_extension_kupivip_api->getProduct($prod['article'], $prod['color']);
/*        if ($prod['productStatus'] != 'published' and $product['quantity'] > 0) {
          $this->model_extension_kupivip_api->hideProduct($prod['article'], $prod['color']);
        }
        if ($prod['productStatus'] == 'published' and $product['quantity'] <= 0) {
          $this->model_extension_kupivip_api->hideProduct($prod['article'], $prod['color']);
        }*/
        if ($prod_info['retailPrice'] != (int) $product['price']) {
          $prod_info['retailPrice'] = $prod_info['storePrice'] = (int) $product['price'];
        }
        $this->model_extension_kupivip_api->updateProduct($prod_info);
        $this->model_extension_kupivip_api->updateProductReserve($prod['article'], $prod['color'], $prod['items'][0]['variantCode'], $product['quantity']);
      }
    }
    $this->log->write('Kupivip updater has finished');
    $this->cache->set('KupivipSyncLock', false);
    $this->model_tool_status->done('kupivip', 1);
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
//        $this->log->write('Added ' . sizeof($products) . ' products. Task ID=' . $ret->result->task_id);
//      } else {
//        $this->log->write('Ozon error: ' . json_encode($ret));
//      }
//    }
    foreach ($products as $product) {
      $log = '';
      if ($product['status']) {
        $log = 'Product ' . $product['model'] . ' is activating: ';
        $ret = $this->model_extension_ozon_api->activateProduct($product['prod']['product_id']);
      } else {
        $log = 'Product ' . $product['model'] . ' is deactivating: ';
        $ret = $this->model_extension_ozon_api->deactivateProduct($product['prod']['product_id']);
      }
      if (isset($ret->result)) {
        $log .= (string) $ret->result;
      } else {
        $log .= 'error: ' . json_encode($ret);
      }
      $this->log->write($log);
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
          if (!empty($item->errors)) $this->log->write('Ozon price update errors:' . json_encode($item));
        }
        $this->log->write('Updated ' . $counter . ' prices for ' . sizeof($prices) . ' products.');
      } else {
        $this->log->write('Ozon error: ' . json_encode($ret));
      }
    }
    // stocks
    if (!empty($stocks)) {
      $ret = $this->model_extension_ozon_api->updateStocks($stocks);
      if (isset($ret->result)) {
        $counter  = 0;
        foreach ($ret->result as $item) {
          if ($item->updated) $counter++;
          if (!empty($item->errors)) $this->log->write('Ozon stocks update errors:' . json_encode($item));
        }
        $this->log->write('Updated ' . $counter . ' stocks for ' . sizeof($prices) . ' products.');
      } else {
        $this->log->write('Ozon error: ' . json_encode($ret));
      }
    }
  }

  private function findProduct($model) {
//    var_dump($model);
    foreach ($this->KipivipProducts as $prod) {
//      var_dump($prod->offer_id);
      if ($model == str_replace('_', '/', $prod['article']))
        return $prod;
    }
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
