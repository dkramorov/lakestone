<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class CustomerAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected int $id; // Внутренний ID клиента
  protected string $externalId; // Внешний ID клиента
  protected string $browserId; // Идентификатор устройства в Collector
  protected string $site; // Код магазина, необходим при передаче externalId
  protected string $type; // Тип клиента (передаётся когда нужно создать нового клиента)
  protected string $nickName; // Наименование корпоративного клиента (передаётся когда нужно создать нового корпоративного клиента)
  
  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }
  
  /**
   * @param int $id
   * @return CustomerAbstract
   */
  public function setId(int $id): CustomerAbstract {
    $this->id = $id;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getExternalId(): string {
    return $this->externalId;
  }
  
  /**
   * @param string $externalId
   * @return CustomerAbstract
   */
  public function setExternalId(string $externalId): CustomerAbstract {
    $this->externalId = $externalId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBrowserId(): string {
    return $this->browserId;
  }
  
  /**
   * @param string $browserId
   * @return CustomerAbstract
   */
  public function setBrowserId(string $browserId): CustomerAbstract {
    $this->browserId = $browserId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getSite(): string {
    return $this->site;
  }
  
  /**
   * @param string $site
   * @return CustomerAbstract
   */
  public function setSite(string $site): CustomerAbstract {
    $this->site = $site;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }
  
  /**
   * @param string $type
   * @return CustomerAbstract
   */
  public function setType(string $type): CustomerAbstract {
    $this->type = $type;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getNickName(): string {
    return $this->nickName;
  }
  
  /**
   * @param string $nickName
   * @return CustomerAbstract
   */
  public function setNickName(string $nickName): CustomerAbstract {
    $this->nickName = $nickName;
    return $this;
  }
  
}