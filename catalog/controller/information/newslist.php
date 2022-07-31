<?php
class ControllerInformationNewslist extends Controller {
	public function index() {
		$this->load->language('information/news');
		$this->load->model('catalog/news');
		$this->load->model('tool/image');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/information_news.min.css');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('list_title'),
			'href' => $this->url->link('information/newslist')
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = 20;

		$this->document->setTitle($this->language->get('list_title'));

		$data['heading_title'] = $this->language->get('list_title');

		$data['text_error'] = $this->language->get('list_title');

		$data['articles'] = array();

		$news_array = $this->model_catalog_news->getInformations($limit, ($page-1) * $limit);
		$articles_total = $news_array['total'];

		foreach ($news_array['rows'] as $article) {
			//var_dump($article);exit;
			$data['articles'][] = array(
				'title'		=> $article['title'],
				'url'		=> $this->url->link('information/news', 'news_id=' . $article['news_id']),
				'image'		=> $this->model_tool_image->resize($article['image'], 100, 200),
				//'announce'	=> htmlspecialchars_decode($article['announce']),
				'announce'	=> preg_replace('/&lt;[^&]*&gt;/', '', $article['announce']),
				//'date'		=> strftime('%d.%m.%Y', strtotime((int)$article['date_modified'] != 0 ? $article['date_modified'] : $article['date_added'])),
				'date'		=> strftime('%d.%m.%Y', strtotime($article['date_added'])),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $articles_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text_button = 'ПОКАЗАТЬ ЕЩЕ СТАТЬИ';
		$pagination->url = $this->url->link('information/newslist', 'page={page}');

		$data['pagination'] = $pagination->render();


		$data['results'] =
		sprintf(
			$this->language->get('text_pagination'),
			($articles_total) ?  (($page - 1) * $limit) + 1 : 0,
			((($page - 1) * $limit) > ($articles_total - $limit)) ? $articles_total : ((($page - 1) * $limit) + $limit),
			$articles_total, ceil($articles_total / $limit)
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/news_list', $data));
	}
}
