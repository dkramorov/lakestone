<?php

class ControllerProductCategory extends Controller {

  public function index() {
    //now('start');
    $this->load->language('product/category');
    $this->load->model('catalog/review');
    $this->load->model('catalog/category');
    $this->load->model('catalog/product');
    $this->load->model('tool/image');
    $this->load->model('catalog/filter');
    $this->load->model('catalog/seo_link');
    $this->document->addStyle('catalog/view/theme/lakestone/stylesheet/category.min.css');
    // $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
    // $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

    if (isset($this->request->get['filter'])) {
      $filter = $this->request->get['filter'];
    } else {
      $filter = '';
    }

    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = 'p.sort_order';
    }

    if (isset($this->request->get['order'])) {
      $order = $this->request->get['order'];
    } else {
      $order = 'ASC';
    }

    if (isset($this->request->get['page'])) {
      $page = $this->request->get['page'];
    } else {
      $page = 1;
    }

    if (isset($this->request->get['limit'])) {
      $limit = (int) $this->request->get['limit'];
    } else {
      $limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_home'),
        'href' => $this->url->link('common/home')
    );
    $SelfLink = '';

    if (isset($this->request->get['path'])) {
      $url = '';

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      $path = (string) $this->request->get['path'];

      $cat_path = '';

      $parts = explode('_', $path);

      $category_id = (int) array_pop($parts);

      foreach ($parts as $path_id) {
        if (!$cat_path) {
          $cat_path = (int) $path_id;
        } else {
          $cat_path .= '_' . (int) $path_id;
        }

        $category_info = $this->model_catalog_category->getCategory($path_id);

        if ($category_info) {
          $data['breadcrumbs'][] = array(
              'text' => $category_info['name'],
              'href' => $this->url->link('product/category', 'path=' . $cat_path . $url)
          );
        }
      }
    } else if (isset($this->request->get['seo_link_id'])) {
      $seo_data = $this->model_catalog_seo_link->getLink((int) $this->request->get['seo_link_id']);
//      d($this->request->get, $seo_link);
      if ($seo_data) {
        $seo_link_id = (int) $this->request->get['seo_link_id'];
        $path = $category_id = $seo_data['category_id'];
        $seo_data['seo_url'] = $this->url->link('product/category', 'seo_link_id=' . $seo_link_id);
        $filter = $seo_data['filter_tag'];
//        $filter = $seo_link['filter_tag'];
      }
    } else {
      $path = '';
      $category_id = 0;
    }

    $this->document->category_id = $category_id;
    $this->document->category_name = '';
    $category_info = $this->model_catalog_category->getCategory($category_id);

    if ($category_info) {
      //now(0);

      $dt = new DateTime($category_info['date_modified']);
      $this->response->addHeader('Last-Modified: ' . $dt->format('D, j M Y H:i:s') . ' GMT');
      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        try {
          $md = new DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
          if ($dt < $md) {
            header('HTTP/1.0 304 Not Modified');
            exit;
          }
        } catch (Exception $e) {
          $this->log->write($e->getMessage());
        }
      }

      if ($category_info['special_tag'] == 'sale') {
        $this->document->setTitle($category_info['meta_title']);
        $this->document->setDescription($category_info['meta_description']);
      } else {
        $this->document->setDescription($this->getMetaTemplate('config_category_description', $category_info));
        $this->document->setTitle($this->getMetaTemplate('config_category_title', $category_info));
        if ($category_info['meta_title'])
          $this->document->setTitle($category_info['meta_title']);
        if ($category_info['meta_description'])
          $this->document->setDescription($category_info['meta_description']);
      }
      //$this->document->setKeywords($category_info['meta_keyword']);

      $data['heading_title_h1'] = true;
      if ($category_info['tag_h1'])
        $data['heading_title'] = $category_info['tag_h1'];
      else
        $data['heading_title'] = $category_info['name'];
      $this->document->category_name = $category_info['name'];

      $data['text_refine'] = $this->language->get('text_refine');
      $data['text_empty'] = $this->language->get('text_empty');
      $data['text_quantity'] = $this->language->get('text_quantity');
      $data['text_manufacturer'] = $this->language->get('text_manufacturer');
      $data['text_model'] = $this->language->get('text_model');
      $data['text_price'] = $this->language->get('text_price');
      $data['text_tax'] = $this->language->get('text_tax');
      $data['text_points'] = $this->language->get('text_points');
      $data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
      $data['text_sort'] = $this->language->get('text_sort');
      $data['text_limit'] = $this->language->get('text_limit');

