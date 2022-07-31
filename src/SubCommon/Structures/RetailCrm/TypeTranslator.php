<?php

namespace Lakestone\SubCommon\Structures\RetailCrm;

use Lakestone\SubCommon\Structures\Interface\StructureInterface;

class TypeTranslator {
  
  /**
   * returns the correct value of $object by type
   * @param mixed $object
   * @return mixed
   */
  public static function translate(mixed $object): mixed {
    switch (true) {
      case is_object($object):
        $ret = match (true) {
          is_a($object, Date::class) => $object->format('Y-m-d'),
          is_a($object, \DateTime::class) => $object->format('Y-m-d H:i:s'),
          is_a($object, StructureInterface::class) => $object->toArray(),
          default => $object,
        };
        break;
      case is_array($object):
        $ret = self::array($object);
        break;
      default:
        $ret = $object;
    }
    return $ret;
  }
  
  private static function array(mixed $object): array {
    $ret = [];
    foreach ($object as $key => $item) {
      $ret[$key] = self::translate($item);
    }
    return $ret;
  }
  
}