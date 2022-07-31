<?php

class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
	  //now(0);
		$this->load->language('product/product');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');
		$this->document->addScript('catalog/view/theme/lakestone/js/product.min.js');
//		$this->document->addScript('catalog/view/theme/lakestone/js/product.js');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/owl_product_list.min.css');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/product.min.css');

		$need_attributes_name = array(
			'Материал' => 'Материал',
			'Цвет' => 'Цвет',
			'Внешние размеры, см' => array('view' => 'Внешние размеры, см'),
			'Ширина ремня, см' => array('view' => 'Ширина ремня'),
		);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');
		$this->load->model('extension/module');

		$data['action'] = $this->url->link('checkout/cart/add');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

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
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

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

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');
		// $data['categories'] = array();

		$product_info = $this->model_catalog_product->getProduct($product_id, false);
		$product_categories = $this->model_catalog_product->getCategories($product_id);

		$url = '';
		$category_id = 0;
		foreach ($product_categories as $product_category) {
			if ($product_category['category_id'] == 59)
				continue;
			foreach ($this->model_catalog_category->getCategoryPath($product_category['category_id']) as $PathCategory) {
				$product_category_info = $this->model_catalog_category->getCategory($PathCategory['path_id']);
				// if ($PathCategory['path_id'] == $PathCategory['category_id']) {
				// 	$data['categories'][] = $product_category_info['name'];
				// 	if (file_exists(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/template/product/blockbuster_addon_cat' . $product_category['category_id'] . '.tpl')) {
				// 		$data['blockbuster'] = $this->load->view('product/blockbuster_addon_cat' . $product_category['category_id'] . '.tpl', $data);
				// 	}
				// }
        if (!empty($product_category_info)) {
          $data['breadcrumbs'][] = array(
              'text' => ($product_category_info['name']),
              'href' => $this->url->link('product/category', 'path=' . $product_category['category_id'] . $url)
          );
        }
			}
			$category_id = $product_category['category_id'];
			break;
		}

		$dt = new DateTime($product_info['date_modified']);
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

		if ($product_info) {
		  //now('start');
		  $product_cache = $this->cache->get('product_' . $product_id);
			$url = '';

			$this->load->model('tool/image');
      $this->load->model('catalog/review');
      
      $this->document->product_id = $product_id;
			if (isset($product_category['category_id']))
				$this->document->category_id = $product_category['category_id'];
			else
				$this->document->category_id = 0;
			$this->document->price = sprintf('%0d', $product_info['price']);
			$this->document->name = $product_info['name'];
			$this->document->sku = $product_info['sku'];
			$this->document->url = $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']);

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);
			$this->document->setTitle($this->getMetaTemplate('config_product_title', $product_info));
			$this->document->setDescription($this->getMetaTemplate('config_product_description', $product_info));
			if ($product_info['meta_title'])
				$this->document->setTitle($product_info['meta_title']);
			if ($product_info['meta_description'])
				$this->document->setDescription($product_info['meta_description']);
			//$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			// $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			// $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.min.css');
			//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.min.js');
			//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			//$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			$data['heading_title'] = $product_info['name'];

			$data['text_select'] = $this->language->get('text_select');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_reward'] = $this->language->get('text_reward');
			$data['text_points'] = $this->language->get('text_points');
			$data['text_stock'] = $this->language->get('text_stock');
			$data['text_discount'] = $this->language->get('text_discount');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_option'] = $this->language->get('text_option');
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_write'] = $this->language->get('text_write');
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
			$data['text_note'] = $this->language->get('text_note');
			$data['text_tags'] = $this->language->get('text_tags');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['text_loading'] = $this->language->get('text_loading');
			$data['telephone'] = $this->config->get('config_telephone');
			$data['telephone_href'] = str_replace(array(' ', '(', ')', '-'), '', $this->config->get('config_telephone'));
			$data['email'] = $this->config->get('config_email');

			$data['entry_qty'] = $this->language->get('entry_qty');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_review'] = $this->language->get('entry_review');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_rating_photo'] = $this->language->get('entry_rating_photo');
			$data['entry_rating_description'] = $this->language->get('entry_rating_description');
			$data['entry_good'] = $this->language->get('entry_good');
			$data['entry_bad'] = $this->language->get('entry_bad');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_upload'] = $this->language->get('button_upload');
			$data['button_continue'] = $this->language->get('button_continue');

			$data['tab_description'] = $this->language->get('tab_description');
			$data['tab_attribute'] = $this->language->get('tab_attribute');
			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['status'] = $product_info['status'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			$this->session->data['UploadToken'] = random_bytes(32);
			$data['UploadToken'] = bin2hex($this->session->data['UploadToken']);
			$data['ReviewFiles'] = json_encode($this->config->get('review_files'));

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = '<span class="text-danger">' . $product_info['stock_status'] . '</span>';
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = '<span class="text-success">' . $this->language->get('text_instock') . '</span>';
			}
			$data['quantity'] = $product_info['quantity'];
      
      // build images
      //now('i0');
      if (!empty($product_cache['popup'])) {
        $data['popup'] = $product_cache['popup'];
      } else {
        $this->document->image = $data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
      }
      if (!empty($product_cache['images'])) {
        $data['images'] = $product_cache['images'];
      } else {
        $data['images'] = array();
        $image_counter = 9;
        if ($product_info['image']) {
          $image_counter--;
          $data['images'][] = array(
              'video' => false,
              'popup' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
              'big' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height')),
              'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
          );
        }
        $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
        foreach ($results as $result) {
          if ($result['position'] == 99) continue;
          if ($this->model_tool_image->is_image($result['image'])) {
            $data['images'][] = array(
                'video' => false,
                'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
                'big' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height')),
                'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
            );
          } elseif ($this->model_tool_image->is_video($result['image'])) {
            $data['images'][] = [
                'video' => true,
                'href' => $this->model_tool_image->getUrl($result['image']),
                'preview' => ($this->model_tool_image->is_image($result['image'] . '.jpg') ? $this->model_tool_image->resize($result['image'] . '.jpg', $this->config->get($this->config->get('config_theme') . '_image_additional_height'), $this->config->get($this->config->get('config_theme') . '_image_additional_height')) : false),
            ];
          }
          if (!$image_counter--) break;
        }
      }

			// Other parameters
			$data['currency'] = $this->session->data['currency'];
			$data['ean'] = $product_info['ean'] ?? '';
			$data['mnp'] = $product_info['mnp'] ?? '';
			$data['brand'] = 'Lakestone';
			$data['product_href'] = $this->url->link('product/product', 'product_id=' . $product_id);

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$data['price_num'] = sprintf('%0d', $product_info['price']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				$data['price_num'] = sprintf('%0d', $product_info['special']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			//now('loc0');
			$data['Locality'] = $this->session->data['Locality'];
			$data['ShowRoomBanner'] = $this->load->controller('extension/module/banner', $this->model_extension_module->getModuleByName('Посетите наш Шоу-Рум'));

			//now('rev0');
      $data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			// var_dump($product_info);

			$this->load->model('catalog/screen');

			//now('rev1');
			$data['screens_count'] = $this->model_catalog_screen->getTotalScreeensByProductId($product_id);

			//now('rev2');
			$LocaleRU = new Locality_RU();
			//$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			if ($product_info['reviews'])
				$data['reviews_count'] = $product_info['reviews'];
			else
				$data['reviews_count'] = '0';
			if ($product_info['questions'])
				$data['questions_count'] = $product_info['questions'];
			else
				$data['questions_count'] = '0';
			$data['reviews_num'] = (int)$product_info['reviews'];
			$data['reviews'] = ' отзыв' . $LocaleRU->num_ending((int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];
			$data['rating_float'] = sprintf('%0.1f', $product_info['rating']);
			$data['review_write_href'] = $data['review_href'] = $this->url->link('product/product', 'product_id=' . $product_id);
			$data['review_like_href'] = $this->url->link('product/product/like');
			$data['review_unlike_href'] = $this->url->link('product/product/unlike');
			$data['reviews_array'] = $this->model_catalog_review->getReviewsByProductId($product_id, 0, 10, 0);
//			dd($data['reviews_array'], $product_id);

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			// build attributes
      //now('attr0');
      if (!empty($product_cache['attributes'])) {
        $data['attributes'] = $product_cache['attributes'];
      } else {
        $data['attributes'] = array();
        foreach ($this->model_catalog_product->getProductAttributes($this->request->get['product_id']) as $attribute_group) {
          if ($attribute_group['name'][0] == '_')
            continue;
          foreach ($attribute_group['attribute'] as $attribute) {
            foreach ($need_attributes_name as $name => $attr_fixes) {
              if ($name == $attribute['name']) {
                $attr = array(
                    'name' => $attribute['name'],
                    'text' => $attribute['text'],
                    'class' => '',
                );
                if (isset($attr_fixes['view'])) $attr['name'] = $attr_fixes['view'];
                if (isset($attr_fixes['class'])) $attr['class'] = $attr_fixes['class'];
                $data['attributes'][] = $attr;
              }
            }
          }
        }
      }

			// build colors and similar
      //now('col0');
			if (!empty($product_cache['colors'])) {
			  $data['colors'] = $product_cache['colors'];
			  $data['similar'] = $product_cache['similar'] ?? false;
      } else {
        $limit = 20;
        $similar = array();
        $similar_numbers = array();
        $data['colors'] = array();
        $data['colors'][] = array(
            'product_id' => $product_id,
            'name' => $product_info['name'],
            'href' => $this->url->link('product/product', '&product_id=' . $product_id),
            'image' => $this->getImages($product_id, 0)
        );
        foreach ($this->model_catalog_product->getProductRelated($product_id) as $result) {
          if ($result['product_id'] == $product_id) continue;
          $data['colors'][] = array(
              'product_id' => $result['product_id'],
              'name' => $result['name'],
              'href' => $this->url->link('product/product', '&product_id=' . $result['product_id']),
              'image' => $this->getImages($result['product_id'], 0)
          );
          $similar[] = $this->buildProduct($result);
          $similar_numbers[] = $result['product_id'];
        }
        array_multisort(array_column($data['colors'], 'name'), SORT_ASC, $data['colors']);
        $limit -= sizeof($similar);
        $price = $product_info['price'];
        if ((float)$product_info['special']) {
          $price = $product_info['special'];
        }
        /*** add this to similar ***/
        //now('col1');
        $price_period = 1000;
        foreach ($this->model_catalog_product->getProducts(array('filter_category_id' => $category_id, 'start' => 0, 'limit' => $limit)) as $result) {
          if ($result['product_id'] == $product_id) continue;
          if (in_array($result['product_id'], $similar_numbers)) continue;
          if ((float)$result['special']) {
            if (abs($price - $result['special']) > $price_period) continue;
          } else {
            if (abs($price - $result['price']) > $price_period) continue;
          }
          if ($limit-- <= 0) break;
          $similar[] = $this->buildProduct($result);
          $similar_numbers[] = $result['product_id'];
        }
        /** to shuffle array here **/
        // shuffle($similar);
        if (!empty($similar)) {
          $data['similar'] = $this->load->view('extension/module/owl_product_list', array(
              'module' => '_similar',
              'products' => $similar,
              'heading_title' => 'Похожие товары',
              'setting' => array('height' => $this->config->get($this->config->get('config_theme') . '_image_related_height')),
          ));
        } else {
          $data['similar'] = false;
        }
      }

			// build profitable_set
			//now('acc0');
			if (isset($product_cache['profitable_set'])) {
        $data['profitable_set'] = $product_cache['profitable_set'];
        $data['profitable_name'] = $product_cache['profitable_name'] ?? 'аксессуаров';
        $data['profitable_href'] = $product_cache['profitable_href'] ?? '/accessories';
      } else {
        $profitable_set = array();
        $data['profitable_set'] = false;
        $data['profitable_name'] = 'аксессуаров';
        $data['profitable_href'] = '/accessories';
        $limit = 20;
        foreach ($this->model_catalog_category->findCategories(array('tag' => 'profitable_set')) as $category) {
          $data['profitable_href'] = $this->url->link('product/category', 'path=' . $category['category_id']);
          $data['profitable_name'] = $category['name'];
          if ($limit <= 0) break;
          foreach ($this->model_catalog_product->getProducts(array('filter_category_id' => $category['category_id'], 'start' => 0, 'limit' => $limit)) as $result) {
            if ($limit-- <= 0) break;
            $profitable_set[] = $this->buildProduct($result);
          }
        }
        if (!empty($profitable_set)) {
          $data['profitable_set'] = $this->load->view('extension/module/owl_product_list', array(
              'module' => '_profitable_set',
              'products' => $profitable_set,
              'heading_title' => false,
              'setting' => array('height' => $this->config->get($this->config->get('config_theme') . '_image_related_height')),
          ));
        } else {
          $data['profitable_set'] = false;
        }
      }
			
			//now('tag0');
			$data['tags'] = array();
			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);
				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag' => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			//now('ban0');
			$data['banner1'] = $this->load->controller('extension/module/banner', $this->model_extension_module->getModuleByName('Преимущества товара'));
			//now('tab0');
			$data['TabDescription'] = $this->getTabDescription($this->request->get['product_id'], $category_id);
			//now('del0');
			$data['product_delivery'] = $this->loadDelivery(true);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			//now('block_left');
			$data['column_left'] = $product_cache['column_left'] ?? $this->load->controller('common/column_left');
			//now('block_right');
			$data['column_right'] = $product_cache['column_right'] ?? $this->load->controller('common/column_right');
      //now('block_top');
			$data['content_top'] = $this->load->controller('common/content_top');

			//now('ban1');
			$data['banner_schedule'] = '';
			if (strpos($data['content_top'], 'banner_schedule') !== false) {
				$TopXML = new DOMDocument('1.0', 'UTF-8');
				if ($TopXML->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $data['content_top'], LIBXML_HTML_NODEFDTD)) {
					$TopXP = new DOMXPath($TopXML);
					$BannerScheduleNL = $TopXP->query('//div[@class="banner_schedule"]');
					if ($BannerScheduleNL->length > 0) {
						$data['banner_schedule'] = $TopXML->saveHTML($BannerScheduleNL->item(0));
						$BannerScheduleNL->item(0)->parentNode->removeChild($BannerScheduleNL->item(0));
						$data['content_top'] = '';
						foreach ($TopXML->getElementsByTagName('body')->item(0)->childNodes as $node) {
							$data['content_top'] .= $TopXML->saveHTML($node);
						}
					}
				}
			}


			//now('acc_link');
			// Получаем линки для аксессуаров
      $data['accessoriesLinks'] = $this->getCache(
          'accessories_links_' . $category_id,
          function () use ($category_id) {
            return $this->model_catalog_product->getAccessoriesLinks($category_id);
          }
      );
			
//      now('block_bottom');
      $data['content_bottom'] = $this->getCache(
          'common_bottom_product',
          function () {
            return $this->load->controller('common/content_bottom');
          }
      );
//			now('footer');
      $data['footer'] = $this->getCache(
          'common_footer_product',
          function () {
            return $this->load->controller('common/footer');
          }
      );
//			now('header');
      $data['header'] = $this->load->controller('common/header');
//      $data['header'] = $this->getCache(
//          'common_header_product',
//          function () {
//            return $this->load->controller('common/header');
//          }
//      );
			
			// save cache
//      now('save cache');
      if (!$product_cache) {
        $product_cache = $data;
        $this->cache->set('product_' . $product_id, $product_cache);
      }
//			now('end');

			$this->response->setOutput($this->load->view('product/product', $data));

		} else {
		  
		  // 404 - product not found
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
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
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	private function getMetaTemplate($template_name, array $product_info) {
		$template = $this->config->get($template_name);
		if (empty($template)) return $product_info['name'];
		$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		$template = str_replace('{phone}', $this->config->get('config_telephone'), $template);
		$template = str_replace('{product_name}', $product_info['name'], $template);
		$template = str_replace('{product_price}', $price, $template);
		$template = str_replace('{product_special}', $special, $template);
		$template = str_replace('{product_actual}', ((float)$product_info['special'] ? $special : $price), $template);
		return strip_tags($template);
	}

	private function getImages($product_id, $count = 100) {
		$images = array();
		$img_width = $this->config->get($this->config->get('config_theme') . '_image_thumb_width');
		$img_height = $this->config->get($this->config->get('config_theme') . '_image_thumb_height');
		$th_width = $this->config->get($this->config->get('config_theme') . '_image_additional_width');
		$th_height = $this->config->get($this->config->get('config_theme') . '_image_additional_height');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		if ($product_info and $product_info['image'] and $this->model_tool_image->is_image($product_info['image'])) {
			$images[] = array(
				'thumb' => $this->model_tool_image->resize($product_info['image'], $th_width, $th_height),
				'image' => $this->model_tool_image->resize($product_info['image'], $img_width, $img_height),
				'popup' => $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
			);
		}
		foreach ($this->model_catalog_product->getProductImages($product_id) as $image) {
			if (!$this->model_tool_image->is_image($image['image'])) continue;
			if ($image['position'] == 99) continue;
			if ($count-- <= 0)
				break;
			$images[] = array(
				'thumb' => $this->model_tool_image->resize($image['image'], $th_width, $th_height),
				'image' => $this->model_tool_image->resize($image['image'], $img_width, $img_height),
				'popup' => $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
			);
		}
		if (!$images) {
			$images = array(array(
				'thumb' => $this->model_tool_image->resize('no_image.png', $th_width, $th_height),
				'image' => $this->model_tool_image->resize('no_image.png', $img_width, $img_height),
			));
		}
		return $images;
	}

	private function buildProduct($product_source) {
		$this->load->model('tool/image');
		$LocaleRU = new Locality_RU();
		$result = array();
		$images = array();
		if ($product_source['image']) {
			$images[0] = $this->model_tool_image->resize($product_source['image'], $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
		} else {
			$images[0] = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
		}

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$price = $this->currency->format($this->tax->calculate($product_source['price'], $product_source['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		} else {
			$price = false;
		}

		if ((float)$product_source['special']) {
			$special = $this->currency->format($this->tax->calculate($product_source['special'], $product_source['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			$sale = sprintf('%2d%%', 100 - ($product_source['special'] * 100 / $product_source['price']));
		} else {
			$special = false;
			$sale = false;
		}

		if ($this->config->get('config_tax')) {
			$tax = $this->currency->format((float)$product_source['special'] ? $product_source['special'] : $product_source['price'], $this->session->data['currency']);
		} else {
			$tax = false;
		}

		if ($this->config->get('config_review_status')) {
			$rating = $product_source['rating'];
		} else {
			$rating = false;
		}

		$result = array(
			'product_id' => $product_source['product_id'],
			'thumb' => $images[0],
			'images' => $images,
			'name' => $product_source['name'],
			'short' => html_entity_decode($product_source['short'], ENT_QUOTES, 'UTF-8'),
			'description' => utf8_substr(strip_tags(html_entity_decode($product_source['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
			'price' => $price,
			'special' => $special,
			'tax' => $tax,
			'sale' => $sale,
			'rating' => $rating,
			'reviews_num' => (int)$product_source['reviews'],
			'reviews' => ' отзыв' . $LocaleRU->num_ending((int)$product_source['reviews']),
			'href' => $this->url->link('product/product', 'product_id=' . $product_source['product_id'])
		);

		return $result;

	}

	private function getTabDescription($product_id, $category_id) {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		$need_attributes_name = array(
			// 'Вместимость документов' => array('class' => 'blue'),
			// 'Вместимость ноутбука' => array('class' => 'blue'),
		);

		$data = array();
		$data['add_links'] = $this->model_catalog_product->getProductLinks($product_id);
		if (empty($data['add_links']))
			$data['add_links'] = $this->model_catalog_category->getCategoryProductLinks($category_id);

		$product_info = $this->model_catalog_product->getProduct($product_id);
		// var_dump($product_info, $product_id);
		if ($product_info) {
			$data['attributes'] = array();
			$data['fullDescription'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			foreach ($this->model_catalog_product->getProductAttributes($product_id) as $attribute_group) {
				if ($attribute_group['name'][0] == '_')
					continue;
				foreach ($attribute_group['attribute'] as $attribute) {
					$attr = array(
						'name' => $attribute['name'],
						'value' => $attribute['text'],
						'class' => '',
					);
					foreach ($need_attributes_name as $name => $attr_fixes) {
						if ($name == $attribute['name']) {
							if (isset($attr_fixes['view'])) $attr['name'] = $attr_fixes['view'];
							if (isset($attr_fixes['class'])) $attr['class'] = $attr_fixes['class'];
						}
					}
					$attr['name'] .= ':';
					$data['attributes'][] = $attr;
				}
			}
			return $this->load->view('product/tab_description', $data);
		} else {
			return '<div>Ошибка. Продукт не найден.</div>';
		}

	}

	public function loadDelivery($local = false) {
		$data = array();
		$LocaleRU = new Locality_RU();
		$MoscowTZ = new DateTimeZone('Europe/Moscow');
		$NOW = new DateTime('now', $MoscowTZ);
		$data['Locality'] = $this->session->data['Locality'];
		$data['courier'] = $data['pickpoint'] = $data['post'] = true;
		$data['courier_cond'] = $data['pickpoint_cond'] = $data['post_cond'] = 'бесплатно';
		$data['courier_cont'] = 'до 2 дней';
		$data['pickpoint_cont'] = 'от 3 до 5 дней';
		$data['post_cont'] = 'от 3 до 7 дней';
		switch ($this->session->data['Locality']) {
			case 'г. Москва':
				$data['post'] = false;
				$data['pickpoint_cont'] = '1-2 дня';
				if ($NOW < new DateTime('12:00', $MoscowTZ)) {
					$data['courier_cont'] = 'сегодня';
				} else {
					$data['courier_cont'] = 'завтра';
				}
				break;
			case 'г. Санкт-Петербург':
				$data['post'] = false;
				$data['pickpoint_cont'] = '1-2 дня';
				if ($NOW < new DateTime('15:30', $MoscowTZ)) {
					$data['courier_cont'] = 'завтра';
				} else {
					$data['courier_cont'] = '1-2 дня';
				}
				break;
			default:
				$data['courier_cond'] = '390 рублей';
				if (isset($this->session->data['LocalityCodes']) and isset($this->session->data['LocalityCodes']['boxberry'])) {
					$this->load->model('extension/shipping/boxberry');
					$boxberry_delivery = $this->model_extension_shipping_boxberry->calcDelivery($this->session->data['LocalityCodes']['boxberry']);
					if ($boxberry_delivery === false) {
						$data['pickpoint'] = false;
					} else {
						$data['pickpoint_cont'] = sprintf(
							'%d %s',
							$boxberry_delivery + 1,
							$LocaleRU->days((int)$boxberry_delivery + 1)
						);
					}
				} else {
					$data['pickpoint'] = false;
				}
				if (isset($this->session->data['LocalityCodes']) and isset($this->session->data['LocalityCodes']['cdek'])) {
					$this->load->model('extension/shipping/cdek');
					$cdek_delivery = $this->model_extension_shipping_cdek->calcDelivery($this->session->data['LocalityCodes']['cdek'], 137);
					if (empty($cdek_delivery)) {
						$data['courier'] = false;
					} else {
						$data['courier_cont'] = sprintf(
							'%d %s',
							$cdek_delivery['deliveryPeriodMax'],
							$LocaleRU->days((int)$cdek_delivery['deliveryPeriodMax'])
						);
					}
					// if ($data['pickpoint'] === false) {
					// 	$cdek_delivery = $this->model_extension_shipping_cdek->calcDelivery($this->session->data['LocalityCodes']['cdek'], 136);
					// 	if (!empty($cdek_delivery)) {
					//		$data['pickpoint'] = true;
					// 		$data['pickpoint_cont'] = sprintf(
					// 			'%d %s',
					// 			$cdek_delivery['deliveryPeriodMax'],
					// 			$LocaleRU->days((int) $cdek_delivery['deliveryPeriodMax'])
					// 		);
					// 	}
					// }
				} else {
					$data['courier_cond'] = '';
					$data['courier_cont'] = 'по запросу';
				}
				break;
		}
		$result = $this->load->view('product/product_delivery', $data);
		if ($local)
			return $result;
		$this->response->setOutput($result);
	}

	public function review_rules() {
		$this->response->setOutput($this->load->view('product/review_rules'));
	}

	public function screens() {
		$this->load->language('product/product');
		$this->load->model('catalog/screen');
		$this->load->model('tool/image');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 8;

		//$screen_total = $this->model_catalog_screen->getTotalScreeensByProductId($this->request->get['product_id']);

		//$results = $this->model_catalog_screen->getScreensByProductId($this->request->get['product_id'], ($page - 1) * $limit, $limit);

		$results = $this->model_catalog_screen->getScreensByProductId($this->request->get['product_id']);

		$data['screens'] = [];

		foreach ($results as $v) {
			$data['screens'][] = [
				'screen' => $v['screen'],
				'screen_id' => $v['screen_id'],
				'author_name' => $v['author_name'],
				'date_screen' => $v['date_screen'],
				'thumb' => '/image/' . $v['screen']//$this->model_tool_image->resize($v['screen'], 288, 1000)
			];
		}

		$this->response->setOutput($this->load->view('product/screen', $data));
	}

	public function reviews() {
		return $this->review();
	}

	private function review($type = 0) {
		$this->load->language('product/product');

		$this->load->model('catalog/review');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');
		if ($type == 1)
			$data['text_no_reviews'] = $this->language->get('text_no_questions');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 5;

		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		if (!empty($product_info)) {
			$data['product_name'] = $product_info['name'];
			$data['product_image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
			$data['product_href'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);
		}
		$data['like_href'] = $this->url->link('product/product/like', 'product_id=' . $this->request->get['product_id']);
		$data['unlike_href'] = $this->url->link('product/product/unlike', 'product_id=' . $this->request->get['product_id']);
		$data['reviews'] = array();
		$data['more'] = false;
		$data['page'] = $page + 1;
		$data['all_images'] = [];
		
    if ($page == 1) {
      $all_images = $this->model_catalog_review->getReviewImagesProduct($this->request->get['product_id']);
      foreach ($all_images as $image) {
        $img = [];
        $img['thumb'] = $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'), true, true);
				$img['popup'] = $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'), true, true);
//        $img['popup'] = $this->model_tool_image->resize($image['image'], false, false, true);
        $data['all_images'][] = $img;
      }
    }

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id'], $type);
		if ($review_total > $page * $limit) {
			$data['more'] = true;
		}

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * $limit, $limit, $type);

		foreach ($results as $result) {
			$images = [];
			foreach ($this->model_catalog_review->getReviewImages($result['review_id']) as $image) {
				$img = [];
				$img['thumb'] = $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'), true, true);
				$img['popup'] = $this->model_tool_image->resize($image['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'), true, true);
//				$img['popup'] = $this->model_tool_image->resize($image['image'], false, false, true);
				$images[] = $img;
			}
			$data['reviews'][] = array(
				'review_id' => $result['review_id'],
				'type' => $type,
				'author' => $result['author'],
				'text' => nl2br($result['text']),
				'answer' => nl2br($result['respond']),
				'review_images' => $images,
				'rating' => (int)$result['rating'],
				'useful_photo' => (int)$result['useful_photo'],
				'useful_description' => (int)$result['useful_description'],
				'like' => (int)$result['like'],
				'unlike' => (int)$result['unlike'],
				'like_token' => $this->getToken($result['review_id'], 'product/review/like'),
				'unlike_token' => $this->getToken($result['review_id'], 'product/review/unlike'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_responded' => date($this->language->get('date_format_short'), strtotime($result['date_responded']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		// $data['pagination'] = $pagination->render();
		//
		// $data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($review_total - $limit)) ? $review_total : ((($page - 1) * $limit) + $limit), $review_total, ceil($review_total / $limit));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	private function getToken($id, $route = '') {
		$res = sha1($this->session->getId());
		if (!empty($route))
			$res = sha1($res . $route);
		$res = sha1($res . $id);
		$res = sha1($res . 'aozeya6Paishu7Aecha7AhCeiw8shoo:');
		return $res;
	}

	public function questions() {
		return $this->review(1);
	}

	public function like() {
		$this->load->model('catalog/review');
		if ($this->checkToken($this->request->get['review_id'], $this->request->get['token'], 'product/review/like')) {
			$this->model_catalog_review->likeReview($this->request->get['review_id']);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->model_catalog_review->getLikeReview($this->request->get['review_id'])));
	}

	private function checkToken($id, $token, $route = '') {
		if ($token == $this->getToken($id, $route)) {
			if (empty($this->session->data['tokens'])) {
				$this->session->data['tokens'] = array($token);
				return true;
			} elseif (!in_array($token, $this->session->data['tokens'])) {
				array_push($this->session->data['tokens'], $token);
				return true;
			} else {
				return false;
			}
		}
	}

	public function unlike() {
		$this->load->model('catalog/review');
		if ($this->checkToken($this->request->get['review_id'], $this->request->get['token'], 'product/review/unlike')) {
			$this->model_catalog_review->unlikeReview($this->request->get['review_id']);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->model_catalog_review->getLikeReview($this->request->get['review_id'])));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		$json['debug'] = $this->request->post;

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 35)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}
			if (empty($this->request->post['rating_photo']) || $this->request->post['rating_photo'] < 0 || $this->request->post['rating_photo'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}
			if (empty($this->request->post['rating_description']) || $this->request->post['rating_description'] < 0 || $this->request->post['rating_description'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day' => $this->language->get('text_day'),
					'week' => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month' => $this->language->get('text_month'),
					'year' => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function productImages() {
		$this->load->language('product/product');
		$this->load->model('tool/image');

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = 100;
		}

		$json = array(
			'images' => $this->getImages($product_id, $limit)
		);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function quickview() {
		$this->load->language('product/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$need_attributes_name = array(
			'Внешние размеры, см' => 'Размеры',
			'Размеры' => 'Размеры',
			'Ширина ремня, см' => 'Ширина ремня, см',
		);

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 70;
		}
		$data = array();

		$product_info = $this->model_catalog_product->getProduct($product_id);
		// var_dump($product_info, $product_id);
		if ($product_info) {
			$data['javascript'] = file_get_contents('catalog/view/theme/lakestone/js/quickview.min.js');
			$data['name'] = $product_info['name'];
			$data['quantity'] = $product_info['quantity'];
			$data['href'] = $this->url->link('product/product', array('product_id' => $product_id));
			$data['product_id'] = $product_id;
			$data['related'] = array();
			$data['attributes'] = array(array(
				'name' => 'Артикул',
				'value' => $product_info['model'],
			));
			// $data['short'] = html_entity_decode($product_info['short'], ENT_QUOTES, 'UTF-8');
			$data['short'] = mb_substr(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'), 0, 130) . '...';
			$data['full'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			foreach ($this->model_catalog_product->getProductAttributes($product_id) as $attribute_group) {
				if ($attribute_group['name'][0] == '_')
					continue;
				foreach ($attribute_group['attribute'] as $attribute) {
					foreach ($need_attributes_name as $name => $view) {
						if ($name == $attribute['name']) {
							$data['attributes'][] = array(
								'name' => (empty($view) ? $name : $view),
								'value' => $attribute['text']
							);
						}
					}
				}
			}

			$data['related'][] = array(
				'product_id' => $product_id,
				'href' => $this->url->link('product/product', '&product_id=' . $product_id),
				'name' => $product_info['name'],
				'image' => $this->getImages($product_id, 0)
			);

			foreach ($this->model_catalog_product->getProductRelated($product_id) as $product) {
				if ($product['product_id'] == $product_id)
					continue;
				$data['related'][] = array(
					'product_id' => $product['product_id'],
					'href' => $this->url->link('product/product', '&product_id=' . $product['product_id']),
					'name' => $product['name'],
					'image' => $this->getImages($product['product_id'], 0)
				);
			}

			array_multisort(array_column($data['related'], 'name'), SORT_ASC, $data['related']);
			// var_dump($data['related']);exit;

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$data['images'] = $this->getImages($product_id, 9);

			$this->response->setOutput($this->load->view('product/quickview', $data));
		} else {
			$this->response->setOutput('<div>Ошибка. Продукт не найден.</div>');
		}

	}

	public function oneclick() {
		$this->load->language('product/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		$data = array();

		$product_info = $this->model_catalog_product->getProduct($product_id);
		if ($product_info) {

			$product_categories = $this->model_catalog_product->getCategories($product_id);
			$data['category'] = $this->model_catalog_category->getCategory($product_categories[0]['category_id'])['name'];


			$data['name'] = $product_info['name'];
			$data['product_id'] = $product_id;
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$data['images'] = $this->getImages($product_id, 0);

			$this->response->setOutput($this->load->view('product/oneclick', $data));
		} else {
			$this->response->setOutput('<div>Ошибка. Продукт не найден.</div>');
		}

	}

	public function preorder() {
		$this->load->language('product/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		$data = array();

		$product_info = $this->model_catalog_product->getProduct($product_id);
		if ($product_info) {
			$data['name'] = $product_info['name'];
			$data['product_id'] = $product_id;
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$data['images'] = $this->getImages($product_id, 0);

			$this->response->setOutput($this->load->view('product/preorder', $data));
		} else {
			$this->response->setOutput('<div>Ошибка. Продукт не найден.</div>');
		}

	}
  
  private function getCache(string $cache_name, callable $cache_making) {
	  if ($this->config->get('cache_out')) {
      $ret = $this->cache->get($cache_name);
      if ($ret === false) {
        $ret = $cache_making();
        $this->cache->set($cache_name, $ret);
      }
    } else {
	    $ret = $cache_making();
    }
    return $ret;
	}
	
	private function setup_delivery_pickpoints() { }

	private function setup_delivery_courier() { }

}
