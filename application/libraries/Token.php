
<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/third_party/JWT/JWT.php';
require APPPATH.'/third_party/JWT/ExpiredException.php';
require APPPATH.'/third_party/JWT/BeforeValidException.php';
require APPPATH.'/third_party/JWT/SignatureInvalidException.php';
require APPPATH.'/third_party/JWT/JWK.php';
require APPPATH.'/third_party/JWT/Key.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\Key;

class Token
{
	protected $key;
	protected $algorithm;
	protected $header;
	protected $expire;
	protected $name_cookie;
	protected $key_cookie;
	protected $time_cookie;

	public function __construct() 
	{
		$this->ci =& get_instance();
		$this->ci->load->config('jwt');

		$this->key = $this->ci->config->item('jwt_key');
		$this->algorithm = $this->ci->config->item('jwt_algorithm');
		$this->header = $this->ci->config->item('token_header');
		$this->expire = $this->ci->config->item('token_expire_time');
		// EXTRA
		$this->name_cookie = $this->ci->config->item('name_cookie');
		$this->key_cookie = $this->ci->config->item('key_cookie');
		$this->time_cookie = $this->ci->config->item('time_cookie');
	}


	public function create($nip, $extra = null)
	{
		$user_agent = $this->get_device();
		$exp = time()+$this->expire;
		$payload = array(
			'sub' => $user_agent,
			'aud' => $nip,
			'exp' => $exp);
		if($extra) {
			$payload = array_merge($payload, array('ext' => $extra));
		}

		$encode = $this->encode($payload);
		if($encode['code'] != 200) {
			return $encode;
		} else {
			$token = $encode['body']['token'];
			$this->set_cookie($token, $nip);
			$body = array('token' => $token,
				'expired' => $exp);
			return $this->res($body, 200);
		}
	}

	public function validate()
	{
		$cookie = $this->get_cookie();
		if($cookie['code'] == 200) {
			$token = $cookie['body']['token'];
			$decode = $this->decode($token);
			if($decode['code'] == 200) {
				$user = $decode['body']['data']->aud;
				$device = $this->valid_device($decode);
				$nip = $this->get_nip();
				if(($device['code'] == 200) && ($nip == $user)) {
					return $this->res('Token is valid');
				} else { 
					return $this->res('Access forbidden!', 403); 
				}
			} else { 
				if($decode['body']['message'] === 'Expired token') {
					$nip = $this->get_nip();
					if($nip) {
						$create = $this->create($nip);
						return $create;
					} else { return $decode; }
				} else { return $decode; }
			}
		} else {
			$valid = $this->valid_headers();
			return $valid;
		}
	}

	public function destroy()
	{
		$this->ci->load->helper('cookie');
		delete_cookie($this->name_cookie);
		delete_cookie($this->key_cookie);
	}

	// PRIVATE
	private function encode($data)
	{
		try {
			$encode = JWT::encode($data, $this->key, $this->algorithm);
			$body['token'] = $encode;
			$res = $this->res($body, 200);
		} catch(Exception $err) {
			$res = $this->res($err->getMessage(), 500);
		}
		return $res;
	}

	private function decode($token)
	{
		try {
			$key = new Key($this->key, $this->algorithm);
			$decoded = JWT::decode($token, $key);
			$body['data'] = $decoded;
			$res = $this->res($body, 200);
		} catch(Exception $err) {
			$res = $this->res($err->getMessage(), 403);
		}
		return $res;
	}

	private function get_device()
	{
		$this->ci->load->library('user_agent');
		$ip_address = $this->ci->input->ip_address();
		if(!$this->ci->agent->is_browser()) {
			$string = $this->ci->agent->agent_string();
			$get = $ip_address.'/'.$string;
		} else {
			$version = $this->ci->agent->version();
			$browser = $this->ci->agent->browser();
			$platform = $this->ci->agent->platform();
			$get = $ip_address.'/'.$browser.'/'.$version.'/'.$platform;
		}
		$get = str_replace(' ','/',$get);
		return strtolower($get);
	}

	private function set_cookie($token, $nip)
	{
		$this->ci->load->helper('cookie');
		$token = array(
			'name' => $this->name_cookie,
			'value'=> $token,
			'expire' => $this->time_cookie,
			'secure' => true,
			'httponly' => true);
		$this->ci->input->set_cookie($token);

		$user = array(
			'name' => $this->key_cookie,
			'value'=> $nip,
			'expire' => $this->time_cookie,
			'secure' => true,
			'httponly' => true);
		$this->ci->input->set_cookie($user);
	}

	private function get_cookie()
	{
		$this->ci->load->helper('cookie');
		$cookie = get_cookie($this->name_cookie);
		if(empty($cookie)) {
			return $this->res('Token not found', 404);
		} else {
			$body['token'] = $cookie;
			return $this->res($body);
		}
	}

	private function get_nip()
	{
		$this->ci->load->helper('cookie');
		$nip = get_cookie($this->key_cookie);
		if(empty($nip)) {
			return false;
		} else {
			return $nip;
		}
	}

	private function get_headers()
	{
		$head = $this->ci->input->request_headers();
		if(!empty($head) && is_array($head)) {
			$config_head = ucfirst(trim($this->header));
			if(empty($head[$config_head])) {
				return $this->res('Access forbidden!', 403);
			} else {
				$bearer = $head[$config_head];
				$token = explode(" ", $bearer)[1];
				$body['token'] = $token;
				return $this->res($body);
			}
		} else {
			return $this->res('Access forbidden!', 403);
		}
	}

	private function valid_headers()
	{
		$head = $this->get_headers();
		if($head['code'] == 200) {
			$token = $head['body']['token'];
			$decode = $this->decode($token);
			if($decode['code'] == 200) {
				$user = $decode['body']['data']->aud;
				$device = $this->valid_device($decode);
				$nip = $this->get_nip();
				if(($device['code'] == 200) && ($nip == $user)) {
					$this->set_cookie($token, $nip);
					return $this->res('Token is valid');
				} else { 
					return $this->res('Access forbidden!', 403); 
				}
			} else { 
				if($decode['body']['message'] === 'Expired token') {
					$nip = $this->get_nip();
					if($nip) {
						$create = $this->create($nip);
						return $create;
					} else { return $decode; }
				} else { return $decode; }
			}
		} else { return $head; }
	}

	private function valid_device($decode)
	{
		$user_agent = $this->get_device();
		$sub = $decode['body']['data']->sub;
		if($user_agent !== $sub) {
			return $this->res('Access forbidden!', 403);
		} else {
			$body = array('platform' => $sub);
			return $this->res($body);
		}
	}



	// HELPER
	private function res($data, $code = null)
	{
		if(!$code) {
			$status = array('status' => true);
			$code = 200;
		} else {
			$status = array('status' => ($code != 200) ? false : true);
		}

		$res['code'] = $code;
		if(is_array($data)) {
			$res['body'] = array_merge($status, $data);
		} else {
			$res['body'] = array_merge($status, 
				array('message' => $data));
		}
		return $res;
	}
}
?>
