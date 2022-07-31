<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\EditOrder;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\OrderAbstract;

class Order extends OrderAbstract {
    protected $orderId;

    /**
     * @return string
     */
    public function getAction (): string
    {
        $action = str_ireplace('#ORDER_ID#', $this->orderId, RetailCrmInterface::orderActionEdit);
        return $action;
    }

    /**
     * @param int $id
     * @return int
     */
    public function setOrderId (int $id): Order
    {
        $this->orderId = $id;
        return $this;
    }
}