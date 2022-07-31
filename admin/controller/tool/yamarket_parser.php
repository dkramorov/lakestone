<?php

/**
 * Class ControllerToolYamarketParser
 */
class ControllerToolYamarketParser extends Controller {

	private $error = array();
	/**
	 * @var DOMXPath
	 */
	private $YP;

	public function index() {

		$this->load->language('tool/yamarket_parser');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_input'] = $this->language->get('text_input');
		$data['text_target'] = $this->language->get('text_target');
		$data['text_div'] = $this->language->get('text_div');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['button_download'] = $this->language->get('button_download');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_parsing'] = $this->language->get('button_parsing');
		$data['button_reset'] = $this->language->get('button_reset');

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tool/yamarket_parser', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('tool/yamarket_parser', 'token=' . $this->session->data['token'], true);
		// $data['error_warning'] = 'test error';

		$data['input_html'] = '';
		$data['reviews'] = array();
		$Base = 'https://market.yandex.ru';
		$URL = '/shop--lakestone-ofitsialnyi-magazin/386426/reviews?page=1';
		$data['target_url'] = $Base . $URL;

		if (isset($this->request->post['input_html'])) {
			$YX = new DOMDocument('1.0', 'UTF-8');
			$New = $Records = 0;
			/*
						$YMCache = new Cache('file', 3600 * 24 * 365);
						$reviews_array = $YMCache->get('yamarket_lakestone');

						if (!$reviews_array) {
							$reviews_array = json_decode(file_get_contents('../ymarket_lakestone.json'), true);
						}
			*/
			$reviews_array = array();

			@$YX->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . html_entity_decode($this->request->post['input_html']), LIBXML_HTML_NODEFDTD);
			$this->YP = new DOMXPath($YX);

			$RL = array();
			$RR = $this->YP->query('//div[@id="scroll-to-reviews-list"][@data-tid="f22d1daa"]');
			if ($RR->length == 0) {
				$data['error_warning'] = 'Не найден основной блок отзывов';
				$data['input_html'] = $YX->saveXML();
			} else {
				$YX->formatOutput = true;
				$data['input_html'] = $YX->saveXML($RR->item(0));
//				$RL = $this->YP->query('.//div[contains(@class, "n-product-review-item ")]', $RR->item(0));
				$RL = $this->YP->query('.//div[@data-review-id]', $RR->item(0));
			}

			setlocale(LC_TIME, 'ru_RU.UTF-8');
			$MoscowTZ = new DateTimeZone('Europe/Moscow');
			$NOW = new DateTime('now', $MoscowTZ);
			$Yesterday = trim(strftime('%e %B', $NOW->sub(new DateInterval('P1D'))->getTimestamp()));

			foreach ($RL as $RN) {
				$res = array(
					'Values' => '',
					'Flaws' => '',
					'Comments' => ''

				);
				$Records++;
				
				$NL = $this->YP->query('.//div[@itemprop="review"]', $RN);
				if ($NL->length > 0) {
					 $res['User'] = $this->getMeta('author', $NL->item(0));
					 $res['Date'] = $this->getMeta('datePublished', $NL->item(0));
					 $res['Rating'] = $this->getMeta('ratingValue', $NL->item(0));
					 $description = $this->getMeta('description', $NL->item(0));
//					 $description = 'Достоинства: Достойное качество пошива.. Очень красивая сумочка. Приятно держать в руках, добротная натуральная кожа. Недостатки: Нет. Комментарий: Приятный бонус - дисконтная карта клиента со скидной.';
//					d($description);
//					 if (preg_match('/(Достоинства: (?P<values>.*))?(Недостатки: (?P<flaws>.*))?(Комментарий: (?P<comments>.*))?/u', $description, $arr)) {
					 if (preg_match('/Комментарий: (?P<comments>.*)/u', $description, $arr)) {
						 $res['Comments'] = $arr[1];
						 $description = str_replace($arr[0], '', $description);
					 }
					 if (preg_match('/Недостатки: (?P<flaws>.*)/u', $description, $arr)) {
						 $res['Flaws'] = $arr[1];
						 $description = str_replace($arr[0], '', $description);
					 }
					 if (preg_match('/Достоинства: (?P<values>.*)/u', $description, $arr)) {
						 $res['Values'] = $arr[1];
						 $description = str_replace($arr[0], '', $description);
					 }
				} else {
					$data['error_warning'] .= ' Не найден блок schema.org.';
				}

				// User
/*				$NL = $this->YP->query('.//*[contains(@class, "n-product-review-user__name")]', $RN);
				if ($NL->length > 0) {
					$res['User'] = $NL->item(0)->nodeValue;
				} else {
					$res['User'] = '';
				}*/

				// Avatar
//				$NL = $this->YP->query('.//img[contains(@class, "n-product-review-user__avatar")]', $RN);
//				$NL = $this->YP->query('.//div[@data-author-id]/*/picture/source/img', $RN);
				$NL = $this->YP->query('.//div[@data-tid="29163484 be8ef234"]/picture/source/img', $RN);
				if ($NL->length > 0) {
					$res['Avatar'] = $NL->item(0)->getAttribute('src');
				} else {
					$res['Avatar'] = '';
				}

				// Rating
/*				$NL = $this->YP->query('.//*[contains(@class, "n-rating-stars")]', $RN);
				if ($NL->length > 0) {
					$res['Rating'] = $NL->item(0)->getAttribute('data-rate');
				} else {
					$res['Rating'] = 5;
				}*/

				// RLabel
//				$NL = $this->YP->query('.//*[contains(@class, "n-product-review-item__rating-label")]', $RN);
				$NL = $this->YP->query('.//div[@data-tid="6c07946b"]', $RN);
				if ($NL->length > 0) {
					$res['RatingLabel'] = $NL->item(0)->nodeValue;
				} else {
					$res['RatingLabel'] = '';
				}

				// Texts
				foreach ($this->YP->query('.//dl[contains(@class, "n-product-review-item__stat") and dt]', $RN) as $N) {
					if (preg_match('/Достоинства/i', $N->getElementsByTagName('dt')->item(0)->nodeValue)) {
						$res['Values'] = $N->getElementsByTagName('dd')->item(0)->nodeValue;
					}
					if (preg_match('/Недостатки/i', $N->getElementsByTagName('dt')->item(0)->nodeValue)) {
						$res['Flaws'] = $N->getElementsByTagName('dd')->item(0)->nodeValue;
					}
					if (preg_match('/Комментарий/i', $N->getElementsByTagName('dt')->item(0)->nodeValue)) {
						$res['Comments'] = $N->getElementsByTagName('dd')->item(0)->nodeValue;
					}
				}

				// Date
/*				$DR = $this->YP->query('.//*[contains(@class, "n-product-review-item__date-region")]', $RN)->item(0)->nodeValue;
				$DRa = explode(',', $DR);
				if ($DRa[0] == 'вчера') {
					$res['Date'] = $Yesterday;
				} else {
					$res['Date'] = $DRa[0];
				}*/
				$res['DateReview'] = $res['Date'];
				
				// Region
				$NL = $this->YP->query('.//span[@data-tid="ed7375bf"]', $RN);
				if ($NL->length > 0) {
					$RA = explode(',', $NL->item(0)->nodeValue);
					$res['Region'] = array_pop($RA);
				} else {
					$res['Region'] = '';
				}


				foreach ($reviews_array as &$review) {
/*					if (!isset($review['timestamp'])) {
						// $DT = trim(strftime('%e %B %Y', $this->getTimestamp($review['Date'])));
						$review['timestamp'] = $this->getTimestamp($review['Date']);
					}*/
					$comp = true;
					if (isset($review['Values']) and isset($res['Values'])) {
						$comp = (md5($res['Values']) == md5($review['Values']));
					}
					if (isset($review['Flaws']) and isset($res['Flaws'])) {
						$comp = (md5($res['Flaws']) == md5($review['Flaws']));
					}
					if (isset($review['Comments']) and isset($res['Comments'])) {
						$comp = (md5($res['Comments']) == md5($review['Comments']));
					}
					if (
						$res['User'] == $review['User'] and
						$res['Date'] == $review['Date'] and
						$comp
					)
						$res = false;
				}
				
				if (!empty($res)) {
					$reviews_array[] = $res;
					$New++;
				}
			}
			
			$this->load->model('tool/yamarket_parser');

			$result = $this->model_tool_yamarket_parser->addReviews($reviews_array);

			$data['success'] = 'Найдено ' . $Records . ' и добавлено ' . $result['new'] . ' новых отзыва. Всего отзывов: '
				. $result['total'];

			//array_multisort(array_column($reviews_array, 'timestamp'), SORT_DESC, $reviews_array);

			// $data['reviews'] = $reviews_array;
			//$data['success'] = 'Найдено ' . $Records . ' и добавлено ' . $New . ' новых отзыва. Всего отзывов: ' .sizeof($reviews_array);
			//$YMCache->set('yamarket_lakestone', $reviews_array);

		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tool/yamarket_parser', $data));

	}

	private function getTimestamp($date) {
		if (!preg_match('/\d{4}$/', $date)) {
			$date .= ' ' . date('Y');
		}

		$date = str_replace(array(
			'января',
			'февраля',
			'марта',
			'апреля',
			'мая',
			'июня',
			'июля',
			'августа',
			'сентября',
			'октября',
			'ноября',
			'декабря'
		), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), $date);

		$dt = date_parse_from_format('j n Y', $date);

		//$dt = strptime($date, '%e %B %Y');

		return mktime(0, 0, 0, $dt['month'], $dt['day'], $dt['year']);
	}
	
	private function getMeta(string $prop, DOMNode $node): string {
		$NL = $this->YP->query('.//meta[@itemprop="' . $prop . '"]', $node);
		if ($NL->length > 0)
			return (string) $NL->item(0)->getAttribute('content');
		else
			return '';
	}
}

?>
