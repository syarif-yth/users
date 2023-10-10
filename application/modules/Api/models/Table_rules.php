<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Table_rules extends CI_Model
{
	private $db_table;
	private $key;
	function __construct()
	{
		parent::__construct();
		$this->db_table = $this->db->protect_identifiers('rules', TRUE);
		$this->key = 'id';
		$this->load->helper('starterkit');
	}

	public function select_all($column = null)
	{
		if($column) $this->db->select($column);
		$kueri = $this->db->get($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($kueri->num_rows() == 0) {
				$res = for_response('Data not found!', 404);
			} else {
				$body['data'] = $kueri->result_array();
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function select_key($key, $column = null)
	{
		if($column) $this->db->select($column);
		$this->db->where($this->key, $key);
		$kueri = $this->db->get($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($kueri->num_rows() == 1) {
				$body['data'] = $kueri->result_array()[0];
				$res = for_response($body);
			} else {
				$res = for_response('Data duplicated', 400);
			}
			return $res;
		}
	}

	public function select_by($where = null, $column = null)
	{
		if($column) $this->db->select($column);
		if($where) $this->db->where($where);
		$kueri = $this->db->get($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($kueri->num_rows() == 0) {
				$res = for_response('Data not found!', 404);
			} else {
				$body['data'] = $kueri->result_array();
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function select_like($like = null, $column = null)
	{
		if($column) $this->db->select($column);
		if($like) $this->db->like($like);
		$kueri = $this->db->get($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($kueri->num_rows() == 0) {
				$res = for_response('Data not found!', 404);
			} else {
				$body['data'] = $kueri->result_array();
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function insert($data)
	{
		$kueri = $this->db->insert($this->db_table, $data);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been inserted', 400);
			} else {
				$body['message'] = 'New data has been inserted';
				$body['data'] = $data;
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function insert_batch($data)
	{
		$kueri = $this->db->insert_batch($this->db_table, $data);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been inserted', 400);
			} else {
				$body['message'] = 'New data has been inserted';
				$body['data'] = $data;
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function update($data, $key)
	{
		$this->db->where($this->key, $key);
		$kueri = $this->db->update($this->db_table, $data);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been updated', 400);
			} else {
				$body['message'] = 'Data has been updated';
				$body['data'] = $data;
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function update_where($data, $where)
	{
		$this->db->where($where);
		$kueri = $this->db->update($this->db_table, $data);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been updated', 400);
			} else {
				$body['message'] = 'Data has been updated';
				$body['data'] = $data;
				$res = for_response($body);
			}
			return $res;
		}
	}

	public function delete($key)
	{
		$this->db->where($this->key, $key);
		$kueri = $this->db->delete($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been deleted', 400);
			} else {
				$res = for_response('Data has been deleted');
			}
			return $res;
		}
	}

	public function delete_where($where)
	{
		$this->db->where($where);
		$kueri = $this->db->delete($this->db_table);
		if(!$kueri) {
			$err = $this->db->error();
			return db_error($err);
		} else {
			if($this->db->affected_rows() == 0) {
				$res = for_response('No data has been deleted', 400);
			} else {
				$res = for_response('Data has been deleted');
			}
			return $res;
		}
	}
}
