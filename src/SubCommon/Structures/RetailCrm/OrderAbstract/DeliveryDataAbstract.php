<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Interface\OpencartCheckoutInterface;
use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class DeliveryDataAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $externalId; // Идентификатор в службе доставки
  protected string $trackNumber; // Номер отправления (поле deprecated на запись)
  protected bool $locked; // Не синхронизировать со службой доставки
  protected string $tariff; // Код тарифа
  protected string $pickuppointId; // Идентификатор пункта самовывоза
  protected string $payerType; // Плательщик за доставку
  protected string $shipmentpointId; // Идентификатор терминала отгрузки
  protected array $extraData; // Дополнительные данные доставки (deliveryDataField.code => значение)
  
  // только для СДЭК
  protected string $tariffType; // Тариф для СДЭК
  protected string $receiverCity; // id города из справочника СДЭКа

  /**
   * @var array <int, DeliveryPackage>
   */
  protected array $packages = []; // Упаковки
  
  private array $validTariffType = [
      RetailCrmInterface::shippingCdekTariffCode,
  ];
  private array $validTariff = [
      RetailCrmInterface::shippingBoxberryTariffCode,
  ];
  
  /**
   * @return string
   */
  public function getExternalId(): string {
    return $this->externalId;
  }
  
  /**
   * @param string $externalId
   * @return DeliveryDataAbstract
   */
  public function setExternalId(string $externalId): DeliveryDataAbstract {
    $this->externalId = $externalId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getTrackNumber(): string {
    return $this->trackNumber;
  }
  
  /**
   * @param string $trackNumber
   * @return DeliveryDataAbstract
   */
  public function setTrackNumber(string $trackNumber): DeliveryDataAbstract {
    $this->trackNumber = $trackNumber;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isLocked(): bool {
    return $this->locked;
  }
  
  /**
   * @param bool $locked
   * @return DeliveryDataAbstract
   */
  public function setLocked(bool $locked): DeliveryDataAbstract {
    $this->locked = $locked;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getTariff(): string {
    return $this->tariff;
  }
  
  /**
   * use $this->validTariff for correct value
   * @param string $tariff
   * @return DeliveryDataAbstract
   */
  public function setTariff(string $tariff): DeliveryDataAbstract {
    if (!in_array($tariff, $this->validTariff)) {
      throw new \Exception('Invalid tariff');
    }
    $this->tariff = $tariff;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getPickuppointId(): string {
    return $this->pickuppointId;
  }
  
  /**
   * @param string $pickuppointId
   * @return DeliveryDataAbstract
   */
  public function setPickuppointId(string $pickuppointId): DeliveryDataAbstract {
    $this->pickuppointId = $pickuppointId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getPayerType(): string {
    return $this->payerType;
  }
  
  /**
   * @param string $payerType
   * @return DeliveryDataAbstract
   */
  public function setPayerType(string $payerType): DeliveryDataAbstract {
    $this->payerType = $payerType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getShipmentpointId(): string {
    return $this->shipmentpointId;
  }
  
  /**
   * @param string $shipmentpointId
   * @return DeliveryDataAbstract
   */
  public function setShipmentpointId(string $shipmentpointId): DeliveryDataAbstract {
    $this->shipmentpointId = $shipmentpointId;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getExtraData(): array {
    return $this->extraData;
  }
  
  /**
   * @param array $extraData
   * @return DeliveryDataAbstract
   */
  public function setExtraData(array $extraData): DeliveryDataAbstract {
    $this->extraData = $extraData;
    return $this;
  }
  
  /**
   * @return array <int, DeliveryPackage>
   */
  public function getPackages(): array {
    return $this->packages;
  }
  
  /**
   * @param DeliveryPackageAbstract $package
   * @return DeliveryDataAbstract
   */
  public function addPackage(DeliveryPackageAbstract $package): DeliveryDataAbstract {
    $this->packages[] = $package;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getTariffType(): string {
    return $this->tariffType;
  }
  
  /**
   * use $this->validTariffType for correct value
   * @param string $tariffType
   * @return DeliveryDataAbstract
   */
  public function setTariffType(string $tariffType): DeliveryDataAbstract {
    if (!in_array($tariffType, $this->validTariffType)) {
      throw new \Exception('Invalid tariffType');
    }
    $this->tariffType = $tariffType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getReceiverCity(): string {
    return $this->receiverCity;
  }
  
  /**
   * @param string $receiverCity
   * @return DeliveryDataAbstract
   */
  public function setReceiverCity(string $receiverCity): DeliveryDataAbstract {
    $this->receiverCity = $receiverCity;
    return $this;
  }
  
}