<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scsystem extends CI_Model{
	private $table_name			= 'scsystem';		// user accounts
	private $profile_table_name	= 'user_profiles';	// user profiles
	
	function __construct()
	{
		parent::__construct();
	
		$ci =& get_instance();
		//$this->table_name			= $ci->config->item('db_table_prefix', 'school').$this->table_name;
		//$this->profile_table_name	= $ci->config->item('db_table_prefix', 'school').$this->profile_table_name;
	}
	function getsystems(){
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) return $query->result_array();
		return NULL;
	}
}