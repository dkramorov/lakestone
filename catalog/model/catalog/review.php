<?php
class ModelCatalogReview extends Model {
	public function addReview($product_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "review SET review_type = " . (int)$data['review_type'] . ", useful_photo = ". (int) $data['rating_photo'] . ", useful_description = ". (int) $data['rating_description'] . ", author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");

		$review_id = $this->db->getLastId();
    
    $config = $this->config->get('review_files');
    $counter = 0;
    foreach ($data as $key => $val) {
      if (substr($key, 0 , 4) === 'file') {
        $file = str_replace('//', '/', DIR_UPLOAD . '/' . $val);
        $file_new = '/upload/' . basename($file);
        if (!file_exists($file)) {
          continue;
        }
        if (filesize($file) > $config['max_size']) {
          continue;
        }
        if (!in_array(mime_content_type($file), $config['acceptable'])) {
          continue;
        }
        if ($counter++ >= $config['max_count']) {
          break;
        }
        if (!rename($file, str_replace('//', '/', DIR_IMAGE . '/' . $file_new))) continue;
        $this->db->query("INSERT INTO " . DB_PREFIX . "review_image SET review_id = $review_id, image = '". $this->db->escape($file_new) ."', status = 1");
      }
    }
    
    if (in_array('review', (array)$this->config->get('config_mail_alert'))) {
			$this->load->language('mail/review');
			$this->load->model('catalog/product');

			if ($data['review_type'] < 2) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
			} elseif ($data['review_type'] == 2) {
				$this->load->model('catalog/blog');
				$product_info = $this->model_catalog_blog->getInformation($product_id);
				if ($product_info) {
					$product_info['name'] = $product_info['title'];
				}
			}

			$subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $data['rating']) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8') . "\n\n";

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($message);
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_alert_email'));

			foreach ($emails as $email) {
				if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20, $type = 0, $not_empty = false) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.like, r.unlike, r.useful_photo, r.useful_description, r.review_id, r.author, r.rating, r.text,
			r.product_id, /*pd.name, p.price, p.image, */r.date_added, re.respond, re.date_responded
			FROM " . DB_PREFIX .  "review r
			/*LEFT JOIN " .  DB_PREFIX .  "product p ON (r.product_id = p.product_id)
			LEFT JOIN " .  DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)*/
			LEFT JOIN " .  DB_PREFIX . "respond re ON (r.review_id = re.review_id AND re.status = '1')
			WHERE r.product_id = '" .  (int) $product_id .  "'
			". ($not_empty ? " AND r.author != '_only_rating'" : '') ."
			/*AND p.date_available <= NOW()
			AND p.status = '1' */AND r.status = '1'
			AND r.review_type = '" . (int) $type .  "'
			ORDER BY r.date_added DESC LIMIT " .  (int) $start .  "," .  (int) $limit
		);

		return $query->rows;
	}
  
  public function getReviewImagesProduct(int $product_id): array {
	  
	  $query = $this->db->query("SELECT ri.image FROM " . DB_PREFIX .  "review r LEFT JOIN " . DB_PREFIX .  "review_image ri ON r.review_id = ri.review_id WHERE r.product_id = ". (int) $product_id ." AND ri.image IS NOT NULL AND ri.status = 1 AND r.status = 1 ORDER BY r.date_added desc");
	  return $query->rows;
  
	}
	
  public function getReviewImages($review_id) {
  
    $query = $this->db->query("SELECT i.image FROM oc_review_image AS i WHERE i.review_id = '" .  (int)$review_id .  "' AND i.status = '1'");
  
    return $query->rows;

	}

	public function getReviewsByCategoryId($category_id, $start = 0, $limit = 20, $type = 0) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$query = $this->db->query("SELECT r.like, r.unlike, r.useful_photo, r.useful_description, r.review_id, r.author, r.rating, r.text,
			r.product_id, /*pd.name, p.price, p.image, */r.date_added, re.respond
			FROM " . DB_PREFIX .  "review r
			/*LEFT JOIN " .  DB_PREFIX .  "product p ON (r.product_id = p.product_id)
			LEFT JOIN " .  DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)*/
			LEFT JOIN " .  DB_PREFIX . "respond re ON (r.review_id = re.review_id AND re.status = '1')
			WHERE r.product_id IN (SELECT product_id FROM " . DB_PREFIX .  "product_to_category
			WHERE category_id = '" .  (int)$category_id .  "')
			AND r.status = '1' AND r.review_type = '" . (int)$type .  "'
			ORDER BY r.date_added DESC LIMIT " .  (int)$start .  "," .  (int)$limit
		);

		return $query->rows;
	}

	public function getTotalReviewsByProductId($product_id, $type = 0, $not_empty = false) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . ($not_empty ? " AND r.author != '_only_rating'" : '') . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND r.review_type = '" . (int)$type . "'
		");

		return $query->row['total'];
	}

	public function likeReview($review_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "review SET `like` = `like` + 1 WHERE `status` = 1 AND `review_id` = '" . (int)$review_id . "'");
	}

	public function unlikeReview($review_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "review SET `unlike` = `unlike` + 1 WHERE `status` = 1 AND `review_id` = '" . (int)$review_id . "'");
	}

	public function getLikeReview($review_id) {
		$query = $this->db->query("SELECT `like`, `unlike` FROM " . DB_PREFIX . "review WHERE `status` = 1 AND `review_id` = '" . (int)$review_id . "'");
		return $query->row;
	}

}
