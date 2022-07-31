<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue;

use Lakestone\SubCommon\Interface\AwsServiceInterface;
use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\BinaryAttribute;
use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\NumberAttribute;
use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\StringAttribute;
use Lakestone\SubCommon\Structures\Opencart\Relation;

class OrderMessage extends MessageAbstract {
  
  protected NumberAttribute|MessageAttributeInterface $orderId;
  protected BinaryAttribute|MessageAttributeInterface|null $relations = null;
  
  /**
   * Parses AWS SQS Message $message and sets itself properties
   * @param array|null $message
   */
  public function __construct(?array $message = null) {
    $this->setQueueUrl(AwsServiceInterface::queueOrderUrl);
    $this->setMessageBody('Opencart Order');
    if ($message) {
      parent::__construct($message);
      $this->orderId = $this->getMessageAttribute('orderId');
      if ($this->hasMessageAttrinute('relations')) {
        $this->relations = $this->getMessageAttribute('relations');
      }
    }
  }
  
  /**
   * @return int
   */
  public function getOrderId(): int {
    return $this->orderId->getAttributeValue();
  }
  
  /**
   * @param int $orderId
   * @return OrderMessage
   */
  public function setOrderId(int $orderId): OrderMessage {
    $this->orderId = new NumberAttribute($orderId);
    return $this;
  }
  
  /**
   * @return Relation[]
   */
  public function getRelations(): array {
    $ret = [];
    if ($this->relations !== null) {
      $ret = unserialize($this->relations->getAttributeValue());
    }
    return $ret;
  }
  
  /**
   * @param Relation[] $relations
   * @return $this
   */
  public function setRelations(array $relations): self {
    $this->relations = new BinaryAttribute(serialize($relations));
    return $this;
  }
  
}