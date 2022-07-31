<?php

class ControllerToolTest extends Controller {
  
  public function index() {
    $newOrder =
        (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder())
            ->setSite(\Lakestone\SubCommon\Interface\RetailCrmInterface::siteLakestone)
            ->setOrder(
                (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Order())
                    ->setNumber(1)
                    ->addItem(
                        (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Item())
                            ->setOffer(
                                (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Offer())
                                    ->setXmlId('qwe#123')
                            )
                    )
                    ->addItem(
                        (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Item())
                            ->setOffer(
                                (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Offer())
                                    ->setXmlId('asd#456')
                                    ->setExternalId('qweqwe')
                            )
                    )
                    ->addPayment(
                        (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Payment())
                            ->setType(\Lakestone\SubCommon\Interface\RetailCrmInterface::paymentByCash)
                            ->setAmount(200)
                    )
                    ->setDelivery(
                        (new \Lakestone\SubCommon\Structures\RetailCrm\CreateOrder\Delivery(\Lakestone\SubCommon\Interface\OpencartCheckoutInterface::deliveryTypeCdek, 1, 2))
                )
            );
  
    $this->load->model('extension/module/retailcrm');
//    dd($this->model_extension_module_retailcrm->sendOrder($newOrder));
    dd($newOrder->toArray());
    
    $dt = new DateTime();
    $this->response->addHeader('Last-Modified: ' . $dt->format('D, j M Y H:i:s') . ' GMT');
    //$this->log->write($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
      try {
        $md = new DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        $this->log->write($dt);
        $this->log->write('md:');
        $this->log->write($md);
        if ($dt < $md) {
          header('HTTP/1.0 304 Not Modified');
          exit;
        }
      } catch (Exception $e) {
        $this->log->write($e->getMessage());
      }
    }
    
    $this->response->setOutput($this->load->view('common/empty', array()));
    
  }
}
