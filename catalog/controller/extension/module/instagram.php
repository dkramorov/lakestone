<?php

class ControllerExtensionModuleInstagram extends Controller
{
    public function index($setting)
    {
		static $module = 0;

		//$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->model('extension/module/instagram');

		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

        $data['images'] = [];

		foreach ($this->model_extension_module_instagram->getFeed() as $result) {

		    # исключаем из выдачи посты видео, т.к. нет миниатюры . возможно будет какая-то заглушка для таких
            # постов, но пока её нет
		    if (strpos($result['image'], 'https://video', 0) === 0) {
		        continue;
            }

            $cap = explode("\n", $result['caption']);
            if (isset($cap[1]) && $cap[1])
                $title = $cap[1];
            else
                $title = '';

			$data['images'][] = [
                'src'   => $result['image'],
                'desc'	=> $result['caption'],
                'link'	=> $result['link'],
                'title'	=> $title,
            ];
		}

		$data['module'] = $module++;

		$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
		$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
		$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/instagram.min.css';

		if (file_exists($style_file))
			$this->document->addStyle($style_file);

		return $this->load->view('extension/module/instagram', $data);
	}

	public function updateToken ()
    {
        $this->load->model('setting/setting');
        $this->load->model('extension/module/instagram');
        $this->model_extension_module_instagram->updateToken();
    }
}