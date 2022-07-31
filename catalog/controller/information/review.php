<?php
class ControllerInformationReview extends Controller {

    public function index() {
    
        $this->load->model('extension/module/market');
        var_dump($this->model_extension_module_market->getShopsOpinions(386426));
    
    }

}
?>
