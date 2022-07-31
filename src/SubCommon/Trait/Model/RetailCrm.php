<?php

namespace Lakestone\SubCommon\Trait\Model;

use ArrayObject;
use Exception;
use GuzzleHttp\Client;
use Lakestone\Config;
use Lakestone\Service\Logger;
use Lakestone\SubCommon\Exception\Communiction;
use Lakestone\SubCommon\Exception\PermanentError;
use Lakestone\SubCommon\Interface\AwsServiceInterface;
use Lakestone\SubCommon\Interface\OpencartCheckoutInterface;
use Lakestone\SubCommon\Service\AWS\CloudWatchLogs;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder;
use Lakestone\SubCommon\Structures\RetailCrm\EditOrder;
use Log;
use stdClass;
use Throwable;
use Lakestone\SubCommon\Interface\RetailCrmInterface;

trait RetailCrm {
  
  private $CURL;
  private Client $client;
  private $DefaultFields = array(
      'orderCreate' => array(
//            'paymentType'	=> array('cash', 'payment_method'), // cash/bank-card
          'orderMethod' => array('one-click', 'order_method'), // one-click/shopping-cart
          'customerComment' => array('', 'customerComment'),
          'phone' => array('красный', 'telephone'),
          'firstName' => array('Вова', 'firstname'),
          'email' => array('admin@lakestone.ru', 'email'),
          'call' => array(false, 'callback'),
      ),
  );
  
  public function __construct($registry) {
    parent::__construct($registry);
    $this->MyLog = new Log('retail_crm.log');
  }
  
  public function request($data) {
    $ch = $this->getCURL();
    $param = array_merge(array('apiKey' => Config::getInstance()->getParam('rcrm.key')), $data['fields']);
    if ($data['method'] == 'POST') {
      foreach ($param as $k => $v) {
        if (is_array($v)) {
          $param[$k] = json_encode($v);
        }
      }
    }
    $query = http_build_query($param);
    curl_setopt($ch, CURLOPT_URL, trim(Config::getInstance()->getParam('rcrm.endpoint') . $data['endpoint']));
    if ($data['method'] == 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    } else {
      curl_setopt($ch, CURLOPT_URL, trim(Config::getInstance()->getParam('rcrm.endpoint') . $data['endpoint']) . '?' . $query);
    }
    
    $response = curl_exec($ch);
    $res = json_decode($response);
    
    if ($data['debug'] ?? false) {
      d($query, $res);
    }
    
    if ($res != NULL) {
      return $res;
    } else {
      $error = new stdClass();
      $error->success = false;
      $error->error = 'Ошибка коммуникации с системой "RetailCRM"';
      $error->debug = $response;
      $error->error_message = curl_error($ch);
      $error->info = curl_getinfo($ch);
      (new Logger())->getLogger()->debug('Ошибка коммуникации с системой "RetailCRM"', ['response' => $response]);
      $ret = new stdClass();
      $ret->errors = array($error);
      return $ret;
    }
  }
  
  protected function getObjects($req, $ret_name) {
    try {
      if (!isset($req['fields'])) {
        $req['fields'] = array();
      }
      $page = 1;
      $res = $this->request($req);
      $objects = $res->{$ret_name};
      if (isset($res->pagination) and $res->pagination->totalPageCount > 1) {
        while ($page++ < $res->pagination->totalPageCount) {
          if (sizeof($res->{$ret_name}) == 0) {
            break;
          }
          $req['fields']['page'] = $page;
          $res = $this->request($req);
          $objects = array_merge($objects, $res->{$ret_name});
        }
      }
    } catch (Throwable $e) {
      $this->log->write('RetailCRM getObjects() error: ' . $e->getMessage());
      $this->MyLog->write('getObjects() error: ' . $e->getMessage());
      $this->MyLog->write('Request: ' . json_encode($req ?? ''));
      $this->MyLog->write('Request result: ' . json_encode($res ?? ''));
      $this->MyLog->write('Trace: ' . $e->getTraceAsString());
      $objects = [];
    }
    return $objects;
  }
  
