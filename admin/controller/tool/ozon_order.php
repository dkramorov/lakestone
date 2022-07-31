<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Address;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Contragent;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Customer;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Delivery;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Order;
use Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Payment;
use Lakestone\SubCommon\Structures\RetailCrm\Date;
use Lakestone\SubCommon\Structures\RetailCrm\EditOrder as EditOrder;
use Lakestone\SubCommon\Structures\RetailCrm\EditOrder\ {
    Order as EditOrderOrder,
};

/**
 * Description of ozon
 *
 * @author mic
 */
class ControllerToolOzonOrder extends Controller {
  
  private $OzonProducts = [];
  
  public function index() {
    
  }
  
  public function sync() {
    
    
    if (
        !$this->user->isLogged()
        and !(
            !empty($this->request->get['key'])
            and $this->request->get['key'] == 'GMbRZckWzLm4yjQEcPZ0fFSx1EKwwTV7'
        )
    ) {
      echo 'Access deny';
      return;
    }
  
    if (isset($this->request->get['status'])) {
      return $this->updateOrderStatusRetailCrm();
    }
    
      if (
        !$this->config->get('dev')
        and $this->cache->get('OzonOrderSyncLock')
    ) {
      echo "block";
      return False;
    }
    
    set_time_limit(0);
    ignore_user_abort(TRUE);
    
    $this->cache->set('OzonOrderSyncLock', true);
    $Log = new Log('ozon_order.log');
    
    $Log->write('Ozon updater has started');
    
    $this->load->model('catalog/product');
    $this->load->model('catalog/category');
    $this->load->model('catalog/attribute');
    $this->load->model('extension/ozon/api');
    $this->load->model('extension/ozon/order');
    $this->load->model('extension/module/retailcrm');
    $this->load->model('tool/status');
    
    $this->model_tool_status->start('ozon_order');
    
    $counter = 0;
    
    foreach ($this->model_extension_ozon_api->getOrdersV3(arrayResult: true) as $order) {
      $Log->write('Got Ozon order:' . $order['order_number']);
      $MyOrder = $this->model_extension_ozon_order->getOrder($order['order_number']);
      
      if (!empty($MyOrder)) {
        $Log->write(sprintf(
            "already registered in my db: %s (%s)",
            $MyOrder['crm_id'],
            $MyOrder['crm_site']
        ));
        continue;
      }
      $rcrm_order = $this->model_extension_module_retailcrm->getOrders(['customFields' => [RetailCrmInterface::customerFieldOzonOrderId => $order['posting_number']]]);
      if (!empty($rcrm_order)) {
        $Log->write(sprintf(
            'already registered in RCRM: %s (%s)',
            $rcrm_order[0]->number ?? '-',
            $rcrm_order[0]->site ?? '-'
        ));
        $this->model_extension_ozon_order->updateOrder($order['order_number'], [
            'status' => $order['status'],
            'ozon_date' => $order['created_at'],
            'crm_id' => $rcrm_order[0]->number,
            'crm_date' => $rcrm_order[0]->createdAt,
            'crm_site' => $rcrm_order[0]->site,
        ]);
        continue;
      }
      $Log->write("Creating a new order for RCRM");
  
      /***************************************************/
      /***************************************************/
      $createOrder = new Lakestone\SubCommon\Structures\RetailCrm\CreateOrder();
      $newOrder = new Order();
  
      $site = null;
      foreach ($order['products'] as $item) {
        $products = $this->model_catalog_product->getProducts(['filter_model' => $item['offer_id']]);
        $newItem = $this->model_extension_module_retailcrm->createOrderItemByProductCode($item['offer_id']);
        $newItem
            ->setInitialPrice((float)$item['price'])
            ->setQuantity((int)$item['quantity']);
        $newOrder->addItem($newItem);
        if (!$site) {
          if (empty($products)) {
            $site = RetailCrmInterface::siteBlackwood;
          } else {
            $site = RetailCrmInterface::siteLakestone;
          }
        }
      }
      $newOrder
          ->setContragent(
              (new Contragent())
                  ->setContragentType(RetailCrmInterface::contragentTypeLegalEntity)
                  ->setLegalName('ООО "Интернет решения"')
                  ->setLegalAddress('123112, ГОРОД МОСКВА, НАБЕРЕЖНАЯ ПРЕСНЕНСКАЯ, ДОМ 10, ПОМЕЩЕНИЕ I ЭТ 41 КОМН 6')
                  ->setINN('7704217370')
                  ->setKPP('770301001')
                  ->setOGRN('1027739244741')
          )
          ->addPayment(
              (new Payment())
                  ->setStatus(RetailCrmInterface::paymentStatusNotPaid)
                  ->setType(RetailCrmInterface::paymentByBank)
          )
          ->setCustomer(
              (new Customer())
                  ->setId(34082)
          )
          ->setDelivery(
              (new Delivery())
                  ->setAddress((new Address())->setCity($order['analytics_data']['city'] ?? null ?: 'Москва'))
                  ->setDate(new Date($order['shipment_date']))
          )
          ->setStatus(RetailCrmInterface::orderStatusNewMP)
          ->setOrderType(RetailCrmInterface::orderTypeMarketplace)
          ->setOrderMethod(RetailCrmInterface::orderMethodPhone)
          ->setDiscountManualPercent(5)
          ->setCreatedAt(new DateTime($order['created_at'] ?? ''))
          ->setFirstName('Ozon')
          ->setManagerComment($order['posting_number'])
          ->addCustomField(RetailCrmInterface::customerFieldOzonOrderId, $order['posting_number'])
      ;
  
      $createOrder
          ->setOrder($newOrder)
          ->setSite($site)
      ;
      $res = $this->model_extension_module_retailcrm->sendOrder($createOrder);
  
      /***************************************************/
      /***************************************************/
      if ($res->success) {
        $this->model_extension_ozon_order->updateOrder($order['order_number'], [
            'status' => $order['status'] ?? '',
            'ozon_date' => $order['created_at'] ?? null,
            'crm_id' => $res->order->number,
            'crm_date' => $res->order->createdAt,
            'crm_site' => $res->order->site,
        ]);
      }
      $counter++;
    }
    $Log->write('Ozon updater has finished');
    $this->cache->set('OzonOrderSyncLock', false);
    $this->model_tool_status->done('ozon_order', 1, $counter);
  }

