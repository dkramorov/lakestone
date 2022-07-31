<?php
/**
 * TODO: ALTER TABLE `oc_external_review` ADD `hash` VARCHAR(32) NOT NULL AFTER `external_link`;
 * TODO: UPDATE `oc_external_review` SET `hash` = MD5(CONCAT_WS('|',`date_review`,`author_name`,`comment`,`values`,`defects`)) WHERE 1
 * TODO: ALTER TABLE `oc_external_review` ADD UNIQUE(`hash`);
 */
class ModelToolYamarketParser extends Model {
	/**
	 * @param array $reviews
	 * @return array
	 */
	public function addReviews($reviews = array()) {
		$data = array(
			'new' => 0,
			'total' => 0
		);

		$result = $this->db->query("SELECT `date_added` FROM " . DB_PREFIX . "external_review ORDER BY 1 DESC LIMIT 1");

		$lastDate = $result->row['date_added'];

		foreach ($reviews as $v) {
			$v['Comments'] = $this->db->escape($v['Comments']);
			$v['Values'] = $this->db->escape($v['Values']);
			$v['Flaws'] = $this->db->escape($v['Flaws']);
			$v['User'] = $this->db->escape($v['User']);
//			$v['DateReview'] = date('Y-m-d', $v['timestamp']);

			// TODO: source_id пока для всех 1
			$this->db->query("INSERT INTO " .
				DB_PREFIX . "external_review 
			SET 
				`source_id` = 1, 
				`date_review` = '" . $v['DateReview'] . "',
				`rating_value` =" . (int)$v['Rating'] . ",
				`rating_text` = '" . $v['RatingLabel'] . "',
				`author_name` = '" . $v['User'] . "',
				`author_avatar_link` = '" . $v['Avatar'] . "',
				`author_region` = '" . $v['Region'] . "',
				`comment` = '" . $v['Comments'] . "',
				`values` = '" . $v['Values'] . "',
				`defects` = '" . $v['Flaws'] . "',
				`date_added` = NOW(),
				`status` = 1,
				`hash` = MD5(CONCAT_WS('|','" . $v['DateReview'] . "','" . $v['User'] . "','" . $v['Comments'] . "','" .
				$v['Values'] . "','" . $v['Flaws'] . "'))
			on duplicate key update
				`date_review` = '" . $v['DateReview'] . "',
				`rating_value` =" . (int)$v['Rating'] . ",
				`rating_text` = '" . $v['RatingLabel'] . "',
				`author_avatar_link` = '" . $v['Avatar'] . "',
				`date_modified` = NOW()");
		}

		$result = $this->db->query("SELECT COUNT(*) as 'count' FROM " . DB_PREFIX . "external_review WHERE `date_added` > '{$lastDate}'");

		$data['new'] = $result->row['count'];

		$result = $this->db->query("SELECT COUNT(*) as 'count' FROM " . DB_PREFIX . "external_review WHERE `status` = 1");

		$data['total'] = $result->row['count'];

		return $data;
	}
}