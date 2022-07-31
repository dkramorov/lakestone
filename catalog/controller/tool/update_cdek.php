<?php


class ControllerToolUpdateCdek extends Controller {
  
  public function index() {
    $this->load->model('extension/shipping/cdek');
    $this->model_extension_shipping_cdek->forceGetList();
  }
  
}