<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class PriceTypeAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $code; // Код типа цены
  
  private array $validCode = [
      RetailCrmInterface::priceTypeRegular,
      RetailCrmInterface::priceTypeSale,
      RetailCrmInterface::priceTypeWholesale,
      RetailCrmInterface::priceTypeSaleWholesale,
  ];
  
  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }
  
  /**
   * use $this->$validType for correct value
   * @param string $code
   * @return PriceTypeAbstract
   */
  public function setCode(string $code): PriceTypeAbstract {
    if (!in_array($code, $this->validCode)) {
      throw new \Exception('Invalid code');
    }
    $this->code = $code;
    return $this;
  }
  
}