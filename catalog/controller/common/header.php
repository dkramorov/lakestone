<?php

class ControllerCommonHeader extends Controller {
  
  public function index() {
    //now('h_start');
    
    // check special domain
    if (substr(ROOT_DOMAIN, 0, 1) == '_') {
      $this->document->addMeta('robots', 'noindex, nofollow');
    }
    
    // Analytics
    $this->load->model('extension/extension');
    $data['analytics'] = array();
    $analytics = $this->model_extension_extension->getExtensions('analytics');
    
    foreach ($analytics as $analytic) {
      if ($this->config->get($analytic['code'] . '_status')) {
        $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
      }
    }
    
    if ($this->request->server['HTTPS']) {
      $server = $this->config->get('config_ssl');
    } else {
      $server = $this->config->get('config_url');
    }
    
    if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
      $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon', mime_content_type(DIR_IMAGE . $this->config->get('config_icon')));
    }
    
    $data['cart_link'] = $this->url->link('checkout/cart');
    
    $data['title'] = $this->document->getTitle();
    $data['open'] = $this->config->get('config_open');
    
    $data['base'] = $server;
    $data['description'] = $this->document->getDescription();
    $data['keywords'] = $this->document->getKeywords();
    $data['links'] = $this->document->getLinks();
    $data['meta'] = $this->document->getMeta();
    $data['style'] = $data['script'] = '';
    //now('style');
    $data['style'] = $this->cache->check(
        'header_style',
        function () {
          $ret = '';
          $ret .= file_get_contents('catalog/view/theme/lakestone/stylesheet/style.min.css');
          $ret .= file_get_contents('catalog/view/theme/lakestone/stylesheet/stylesheet.min.css');
          return $ret;
        }
    );
    
    //now('styles');
    $data['styles'] = array(); //$this->document->getStyles();
    $styles_cache = $this->cache->check(
        'common_header_styles_' . str_replace('/', '_', $this->request->get['route'] ?? 'common/home'),
        function () {
          $ret = [
              'style' => '',
              'styles' => [],
          ];
          foreach ($this->document->getStyles() as $style) {
            if ($style['rel'] == 'stylesheet' and !preg_match('%(^//)|(https?://)%i', $style['href'])) {
              $ret['style'] .= file_get_contents($style['href']);
            } else {
              $ret['styles'][] = $style;
            }
          }
          return $ret;
        }
    );
    $data['styles'] = $styles_cache['styles'];
    $data['style'] .= $styles_cache['style'];
    
    //now('script');
    $data['script'] = $this->cache->check(
        'header_script',
        function () {
          $ret = '';
          $ret .= file_get_contents('catalog/view/javascript/jquery/jquery-2.1.1.min.js') . ';jQuery211 = jQuery;';
          $ret .= file_get_contents('catalog/view/javascript/bootstrap/js/bootstrap.min.js') . ';';
          $ret .= file_get_contents('catalog/view/theme/lakestone/js/common.min.js') . ';';
          $ret .= file_get_contents('catalog/view/javascript/common.min.js') . ';';
          return $ret;
        }
    );
    
    //now('scripts');
    $data['scripts'] = array(); //$this->document->getScripts();
    $data['script'] .= $this->cache->check(
        'common_header_scripts_' . str_replace('/', '_', $this->request->get['route'] ?? 'common/home'),
        function () {
          $ret = '';
          foreach ($this->document->getScripts() as $script) {
            $ret .= file_get_contents($script) . ';';
          }
          return $ret;
        }
    );

    $data['lang'] = $this->language->get('code');
    $data['direction'] = $this->language->get('direction');
    
    $data['name'] = $this->config->get('config_name');
    
