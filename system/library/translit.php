<?php
class Translit {

	private $cyr = array('ё', 'ж', 'ц', 'ч', 'ш', 'щ', 'ю', 'я', 'Ё', 'Ж', 'Ц', 'Ч', 'Ш', 'Щ', 'Ю', 'Я');
	private $lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
	private $a_cyr = 'АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ';
	private $a_lat = 'ABVGDEZIJKLMNOPRSTUFH_I_Eabvgdezijklmnoprstufh_i_e';

	public function __construct() {
		$this->a_cyr = array_merge($this->cyr, preg_split('//u', $this->a_cyr, null, PREG_SPLIT_NO_EMPTY));
		$this->a_lat = array_merge($this->lat, str_split($this->a_lat));
	}

	public function cyr2lat($str) {
	    mb_regex_encoding('UTF-8');
            return str_replace($this->a_cyr, $this->a_lat, $str);
	}

}
