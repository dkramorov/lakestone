<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Interface\OpencartCheckoutInterface;
use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\Date;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class DeliveryAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $code; // Код типа доставки
  protected DeliveryDataAbstract $data; // Данные службы доставки, подключенной через API
  protected DeliveryServiceAbstract $service;
  protected float $cost; // Стоимость доставки
  protected float $netCost; // Себестоимость доставки
  protected Date $date; // Дата доставки
  protected DeliveryTimeAbstract $time; // Информация о временном диапазоне
  protected AddressAbstract $address; // Адрес доставки
  protected string $vatRate; // Ставка НДС
  
  private array $validCode = [
      RetailCrmInterface::shippingCdekProviderCode,
      RetailCrmInterface::shippingBoxberryProviderCode,
  ];
  
  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }
  
  /**
   * @param string $code
   * @return DeliveryAbstract
   */
  public function setCode(string $code): DeliveryAbstract {
    $this->code = $code;
    return $this;
  }
  
  /**
   * @return DeliveryDataAbstract
   */
  public function getData(): DeliveryDataAbstract {
    return $this->data;
  }
  
  /**
   * @param DeliveryDataAbstract $data
   * @return DeliveryAbstract
   */
  public function setData(DeliveryDataAbstract $data): DeliveryAbstract {
    $this->data = $data;
    return $this;
  }
  
  /**
   * @return DeliveryServiceAbstract
   */
  public function getService(): DeliveryServiceAbstract {
    return $this->service;
  }
  
  /**
   * @param DeliveryServiceAbstract $service
   * @return DeliveryAbstract
   */
  public function setService(DeliveryServiceAbstract $service): DeliveryAbstract {
    $this->service = $service;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getCost(): float {
    return $this->cost;
  }
  
  /**
   * @param float $cost
   * @return DeliveryAbstract
   */
  public function setCost(float $cost): DeliveryAbstract {
    $this->cost = $cost;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getNetCost(): float {
    return $this->netCost;
  }
  
  /**
   * @param float $netCost
   * @return DeliveryAbstract
   */
  public function setNetCost(float $netCost): DeliveryAbstract {
    $this->netCost = $netCost;
    return $this;
  }
  
  /**
   * @return Date
   */
  public function getDate(): Date {
    return $this->date;
  }
  
  /**
   * @param Date $date
   * @return DeliveryAbstract
   */
  public function setDate(Date $date): DeliveryAbstract {
    $this->date = $date;
    return $this;
  }
  
  /**
   * @return DeliveryTimeAbstract
   */
  public function getTime(): DeliveryTimeAbstract {
    return $this->time;
  }
  
  /**
   * @param DeliveryTimeAbstract $time
   * @return DeliveryAbstract
   */
  public function setTime(DeliveryTimeAbstract $time): DeliveryAbstract {
    $this->time = $time;
    return $this;
  }
  
  /**
   * @return AddressAbstract
   */
  public function getAddress(): AddressAbstract {
    return $this->address;
  }
  
  /**
   * @param AddressAbstract $address
   * @return DeliveryAbstract
   */
  public function setAddress(AddressAbstract $address): DeliveryAbstract {
    $this->address = $address;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getVatRate(): string {
    return $this->vatRate;
  }
  
  /**
   * @param string $vatRate
   * @return DeliveryAbstract
   */
  public function setVatRate(string $vatRate): DeliveryAbstract {
    $this->vatRate = $vatRate;
    return $this;
  }
  
}