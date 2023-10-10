<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Rules
{
	public function __construct() 
	{
		$this->ci =& get_instance();
	}

	private function get_uri()
	{
		$func = $this->ci->router->fetch_method();
		if($func == 'index') {
			$uri = $this->ci->router->fetch_class();
		} else {
			$uri = $func;
		}
		return $uri;
	}

	public function check()
	{
		$method = $this->ci->input->method();
		$func = $this->get_uri();
		$res['body'] = array($method, $func);
		return $res;
	}

	private function db_rules()
	{
		$rule = array(
			'admin' => array(
				'get' => ['update',''],
				'post' => ['index',''],
				'put' => ['index',''],
				'delete' => ['index',''],
			)
			);
	}
}
?>
