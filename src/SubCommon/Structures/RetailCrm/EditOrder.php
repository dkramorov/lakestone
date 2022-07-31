<?php

namespace Lakestone\SubCommon\Structures\RetailCrm;

class EditOrder extends OrderAbstract {
    /**
     * Returns this structure as array
     * @return array
     */
    public function toArray(): array {

        $ret = [
            'site' => $this->getSite(),
            'order' => $this->order->toArray(),
            'by' => $this->getOrderEditBy(),
        ];

        return $ret;
    }
}