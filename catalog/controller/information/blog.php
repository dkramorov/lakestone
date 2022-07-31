<?php
class ControllerInformationBlog extends Controller {
	public function index() {
		$this->load->language('information/blog');
		$this->document->addStyle('catalog/view/theme/lakestone/stylesheet/information_blog.min.css');

		$this->load->model('catalog/blog');
		$this->load->model('account/user');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('list_title'),
			'href' => $this->url->link('information/bloglist')
		);

		if (isset($this->request->get['blog_id'])) {
			$blog_id = (int)$this->request->get['blog_id'];
		} else {
			$blog_id = 0;
		}

		$blog_info = $this->model_catalog_blog->getInformation($blog_id);
		$this->document->addLink($this->url->link('information/blog', 'blog_id=' . $blog_id), 'canonical');

		if ($blog_info) {
			$blogger = $this->model_account_user->getUserByUsername('blogger');
			$admin = $this->model_account_user->getUserByUsername('admin');
			if (!empty($blogger)) {
				$user = $blogger;
			} else if (!empty($admin)) {
				$user = $admin;
			}
			if (!empty($user))
				$data['author'] = $user['lastname'] . ' ' . $user['firstname'];
			else
				$data['author'] = '';

			$this->document->setTitle($blog_info['meta_title']);
			$this->document->setDescription($blog_info['meta_description']);
			$this->document->setKeywords($blog_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $blog_info['title'],
				'href' => $this->url->link('information/blog', 'blog_id=' .  $blog_id)
			);

			$data['heading_title'] = $blog_info['title'];
			$data['button_continue'] = $this->language->get('button_continue');
			$data['description'] = html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8');
			$data['datetimePublished'] = $blog_info['date_added'];
			$data['datePublished'] = strftime('%d.%m.%Y', strtotime($blog_info['date_added']));
			$data['datetimeModified'] = $blog_info['date_modified'];
			$data['continue'] = $this->url->link('common/home');
			$data['review_status'] = $this->config->get('config_review_status');
			$data['review_guest'] = $this->config->get('config_review_guest');
			$data['entry_name'] = 'Ваше имя:';
			$data['entry_review'] = 'Ваш комментарий:';
			$data['action'] = $this->url->link('information/blog/write', 'blog_id=' . $blog_id);
			$data['blog_id'] = $blog_id;
                        $data['text_loading'] = 'Подождите';


			$LocaleRU = new Locality_RU();
			$data['reviews_num'] = (int)$blog_info['reviews'];
			$data['reviews'] = ' отзыв' . $LocaleRU->num_ending((int)$blog_info['reviews']);
			$data['rating'] = (int)$blog_info['rating'];
			$data['review_write_href'] = $data['review_href'] = $this->url->link('information/blog', 'blog_id=' . $blog_id);
			$data['review_like_href'] = $this->url->link('information/blog/like');
			$data['review_unlike_href'] = $this->url->link('information/blog/unlike');

			$rating_token = $this->getToken($blog_id, 'blog/review/rating');
			$data['rating_token'] = $rating_token;
			if (isset($this->session->data['tokens']) and in_array($rating_token, $this->session->data['tokens'])) {
				$data['rating_href'] = false;
			} else {
				$data['rating_href'] = $this->url->link('information/blog/rating');
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('information/blog', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('list_title'),
				'href' => $this->url->link('information/bloglist', 'blog_id=' . $blog_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function reviews() {
		$this->load->language('information/blog');

		$this->load->model('catalog/review');
		$this->load->model('tool/image');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		#$data['like_href'] = $this->url->link('information/blog/like', 'blog_id=' . $this->request->get['blog_id']);
		#$data['unlike_href'] = $this->url->link('information/blog/unlike', 'blog_id=' . $this->request->get['blog_id']);
		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['blog_id'], 2);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['blog_id'], ($page - 1) * 5, 5, 2, true);

		foreach ($results as $result) {
			if ($result['author'] == '_only_rating')
				continue;
			$data['reviews'][] = array(
				'review_id'		=> $result['review_id'],
				'type'				=> 2,
				'author'    	=> $result['author'],
				'text'      	=> nl2br($result['text']),
				'answer'			=> nl2br($result['respond']),
				'rating'   		=> (int)$result['rating'],
				'ratingCount'	=> $review_total,
				'useful_photo' => (int)$result['useful_photo'],
				'useful_description' => (int)$result['useful_description'],
				'like'				=> (int)$result['like'],
				'unlike'			=> (int)$result['unlike'],
				'like_token'				=> $this->getToken($result['review_id'], 'blog/review/like'),
				'unlike_token'				=> $this->getToken($result['review_id'], 'blog/review/unlike'),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('information/blog/review', 'blog_id=' . $this->request->get['blog_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('information/review', $data));
	}

	public function review_rules() {
			$this->response->setOutput($this->load->view('information/review_rules'));
	}

	private function getToken($id, $route = '') {
		$res = sha1($this->session->getId());
		if (!empty($route))
			$res = sha1($res . $route);
		$res = sha1($res . $id);
		$res = sha1($res . 'aozeya6Paishu7Aecha7AhCeiw8shoo:');
		return $res;
	}

	private function checkToken($id, $token, $route = '') {
		if ($token == $this->getToken($id, $route)) {
			if (empty($this->session->data['tokens'])) {
				$this->session->data['tokens'] = array($token);
				return true;
			} elseif (!in_array($token, $this->session->data['tokens'])) {
				array_push($this->session->data['tokens'], $token);
				return true;
			} else {
				return false;
			}
		}
	}

	public function rating() {
		$this->load->model('catalog/review');
		if ( $this->checkToken($this->request->get['blog_id'], $this->request->get['token'], 'blog/review/rating')) {
			$arr = array(
				'review_type'		=> 2,
				'rating_photo'	=> 0,
				'rating_description' => 0,
				'name'					=> '_only_rating',
				'text'					=> 'только оценка',
			);
			if ($this->request->get['like'] == 'true')
				$arr['rating']	= 5;
			else
				$arr['rating']	= 1;
			$this->model_catalog_review->addReview($this->request->get['blog_id'], $arr);
		}
		$this->response->addHeader('Content-Type: application/json');
		//$this->response->setOutput(json_encode($this->model_catalog_review->getInformation($this->request->get['review_id'])));
		$this->response->setOutput('{"status":"OK"}');
	}

	public function like() {
		$this->load->model('catalog/review');
		if ( $this->checkToken($this->request->get['review_id'], $this->request->get['token'], 'blog/review/like')) {
			$this->model_catalog_review->likeReview($this->request->get['review_id']);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->model_catalog_review->getLikeReview($this->request->get['review_id'])));
	}

	public function unlike() {
		$this->load->model('catalog/review');
		if ( $this->checkToken($this->request->get['review_id'], $this->request->get['token'], 'blog/review/unlike')) {
			$this->model_catalog_review->unlikeReview($this->request->get['review_id']);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->model_catalog_review->getLikeReview($this->request->get['review_id'])));
	}

	public function write() {
		$this->load->language('information/blog');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->request->post['review_type'] = 2;
				$this->request->post['rating_photo'] = 0;
				$this->request->post['rating_description'] = 0;
				if (!isset($this->request->post['rating']))
					$this->request->post['rating'] = 0;
				$this->model_catalog_review->addReview($this->request->get['blog_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
