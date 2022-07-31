<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class ItemPropertyAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $code; // Код свойства (не обязательное поле, код может передаваться в ключе свойства)
  protected string $name; // Имя свойства
  protected string $value; // Значение свойства
  
  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }
  
  /**
   * @param string $code
   * @return ItemPropertyAbstract
   */
  public function setCode(string $code): ItemPropertyAbstract {
    if (!preg_match('/^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/D', $code)) {
      throw new \Exception('Code is not matched for regexp: /^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/D');
    }
    $this->code = $code;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }
  
  /**
   * @param string $name
   * @return ItemPropertyAbstract
   */
  public function setName(string $name): ItemPropertyAbstract {
    $this->name = $name;
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
   * @return ItemPropertyAbstract
   */
  public function setValue(string $value): ItemPropertyAbstract {
    $this->value = $value;
    return $this;
  }
  
}