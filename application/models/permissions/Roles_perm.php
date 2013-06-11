<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Roles_perm extends CI_Model
{
	private $table_name			= 'roles';			// roles
	private $table_asignment	= 'role_asignment';	// role asignments

	function __construct()
	{
		parent::__construct();

		$ci =& get_instance();
		$this->table_name			= $ci->config->item('db_table_prefix', 'tank_auth').$this->table_name;
		$this->table_asignment	= $ci->config->item('db_table_prefix', 'tank_auth').$this->table_asignment;
	}

	/**
	 * Get a Role with a name
	 * 
	 * @param $rolename
	 * @return NULL
	 */
	function get_role_by_name($rolename,$create=false){
	    $this->db->where('LOWER(name)=', strtolower($rolename));
	    
	    $query = $this->db->get($this->table_name);
	    if ($query->num_rows() == 1){
	        return $query->row();
	    }elseif($create){
	        if($this->create_role(array('name'=>$rolename,'shortname'=>$rolename,'description'=>''))!=NULL)
	            return $this->get_role_by_name($rolename);
	    }
	    
	    return NULL;
	}
	
	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_role($data)
	{
		$data['created'] = date('Y-m-d H:i:s');
		$data['activated'] = $activated ? 1 : 0;

		if ($this->db->insert($this->table_name, $data)) {
			$role_id = $this->db->insert_id();
			return array('role_id' => $role_id);
		}
		return NULL;
	}

}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */