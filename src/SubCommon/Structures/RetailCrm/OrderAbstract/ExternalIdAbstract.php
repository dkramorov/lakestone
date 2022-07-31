<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class ExternalIdAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $code; // Код
  protected string $value; // Значение
  
  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }
  
  /**
   * @param string $code
   * @return ExternalIdAbstract
   */
  public function setCode(string $code): ExternalIdAbstract {
    $this->code = $code;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getValue(): string {
    return $this->value;
  }
  
  /**
   * @param string $value
   * @return ExternalIdAbstract
   */
  public function setValue(string $value): ExternalIdAbstract {
    $this->value = $value;
    return $this;
  }
  
}