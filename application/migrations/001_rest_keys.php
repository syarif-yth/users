<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_rest_keys extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'rest_keys';
		$this->tb_key = 'id';
		$this->tb_engine = array('ENGINE' => 'InnoDB');
		$this->tb_field = $this->set_field();
	}

	private function set_field()
	{
		return $field = array(
			'id' => $this->id(),
			'user_id' => $this->user_id(),
			'key' => $this->key(),
			'level' => $this->level(),
			'ignore_limits' => $this->ignore_limits(),
			'is_private_key' => $this->is_private_key(),
			'ip_addresses' => $this->ip_addresses(),
			'date_created datetime default CURRENT_TIMESTAMP');
	}

	private function id()
	{
		return $attr = array(
			'type' => 'INT',
			'constraint' => 11,
			'null' => false,
			'auto_increment' => TRUE);
	}

	private function user_id()
	{
		return $attr = array(
			'type' => 'INT',
			'constraint' => 11,
			'null' => false);
	}

	private function key()
	{
		return $attr = array(
			'type' => 'VARCHAR',
			'constraint' => 40,
			'null' => false);
	}

	private function level()
	{
		return $attr = array(
			'type' => 'INT',
			'constraint' => 2,
			'null' => false);
	}

	private function ignore_limits()
	{
		return $attr = array(
			'type' => 'TINYINT',
			'constraint' => 1,
			'null' => false,
			'default' => 0);
	}

	private function is_private_key()
	{
		return $attr = array(
			'type' => 'TINYINT',
			'constraint' => 1,
			'null' => false,
			'default' => 0);
	}

	private function ip_addresses()
	{
		return $attr = array(
			'type' => 'TEXT',
			'null' => true,
			'default' => NULL);
	}

	public function up()
	{

		$exis = $this->db->table_exists($this->tb_name);
		if(!$exis) {
			$this->dbforge->add_field($this->tb_field);
			$this->dbforge->add_key($this->tb_key, TRUE);
			$this->dbforge->create_table($this->tb_name, FALSE, $this->tb_engine);

			$value = $this->set_value();
			$this->load->database();
			$this->db->insert_batch($this->tb_name, $value);
		}
	}

	public function down()
	{
		$this->dbforge->drop_table($this->tb_name);
	}

	private function set_value()
	{
		$data[] = array(
			'id' => NULL,
			'user_id' => '1',
			'key' => 'code3',
			'level' => '',
			'ignore_limits' => '',
			'is_private_key' => '1',
			'ip_addresses' => '::1');
		return $data;
	}
}
?>