  public function putObjects(array $req, string $ObjName = null, int $limit = 250) {
    $results = [];
    $offset = 0;
    foreach ($req['fields'] as $field => $value) {
      if (is_array($value)) {
        $Objects = (new ArrayObject($value))->getArrayCopy();
        $ObjectName = $field;
      }
    }
    if (empty($ObjectName)) {
      throw new Exception('Objects not found in the request');
    }
    while ($Objects_slice = array_slice($Objects, $offset, $limit)) {
      $req['fields'][$ObjectName] = $Objects_slice;
      $ret = $this->request($req);
      if (!$ret->success) {
        $ret->ExtError = 'putObjects error';
        return $ret;
      }
      $offset += $limit;
      $results[] = (new ArrayObject($ret))->getArrayCopy();
    }
    $ret->PutObjectsResults = $results;
    return $ret;
  }
  
  public function updatePrices(string $streamLogNamePrefix = null): object {
    $streamPrefix = $streamLogNamePrefix ?? date('[d@H.i.s] ');
    $cloudWatch = CloudWatchLogs::getInstance();
    $cloudWatch->setGroupName(AwsServiceInterface::productSyncGroup);
    $this->load->model('extension/module/integration');
    $this->load->model('catalog/product');
    $PriceUpdates = [];
    foreach ($this->getOffers(['active' => 1, 'offerIds' => []]) as $offer) {
      $prod_info = $this->model_extension_module_integration->getIntegrationVariantByCode($offer->article);
      if (empty($prod_info)) {
        $this->log->write('Not found product for price updating: "' . $offer->name . "\"\nOffer:\n" . json_encode($offer));
        $cloudWatch
            ->setupStream($streamPrefix . 'RCRM_errors')
            ->putLogEvent([
                'message' => 'Not found product for price updating: ' . $offer->name,
                'offer' => $offer,
            ]);
        continue;
      }
      if (empty($offer->offers[0])) {
        $this->log->write('Not found RCRM offers: ' . $offer->article);
        continue;
      }
      $product_id = $this->model_extension_module_integration->findProductByModel($prod_info['code']);
      if ($product_id) {
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (!empty($product_info)) {
          if (!empty($product_info['special'])) {
            $price_special = $product_info['special'];
          }
        }
      }
      $update = [];
      $update_str = '';
      foreach ($offer->offers[0]->prices ?? [] as $price) {
        $new_price = null;
        switch ($price->priceType) {
          case 'base':
            $new_price = $prod_info['price'];
            break;
          case 'wholesale':
            $new_price = $prod_info['price1'];
            break;
          case 'sale':
            if (!empty($product_info['special'])) {
              $new_price = $product_info['special'];
            }
            break;
          case 'sale_wholesale':
            $new_price = round($prod_info['price'] * 0.6667); // base-33.33%
            break;
        }
        if (
            $new_price !== null
            and $price->price != $new_price
        ) {
          $update[] = [
              'code' => $price->priceType,
              'price' => $new_price,
          ];
          $update_str .= sprintf('%s = %d > %d, ', $price->priceType, $price->price, $new_price);
        }
      }
      if (!empty($update)) {
        $PriceUpdates[] = [
            'id' => $offer->offers[0]->id,
            'site' => ($offer->manufacturer == 'Lakestone' ? RetailCrmInterface::siteLakestone : RetailCrmInterface::siteBlackwood),
            'prices' => $update,
        ];
        $this->log->write(sprintf(
            'Update prices for "%s": %s',
            $prod_info['code'],
            $update_str,
        ));
        $cloudWatch
            ->setupStream($streamPrefix . $prod_info['code'])
            ->putLogEvent([
                'message' => 'update prices in RCRM',
                'offerInRcrm' => $offer,
                'product_info' => $prod_info,
                'prices' => $update,
            ]);
      }
    }
    if (!empty($PriceUpdates)) {
      $result = $this->putObjects(
          [
              'fields' => array(
                  'prices' => $PriceUpdates,
              ),
              'method' => 'POST',
              'endpoint' => 'store/prices/upload',
          ]
      );
      $cloudWatch
          ->setupStream($streamPrefix . 'RCRM_prices_update')
          ->putLogEvent([
              'message' => 'RCRM request for store/inventories/upload',
              'prices' => $PriceUpdates,
              'response' => $result,
          ]);
      return $result;
    } else {
      return (object)[
          'success' => true,
          'processedOffersCount' => 0,
      ];
    }
  }
  
