<?php
class ControllerInformationCompanyReviews extends Controller {

    public function index() {

        $this->load->language('information/company_reviews');
        $this->load->model('catalog/external_review');
        setlocale( LC_TIME, 'ru_RU.UTF-8');

        $this->document->addStyle('catalog/view/theme/lakestone/stylesheet/information_reviews.min.css');

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
        $limit = 100;


        $this->document->setTitle($this->language->get('list_title'));
        $this->document->setDescription($this->language->get('text_meta_description'));

        $data['heading_title'] = $this->language->get('list_title');

        $data['text_error'] = $this->language->get('list_title');
        $data['reviews'] = array();

        $filter =[
          'filter_source' => 1,
          'limit' => $limit,
          'start' => ($page - 1) * $limit,
          'filter_status' => 1,
          'sort' => 'date_review',
          'order' => 'DESC',
        ];

        $reviews_array = $this->model_catalog_external_review->getReviews($filter);
        $reviews_total = $this->model_catalog_external_review->getTotalReviews(['source' => 1]);

        foreach ($reviews_array as $review) {
          if (!empty($review['comment']))
            $res['text'] = $review['comment'];
          if (!empty($review['values']))
            $res['pros'] = $review['values'];
          if (!empty($review['defects']))
            $res['cons'] = $review['defects'];
          $author = [];
          if (!empty($review['author_name']))
            $author['name'] = $review['author_name'];
          else
            $author['name'] = 'Аноним';
          if (!empty($review['author_avatar_link']))
            $author['image'] = $review['author_avatar_link'];
          if ($review['date_review_unixtime'] < time() - 3600*24*90)
            $date = strftime('%e %B %Y', $review['date_review_unixtime']);
          else
            $date = strftime('%e %B', $review['date_review_unixtime']);
          $data['reviews'][] = [
            'date' => $date,
            'region' => $review['author_region'],
            'text' => $review['comment'],
            'pros' => $review['values'],
            'cons' => $review['defects'],
            'author' => $author,
          ];
        }

        $pagination = new Pagination();
        $pagination->total = $reviews_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text_button = 'ПОКАЗАТЬ ЕЩЕ ОТЗЫВЫ';
        $pagination->url = $this->url->link('information/company_reviews', 'page={page}');

        $data['pagination'] = $pagination->render();


        $data['results'] =
        sprintf(
          $this->language->get('text_pagination'),
          ($reviews_total) ?  (($page - 1) * $limit) + 1 : 0,
          ((($page - 1) * $limit) > ($reviews_total - $limit)) ? $reviews_total : ((($page - 1) * $limit) + $limit),
          $reviews_total, ceil($reviews_total / $limit)
        );

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/company_reviews', $data));

    }

}