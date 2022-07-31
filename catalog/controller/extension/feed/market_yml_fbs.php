<?php

class ControllerExtensionFeedMarketYmlFbs extends Controller {
  
  private $XML;
  private $XCategories;
  private $CategoriesYML = array();
  private $YMLPath = array();
  private $YMLCategories = array();
  private $YMLCounter = 1;
  private $YMLDefaultPath = 'Все товары/Одежда, обувь и аксессуары/Аксессуары/Сумки и чемоданы';
  
  public function index() {
    
    setlocale(LC_ALL, 'ru_RU.utf8');
    
    $Translit = new Translit();
    
    if ($this->config->get('dev')) {
      $XML_str = false;
      $Limit = 5;
    } else {
      $XML_str = $this->cache->get('market_yml_fbs');
      $Limit = -1;
    }
    if (php_sapi_name() == 'cli') {
      $this->load->model('tool/status');
      $this->model_tool_status->start('market_yml_fbs');
      $XML_str = false;
    }
    
    if (!$XML_str) {
      
      $this->load->model('catalog/category');
      $this->load->model('catalog/product');
      $this->load->model('tool/image');
      
      $this->XML = new DOMDocument('1.0', 'utf-8');
      
      $implementation = new DOMImplementation();
      $this->XML->appendChild($implementation->createDocumentType('yml_catalog SYSTEM "shops.dtd"'));

#			$this->XML->formatOutput = true;
      $Translit = new Translit();
      
      $XRoot = $this->XML->createElement('yml_catalog');
      $XDate = $this->XML->createAttribute('date');
      $XDate->value = strftime('%Y-%m-%d %H:%M');
//			$XDate->value=strftime('%Y-%m-%d %H:%M:%S');
      $XRoot->appendChild($XDate);
      
      $XShop = $this->XML->createElement('shop');
      $this->XCategories = $this->XML->createElement('categories');
      //$this->getCategories();
      $this->getCategoriesYMLPath();
      
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
      
      foreach ($this->model_catalog_product->getProducts() as $product) {
        if ($Limit-- == 0) break;
        if ($product['quantity'] < 0) continue;
        if ($product['product_id'] == GIFT_SET_ID) continue;
        $XOffer = $this->XML->createElement('offer');
        foreach ($this->model_catalog_product->getCategories($product['product_id']) as $product_category) {
          $product['category_id'] = $product_category['category_id'];
          if ($product['category_id'] != 59)
            break;
        }
        $utm_campaign = '';
        
        if (isset($product['category_id'])) {
          $product_category = $this->model_catalog_category->getCategory($product['category_id']);
          $utm_campaign = $Translit->cyr2lat(str_replace(' ', '_', $product_category['name'] ?? ''));
          //$XOffer->appendChild($this->XML->createElement('categoryId', $product['category_id']));
          if (!empty($this->CategoriesYML[$product['category_id']])) {
            $XOffer->appendChild($this->XML->createElement('categoryId', $this->CategoriesYML[$product['category_id']]));
          }
        } else {
          $XOffer->appendChild($this->XML->createElement('categoryId', $this->buildYMLCategory($this->YMLCategories, explode('/', $this->YMLDefaultPath))[1]));
        }
        $utm_content = $this->getRange($product['price']);
        $product_link_args = array(
            'utm_source' => 'yandex_market',
            'utm_medium' => 'cpc',
            'utm_campaign' => $utm_campaign,
            'utm_term' => $product['product_id'],
            'utm_content' => $utm_content,
        );
        $XOffer_id = $this->XML->createAttribute('id');
        $XOffer_id->value = $product['product_id'];
        $XOffer->appendChild($XOffer_id);
        //$XOffer_type = $this->XML->createAttribute('type');
        //$XOffer_type->value = 'vendor.model';
        //$XOffer->appendChild($XOffer_type);
        $XOffer_available = $this->XML->createAttribute('available');
        $XOffer_available->value = ($product['quantity'] > 0 ? 'true' : 'false');
        $XOffer->appendChild($this->XML->createElement('sales_notes', 'Наличные, квитанция Сбербанка, банковская карта'));
        $XOffer->appendChild($XOffer_available);
        if ($product['yml_name']) {
          $name = $product['yml_name'];
        } else {
          $name = $product['name'];
          $replace = '';
          if (
              preg_match('/[Сс]умка/i', $name) == 1 or
              preg_match('/[Бб]арсетка/i', $name) == 1 or
              preg_match('/[Пп]апка/i', $name) == 1
          ) {
            $replace = 'мужская кожаная';
            if (preg_match('/black/i', $name) == 1)
              $replace .= ' черная';
            elseif (preg_match('/brown/i', $name) == 1)
              $replace .= ' коричневая';
          } elseif (
              preg_match('/[Кк]латч/i', $name) == 1 or
              preg_match('/[Пп]ортфель/i', $name) == 1
          ) {
            $replace = 'мужской кожаный';
            if (preg_match('/black/i', $name) == 1)
              $replace .= ' черный';
            elseif (preg_match('/brown/i', $name) == 1)
              $replace .= ' коричневый';
          }
          if (strpos('Lakestone', $name) !== FALSE)
            $name = str_replace('Lakestone', $replace, $name);
          elseif (!empty($replace))
            $name .= ' ' . $replace;
        }
        //echo $product['name'] . " :: $name\n";
        $XOffer->appendChild($this->XML->createElement('name', $name));
        $XOffer->appendChild($this->XML->createElement('vendorCode', $product['model']));
        $XOffer->appendChild($this->XML->createElement('count', $product['quantity'] ?? 0));
        if ($product['yml_model'])
          $XOffer->appendChild($this->XML->createElement('model', $product['yml_model']));
        $XDesc = $this->XML->createElement('description');
        $XDesc->appendChild($this->XML->createCDATASection(htmlspecialchars(strip_tags(str_replace(['<br>', '</p>'], "\n", htmlspecialchars_decode($product['description']))))));
        $XOffer->appendChild($XDesc);
        $XOffer->appendChild($this->XML->createElement('vendor', 'Lakestone'));
        $XOffer->appendChild($this->XML->createElement('manufacturer', 'Лэйкстоун'));
        $XOffer->appendChild($this->XML->createElement('country_of_origin', 'Россия'));
        $XOffer->appendChild($this->XML->createElement('currencyId', 'RUR'));
        $XOffer->appendChild($this->XML->createElement('pickup', 'true'));
        $XOffer->appendChild($this->XML->createElement('store', 'false'));
        $XOffer->appendChild($this->XML->createElement('price', sprintf('%d', $product['price'])));
        $url = $this->url->link('product/product', array_merge(array('product_id' => $product['product_id']), $product_link_args));
        $url = preg_replace_callback('/&.{4}/', function ($m) {
          if ($m[0] == '&amp;') return $m[0]; else return str_replace('&', '&amp;', $m[0]);
        }, $url);
        $XOffer->appendChild($this->XML->createElement('url', $url));
        $XOffer->appendChild($this->XML->createElement('picture', $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'))));
        $dimensions = $weight = null;
        foreach ($this->model_catalog_product->getProductAttributes($product['product_id']) as $attribute_group) {
          $textParam = '';
          if (preg_match('/сновные/i', $attribute_group['name']) === 1) {
            foreach ($attribute_group['attribute'] as $attribute) {
              if ($attribute['name'] == 'Внешние размеры, см') {
                $dimensions = $attribute['text'];
              }
              if (
                  empty($dimensions)
                  and $attribute['name'] == 'Внутренние размеры, см'
              ) {
                $dimensions = $attribute['text'];
              }
              if ($attribute['name'] == 'Вес, грамм') {
                $weight = round(intval($attribute['text'])/1000, 3);
              }
              $XParam = $this->XML->createElement('param', $attribute['text']);
              $XParam_name = $this->XML->createAttribute('name');
              $XParam_name->value = $attribute['name'];
              $XParam->appendChild($XParam_name);
              $textParam .= $attribute['name'] . ' ' . $attribute['text'] . '; ';
              $XOffer->appendChild($XParam);
            }
            /*
                        if ( sizeof($textParam) > 0 ) {
                          $textParam .= 'Артикул ' . $product['model'];
                          $XOffer->appendChild($this->XML->createElement('textParams', $textParam));
                        }
            */
          }
        }
        $XOffer->appendChild($this->XML->createElement('weight', $weight ?? 0.5));
        if ($dimensions) {
          $dimensions = str_replace(['х', 'x'], '/', $dimensions);
          $dimensions = str_replace(',', '.', $dimensions);
          $dimensions = preg_replace('|[^\d/]|', '', $dimensions);
        }
        if (!preg_match('|^\d+/\d+/\d+$|', $dimensions)) $dimensions = '37/26/10';
        $XOffer->appendChild($this->XML->createElement('dimensions', $dimensions));
        $image_counter = 8;
        foreach ($this->model_catalog_product->getProductImages($product['product_id']) as $image) {
          if ($this->model_tool_image->is_image($image['image'])) {
            $XOffer->appendChild($this->XML->createElement('picture', $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'))));
            if ($image_counter-- <= 0) break;
          }
        }
        $XOffers->appendChild($XOffer);
      }
      
      $XShop->appendChild($XCurrencies);
      $XShop->appendChild($this->XCategories);
      $XShop->appendChild($XOffers);
      $XRoot->appendChild($XShop);
      $this->XML->appendChild($XRoot);
      
      $XML_str = $this->XML->saveXML();
      $this->cache->set('market_yml_fbs', $XML_str);
      
    }
  
    if (php_sapi_name() == 'cli') {
      $this->model_tool_status->done('market_yml_fbs', 1);
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
  
  protected function getCategoriesYMLPath($parent_id = 0) {
    
    foreach ($this->model_catalog_category->getCategories($parent_id) as $category) {
      if (!$category['yml_path'])
        $category['yml_path'] = $this->YMLDefaultPath;
      $this->CategoriesYML[$category['category_id']] = $this->buildYMLCategory($this->YMLCategories, explode('/', $category['yml_path']))[1];
      $this->getCategoriesYMLPath($category['category_id']);
    }
  }
  
  protected function buildYMLCategory(&$parent, $path) {
    static $current_id = 0, $parent_id = 0;
    $item = array_shift($path);
    if (!isset($parent[$item])) {
      $this->YMLCounter++;
      $parent[$item] = array(
          'id' => $this->YMLCounter,
      );
      $XCat = $this->XML->createElement('category');
      if ($parent_id > 0) {
        $XCat_pid = $this->XML->createAttribute('parentId');
        $XCat_pid->value = $parent_id;
        $XCat->appendChild($XCat_pid);
      }
      $XCat_id = $this->XML->createAttribute('id');
      $XCat_id->value = $this->YMLCounter;
      $XCat->appendChild($XCat_id);
      $XCat->nodeValue = $item;
      $this->XCategories->appendChild($XCat);
    }
    $current = &$parent[$item];
    $current_id = &$parent[$item]['id'];
    if ($path) {
      $parent_id = $current_id;
      return $this->buildYMLCategory($current, $path);
    } else {
      return array($parent_id, $current_id);
    }
  }
  
}
