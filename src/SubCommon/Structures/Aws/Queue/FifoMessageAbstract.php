<?php

namespace Lakestone\SubCommon\Structures\Aws\Queue;

use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class FifoMessageAbstract extends MessageAbstract implements AwsQueueMessageStructureInterface {
  protected string $MessageGroupId;
  protected string $MessageDeduplicationId;
  
  /**
   * @return string
   */
  public function getMessageGroupId(): string {
    return $this->MessageGroupId;
  }
  
  /**
   * @return string
   */
  public function getMessageDeduplicationId(): string {
    return $this->MessageDeduplicationId;
  }
  
  /**
   * @param string $MessageDeduplicationId
   * @return OrderMessage
   */
  public function setMessageDeduplicationId(string $MessageDeduplicationId): self {
    $this->MessageDeduplicationId = $MessageDeduplicationId;
    return $this;
  }
  
  /**
   * @param string $MessageGroupId
   * @return OrderMessage
   */
  public function setMessageGroupId(string $MessageGroupId): self {
    $this->MessageGroupId = $MessageGroupId;
    return $this;
  }
}