<?php

namespace Lakestone\SubCommon\Interface;

interface MoyskladInterface {
  
  /**
   * Stores
   */
  
  /**
   * идентификатор атрибута *RCRM* склада МойСклад, в котором можно записать идентификатор склада RCRM,
   * который соответствует этому складу МойСклад.<br>
   * Для примера:<br>
   * На МойСклад есть склад "Основной склад" с атрибутом RCRM = main_store<br>
   * Таким образом, "Основной склад" на Мойсклад соответствует складу "main_store" на RCRM
   */
  const storeAttributeWithRcrmId = 'd6eed667-9208-11eb-0a80-030a001cba3e';
  
}