<?php

namespace Lakestone\SubCommon\Trait\Model;

trait Admitad {
  
  public function postback($data) {
    
    /*
    https://ad.admitad.com/r?campaign_code=6094969c4d&postback=1&postback_key=8650e9CCf0c817Da05656a9b2c8a9C0D&action_code=1
    &uid=&order_id=&tariff_code=1&currency_code=&price=&quantity=&position_id=&position_count=&product_id=&client_id=&payment_type=sale
    */
    
    foreach ($data['products'] as $pid => $product) {
      $qs = http_build_query(array(
          'campaign_code' => '6094969c4d',
          'postback' => 1,
          'postback_key' => '8650e9CCf0c817Da05656a9b2c8a9C0D',
          'action_code' => 1,
          'uid' => $data['admitad_uid'],
          'order_id' => $data['order_id'],
          'tariff_code' => 1,
          'currency_code' => 'RUB',
          'price' => sprintf('%d', $product['price']),
          'quantity' => $product['quantity'],
          'position_id' => ($pid + 1),
          'position_count' => sizeof($data['products']),
          'product_id' => $product['product_id'],
          'client_id' => '',
          'payment_type' => 'sale',
      ));
      $this->log->write('Admitad URL: ' . $qs);
      $at_curl = curl_init('https://ad.admitad.com/r?' . $qs);
      $cr = curl_setopt_array($at_curl, array(
          CURLOPT_RETURNTRANSFER => true,
      ));
      if ($cr) {
        $res = curl_exec($at_curl);
        if (curl_getinfo($at_curl)['http_code'] != 200) {
          $this->log->write('Admitag says an error: ' . $res);
        }
        $this->log->write('Admitad result: ' . $res);
      } else {
        $this->log->write('Admitad curl error: ' . curl_error($cr));
      }
    }
    
    self::save_order(array(
        'order_id' => $data['order_id'],
        'partner_id' => 1,
        'partner_tag' => $data['admitad_uid']
    ));
    
  }
  
  public function save_order($data) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_partner` SET
      `order_id` = '" . (int)$data['order_id'] . "',
	    `partner_id` = '" . (int)$data['partner_id'] . "',
	    `partner_tag` = '" . $this->db->escape($data['partner_tag']) . "'
    ");
  }
 
}