  public function updateStock(string $streamLogNamePrefix = null) {
    $streamPrefix = $streamLogNamePrefix ?? date('[d@H.i.s] ');
    $cloudWatch = CloudWatchLogs::getInstance();
    $cloudWatch->setGroupName(AwsServiceInterface::productSyncGroup);
    $this->load->model('extension/module/integration');
    $OfferUpdates = [];
    
    foreach ($this->getStocks(['details' => 1]) as $offer) {
      $prod_info = $this->model_extension_module_integration->getIntegrationVariantByRcrmCode($offer->xmlId);
      if (empty($prod_info)) {
        $this->log->write('Not found product for stock updating: ' . $offer->xmlId);
        $cloudWatch
            ->setupStream($streamPrefix . 'RCRM_errors')
            ->putLogEvent([
                'message' => 'Not found product for stock updating: ' . $offer->xmlId,
                'offer' => $offer,
            ]);
        continue;
      }
      $LocalStores = unserialize($prod_info['stores']);
      if ($LocalStores === false) {
        $LocalStores = [];
      }
      $stores_update = [];
      foreach ($offer->stores ?? [] as $store) {
        $new_quantity = null;
        if (isset($LocalStores[$store->store])) {
          $new_quantity = $LocalStores[$store->store] >= 0 ? $LocalStores[$store->store] : 0;
        }
        $update = [
            'code' => $store->store,
        ];
        if (
            $new_quantity !== null
            and $store->quantity != $new_quantity
        ) {
          $update['available'] = $new_quantity;
        }
        if ($offer->purchasePrice != $prod_info['price2']) {
          $update['purchasePrice'] = $prod_info['price2'];
        }
        if (sizeof($update) > 1) {
          $stores_update[] = $update;
          $this->log->write(sprintf(
              'Update stock "%s" for "%s": available = %d > %d, purchasePrice = %d > %d',
              $store->store,
              $prod_info['code'],
              $store->quantity,
              $new_quantity,
              $offer->purchasePrice,
              $prod_info['price2'],
          ));
        }
      }
      if (!empty($stores_update)) {
        $OfferUpdates[$offer->site][] = [
            'id' => $offer->id,
            'stores' => $stores_update,
        ];
        $cloudWatch
            ->setupStream($streamPrefix . $prod_info['code'])
            ->putLogEvent([
                'message' => 'update stores in RCRM',
                'offerInRcrm' => $offer,
                'product_info' => $prod_info,
                'update' => $stores_update,
            ]);
      }
    }
    $results = [];
    foreach ($OfferUpdates as $site => $offer) {
      if (!empty($offer)) {
        $results[$site] = $this->putObjects(
            [
                'fields' => array(
                    'offers' => $offer,
                    'site' => $site,
                ),
//                'debug' => true,
                'method' => 'POST',
                'endpoint' => 'store/inventories/upload',
            ]
        );
        $cloudWatch
            ->setupStream($streamPrefix . 'RCRM_stock_update')
            ->putLogEvent([
                'message' => 'RCRM request for store/inventories/upload',
                'site' => $site,
                'request' => $offer,
                'response' => $results[$site],
            ]);
      }
    }
    return $results;
  }
  
  public function getOrders(array $data = []) {
    $filter = array();
    if (isset($data['crm_ids'])) {
      $filter['ids'] = $data['crm_ids'];
    }
    $filter += $data;
    $req = array(
        'fields' => array(
            'filter' => $filter,
        ),
        'method' => 'GET',
        'endpoint' => 'orders',
    );
    return $this->getObjects($req, 'orders');
  }
  
  public function getStocks(array $filter = []) {
    $req = array(
        'fields' => array(
            'filter' => $filter,
        ),
        'method' => 'GET',
        'endpoint' => 'store/inventories',
    );
    return $this->getObjects($req, 'offers');
  }
  
