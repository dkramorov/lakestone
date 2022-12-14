<?php
class ControllerCheckoutCart extends Controller {

  private $UsersVars = array(
      'FullName', 'Phone', 'EMail', 'Comment',
      'PostAddress', 'CourierAddress'
  );
  
  private $freeDelivery = array('г. Москва', 'г. Санкт-Петербург');
  
  private $DeliveryMethods = array(
      'pickpoint' => 'Забрать в пункте выдачи (бесплатно)',
      'courier' => 'Курьерская доставка до двери (390 руб.)',
      'post' => 'Почта России (бесплатно)',
      'showroom' => 'Забрать из шоурума',
  );

  public function index() {
    $this->load->language('checkout/cart');
    $this->document->addStyle('catalog/view/theme/lakestone/stylesheet/checkout_cart.min.css');
    $this->document->addScript('catalog/view/javascript/jquery/jquery.inputmask.bundle.min.js');

    /*     * ******** */
//		$data['product_count'] = $this->cart->countProducts();
//		$data['form_action'] = $this->url->link('checkout/checkout/quickorder', '', 'SSL');
    $data['errors'] = array();
    $data['error_message'] = '';
    foreach ($this->UsersVars as $field) {
      $data[$field] = '';
    }

    if (isset($this->session->data['errors'])) {
      foreach ($this->session->data['errors'] as $field => $status) {
        if ($status)
          $data['errors'][$field] = true;
      }
    } else {
      $this->session->data['errors'] = array();
    }
    if (isset($this->session->data['form_fields'])) {
      foreach ($this->session->data['form_fields'] as $field => $value) {
        $data[$field] = $value;
      }
    } else {
      $this->session->data['form_fields'] = array();
    }
    if (sizeof($data['errors']) > 0)
      $data['error_message'] = 'Пожалуйста, заполните все обязательные поля. Незаполненные поля выделены красным.';
    /*     * ******** */

    $this->document->setTitle($this->language->get('heading_title'));

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
        'href' => $this->url->link('common/home'),
        'text' => $this->language->get('text_home')
    );

    $data['breadcrumbs'][] = array(
        'href' => $this->url->link('checkout/cart'),
        'text' => $this->language->get('heading_title')
    );

    $retag = array();
    if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
      $data['heading_title'] = $this->language->get('heading_title');

      $data['text_recurring_item'] = $this->language->get('text_recurring_item');
      $data['text_next'] = $this->language->get('text_next');
      $data['text_next_choice'] = $this->language->get('text_next_choice');

      $data['column_image'] = $this->language->get('column_image');
      $data['column_name'] = $this->language->get('column_name');
      $data['column_model'] = $this->language->get('column_model');
      $data['column_quantity'] = $this->language->get('column_quantity');
      $data['column_price'] = $this->language->get('column_price');
      $data['column_total'] = $this->language->get('column_total');

      $data['button_update'] = $this->language->get('button_update');
      $data['button_remove'] = $this->language->get('button_remove');
      $data['button_shopping'] = $this->language->get('button_shopping');
      $data['button_checkout'] = $this->language->get('button_checkout');
      $data['telephone'] = $this->config->get('config_telephone');

