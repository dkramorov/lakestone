<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');
		$retag = array();
		$this->document->amount = 0;
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/success.min.css');

                /****** !!! FOR ONLY DEBUG !!! ****/
//                $this->session->data['order_id'] = 151630;
                /**********************************/

		$this->load->model('account/order');
		$this->load->model('extension/module/mysklad');
		$this->load->model('extension/module/retailcrm');
		$this->load->model('extension/module');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->session->data['order_id'])) {

			$data['order_id'] = $this->session->data['order_id'];
		  $data['sc'] = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
			$data['total_full'] = $data['total'] = $this->document->amount = $this->cart->getTotal();
			$data['order_info'] = $orderInfo = $this->model_account_order->getOrder($this->session->data['order_id']);

			$data['heading_title'] = 'Спасибо! Заказ № ' . $this->session->data['order_id'] . ' принят';
			$data['text_total_full'] = 'Итого к оплате:';
			$data['telephone'] = $this->config->get('config_telephone');
			$data['telephone_href'] = str_replace(array(' ', '(', ')', '-'), '', $this->config->get('config_telephone'));
			$data['Locality'] = $this->session->data['Locality'];

			$this->document->setTitle($this->language->get('heading_title'));

			$data['order_info']['cond'] = array();
			$data['order_info']['cont'] = array();
			$data['products'] = array();

			$data['order_info']['cond'][] = array(
				'name'	=> 'Способ оплаты',
				'text'	=> $orderInfo['payment_method'] . ' при получении'
			);

			$data['order_info']['cond'][] = array(
				'name'	=> 'Адрес доставки',
				'text'	=> $orderInfo['shipping_address_1']
			);

			$data['order_info']['cond'][] = array(
				'name'	=> 'Способ доставки',
				'text'	=> $orderInfo['shipping_method']
			);

			foreach ($data['sc'] as $product) {
				$data['products'][] = array(
					'name'			=> $product['name'],
					'quantity'	=> 'x ' . $product['quantity'] . ' шт',
					'total'			=> $this->currency->format($product['total'], $orderInfo['currency_code']),
				);
			}

			// var_dump($orderInfo);

			// totals
			$data['totals'] = $this->model_account_order->getOrderTotals($this->session->data['order_id']);
			foreach ($data['totals'] as $total) {
				switch ($total['code']) {
					case 'coupon':
						$data['coupon'] = $this->currency->format($total['value'], $orderInfo['currency_code']);
                                                $data['coupon_title'] = $total['title'];
						break;
					case 'shipping':
						$data['shipping_cost'] = $this->currency->format($total['value'], $orderInfo['currency_code']);
						break;
					case 'sub_total':
						$data['total'] = $this->currency->format($total['value'], $orderInfo['currency_code']);
						break;
					case 'total':
						$data['total_full'] = $this->currency->format($total['value'], $orderInfo['currency_code']);
						break;
				}
			}

			$data['products'][] = array(
				'name'			=> 'Cтоимость доставки',
				'quantity'	=> '',
				'total'			=> $data['shipping_cost'],
			);
      
      if (!empty($data['coupon'])) {
        $data['products'][] = array(
                'name'			=> 'Скидка по промокоду',
                'quantity'	=> '',
                'total'			=> $data['coupon'],
        );
      }

			$this->document->order_id = $this->session->data['order_id'];
			foreach ($data['sc'] as $product) {
				$retag[] = array(
					'id'    => $product['product_id'],
					'number'=> $product['quantity'],
				);
			}

			// AdmiTad
      if ( isset($this->session->data['admitad_uid']) ) {
        try {
          $this->load->model('extension/module/admitad');
          $this->model_extension_module_admitad->postback(array(
              'products' => $data['sc'],
              'admitad_uid' => $this->session->data['admitad_uid'],
              'order_id' => $this->session->data['order_id'],
          ));
        } catch (Throwable $e) {
          $this->log->write('Admitad error: ' . $e->getMessage());
        }
      }

			// MySklad
/*			$res = $this->model_extension_module_mysklad->createOrder(array(
				'order_id'	=> $this->session->data['order_id'],
				'products'	=> $data['sc'],
			));*/

			// retailCRM
			if ( $orderInfo['payment_method'] == 'наличными' )
				$orderInfo['payment_method'] = 'cash';
			else
				$orderInfo['payment_method'] = 'bank-card';
                        if (!empty($data['coupon_title'])) {
                          $managerComment = $data['coupon_title'];
                        } else {
                          $managerComment = '';
                        }
			if ( ! isset($res->errors) ) {
				$mres = $this->model_extension_module_retailcrm->createOrder(array(
					'order_id'		=> $this->session->data['order_id'],
					'products'		=> $data['sc'],
					'firstname'		=> $orderInfo['firstname'],
					'telephone'		=> $orderInfo['telephone'],
					'callback'		=> false,
					'order_method'		=> 'shopping-cart',
					'customerComment'	=> $orderInfo['comment'],
					'payment_method'	=> $orderInfo['payment_method'],
					'email'			=> $orderInfo['email'],
                                        'managerComment'        => $managerComment,
				));
			}

			$this->cart->clear();

			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');

				if ($this->customer->isLogged()) {
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
						'order_id'    => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_account', $activity_data);
				} else {
					$activity_data = array(
						'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
						'order_id' => $this->session->data['order_id']
					);

					$this->model_account_activity->addActivity('order_guest', $activity_data);
				}
			}

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$AccessoriesSetting = $this->model_extension_module->getModuleByName('Аксесуары к завершению заказа');
		$AccessoriesSetting['heading_title'] = false;
		$AccessoriesSetting['ProductRating'] = true;
		$data['Accessories'] = $this->load->controller('extension/module/featured', $AccessoriesSetting);

		$this->document->retag = json_encode($retag);
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
