<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class Access extends RestController 
{
	function __construct()
	{
		$this->load->library('rules');
		parent::__construct();
		
		
	}

	public function index_get()
	{
		// $method = $this->router->fetch_method();
		$user = array('admin','object');
		$cek = $this->rules->check();

		$res['status'] = true;
		$res['message'] = 'Access Get';
		// $res['uri'] = $method;
		$res['cek'] = $cek;
		$this->response($res);
	}

	public function cek_get()
	{
		// $method = $this->uri->segment(2);
		// $method = $this->router->fetch_class();
		// $method = $this->router->fetch_method();
		$cek = $this->rules->check();

		$res['status'] = true;
		// $res['uri'] = $method;
		$res['cek'] = $cek;
		$this->response($res);
	}

	public function ok_get()
	{
		$this->load->library('cek');
		$c = $this->cek->model_is_unique();
		$res['status'] = true;
		$res['data'] = $c;
		$this->response($res);
	}
}
?>
