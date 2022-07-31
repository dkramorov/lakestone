<?php

namespace Lakestone\SubCommon\Structures\Opencart;

use Lakestone\SubCommon\Structures\Interface\StructureInterface;

interface OpencartStructureInterface extends StructureInterface {
  
  const relationType_Admitad = 'admitad';

  const stageCreateOrder_create = 'create';
  const stageCreateOrder_push = 'push';
  const stageCreateOrder_pull = 'pull';
  
}