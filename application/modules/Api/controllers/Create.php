<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class Create extends RestController 
{
	var $nip;
	function __construct()
	{
		parent::__construct();
		$this->load->library('token');
		$this->nip = '20231009165242';
	}

	function index_get()
	{
		$res = $this->token->create($this->nip);
		$this->response($res['body'], $res['code']);
	}
}
