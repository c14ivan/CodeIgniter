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
class Perms extends CI_Model
{
    private $table_roles         = 'roles';            // roles
    private $table_enrolments    = 'role_asignment';   // role asignments
    private $table_rolecaps      = 'role_capabilities';// role capabilities
    private $table_caps          = 'capabilities';     // capabilities
    private $table_ctx           = 'context';          // contexts

    function __construct()
    {
        parent::__construct();

        $ci =& get_instance();
        $this->table_roles        = $ci->config->item('db_table_prefix', 'permissions').$this->table_roles;
        $this->table_enrolments   = $ci->config->item('db_table_prefix', 'permissions').$this->table_enrolments;
        $this->table_rolecaps     = $ci->config->item('db_table_prefix', 'permissions').$this->table_rolecaps;
        $this->table_caps         = $ci->config->item('db_table_prefix', 'permissions').$this->table_caps;
        $this->table_ctx          = $ci->config->item('db_table_prefix', 'permissions').$this->table_ctx;
    }
    /**
     * Create a context
     * @param int $contextlevel its valor doesn't refers to relevance, is just for diferentiate max 99
     * @param int $instanceid id of the instance to be created
     * @return unknown|boolean id of the created context or false if couldn't create
     */
    function create_context($contextlevel,$instanceid){
        $data=array('contextlevel'=>$contextlevel,'instanceid'=>$instanceid);
        if ($this->db->insert($this->table_ctx, $data)) {
            $role_id = $this->db->insert_id();
            return $role_id;
        }
        return false;
    }

    function get_context($instanceid,$contextlevel){
        $this->db->select('id');
        $this->db->where('instanceid',$instanceid);
        $this->db->where('contextlevel',$contextlevel);

        // set permissions array and return
        $query = $this->db->get($this->table_ctx);

        if ($query->num_rows())
        {
            $result = $query->result();
            return $result->id;
        }
        else
        {
            return false;
        }
    }

    /**
     * Verify if exist a role with the given name or shortname, if exists return false, otherwise return true
     * @param $name
     * @param $shortname
     * @return boolean
     */
    function valid_role($name,$shortname){
        $query = $this->db->query("SELECT * FROM {$this->table_roles} where name='{$name}' OR shortname='{$shortname}'");
        return ($query->num_rows() > 0)?false:true;
    }

    /**
     * Create a role
     * @param string $name name of the role
     * @param int $weigth  weigth of the role, max 99
     * @param string $shortname shortname of the role
     * @param string $description optional description of the role
     * @return unknown|boolean the roleid if could create, otherwise returns false
     */
    function create_role($name,$weigth,$shortname,$description='')
    {
        $data['name'] = $name;
        $data['weigth'] = $weigth;
        $data['shortname'] = $shortname;
        $data['description'] = $description;

        if ($this->db->insert($this->table_roles, $data)) {
            $role_id = $this->db->insert_id();
            return $role_id;
        }
        return false;
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
     * Get a Role with an id
     *
     * @param $rolename
     * @return NULL
     */
    function get_role_by_id($roleid){
        $this->db->where('id=', strtolower($roleid));
         
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1){
            return $query->row();
        }
        return NULL;
    }
    /**
     * get all the available capabilities
     */
    function get_capabilities(){
        $q = $this->db->get($this->table_caps);
        return $q->result_array();
    }
    /**
     * Update the capabilitie for a role and its positions
     * if it doesn't exists, the role assignment is added
     * @param int $roleid The roleid
     * @param int $capabilityid The capability id 
     * @param boolean $permission if is active or not
     * @param int $position Refers to menus, if it's just a capability it doesn't matter
     */
    function set_role_permission($roleid,$capabilityid,$permission,$position=''){
        $this->db->where('roleid=', $roleid);
        $this->db->where('capabilityid=', $capabilityid);
        
        $query=$this->db->get($this->table_enrolments);
        
        if ($query->num_rows() > 0){//update permission
            $roleperm= $query->row_array();
            
            $roleupdate = array(
                    'position'=>$position,
                    'permission'=> $permission
                    );
            
            $this->db->where('id', $roleperm['id']);
            $this->db->update($this->table_enrolments, $roleupdate);
        }else{//insert permission
            $roleperm=array(
                    'roleid'=>$roleid,
                    'capabilityid'=>$capabilityid,
                    'position'=>$position,
                    'permission'=>$permission,
                    );
            $this->db->insert($this->table_enrolments, $data);
        }
        
        
    }
    
    function create_capability($capability,$url,$weigth,$context_level,$visible){
        $cap=array(
                'capability'=>$capability,
                'url'=>$url,
                'weigth'=>$weigth,
                'context_level'=>$context_level,
                'visible'=>$visible
        );
        $this->db->insert($this->table_caps, $cap);
    }
}

/* End of file perms.php */
/* Location: ./application/models/auth/users.php */