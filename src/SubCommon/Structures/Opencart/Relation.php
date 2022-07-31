<?php

namespace Lakestone\SubCommon\Structures\Opencart;

class Relation {
  
  protected string $type;
  protected mixed $data;
  
  /**
   * @return OpencartStructureInterface
   */
  public function getType(): string {
    return $this->type;
  }
  
  /**
   * @param OpencartStructureInterface $type
   * @return Relation
   */
  public function setType(string $type): Relation {
    $this->type = $type;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getData(): mixed {
    return $this->data;
  }
  
  /**
   * @param mixed $data
   * @return Relation
   */
  public function setData(mixed $data): Relation {
    $this->data = $data;
    return $this;
  }
  
  
}