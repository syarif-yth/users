<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/third_party/JWT/JWT.php';
require APPPATH.'/third_party/JWT/ExpiredException.php';
require APPPATH.'/third_party/JWT/BeforeValidException.php';
require APPPATH.'/third_party/JWT/SignatureInvalidException.php';
require APPPATH.'/third_party/JWT/JWK.php';
require APPPATH.'/third_party/JWT/Key.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\Key;

// 2 token = access_token & refresh_token
// access_token digunakan client untuk melakukan request
// refresh_token digunakan server untuk generate ulang access_token
// access_token bersifat public
// refresh_token bersifat private
// access_token 15mnt
// refresh_token akan disimpan db
// refresh_token akan hilang apabila logout
// refresh_token akam hilang apabila 1 hari tidak ada aktifitas, dan user akan diminta login kembali untuk membuat refresh_token dan access_token baru
// refresh_token akan menginduk pada device/useragent, 1 device 1 refresh_token

// untuk generate ulang access_token menggunakan refresh_token dan id_user
// syarat bisa regenerate 
// 1. database exist
// 2. platform sama
// 3. user sam
// 4. token expired
// 5. cookie not found
// 6. header not found

class Token
{
	protected $key;
	protected $algorithm;
	protected $header;
	protected $expire;
	protected $cookie ;
	protected $name_cookie ;

	public function __construct() 
	{
		$this->ci =& get_instance();
		$this->ci->load->config('jwt');

		$this->key = $this->ci->config->item('jwt_key');
		$this->algorithm = $this->ci->config->item('jwt_algorithm');
		$this->header = $this->ci->config->item('token_header');
		$this->expire = $this->ci->config->item('token_expire_time');
		$this->cookie = $this->ci->config->item('token_cookie');
		$this->name_cookie = $this->ci->config->item('name_cookie');
	}
	
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

	private function set_access($user_agent, $nip)
	{
		$exp = time()+$this->expire;
		$payload = array(
			'sub' => $user_agent,
			'aud' => (int) $nip,
			'exp' => $exp);
		$token = $this->encode($payload);
		if($token['code'] != 200) {
			return $token;
		} else {
			$body['data'] = array(
				'token' => $token['body']['token'],
				'expired' => $exp);
			return $this->res($body, 200);
		}
	}

	private function set_cookie($token)
	{
		$this->ci->load->helper('cookie');
		$cookie = array(
			'name' => $this->name_cookie,
			'value'=> $token,
			'expire' => $this->expire,
			'secure' => TRUE);
		$this->ci->input->set_cookie($cookie);
	}