    /**
     * Обновляет в retailcrm статус заказа для заказов Ozon со статусом в Ozon "Отменено"
     * @return int
     */
    public function updateOrderStatusRetailCrm (): int
    {
        set_time_limit(0);
        ignore_user_abort(TRUE);

        $this->cache->set('OzonOrderSyncLock', true);
        $Log = new Log('ozon_order.log');

        $Log->write('Ozon update orders status has started');

        $this->load->model('extension/ozon/api');
        $this->load->model('extension/ozon/order');
        $this->load->model('extension/module/retailcrm');
        $this->load->model('tool/status');

        $ozonDatePeriod = '-1 month';
        $ozonOrdersLimit = 1000;
        $status = 'cancelled';

        $counter = 0;

        $ozonOrders = $this->model_extension_ozon_api->getOrdersV3('', $ozonDatePeriod, $ozonOrdersLimit, $status, arrayResult: true);

        if (eRU($ozonOrders)) {

            foreach ($ozonOrders as $order) {

                $Log->write('Got Ozon order:' . $order['order_number'] . ', date: ' . $order['in_process_at']);

                $myOrder = $this->model_extension_ozon_order->getOrder($order['order_number']);

                if (empty($myOrder)) {
                    $Log->write(sprintf("not registered in lk db: %s (%s)", $myOrder['crm_id'] ?? '-', $myOrder['crm_site'] ?? '-'));
                    continue;
                }

                $rcrm_order = $this->model_extension_module_retailcrm->getOrders(['customFields' => [RetailCrmInterface::customerFieldOzonOrderId => $order['posting_number']]]);

                if (empty($rcrm_order)) {
                    $Log->write(sprintf('not registered in RCRM: %s (%s)', $rcrm_order[0]->number ?? '-', $rcrm_order[0]->site ?? '-'));
                    continue;
                } elseif (in_array($rcrm_order[0]->status, [
                    RetailCrmInterface::orderStatusDublicate,
                    RetailCrmInterface::orderStatusDeliveryCallFailed,
                    RetailCrmInterface::orderStatusNotFound,
                    RetailCrmInterface::orderStatusCanceledBeforeDelivered,
                    RetailCrmInterface::orderStatusCanceledInDelivered,
                    RetailCrmInterface::orderStatusReturnedInThirtyDays,
                ])) {
                    $Log->write(sprintf('status "%s" in exception list to cancel update order status in RetailCrm (order %s)', $rcrm_order[0]->status, $rcrm_order[0]->number));
                    continue;
                }

                $newOrder = new EditOrderOrder();
                $editOrder = new EditOrder();

                if ($rcrm_order[0]->status == RetailCrmInterface::orderStatusVozvratOzon) {
                    $Log->write(sprintf('the same order statuses (%s) in Ozon and RetailCrm (order %s)', $rcrm_order[0]->number ?? '-', $rcrm_order[0]->number ?? '-'));
                    continue;
                } else {
                    $orderRcrmStatus = RetailCrmInterface::orderStatusVozvratOzon;
                }

                $newOrder
                    ->setStatus($orderRcrmStatus)
                    ->setOrderId(intval($myOrder['crm_id']));

                $editOrder
                    ->setOrder($newOrder)
                    ->setSite($myOrder['crm_site'])
                    ->setOrderEditBy('id');

                /*save2FileVariable([
                    'order id wb: ' => $order['posting_number'],
                    'order id rc: ' => $myOrder['crm_id'],
                    'site' => $myOrder['crm_site'],
                    'status' => $orderRcrmStatus,
                ], 'update.order.status.txt', FILE_APPEND);*/

                $res = $this->model_extension_module_retailcrm->sendOrder($editOrder, 'orders/' . intval($myOrder['crm_id']) . '/edit');

                if ($res->success) {
                    $this->model_extension_ozon_order->updateOrder($order['order_number'], [
                        'status' => $order['status'] ?? '',
                        'ozon_date' => $order['in_process_at'] ?? null,
                        'crm_id' => $res->order->number,
                        'crm_date' => $res->order->createdAt,
                        'crm_site' => $res->order->site,
                    ]);

                    $Log->write('update order ' . $myOrder['crm_id'] . ' set status ' . $orderRcrmStatus);
                }

                $counter++;
            }
        } else {
            $Log->write('Noone orders or error on Ozon server');
        }

        $Log->write('Ozon update orders status has finished');
        $this->cache->set('OzonOrderSyncLock', false);
        $this->model_tool_status->done('ozon_order', 1, $counter);

        return $counter;
    }
}