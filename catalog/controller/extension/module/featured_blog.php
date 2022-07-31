<?php
class ControllerExtensionModuleFeaturedBlog extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured_blog');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/blog');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');

		// $this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		// $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel2.min.js');

		$articles = [];
                $data['articles'] = $articles;
		$data['module'] = '_featured_blog';

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		$data['setting'] = $setting;

		// var_dump($setting);exit;

		if (!empty($setting['article'])) {
			$articles = array_slice($setting['article'], 0, (int)$setting['limit']);

			foreach ($articles as $blog_id) {
				$article_info = $this->model_catalog_blog->getInformation($blog_id);
				// var_dump($article_info);exit;

				if ($article_info) {
					if ($article_info['image']) {
						$image = $this->model_tool_image->resize($article_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					$data['articles'][] = array(
						'blog_id'  		=> $article_info['blog_id'],
						'image'      => $image,
						'name'        => $article_info['title'],
						'description' => utf8_substr(strip_tags(html_entity_decode($article_info['announce'], ENT_QUOTES, 'UTF-8')), 0, 180) . '..',
						'href'        => $this->url->link('information/blog', 'blog_id=' . $article_info['blog_id'])
					);
				}
			}
		}
		$left = $setting['limit'] - sizeof($articles);
		if ($left > 0) {
			foreach ($this->model_catalog_blog->getInformations()['rows'] as $article_info) {
				if (in_array($article_info['blog_id'], $articles)) continue;
				if ($article_info['image']) {
					$image = $this->model_tool_image->resize($article_info['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				$data['articles'][] = array(
					'blog_id'  		=> $article_info['blog_id'],
					'image'      => $image,
					'name'        => $article_info['title'],
					'description' => utf8_substr(strip_tags(html_entity_decode($article_info['announce'], ENT_QUOTES, 'UTF-8')), 0, 180) . '..',
					'href'        => $this->url->link('information/blog', 'blog_id=' . $article_info['blog_id'])
				);
				if ($left-- < 0) break;
			}
		}

		$theme_setting = $this->model_setting_setting->getSetting($this->config->get('config_theme'));
		$theme_dir = $theme_setting[$this->config->get('config_theme') . '_directory'];
		$style_file = DIR_APPLICATION . 'view/theme/' . $theme_dir . '/stylesheet/featured_blog.min.css';
		if (file_exists($style_file))
			$this->document->addStyle($style_file);

		if ($data['articles']) {
			return $this->load->view('extension/module/featured_blog', $data);
		}
	}
}
