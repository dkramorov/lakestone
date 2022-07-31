<?php
class ControllerExtensionModuleLatest extends Controller {
	public function index($setting) {
		//$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.css');
		//$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

		$Out = $this->cache->get('module_latest');
		if (defined('OUT_CACHE') and OUT_CACHE and $Out) {
			return $Out;
		}

		$this->load->language('extension/module/latest');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/product');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');


		$data['products'] = array();
		$data['module'] = '_latest';
		$data['setting'] = $setting;

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $cache_id = False;
		if (defined('OUT_CACHE') and OUT_CACHE) {
			$cache_id = 'sql_catalog_product_getProducts_' . md5(json_encode($filter_data));
			$results = $this->cache->get($cache_id);
		}
		if (!$results) {
			$results = $this->model_catalog_product->getProducts($filter_data);
			if ($cache_id)
				$this->cache->set($cache_id, $results);
		}

		if ($results) {
			foreach ($results as $result) {

				// $product_images = $this->model_catalog_product->getProductImages($result['product_id']);
				$images = array();

				if ($result['image']) {
					$images[0] = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$images[0] = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}
				// if ( isset($product_images[6]) and ! empty($product_images[6]) ) {
				// 	$images[1] = $this->model_tool_image->resize($product_images[6]['image'], $setting['width'], $setting['height']);
				// } else {
				// 	$images[1] = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				// }

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$sale = sprintf('%2d%%', 100 - ($result['special'] * 100 / $result['price']));
				} else {
					$special = false;
					$sale = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $images[0],
					'images'      => $images,
					'name'        => $result['name'],
					// 'short'				=> html_entity_decode($result['short'], ENT_QUOTES, 'UTF-8'),
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'sale'				=> $sale,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

		$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
		$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
		$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/owl_product_list.min.css';
		if (file_exists($style_file))
			$this->document->addStyle($style_file);

		$Out = $this->load->view('extension/module/owl_product_list', $data);
		if (defined('OUT_CACHE') and OUT_CACHE)
			$this->cache->set('module_latest', $Out);
		return $Out;
		}
	}
}
