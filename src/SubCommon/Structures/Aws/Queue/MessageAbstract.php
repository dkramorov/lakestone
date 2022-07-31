<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue;

use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\BinaryAttribute;
use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\NumberAttribute;
use Lakestone\SubCommon\Structures\Aws\Queue\Attributes\StringAttribute;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class MessageAbstract implements AwsQueueMessageStructureInterface {
  /**
   * @var MessageAttributeInterface[]
   */
  protected array $MessageAttributes = [];
  protected string $MessageBody = '';
  protected string $QueueUrl;
  
  public function __construct(?array $message = null) {
    if ($message) {
      $this->setMessageBody($message['Body']);
      foreach ($message['MessageAttributes'] as $name => $attributeArray) {
        $this->MessageAttributes[$name] = match ($attributeArray['DataType']) {
          MessageAttributeInterface::attributeTypeNumber => new NumberAttribute($attributeArray),
          MessageAttributeInterface::attributeTypeBinary => new BinaryAttribute($attributeArray),
          MessageAttributeInterface::attributeTypeString => new StringAttribute($attributeArray),
        };
      }
    }
  }
  
  public function toArray(): array {
    $ret = [];
    $this->collectForeignProperties();
    foreach ((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
      if ($property->getDeclaringClass()->getName() == __CLASS__) {
        $ret[$property->getName()] = match ($property->getName()) {
          'QueueUrl' => $this->{$property->getName()},
          'MessageBody' => $this->{$property->getName()},
          'MessageAttributes' => $this->buildAttributes(),
        };
      }
    }
    return $ret;
  }
  
  protected function collectForeignProperties(): void {
    foreach ((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
      $property->setAccessible(true);
      if (
          $property->getDeclaringClass()->getName() != __CLASS__
          and $property->isInitialized($this)
      ) {
        $this->MessageAttributes[$property->getName()] = $this->{$property->getName()};
      }
    }
  }
  
  protected function buildAttributes(): array {
    $ret = [];
    foreach ($this->MessageAttributes as $name => $value) {
      if ($value === null) {
        continue;
      }
      $ret[$name] = [
          'DataType' => $value->getAttributeType(),
      ];
      $valueNameField = match ($value->getAttributeType()) {
        MessageAttributeInterface::attributeTypeBinary => 'BinaryValue',
        default => 'StringValue',
      };
      $ret[$name][$valueNameField] = $value->getAttributeValue();
    }
    return $ret;
  }
  
  /**
   * @return string
   */
  public function getQueueUrl(): string {
    return $this->QueueUrl;
  }
  
  /**
   * @param string $QueueUrl
   * @return MessageAbstract
   */
  public function setQueueUrl(string $QueueUrl): self {
    $this->QueueUrl = $QueueUrl;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getMessageBody(): mixed {
    if (is_string($this->MessageBody)) {
      $result = $this->MessageBody;
    } else {
      $result = json_decode($this->MessageBody);
    }
    return $result;
  }
  
  /**
   * @param string $MessageBody
   * @return MessageAbstract
   */
  public function setMessageBody(mixed $MessageBody): self {
    if (is_string($MessageBody)) {
      $this->MessageBody = $MessageBody;
    } else {
      $this->MessageBody = json_encode($MessageBody);
    }
    return $this;
  }
  
  /**
   * @return MessageAttributeInterface[]
   */
  public function getMessageAttributes(): array {
    return $this->MessageAttributes;
  }
  
  /**
   * @param array $MessageAttributes
   * @return MessageAbstract
   */
  public function setMessageAttributes(array $MessageAttributes): self {
    $this->MessageAttributes = $MessageAttributes;
    return $this;
  }
  
  public function setMessageAttribute(string $name, MessageAttributeInterface $attribute): self {
    $this->MessageAttributes[$name] = $attribute;
    return $this;
  }
  
  public function getMessageAttribute(string $name): ?MessageAttributeInterface {
    return $this->MessageAttributes[$name] ?? null;
  }
  
  public function hasMessageAttrinute(string $name): bool {
    return isset($this->MessageAttributes[$name]);
  }
  
}