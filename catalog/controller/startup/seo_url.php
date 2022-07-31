<?php

class ControllerStartupSeoUrl extends Controller {

  private $aliases = array(
      '/' => 'common/home',
      'faq' => 'information/faq',
      'gifts' => 'information/gifts',
      'sitemap' => 'information/sitemap',
      'reviews' => 'information/company_reviews',
      'contact' => 'information/contact',
      'blog' => 'information/bloglist',
      'news' => 'information/newslist',
      'sitemap.xml' => 'extension/feed/google_sitemap',
      'googlebase.xml' => 'extension/feed/google_base',
      'cart' => 'checkout/cart',
      'uploadReviewPhotos' => 'product/upload/image',
  );

  public function index() {
    // Add rewrite to url class
    if ($this->config->get('config_seo_url')) {
      $this->url->addRewrite($this);
    }

    // check local aliases
    if (isset($this->request->get['_route_'])) {
      if (isset($this->aliases[$this->request->get['_route_']])) {
        $this->request->get['route'] = $this->aliases[$this->request->get['_route_']];
        return;
      }
    }

    // Decode URL
    if (isset($this->request->get['_route_'])) {
      $parts = explode('/', $this->request->get['_route_']);

      // remove any empty arrays from trailing
      if (utf8_strlen(end($parts)) == 0) {
        array_pop($parts);
      }

      foreach ($parts as $part) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

        if ($query->num_rows) {
          $url = explode('=', $query->row['query']);

          if ($url[0] == 'product_id') {
            $this->request->get['product_id'] = $url[1];
          }

          if ($url[0] == 'category_id') {
            if (!isset($this->request->get['path'])) {
              $this->request->get['path'] = $url[1];
            } else {
              $this->request->get['path'] .= '_' . $url[1];
            }
          }

          if ($url[0] == 'manufacturer_id') {
            $this->request->get['manufacturer_id'] = $url[1];
          }

          if ($url[0] == 'information_id') {
            $this->request->get['information_id'] = $url[1];
          }

          if ($url[0] == 'seo_link_id') {
            $this->request->get['seo_link_id'] = $url[1];
//            $this->request->get['route'] = 'error/not_found';
          }

          if ($url[0] == 'blog_id') {
            $this->request->get['blog_id'] = $url[1];
          }

          if ($url[0] == 'news_id') {
            $this->request->get['news_id'] = $url[1];
          }

          //if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'seo_link_id') {
          if (
                  $query->row['query'] &&
                  !in_array($url[0], array(
                      'information_id',
                      'manufacturer_id',
                      'category_id',
                      'product_id',
                      'seo_link_id',
                      'blog_id',
                      'news_id',
                  ))
          ) {
            $this->request->get['route'] = $query->row['query'];
          }
        } else {
          $this->request->get['route'] = 'error/not_found';

          break;
        }
      }

      if (!isset($this->request->get['route'])) {
        if (isset($this->request->get['product_id'])) {
          $this->request->get['route'] = 'product/product';
        } elseif (isset($this->request->get['path'])) {
          $this->request->get['route'] = 'product/category';
        } elseif (isset($this->request->get['manufacturer_id'])) {
          $this->request->get['route'] = 'product/manufacturer/info';
        } elseif (isset($this->request->get['information_id'])) {
          $this->request->get['route'] = 'information/information';
        } elseif (isset($this->request->get['blog_id'])) {
          $this->request->get['route'] = 'information/blog';
        } elseif (isset($this->request->get['news_id'])) {
          $this->request->get['route'] = 'information/news';
        } elseif (isset($this->request->get['seo_link_id'])) {
          $this->request->get['route'] = 'product/category';
        }
      }
    }
  }

  public function rewrite($link) {
    $url_info = parse_url(str_replace('&amp;', '&', $link));

    $url = '';

    $data = array();

    parse_str($url_info['query'], $data);

    foreach ($data as $key => $value) {
      if (isset($data['route'])) {
        if (
                ($data['route'] == 'product/product' && $key == 'product_id') ||
                ($data['route'] == 'product/category' && $key == 'seo_link_id') ||
                (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') ||
                ($data['route'] == 'information/information' && $key == 'information_id') ||
                ($data['route'] == 'information/blog' && $key == 'blog_id') ||
                ($data['route'] == 'information/news' && $key == 'news_id')
        ) {
          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int) $value) . "'");

          if ($query->num_rows && $query->row['keyword']) {
            $url .= '/' . $query->row['keyword'];

            unset($data[$key]);
          }
        } elseif ($key == 'path') {
          $categories = explode('_', $value);

          foreach ($categories as $category) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int) $category . "'");

            if ($query->num_rows && $query->row['keyword']) {
              $url .= '/' . $query->row['keyword'];
            } else {
              $url = '';

              break;
            }
          }

          unset($data[$key]);
        } elseif ($data['route'] == 'common/home') {
          $url .= '/';
          unset($data[$key]);
//        } elseif ($data['route'] == 'information/contact') {
//          $url .= '/contact';
//          unset($data[$key]);
        } elseif ($data['route'] == 'information/bloglist') {
          $url .= '/blog';
          unset($data[$key]);
        } elseif ($data['route'] == 'information/newslist') {
          $url .= '/news';
          unset($data[$key]);
//        } elseif ($data['route'] == 'information/sitemap') {
//          $url .= '/sitemap';
//          unset($data[$key]);
        } elseif (in_array($data[$key], $this->aliases)) {
          $url .= '/' . array_search($data[$key], $this->aliases);
          unset($data[$key]);
        }
      }
    }

    if ($url) {
      unset($data['route']);

      $query = '';

      if ($data) {
        foreach ($data as $key => $value) {
          $query .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string) $value));
        }

        if ($query) {
          $query = '?' . str_replace('&', '&amp;', trim($query, '&'));
        }
      }

      return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
    } else {
      return $link;
    }
  }

}
