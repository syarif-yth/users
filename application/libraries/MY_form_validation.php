<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ----------------------------------------------------------------------------
 *
 * Class        MY_Form_validation
 *
 * @project     StarterKIT
 * @author      Syarif YTH
 * @link        http://syarif-yth.github.io
 * ----------------------------------------------------------------------------
 */

/**
 * Class MY_Form_validation
 *
 * place into ./application/libraries
 * 
 * use like this
 * if ($this->form_validation->run($this) == FALSE)
 * {
 *
 * }
 * else
 * {
 *
 * }
 */
class MY_form_validation extends CI_Form_validation
{

  /**
   * Class properties - public, private, protected and static.
   * --------------------------------------------------------------------
   */
  /**
   * run ()
   * --------------------------------------------------------------------
   * @param   string $module
   * @param   string $group
   * @return  bool
   */

  public function run($module = '', $group = '')
  {
    (is_object($module)) AND $this->CI = &$module;
    return parent::run($group);
  }

	

	/**
	* custom validation, name function "{controller}_{field}"
	* extra validation, name function "valid_{field}"
	* database validation, name function "db_{field}_{model function name} 
	* model for validation, name function "model_{function name}
	*/

	public function __construct()
	{
		parent::__construct();
		$this->ci =& get_instance();
		$this->ci->load->helper('starterkit');
		$this->ci->db->db_debug = false;
	}

	public function valid_username($str)
	{
		$preg = preg_user($str);
		if(!$preg) {
			$this->ci->form_validation->set_message('valid_username', 'The {field} field may only contain alpha-numeric characters, underscores, and dashes.');
			return false;
		} else { return true; }
	}

	public function valid_password($str)
	{
		$preg = preg_pass($str);
		if(!$preg) {
			$this->ci->form_validation->set_message('valid_password', 'The {field} field must contain uppercase letters, lowercase letters, numbers and special characters.');
			return false;
		} else { return true; }
	}

	public function db_username_is_unique($str)
	{
		$where = array('username' => $str);
		$is_unique = $this->model_is_unique($where, 'users');
		if($is_unique['code'] != 200) {
			if($is_unique['code'] == 400) {
				$this->ci->form_validation->set_message('db_username_is_unique', 'The {field} field must contain a unique value.');
			} else {
				$this->ci->form_validation->set_message('db_username_is_unique', $is_unique['message']);
			}
			return false;
		} else { return true; }
	}

	public function db_email_is_unique($str)
	{
		$where = array('email' => $str);
		$is_unique = $this->ci->model_is_unique($where, 'users');
		if($is_unique['code'] != 200) {
			if($is_unique['code'] == 400) {
				$this->ci->form_validation->set_message('db_email_is_unique', 'The {field} field must contain a unique value.');
			} else {
				$this->ci->form_validation->set_message('db_email_is_unique', $is_unique['message']);
			}
			return false;
		} else { return true; }
	}
	public function db_email_is_exist($str)
	{
		$where = array('email' => $str);
		$is_exist = $this->ci->model_is_exist($where, 'users');
		if($is_exist['code'] != 200) {
			if($is_exist['code'] == 400) {
				$this->ci->form_validation->set_message('db_email_is_exist', 'Email not registed.');
			} else {
				$this->ci->form_validation->set_message('db_email_is_exist', $is_exist['message']);
			}
			return false;
		} else { return true; }
	}

	public function db_username_is_exist($str)
	{
		$where = array('username' => $str);
		$is_exist = $this->ci->model_is_exist($where, 'users');
		if($is_exist['code'] != 200) {
			if($is_exist['code'] == 400) {
				$this->ci->form_validation->set_message('db_username_is_exist', 'Username not registed.');
			} else {
				$this->ci->form_validation->set_message('db_username_is_exist', $is_unique['message']);
			}
			return false;
		} else { return true; }
	}

	public function recovery_email($str)
	{
		$where = array('email' => $str);
		$is_exist = $this->ci->model_is_exist($where, 'users');
		if($is_exist['code'] != 200) {
			if($is_exist['code'] == 400) {
				$this->ci->form_validation->set_message('recovery_email', 'The Email or username not registed.');
			} else {
				$this->ci->form_validation->set_message('recovery_email', $is_unique['message']);
			}
			return false;
		} else { return true; }
	}

	public function recovery_username($str)
	{
		$where = array('username' => $str);
		$is_exist = $this->ci->model_is_exist($where, 'users');
		if($is_exist['code'] != 200) {
			if($is_exist['code'] == 400) {
				$this->ci->form_validation->set_message('recovery_username', 'The Email or username not registed.');
			} else {
				$this->ci->form_validation->set_message('recovery_username', $is_unique['message']);
			}
			return false;
		} else { return true; }
	}





	/**
	 * MODEL for validation with database data
	 */
	private function model_is_unique($where, $table)
	{
		$this->ci->db->where($where);
		$kueri = $this->ci->db->get($table);
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $err;
		} else {
			$res['code'] = ($kueri->num_rows() == 0) ? 200 : 400;
			return $res;
		}
	}
	private function model_is_exist($where, $table)
	{
		$this->ci->db->where($where);
		$kueri = $this->ci->db->get($table);
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $err;
		} else {
			$res['code'] = ($kueri->num_rows() == 1) ? 200 : 400;
			return $res;
		}
	}




}   
// End of MY_Form_validation Class.
/**
 * ----------------------------------------------------------------------------
 * Filename: MY_Form_validation.php
 * Location: ./application/libraries/MY_Form_validation.php
 * ----------------------------------------------------------------------------
 */ 
