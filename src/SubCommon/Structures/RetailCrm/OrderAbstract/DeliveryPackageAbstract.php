<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class DeliveryPackageAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  /**
   *
  order[delivery][data][packages][][packageId]	string		Идентификатор упаковки
  order[delivery][data][packages][][weight]	double		Вес г.
  order[delivery][data][packages][][length]	integer		Длина мм.
  order[delivery][data][packages][][width]	integer		Ширина мм.
  order[delivery][data][packages][][height]	integer		Высота мм.
  order[delivery][data][packages][][items][]	array of objects (PackageItem)		Содержимое упаковки
  order[delivery][data][packages][][items][][orderProduct]	object (PackageItemOrderProduct)		Позиция в заказе
  order[delivery][data][packages][][items][][orderProduct][id]	integer		ID позиции в заказе
  order[delivery][data][packages][][items][][orderProduct][externalId]	string		deprecated Внешний ID позиции в заказе
  order[delivery][data][packages][][items][][orderProduct][externalIds][]	array of objects (CodeValueModel)		Внешние идентификаторы позиции в заказе
  order[delivery][data][packages][][items][][orderProduct][externalIds][][code]	string		Код
  order[delivery][data][packages][][items][][orderProduct][externalIds][][value]	string		Значение
  order[delivery][data][packages][][items][][quantity]	double		Количество товара в упаковке
   */
  
}