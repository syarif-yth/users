<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class Login extends RestController 
{
	var $nip;
	function __construct()
	{
		parent::__construct();
		$this->nip = '20231009165242';
		$this->load->library('token');
	}

	function index_post()
	{
		$auth = $this->token->create($this->nip);
		$res['status'] = true;
		$res['message'] = 'Login';
		$res['auth'] = $auth['body'];
		$this->response($res);
	}
}
