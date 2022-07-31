<?php
class ControllerExtensionModuleBannerSmart1 extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');

		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/banner_smart.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

		$data['banners'] = array();
		$data['banners1'] = array();
		$data['banners2'] = array();

		foreach ($this->model_design_banner->getBanner($setting['banner_id']) as $result) {
			$banner = array(
				'title' => $result['title'],
				'text'	=> $result['text'],
				'button' => $result['button'],
				'link'  => $result['link'],
				'width'	=> $setting['width'],
				'height'	=> $setting['height']
			);
			if (is_file(DIR_IMAGE . $result['image'])) {
				$banner['image'] = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
			} else {
				$banner['image'] = $this->model_tool_image->resize('no_image.png', $setting['width'], $setting['height']);
			}
			$data['banners'][] = $banner;
		}

		foreach ($this->model_design_banner->getBanner($setting['banner1_id']) as $result) {
			$banner = array(
				'title' => $result['title'],
				'text'	=> $result['text'],
				'button' => $result['button'],
				'link'  => $result['link'],
				'width'	=> $setting['width1'],
				'height'	=> $setting['height1']
			);
			if (preg_match('|https?://(www\.)?youtube.com/watch\?v=([^&]+)|i', $result['link'], $link_ar)) {
				$banner['video'] = array(
					'href'	=> $result['link'],
					'vid'		=> $link_ar[2],
				);
			} else {
				$banner['video'] = false;
			}

			if (is_file(DIR_IMAGE . $result['image'])) {
				$banner['image'] = $this->model_tool_image->resize($result['image'], $setting['width1'], $setting['height1']);
			} else {
				$banner['image'] = $this->model_tool_image->resize('no_image.png', $setting['width1'], $setting['height1']);
			}
			$data['banners1'][] = $banner;
		}

		foreach ($this->model_design_banner->getBanner($setting['banner2_id']) as $result) {
			$banner = array(
				'title' => $result['title'],
				'text'	=> $result['text'],
				'button' => $result['button'],
				'link'  => $result['link'],
				'width'	=> $setting['width1'],
				'height'	=> $setting['height2']
			);
			if (preg_match('|https?://(www\.)?youtube.com/watch\?v=([^&]+)|i', $result['link'], $link_ar)) {
				$banner['video'] = array(
					'href'	=> $result['link'],
					'vid'		=> $link_ar[2],
				);
			} else {
				$banner['video'] = false;
			}

			if (is_file(DIR_IMAGE . $result['image'])) {
				$banner['image'] = $this->model_tool_image->resize($result['image'], $setting['width1'], $setting['height2']);
			} else {
				$banner['image'] = $this->model_tool_image->resize('no_image.png', $setting['width1'], $setting['height2']);
			}
			$data['banners2'][] = $banner;
		}

		$data['maintitle'] = html_entity_decode($setting['maintitle'], ENT_QUOTES, 'UTF-8');
		$data['title'] = html_entity_decode($setting['title'], ENT_QUOTES, 'UTF-8');
		$data['title1'] = html_entity_decode($setting['title1'], ENT_QUOTES, 'UTF-8');
		$data['title2'] = html_entity_decode($setting['title2'], ENT_QUOTES, 'UTF-8');
		if (empty($setting['article'])) 
                  $data['article'] = "В последнее время в моду входят предметы гардероба, изготовленные из природных материалов. К таким относятся кожаные сумки. Это универсальный аксессуар из натуральной кожи, который отлично вписывается и дополняет любой образ - от роскоши до повседневного стиля, благодаря расцветкам, сочетающимся со всей цветовой гаммой. Существуют как женские кожаные изделия, рюкзаки, так и мужские деловые сумки, портфели, барсетки и клатчи. Их можно взять с собой в путешествие, на прогулку или работу, в обоих случаях они придают владельцу статусности и повышают имидж. В нашем интернет-магазине можно недорого купить качественный аксессуар от производителя и даже попасть на распродажу, получив хорошую сумку по приемлемой цене. Также, предоставляется возможность оформить бесплатную доставку товара на территории России и Казахстана.";
                else
                  $data['article'] = html_entity_decode($setting['article'], ENT_QUOTES, 'UTF-8');

		$data['module'] = $module++;
		if ($data['banners'])
			$data['banner'] = $data['banners'][0];
		if ($data['banners1'])
			$data['banner1'] = $data['banners1'][0];
		if ($data['banners2'])
			$data['banner2'] = $data['banners2'][0];

		if (empty($setting['template'])) {
			$template = 'extension/module/banner_smart1';
		} else {
			$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
			$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
			$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/' . basename($setting['template']) . '.min.css';
			if (file_exists($style_file))
				$this->document->addStyle($style_file);
			$template = $setting['template'];
		}

		return $this->load->view($template, $data);
	}
}
