<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_attr_users extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'attr_users';
		$this->tb_key = 'nip';
		$this->tb_engine = array('ENGINE' => 'InnoDB');
		$this->tb_field = $this->set_field();
	}

	private function set_field()
	{
		$field = array(
			'nip' => array(
				'type' => 'VARCHAR',
				'constraint' => 15,
				'null' => false),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
				'unique' => true),
			'online' => array(
				'type' => 'ENUM("0","1")',
				'null' => true,
				'default' => '0'),
			'token' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'default' => NULL),
			'kode_aktifasi' => array(
				'type' => 'VARCHAR',
				'constraint' => 6,
				'null' => true,
				'default' => NULL),
			'exp_aktifasi' => array(
				'type' => 'INT',
				'null' => true,
				'default' => NULL),
			'kode_recovery' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => true,
				'default' => NULL),
			'exp_recovery' => array(
				'type' => 'INT',
				'null' => true,
				'default' => NULL),
			'non_aktif' => array(
				'type' => 'ENUM("0","1")',
				'null' => true,
				'default' => '0'),
			'tgl_regist datetime default CURRENT_TIMESTAMP',
			'tgl_modif datetime default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'
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
