<?php

class ControllerExtensionFeedMarketIcml extends Controller {
  
  private $XML;
  private $XCategories;
  
  public function index() {
    
    setlocale(LC_ALL, 'ru_RU.utf8');
    
    $XML_str = $this->cache->get('market_icml');
    if (php_sapi_name() == 'cli') {
      $this->load->model('tool/status');
      $this->model_tool_status->start('market_icml');
  		$XML_str = false;
    }
    
    if (!$XML_str) {
      
      $this->load->model('catalog/category');
      $this->load->model('catalog/product');
      $this->load->model('tool/image');
      $this->load->model('extension/module/integration');
      $this->load->model('tool/status');
  
      $this->XML = new DOMDocument('1.0', 'utf-8');
      $Translit = new Translit();
      
      $XRoot = $this->XML->createElement('yml_catalog');
      $XDate = $this->XML->createAttribute('date');
      $XDate->value = strftime('%Y-%m-%d %H:%M');
//			$XDate->value=strftime('%Y-%m-%d %H:%M:%S');
      $XRoot->appendChild($XDate);
      
      $XShop = $this->XML->createElement('shop');
      $this->XCategories = $this->XML->createElement('categories');
      $this->getCategories();
      $XOffers = $this->XML->createElement('offers');
      $XCurrencies = $this->XML->createElement('currencies');
      $XDeliveryOptions = $this->XML->createElement('delivery-options');
      
      $XShop->appendChild($this->XML->createElement('name', $this->config->get('config_name')));
      $XShop->appendChild($this->XML->createElement('company', $this->config->get('config_owner')));
      $XShop->appendChild($this->XML->createElement('url', $this->config->get('config_url')));
      
      
      $XDeliveryOption = $this->XML->createElement('option');
      $XDeliveryOption_cost = $this->XML->createAttribute('cost');
      $XDeliveryOption_cost->value = 0;
      $XDeliveryOption_days = $this->XML->createAttribute('days');
      $XDeliveryOption_days->value = '0-1';
      $XDeliveryOption->appendChild($XDeliveryOption_cost);
      $XDeliveryOption->appendChild($XDeliveryOption_days);
      $XDeliveryOptions->appendChild($XDeliveryOption);
      $XShop->appendChild($XDeliveryOptions);
      
      $XCurRUR = $this->XML->createElement('currency');
      $XCurRUR_id = $this->XML->createAttribute('id');
      $XCurRUR_rate = $this->XML->createAttribute('rate');
      $XCurRUR_id->value = 'RUR';
      $XCurRUR_rate->value = 1;
      $XCurRUR->appendChild($XCurRUR_id);
      $XCurRUR->appendChild($XCurRUR_rate);
      $XCurrencies->appendChild($XCurRUR);
  
      $counter=0;
      foreach ($this->model_catalog_product->getProducts() as $product) {
        $start = microtime(true);
        $prod_info = $this->model_extension_module_integration->getProductIDs($product['product_id']);
        $XOffer = $this->XML->createElement('offer');
        foreach ($this->model_catalog_product->getCategories($product['product_id']) as $product_category) {
          $product['category_id'] = $product_category['category_id'];
          if ($product['category_id'] != 59)
            break;
        }
        $utm_campaign = '';
        if (isset($product['category_id'])) {
          $product_category = $this->model_catalog_category->getCategory($product['category_id']);
          $utm_campaign = $Translit->cyr2lat(str_replace(' ', '_', $product_category['name']));
          $XOffer->appendChild($this->XML->createElement('categoryId', $product['category_id']));
        }
        $utm_content = $this->getRange($product['price']);
        $product_link_args = array(/*					'utm_source'	=> 'yandex_market',
					'utm_medium'	=> 'cpc',
					'utm_campaign'	=> $utm_campaign,
					'utm_term'	=> $product['product_id'],
					'utm_content'	=> $utm_content,
*/);
        if (isset($prod_info['externalCode'])) {
          $XOffer_id = $this->XML->createAttribute('id');
          $XOffer_id->value = $prod_info['externalCode'];
          $XOffer->appendChild($XOffer_id);
          $sku = explode('#', $prod_info['externalCode']);
          if (sizeof($sku) > 1) {
            $XOffer_id = $this->XML->createAttribute('productId');
            $XOffer_id->value = $sku[0];
            $XOffer->appendChild($XOffer_id);
          }
        }
        $XOffer_quantity = $this->XML->createAttribute('quantity');
        $XOffer_quantity->value = $product['quantity'];
        $XOffer->appendChild($XOffer_quantity);
        /*				$XOffer_type = $this->XML->createAttribute('type');
                $XOffer_type->value = 'vendor.model';
                $XOffer->appendChild($XOffer_type);
                $XOffer_available = $this->XML->createAttribute('available');
                $XOffer_available->value = ($product['quantity'] > 0 ? 'true' : 'false');
                $XOffer->appendChild($XOffer_available);
        */
        $name = $product['name'];
        /*				$replace = '';
                if (
                  preg_match('/[Сс]умка/i', $name) == 1 or
                  preg_match('/[Бб]арсетка/i', $name) == 1 or
                  preg_match('/[Пп]апка/i', $name) == 1
                ) {
                  $replace = 'мужская кожаная';
                  if ( preg_match('/black/i', $name) == 1 )
                    $replace .= ' черная';
                  elseif ( preg_match('/brown/i', $name) == 1 )
                    $replace .= ' коричневая';
                } elseif (
                  preg_match('/[Кк]латч/i', $name) == 1 or
                  preg_match('/[Пп]ортфель/i', $name) == 1
                ) {
                  $replace = 'мужской кожаный';
                  if ( preg_match('/black/i', $name) == 1 )
                    $replace .= ' черный';
                  elseif ( preg_match('/brown/i', $name) == 1 )
                    $replace .= ' коричневый';
                }
                if ( strpos('Lakestone', $name) !== FALSE )
                  $name = str_replace('Lakestone', $replace, $name);
                elseif ( ! empty($replace) )
                  $name .= ' ' . $replace;
        */        //echo $product['name'] . " :: $name\n";
        if (sizeof($prod_info) > 0) {
          $XOffer->appendChild($this->XML->createElement('xmlId', $prod_info['externalCode']));
          $XOffer->appendChild($this->XML->createElement('purchasePrice', (int)$prod_info['price2']));
        }
        $XOffer->appendChild($this->XML->createElement('name', $name));
        $XOffer->appendChild($this->XML->createElement('model', $product['model']));
        $XOffer->appendChild($this->XML->createElement('description', htmlspecialchars(str_replace("\n", ' ', $product['description']))));
        $XOffer->appendChild($this->XML->createElement('vendor', 'Lakestone'));
        $XOffer->appendChild($this->XML->createElement('currencyId', 'RUR'));
        $XOffer->appendChild($this->XML->createElement('pickup', 'true'));
        $XOffer->appendChild($this->XML->createElement('store', 'false'));
        $XOffer->appendChild($this->XML->createElement('price', sprintf('%d', $product['price'])));
        $XOffer->appendChild($this->XML->createElement('url', $this->url->link('product/product', array_merge(array('product_id' => $product['product_id']), $product_link_args))));
        $XOffer->appendChild($this->XML->createElement('picture', $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'))));
        
        $textParam = 'Артикул ' . $product['model'];
        #$textParam .= '; Модель ' . $product['model'];
        
  
        $ProdIntegrationInfo = $this->model_extension_module_integration->getIntegrationProductByCode($product['model']);
        if ($ProdIntegrationInfo) {
          $XParam = $this->XML->createElement('param', $ProdIntegrationInfo['storage_cell']);
          $XParam_name = $this->XML->createAttribute('name');
          $XParam_name->value = 'Ячейка хранения';
          $XParam->appendChild($XParam_name);
          $XParam_code = $this->XML->createAttribute('code');
          $XParam_code->value = 'storage_cell';
          $XParam->appendChild($XParam_code);
          $XOffer->appendChild($XParam);
        }
        
        $XParam = $this->XML->createElement('param', $product['sku']);
        $XParam_name = $this->XML->createAttribute('name');
        $XParam_name->value = 'Код';
        $XParam->appendChild($XParam_name);
        $XParam_code = $this->XML->createAttribute('code');
        $XParam_code->value = 'code';
        $XParam->appendChild($XParam_code);
        $XOffer->appendChild($XParam);
        
        $XParam = $this->XML->createElement('param', $product['model']);
        $XParam_name = $this->XML->createAttribute('name');
        $XParam_name->value = 'Артикул';
        $XParam->appendChild($XParam_name);
        $XParam_code = $this->XML->createAttribute('code');
        $XParam_code->value = 'article';
        $XParam->appendChild($XParam_code);
        $XOffer->appendChild($XParam);
        
        foreach ($this->model_catalog_product->getProductAttributes($product['product_id']) as $attribute_group) {
          
          
          if (preg_match('/сновные/i', $attribute_group['name']) === 1) {
            foreach ($attribute_group['attribute'] as $attribute) {
              $XParam = $this->XML->createElement('param', $attribute['text']);
              $XParam_name = $this->XML->createAttribute('name');
              $XParam_name->value = $attribute['name'];
              $XParam->appendChild($XParam_name);
              $XParam_code = $this->XML->createAttribute('code');
              switch ($attribute['name']) {
                case 'Цвет':
                  $XParam_code->value = 'color';
                  break;
                case 'Размер':
                  $XParam_code->value = 'format';
                  break;
                case 'Материал':
                  $XParam_code->value = 'material';
                  break;
                case 'Вес, грамм':
                  $XParam_code->value = 'weight';
                  break;
                case 'Внешние размеры, см':
                  $XParam_code->value = 'size';
                  break;
                case 'Вместимость':
                  $XParam_code->value = 'capacity';
                  break;
                default:
                  $XParam_code->value = $Translit->cyr2lat($attribute['name']);
              }
              $XParam->appendChild($XParam_code);
//              $textParam .= '; ' . $attribute['name'] . ' ' . $attribute['text'];
              $XOffer->appendChild($XParam);
            }
//            if (!empty($textParam)) {
//              $XOffer->appendChild($this->XML->createElement('textParams', $textParam));
//            }
          }
        }
        $image_counter = 3;
        foreach ($this->model_catalog_product->getProductImages($product['product_id']) as $image) {
          if ($this->model_tool_image->is_image($image['image'])) {
            $XOffer->appendChild($this->XML->createElement('picture', $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'))));
          }
          if ($image_counter-- <= 0) break;
        }
        $XOffers->appendChild($XOffer);
        /**********
        $runtime = microtime(true) - $start;
        printf("time: %0.5f\n", $runtime);
        now($counter++);
        now('end');
//        if ($counter++>10) dd(0);
        if ($runtime > 0.1) dd($runtime);
        /**********/

      }
      
      $XShop->appendChild($XCurrencies);
      $XShop->appendChild($this->XCategories);
      $XShop->appendChild($XOffers);
      $XRoot->appendChild($XShop);
      $this->XML->appendChild($XRoot);
      
      $XML_str = $this->XML->saveXML();
      $this->cache->set('market_icml', $XML_str);
      
    }
  
    if (php_sapi_name() == 'cli') {
      $this->model_tool_status->done('market_icml', 1);
    } else {
      $this->response->addHeader('Content-Type: text/xml');
      $this->response->setOutput($XML_str);
    }
    
  }
  
  protected function getRange($value) {
    $steps = array(50000, 20000, 10000, 5000, 2500, 1000);
    if ($value > $steps[0])
      return $steps[0] + 1;
    for ($pointer = 0; $pointer < sizeof($steps) - 1; $pointer++) {
      if ($value > $steps[$pointer + 1])
        return ($steps[$pointer + 1] + 1) . '_' . $steps[$pointer];
    }
    return '0_' . $steps[$pointer];
    
  }
  
  protected function getCategories($parent_id = 0) {
    
    if (!($this->XCategories instanceof DOMElement))
      $this->XCategories = $this->XML->createElement('categories');
    
    foreach ($this->model_catalog_category->getCategories($parent_id) as $category) {
      $XCat = $this->XML->createElement('category', $category['name']);
      $XCat_id = $this->XML->createAttribute('id');
      $XCat_id->value = $category['category_id'];
      if ($category['parent_id'] > 0) {
        $XCat_pid = $this->XML->createAttribute('parentId');
        $XCat_pid->value = $category['parent_id'];
        $XCat->appendChild($XCat_pid);
      }
      $XCat->appendChild($XCat_id);
      $this->XCategories->appendChild($XCat);
      
      $this->getCategories($category['category_id']);
    }
    
  }
  
}
