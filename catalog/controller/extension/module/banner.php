<?php
class ControllerExtensionModuleBanner extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');


		if (empty($setting['template'])) {
			// $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
			// $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.transitions.css');
			// $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
			$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');
			$template = 'extension/module/banner';
		} else {
      $this->load->model('setting/setting');
			$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
			$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
			$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/' . basename($setting['template']) . '.min.css';
			if (file_exists($style_file))
				$this->document->addStyle($style_file);
			$template = $setting['template'];
		}

		$data['banners'] = array();
		$data['Locality'] = $this->session->data['Locality'];

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'text' 	=> $result['text'],
					'button'=> $result['button'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view($template, $data);

	}
}
