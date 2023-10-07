<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends MX_Controller
{
	function __construct()
  {
    parent::__construct();
    $this->load->library('migration');
		$this->load->helper('tree_helper');
  }

	public function index()
	{
		$this->load->database();
		$db_name = $this->db->database;
		$view = array('dbName' => $db_name);
		$this->load->view('migrate', $view);
	}

	public function process()
	{
		$count = $this->count_table();
		$migrate = $this->migration($count);
		$view = array('response' => $migrate);
		$this->load->view('response', $view);
	}

	private function count_table()
	{
		$path = APPPATH."migrations/";
		$glob = glob($path."*_*.php");
		$table = array();
		foreach($glob as $key => $val) {
			$string = str_replace($path, "", $val);
			$pattern = "/\d+/";
			preg_match($pattern, $string, $matches);
			if($matches) {
				$numb = str_replace("0", "",$matches[0]);
				$name = str_replace(".php", "", $string);
				$table[] = array(
					'numb' => $numb,
					'name' => $name);
			}
		}
		return $table;
	}

	private function migration($v)
	{
		$migrate = array();
		foreach($v as $key => $val) {
			$int = (int) $val['numb'];
			if(!$this->migration->version($int)) {
				$error = show_error($this->migration->error_string());
				$migrate[] = array(
					'status' => false,
					'message' => 'Migration '.$val['name'].' ERROR.<br>'.$error);
			} else {
				$migrate[] = array(
					'status' => true,
					'message' => 'Migration '.$val['name'].' SUCCESS.');
			}
		}
		return $migrate;
	}
}