	private function insert_db($user_agent, $nip)
	{
		$this->ci->load->database();
		$this->ci->db->set('nip', $nip);
		$this->ci->db->set('platform', $user_agent);
		$kueri = $this->ci->db->insert('users_token');
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $this->db_error($err);
		} else {
			return $this->res('Inserted', 200);
		}
	}

	private function delete_db($user_agent, $nip)
	{
		$this->ci->load->database();
		$this->ci->db->where('nip', $nip);
		$this->ci->db->where('platform', $user_agent);
		$kueri = $this->ci->db->delete('users_token');
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $this->db_error($err);
		} else {
			return $this->res('Deleted', 200);
		}
	}

	private function check_db($platform, $nip)
	{
		$this->ci->load->database();
		$this->ci->db->where('nip', $nip);
		$this->ci->db->where('platform', $platform);
		$kueri = $this->ci->db->get('users_token');
		if(!$kueri) {
			$err = $this->ci->db->error();
			return $this->db_error($err);
		} else {
			if($kueri->num_rows() == 1) {
				return $this->res('Token exist', 200);
			} else {
				return $this->res('Access forbidden!', 403);
			}
		}
	}

	private function valid_cookie($nip)
	{
		$this->ci->load->helper('cookie');
		$cookie = get_cookie($this->name_cookie);
		if(empty($cookie)) {
			return $this->res('Token not found', 404);
		} else {
			$decode = $this->decode($cookie);
			if($decode['code'] != 200) {
				// 403
				return $decode;
			} else {
				$now = strtotime('now');
				$rem = $decode['body']['data']->exp-$now;
				if($rem <= 0) {
					return $this->res('Token expired!', 401);
				} else {
					$platform = $decode['body']['data']->sub;
					$check = $this->check_db($platform, $nip);
					if($check['code'] != 200) {
						// 404 | 500
						return $check;
					} else {
						$body['token'] = $cookie;
						return $this->res($body, 200);
					}
				}
			}
		}
	}

	private function valid_header($nip)
	{
		$head = $this->ci->input->request_headers();
		if(!empty($head) && is_array($head)) {
			$config_head = ucfirst(trim($this->header));
			if(empty($head[$config_head])) {
				return $this->res('Access forbidden!', 403);
			} else {
				$bearer = $head[$config_head];
				$token = explode(" ", $bearer)[1];
				$decode = $this->decode($token);
				if($decode['code'] != 200) {
					// 403
					return $decode;
				} else {
					$now = strtotime('now');
					$rem = $decode['body']['data']->exp-$now;
					if($rem <= 0) {
						return $this->res('Token expired!', 401);
					} else {
						$platform = $decode['body']['data']->sub;
						$check = $this->check_db($platform, $nip);
						if($check['code'] != 200) {
							return $check;
						} else {
							$body['token'] = $token;
							return $this->res($body, 200);
						}
					}
				}
			}
		} else {
			return $this->res('Token undefined!', 400);
		}
	}

	private function cookie_is_exist($nip)
	{
		$this->ci->load->helper('cookie');
		$cookie = get_cookie($this->name_cookie);
		if(empty($cookie)) {
			$header = $this->header_is_exist($nip);
			if($header['code'] != 200) {
				return $header;
			} else {

			}
		} else {
			return $this->res('ada');
		}
	}

	private function header_is_exist($nip)
	{
		$head = $this->ci->input->request_headers();
		if(!empty($head) && is_array($head)) {
			$config_head = ucfirst(trim($this->header));
			if(empty($head[$config_head])) {
				return $this->res('Access forbidden!', 403);
			} else {
				$bearer = $head[$config_head];
				$token = explode(" ", $bearer)[1];
				$decode = $this->decode($token);
				if($decode['code'] != 200) {
					return $decode;
				} else {
					$user_agent = $this->get_device();
					$sub = $decode['body']['data']->sub;
					if($user_agent !== $sub) {
						return $this->res('Access forbidden!', 403);
					} else {
						$body = array('platform' => $sub,
							'token' => $token);
						return $this->res($body);
					}
				}
			}
		} else {
			return $this->res('Access forbidden!', 403);
		}
	}

	// PUBLIC
	public function generate($nip)
	{
		$user_agent = $this->get_device();
		$access = $this->set_access($user_agent, $nip);
		if($access['code'] != 200) {
			return $access;
		} else {
			$check = $this->check_db($user_agent, $nip);
			if($check['code'] != 200) {
				$this->insert_db($user_agent, $nip);
			} 

			$token = $access['body']['data']['token'];
			if($this->cookie) {
				$this->set_cookie($token);
			}
			return $access;
		}
	}

	public function valid($nip, $regenerate = false) 
	{
		if($this->cookie) {
			$cookie = $this->cookie_is_exist($nip);
			return $cookie;





		} else {
			$header = $this->header_is_exist($nip);
			if($header['code'] != 200) {
				return $header;
			} else {
				$platform = $header['body']['platform'];
				$check = $this->check_db($platform, $nip);
				if($check['code'] != 200) {
					return $check;
				} else {
					if($regenerate) {
						$generate = $this->generate($nip);
						return $generate;
					} else {
						$body['token'] = $header['body']['token'];
						return $this->res($body);
					}
				}
			}
		}
	}

	public function validationxxxxxxxxx($nip, $regenerate = false)
	{
		$cookie = $this->valid_cookie($nip);
		if($cookie['code'] != 200) {
			$header = $this->valid_header($nip);
			if($header['code'] != 200) {
				$platform = $this->get_device();
				$check = $this->check_db($platform, $nip);
				if($check['code'] != 200) {
					return $this->res('re login', 401);
				} else {
					if($regenerate) {
						$generate = $this->generate($nip);
						return $generate;
					} else {

						if(!empty($cookie['body']['token'])) {
							$body['token'] = $cookie['body']['token'];
						} else {
							$body = $header['body'];
						}

						return $cookie;
						// return $this->res($body, 400);

					}
				}
			} else {
				return $header;
			}
		} else {
			return $cookie;
		}
	}

	public function destroy($nip)
	{
		$valid_token = $this->validation($nip);
		if($valid_token['code'] != 200) {
			return $valid_token;
		} else {
			$platform = $this->get_device();
			$destroy = $this->delete_db($platform, $nip);
			if($destroy['code'] != 200) {
				return $destroy;
			} else {
				return $this->res('Token has been destroy', 200);
			}
		}
	}

	public function clear()
	{
		$this->ci->load->helper('cookie');
		delete_cookie('nip');
		delete_cookie('token');
		return $this->res('Cleared');
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

	private function db_error($err)
	{
		$res['code'] = 500;
		$res['body'] = array('status' => false,
			'message' => $err['message']);
		return $res;
	}
}
?>
