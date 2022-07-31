<?php

class ControllerCatalogCategory extends Controller {

	private $error = array();

	public function index() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$url_c = $url;
		$path_arr = array();
		if (isset($this->request->get['path'])) {
			$url .= '&path=' . $this->request->get['path'];
			$path_arr = explode('-', $this->request->get['path']);
		}

		$this->load->model('localisation/language');
		$admin_language = $this->model_localisation_language->getLanguageByCode($this->config->get('config_admin_language'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url_c, true)
		);

		$path_cur = '';
		$category_id = $root_id = 0;
		foreach ($path_arr as $path_item) {
			$root_id = $category_id;
			$category_id = $path_item;
			$path_cur .= '-' . $path_item;
			$path_info = $this->model_catalog_category->getCategoryDescriptions($path_item);
			$data['breadcrumbs'][] = array(
				'text' => $path_info[$admin_language['language_id']]['name'],
				'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&path=' . substr($path_cur, 1) . $url_c, true)
			);
		}

		$data['add'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . $url, true);
		$data['repair'] = $this->url->link('catalog/category/repair', 'token=' . $this->session->data['token'] . $url, true);
		$data['filter_action'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true);

		$data['categories'] = array();
		$data['root_id'] = $category_id; //$root_id;
		$data['grouping'] = true;
		$data['token'] = $this->session->data['token'];

		$filter_data = array(
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			// 'filter_name'	=> 'оль',
			// 'filter_root_only' => true,
		);
		$filter_total = array(
			'filter_parent_only' => $category_id,
		);
		if ($category_id) {
			$filter_path = ''; //$category_id;
			foreach ($this->model_catalog_category->getCategoryChildren($category_id) as $path_id) {
				$filter_path .= ',' . $path_id['category_id'];
			}
			$filter_data['filter_path'] = substr($filter_path, 1);
			$filter_total['filter_path'] = substr($filter_path, 1);
		}
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
			$data['grouping'] = false;
			$filter_data['filter_name'] = $filter_name;
			$filter_total['filter_name'] = $filter_name;
		} else {
			$filter_name = '';
		}
		$data['filter_name'] = $filter_name;

		$category_total = $this->model_catalog_category->getTotalCategories($filter_total);

		$results = $this->model_catalog_category->getCategories($filter_data);

		foreach ($results as $result) {
			$path = '';
			foreach ($this->model_catalog_category->getCategoryPath($result['category_id']) as $path_row) {
				$path .= '-' . $path_row['path_id'];
			}
			$path = substr($path, 1);
			$data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name' => $result['name'],
				'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&path=' . $path . $url_c, true),
				'parent_id' => $result['parent_id'],
				'children' => sizeof($this->model_catalog_category->getCategoryChildren($result['category_id'])),
				'sort_order' => $result['sort_order'],
				'edit' => $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, true),
				'delete' => $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['entry_name'] = $this->language->get('entry_name');

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/category_list', $data));
	}

	public function add() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_category->addCategory($this->request->post);
			$this->cache->delete('categories');

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			// if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
			// 	$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			// }
		}

		if (!empty($this->request->post['yml_path'])) {
			if (utf8_strlen($this->request->post['yml_path']) > 300) {
				$this->error['yml_path'] = $this->language->get('error_yml_path');
			} else {
				$needle = trim($this->request->post['yml_path']);
				$res = $this->searchYMLPath($needle);
				if (sizeof($res) == 0 or $res[0] !== $needle)
					$this->error['yml_path'] = $this->language->get('error_yml_path_miss');
			}
		}

		if (isset($this->request->get['category_id']) && $this->request->post['parent_id']) {
			$results = $this->model_catalog_category->getCategoryPath($this->request->post['parent_id']);

			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['category_id']) {
					$this->error['parent'] = $this->language->get('error_parent');

					break;
				}
			}
		}

		if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'category_id=' . $this->request->get['category_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['category_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function searchYMLPath($search) {
		$ret = array();
		$needle = trim($search);
		$handle = @fopen(realpath($_SERVER['DOCUMENT_ROOT']) . '/market_categories.csv', 'r');
		if ($handle) {
			while (!feof($handle)) {
				$str = trim(fgets($handle));
				$str = str_replace(['"'], '', $str);
				if (mb_stripos($str, $needle) !== false)
					$ret[] = $str;
			}
			fclose($handle);
			return $ret;
		} else {
			return ['не могу открыть справочник категорий: "market_categories.csv"'];
		}
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_tag_h1'] = $this->language->get('entry_tag_h1');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_yml_path'] = $this->language->get('entry_yml_path');
		$data['entry_parent'] = $this->language->get('entry_parent');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_icon'] = $this->language->get('entry_icon');
		$data['entry_top'] = $this->language->get('entry_top');
		$data['entry_bottom'] = $this->language->get('entry_bottom');
		$data['entry_column'] = $this->language->get('entry_column');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_layout'] = $this->language->get('entry_layout');

		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_yml_path'] = $this->language->get('help_yml_path');
		$data['help_top'] = $this->language->get('help_top');
		$data['help_bottom'] = $this->language->get('help_bottom');
		$data['help_column'] = $this->language->get('help_column');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_design'] = $this->language->get('tab_design');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['yml_path'])) {
			$data['error_yml_path'] = $this->error['yml_path'];
		} else {
			$data['error_yml_path'] = '';
		}

		if (isset($this->error['ozon_category'])) {
			$data['error_ozon_category'] = $this->error['ozon_category'];
		} else {
			$data['error_ozon_category'] = '';
		}

		if (isset($this->error['parent'])) {
			$data['error_parent'] = $this->error['parent'];
		} else {
			$data['error_parent'] = '';
		}

		$url = '';

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
			'href' => $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['category_id'])) {
			$data['action'] = $this->url->link('catalog/category/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['category_description'])) {
			$data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($this->request->get['category_id']);
		} else {
			$data['category_description'] = array();
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		if (isset($this->request->post['category_link'])) {
			$data['category_links'] = $this->request->post['category_link'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_links'] = $this->model_catalog_category->getCategoryLinks($this->request->get['category_id']);
		} else {
			$data['category_links'] = array();
		}

		if (isset($this->request->post['category_product_link'])) {
			$data['category_product_links'] = $this->request->post['category_product_link'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_product_links'] = $this->model_catalog_category->getCategoryProductLinks($this->request->get['category_id']);
		} else {
			$data['category_product_links'] = array();
		}

		$this->load->model('tool/image');

		// Линки для аксессуаров
		$category_id = isset($this->request->get['category_id']) ? $this->request->get['category_id'] : 0;

		$tmp = $this->config->get('config_accessory_link');

		$data['category_accessory_links'] = [];
    $svgs = $this->config->get('icons')['accessory_links'];

		foreach ($tmp as $k => $v) {
		  $placeholder = array_pop($svgs);
      if (empty($v['icon'])) {
        $v['icon'] = 'data:image/svg+xml;utf8,' . str_replace('"', '\'', $placeholder);
      } else {
        $v['icon'] = $this->model_tool_image->resize($v['icon'], 64, 64);
      }
			$data['category_accessory_links'][$k] = [
				'icon' => $v['icon'], //$this->model_tool_image->resize($v['icon'], 64, 64),
				'name_ph' => $v['name'],
				'href_ph' => $v['href'],
				'name' => '',
				'href' => ''
			];
		}

		$tmp = $this->model_catalog_category->getCategoryAccessoryLinks($category_id);

		if (!empty($tmp)) {
			foreach ($tmp as $v) {
				$data['category_accessory_links'][$v['icon_id']]['name'] = $v['name'];
				$data['category_accessory_links'][$v['icon_id']]['href'] = $v['href'];
			}
		}

		if (isset($this->request->post['category_accessory_link'])) {
			foreach ($data['category_accessory_links'] as $k => $v) {
				foreach (['name', 'href'] as $key) {
					if (empty($this->request->post['category_accessory_link'][$k][$key])) {
						$v[$key] = '';
					} else {
						$v[$key] = $this->request->post['category_accessory_link'][$k][$key];
					}
				}

				$data['category_accessory_links'][$k] = $v;
			}
		}
		/*


				foreach ($tmp2 as $v){
					$data['category_accessory_links'][$v['icon_id']] = [
						''
					]
				}
		*/

		//print_r($data['accessory_link']);

		//$icons = $this->
		/*
				$tmp = $this->model_catalog_category->getCategoryAccessoryLinks($category_id);

				foreach ($tmp as $v) {
					$data['category_accessory_links'][$v['icon_id']] = $v;
				}

				if (isset($this->request->post['category_accessory_link'])) {
					foreach ($data['category_accessory_links'] as $k => $v) {
						foreach (['name', 'href'] as $key) {
							if (empty($this->request->post['category_accessory_link'][$k][$key])) {
								$v[$key] = '';
							} else {
								$v[$key] = $this->request->post['category_accessory_link'][$k][$key];
							}
						}

						$data['category_accessory_links'][$k] = $v;
					}
				}
		*/
		$this->load->model('catalog/filter');

		if (isset($this->request->post['category_filter'])) {
			$filters = $this->request->post['category_filter'];
		} elseif (isset($this->request->get['category_id'])) {
			$filters = $this->model_catalog_category->getCategoryFilters($this->request->get['category_id']);
		} else {
			$filters = array();
		}

		$data['category_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['category_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name' => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['category_store'])) {
			$data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_store'] = $this->model_catalog_category->getCategoryStores($this->request->get['category_id']);
		} else {
			$data['category_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($category_info)) {
			$data['keyword'] = $category_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['yml_path'])) {
			$data['yml_path'] = $this->request->post['yml_path'];
		} elseif (!empty($category_info)) {
			$data['yml_path'] = $category_info['yml_path'];
		} else {
			$data['yml_path'] = '';
		}

		if (isset($this->request->post['ozon_category_id'])) {
			$data['ozon_category_id'] = $this->request->post['ozon_category_id'];
		} elseif (!empty($category_info)) {
			$data['ozon_category_id'] = $category_info['ozon_category_id'];
		} else {
			$data['ozon_category_id'] = '';
		}

		$data['ozon_category'] = '';
		if ($data['ozon_category_id']) {
			$this->load->model('extension/ozon/api');
			$ozon_categories = $this->model_extension_ozon_api->getCategories();
			$found = $this->findOzonCat((int)$data['ozon_category_id'], $ozon_categories);
			if (!empty($found)) $data['ozon_category'] = $found[0][1];
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}
		if (isset($this->request->post['icon'])) {
			$data['icon'] = $this->request->post['icon'];
		} elseif (!empty($category_info)) {
			$data['icon'] = $category_info['icon'];
		} else {
			$data['icon'] = '';
		}

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		if (isset($this->request->post['icon']) && is_file(DIR_IMAGE . $this->request->post['icon'])) {
			$data['thumb_icon'] = $this->model_tool_image->resize($this->request->post['icon'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['icon'])) {
			$data['thumb_icon'] = $this->model_tool_image->resize($category_info['icon'], 100, 100);
		} else {
			$data['thumb_icon'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($category_info)) {
			$data['top'] = $category_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($category_info)) {
			$data['bottom'] = $category_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($category_info)) {
			$data['column'] = $category_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['special_tag'])) {
			$data['special_tag'] = $this->request->post['special_tag'];
		} elseif (!empty($category_info)) {
			$data['special_tag'] = $category_info['special_tag'];
		} else {
			$data['special_tag'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['category_layout'])) {
			$data['category_layout'] = $this->request->post['category_layout'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_layout'] = $this->model_catalog_category->getCategoryLayouts($this->request->get['category_id']);
		} else {
			$data['category_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/category_form', $data));
	}

	private function findOzonCat($needle, $haystack, $text = '') {
		static $result = [];
		foreach ($haystack as $item) {
			if (sizeof($result) > 5)
				break;
			$ct = '';
			if (!empty($text))
				$ct = $text . '&nbsp;>&nbsp;';
			$ct .= $item['title'];
			if (
				(is_string($needle) and mb_stripos($item['title'], $needle) !== false) or
				(is_int($needle) and $needle == $item['category_id'])
			) {
				$result[] = [$item['category_id'], $ct];
			}
			if (!empty($item['children'])) {
				$this->findOzonCat($needle, $item['children'], $ct);
			}
		}
		return $result;
	}

	public function edit() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);
			$this->cache->delete('categories');

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $category_id) {
				$this->model_catalog_category->deleteCategory($category_id);
			}
			$this->cache->delete('categories');

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function repair() {
		$this->load->language('catalog/category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/category');

		if ($this->validateRepair()) {
			$this->model_catalog_category->repairCategories();
			$this->cache->delete('categories');

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort' => 'cd2.name',
				'order' => 'ASC',
				'start' => 0,
				'limit' => 5
			);

			$results = $this->model_catalog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete_yml_path() {
		$json = array();

		if (isset($this->request->get['yml_path'])) {
			$results = $this->searchYMLPath($this->request->get['yml_path']);

			foreach ($results as $result) {
				$json[] = array(
					'path' => strip_tags(html_entity_decode($result, ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['path'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete_ozon() {
		$json = array();

		$this->load->model('extension/ozon/api');
		$ozon_categories = $this->model_extension_ozon_api->getCategories();

		if (isset($this->request->get['id'])) {
			$results = $this->findOzonCat($this->request->get['id'], $ozon_categories);

			foreach ($results as $result) {
				$json[] = array(
					'id' => (string)$result[0],
					'name' => strip_tags(html_entity_decode($result[1], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