  public function getOffers(array $filter = []) {
    $req = array(
        'fields' => array(
            'filter' => $filter,
        ),
        'method' => 'GET',
        'endpoint' => 'store/products',
    );
    return $this->getObjects($req, 'products');
  }
  
  public function createOrder($data) {
    $this->load->model('extension/module/integration');
    $this->load->model('catalog/product');
    $this->load->model('checkout/order');
    $this->load->model('account/order');
    $order_info = $this->model_extension_module_integration->getOrderIDs($data['order_id']);
    $order_desc = $this->model_checkout_order->getOrder($data['order_id']);
    $order_total = $this->model_account_order->getOrderTotals($data['order_id']);
    $delivery_data = [];
    $order = [
        'shipmentStore' => 'main_store',
        'payments' => [],
    ];
    if (!empty($data['order_id'])) {
      $order['customFields'] = [
          'eshop_orderid' => $data['order_id'],
      ];
    }
    if (!empty($data['managerComment'])) {
      $order['managerComment'] = $data['managerComment'];
    }
    // customer
    if (!empty($data['customer']['id'])) {
      $order['customer']['id'] = $data['customer']['id'];
    }
    // managerId
    if (!empty($data['managerId'])) {
      $order['managerId'] = $data['managerId'];
    }
    // payment
    $payment_method = 'cash';
    if ($order_desc['payment_method'] == OpencartCheckoutInterface::paymentByBank) {
      $payment_method = RetailCrmInterface::paymentByCard;
    }
    $order['payments'][] = array(
        'amount' => $order_desc['total'],
        'type' => $payment_method,
        'status' => 'not-paid',
    );
    // shipping
    switch ($order_desc['shipping_code']) {
      case 'pickpoint':
        $shipping_data = explode('.', $order_desc['shipping_address_2']);
        switch ($shipping_data[0]) {
          case 'boxberry':
            $provider = RetailCrmInterface::shippingBoxberryProviderCode;
            $delivery_data = [
                'tariff' => RetailCrmInterface::shippingBoxberryTariffCode,
                'pickuppointId' => $shipping_data[1],
            ];
            break;
          case 'cdek':
            $this->load->model('extension/shipping/cdek');
            $provider = RetailCrmInterface::shippingCdekProviderCode;
            $delivery_data = [
                'tariffType' => RetailCrmInterface::shippingCdekTariffCode,
                'pickuppointId' => $shipping_data[1],
                'receiverCity' => $this->model_extension_shipping_cdek->findCityByPP($shipping_data[1]),
            ];
            break;
          default:
            $provider = 'unknown';
        }
        $order['delivery'] = array(
            'code' => $provider,
            'data' => $delivery_data,
            'address' => array(
                'city' => preg_replace('/^г\.?\s{0,}/i', '', $order_desc['shipping_city']),
            ),
        );
        break;
      case 'courier':
        $order['delivery'] = array(
            'code' => 'michail',
            'address' => array(
                'city' => preg_replace('/^г\.?\s{0,}/i', '', $order_desc['shipping_city']),
                'text' => $order_desc['shipping_address_1'],
            ),
        );
        break;
      case 'post':
        $order['delivery'] = array(
            'code' => 'russian-post',
            'address' => array(
                'city' => preg_replace('/^г\.?\s{0,}/i', '', $order_desc['shipping_city']),
                'text' => $order_desc['shipping_address_1'],
            ),
        );
        break;
      case 'showroom':
        $order['delivery'] = array(
            'code' => 'self-delivery',
            'address' => array(
                'city' => preg_replace('/^г\.?\s{0,}/i', '', $order_desc['shipping_city']),
                'text' => $order_desc['shipping_address_1'],
            ),
        );
        break;
      default:
        $order['delivery'] = array(
            'code' => 'unknown',
            'address' => array(
                'city' => preg_replace('/^г\.?\s{0,}/i', '', $order_desc['shipping_city']),
                'text' => $order_desc['shipping_address_1'],
            ),
        );
    }
    foreach ($order_total as $total) {
      if ($total['code'] == 'shipping') {
        $order['delivery']['cost'] = $total['value'];
      }
      if ($total['code'] == 'coupon') {
        $order['discountManualAmount'] = abs((float)$total['value']);
      }
    }
    
    
    foreach ($this->DefaultFields['orderCreate'] as $field => $def) {
      if (isset($data[$def[1]])) {
        $order[$field] = $data[$def[1]];
      } else {
        $order[$field] = $def[0];
      }
    }
    if (
        $order['email'] == 'admin@lakestone.ru' or
        $order['email'] == 'info@lakestone.ru' or
        $order['email'] == 'lakestonebags@gmail.com'
    ) {
      $order['email'] = '';
    }
    $items = array();
    $weight = 0;
    foreach ($data['products'] as $product) {
      $prod_info = $this->model_extension_module_integration->getProductIDs($product['product_id']);
      $prod = $this->model_catalog_product->getProduct($product['product_id']);
      foreach ($this->model_catalog_product->getProductAttributes($product['product_id']) as $attributeGroup) {
        if ($attributeGroup['name'] !== 'Основные') {
          continue;
        }
        foreach ($attributeGroup['attribute'] as $attribute) {
          if ($attribute['name'] == 'Вес, грамм') {
            $weight += intval($attribute['text']);
          }
        }
      }

#            $sku = explode('#', $prod_info['externalCode']);
#            if ( sizeof($sku) > 1)
#                $prod_info['externalCode'] = $sku[1];
      
      if ($prod['special']) {
        $price = $prod['price'];
        $discount = (float)$prod['price'] - (float)$prod['special'];
      } else {
        $price = $product['price'];
        $discount = 0;
      }
      $items[] = array(
          'offer' => array(
//                    'id'		=> $prod_info['internalId'],
              'externalId' => $prod_info['externalCode'] ?? '',
//                    'xmlId'		=> $prod_info['externalCode'],
          ),
          'properties' => array(
              array(
                  'code' => 'code',
                  'name' => 'Код',
                  'value' => (!empty($prod['sku']) ? $prod['sku'] : 'ЗАПОЛНИ В АДМИНКЕ'),
              ),
              array(
                  'code' => 'article',
                  'name' => 'Артикул',
                  'value' => $product['model'],
              ),
          ),
          'quantity' => $product['quantity'],
          'initialPrice' => $price,
          'discountManualAmount' => $discount,
          'productName' => $product['name'],
      );
    }
    $order['weight'] = $weight;
    $order['items'] = $items;
    
    if (isset($_COOKIE['roistat_visit'])) {
      $order['customFields']['roistat'] = $_COOKIE['roistat_visit'];
    }
    
    $req = array(
        'fields' => array(
            'site' => $data['shop'] ?? RetailCrmInterface::siteLakestone,
            'order' => $order,
        ),
        'method' => 'POST',
        'endpoint' => 'orders/create',
    );
//    dd($req);
    $res = $this->request($req);
    $this->MyLog->write('RetailCRM/createOrder debug:');
    $this->MyLog->write($req);
    $this->MyLog->write('the response:');
    $this->MyLog->write($res);
    if ($res->success) {
      $this->model_extension_module_integration->updateOrderCrmID($data['order_id'], $res->id);
    } else {
      $this->MyLog->write('RetailCRM/createOrder has an error with the request:');
      $this->MyLog->write($req);
      $this->MyLog->write('the response:');
      $this->MyLog->write($res);
      $text = '<p>При передаче заказа ' . $data['order_id'] . ' возникли ошибки:</p><ol>';
      $text .= '<li>' . $res->errorMsg . '</li>';
      $text .= '<li>' . print_r($res, true) . '</li>';
      $text .= '</ol><p>Пожалуйста, обработайте заказ ' . $data['order_id'] . ' самостоятельно.</p>';
      $this->model_extension_module_integration->sendReport(array(
          'subject' => 'Ошибка при передеаче заказа ' . $data['order_id'] . ' в систему "Retail CRM"',
          'text' => $text,
      ));
    }
    return $res;
  }
  
