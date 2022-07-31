<?php
class ControllerCheckoutOneClick extends Controller {

    public function index() {

        if ( isset($this->request->post['product_id']) ) {

          $this->load->model('extension/module/mysklad');
          $this->load->model('extension/module/retailcrm');

          $this->cart->clear();
          $this->cart->add($this->request->post['product_id']);

          $this->session->data['guest'] = array(
            'customer_group_id' => $this->config->get('config_customer_group_id'),
            'firstname'		=> $this->request->post['FullName'],
            'lastname'		=> '',
            'email'		=> $this->config->get('config_email'),
            'telephone'		=> $this->request->post['Phone'],
            'fax'		=> '',
            'custom_field'	=> '',
          );
          $this->session->data['payment_address'] = $this->session->data['shipping_address'] = array(
            'country_id'	=> $this->config->get('config_country_id'),
            'zone_id'		=> $this->config->get('config_zone_id'),
            'firstname'		=> $this->request->post['FullName'],
            'lastname'		=> '',
            'company'		=> '',
            'address_1'		=> '1Клик, без адреса',
            'address_2'		=> '',
            'city'		=> '',
            'postcode'		=> '',
            'zone'		=> '',
            'country'		=> '',
            'address_format'	=> '',
            'custom_field'	=> 'OneClick',
          );
          $this->session->data['vouchers'] = '';
          $this->session->data['comment'] = 'OneClick';
          $this->session->data['payment_method'] = array(
            'code'		=> 'cod',
            'title'		=> '1Клик, не определено',
          );
          $this->session->data['shipping_method'] = array(
            'code'		=> '',
            'title'		=> '',
    				'tax_class_id' => $this->config->get('config_tax'),
    				'cost'		=> 0,
          );
          $r1 = $this->load->controller('checkout/confirm');
          $r2 = $this->load->controller('extension/payment/cod/confirm');

          $products = $this->cart->getProducts();
          // AdmiTad
          if ( isset($this->session->data['admitad_uid']) ) {
            $this->load->model('extension/module/admitad');
            $this->model_extension_module_admitad->postback(array(
              'products'	=> $products,
              'admitad_uid' => $this->session->data['admitad_uid'],
              'order_id'	=> $this->session->data['order_id'],
            ));
          }

          // MySklad

/*            $res = $this->model_extension_module_mysklad->createOrder(array(
                'order_id'	=> $this->session->data['order_id'],
                'products'	=> $products,
            ));
*/

          // retailCRM
          if ( ! isset($res->errors) ) {
            $mres = $this->model_extension_module_retailcrm->createOrder(array(
              'order_id'		=> $this->session->data['order_id'],
              'products'		=> $products,
              'firstname'		=> $this->request->post['FullName'],
              'telephone'		=> $this->request->post['Phone'],
              'callback'		=> true,
              'order_method'	=> 'one-click',
              'comment'		=> '',
              'payment_method'	=> 'cash',
              // 'email'		=> $this->config->get('config_email'),
              'email'		=> '',
            ));
          }
          // retailCRM
/*	    $this->load->model('extension/module/retailcrm');
	    $res = $this->model_extension_module_retailcrm->createOrder(array(
	        'order_id'	=> $this->session->data['order_id'],
	        'products'	=> $this->cart->getProducts(),
	        'firstname'	=> $this->request->post['FullName'],
	        'telephone'	=> $this->request->post['Phone'],
	        'callback'	=> true,
	        'order_method'	=> 'one-click',
	        'comment'	=> '',
	        'payment_method'=> 'cash',
	        'email'		=> $this->config->get('config_email'),
	    ));*/

          $this->cart->clear();
          $json = array();

          $json['text'] = 'Cпасибо за заказ, с Вами свяжется менеджер магазина';
          $json['order_id']	= $this->session->data['order_id'];
          $this->response->addHeader('Content-Type: application/json');
          $this->response->setOutput(json_encode($json));

        } else {

          $json['text'] = 'Функция заказа "в один клик" не работает, пожалуйста, воспользуйтесь полным оформлением заказа';
          $this->response->addHeader('Content-Type: application/json');
          $this->response->setOutput(json_encode($json));

        }
    }
}
