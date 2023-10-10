<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_rules extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;
	private $tb_key;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'rules';
		$this->tb_key = 'id';
		$this->tb_engine = array('ENGINE' => 'InnoDB');
		$this->tb_field = $this->set_field();
	}

	private function set_field()
	{
		$field = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
				'unique' => true),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
				'unique' => true),
			'label' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
				'unique' => true),
			'rules' => array(
				'type' => 'JSON',
				'null' => false),
			'date_modify datetime default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'
		);
		return $field;
	}

	private function set_value()
	{
		$rules = array('get' => ['users','product'],
			'post' => ['users'],
			'put' => ['product'],
			'delete' => ['users']);
		$data[] = array(
			'name' => 'admin',
			'label' => 'Administrator',
			'rules' => json_encode($rules));
		return $data;
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
}
?>
