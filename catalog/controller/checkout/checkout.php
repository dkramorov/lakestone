<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->response->redirect($this->url->link('checkout/cart'));
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('klarna_account') || $this->config->get('klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
		$data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
		$data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
		$data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
		$data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);
		
		if ($this->cart->hasShipping()) {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);	
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('checkout/checkout', $data));
	}

	public function quickorder() {
		$this->session->data['errors'] = array();
		if ( $this->cart->hasProducts() and ! empty($this->request->post['quantity']) ) {
			foreach ( $this->request->post['quantity'] as $pos => $quantity ) {
				$this->cart->update($pos, $quantity);
			}
		}
		if (isset($this->request->post['button_refresh']))
			$this->response->redirect($this->url->link('checkout/cart'));
/*		if (empty($this->request->post['EMail']))
			$this->session->data['errors']['EMail'] = true;*/
		foreach (array('Phone', 'FullName', 'PayWay') as $field) {
			if (empty($this->request->post[$field])) {
				$this->session->data['errors'][$field] = true;
			} else {
				$this->session->data['form_fields'][$field] = $this->request->post[$field];
			}
		}
		foreach (array('EMail', 'Comment', 'Address') as $field) {
			if ( ! empty($this->request->post[$field])) {
				$this->session->data['form_fields'][$field] = $this->request->post[$field];
			}
		}
		if (empty($this->request->post['Address']) and empty($this->request->post['DPointAddress'])) {
			$this->session->data['errors']['Address'] = true;
		}
		if ( ! empty($this->session->data['errors']) )
			$this->response->redirect($this->url->link('checkout/cart'));
		if ( empty($this->request->post['EMail']) )
			$email = 'admin@lakestone.ru';
		else
			$email = $this->request->post['EMail'];
		if ( $this->request->post['PayWay'] == 1 )
			$payment_method = 'наличными';
		else
			$payment_method = 'банковской картой';
		$shipping_address_1 = $this->request->post['Address'];
		$shipping_address_2 = $shipping_city = '';
		$shipping_code = 'free';
		$shipping_method = 'бесплатная доставка';
		if (empty($this->request->post['Address'])) {
			$shipping_city = $this->session->data['DPoint']['CityName'];
			$shipping_address_1 = 'самовывоз из пункта выдачи заказов ';
			if ($this->session->data['DPoint']['prov'] == 'boxberry') {
				$shipping_address_1 .= '"BoxBerry" (http://boxberry.ru/)';
				$shipping_method = 'Доставка в пункт выдачи заказов "BoxBerry"';
				$shipping_code = 'boxberry:' . $this->session->data['DPoint']['id'];
			} elseif ($this->session->data['DPoint']['prov'] == 'cdek') {
				$shipping_address_1 .= '"CDEK" (http://cdek.ru/)';
				$shipping_method = 'Доставка в пункт выдачи заказов "CDEK"';
				$shipping_code = 'cdek:' . $this->session->data['DPoint']['id'];
			}
			$shipping_address_2 = $this->request->post['DPointAddress'];
		}
		$this->session->data['guest'] = array(
			'customer_group_id' => $this->config->get('config_customer_group_id'),
			'firstname'	=> $this->request->post['FullName'],
			'lastname'	=> '',
			'email'		=> $email,
			'telephone'	=> $this->request->post['Phone'],
			'fax'		=> '',
			'custom_field'	=> '',
		);
		$this->session->data['payment_address'] = $this->session->data['shipping_address'] = array(
			'country_id'	=> $this->config->get('config_country_id'),
			'zone_id'	=> $this->config->get('config_zone_id'),
			'firstname'	=> $this->request->post['FullName'],
			'lastname'	=> '',
			'company'	=> '',
			'address_1'	=> $this->request->post['Address'],
			'address_2'	=> '',
			'city'		=> '',
			'postcode'	=> '',
			'zone'		=> '',
			'country'	=> '',
			'address_format'=> '',
			'custom_field'	=> '',
		);
		$this->session->data['shipping_address']['address_1'] = $shipping_address_1;
		$this->session->data['shipping_address']['address_2'] = $shipping_address_2;
		$this->session->data['shipping_address']['city'] = $shipping_city;
			$this->session->data['vouchers'] = '';
		$this->session->data['comment'] = $this->request->post['Comment'];
		$this->session->data['payment_method'] = array(
			'code'		=> 'cod',
			'title'		=> $payment_method,
		);
		$this->session->data['shipping_method'] = array(
			'title'		=> $shipping_method,
			'code'		=> $shipping_code,
		);
		if ( ! isset($this->request->post['order']) )
			$this->response->redirect($this->url->link('checkout/cart'));
		$r1 = $this->load->controller('checkout/confirm');
		$r2 = $this->load->controller('extension/payment/cod/confirm');
		$this->response->redirect($this->url->link('checkout/success'));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
