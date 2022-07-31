<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class OfferAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected int $id;
  protected string $externalId;
  protected string $xmlId;
  
  /**
   * ID торгового предложения
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }
  
  /**
   * ID торгового предложения
   * @param int $id
   * @return OfferAbstract
   */
  public function setId(int $id): OfferAbstract {
    $this->id = $id;
    return $this;
  }
  
  /**
   * Внешний ID торгового предложения
   * @return string
   */
  public function getExternalId(): string {
    return $this->externalId;
  }
  
  /**
   * Внешний ID торгового предложения
   * @param string $externalId
   * @return OfferAbstract
   */
  public function setExternalId(string $externalId): OfferAbstract {
    $this->externalId = $externalId;
    return $this;
  }
  
  /**
   * ID торгового предложения в складской системе
   * @return string
   */
  public function getXmlId(): string {
    return $this->xmlId;
  }
  
  /**
   * ID торгового предложения в складской системе
   * @param string $xmlId
   * @return OfferAbstract
   */
  public function setXmlId(string $xmlId): OfferAbstract {
    $this->xmlId = $xmlId;
    return $this;
  }
  
}