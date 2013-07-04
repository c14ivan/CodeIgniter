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
     * @param unknown $name
     * @param unknown $shortname
     * @return boolean
     */
    function valid_role($name,$shortname){
        $query = $this->db->query("SELECT * FROM {$this->table_roles} where name='{$name}' OR shortname='{$shortname}'");
        return ($query->num_rows() > 0)?false:true;
    }

    /**
     * Create a role
     * @param unknown $name name of the role
     * @param unknown $weigth  weigth of the role, max 99
     * @param unknown $shortname shortname of the role
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

    //TODO: implement this functions
    /**
    * Asign a capabilitie to a role
    * @param unknown $roleid
    * @param unknown $contextid
    * @param unknown $capabilitieid
    */
    function asign_role_capabilitie($roleid,$contextid,$capabilitieid){
         
    }
    /**
     * Unasign a capabilitie to a role
     * @param unknown $roleid
     * @param unknown $contextid
     * @param unknown $capabilitieid
     */
    function unasign_role_capabilitie($roleid,$contextid,$capabilitieid){

    }
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */