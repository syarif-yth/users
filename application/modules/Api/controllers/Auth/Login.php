<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."libraries/format.php";
require APPPATH."libraries/RestController.php";

use chriskacerguis\RestServer\RestController;

class Login extends RestController 
{
	// PINDAH KE CONFIG STARTERKIT
	var $count_mistake_login;
	var $max_login_mistake;
	var $time_penalty_login;

	function __construct()
	{
		parent::__construct();
		$this->load->library('token');
		$this->load->library('form_validation');
		$this->load->model('model_auth');
		$this->count_mistake_login = false;
		$this->max_login_mistake = 10;
		$this->time_penalty_login = 86400/4; // 1DAY/4=6HOURS
	}

	function index_post()
	{
		$is_valid = $this->validate();
		if($is_valid === true) {
			$username = $this->post('username', true);
			$check = $this->model_auth->check_username_login($username);
			if($check['code'] == 200) {
				$nip = $check['data'];
				$password = $this->post('password', true);
				$remember = $this->post('remember', true);
				// search fo
				// $pass = encrypt_pass($nip, $password);

				$res['status'] = true;
				$res['posted'] = $check;
				$this->response($res);

			} else {
				$mistake = $this->count($check);
				$res['status'] = false;
				$res['message'] = $mistake['message'];
				$this->response($res, $mistake['code']);
			}
		} else {
			$res['status'] = false;
			$res['message'] = 'Your request not valid';
			$res['errors'] = [$is_valid];
			$this->response($res, 400);
		}
	}




	private function count($response)
	{
		if($this->count_mistake_login) {
			if($response['code'] == 400) {
				$attempt = $this->session->userdata('attempt_login');
				$attempt++;
				$this->session->set_userdata('attempt_login', $attempt);
				if($attempt >= $this->max_login_mistake) {
					$attempt = 0;
					$this->session->set_userdata('attempt_login', $attempt);
					$this->session->set_tempdata('penalty_login', true, $this->time_penalty_login);
					$res['code'] = $response['code'];
					$res['message'] = 'Login errors occur too often, please try again after 6 hours';
					return $res;
				} else { return $response; }
			} else { return $response; }
		} else { return $response; }
	}

	private function validate()
  {
		$this->form_validation->set_data($this->post());
    $data = array(
      array('field' => 'username',
        'rules' => 'trim|required|min_length[5]|max_length[20]|valid_username'),
      array('field' => 'password',
        'rules' => 'required|min_length[5]|valid_password')
    );
    $this->form_validation->set_rules($data);
		if($this->form_validation->run($this) == false) {
			return $this->form_validation->error_array();
		} else {
			return true;
		}
  }
}
