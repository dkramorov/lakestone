<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class DeliveryServiceAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $name; // Название
  protected string $code; // Символьный код
  protected bool $active; // Статус активности
  protected string $deliveryType; // Тип доставки
  
  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }
  
  /**
   * @param string $name
   * @return DeliveryServiceAbstract
   */
  public function setName(string $name): DeliveryServiceAbstract {
    $this->name = $name;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }
  
  /**
   * @param string $code
   * @return DeliveryServiceAbstract
   */
  public function setCode(string $code): DeliveryServiceAbstract {
    $this->code = $code;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isActive(): bool {
    return $this->active;
  }
  
  /**
   * @param bool $active
   * @return DeliveryServiceAbstract
   */
  public function setActive(bool $active): DeliveryServiceAbstract {
    $this->active = $active;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getDeliveryType(): string {
    return $this->deliveryType;
  }
  
  /**
   * @param string $deliveryType
   * @return DeliveryServiceAbstract
   */
  public function setDeliveryType(string $deliveryType): DeliveryServiceAbstract {
    $this->deliveryType = $deliveryType;
    return $this;
  }
  
}