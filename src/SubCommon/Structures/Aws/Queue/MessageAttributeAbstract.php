<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue;

class MessageAttributeAbstract implements MessageAttributeInterface {
  
  protected string $attributeValue;
  
  /**
   * Parses AWS SQS MessageAttribute $attribute and sets itself properties
   * @param string|array|null $attribute
   */
  public function __construct(string|array $attribute = null) {
    if ($attribute) {
      if (is_string($attribute)) {
        $this->attributeValue = $attribute;
      } else {
        $this->attributeValue = match ($attribute['DataType']) {
          MessageAttributeInterface::attributeTypeBinary => $attribute['BinaryValue'],
          default => $attribute['StringValue'],
        };
      }
    }
  }
  
  /**
   * @return string
   */
  public function getAttributeType(): string {
    return $this->AttributeType;
  }
  
  /**
   * @param string $AttributeType
   * @return MessageAttributeAbstract
   */
  public function setAttributeType(string $AttributeType): MessageAttributeAbstract {
    $this->AttributeType = $AttributeType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getAttributeValue(): string {
    return match ($this->getAttributeType()) {
      MessageAttributeInterface::attributeTypeString => (string) $this->attributeValue,
      MessageAttributeInterface::attributeTypeNumber => (int) $this->attributeValue,
      MessageAttributeInterface::attributeTypeBinary => (string) $this->attributeValue,
    };
  }
  
  /**
   * @param string $attributeValue
   * @return MessageAttributeAbstract
   */
  public function setAttributeValue(string $attributeValue): MessageAttributeAbstract {
    $this->attributeValue = $attributeValue;
    return $this;
  }
  
}