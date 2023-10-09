<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class App extends RestController 
{
	var $nip;
	function __construct()
	{
		parent::__construct();
		$this->load->library('token');
		$auth = $this->token->validate();
		if($auth['code'] == 200) {
			$this->nip = '20231009165242';
		} else {
			$this->response($auth['body'], $auth['code']);
			die();
		}
	}

	function logout_get()
	{
		$log = $this->token->destroy();
		$res['status'] = true;
		$res['message'] = 'logout';
		$this->response($res);
	}

	function valid_get()
	{
		$get = $this->token->validate();
		$this->response($get['body'], $get['code']);
	}

	function index_get()
	{
		$res['status'] = true;
		$res['message'] = 'Welcome to APP StarterKit';
		$this->response($res);
	}
}
