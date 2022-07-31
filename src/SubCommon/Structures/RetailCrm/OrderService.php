<?php

namespace Lakestone\SubCommon\Structures\RetailCrm;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\Exception\Exception;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\DeliveryAbstract;

class OrderService {
  
  /**
   * @param OrderAbstract $order
   */
  public function __construct(
      protected OrderAbstract $order
  ) {}
  
  /**
   * Returns the Delivery with preset Data
   * @param string $deliveryId
   * @param string $city
   * @param string $pickuppointId
   * @return RetailCrmStructureInterface Delivery
   * @throws Exception
   */
  public function makeDelivery(string $deliveryId = '', string $city = '', string $pickuppointId = ''): DeliveryAbstract {
    $deliveryClassName = get_class($this->order) . '\Delivery';
    $deliveryDataClassName = get_class($this->order) . '\DeliveryData';
    $delivery = new $deliveryClassName();
    switch ($deliveryId) {
      case 'boxberry':
        $delivery->setCode(RetailCrmInterface::shippingBoxberryProviderCode);
        $data = (new $deliveryDataClassName())->setTariff(RetailCrmInterface::shippingBoxberryTariffCode);
        if ($pickuppointId) {
          $data->setPickuppointId($pickuppointId);
        }
        break;
      case 'cdek':
        $delivery->setCode(RetailCrmInterface::shippingCdekProviderCode);
        $data = (new $deliveryDataClassName())->setTariffType(RetailCrmInterface::shippingCdekTariffCode);
        if ($pickuppointId) {
          $data->setPickuppointId($pickuppointId);
        }
        if ($city) {
          $data->setReceiverCity($city);
        }
        break;
      default:
        throw new Exception('Unknown deliveryId: ' . $deliveryId);
    }
    $delivery->setData($data);
    return $delivery;
  }
  
}