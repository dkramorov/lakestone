<?php
class ControllerExtensionModuleHTMLCode extends Controller {
	public function index($setting) {
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			$data['html_code'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');
			if (!empty($setting['module_description'][$this->config->get('config_language_id')]['template'])) {
				$module_template = $setting['module_description'][$this->config->get('config_language_id')]['template'];
				$this->load->model('setting/setting');
				$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
				$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
				$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/' . basename($module_template) . '.min.css';
				if (file_exists($style_file))
					$this->document->addStyle($style_file);
			} else {
				$module_template = 'extension/module/html_code';
			}

			$data['html_code'] = preg_replace_callback(
				'/{include=(.*)}/',
				function($matches){
					$template = explode('.', basename($matches[1]));
					if (sizeof($template) == 2) {
						$template_name = $template[0];
						$template_ext = $template[1];
					} else {
						$template = false;
					}
					if ($template) {
						$this->load->model('setting/setting');
						$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
						$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
						$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/' . $template_name . '.min.css';
						if (file_exists($style_file))
							$this->document->addStyle($style_file);
					}
					if (isset($this->session->data['data_module']))
						return $this->load->view($matches[1], $this->session->data['data_module']);
					else
						return $this->load->view($matches[1]);
				},
				$data['html_code']
			);

			$data['html_code'] = preg_replace_callback(
				'/{session=(.*)}/',
				function($matches){
					if ( isset($this->session->data[($matches[1])]) )
						return $this->session->data[($matches[1])];
				},
				$data['html_code']
			);

			$data['html_code'] = preg_replace_callback(
				'/{document=(.*)}/',
				function($matches){
					if (isset(get_object_vars($this->document)[($matches[1])]))
						return get_object_vars($this->document)[($matches[1])];
				},
				$data['html_code']
			);

			return $this->load->view($module_template, $data);
		}
	}
}
