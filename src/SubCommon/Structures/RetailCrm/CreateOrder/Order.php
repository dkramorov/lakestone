<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\CreateOrder;

use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\OrderAbstract;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\PaymentAbstract;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\ItemAbstract;
use Lakestone\SubCommon\Interface\RetailCrmInterface;

class Order extends OrderAbstract {
  
  /**
   * @var array<int, ItemAbstract>
   */
  protected array $items = [];
  /**
   * @var array<int, PaymentAbstract>
   */
  protected array $payments = []; // Платежи

    /**
     * @return string
     */
    public function getAction (): string
    {
        $action = RetailCrmInterface::orderActionCreate;
        return $action;
    }
}