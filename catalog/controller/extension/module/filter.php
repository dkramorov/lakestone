<?php

class ControllerExtensionModuleFilter extends Controller {

  public function index() {

    $this->document->addStyle('catalog/view/theme/lakestone/stylesheet/filter.min.css');

    $this->load->model('catalog/category');
    $this->load->model('catalog/seo_link');

    if (!empty($this->request->get['path'])) {
      $parts = explode('_', (string)$this->request->get['path']);
      $path = $category_id = end($parts);
//    } elseif ($this->request->get['seo_link_id']) {
//      $this->load->model('catalog/seo_link');
//      $category_id = $this->model_catalog_seo_link->getLink((int) $this->request->get['seo_link_id'])['category_id'];
//      $parts = array();
    } elseif (!empty($this->request->get['seo_link_id'])) {
      $seo_link_id = $this->request->get['seo_link_id'];
      $seo_data = $this->model_catalog_seo_link->getLink((int)$this->request->get['seo_link_id']);
      $path = $category_id = $seo_data['category_id'];
    } else {
      $parts = array();
      $category_id = 0;
    }

//    dd($category_id, $this->request->get);

    if (isset($this->request->get['order']))
      $data['order'] = $this->request->get['order'];
    else
      $data['order'] = '';
    if (isset($this->request->get['sort']))
      $data['sort'] = $this->request->get['sort'];
    else
      $data['sort'] = '';

    $category_info = $this->model_catalog_category->getCategory($category_id);

    if ($category_info) {
      $this->load->language('extension/module/filter');

      $data['heading_title'] = $this->language->get('heading_title');

      $data['button_filter'] = $this->language->get('button_filter');

      $url = '';

      if (isset($this->request->get['limit'])) {
        $url .= '&limit=' . $this->request->get['limit'];
      }

      if (!empty($seo_link_id)) {
        $url .= '&seo_link_id=' . $seo_link_id;
      } else {
        $url .= '&path=' . $path;
      }

      if (empty($seo_data)) {
        if (isset($this->request->get['filter'])) {
          $data['filter_category'] = explode(',', $this->request->get['filter']);
          $url .= '&filter=' . $this->request->get['filter'];
        } else {
          $data['filter_category'] = array();
        }
      } else {
        $data['filter_category'] = explode(',', $seo_data['filter_tag']);
      }

      $data['sorts'] = array();

      $data['sorts'][] = array(
        'text' => $this->language->get('text_default'),
        'value' => 'p.sort_order-ASC',
        'href' => str_replace('&amp;', '&', $this->url->link('product/category', '&sort=p.sort_order&order=ASC' . $url))
      );
      // $data['sorts'][] = array(
      // 	'text'  => $this->language->get('text_name_asc'),
      // 	'value' => 'pd.name-ASC',
      // 	'href'  => str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url))
      // );
      // $data['sorts'][] = array(
      // 	'text'  => $this->language->get('text_name_desc'),
      // 	'value' => 'pd.name-DESC',
      // 	'href'  => str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url))
      // );
      $data['sorts'][] = array(
        'text' => $this->language->get('text_price_asc'),
        'value' => 'p.price-ASC',
        'href' => str_replace('&amp;', '&', $this->url->link('product/category', '&sort=p.price&order=ASC' . $url))
      );
      $data['sorts'][] = array(
        'text' => $this->language->get('text_price_desc'),
        'value' => 'p.price-DESC',
        'href' => str_replace('&amp;', '&', $this->url->link('product/category', '&sort=p.price&order=DESC' . $url))
      );

      $data['sort_num'] = 0;
      foreach ($data['sorts'] as $num => $sort_a) {
        if ($sort_a['value'] == $data['sort'] . '-' . $data['order']) {
          $data['sort_num'] = $num;
          break;
        }
      }

      $url = '';

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (!empty($seo_link_id)) {
        $url .= '&seo_link_id=' . $seo_link_id;
      } else {
        $url .= '&path=' . $path;
      }

      $data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', $url));
      $data['action_add'] = str_replace('&amp;', '&', $this->url->link('product/category', $url . '&filter='));

      $this->load->model('catalog/product');

      $data['filter_groups'] = array();
      $data['filter_set'] = array();

      $filter_groups = $this->model_catalog_category->getCategoryFilters($category_id);
      /*       * */
      // $filter_groups = array_merge($filter_groups,$filter_groups);
      // $filter_groups = array_merge($filter_groups,$filter_groups);
      // $filter_groups = array_merge($filter_groups,$filter_groups);
      /*       * */

      if ($filter_groups) {
        foreach ($filter_groups as $filter_group) {
          $childen_data = array();
          $active_counter = 0;

          foreach ($filter_group['filter'] as $filter) {
            $filter_data = array(
              'filter_category_id' => $category_id,
              'filter_filter' => $filter['filter_id']
            );

            if (in_array($filter['filter_tag'], $data['filter_category'])) {
              $active_counter++;
              $data['filter_set'][] = array(
                'filter_name' => $filter['name'],
                'filter_id' => $filter['filter_id'],
                'filter_tag' => $filter['filter_tag'],
              );
            }

            $childen_data[] = array(
              'filter_id' => $filter['filter_id'],
              'filter_tag' => $filter['filter_tag'],
              'name' => $filter['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : '')
            );
          }

          $data['filter_groups'][] = array(
            'filter_group_id' => $filter_group['filter_group_id'],
            'name' => $filter_group['name'] . ($active_counter > 0 ? ": $active_counter" : ''),
            'title' => $filter_group['name'],
            'filter' => $childen_data
          );
        }

        return $this->load->view('extension/module/filter', $data);
      }
    }
  }

}
