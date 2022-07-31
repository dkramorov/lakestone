<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		if (isset($setting['heading_title']))
			$data['heading_title'] = $setting['heading_title'];
		else
			$data['heading_title'] = $this->language->get('heading_title');


		if (isset($setting['ProductDescription']) and $setting['ProductDescription']) {
			$data['ProductDescription'] = true;
		} else {
			$data['ProductDescription'] = false;
		}

		if (isset($setting['ProductRating']) and $setting['ProductRating']) {
			$data['ProductRating'] = true;
		} else {
			$data['ProductRating'] = false;
		}

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');

		// $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		// $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/owl_product_list.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

		$data['products'] = array();
		$data['module'] = '_featured';

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		$data['setting'] = $setting;
		$LocaleRU = new Locality_RU();

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				// $product_images = $this->model_catalog_product->getProductImages($product_id);
				$images = array();

				if ($product_info) {
					if ($product_info['image']) {
						$images[0] = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$images[0] = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}
					// if ($product_images[6]) {
					// 	$images[1] = $this->model_tool_image->resize($product_images[6]['image'], $setting['width'], $setting['height']);
					// } else {
					// 	$images[1] = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					// }

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$sale = sprintf('%2d%%', 100 - ($product_info['special'] * 100 / $product_info['price']));
					} else {
						$special = false;
						$sale = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $images[0],
						'images'      => $images,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
						'short' => utf8_substr(strip_tags(html_entity_decode($product_info['short'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')),
						// 'short'				=> html_entity_decode($product_info['short'], ENT_QUOTES, 'UTF-8'),
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'sale'				=> $sale,
						'rating'      => $rating,
						'reviews_num' => (int)$product_info['reviews'],
						'reviews' 		=> ' отзыв' . $LocaleRU->num_ending((int)$product_info['reviews']),
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
		$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
		$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/owl_product_list.min.css';
		if (file_exists($style_file))
			$this->document->addStyle($style_file);

		if ($data['products']) {
			return $this->load->view('extension/module/owl_product_list', $data);
		}
	}
}
