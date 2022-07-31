<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControllerToolSubSync
 *
 * @author mic
 */
class ControllerToolSubSync extends Controller {

  private $config_sub = 'config_sub.php';
  private $light = true;
  private $Log;
  const SyncUpdateField = 'SubSyncUpdated';

  public function index() {

    if (!$this->user->isLogged() and ! (
            !empty($this->request->get['key']) and
            $this->request->get['key'] == 'f0EuzCTJ5RTQ0RQMSWuIVQHVGA1V0pRMN04GVR0cTteUziCe'
            )) {
      echo 'Access deny';
      return;
    }

    if (file_exists($this->config_sub))
      include($this->config_sub);
    if (!isset($SUB_DOMAIN) or empty($SUB_DOMAIN))
      return;
  
    $this->Log = new Log('sub_sync.log');
    $this->Log->write('Start sync at ' . date('r'));

    $this->registry->set('locking', new Locking($this->registry));
    if (
        !$this->config->get('dev')
        and !$this->locking->lock('SubSyncIntegration', 15 * 60)
    ) {
      $this->Log->write('access temporary locked');
      echo "block";
      return false;
    }

    set_time_limit(0);
    ignore_user_abort(TRUE);
    
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('catalog/seo_link');
    $this->load->model('tool/status');
  
    
    if (isset($this->request->get['full'])) {
      $this->light = false;
    }

    $this->status_name = 'sub_sync' . (!$this->light ? '_full' : '');
    $this->model_tool_status->start($this->status_name);

    foreach ($SUB_DOMAIN as $domain) {
      $db = [];
      if ($domain['Locality'] !== 'default' and isset($domain['DB_DATABASE']) and ! empty($domain['DB_DATABASE'])) {
        $db = ['DB_DATABASE' => $domain['DB_DATABASE']];
        foreach (['DB_DRIVER', 'DB_PREFIX', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD', 'DB_PORT'] as $param) {
          if (isset($domain[$param]) and ! empty($domain[$param]))
            $db[$param] = $domain[$param];
          else
            $db[$param] = constant($param);
        }
        echo 'Processing DB: ' . $domain['DB_DATABASE'] . ' ';
        $registry = $this->getRegistry($db);
  
        if (!$this->light) {
          $this->copyTable($registry, 'category_product_link', 'link_id');
          $this->copyTable($registry, 'category_link', 'link_id');
          $this->copyTable($registry, 'review', 'review_id');
          $this->copyTable($registry, 'respond', 'review_id');
          $this->copyTable($registry, 'coupon', 'coupon_id');
          $this->copyTable($registry, 'coupon_category', 'coupon_id');
          $this->copyTable($registry, 'coupon_product', 'coupon_product_id');
          $this->copyTable($registry, 'filter', 'filter_id');
          $this->copyTable($registry, 'filter_description', 'filter_id');
          $this->copyTable($registry, 'filter_group', 'filter_group_id');
          $this->copyTable($registry, 'filter_group_description', 'filter_group_id');
          $this->copyTable($registry, 'product_special', 'product_special_id');
          $this->copyTable($registry, 'product_discount', 'product_discount_id');
          $this->copyTable($registry, 'product_integration', 'product_id');
          $this->copyTable($registry, 'integration_product', 'id', '');
          $this->copyTable($registry, 'integration_variant', 'id', '');
          $this->copyTable($registry, 'accessories_links', 'category_id');
          $this->syncProducts($registry);
          $this->syncCategories($registry);
          $this->syncSeoLinks($registry);
        } else {
          $this->syncProducts($registry);
        }
        echo " OK\n";
      }
    }
    if (!$this->light) {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET `need_spread` = 0");
      $this->db->query("UPDATE " . DB_PREFIX . "category SET `need_spread` = 0");
    }
  
  
    $this->locking->unlock('SubSyncIntegration');
    $this->model_tool_status->done($this->status_name, 1);
  }

  private function syncProducts($registry) {

    $loader = $registry->get('load');
    $loader->model('catalog/product');
    $pm = $registry->get('model_catalog_product');
    $db = $registry->get('db');
    $this->dropUpdatedStatus($db, 'product');

    foreach ($this->model_catalog_product->getProducts() as $prod) {
      echo '.';
      $p1 = $pm->getProduct($prod['product_id']);
      $product = $this->model_catalog_product->getProduct($prod['product_id']);
      if (!$this->light and (empty($p1) or $product['need_spread'])) {
        $this->Log->write('Full update for: ' . $prod['name'] . '(' . $prod['product_id'] . ')');
        $product['product_link'] = $this->model_catalog_product->getProductLinks($prod['product_id']);
        $product['product_description'] = $this->model_catalog_product->getProductDescriptions($prod['product_id']);
        $product['product_image'] = $this->model_catalog_product->getProductImages($prod['product_id']);
        $product['product_store'] = $this->model_catalog_product->getProductStores($prod['product_id']);
        $product['product_attribute'] = $this->model_catalog_product->getProductAttributes($prod['product_id']);
        $product['product_option'] = $this->model_catalog_product->getProductOptions($prod['product_id']);
        $product['product_discount'] = $this->model_catalog_product->getProductDiscounts($prod['product_id']);
        $product['product_special'] = $this->model_catalog_product->getProductSpecials($prod['product_id']);
        $product['product_category'] = $this->model_catalog_product->getProductCategories($prod['product_id']);
        $product['product_filter'] = $this->model_catalog_product->getProductFilters($prod['product_id']);
        $product['product_related'] = $this->model_catalog_product->getProductRelated($prod['product_id']);
        $product['product_layout'] = $this->model_catalog_product->getProductLayouts($prod['product_id']);
        $pm->deleteProduct($prod['product_id']);
        $pm->addProduct($product, $prod['product_id']);
      } else {
        $this->Log->write('Light update for: ' . $prod['name'] . '(' . $prod['product_id'] . ')');
        $db->query("UPDATE " . DB_PREFIX . "product SET "
                . "price = '" . (float) $prod['price'] . "', "
                . "quantity = '" . (int) $prod['quantity'] . "', "
                . "image = '" . $this->db->escape($product['image']) . "', "
                . "status = '" . (int) $prod['status'] . "' "
                . "WHERE product_id = '" . (int) $prod['product_id'] . "'"
        );
        if (!$this->light) {
          $db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $prod['product_id'] . "'");
          foreach ($this->model_catalog_product->getProductAttributes($prod['product_id']) as $product_attribute) {
            if ($product_attribute['attribute_id']) {
              $db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int) $prod['product_id'] . "' AND attribute_id = '" . (int) $product_attribute['attribute_id'] . "'");
              foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
                $db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int) $prod['product_id'] . "', attribute_id = '" . (int) $product_attribute['attribute_id'] . "', language_id = '" . (int) $language_id . "', text = '" . $this->db->escape($product_attribute_description['text']) . "'");
              }
            }
          }
          $product_images = $this->model_catalog_product->getProductImages($prod['product_id']);
          if (sizeof($product_images) > 0) {
            $db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '". (int) $prod['product_id'] ."'");
          }
          foreach ($product_images as $product_image) {
            $db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '". (int) $prod['product_id'] ."', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int) $product_image['sort_order'] . "', position = '" . (int) $product_image['position'] . "'");
          }
        }
      }
      $db->query("UPDATE " . DB_PREFIX . "product SET "
        . self::SyncUpdateField . " = NOW() "
        . "WHERE product_id = '" . (int) $prod['product_id'] . "'"
      );
    }
    $this->offDropped($db, 'product');
  }

  private function syncCategories($registry) {

    $loader = $registry->get('load');
    $loader->model('catalog/category');
    $loader->model('catalog/product');
    $cm = $registry->get('model_catalog_category');
    $cp = $registry->get('model_catalog_product');
    $db = $registry->get('db');
    $this->dropUpdatedStatus($db, 'category');

    foreach ($this->model_catalog_category->getCategories() as $cat) {
      echo '.';
      $c1 = $cm->getCategory($cat['category_id']);
      if (!$this->light and (empty($c1) or $cat['need_spread'])) {
        $category = $this->model_catalog_category->getCategory($cat['category_id']);
        $category['category_link'] = $this->model_catalog_category->getCategoryLinks($cat['category_id']);
        $category['category_product_link'] = $this->model_catalog_category->getCategoryProductLinks($cat['category_id']);
        $category['category_description'] = $this->model_catalog_category->getCategoryDescriptions($cat['category_id']);
        $category['category_store'] = $this->model_catalog_category->getCategoryStores($cat['category_id']);
        $category['category_layout'] = $this->model_catalog_category->getCategoryLayouts($cat['category_id']);
        $category['category_filter'] = $this->model_catalog_category->getCategoryFilters($cat['category_id']);
        $cm->deleteCategory($cat['category_id']);
        $cm->addCategory($category, $cat['category_id']);
        $category_products = $this->model_catalog_category->getCategoryProducts($cat['category_id']);
        foreach ($category_products as $product) {
          $cp->addProduct2Category($product['product_id'], $cat['category_id']);
        }
      } else {
        $db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int) $cat['category_id'] . "'");
        foreach ($this->model_catalog_category->getCategoryFilters($cat['category_id']) as $filter_id) {
          $db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int) $cat['category_id'] . "', filter_id = '" . (int) $filter_id . "'");
        }
        $category = $this->model_catalog_category->getCategory($cat['category_id']);
        $db->query("UPDATE " . DB_PREFIX . "category SET "
                . "status = '" . (int) $category['status'] . "' "
                . "WHERE category_id = '" . (int) $category['category_id'] . "'"
        );
      }
      $db->query("UPDATE " . DB_PREFIX . "category SET "
        . self::SyncUpdateField . " = NOW() "
        . "WHERE category_id = '" . (int) $cat['category_id'] . "'"
      );
    }
    $this->offDropped($db, 'category');
  }

  private function syncSeoLinks($registry) {

    $loader = $registry->get('load');
    $loader->model('catalog/seo_link');
    $loader->model('catalog/product');
    $cm = $registry->get('model_catalog_seo_link');
    $db = $registry->get('db');
    $this->dropUpdatedStatus($db, 'seo_link');

    foreach ($this->model_catalog_seo_link->getLinks() as $link) {
      echo '.';
      $c1 = $cm->getLink($link['seo_link_id']);
      if (!$this->light and (empty($c1) or ($link['need_spread'] ?? false))) {
        $seo_link = $this->model_catalog_seo_link->getLink($link['seo_link_id']);
        $seo_link['seo_link_description'] = $this->model_catalog_seo_link->getLinkDescriptions($link['seo_link_id']);
        $seo_link['seo_link_filter'] = $this->model_catalog_seo_link->getLinkFilters($link['seo_link_id']);
        $cm->deleteLink($link['seo_link_id']);
        $cm->addLink($seo_link, $link['seo_link_id']);
      } else {
        $db->query("DELETE FROM " . DB_PREFIX . "seo_link_filter WHERE seo_link_id = '" . (int)$link['seo_link_id'] . "'");
        foreach ($this->model_catalog_seo_link->getLinkFilters($link['seo_link_id']) as $filter_id) {
          $db->query("INSERT INTO " .  DB_PREFIX .  "seo_link_filter SET
          seo_link_id = '" . (int)$link['seo_link_id'] .  "',
          filter_id = '" . (int)$filter_id .  "'");
        }
        $seo_link = $this->model_catalog_seo_link->getLink($link['seo_link_id']);
        $db->query("UPDATE " . DB_PREFIX . "seo_link SET "
                . "status = '" . (int) $seo_link['status'] . "' "
                . "WHERE seo_link_id = '" . (int) $link['seo_link_id'] . "'"
        );
        $db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seo_link_id=" . (int) $link['seo_link_id'] . "'");
        $db->query("INSERT INTO " . DB_PREFIX . "url_alias SET "
                . "query = 'seo_link_id=" . (int) $link['seo_link_id'] . "', "
                . "keyword = '" . $this->db->escape($seo_link['keyword']) . "' "
        );
      }
      $db->query("UPDATE " . DB_PREFIX . "seo_link SET "
        . self::SyncUpdateField . " = NOW() "
        . "WHERE seo_link_id = '" . (int) $link['seo_link_id'] . "'"
      );
    }
    $this->offDropped($db, 'seo_link');
  }

  private function getRegistry($db) {

    $registry = new Registry();

    // Config
    $config = new Config();
    $config->load('default');
    $config->load('admin');
    $registry->set('config', $config);
    $config->set('config_language_id', 2);

    // Event
    $registry->set('event', new Event($registry));

    // Loader
    $loader = new Loader($registry);
    $registry->set('load', $loader);

    $registry->set('db', new DB(
                    $db['DB_DRIVER'],
                    $db['DB_HOSTNAME'],
                    $db['DB_USERNAME'],
                    $db['DB_PASSWORD'],
                    $db['DB_DATABASE'],
                    $db['DB_PORT'])
    );

    // Cache 
    $registry->set('cache', new Cache($config->get('cache_type'), $config->get('cache_expire')));

    // Config Autoload
    if ($config->has('config_autoload')) {
      foreach ($config->get('config_autoload') as $value) {
        $loader->config($value);
      }
    }

    // Model Autoload
    if ($config->has('model_autoload')) {
      foreach ($config->get('model_autoload') as $value) {
        $loader->model($value);
      }
    }

    return $registry;
  }

  private function dropUpdatedStatus($db, $table, string $db_prefix = DB_PREFIX) {
    $needAlterTable = true;
    $query = $db->query("DESCRIBE " . $db_prefix . $table);
    foreach ($query->rows as $row) {
      if ($row['Field'] == self::SyncUpdateField) {
        $needAlterTable = false;
        break;
      }
    }
    if ($needAlterTable) {
      $db->query("ALTER TABLE " . $db_prefix . $table . " ADD " . self::SyncUpdateField . " DATETIME DEFAULT NULL");
    } else {
      $db->query("UPDATE " . $db_prefix . $table . " SET " . self::SyncUpdateField . " = NULL");
    }
  }

  private function offDropped($db, $table, string $db_prefix = DB_PREFIX) {
    $db->query("UPDATE " . $db_prefix . $table . " SET status = 0 WHERE " . self::SyncUpdateField . " is NULL");
  }

  private function copyTable($registry, $table, $pk, string $db_prefix = DB_PREFIX) {
    $db = $registry->get('db');
    $table = $this->db->escape($table);
    $db_prefix = $this->db->escape($db_prefix);
    
    // copy the table if needed
    $res = $db->query("SHOW TABLES LIKE '" . $db_prefix . $table . "'");
    if ($res->num_rows == 0) {
      $query = $this->db->query("SHOW CREATE TABLE " . $db_prefix . $table);
      $db->query($query->row['Create Table']);
    } else {
      $sql = '';
      $src_table = explode("\n", $this->db->query("SHOW CREATE TABLE " . $db_prefix . $table)->row['Create Table']);
      $dst_table = explode("\n", $db->query("SHOW CREATE TABLE " . $db_prefix . $table)->row['Create Table']);
      foreach ([&$src_table, &$dst_table] as &$arr) {
        foreach ($arr as $num => &$str) {
          $str = trim($str);
          $str = preg_replace('/DEFAULT \'(\d+(\.\d+)?)\'/', 'DEFAULT \1', $str);
          $str = preg_replace('/,$/', '', $str);
          if (preg_match('/^CREATE TABLE/', $str)) unset($arr[$num]);
          elseif (preg_match('/^\) ENGINE=/', $str)) unset($arr[$num]);
          elseif (preg_match('/^`SubSyncUpdated`/', $str)) unset($arr[$num]);
        }
      }
      $table_diff = array_diff($src_table, $dst_table);
      if (sizeof($table_diff) > 0) {
        foreach ($table_diff as $str) {
          $str = $this->db->escape(preg_replace('/,$/', '', trim($str)));
          $db->query("ALTER TABLE ". $db_prefix . $table ." ADD " . $str);
        }
      }
    }
    
    $status_field = false;
    $this->dropUpdatedStatus($db, $table, $db_prefix);
    $query = $this->db->query("SELECT * FROM " . $db_prefix . $table);
    foreach ($query->rows as $row) {
      echo '.';
      $sql = "INSERT INTO " . $db_prefix . $table . " SET ";
      $sql .= "`" . $pk . "` = '" . $row[$pk] . "' ,";
      $field_list = "`" . self::SyncUpdateField . "` = NOW(), ";
      foreach ($row as $field => $value) {
        if ($field == $pk) continue;
        if ($field == 'status') $status_field = true;
        $field_list .= "`" . $field . "` = '" . $db->escape($value) . "', ";
      }
      $field_list = substr($field_list, 0, -2);
      $sql .= $field_list . " ON DUPLICATE KEY UPDATE " . $field_list;
//      dd($sql);
      $db->query($sql);
    }
    if ($status_field) {
      $this->offDropped($db, $table);
    } else {
      $db->query("DELETE FROM " . $db_prefix . $table . " WHERE " . self::SyncUpdateField . " is NULL");
    }
  }

}
