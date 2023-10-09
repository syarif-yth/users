<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_users extends CI_migration
{
	private $tb_name;
	private $tb_engine;
	private $tb_field;

	function __construct()
	{
		parent::__construct();
		$this->tb_name = 'users';
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
				'null' => false,
				'unique' => true),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
				'unique' => true),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false,
				'unique' => true),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
				'null' => false),
			'nama' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'default' => 'New User')
		);
		return $field;
	}

	private function set_users($nip)
	{
		$pass = encrypt_pass($nip, 'Adm1n@pp');
		$data[] = array(
			'nip' => $nip,
			'email' => 'syarif.yth@gmail.com',
			'username' => 'admin_app',
			'password' => $pass,
			'nama' => 'Admin APP');
		return $data;
	}

	// private function set_attr($nip)
	// {
	// 	$data[] = array(
	// 		'nip' => $nip,
	// 		'email' => 'syarif.yth@gmail.com');
	// 	return $data;
	// }

	public function up()
	{
		$exis = $this->db->table_exists($this->tb_name);
		if(!$exis) {
			$this->dbforge->add_field($this->tb_field);
			$this->dbforge->add_key($this->tb_key, TRUE);
			$this->dbforge->create_table($this->tb_name, FALSE, $this->tb_engine);

			$this->load->helper('input');
			$nip = create_nip();
			$users = $this->set_users($nip);
			// $attr = $this->set_attr($nip);

			$this->load->database();
			$this->db->insert_batch($this->tb_name, $users);
			// $this->db->insert_batch('attr_users', $attr);
		}
	}

	public function down()
	{
		$this->dbforge->drop_table($this->tb_name);
	}

}
?>