      $data['button_cart'] = $this->language->get('button_cart');
      $data['button_wishlist'] = $this->language->get('button_wishlist');
      $data['button_compare'] = $this->language->get('button_compare');
      $data['button_continue'] = $this->language->get('button_continue');
      $data['button_list'] = $this->language->get('button_list');
      $data['button_grid'] = $this->language->get('button_grid');

      // Set the last category breadcrumb
      $data['breadcrumbs'][] = array(
          'text' => $category_info['name'],
          'href' => $this->url->link('product/category', 'path=' . $category_id)
      );
      
      // get subcategories
      $data['sub_categories'] = [];
      if (empty($filter)) {
        foreach ($this->model_catalog_category->getCategories($category_id) as $SubCategory) {
          $image = false;
          $links = [];
          if ($SubCategory['icon']) {
            $image = $this->model_tool_image->resize($SubCategory['icon'], 50, 50);
          }
          foreach ($this->model_catalog_category->getCategoryLinks($SubCategory['category_id']) as $link) {
            $links[] = [
                'name' => $link['name'],
                'href' => $link['href'],
            ];
          }
          foreach ($this->model_catalog_category->getCategories($SubCategory['category_id']) as $sub_category) {
            $links[] = [
                'name' => $sub_category['name'],
                'href' => $this->url->link('product/category', 'path=' . $sub_category['category_id']),
            ];
          }
          array_multisort(array_column($links, 'name'), SORT_ASC, $links);
          $data['sub_categories'][] = [
              'icon' => $image,
              'name' => $SubCategory['name'],
              'href' => false, //$this->url->link('product/category', 'path=' . $SubCategory['category_id']),
              'links' => $links,
          ];
        }
      }

