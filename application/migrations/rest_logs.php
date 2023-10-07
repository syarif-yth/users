<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_rest_logs extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'rest_logs';
		$this->tb_key = 'id';
		$this->tb_engine = array('ENGINE' => 'InnoDB');
		$this->tb_field = $this->set_field();
	}

	private function set_field()
	{
		return $field = array(
			'id' => $this->id(),
			'uri' => $this->uri(),
			'method' => $this->method(),
			'params' => $this->params(),
			'api_key' => $this->api_key(),
			'ip_address' => $this->ip_address(),
			'time' => $this->time(),
			'rtime' => $this->rtime(),
			'authorized' => $this->authorized(),
			'response_code' => $this->response_code());
	}

	private function id()
	{
		return $attr = array(
			'type' => 'INT',
			'constraint' => 11,
			'null' => false,
			'auto_increment' => TRUE);
	}

	private function uri()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 255,
			'null' => false);
	}

	private function method()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 6,
			'null' => false);
	}

	private function params()
	{
		return $attr = array(
			'type' => 'TEXT',
			'null' => true);
	}

	private function api_key()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 40,
			'null' => false);
	}

	private function ip_address()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 45,
			'null' => false);
	}

	private function time()
	{
		return $attr = array(
			'type' => 'INT',
			'constraint' => 11,
			'null' => false);
	}

	private function rtime()
	{
		return $attr = array(
			'type' => 'FLOAT',
			'default' => NULL);
	}

	private function authorized()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 1,
			'null' => false);
	}

	private function response_code()
	{
		return $attr = array(
			'type' => 'SMALLINT',
			'constraint' => 3,
			'default' => 0);
	}

	public function up()
	{
		$exis = $this->db->table_exists($this->tb_name);
		if(!$exis) {
			$this->dbforge->add_field($this->tb_field);
			$this->dbforge->add_key($this->tb_key, TRUE);
			$this->dbforge->create_table($this->tb_name, FALSE, $this->tb_engine);
		}
	}

	public function down()
	{
		$this->dbforge->drop_table($this->tb_name);
	}

}
?>
