<?php
class ControllerCommonHome extends Controller {
	public function index() {
	  ////now('start');
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
		$this->document->addLink($this->url->link('common/home'), 'canonical');
    
    ////now('left');
		$data['column_left'] = $this->load->controller('common/column_left');
		////now('right');
		$data['column_right'] = $this->load->controller('common/column_right');
    ////now('top');
    $data['content_top'] = $this->cache->check(
        'home_top',
        function() {
          return $this->load->controller('common/content_top');
        }
    );
		$data['content_top'] = $this->load->controller('common/content_top');
    ////now('bottom');
    $data['content_bottom'] = $this->cache->check(
        'home_bottom',
        function() {
          return $this->load->controller('common/content_bottom');
        }
    );
//		$data['content_bottom'] = $this->load->controller('common/content_bottom');
    //now('footer');
		$data['footer'] = $this->load->controller('common/footer');
    //now('header');
		$data['header'] = $this->load->controller('common/header');
		//now('end');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
