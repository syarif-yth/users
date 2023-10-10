<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_auth extends CI_Model
{
	private $tb_users;
	private $users_key;
	function __construct()
	{
		parent::__construct();
		$this->tb_users = $this->db->protect_identifiers('users', TRUE);
		$this->users_key = 'nip';
		$this->load->helper('starterkit');
	}

	public function check_username_login($username)
	{
		$this->db->select($this->users_key);
		$this->db->where('username', $username);
		$kueri = $this->db->get_where($this->tb_users);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($kueri->num_rows() == 0) {
				$res = for_response('The username or password is incorrect.', 400);
			} else {
				$res['data'] = $kueri->result_array()[0][$this->users_key];
				$res = for_response($body);
			}
			return $res;
		}
	}
}
?>
