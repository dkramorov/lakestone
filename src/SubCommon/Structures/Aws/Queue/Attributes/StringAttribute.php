<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue\Attributes;

use Lakestone\SubCommon\Structures\Aws\Queue\MessageAttributeAbstract;
use Lakestone\SubCommon\Structures\Aws\Queue\MessageAttributeInterface;

class StringAttribute extends MessageAttributeAbstract {
  
  protected string $AttributeType = MessageAttributeInterface::attributeTypeString;
  
}