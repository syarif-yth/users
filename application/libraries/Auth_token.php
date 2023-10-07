<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/third_party/JWT/JWT.php';
require APPPATH . '/third_party/JWT/ExpiredException.php';
require APPPATH . '/third_party/JWT/BeforeValidException.php';
require APPPATH . '/third_party/JWT/SignatureInvalidException.php';
require APPPATH . '/third_party/JWT/JWK.php';
require APPPATH . '/third_party/JWT/Key.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;
use \Firebase\JWT\Key;

class Auth_token
{
	protected $key;
	protected $algorithm;
	protected $header;
	protected $expire; 

	public function __construct()
	{
		$ci =& get_instance();
		$ci->load->config('jwt');

		$this->key = $ci->config->item('jwt_key');
		$this->algorithm = $ci->config->item('jwt_algorithm');
		$this->header = $ci->config->item('token_header');
		$this->expire = $ci->config->item('token_expire_time');
	}

	public function create_token($data = null)
	{
		if($data AND is_array($data)) {
			$data['API_TIME'] = time()+$this->expire;
			try {
				$token = JWT::encode($data, $this->key, $this->algorithm);
				$res['status'] = true;
				$res['data'] = array(
					'token' => $token,
					'expired' => date('H:i:s', $data['API_TIME']));
			} catch(Exception $err) {
				$res['status'] = false;
				$res['messasge'] = $err->getMessage();
			}
		} else {
			$res['status'] = false;
			$res['message'] = "Data Token Undefined!";
		}
		return $res;
	}

	public function valid_token()
	{
		$head = $ci->input->request_headers();
		$exist = $this->token_exist($head);

		if($exist['status'] === false) {
			$res = $exist;
		} else {
			try {				
				$head_token = $exist['token'];
				$decode = $this->decode($head_token);

				if(($decode['status'] === true) &&
					(!empty($decode)) && 
					(is_array($decode))) {

					$api_time = $decode['data']->API_TIME;
					if(empty($api_time || !is_numeric($api_time))) {
						$res['status'] = false;
						$res['message'] = 'Token Time undefined!';
					} else {
						$difference = strtotime('now') - $api_time;

						if($difference >= $this->expire) {
							$res['status'] = false;
							$res['message'] = 'Token Expired!';
						} else {
							$res = $decode;
						}
					}
				} else {
					$res['status'] = false;
					$res['message'] = 'Forbidden!';
				}
			} catch(Exception $err) {
				$res['status'] = false;
				$res['message'] = $err->getMessage();
			}
		}
		return $res;
	}

	private function decode($token)
	{
		try {
			$key = new Key($this->key, $this->algorithm);
			$jwt = explode(" ", $token)[1];
			$decoded = JWT::decode($jwt, $key);

			$res['status'] = true;
			$res['data'] = $decoded;
		} catch(Exception $err) {
			$res['status'] = false;
			$res['message'] = $err->getMessage();
		}
		return $res;
	}
	
	
	private function token_exist($head)
	{
		if(!empty($head) && is_array($head)) {
			$res = array();
			foreach($head as $key => $val) {
				$lower_key = strtolower(trim($key));
				$lower_head = strtolower(trim($this->header));
				if($lower_key === $lower_head) {
					$res['status'] = true;
					$res['token'] = $val;
				} else {
					$res['status'] = false;
					$res['message'] = 'Token undefined!';
				}
			}
			return $res;
		} else {
			$res['status'] = false;
			$res['message'] = 'Token undefined!';
			return $res;
		}
	}
}
?>
