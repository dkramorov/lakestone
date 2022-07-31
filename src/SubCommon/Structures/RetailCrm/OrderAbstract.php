<?php

namespace Lakestone\SubCommon\Structures\RetailCrm;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract\OrderAbstract as OrderOrderAbstract;
use ReflectionClassConstant;

class OrderAbstract implements RetailCrmStructureOrderInterface {
  
  private const sitePrefix = 'site';
  
  protected string $site;
  protected OrderOrderAbstract $order;
  protected string $orderEditBy;

  /**
   * Returns this structure as array
   * @return array
   */
  public function toArray(): array {
    $ret = [
        'site' => $this->getSite(),
        'order' => $this->order->toArray(),
    ];
    return $ret;
  }
  
  /**
   * @return string
   */
  public function getSite(): string {
    return $this->site;
  }
  
  /**
   * Use constants of \Lakestone\SubCommon\Interface\RetailCrmInterface\site* for setting
   * @param string $site
   * @return OrderAbstract
   */
  public function setSite(string $site): OrderAbstract {
    $ret = false;
    foreach ((new \ReflectionClass(RetailCrmInterface::class))->getConstants(ReflectionClassConstant::IS_PUBLIC) as $name => $value) {
      if (
          $site == $value
          and substr($name, 0, strlen(self::sitePrefix)) == self::sitePrefix
      ) {
        $ret = true;
        break;
      }
    }
    if ($ret) {
      $this->site = $site;
    } else {
      throw new \Exception('Unknown site ID');
    }
    return $this;
  }
  
  /**
   * @return OrderOrderAbstract
   */
  public function getOrder(): OrderOrderAbstract {
    return $this->order;
  }
  
  /**
   * @param OrderOrderAbstract $order
   * @return OrderAbstract
   */
  public function setOrder(OrderOrderAbstract $order): OrderAbstract {
    $this->order = $order;
    return $this;
  }

    /**
     * @param string $orderEditBy
     * @return string
     */
    public function getOrderEditBy (): string
    {
        return $this->orderEditBy;
    }

    /**
     * Parameter "By" - required for order edit by id order
     * @param string $orderEditBy
     * @return string
     */
    public function setOrderEditBy (string $orderEditBy): string
    {
        $this->orderEditBy = $orderEditBy;
        return $this->orderEditBy;
    }

    /**
     *
     * @return string
     */
    public function getAction (): string
    {
        return $this->order->getAction();
    }
}