  public function sendOrder(object $order): object {
    
    $option = [
        'debug' => true,
        'form_params' => $order->toArray()
    ];
    $option['form_params']['order'] = json_encode($option['form_params']['order']);
    $this->prepareFormField($option['form_params']);
    $requestCounter = 1;
    $action = $order->getAction();
    do {

        if ($order instanceof CreateOrder) {

            $this->MyLog->write('The sending "CreateOrder" to RCRM, attempt: ' . $requestCounter . '. The request:');
            $this->MyLog->write($order->toArray());

            try {

                $res = $this->getClient()->post($action, $option);
                switch ($res->getStatusCode()) {
                    case 201:
                        $ret = json_decode($res->getBody()->getContents());
                        if (!$ret->success ?? false) {
                            $this->MyLog->write('The sending is failed. The response:');
                            $this->MyLog->write($ret);
                            break;
                        }
                        $this->MyLog->write('Sending is completed success. The response:');
                        $this->MyLog->write($ret);
                        break 2;
                    case 401:
                        throw new PermanentError('Invalid request');
                    case 503:
                        sleep(RetailCrmInterface::repeatRequestTimeout);
                        throw new Communiction('Service Temporarily Unavailable');
                }
            } catch (Communiction $e) {
                $this->MyLog->write('Communication error: ' . $e->getMessage());
            } catch (PermanentError|\Throwable $e) {
                $ret = (object) [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
                $this->MyLog->write($e->getMessage());
                $this->MyLog->write('Unfixable error. We won\'t try again.');
                break;
            }

        } elseif ($order instanceof EditOrder) {

            $this->MyLog->write('The sending "EditOrder" to RCRM, attempt: ' . $requestCounter . '. The request:');
            $this->MyLog->write($order->toArray());

            try {

                $res = $this->getClient()->post($action, $option);
                switch ($res->getStatusCode()) {
                    case 200:
                        $ret = json_decode($res->getBody()->getContents());
                        if (!$ret->success ?? false) {
                            $this->MyLog->write('The sending is failed. The response:');
                            $this->MyLog->write($ret);
                            break;
                        }
                        $this->MyLog->write('Sending is completed success. The response:');
                        $this->MyLog->write($ret);
                        break 2;
                    case 400:
                        throw new PermanentError('Invalid request');
                    case 500:
                        sleep(RetailCrmInterface::repeatRequestTimeout);
                        throw new Communiction('Service Temporarily Unavailable');
                }
            } catch (Communiction $e) {
                $this->MyLog->write('Communication error: ' . $e->getMessage());
            } catch (PermanentError|\Throwable $e) {
                $ret = (object) [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
                $this->MyLog->write($e->getMessage());
                $this->MyLog->write('Unfixable error. We won\'t try again.');
                break;
            }
        }
    }

    while ($requestCounter++ <= RetailCrmInterface::repeatRequestAttempt);
    
    return $ret;
  }
  
  public function createOrderItemByProductCode(string $product_code): CreateOrder\Item {
    $this->load->model('extension/module/integration');
    $product = $this->model_extension_module_integration->getIntegrationVariantByCode($product_code);
    if (empty($product)) {
      throw new Exception('Product not found', 404);
    }
    return
        (new CreateOrder\Item())
            ->setProductName($product['name'])
            ->setOffer(
                (new CreateOrder\Offer())
//                    ->setExternalId($product['externalCode'])
                    ->setXmlId($this->model_extension_module_integration->getRcrmCodeByCode($product_code))
            );
  }
  
  /**
   * Prepares data for RetailCRM API
   * @param array <string, mixed> $formFields
   * @return void
   */
  private function prepareFormField(array &$formFields): void {
    foreach ($formFields as $key => &$value) {
      if (is_array($value)) {
        $value = json_encode($value);
      }
    }
  }
  
  protected function getCURL() {
    if ($this->CURL === NULL) {
      $this->CURL = curl_init();
      curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
    }
    return $this->CURL;
  }
  
  protected function getClient() {
    if (empty($this->client)) {
      $this->client = new Client([
          'base_uri' => Config::getInstance()->getParam('rcrm.endpoint'),
          'headers' => [
              'X-API-KEY' => Config::getInstance()->getParam('rcrm.key'),
              'Content-Type' => 'application/x-www-form-urlencoded',
          ],
      ]);
    }
    return $this->client;
  }
  
}
