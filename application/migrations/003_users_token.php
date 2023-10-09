<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_users_token extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'users_token';
		$this->tb_key = 'id';
		$this->tb_engine = array('ENGINE' => 'InnoDB');
		$this->tb_field = $this->set_field();
	}

	private function set_field()
	{
		$field = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'null' => false,
				'auto_increment' => true),
			'nip' => array(
				'type' => 'VARCHAR',
				'constraint' => 15,
				'null' => false),
			'platform' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false),
			'date_created datetime default CURRENT_TIMESTAMP'
		);
		return $field;
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
