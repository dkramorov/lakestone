<?php
class ControllerExtensionModuleBannerSmart1 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/banner_smart1');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('banner_smart1', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_template'] = $this->language->get('entry_template');
		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_maintitle'] = $this->language->get('entry_maintitle');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_button'] = $this->language->get('entry_button');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

/*
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}
*/
		
		foreach (array(
			'warning', 'name', 'template',
			'width', 'height', 'article'
		) as $var) {
			if (isset($this->error[$var])) {
				$data['error_' . $var] = $this->error[$var];
			} else {
				$data['error_' . $var] = '';
			}
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/banner_smart1', 'token=' . $this->session->data['token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/banner_smart1', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/banner_smart1', 'token=' . $this->session->data['token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/banner_smart1', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

/*
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['banner_id'])) {
			$data['banner_id'] = $this->request->post['banner_id'];
		} elseif (!empty($module_info)) {
			$data['banner_id'] = $module_info['banner_id'];
		} else {
			$data['banner_id'] = '';
		}
		if (isset($this->request->post['banner1_id'])) {
			$data['banner1_id'] = $this->request->post['banner1_id'];
		} elseif (!empty($module_info)) {
			$data['banner1_id'] = $module_info['banner1_id'];
		} else {
			$data['banner1_id'] = '';
		}
		if (isset($this->request->post['banner2_id'])) {
			$data['banner2_id'] = $this->request->post['banner2_id'];
		} elseif (!empty($module_info)) {
			$data['banner2_id'] = $module_info['banner2_id'];
		} else {
			$data['banner2_id'] = '';
		}
*/

		$this->load->model('design/banner');

		$data['banners'] = $this->model_design_banner->getBanners();

/*
		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}
		if (isset($this->request->post['width1'])) {
			$data['width1'] = $this->request->post['width1'];
		} elseif (!empty($module_info)) {
			$data['width1'] = $module_info['width1'];
		} else {
			$data['width1'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}
		if (isset($this->request->post['height1'])) {
			$data['height1'] = $this->request->post['height1'];
		} elseif (!empty($module_info)) {
			$data['height1'] = $module_info['height1'];
		} else {
			$data['height1'] = '';
		}
		if (isset($this->request->post['height2'])) {
			$data['height2'] = $this->request->post['height2'];
		} elseif (!empty($module_info)) {
			$data['height2'] = $module_info['height2'];
		} else {
			$data['height2'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
*/

		$vars = array(
			'name', 'template', 'status',
			'banner_id','banner1_id','banner2_id',
			'width', 'width1',
			'height', 'height1', 'height2',
			'maintitle', 'title', 'title1', 'title2',
                        'article',
#			'text', 'button',
#			'text1', 'button1',
#			'text2', 'button2',
		);

		foreach ($vars as $var) {
			if (isset($this->request->post[$var])) {
				$data[$var] = $this->request->post[$var];
			} elseif (!empty($module_info)) {
				$data[$var] = $module_info[$var];
			} else {
				$data[$var] = '';
			}
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/banner_smart1', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		// $this->request->post['template']

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}
