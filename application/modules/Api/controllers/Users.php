<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// require APPPATH."libraries/format.php";
// require APPPATH."libraries/RestController.php";

// use chriskacerguis\RestServer\RestController;

include_once (dirname(__FILE__)."\Auth\Login.php");

class Users extends Login
{
	function __construct()
	{
		parent::__construct();
	}

	function index_get()
	{
		$res['status'] = true;
		$res['message'] = 'Welcome to StarterKit CTR Users';
		$res['cek'] = dirname(__FILE__);
		$res['oke'] = $this->oke();
		$this->response($res);
	}
}
