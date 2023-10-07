<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class App extends RestController 
{
	function __construct()
	{
		parent::__construct();
	}

	function index_get()
	{
		$res['status'] = true;
		$res['message'] = 'Welcome to APP StarterKit';
		$this->response($res);
	}
}
