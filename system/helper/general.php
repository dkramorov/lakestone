<?php
function token($length = 32) {
	// Create random token
	$string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	
	$max = strlen($string) - 1;
	
	$token = '';
	
	for ($i = 0; $i < $length; $i++) {
		$token .= $string[mt_rand(0, $max)];
	}	
	
	return $token;
}

/**
 * Backwards support for timing safe hash string comparisons
 * 
 * http://php.net/manual/en/function.hash-equals.php
 */

if(!function_exists('hash_equals')) {
	function hash_equals($known_string, $user_string) {
		$known_string = (string)$known_string;
		$user_string = (string)$user_string;

		if(strlen($known_string) != strlen($user_string)) {
			return false;
		} else {
			$res = $known_string ^ $user_string;
			$ret = 0;

			for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);

			return !$ret;
		}
	}
}

/**
 * Проверяет массив на пустоту
 * @param $arr
 * @return bool|void
 */
if (!function_exists('eRU')) {
    function eRU($arr) {
        if (is_array($arr) && !empty($arr)) {
            return true;
        }
        return false;
    }
}

/**
 * Печатает массив
 * @param $arr
 * @param $show
 * @param bool|false $break
 */
if (!function_exists('pRU')) {
    function pRU($arr, $show, $break = false) {
        if ($show == 'all') {
            echo "<pre>";
            print_r($arr);
            echo "</pre>";
            $break ? die() : '';
        }
    }
}

/**
 * Сохраняет переменную в файл
 * вывод в файл результатов работы чего-либо
 * @param $var
 */
if (!function_exists('save2FileVariable')) {
    function save2FileVariable($var, $file = "variables.txt", $append = false) {
        ob_start();
        if (eRU($var)) {
            print_r($var);
        } else {
            echo $var;
        }
        $out = '<pre>' . ob_get_contents() . '</pre>';
        ob_end_clean();
        file_put_contents(DIR_LOGS . $file, $out, $append);
    }
}

/**
 * Формирует csv на основе входных данных и сокраха¤ет его по пути $file
 * @param $data
 * @param $headerName
 * @param $file
 *
 */
if (!function_exists('getCsv')) {
    function getCsv($data, $file) {

        $values = $data['VALUES'];
        $columns = $data['COLUMNS'];
        $csv = "";

        if (eRU($values)) {
            if (eRU($columns)) {
                foreach ($columns as $column) {
                    $csv .= $column . ';';
                }
                $csv .= PHP_EOL . PHP_EOL;
            }
            foreach ($values as $params) {
                foreach ($params as $param) {
                    $csv .= $param . ';';
                }
                $csv .= PHP_EOL;
            }
        }
        file_put_contents($file, $csv);
    }
}