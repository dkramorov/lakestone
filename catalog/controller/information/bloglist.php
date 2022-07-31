<?php
class ControllerInformationBloglist extends Controller {
	public function index() {
		$this->load->language('information/blog');
		$this->load->model('catalog/blog');
		$this->load->model('tool/image');
		$this->load->model('account/user');
		$this->document->addLink($this->url->link('information/bloglist'), 'canonical');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/information_blog.min.css');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('list_title'),
			'href' => $this->url->link('information/bloglist')
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = 20;

		$this->document->setTitle($this->language->get('list_title'));
		$this->document->setDescription($this->language->get('text_meta_description'));

		$data['heading_title'] = $this->language->get('list_title');

		$data['text_error'] = $this->language->get('list_title');

		$data['articles'] = array();
		$data['author'] = '';

		$blogger = $this->model_account_user->getUserByUsername('blogger');
		$admin = $this->model_account_user->getUserByUsername('admin');
		if (!empty($blogger)) {
			$user = $blogger;
		} else if (!empty($admin)) {
			$user = $admin;
		}
		if (!empty($user))
			$data['author'] = $user['lastname'] . ' ' . $user['firstname'];

		$data['review_status'] = $this->config->get('config_review_status');
		$blog_array = $this->model_catalog_blog->getInformations($limit, ($page-1) * $limit);
		$articles_total = $blog_array['total'];

		foreach ($blog_array['rows'] as $article) {
			//var_dump($article);exit;
			if ($data['review_status']) {
				$blog_info = $this->model_catalog_blog->getInformation($article['blog_id']);
				$rating = $blog_info['rating'];
				$reviews = $blog_info['reviews'];
			} else {
				$rating = $reviews = false;
			}
			$data['articles'][] = array(
				'title'		=> $article['title'],
				'url'			=> $this->url->link('information/blog', 'blog_id=' . $article['blog_id']),
				'image'		=> $this->model_tool_image->resize($article['image'], 200, 200),
				'rating'	=> $rating,
				'reviews'	=> $reviews,
				//'announce'	=> htmlspecialchars_decode($article['announce']),
				'announce'	=> preg_replace('/&lt;[^&]*&gt;/', '', $article['announce']),
				//'date'		=> strftime('%d.%m.%Y', strtotime((int)$article['date_modified'] != 0 ? $article['date_modified'] : $article['date_added'])),
				'date'		=> strftime('%d.%m.%Y', strtotime($article['date_added'])),
				'isodate'	=> $article['date_added'],
				'isomod'	=> $article['date_modified'],
			);
		}

		$pagination = new Pagination();
		$pagination->total = $articles_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text_button = 'ПОКАЗАТЬ ЕЩЕ СТАТЬИ';
		$pagination->url = $this->url->link('information/bloglist', 'page={page}');

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

		$this->response->setOutput($this->load->view('information/blog_list', $data));
	}
}