      if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
        $data['error_warning'] = $this->language->get('error_stock');
      } elseif (isset($this->session->data['error'])) {
        $data['error_warning'] = $this->session->data['error'];

        unset($this->session->data['error']);
      } else {
        $data['error_warning'] = '';
      }

      if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
        $data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
      } else {
        $data['attention'] = '';
      }

      // set Defaults
      if (!isset($this->session->data['shipping_method'])) {
        if (in_array($this->session->data['Locality'], $this->freeDelivery))
          $this->setShipping('courier');
        else
          $this->setShipping('pickpoint');
      }
      if (!isset($this->session->data['payment_method'])) {
        $this->setPayment('cache');
      }

      $data['deliveryCode'] = $this->session->data['shipping_method']['code'];
      $data['paymentCode'] = $this->session->data['payment_method']['code'];

      // Locality
      $data['Locality'] = $this->session->data['Locality'];
      $data['DPoint'] = array();
      // var_dump($this->session->data['DPointInfo']);exit;
      if (isset($this->session->data['DPointInfo']['Address'])) {
        $data['DPoint'] = $this->session->data['DPointInfo'];
      }

      if (isset($this->session->data['success'])) {
        $data['success'] = $this->session->data['success'];

        unset($this->session->data['success']);
      } else {
        $data['success'] = '';
      }

      $data['action'] = $this->url->link('checkout/order', '', true);

      if ($this->config->get('config_cart_weight')) {
        $data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
      } else {
        $data['weight'] = '';
      }

      $this->load->model('tool/image');
      $this->load->model('tool/upload');
      $this->load->model('catalog/product');

		if(defined('GIFT_SET_ID')) {
			$gift_set_info = $this->model_catalog_product->getProduct(GIFT_SET_ID);

			$data['gift_set_price'] = (int) $gift_set_info['price'];
		}

      $data['products'] = array();
      $data['full_count'] = 0;
      $data['full_price'] = 0;
  
      if (defined('GIFT_SET_ID')) {
        $gift_info = $this->model_catalog_product->getProduct(GIFT_SET_ID);
        $data['gift_info'] = [
            'price' => $this->currency->format($this->tax->calculate($gift_info['price'], $gift_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
        ];
      }

      $products = $this->cart->getProducts();

      foreach ($products as $product) {
        $product_total = 0;

        foreach ($products as $product_2) {
          if ($product_2['product_id'] == $product['product_id']) {
            $product_total += $product_2['quantity'];
          }
        }
  
        $product_info = $this->model_catalog_product->getProduct($product['product_id']);
        if (!empty($product_info['special'])) {
          $data['coupon_on'] = false;
        }

        $retag[] = array(
            'id' => $product['product_id'],
            'number' => $product['quantity'],
        );

        if ($product['minimum'] > $product_total) {
          $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
        }

        if ($product['image']) {
          $image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
        } else {
          $image = '';
        }

        $option_data = array();

        foreach ($product['option'] as $option) {
          if ($option['type'] != 'file') {
            $value = $option['value'];
          } else {
            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

            if ($upload_info) {
              $value = $upload_info['name'];
            } else {
              $value = '';
            }
          }

          $option_data[] = array(
              'name' => $option['name'],
              'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
          );
        }

        // Display prices
        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
          $unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));

          $price = $this->currency->format($unit_price, $this->session->data['currency']);
          $total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
        } else {
          $price = false;
          $total = false;
        }

        $recurring = '';

        if ($product['recurring']) {
          $frequencies = array(
              'day' => $this->language->get('text_day'),
              'week' => $this->language->get('text_week'),
              'semi_month' => $this->language->get('text_semi_month'),
              'month' => $this->language->get('text_month'),
              'year' => $this->language->get('text_year'),
          );

          if ($product['recurring']['trial']) {
            $recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
          }

          if ($product['recurring']['duration']) {
            $recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
          } else {
            $recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
          }
        }


        $data['products'][] = array(
            'product_id' => $product['product_id'],
            'cart_id' => $product['cart_id'],
            'thumb' => $image,
            'name' => $product['name'],
            'model' => $product['model'],
            'option' => $option_data,
            'recurring' => $recurring,
            'quantity' => $product['quantity'],
            'stock' => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
            'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
            'price' => $price,
            'total' => $total,
            'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
        );
        $data['full_count'] += $product['quantity'];
        $data['full_price'] += (float) $total;
      }


      // Gift Voucher
      $data['vouchers'] = array();

      if (!empty($this->session->data['vouchers'])) {
        foreach ($this->session->data['vouchers'] as $key => $voucher) {
          $data['vouchers'][] = array(
              'key' => $key,
              'description' => $voucher['description'],
              'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency']),
              'remove' => $this->url->link('checkout/cart', 'remove=' . $key)
          );
        }
      }

      // full
      $data['full_price'] = $this->currency->format($data['full_price'], $this->session->data['currency']);
      $data['DeliveryMethods'] = $this->loadDeliveryMethods('int');

      if (!isset($data['coupon_on']))
        $data['coupon_on'] = $this->config->get('coupon_status');

      $this->loadTotals($data);
      
      $data['continue'] = $this->url->link('common/home');

      $data['checkout'] = $this->url->link('checkout/checkout', '', true);

      $this->load->model('extension/extension');

      $data['modules'] = array();

      $files = glob(DIR_APPLICATION . '/controller/extension/total/*.php');

      if ($files) {
        foreach ($files as $file) {
          $result = $this->load->controller('extension/total/' . basename($file, '.php'));

          if ($result) {
            $data['modules'][] = $result;
          }
        }
      }

      $this->document->retag = json_encode($retag);
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['column_right'] = $this->load->controller('common/column_right');
      $data['content_top'] = $this->load->controller('common/content_top');
      $data['content_bottom'] = $this->load->controller('common/content_bottom');
      $data['footer'] = $this->load->controller('common/footer');
      $data['header'] = $this->load->controller('common/header');

      $this->response->setOutput($this->load->view('checkout/cart', $data));
    } else {
      $data['heading_title'] = $this->language->get('heading_title');

      $data['text_error'] = $this->language->get('text_empty');

      $data['button_continue'] = $this->language->get('button_continue');

      $data['continue'] = $this->url->link('common/home');

      unset($this->session->data['success']);

      $this->document->retag = json_encode($retag);
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['column_right'] = $this->load->controller('common/column_right');
      $data['content_top'] = $this->load->controller('common/content_top');
      $data['content_bottom'] = $this->load->controller('common/content_bottom');
      $data['footer'] = $this->load->controller('common/footer');
      $data['header'] = $this->load->controller('common/header');

      $this->response->setOutput($this->load->view('error/not_found', $data));
    }
  }

  public function add() {
    $this->load->language('checkout/cart');

    $json = array();

    if (isset($this->request->post['product_id'])) {
      $product_id = (int) $this->request->post['product_id'];
    } else {
      $product_id = 0;
    }

    $this->load->model('catalog/product');
    $this->load->model('tool/image');

    $product_info = $this->model_catalog_product->getProduct($product_id);


    if ($product_info) {
      if (isset($this->request->post['quantity']) && ((int) $this->request->post['quantity'] >= $product_info['minimum'])) {
        $quantity = (int) $this->request->post['quantity'];
      } else {
        $quantity = $product_info['minimum'] ? $product_info['minimum'] : 1;
      }

      if (isset($this->request->post['option'])) {
        $option = array_filter($this->request->post['option']);
      } else {
        $option = array();
      }

      $product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

      foreach ($product_options as $product_option) {
        if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
          $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
        }
      }

      if (isset($this->request->post['recurring_id'])) {
        $recurring_id = $this->request->post['recurring_id'];
      } else {
        $recurring_id = 0;
      }

      $recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);

      if ($recurrings) {
        $recurring_ids = array();

        foreach ($recurrings as $recurring) {
          $recurring_ids[] = $recurring['recurring_id'];
        }

        if (!in_array($recurring_id, $recurring_ids)) {
          $json['error']['recurring'] = $this->language->get('error_recurring_required');
        }
      }

      if (!$json) {
        $data = array();
        $this->cart->add($this->request->post['product_id'], $quantity, $option, $recurring_id);

        $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

        // Unset all shipping and payment methods
        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);
        unset($this->session->data['payment_method']);
        unset($this->session->data['payment_methods']);

        // Totals
        $this->load->model('extension/extension');

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        // Display prices
        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
          $sort_order = array();

          $results = $this->model_extension_extension->getExtensions('total');

          foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
          }

          array_multisort($sort_order, SORT_ASC, $results);

          foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {
              $this->load->model('extension/total/' . $result['code']);

              // We have to put the totals in an array so that they pass by reference.
              $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
          }

          $sort_order = array();

          foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
          }

          array_multisort($sort_order, SORT_ASC, $totals);
        }