      if ($category_info['image']) {
        $data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get($this->config->get('config_theme') . '_image_category_width'), $this->config->get($this->config->get('config_theme') . '_image_category_height'));
        $data['banner'] = $this->model_tool_image->resize($category_info['image'], 1200, 297);
      } else {
        $data['thumb'] = $data['banner'] = '';
      }

      $data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
      if (preg_match('/<h1/i', $data['description']))
        $data['heading_title_h1'] = false;
      $data['compare'] = $this->url->link('product/compare');

      $url = '';

      /* if (isset($this->request->get['filter'])) {
        $url .= '&filter=' . $this->request->get['filter'];
        } */

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      $data['categories'] = array();

      //$results = $this->model_catalog_category->getCategories($category_id);
      $results = $this->model_catalog_category->getCategories(0);

      foreach ($results as $result) {
        $filter_data = array(
            'filter_category_id' => $result['category_id'],
            'filter_sub_category' => true
        );

        $data['categories'][] = array(
            'active' => $result['category_id'] == $category_id,
            'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
            //		'href' => $this->url->link('product/category', 'path=' . $path . '_' . $result['category_id'] . $url)
            'href' => $this->url->link('product/category', 'path=' . $result['category_id'] . $url)
        );
      }

      $data['products'] = array();

      if (sizeof(explode(',', $filter)) > 1) {
        $this->document->addMeta('ROBOTS', 'NOINDEX, NOFOLLOW');
      }

      $filter = $this->model_catalog_filter->translateFilterTag(
              array(
                  'filter_tags' => explode(',', $filter)
              )
      );

      $filter_data = array(
          'filter_category_id' => $category_id,
          'filter_filter' => $filter,
          'sort' => $sort,
          'order' => $order,
          'start' => ($page - 1) * $limit,
          'limit' => $limit
      );

      if (empty($seo_data)) {
        $seo_data = $this->model_catalog_filter->searchFilter(array(
            'category_id' => $category_id,
            'filter' => $filter,
        ));
      }

      if (is_array($seo_data) and sizeof($seo_data) > 0) {
        if (empty($seo_data['seo_url'])) {
          $url = $this->url->link('product/category', 'seo_link_id=' . $seo_data['seo_link_id']);
          if (!strpos($url, '?')) {
            $this->response->redirect($url, 301);
          }
        }
        $data['description'] = html_entity_decode($seo_data['description'], ENT_QUOTES, 'UTF-8');
        $this->document->setDescription($this->getMetaTemplate('config_category_description', $seo_data));
        $this->document->setTitle($this->getMetaTemplate('config_category_title', $seo_data));
        if ($seo_data['meta_title'])
          $this->document->setTitle($seo_data['meta_title']);
        if ($seo_data['meta_description'])
          $this->document->setDescription($seo_data['meta_description']);
//        //$this->document->setTitle($seo_data['meta_title']);
//        //$this->document->setDescription($seo_data['meta_description']);
//        $this->document->setTitle(sprintf('%s купить в Москве по низкой цене в интернет-магазине Lakestone', $seo_data['name']));
        $data['heading_title'] = $seo_data['name'];
        if ($seo_data['tag_h1'])
          $data['heading_title'] = $seo_data['tag_h1'];
        $this->document->setDescription(sprintf('%s продажа в Москве по низкой цене напрямую от производителя, в монобрендовом интернет-магазине LAKESTONE, удобные формы оплаты и доставки, звоните {{phone}} и заказывайте!', $seo_data['name']));
        $this->document->setKeywords($seo_data['meta_keyword']);
        $this->document->textBanner = $seo_data['name'];
        if (!empty($seo_data['seo_url'])) {
          $this->document->addLink($seo_data['seo_url'], 'canonical');
        } elseif ($seo_data['flen'] == 1) {
          $this->document->addLink($this->url->link('product/category', array(
                      'path' => $path,
                      'filter' => $seo_data['filter_tag'],
                  )), 'canonical');
        } else {
          $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], true), 'canonical');
        }
      } else {
        //$this->document->addLink($this->url->link('product/category', 'path=' . $path), 'canonical');
        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], true), 'canonical');
      }

      switch ($category_info['special_tag']) {
        case 'sale':
          $filter_data['filter_category_id'] = 0;
          $filter_data['filter_special'] = true;
          break;
      }

      // create filter tags
      $seo_links = $this->model_catalog_seo_link->getCategoryLinks($category_id);
      $data['filter_tags'] = [];
      foreach ($seo_links as $link) {
        $data['filter_tags'][] = [
            'name' => $link['name'],
            'href' => $this->url->link('product/category', 'seo_link_id=' . $link['seo_link_id']),
        ];
      }
      // $data['filter_tags'] = [];

      // build products list
      //now('products start');
      // check cache
      $filter_hash = md5(json_encode($filter_data));
      $cache_name = 'category_' . $filter_hash;
      if (
          $this->config->get('cache_out')
          and $products_cache = $this->cache->get($cache_name)
      ) {
        
        $data['setting'] = $products_cache['setting'];
        $data['products'] = $products_cache['products'];
        $data['product_total'] = $products_cache['product_total'];
        
      } else {
        
        //region build data for cache
        $data['product_total'] = $this->model_catalog_product->getTotalProducts($filter_data);
        $results = $this->model_catalog_product->getProducts($filter_data);
  
        $data['setting'] = array();
        $data['setting']['width'] = $this->config->get($this->config->get('config_theme') . '_image_product_width');
        $data['setting']['height'] = $this->config->get($this->config->get('config_theme') . '_image_product_height');
  
        if (sizeof($results) == 0) {
          $this->document->addMeta('robots', 'noindex,nofollow');
        }
  
        $LocaleRU = new Locality_RU();
  
        foreach ($results as $result) {
          $product_images = $this->model_catalog_product->getProductImages($result['product_id']);
          $images = array();
          foreach ($product_images as $product_image) {
            if ($product_image['position'] == 1) {
              $images[0] = $this->model_tool_image->resize($product_image['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            }
            if ($product_image['position'] == 2) {
              $images[1] = $this->model_tool_image->resize($product_image['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            }
          }
          if (!isset($images[0])) {
            if ($result['image']) {
              $images[0] = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            } else {
              $images[0] = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            }
          }
          if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
          } else {
            $price = false;
          }
    
          if ((float) $result['special']) {
            $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            $sale = sprintf('%2d%%', 100 - ($result['special'] * 100 / $result['price']));
          } else {
            $special = false;
            $sale = false;
          }
    
          if ($this->config->get('config_tax')) {
            $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
          } else {
            $tax = false;
          }
    
          if ($this->config->get('config_review_status')) {
            $rating = (int) $result['rating'];
          } else {
            $rating = false;
          }
    
          $need_attributes_name = array(
              'Внешние размеры, см' => 'Размеры',
              'Размеры' => 'Размеры',
              'Ширина ремня, см' => 'Ширина ремня',
          );
    
          $attributes = array();
          foreach ($this->model_catalog_product->getProductAttributes($result['product_id']) as $attribute_group) {
            if ($attribute_group['name'][0] == '_')
              continue;
            foreach ($attribute_group['attribute'] as $attribute) {
              foreach ($need_attributes_name as $name => $view) {
                if ($name == $attribute['name']) {
                  $attributes[] = array(
                      'name' => (empty($view) ? $name : $view),
                      'text' => $attribute['text']
                  );
                }
              }
            }
          }
    
          $data['products'][] = array(
              'product_id' => $result['product_id'],
              'thumb' => $images[0],
              'images' => $images,
              'name' => $result['name'],
            //'short'				=> strip_tags(html_entity_decode($result['short'], ENT_QUOTES, 'UTF-8')),
              'short' => html_entity_decode($result['short'], ENT_QUOTES, 'UTF-8'),
              'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
              'price' => $price,
              'special' => $special,
              'tax' => $tax,
              'sale' => $sale,
              'sku' => $result['model'],
              'quantity' => $result['quantity'],
              'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
              'rating' => $rating,
              'reviews_num' => (int) $result['reviews'],
              'reviews' => ' отзыв' . $LocaleRU->num_ending((int) $result['reviews']),
              'attributes' => $attributes,
              'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
            //'href'        => $this->url->link('product/product', 'path=' . $path . '&product_id=' . $result['product_id'] . $url)
          );
        }
        //endregion
        if ($this->config->get('cache_out')) {
          $this->cache->set($cache_name, [
              'setting' => $data['setting'],
              'products' => $data['products'],
              'product_total' => $data['product_total'],
          ]);
        }
      }

      //now('products end');

      $url = '';

      if (isset($this->request->get['filter'])) {
        $url .= '&filter=' . $this->request->get['filter'];
      }

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      if (!empty($seo_link_id)) {
        $url .= '&seo_link_id=' . $seo_link_id;
      } else {
        $url .= '&path=' . $path;
      }

      $data['sorts'] = array();

      $data['sorts'][] = array(
          'text' => $this->language->get('text_default'),
          'value' => 'p.sort_order-ASC',
          'href' => $this->url->link('product/category', '&sort=p.sort_order&order=ASC' . $url)
      );

      $data['sorts'][] = array(
          'text' => $this->language->get('text_name_asc'),
          'value' => 'pd.name-ASC',
          'href' => $this->url->link('product/category', '&sort=pd.name&order=ASC' . $url)
      );

      $data['sorts'][] = array(
          'text' => $this->language->get('text_name_desc'),
          'value' => 'pd.name-DESC',
          'href' => $this->url->link('product/category', '&sort=pd.name&order=DESC' . $url)
      );

      $data['sorts'][] = array(
          'text' => $this->language->get('text_price_asc'),
          'value' => 'p.price-ASC',
          'href' => $this->url->link('product/category', '&sort=p.price&order=ASC' . $url)
      );

      $data['sorts'][] = array(
          'text' => $this->language->get('text_price_desc'),
          'value' => 'p.price-DESC',
          'href' => $this->url->link('product/category', '&sort=p.price&order=DESC' . $url)
      );

      if ($this->config->get('config_review_status')) {
        $data['sorts'][] = array(
            'text' => $this->language->get('text_rating_desc'),
            'value' => 'rating-DESC',
            'href' => $this->url->link('product/category', '&sort=rating&order=DESC' . $url)
        );

        $data['sorts'][] = array(
            'text' => $this->language->get('text_rating_asc'),
            'value' => 'rating-ASC',
            'href' => $this->url->link('product/category', '&sort=rating&order=ASC' . $url)
        );
      }

      $data['sorts'][] = array(
          'text' => $this->language->get('text_model_asc'),
          'value' => 'p.model-ASC',
          'href' => $this->url->link('product/category', '&sort=p.model&order=ASC' . $url)
      );

      $data['sorts'][] = array(
          'text' => $this->language->get('text_model_desc'),
          'value' => 'p.model-DESC',
          'href' => $this->url->link('product/category', '&sort=p.model&order=DESC' . $url)
      );

      $url = '';

      if (isset($this->request->get['filter'])) {
        $url .= '&filter=' . $this->request->get['filter'];
      }

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      if (!empty($seo_link_id)) {
        $url .= '&seo_link_id=' . $seo_link_id;
      } else {
        $url .= '&path=' . $path;
      }

      $pagination = new Pagination();
      $pagination->total = $data['product_total'];
      $pagination->page = $page;
      $pagination->limit = $limit;
      $pagination->url = $this->url->link('product/category', $url . '&page={page}');
      $data['pagination'] = $pagination->render();

      $data['sort'] = $sort;
      $data['order'] = $order;
      $data['limit'] = $limit;
      $data['continue'] = $this->url->link('common/home');

      //now('review');
      $data['reviews'] = $this->cache->check(
          'category_reviews_' . $category_id,
          function() use ($category_id) {
            $ret = [];
            foreach ($this->model_catalog_review->getReviewsByCategoryId($category_id, 0, 3) as $review) {
              $product = $this->model_catalog_product->getProduct($review['product_id']);
              $ret[] = array(
                  'name' => $review['author'],
                  'rating' => $review['rating'],
                  'text' => nl2br($review['text']),
                  'date' => date($this->language->get('date_format_short'), strtotime($review['date_added'])),
                  'product' => $product['name'],
                  'href' => $this->url->link('product/product', array('product_id' => $review['product_id'])),
              );
            }
            return $ret;
          }
      );
      
      //now('modules');
      $data['column_left'] = $this->cache->check(
          'common_left_category',
          function() {
            return $this->load->controller('common/column_left');
          }
      );
      $data['column_right'] = $this->cache->check(
          'common_right_category',
          function() {
            return $this->load->controller('common/column_right');
          }
      );
      $data['content_top'] = $this->cache->check(
          'common_top_category',
          function() {
            return $this->load->controller('common/content_top');
          }
      );
      $data['content_bottom'] = $this->cache->check(
          'common_bottom_category',
          function() {
            return $this->load->controller('common/content_bottom');
          }
      );
      $this->document->addStyle('catalog/view/theme/lakestone/stylesheet/filter.min.css');
      $data['content_filter'] = $this->cache->check(
          'category_filter_' . $filter_hash,
          function() {
            return $this->load->controller('extension/module/filter');
          }
      );
      $data['footer'] = $this->cache->check(
          'common_footer_category',
          function() {
            return $this->load->controller('common/footer');
          }
      );
      $data['header'] = $this->load->controller('common/header');
      
//      now('end');

      $this->response->setOutput($this->load->view('product/category', $data));
    
    } else {
      
      // 404 - not found
      $url = '';

      if (isset($this->request->get['path'])) {
        $url .= '&path=' . $this->request->get['path'];
      }

      if (isset($this->request->get['filter'])) {
        $url .= '&filter=' . $this->request->get['filter'];
      }

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['page'])) {
        $url .= '&page=' . $this->request->get['page'];
      }

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      $data['breadcrumbs'][] = array(
          'text' => $this->language->get('text_error'),
          'href' => $this->url->link('product/category', $url)
      );

      $this->document->setTitle($this->language->get('text_error'));

      $data['heading_title'] = $this->language->get('text_error');

      $data['text_error'] = $this->language->get('text_error');

      $data['button_continue'] = $this->language->get('button_continue');

      $data['continue'] = $this->url->link('common/home');

      $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

      $data['column_left'] = $this->load->controller('common/column_left');
      $data['column_right'] = $this->load->controller('common/column_right');
      $data['content_top'] = $this->load->controller('common/content_top');
      $data['content_bottom'] = $this->load->controller('common/content_bottom');
      $data['content_filter'] = $this->load->controller('extension/module/filter');
      $data['footer'] = $this->load->controller('common/footer');
      $data['header'] = $this->load->controller('common/header');

      $this->response->setOutput($this->load->view('error/not_found', $data));
    }
  }

  private function getMetaTemplate($template_name, array $category_info) {
    $template = $this->config->get($template_name);
    if (empty($template))
      return $category_info['name'];
    $template = str_replace('{phone}', $this->config->get('config_telephone'), $template);
    $template = str_replace('{category_name}', $category_info['name'], $template);
    return strip_tags($template);
  }

}
