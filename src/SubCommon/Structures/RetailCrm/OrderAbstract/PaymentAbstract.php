<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class PaymentAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $externalId; // Внешний ID платежа
  protected string $status; // Статус оплаты
  protected string $comment; // Комментарий
  protected string $type; // Тип оплаты
  protected float $amount; // Сумма платежа
  protected ?\DateTime $paidAt; // Дата оплаты
  
  private array $validType = [
      RetailCrmInterface::paymentByCard,
      RetailCrmInterface::paymentByBank,
      RetailCrmInterface::paymentByCash,
  ];
  private array $validStatus = [
      RetailCrmInterface::paymentStatusPaid,
      RetailCrmInterface::paymentStatusNotPaid,
      RetailCrmInterface::paymentStatusFail,
  ];

  /**
   * @return string
   */
  public function getExternalId(): string {
    return $this->externalId;
  }
  
  /**
   * @param string $externalId
   * @return PaymentAbstract
   */
  public function setExternalId(string $externalId): PaymentAbstract {
    $this->externalId = $externalId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }
  
  /**
   * Use one of self::validStatus for setting
   * @param string $status
   * @return PaymentAbstract
   */
  public function setStatus(string $status): PaymentAbstract {
    if (!in_array($status, $this->validStatus)) {
      throw new \Exception('Invalid type of status');
    }
  
    $this->status = $status;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getComment(): string {
    return $this->comment;
  }
  
  /**
   * @param string $comment
   * @return PaymentAbstract
   */
  public function setComment(string $comment): PaymentAbstract {
    $this->comment = $comment;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }
  
  /**
   * Use one of self::validType for setting
   * @param string $type
   * @return PaymentAbstract
   */
  public function setType(string $type): PaymentAbstract {
    if (!in_array($type, $this->validType)) {
      throw new \Exception('Invalid type of payment');
    }
    $this->type = $type;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getAmount(): float {
    return $this->amount;
  }
  
  /**
   * @param float $amount
   * @return PaymentAbstract
   */
  public function setAmount(float $amount): PaymentAbstract {
    $this->amount = $amount;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getPaidAt(): ?\DateTime {
    return $this->paidAt;
  }
  
  /**
   * @param \DateTime $paidAt
   * @return PaymentAbstract
   */
  public function setPaidAt(?\DateTime $paidAt): PaymentAbstract {
    $this->paidAt = $paidAt;
    return $this;
  }
  
  
}