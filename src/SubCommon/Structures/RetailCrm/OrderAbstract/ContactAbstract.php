<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class ContactAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  /**
   *
  order[contact][id]	integer		Внутренний ID клиента
  order[contact][externalId]	string		Внешний ID клиента
  order[contact][browserId]	string		Идентификатор устройства в Collector
  order[contact][site]	string		Код магазина, необходим при передаче externalId
   */
}