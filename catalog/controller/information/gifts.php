<?php
class ControllerInformationGifts extends Controller {
	public function index() {
		$this->load->language('information/gifts');

		$this->load->model('catalog/category');

		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/gifts.min.css');
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
		$data['categories'] = array();

		$root = $this->model_catalog_category->getCategories(0, array('positive' => '(^|,)(gifts)($|,)'));

		if (!empty($root)) {
			foreach ($this->model_catalog_category->getCategories($root[0]['category_id']) as $category) {
				// var_dump($category);
				$items = array();
				foreach ($this->model_catalog_category->getCategories($category['category_id']) as $item) {
					// var_dump('item:', $item);
					$items[] = array(
						'title'		=> $item['name'],
						'href'		=> $this->url->link('product/category', 'path=' . $item['category_id']),
					);
				}
				$data['categories'][] = array(
					'title'		=> $category['name'],
					'image'		=> $category['icon'],
					'links'		=> $items,
				);
			}
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/gifts', $data));
	}

}
