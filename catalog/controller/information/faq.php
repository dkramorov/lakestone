<?php
class ControllerInformationFaq extends Controller {
	public function index() {
		$this->load->language('information/faq');

		$this->load->model('catalog/faq');

		$this->document->setTitle('Часто задаваемые вопросы (FAQ)');
		$this->document->setDescription('Подробные ответы на часто задаваемые вопросы покупателей интернет-магазина LAKESTONE');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/faq.min.css');
		// $this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('list_title'),
			'href' => $this->url->link('information/faq')
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['faq_cats'] = array();

		$phone_link = sprintf(
			'<a class="phone_link" rel="nofollow" href="tel:%s">%s</a>',
			str_replace(array(' ', '(', ')', '-'), '', $this->config->get('config_telephone')),
			$this->config->get('config_telephone')
		);
		$email_link = sprintf(
			'<a class="email_link" rel="nofollow" href="mailto:%s">%s</a>',
			$this->config->get('config_email'),
			$this->config->get('config_email')
		);

		foreach ($this->model_catalog_faq->getCategories() as $category) {
				$faq_cat = array(
					'image' => $category['image'],
					'title'	=> $category['name'],
					'faqs'	=> array(),
				);
				$filter = array(
					'faq_category' => $category['faq_category_id']
				);
				foreach ($this->model_catalog_faq->getInformations($filter) as $faq_info) {
					$text = html_entity_decode($faq_info['text'], ENT_QUOTES, 'UTF-8');
					$text = str_replace('{{phone}}', $phone_link, $text);
					$text = str_replace('{{email}}', $email_link, $text);
					$faq_cat['faqs'][] = array(
						'id'		=> $faq_info['faq_id'],
						'title'	=> $faq_info['title'],
						'text'	=> $text,
					);
				}
				$data['faq_cats'][] = $faq_cat;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/faq', $data));
	}

}
