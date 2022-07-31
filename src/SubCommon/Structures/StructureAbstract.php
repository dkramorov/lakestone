<?php

namespace Lakestone\SubCommon\Structures;

use Lakestone\SubCommon\Structures\Interface\StructureInterface;
use Lakestone\SubCommon\Structures\RetailCrm\TypeTranslator;
use ReflectionClassConstant;

class StructureAbstract implements StructureInterface {
  
  /**
   * Returns this structure as array
   * @return array
   */
  public function toArray(): array {
    $ret = [];
    foreach ((new \ReflectionClass($this))->getProperties(ReflectionClassConstant::IS_PROTECTED) as $item) {
      $item->setAccessible(true);
      if ($item->isInitialized($this)) {
        $ret[$item->getName()] = $ret[$item->getName()] = TypeTranslator::translate($item->getValue($this));
      }
    }
    return $ret;
  }
}