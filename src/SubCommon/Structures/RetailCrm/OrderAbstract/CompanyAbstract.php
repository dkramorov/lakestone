<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class CompanyAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  /**
   *
  order[company][id]	integer		ID
  order[company][externalId]	string		Внешний ID
   */
  
}