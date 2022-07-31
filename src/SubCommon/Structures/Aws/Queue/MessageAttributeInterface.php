<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue;

interface MessageAttributeInterface {

  const attributeTypeString = 'String';
  const attributeTypeNumber = 'Number';
  const attributeTypeBinary = 'Binary';
  
  /**
   * @return string
   */
  public function getAttributeValue(): string;

  /**
   * @param string $attributeValue
   * @return MessageAttributeAbstract
   */
  public function setAttributeValue(string $attributeValue): MessageAttributeAbstract;
  
  /**
   * @return string
   */
  public function getAttributeType(): string;
  
  /**
   * @param string $AttributeType
   * @return MessageAttributeAbstract
   */
  public function setAttributeType(string $AttributeType): MessageAttributeAbstract;
  
  
  
}