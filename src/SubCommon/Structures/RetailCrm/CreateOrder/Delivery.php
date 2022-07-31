<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\CreateOrder;

use Lakestone\SubCommon\Interface\OpencartCheckoutInterface;
use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\DeliveryAbstract;

class Delivery extends DeliveryAbstract {
  private array $constructors = [
      OpencartCheckoutInterface::deliveryTypeBoxberry => 'presetBoxberry',
      OpencartCheckoutInterface::deliveryTypeCdek => 'presetCdek',
  ];
  
  /**
   * Use these parameters for setup delivery service and pickpoint
   * @param string $deliveryId
   * @param string $city
   * @param string $pickuppointId
   */
  public function __construct(string $deliveryId = '', string $city = '', string $pickuppointId = '') {
    $method = $this->constructors[$deliveryId] ?? false;
    if (
        $method
        and is_callable([$this, $method])
    ) {
      $this->{$method}($city, $pickuppointId);
    }
  }
  
  private function presetBoxberry(?string $city, ?string $pickuppointId) {
    $this->setCode(RetailCrmInterface::shippingBoxberryProviderCode);
    $data = (new DeliveryData())->setTariff(RetailCrmInterface::shippingBoxberryTariffCode);
    if ($pickuppointId) {
      $data->setPickuppointId($pickuppointId);
    }
    $this->setData($data);
  }
  
  private function presetCdek(?string $city, ?string $pickuppointId) {
    $this->setCode(RetailCrmInterface::shippingCdekProviderCode);
    $data = (new DeliveryData())->setTariffType(RetailCrmInterface::shippingCdekTariffCode);
    if ($pickuppointId) {
      $data->setPickuppointId($pickuppointId);
    }
    if ($city) {
      $data->setReceiverCity($city);
    }
    $this->setData($data);
  }
  
}