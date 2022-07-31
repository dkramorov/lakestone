<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\Interface\StructureInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class DeliveryTimeAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected \DateTime $from; // Время "с"
  protected \DateTime $to; // Время "до"
  protected string $custom; // Временной диапазон в свободной форме
  
  /**
   * @return \DateTime
   */
  public function getFrom(): \DateTime {
    return $this->from;
  }
  
  /**
   * @param \DateTime $from
   * @return DeliveryTimeAbstract
   */
  public function setFrom(\DateTime $from): DeliveryTimeAbstract {
    $this->from = $from;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getTo(): \DateTime {
    return $this->to;
  }
  
  /**
   * @param \DateTime $to
   * @return DeliveryTimeAbstract
   */
  public function setTo(\DateTime $to): DeliveryTimeAbstract {
    $this->to = $to;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCustom(): string {
    return $this->custom;
  }
  
  /**
   * @param string $custom
   * @return DeliveryTimeAbstract
   */
  public function setCustom(string $custom): DeliveryTimeAbstract {
    $this->custom = $custom;
    return $this;
  }
  
}