<?php
class ControllerCatalogExternalReview extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/external_review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/external_review');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/external_review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/external_review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_external_review->addReview($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_source_id'])) {
				$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_rating_value'])) {
				$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_review'])) {
				$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/external_review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/external_review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_external_review->editReview($this->request->get['review_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_source_id'])) {
				$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_rating_value'])) {
				$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_review'])) {
				$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/external_review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/external_review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $review_id) {
				$this->model_catalog_external_review->deleteReview($review_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_source_id'])) {
				$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_rating_value'])) {
				$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_review'])) {
				$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {

/*    $reviews_array = json_decode(file_get_contents('../ymarket_lakestone.json'), true);
    foreach ($reviews_array as $review) {
      $this->model_catalog_external_review->addReview([
        'source_id_id' => 1,
        'author_name' => $review['User'],
        'author_avatar_link' => $review['Avatar'],
        'rating_value_text' => $review['rating_value'],
        'rating_value_value' => $review[''],
        'values' => $review['Values'],
        'defects' => $review['Flaws'],
        'comment' => $review['Comments'],
        'author_region' => $review['Region'],
        'date_review' => strftime('%Y-%m-%d', $review['timestamp']),
        'status' => 1,
      ]);
    }*/

    if (isset($this->request->get['filter_source'])) {
      $filter_source = $this->request->get['filter_source'];
    } else {
      $filter_source = null;
    }

		if (isset($this->request->get['filter_rating'])) {
			$filter_rating = $this->request->get['filter_rating'];
		} else {
			$filter_rating = null;
		}

		if (isset($this->request->get['filter_author'])) {
			$filter_author = $this->request->get['filter_author'];
		} else {
			$filter_author = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_review'])) {
			$filter_date_review = $this->request->get['filter_date_review'];
		} else {
			$filter_date_review = null;
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_review';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_source'])) {
			$url .= '&filter_source=' . urlencode(html_entity_decode($this->request->get['filter_source'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . urlencode(html_entity_decode($this->request->get['filter_rating'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_review'])) {
			$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/external_review/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/external_review/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['reviews'] = array();

		$filter_data = array(
			'filter_source' => $filter_source,
			'filter_rating'    => $filter_rating,
			'filter_author'     => $filter_author,
			'filter_status'     => $filter_status,
			'filter_date_review' => $filter_date_review,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$review_total = $this->model_catalog_external_review->getTotalReviews($filter_data);

		$results = $this->model_catalog_external_review->getReviews($filter_data);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'review_id'  => $result['review_id'],
				'source'     => $result['source'],
				'author'     => $result['author_name'],
				'rating'     => $result['rating_text'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_review' => date($this->language->get('date_format_short'), strtotime($result['date_review'])),
				'edit'       => $this->url->link('catalog/external_review/edit', 'token=' . $this->session->data['token'] . '&review_id=' . $result['review_id'] . $url, true)
			);
		}

		$data['sources'] = $this->model_catalog_external_review->getSources();

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_author'] = $this->language->get('column_author');
		$data['column_rating'] = $this->language->get('column_rating');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_review'] = $this->language->get('column_date_review');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_review'] = $this->language->get('entry_date_review');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_source_id'])) {
			$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_rating_value'])) {
			$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_review'])) {
			$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_source'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . '&sort=pd.source_id' . $url, true);
		$data['sort_author'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . '&sort=r.author_name' . $url, true);
		$data['sort_rating'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . '&sort=r.rating_value' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . '&sort=r.status' . $url, true);
		$data['sort_date_review'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . '&sort=r.date_review' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_source_id'])) {
			$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_rating_value'])) {
			$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_review'])) {
			$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($review_total - $this->config->get('config_limit_admin'))) ? $review_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $review_total, ceil($review_total / $this->config->get('config_limit_admin')));

		$data['filter_source'] = $filter_source;
		$data['filter_rating'] = $filter_rating;
		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_review'] = $filter_date_review;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/external_review_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['review_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating_value'] = $this->language->get('entry_rating_value');
		$data['entry_rating_text'] = $this->language->get('entry_rating_text');
		$data['entry_rating_value_description'] = $this->language->get('entry_rating_value_description');
		$data['entry_date_review'] = $this->language->get('entry_date_review');
		$data['entry_source_id'] = $this->language->get('entry_source_id');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_answer_status'] = $this->language->get('entry_answer_status');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_answer'] = $this->language->get('entry_answer');

		$data['help_product'] = $this->language->get('help_product');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['author_name'])) {
			$data['error_author'] = $this->error['author_name'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['comment'])) {
			$data['error_text'] = $this->error['comment'];
		} else {
			$data['error_text'] = '';
		}

		if (isset($this->error['answer'])) {
			$data['error_answer'] = $this->error['answer'];
		} else {
			$data['error_answer'] = '';
		}

		if (isset($this->error['rating_value'])) {
			$data['error_rating_value'] = $this->error['rating_value'];
		} else {
			$data['error_rating_value'] = '';
		}

		if (isset($this->error['rating_text'])) {
			$data['error_rating_text'] = $this->error['rating_text'];
		} else {
			$data['error_rating_text'] = '';
		}

		if (isset($this->error['rating_value_description'])) {
			$data['error_rating_value_description'] = $this->error['rating_value_description'];
		} else {
			$data['error_rating_value_description'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_source_id'])) {
			$url .= '&filter_source_id=' . urlencode(html_entity_decode($this->request->get['filter_source_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_rating_value'])) {
			$url .= '&filter_rating_value=' . urlencode(html_entity_decode($this->request->get['filter_rating_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_review'])) {
			$url .= '&filter_date_review=' . $this->request->get['filter_date_review'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['review_id'])) {
			$data['action'] = $this->url->link('catalog/external_review/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/external_review/edit', 'token=' . $this->session->data['token'] . '&review_id=' . $this->request->get['review_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/external_review', 'token=' . $this->session->data['token'] . $url, true);
		$data['autocomplete'] = $data['autocomplete1'] = $this->url->link('catalog/product/autocomplete', 'token=' . $this->session->data['token'] . $url, true);
		$data['autocomplete2'] = $this->url->link('catalog/blog/autocomplete', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$review_info = $this->model_catalog_external_review->getReview($this->request->get['review_id']);
			if ($review_info['source_id'] == 2) {
				$data['entry_product'] = $this->language->get('entry_blog');
				$data['autocomplete'] = $data['autocomplete2'];
			}
		}

		$data['token'] = $this->session->data['token'];
		
		if (isset($this->request->post['author_name'])) {
			$data['author_name'] = $this->request->post['author_name'];
		} elseif (!empty($review_info)) {
			$data['author_name'] = $review_info['author_name'];
		} else {
			$data['author_name'] = '';
		}

		if (isset($this->request->post['author_region'])) {
			$data['author_region'] = $this->request->post['author_region'];
		} elseif (!empty($review_info)) {
			$data['author_region'] = $review_info['author_region'];
		} else {
			$data['author_region'] = '';
		}

		if (isset($this->request->post['author_avatar_link'])) {
			$data['author_avatar_link'] = $this->request->post['author_avatar_link'];
		} elseif (!empty($review_info)) {
			$data['author_avatar_link'] = $review_info['author_avatar_link'];
		} else {
			$data['author_avatar_link'] = '';
		}

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($review_info)) {
			$data['comment'] = $review_info['comment'];
		} else {
			$data['comment'] = '';
		}
		
		if (isset($this->request->post['values'])) {
			$data['values'] = $this->request->post['values'];
		} elseif (!empty($review_info)) {
			$data['values'] = $review_info['values'];
		} else {
			$data['values'] = '';
		}

		if (isset($this->request->post['defects'])) {
			$data['defects'] = $this->request->post['defects'];
		} elseif (!empty($review_info)) {
			$data['defects'] = $review_info['defects'];
		} else {
			$data['defects'] = '';
		}

		if (isset($this->request->post['rating_value'])) {
			$data['rating_value'] = $this->request->post['rating_value'];
		} elseif (!empty($review_info)) {
			$data['rating_value'] = $review_info['rating_value'];
		} else {
			$data['rating_value'] = '';
		}

		if (isset($this->request->post['rating_text'])) {
			$data['rating_text'] = $this->request->post['rating_text'];
		} elseif (!empty($review_info)) {
			$data['rating_text'] = $review_info['rating_text'];
		} else {
			$data['rating_text'] = '';
		}

		if (isset($this->request->post['date_review'])) {
			$data['date_review'] = $this->request->post['date_review'];
		} elseif (!empty($review_info)) {
			$data['date_review'] = ($review_info['date_review'] != '0000-00-00 00:00' ? $review_info['date_review'] : '');
		} else {
			$data['date_review'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($review_info)) {
			$data['status'] = $review_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['source_id'])) {
			$data['source_id'] = $this->request->post['source_id'];
		} elseif (!empty($review_info)) {
			$data['source_id'] = $review_info['source_id'];
		} else {
			$data['source_id'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/external_review_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/external_review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

/*		if (!$this->request->post['product_id']) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if ((utf8_strlen($this->request->post['author_name']) < 3) || (utf8_strlen($this->request->post['author_name']) > 64)) {
			$this->error['author_name'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['comment']) < 1) {
			$this->error['comment'] = $this->language->get('error_text');
		}

		if ($this->request->post['source_id'] < 2) {
			if (!isset($this->request->post['rating_value']) || $this->request->post['rating_value'] < 0 || $this->request->post['rating_value'] > 5) {
				$this->error['rating_value'] = $this->language->get('error_rating_value');
			}

			if (!isset($this->request->post['rating_text']) || $this->request->post['rating_text'] < 0 || $this->request->post['rating_text'] > 5) {
				$this->error['rating_text'] = $this->language->get('error_rating_text');
			}

			if (!isset($this->request->post['rating_value_description']) || $this->request->post['rating_value_description'] < 0 || $this->request->post['rating_value_description'] > 5) {
				$this->error['rating_value_description'] = $this->language->get('error_rating_value_description');
			}
		}*/

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/external_review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}