<?php

namespace Lakestone\SubCommon\Trait\Model;

use Exception;
use Mail;

trait Integration {
  
  public function updateStock($product_id, $data) {
    // price1 - sale
    // price2 - buy
    $sql = '';
    if (isset($data['quantity'])) {
      $sql .= ", `quantity` = '" . (int)$data['quantity'] . "'";
    }
    if (isset($data['price']) and $data['price'] > 0) {
      $sql .= ", `price` = '" . (float)$data['price'] . "'";
    }
    if ($sql != '') {
      $this->db->query("UPDATE `" . DB_PREFIX . "product` SET " . substr($sql, 1) . " WHERE `product_id` = '" . (int)$product_id . "'");
    }
    
    $sql = '';
    if (isset($data['price1']) and $data['price1'] > 0) {
      $sql .= ", `price1` = '" . (float)$data['price1'] . "'";
      $res = $this->db->query("SELECT `price` FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' AND `priority` = 100500");
      if ($res->num_rows == 0) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "product_discount` SET `priority` = 100500, `product_id` = '" . (int)$product_id . "', `quantity` = 10, `price` = '" . (float)$data['price1'] . "', `customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "'");
      } else {
        $this->db->query("UPDATE `" . DB_PREFIX . "product_discount` SET `price` = '" . (float)$data['price1'] . "' WHERE `product_id` = '" . (int)$product_id . "' AND `priority` = 100500");
      }
    }
    if (isset($data['price2']) and $data['price2'] > 0) {
      $sql .= ", `price2` = '" . (float)$data['price2'] . "'";
    }
    if ($sql != '') {
      $this->db->query("UPDATE `" . DB_PREFIX . "product_integration` SET " . substr($sql, 1) . " WHERE `product_id` = '" . (int)$product_id . "'");
    }
  }
  
  public function updateProductIDs($product_id, $internalId, $externalCode, $EAN = 0) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "product_integration` SET
                `product_id` = '" . (int)$product_id . "',
                `internalId` = '" . $this->db->escape($internalId) . "',
                `externalCode` = '" . $this->db->escape($externalCode) . "'
                ON DUPLICATE KEY UPDATE
                `internalId` = '" . $this->db->escape($internalId) . "',
                `externalCode` = '" . $this->db->escape($externalCode) . "'
            ");
    if ($EAN) {
      $this->db->query("UPDATE `" . DB_PREFIX . "product` SET
                    `ean` = '" . $this->db->escape($EAN) . "',
                    `mpn` = '" . $this->db->escape($EAN) . "'
                    WHERE `product_id` = '" . (int)$product_id . "'
                ");
    }
  }
  
  public function updateProductEAN($product_id, $EAN) {
    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET
                `ean` = '" . $this->db->escape($EAN) . "',
                `mpn` = '" . $this->db->escape($EAN) . "'
                WHERE `product_id` = '" . (int)$product_id . "'
            ");
  }
  
  public function updateOrderIDs($order_id, $internalId, $externalCode) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_integration` SET
                `order_id` = '" . (int)$order_id . "',
                `internalId` = '" . $this->db->escape($internalId) . "',
                `externalCode` = '" . $this->db->escape($externalCode) . "'
                ON DUPLICATE KEY UPDATE
                `internalId` = '" . $this->db->escape($internalId) . "',
                `externalCode` = '" . $this->db->escape($externalCode) . "'
            ");
  }
  
  public function updateOrderCrmID($order_id, $crmId) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "order_integration` SET
                `order_id` = '" . (int)$order_id . "',
                `crmId` = '" . $this->db->escape($crmId) . "'
                ON DUPLICATE KEY UPDATE
                `crmId` = '" . $this->db->escape($crmId) . "'
            ");
  }
  
  public function getProductIDs($product_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_integration` WHERE `product_id` = '" . (int)$product_id . "'");
    return $query->row;
  }
  
  public function getOrderIDs($order_id) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_integration` WHERE `order_id` = '" . (int)$order_id . "'");
    return $query->row;
  }
  
  public function findProductByModel($model) {
    $query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `model` = '" . $this->db->escape($model) . "'");
    
    if ($query->num_rows) {
      return $query->row['product_id'];
    } else {
      return false;
    }
  }
  
  public function createProduct($data) {
    $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "product` SET
            `model` = '" . $this->db->escape($data['model']) . "',
            `sku` = '" . $this->db->escape($data['article']) . "',
            `price` = '" . sprintf('%0.2f', $data['price']) . "'
        ");
    $product_id = $this->db->getLastId();
    $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET
            `product_id` = '" . (int)$product_id . "',
            `name` = '" . $this->db->escape($data['name']) . "',
            `meta_title` = '" . $this->db->escape($data['name']) . "',
            `language_id` = '" . (int)$this->config->get('config_language_id') . "'
        ");
    $query = $this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET
            `product_id` = '" . (int)$product_id . "',
            `store_id` = '" . (int)$this->config->get('config_store_id') . "'
        ");
    return $product_id;
  }
  
  public function sendReport($data) {
    $mail = new Mail();
    $mail->protocol = $this->config->get('config_mail_protocol');
    $mail->parameter = $this->config->get('config_mail_parameter');
    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
    $mail->setTo($this->config->get('config_email'));
    $mail->setFrom($this->config->get('config_email'));
    $mail->setSender(html_entity_decode('Автобот интернет-магазина', ENT_QUOTES, 'UTF-8'));
    $mail->setSubject(html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8'));
    $mail->setHtml($data['text']);
    $mail->setText(strip_tags(str_replace(array('<br>', '<br/>'), "\n\r", $data['text'])));
    $mail->send();
  }
  
  public function getOrders($data) {
    $ret = array();
    $query = $this->db->query("SELECT oi.`crmId`, oi.`order_id`
            FROM `" . DB_PREFIX . "order_integration` as oi
            LEFT JOIN `" . DB_PREFIX . "order` as o ON o.`order_id` = oi.`order_id`
            WHERE o.`order_status_id` = '" . (int)$data['filter']['order_status_id'] . "'
        ");
    foreach ($query->rows as $row) {
      if (!empty($row['crmId'])) {
        $ret[$row['order_id']] = array(
            'crmID' => (int)$row['crmId'],
        );
      }
    }
    return $ret;
  }
  
  public function getRcrmCodeByCode(string $product_code): ?string {
    $query = $this->db->query("select concat(ip.externalCode, '#', iv.externalCode) rcrmOffer
      from integration_variant iv
               left join integration_product ip on iv.parentId = ip.id
      where iv.code = '" . $this->db->escape($product_code) . "';
    ");
    return $query->row['rcrmOffer'] ?? null;
  }
  
  public function updateOrderStatus($order_id, $order_status_id) {
    $this->db->query("UPDATE `" . DB_PREFIX . "order` set `order_status_id` = '" . (int)$order_status_id . "' WHERE `order_id` = '" . (int)$order_id . "'");
  }
  
  public function updateIntegrationProduct(string $id, array $data) {
    
    if (empty($id)) {
      throw new Exception('id not defined');
    }
    $sql = '';
    foreach ($data as $key => $val) {
      $sql .= "`$key` = '" . $this->db->escape($val) . "',";
    }
    $sql = substr($sql, 0, -1);
    $this->db->query("INSERT INTO `integration_product` SET " .
        "`id` = '" . $this->db->escape($id) . "', " .
        $sql .
        " ON DUPLICATE KEY UPDATE " .
        $sql
    );
    
  }
  
  public function updateIntegrationVariant(string $id, array $data) {
    
    if (empty($id)) {
      throw new Exception('id not defined');
    }
    
    $sql = '';
    foreach ($data as $key => $val) {
      $sql .= "`$key` = '" . $this->db->escape($val) . "',";
    }
    $sql = substr($sql, 0, -1);
    $this->db->query("INSERT INTO `integration_variant` SET " .
        "`id` = '" . $this->db->escape($id) . "', " .
        $sql .
        " ON DUPLICATE KEY UPDATE " .
        $sql
    );
    
  }
  
  public function getIntegrationProductByCode(string $code) {
    
    $res = $this->db->query("SELECT * FROM `integration_product` WHERE code = '" . $this->db->escape($code) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationVariantByCode(string $code) {
    
    $res = $this->db->query("SELECT * FROM `integration_variant` WHERE code = '" . $this->db->escape($code) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationVariantByExternalCode(string $code) {
    
    $res = $this->db->query("SELECT * FROM `integration_variant` WHERE externalCode = '" . $this->db->escape($code) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationProductByExternalCode(string $code) {
    
    $res = $this->db->query("SELECT * FROM `integration_product` WHERE externalCode = '" . $this->db->escape($code) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationVariantByRcrmCode(string $code) {
    
    $res = $this->db->query("SELECT IF(iv.price>0, iv.price, ip.price) price, IF(iv.price1>0, iv.price1, ip.price1) price1, IF(iv.price2>0, iv.price2, ip.price2) price2, iv.quantity, iv.code, concat(ip.externalCode, '#', iv.externalCode) rcrmOffer, iv.stores FROM `integration_variant` iv left join integration_product ip on iv.parentId = ip.id having rcrmOffer = '" . $this->db->escape($code) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationProduct(string $id) {
    
    $res = $this->db->query("SELECT * FROM `integration_product` WHERE id = '" . $this->db->escape($id) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationVariant(string $id) {
    
    $res = $this->db->query("SELECT * FROM `integration_variant` WHERE id = '" . $this->db->escape($id) . "'");
    return $res->row;
    
  }
  
  public function getIntegrationVariants() {
    
    $res = $this->db->query("SELECT * FROM `integration_variant`");
    return $res->rows;
    
  }
  
  public function findIntegrationVariant(array $data) {
    
    $sql = "SELECT * FROM `integration_variant`";
    
    $where = '';
    
    if (!empty($data['code'])) {
      $where .= " AND `code` LIKE '" . $this->db->escape($data['code']) . "'";
    }
    
    if (!empty($where)) {
      $sql .= ' WHERE ' . substr($where, 4);
    }
    
    $res = $this->db->query($sql);
    return $res->rows;
    
  }
  
}