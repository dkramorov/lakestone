<?php

namespace Lakestone\SubCommon\Structures\Opencart;

use Lakestone\SubCommon\Structures\StructureAbstract;

class Order extends StructureAbstract implements OpencartStructureInterface {

  protected int $order_id;
  /**
   * @var Relation[]
   */
  protected array $relation = [];
  
  /**
   * @param int $order_id
   * @return Order
   */
  public function setOrderId(int $order_id): Order {
    $this->order_id = $order_id;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getOrderId(): int {
    return $this->order_id;
  }
  
  /**
   * @return Relation[]
   */
  public function getRelation(): array {
    return $this->relation;
  }
  
  /**
   * @param Relation $relation
   * @return Order
   */
  public function addRelation(Relation $relation): Order {
    $this->relation[] = $relation;
    return $this;
  }
  
}