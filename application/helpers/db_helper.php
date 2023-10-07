<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('set_column')) {
	function set_column($array = null)
	{
		$value = '';
		if(is_null($array)) {
			$value = '* ';
		} else {
			$last = count($array);
			foreach($array as $key => $val) {
				if(($key+1) === $last) {
					$value .= $val;
				} else {
					$value .= $val.", ";
				}
			}
		}
		return $value;
	}
}

?>

