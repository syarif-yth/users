<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Cek
{
	public function __construct() 
	{
		$this->ci =& get_instance();
	}

	public function model_is_unique()
	{
		$this->ci->db->db_debug = false;
		$where = array('emails' => 'syari');
		$this->ci->db->where($where);
		$kueri = $this->ci->db->get('users');
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $err;
		} else {
			if($kueri->num_rows() == 0) {
				$res['code'] = 200;
			} else {
				$res['code'] = 400;
			}
			return $res;
		}
	}
}