/*        $results = $this->model_catalog_product->getProductImages($product_id, [0,1,2]);
        if (isset($results[0]['image']))
          $data['image'] = $json['package'] = $this->model_tool_image->resize($results[0]['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));*/
        $data['image'] = $json['package'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
        $json['prod_link'] = $this->url->link('product/product', 'product_id=' . $product_id);
        $data['name'] = $json['name'] = $product_info['name'];
#        $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
        $json['amount'] = $this->currency->format($total, $this->session->data['currency']);
        $json['count'] = $this->cart->countProducts();
        $json['html'] = $this->load->view('product/purchase', $data);
        $json['price'] = (int) $product_info['price'];
        if (isset($this->request->post['button-cart'])) {
          $this->response->redirect($this->url->link('checkout/cart'));
        }
      } else {
        $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function setShipping($method = FALSE) {
    $json = array();
    $methods = array('pickpoint', 'courier', 'post', 'showroom');
    if (isset($this->request->get['method']))
      $method = $this->request->get['method'];
    if ($method and in_array($method, $methods)) {
      $shipping_cost = 0;
      switch ($method) {
        case 'pickpoint':
          $shipping_method = 'самовывоз из ПВЗ';
          break;
        case 'courier':
          if (in_array($this->session->data['Locality'], $this->freeDelivery))
            $shipping_cost = 0;
          else
            $shipping_cost = 390;
          $shipping_method = 'курьерская доставка';
          break;
        case 'post':
          $shipping_method = 'почта России';
          break;
        case 'showroom':
          $shipping_method = 'самовывоз из шоурума';
          break;
      }
      $this->session->data['shipping_method'] = array(
          'title' => $shipping_method,
          'code' => $method,
          'tax_class_id' => $this->config->get('config_tax'),
          'cost' => $shipping_cost,
      );
      $json['success'] = 1;
    }
    if (func_num_args() == 0) {
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    }
  }

  public function saveField() {
    $json = array();
    if (
            isset($this->request->post['val']) and
            isset($this->request->post['var']) and
            in_array($this->request->post['var'], $this->UsersVars)
    ) {
      $this->session->data['form_fields'][$this->request->post['var']] = $this->request->post['val'];
      $json['success'] = 1;
      $json['debug'] = $this->session->data['form_fields'];
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }
  
  public function setCoupon() {
    $json = array();
    if (isset($this->request->get['code'])) {
      $this->load->model('extension/total/coupon');
      $coupon_info = $this->model_extension_total_coupon->getCoupon($this->request->get['code']);
      if ($coupon_info) {
        $this->session->data['coupon'] = $this->request->get['code'];
        $json['status'] = 'OK';
      } else {
        $json['error'] = 'Промокод некорректно введен, либо его срок действия истек';
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function setPayment($method = FALSE) {
    $json = array();
    $methods = array('cache', 'card');
    if (isset($this->request->get['method']))
      $method = $this->request->get['method'];
    if ($method and in_array($method, $methods)) {
      switch ($method) {
        case 'card':
          $payment_method = 'банковской картой';
          break;
        case 'cache':
          $payment_method = 'наличными';
          break;
      }
      $this->session->data['payment_method'] = array(
          'title' => $payment_method,
          'code' => $method,
      );
      $json['success'] = 1;
    }
    if (func_num_args() == 0) {
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    }
  }

  public function edit() {
    $this->load->language('checkout/cart');

    $json = array();

    // Update
    if (!empty($this->request->post['quantity'])) {
      $this->cart->update($this->request->post['key'], $this->request->post['quantity']);

      $this->session->data['success'] = $this->language->get('text_remove');

      // unset($this->session->data['shipping_method']);
      // unset($this->session->data['shipping_methods']);
      // unset($this->session->data['payment_method']);
      // unset($this->session->data['payment_methods']);
      // unset($this->session->data['reward']);
      // Totals
      $this->load->model('extension/extension');

      $totals = array();
      $taxes = $this->cart->getTaxes();
      $total = 0;

      // Because __call can not keep var references so we put them into an array.
      $total_data = array(
          'totals' => &$totals,
          'taxes' => &$taxes,
          'total' => &$total
      );

      // Display prices
      if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');

        foreach ($results as $key => $value) {
          $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
          if ($this->config->get($result['code'] . '_status')) {
            $this->load->model('extension/total/' . $result['code']);

            // We have to put the totals in an array so that they pass by reference.
            $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
          }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
          $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);
      }

      // $this->response->redirect($this->url->link('checkout/cart'));
      $json['success'] = 1;
      $json['count'] = $this->cart->countProducts();
      $json['amount'] = $this->currency->format($this->cart->getTotal(), $this->session->data['currency']);
      $json['total'] = $total;
      $json['products'] = array();
      foreach ($this->cart->getProducts() as $product) {
        $json['products'][$product['cart_id']] = array(
            'quantity' => $product['quantity'],
            'total' => $this->currency->format($product['total'], $this->session->data['currency']),
        );
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function remove() {
    $this->load->language('checkout/cart');

    $json = array();

    if (isset($this->request->post['key'])) {
      // Fucking EC here
      // $this->load->model('catalog/product');
      // $product_info = $this->model_catalog_product->getProduct($product['product_id']);
      foreach ($this->cart->getProducts() as $product) {
        if ($product['cart_id'] == $this->request->post['key']) {
          $json['removed'] = array(
              'id' => $product['product_id'],
              'name' => $product['name'],
              'price' => $product['price'],
              'quantity' => $product['quantity'],
          );
          break;
        }
      }

      // Remove
      $this->cart->remove($this->request->post['key']);

      if (isset($this->session->data['vouchers'][$this->request->post['key']]))
        unset($this->session->data['vouchers'][$this->request->post['key']]);

      $json['success'] = $this->language->get('text_remove');

      // unset($this->session->data['shipping_method']);
      // unset($this->session->data['shipping_methods']);
      // unset($this->session->data['payment_method']);
      // unset($this->session->data['payment_methods']);
      // unset($this->session->data['reward']);
      // Totals
      $this->load->model('extension/extension');


      $totals = array();
      $taxes = $this->cart->getTaxes();
      $total = 0;

      // Because __call can not keep var references so we put them into an array.
      $total_data = array(
          'totals' => &$totals,
          'taxes' => &$taxes,
          'total' => &$total
      );

      // Display prices
      if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');

        foreach ($results as $key => $value) {
          $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
          if ($this->config->get($result['code'] . '_status')) {
            $this->load->model('extension/total/' . $result['code']);

            // We have to put the totals in an array so that they pass by reference.
            $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
          }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
          $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);
      }
      $json['success'] = 1;
      $json['count'] = $this->cart->countProducts();
      $json['amount'] = $this->currency->format($this->cart->getTotal(), $this->session->data['currency']);
      $json['total'] = $total;
      $json['products'] = array();
      foreach ($this->cart->getProducts() as $product) {
        $json['products'][$product['cart_id']] = array(
            'quantity' => $product['quantity'],
            'name' => $product['name'],
            'id' => $product['product_id'],
            'total' => $product['total'],
        );
      }

      // $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function loadDeliveryMethods($format = 'out') {
    $data['DeliveryMethods'] = $this->getDeliveryMethods('array');
    $ret = $this->load->view('checkout/delivery_methods', $data);
    if ($format == 'out')
      $this->response->setOutput($ret);
    else
      return $ret;
  }

  public function getDeliveryMethods($format = 'json') {
    $json = array();
    // switch ($this->session->data['Locality']) {
    // 	case 'г. Москва':
    // 		break;
    // }
    if (in_array($this->session->data['Locality'], $this->freeDelivery)) {
      foreach ($this->DeliveryMethods as $method => $title) {
        $this->DeliveryMethods[$method] = preg_replace('/\(.*\)/', '', $title);
      }
      $json[] = array(
          'method' => 'courier',
          'title' => $this->DeliveryMethods['courier'],
      );
      unset($this->DeliveryMethods['courier']);
    }
    if ($this->session->data['Locality'] == 'г. Москва')
      unset($this->DeliveryMethods['post']);
    if ($this->session->data['Locality'] != 'г. Москва')
      unset($this->DeliveryMethods['showroom']);
    foreach ($this->DeliveryMethods as $method => $data) {
      $json[] = array(
          'method' => $method,
          'title' => $data,
      );
    }
    if ($format == 'json') {
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($json));
    } else {
      return $json;
    }
  }

  public function loadTotals(&$data = []) {
    
    if (empty($data))
      $json = true;
    else
      $json = false;

    foreach ($this->getTotals() as $total) {
      switch ($total['code']) {
        case 'shipping':
          $data['deliveryComment'] = '';
          $data['deliveryCost'] = $this->currency->format($total['value'], $this->session->data['currency']);
          break;
        case 'coupon':
          $data['float_coupon'] = $total['value'];
          $data['coupon'] = $this->currency->format($total['value'], $this->session->data['currency']);
          break;
        case 'sub_total':
          $data['amount'] = $this->currency->format($total['value'], $this->session->data['currency']);
          break;
        case 'total':
          if (!empty($data['float_coupon'])) {
            $data['pre_total'] = $this->currency->format(($total['value'] + abs($data['float_coupon'])), $this->session->data['currency']);
          }
          $data['total'] = $this->currency->format($total['value'], $this->session->data['currency']);
          break;
      }
    }

    if ($json) {
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode($data));
    }

  }

  private function getTotals() {
    // Totals
    $this->load->model('extension/extension');

    $totals = array();
    $taxes = $this->cart->getTaxes();
    $total = 0;

    // Because __call can not keep var references so we put them into an array.
    $total_data = array(
        'totals' => &$totals,
        'taxes' => &$taxes,
        'total' => &$total
    );

    // Display prices
    if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
      $sort_order = array();

      $results = $this->model_extension_extension->getExtensions('total');

      foreach ($results as $key => $value) {
        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
      }

      array_multisort($sort_order, SORT_ASC, $results);

      foreach ($results as $result) {
        if ($this->config->get($result['code'] . '_status')) {
          $this->load->model('extension/total/' . $result['code']);

          // We have to put the totals in an array so that they pass by reference.
          $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
        }
      }

      $sort_order = array();

      foreach ($totals as $key => $value) {
        $sort_order[$key] = $value['sort_order'];
      }
      
      array_multisort($sort_order, SORT_ASC, $totals);
    }

    return $totals;
  }

}
