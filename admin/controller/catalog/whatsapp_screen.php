<?php

class ControllerCatalogWhatsappScreen extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/whatsapp_screen');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/whatsapp_screen');

		$this->getList();
	}

	protected function getList() {
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

		if (isset($this->request->get['filter_date_screen'])) {
			$filter_date_screen = $this->request->get['filter_date_screen'];
		} else {
			$filter_date_screen = null;
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'w.date_screen';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_screen'])) {
			$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
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
			'href' => $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/whatsapp_screen/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/whatsapp_screen/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['screens'] = array();

		$filter_data = array(
			'filter_author' => $filter_author,
			'filter_status' => $filter_status,
			'filter_date_screen' => $filter_date_screen,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$screen_total = $this->model_catalog_whatsapp_screen->getTotalScreens($filter_data);

		$results = $this->model_catalog_whatsapp_screen->getScreens($filter_data);

		foreach ($results as $result) {
			$data['screens'][] = array(
				'screen_id' => $result['screen_id'],
				'author' => $result['author_name'],
				'status' => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_screen' => date($this->language->get('date_format_short'), strtotime($result['date_screen'])),
				'edit' => $this->url->link('catalog/whatsapp_screen/edit', 'token=' . $this->session->data['token'] . '&screen_id=' . $result['screen_id'] . $url, true)
			);
		}

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
		$data['column_date_screen'] = $this->language->get('column_date_screen');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_rating'] = $this->language->get('entry_rating');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_screen'] = $this->language->get('entry_date_screen');

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

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_screen'])) {
			$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_author'] = $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . '&sort=w.author_name' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . '&sort=w.status' . $url, true);
		$data['sort_date_screen'] = $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . '&sort=w.date_screen' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_screen'])) {
			$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $screen_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($screen_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($screen_total - $this->config->get('config_limit_admin'))) ? $screen_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $screen_total, ceil($screen_total / $this->config->get('config_limit_admin')));

		$data['filter_author'] = $filter_author;
		$data['filter_status'] = $filter_status;
		$data['filter_date_screen'] = $filter_date_screen;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/whatsapp_screen_list', $data));
	}

	public function add() {
		$this->load->language('catalog/whatsapp_screen');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/whatsapp_screen');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_whatsapp_screen->addScreen($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_screen'])) {
				$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
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

			$this->response->redirect($this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/whatsapp_screen');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/whatsapp_screen');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_whatsapp_screen->editScreen($this->request->get['screen_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_screen'])) {
				$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
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

			$this->response->redirect($this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/whatsapp_screen');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/whatsapp_screen');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $screen_id) {
				$this->model_catalog_whatsapp_screen->deleteScreen($screen_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_screen'])) {
				$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
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

			$this->response->redirect($this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}	

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['screen_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_screen'] = $this->language->get('entry_screen');
		$data['entry_author'] = $this->language->get('entry_author');


		$data['entry_date_screen'] = $this->language->get('entry_date_screen');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_product'] = $this->language->get('help_product');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['author_name'])) {
			$data['error_author'] = $this->error['author_name'];
		} else {
			$data['error_author'] = '';
		}

		$url = '';
		
		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_screen'])) {
			$url .= '&filter_date_screen=' . $this->request->get['filter_date_screen'];
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
			'href' => $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['screen_id'])) {
			$data['action'] = $this->url->link('catalog/whatsapp_screen/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/whatsapp_screen/edit', 'token=' . $this->session->data['token'] . '&screen_id=' . $this->request->get['screen_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/whatsapp_screen', 'token=' . $this->session->data['token'] . $url, true);
		$data['autocomplete'] = $data['autocomplete1'] = $this->url->link('catalog/product/autocomplete', 'token=' . $this->session->data['token'] . $url, true);
		$data['autocomplete2'] = $this->url->link('catalog/blog/autocomplete', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['screen_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$screen_info = $this->model_catalog_whatsapp_screen->getScreen($this->request->get['screen_id']);
			if ($screen_info['source_id'] == 2) {
				$data['entry_product'] = $this->language->get('entry_blog');
				$data['autocomplete'] = $data['autocomplete2'];
			}
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($screen_info)) {
			$data['image'] = $screen_info['screen'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($screen_info) && is_file(DIR_IMAGE . $screen_info['screen'])) {
			$data['thumb'] = $this->model_tool_image->resize($screen_info['screen'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['author_name'])) {
			$data['author_name'] = $this->request->post['author_name'];
		} elseif (!empty($screen_info)) {
			$data['author_name'] = $screen_info['author_name'];
		} else {
			$data['author_name'] = '';
		}

		if (isset($this->request->post['author_region'])) {
			$data['author_region'] = $this->request->post['author_region'];
		} elseif (!empty($screen_info)) {
			$data['author_region'] = $screen_info['author_region'];
		} else {
			$data['author_region'] = '';
		}

		if (isset($this->request->post['author_avatar_link'])) {
			$data['author_avatar_link'] = $this->request->post['author_avatar_link'];
		} elseif (!empty($screen_info)) {
			$data['author_avatar_link'] = $screen_info['author_avatar_link'];
		} else {
			$data['author_avatar_link'] = '';
		}

		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif (!empty($screen_info)) {
			$data['comment'] = $screen_info['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($this->request->post['values'])) {
			$data['values'] = $this->request->post['values'];
		} elseif (!empty($screen_info)) {
			$data['values'] = $screen_info['values'];
		} else {
			$data['values'] = '';
		}

		if (isset($this->request->post['defects'])) {
			$data['defects'] = $this->request->post['defects'];
		} elseif (!empty($screen_info)) {
			$data['defects'] = $screen_info['defects'];
		} else {
			$data['defects'] = '';
		}

		if (isset($this->request->post['rating_value'])) {
			$data['rating_value'] = $this->request->post['rating_value'];
		} elseif (!empty($screen_info)) {
			$data['rating_value'] = $screen_info['rating_value'];
		} else {
			$data['rating_value'] = '';
		}

		if (isset($this->request->post['rating_text'])) {
			$data['rating_text'] = $this->request->post['rating_text'];
		} elseif (!empty($screen_info)) {
			$data['rating_text'] = $screen_info['rating_text'];
		} else {
			$data['rating_text'] = '';
		}

		if (isset($this->request->post['date_screen'])) {
			$data['date_screen'] = $this->request->post['date_screen'];
		} elseif (!empty($screen_info)) {
			$data['date_screen'] = ($screen_info['date_screen'] != '0000-00-00 00:00' ? $screen_info['date_screen'] : '');
		} else {
			$data['date_screen'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($screen_info)) {
			$data['status'] = $screen_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['source_id'])) {
			$data['source_id'] = $this->request->post['source_id'];
		} elseif (!empty($screen_info)) {
			$data['source_id'] = $screen_info['source_id'];
		} else {
			$data['source_id'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/whatsapp_screen_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/whatsapp_screen')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/whatsapp_screen')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}