<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('now')) {
	function now($format = null)
	{
		$time = new DateTime();
		if(!$format) {
			$now = $time->format('H:i:s d-m-Y');
		} else {
			$now = $time->format($format);
		}
		return $now;
	} 
}

?>
