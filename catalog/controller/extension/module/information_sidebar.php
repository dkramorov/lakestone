<?php
class ControllerExtensionModuleInformationSidebar extends Controller {
	public function index() {
		$this->load->language('extension/module/information_sidebar');

		#$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['information_id']))
			$information_id = (int)$this->request->get['information_id'];
		else
			$information_id = 0;

		$this->load->model('catalog/information');

		$data['sidebar'] = array();

		$sidebar = $this->model_catalog_information->getSidebar();

		foreach ($sidebar as $item) {
			//var_dump($item);
			if ($item['information_id'] == $information_id)
				$active = true;
			else
				$active = false;
			if ($item['type'] == 1)
				$href = $this->url->link('information/information', array('information_id' => $item['information_id']));
			else
				$href = '';
			if (empty($item['name']))
				$name = $item['title'];
			else
				$name = $item['name'];
			$data['sidebar'][] = array(
				'active'	=> $active,
				'title'		=> $name,
				'href'		=> $href,
				'type'		=> $item['type'],
			);
		}

		return $this->load->view('extension/module/information_sidebar', $data);
	}
}