    if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
      $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
    } else {
      $data['logo'] = '';
    }
    
    $this->load->language('common/header');
    
    $data['text_home'] = $this->language->get('text_home');
    
    // Wishlist
    if ($this->customer->isLogged()) {
      $this->load->model('account/wishlist');
      
      $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
    } else {
      $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
    }
    
    $data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
    $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
    
    $data['text_account'] = $this->language->get('text_account');
    $data['text_register'] = $this->language->get('text_register');
    $data['text_login'] = $this->language->get('text_login');
    $data['text_order'] = $this->language->get('text_order');
    $data['text_transaction'] = $this->language->get('text_transaction');
    $data['text_download'] = $this->language->get('text_download');
    $data['text_logout'] = $this->language->get('text_logout');
    $data['text_checkout'] = $this->language->get('text_checkout');
    $data['text_category'] = $this->language->get('text_category');
    $data['text_all'] = $this->language->get('text_all');
    
    $data['home'] = $this->url->link('common/home');
    $data['wishlist'] = $this->url->link('account/wishlist', '', true);
    $data['logged'] = $this->customer->isLogged();
    $data['account'] = $this->url->link('account/account', '', true);
    $data['register'] = $this->url->link('account/register', '', true);
    $data['login'] = $this->url->link('account/login', '', true);
    $data['order'] = $this->url->link('account/order', '', true);
    $data['transaction'] = $this->url->link('account/transaction', '', true);
    $data['download'] = $this->url->link('account/download', '', true);
    $data['logout'] = $this->url->link('account/logout', '', true);
    $data['shopping_cart'] = $this->url->link('checkout/cart');
    $data['checkout'] = $this->url->link('checkout/checkout', '', true);
    $data['contact'] = $this->url->link('information/contact');
    $data['telephone'] = $this->config->get('config_telephone');
    $data['telephone_href'] = str_replace(array(' ', '(', ')', '-'), '', $this->config->get('config_telephone'));
    $data['Locality'] = $this->session->data['Locality'];
    // var_dump($this->session->data['DPoint']);exit;
    if (!isset($this->session->data['DPoint']))
      $this->session->data['DPoint'] = '';
    $data['DPoint'] = $this->session->data['DPoint'];
    // Menu
    $this->load->model('catalog/information');
    
    //now('i0');
    $data['informations'] = array();
    
    foreach ($this->model_catalog_information->getInformations() as $result) {
      if ($result['top']) {
        $data['informations'][] = array(
            'title' => $result['title'],
            'href' => $this->url->link('information/information', 'information_id=' . $result['information_id'])
        );
      }
    }
    
    $this->load->model('catalog/category');
    $this->load->model('catalog/product');
    
    //now('cat0');
    $data['categories'] = $this->cache->get('categories');
    
    if (!$data['categories']) {
      
      $data['categories'] = array();
      
      $categories = $this->model_catalog_category->getCategories(0);
      
      foreach ($categories as $category) {
        
        $href = $this->url->link('product/category', 'path=' . $category['category_id']);
        if ($category['special_tag'] == 'sale')
          $menu_class = 'menu_red';
        else
          $menu_class = false;
        
        if ($category['top']) {
          $ext_menu = array('unnamed' => array());
          $ext_noname = array();
          foreach ($this->model_catalog_category->getCategoryLinks($category['category_id']) as $ext_link) {
            $ext_noname[] = array(
                'name' => $ext_link['name'],
                'href' => $ext_link['href'],
                'sort' => $ext_link['sort_order'],
            );
          }
          if (sizeof($ext_noname) == 0 and $this->model_catalog_category->getTotalCategoriesByCategoryId($category['category_id']) == 0) {
            $data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'href' => $href,
                'mclass' => $menu_class,
            );
            continue;
          }
          foreach ($this->model_catalog_category->getCategories($category['category_id']) as $level2) {
            $ExtLinks = $this->model_catalog_category->getCategoryLinks($level2['category_id']);
            if (sizeof($ExtLinks) == 0 and $this->model_catalog_category->getTotalCategoriesByCategoryId($level2['category_id']) == 0) {
              $ext_noname[] = array(
                  'name' => $level2['name'],
                  'href' => $this->url->link('product/category', 'path=' . $level2['category_id']),
                  'sort' => $level2['sort_order'],
              );
              continue;
            }
            $ext_sub = array();
            foreach ($this->model_catalog_category->getCategories($level2['category_id']) as $level3) {
              $ext_sub[] = array(
                  'name' => $level3['name'],
                  'href' => $this->url->link('product/category', 'path=' . $level3['category_id']),
                  'sort' => $level3['sort_order'],
              );
            }
            foreach ($ExtLinks as $ext_link) {
              $ext_sub[] = array(
                  'name' => $ext_link['name'],
                  'href' => $ext_link['href'],
                  'sort' => $ext_link['sort_order'],
              );
            }
            array_multisort(array_column($ext_sub, 'sort'), SORT_ASC, $ext_sub);
            if (sizeof($ext_sub))
              $ext_menu[$level2['name']] = $ext_sub;
          }
          array_multisort(array_column($ext_noname, 'sort'), SORT_ASC, $ext_noname);
          if (sizeof($ext_noname))
            $ext_menu['unnamed'] = $ext_noname;
          else {
            unset($ext_menu['unnamed']);
          }
          
          $data['categories'][] = array(
              'category_id' => $category['category_id'],
              'name' => $category['name'],
              'ext_menu' => $ext_menu,
              'href' => $href,
              'mclass' => $menu_class,
          );
        }
      }
      $this->cache->set('categories', $data['categories']);
    }
    //now('other');
    #$data['language'] = $this->load->controller('common/language');
    #$data['currency'] = $this->load->controller('common/currency');
    $data['search'] = $this->load->controller('common/search');
    $data['cart'] = $this->load->controller('common/cart');
    //now('loc0');
    $locality_ext = array();
    $data['locality'] = $this->load->controller('common/locality', array(&$locality_ext));
    //now('loc1');
    $data['placed_count'] = $locality_ext['placed_count'];
    // $data['Locality'] = $locality_ext['Locality'];
    $data['pick_point'] = $locality_ext['pick_point'];
    // $data['phone'] = $locality_ext['phone'];
    // $data['phone_comment'] = $locality_ext['phone_comment'];
    // $data['phone_comment_block'] = $locality_ext['phone_comment_block'];
    // $data['client'] = $locality_ext['client'];
    $data['tooltip'] = $locality_ext['tooltip'];
    if ($data['tooltip'])
      $data['LocalityShort'] = $locality_ext['LocalityShort'];
    // $data['delivery_advanced'] = $locality_ext['delivery_advanced'];
    foreach (array('description') as $var) {
      $data[$var] = str_replace('{{phone}}', $this->config->get('config_telephone'), $data[$var]);
    }
    //now('css');
    
    // For page specific css
    if (isset($this->request->get['route'])) {
      if (isset($this->request->get['product_id'])) {
        $class = '-' . $this->request->get['product_id'];
      } elseif (isset($this->request->get['path'])) {
        $class = '-' . $this->request->get['path'];
      } elseif (isset($this->request->get['manufacturer_id'])) {
        $class = '-' . $this->request->get['manufacturer_id'];
      } elseif (isset($this->request->get['information_id'])) {
        $class = '-' . $this->request->get['information_id'];
      } else {
        $class = '';
      }
      
      $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
    } else {
      $data['class'] = 'common-home';
    }
    
    //now('h_end');
    return $this->load->view('common/header', $data);
  }
  
}
