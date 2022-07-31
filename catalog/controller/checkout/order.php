<?php
class ControllerCheckoutOrder extends Controller {

  private $RequiredFields = array(
    'FullName', 'Phone',
		'DeliveryMethod', 'PaymentMethod'
  );

  public function index() {

    $json = array();

    $freeDelivery = array('г. Москва', 'г. Санкт-Петербург');

    $this->load->model('extension/module/mysklad');
    $this->load->model('extension/module/retailcrm');

    $json['debug'] = $this->request->post;

    foreach ($this->RequiredFields as $field) {
      if (!isset($this->request->post[$field]) or empty($this->request->post[$field])) {
        $json['error'] = array(
          'reason'  => 'empty field',
          'field'   => $field
        );
      }
    }
    $Address1 = $Address2 = '';
    $ShippingCode = $this->request->post['DeliveryMethod'];
    $PaymentCode = $this->request->post['PaymentMethod'];
    switch ($this->request->post['DeliveryMethod']) {
      case 'pickpoint':
        if (!isset($this->request->post['pickpoint'])) {
          $json['error'] = array(
            'reason'  => 'empty field',
            'field'   => 'pickpoint'
          );
          break;
        }
        $Address1 = $this->request->post['address'];
        $Address2 = $this->request->post['pickpoint'];
        $ShippingTitle = 'Доставка в пункт самовывоза';
        $ShippingCost = 0;
        break;
      case 'post':
        $Address1 = $this->request->post['address'];
        $ShippingTitle = 'Доставка Почтой России';
        $ShippingCost = 0;
        break;
      case 'courier':
        if (!isset($this->request->post['address'])) {
          $json['error'] = array(
            'reason'  => 'empty field',
            'field'   => 'address'
          );
          break;
        }
        $Address1 = $this->request->post['address'];
        $ShippingTitle = 'Доставка курьером';
        if (in_array($this->session->data['Locality'], $freeDelivery))
          $ShippingCost = 0;
        else
          $ShippingCost = 390;
        break;
      case 'showroom':
        if ($this->session->data['Locality'] != 'г. Москва') {
          $json['error'] = array(
            'reason'  => 'delivery inpossible',
            'message' => 'Доставка в шоурум невозможна для выбранного города: ' . $this->session->data['Locality']
          );
        }
        $Address1 = $this->session->data['Locality'] . ', шоурум';
        $ShippingTitle = 'Самовывоз из шоурума';
        $ShippingCost = 0;
        break;
      default:
        $ShippingCode = 'unknown';
    }
    switch ($this->request->post['PaymentMethod']) {
      case 'card':
        $PaymentTitle = 'банковской картой';
        break;
      case 'cache':
      $PaymentTitle = 'наличными';
        break;
      default:
        $PaymentCode = 'unknown';
    }
    if (isset($this->request->post['EMail']) and !empty($this->request->post['EMail']))
      $CustomerEmail = $this->request->post['EMail'];
    else
      $CustomerEmail = $this->config->get('config_email');

    // make order
    $this->session->data['guest'] = array(
      'customer_group_id' => $this->config->get('config_customer_group_id'),
      'firstname'		=> $this->request->post['FullName'],
      'lastname'		=> '',
      'email'		    => $CustomerEmail,
      'telephone'		=> $this->request->post['Phone'],
      'fax'		=> '',
      'custom_field'	=> '',
    );
    $this->session->data['payment_address'] = $this->session->data['shipping_address'] = array(
      'country_id'  => $this->config->get('config_country_id'),
      'zone_id'     => $this->config->get('config_zone_id'),
      'firstname'		=> $this->request->post['FullName'],
      'lastname'		=> '',
      'company'		  => '',
      'address_1'		=> $Address1,
      'address_2'		=> $Address2,
      'city'		    => $this->session->data['Locality'],
      'postcode'		=> '',
      'zone'		    => '',
      'country'		  => '',
      'address_format' => '',
      'custom_field' => $Address2,
    );
    $this->session->data['vouchers'] = '';
    $this->session->data['comment'] = $this->request->post['Comment'];
    $this->session->data['payment_method'] = array(
      'code'		=> 'cod', //$PaymentCode,
      'title'		=> $PaymentTitle,
    );
    $this->session->data['shipping_method'] = array(
      'code'		=> $ShippingCode,
      'title'   => $ShippingTitle,
      'cost'    => $ShippingCost,
      'tax_class_id' => $this->config->get('config_tax'),
    );

    if (!isset($json['error'])) {
      $r1 = $this->load->controller('checkout/confirm');
      $r2 = $this->load->controller('extension/payment/cod/confirm');
      if (isset($this->session->data['order_id'])) {
        $json['success'] = array(
          'order_id'  => $this->session->data['order_id'],
          'redirect'  => $this->url->link('checkout/success'),
        );
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));

  }
}
