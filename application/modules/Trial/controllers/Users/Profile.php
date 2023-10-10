<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class Profile extends RestController 
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

	function index_get()
	{
		$res['status'] = true;
		$res['message'] = 'Welcome to APP StarterKit';
		$this->response($res);
	}
}
