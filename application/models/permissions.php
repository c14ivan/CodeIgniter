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
class Permissions extends CI_Model
{
    private $table_roles         = 'role';            // roles
    private $table_enrolments    = 'role_assignments';   // role asignments
    private $table_rolecaps      = 'role_capabilities';// role capabilities
    private $table_caps          = 'capabilities';     // capabilities
    private $table_ctx           = 'context';          // contexts

    function __construct()
    {
        parent::__construct();

        $ci =& get_instance();
        $this->table_roles        = $ci->config->item('db_table_prefix', 'permission').$this->table_roles;
        $this->table_enrolments   = $ci->config->item('db_table_prefix', 'permission').$this->table_enrolments;
        $this->table_rolecaps     = $ci->config->item('db_table_prefix', 'permission').$this->table_rolecaps;
        $this->table_caps         = $ci->config->item('db_table_prefix', 'permission').$this->table_caps;
        $this->table_ctx          = $ci->config->item('db_table_prefix', 'permission').$this->table_ctx;
    }
    function has_permission($url,$user=0,$context=0){
        $user_id = ($user>0)?$user:$this->session->userdata('user_id');
        $context = ($context>0)?$context:$this->session->userdata('context');
        
        return true;
    }
    /**
     * Create a context
     * @param int $contextlevel its valor doesn't refers to relevance, is just for diferentiate max 99
     * @param int $instanceid id of the instance to be created
     * @return unknown|boolean id of the created context or false if couldn't create
     */
    function create_context($contextlevel,$instanceid,$name){
        if(!$ctx=$this->get_context($instanceid, $contextlevel)){
            $data=array('contextlevel'=>$contextlevel,'instanceid'=>$instanceid,'name'=>$name);
            if ($this->db->insert($this->table_ctx, $data)) {
                $ctx = $this->db->insert_id();
            }
        }
        return $ctx;
    }

    function get_home_context(){
        $this->db->select('id');
        $this->db->where('contextlevel',CONTEXT_HOME);
    
        // set permissions array and return
        $query = $this->db->get($this->table_ctx);
    
        if ($query->num_rows())
        {
            $result = $query->row();
            return $result->id;
        }
        else
        {
            return false;
        }
    }
    function get_context($instanceid,$contextlevel){
        $this->db->select('id');
        $this->db->where('instanceid',$instanceid);
        $this->db->where('contextlevel',$contextlevel);

        // set permissions array and return
        $query = $this->db->get($this->table_ctx);

        if ($query->num_rows())
        {
            $result = $query->row();
            return $result->id;
        }
        else
        {
            return false;
        }
    }
    function get_context_by_id($id){
        $this->ci->db->select('*');
        $this->ci->db->where('id',$id);
    
        $query = $this->db->get($this->table_ctx);
        $result = $query->result_array();
    
        if ($query->num_rows())
        {
            foreach ($query->result_array() as $row)
            {
                $permissions = $row;
            }
    
            return $permissions;
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
     * @param int $weight  weight of the role, max 99
     * @param string $shortname shortname of the role
     * @param string $description optional description of the role
     * @return unknown|boolean the roleid if could create, otherwise returns false
     */
    function create_role($name,$weight,$shortname,$description='')
    {
        $role_id=false;
        if($this->valid_role($name, $shortname)){
            $data['name'] = $name;
            $data['weight'] = $weight;
            $data['shortname'] = $shortname;
            $data['description'] = $description;
    
            if ($this->db->insert($this->table_roles, $data)) {
                $role_id = $this->db->insert_id();
                $this->init_role($role_id);
            }
        }
        return $role_id;
    }
    
    function update_role($name,$weight,$shortname,$description=''){
        $data['weight'] = $weight;
        $data['description'] = $description;
        $this->db->where('shortname', $shortname);
        $this->db->update($this->table_roles, $data);
        
        $capmode=$this->config->item('permissions_mode','permission');
        
        $this->db->where('shortname', $shortname);
        $query=$this->db->get($this->table_roles);
        $rol=$query->row();
        if($rol){
            foreach ($this->get_capabilities() as $cap){
                $permission=0;
                if(($capmode=='weight' && $rol->weight > $cap['weight']) || ($capmode=='role' && strpos($cap['roles'], $rol->shortname)>=0))
                    $permission=1;
                $this->set_role_permission($rol->id,$cap['id'],$permission,$cap['position']);
            }
            return true;
        }
        return false;
    }
    function init_role($roleid){
        $capmode=$this->config->item('permissions_mode','permission');
        $role= $this->get_role_by_id($roleid);
        $capabilities = $this->get_capabilities();
        foreach ($capabilities as $capability){
            $permission=0;
            if(($capmode=='weight' && $role->weight > $capability['weight']) || ($capmode=='role' && strpos($capability['roles'], $role->shortname)>=0))
                $permission=1;
            $this->set_role_permission($roleid,$capability['id'],$permission,$capability['position']);
        }
    }
    /**
     * Get a Role with a name
     *
     * @param $rolename
     * @return NULL
     */
    function get_role($rolename,$create=false){
        $this->db->where('LOWER(name)=', strtolower($rolename));
         
        $query = $this->db->get($this->table_roles);
        if ($query->num_rows() == 1){
            return $query->row();
        }elseif($create){
            if($this->create_role(array('name'=>$rolename,'shortname'=>$rolename,'description'=>''))!=NULL)
                return $this->get_role($rolename);
        }
         
        return NULL;
    }
    function get_rolePermissions($role){
        $this->db->select("{$this->table_caps}.id,capability,permission");
        $this->db->from($this->table_rolecaps);
        $this->db->join($this->table_caps, "{$this->table_caps}.id = {$this->table_rolecaps}.capabilityid", 'inner');
        $this->db->where('roleid=',$role);
        
        $query=$this->db->get();
        return $query->result_array();
    }
    function get_roles($alldata=false){
         
        $query = $this->db->get($this->table_roles);
        $response=Array();
        if ($query->num_rows() > 0){
            if($alldata){
                $response=$query->result_array();
            }else{
                foreach ($query->result_array() as $role){
                    $response[$role['id']]=$role['name'];
                }
            }
        }
         
        return $response;
    }
    /**
     * Get a Role with an id
     *
     * @param $rolename
     * @return NULL
     */
    function get_role_by_id($roleid){
        $this->db->where('id=', strtolower($roleid));
         
        $query = $this->db->get($this->table_roles);
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
        
        $query=$this->db->get($this->table_rolecaps);
        if ($query->num_rows() > 0){//update permission
            $roleperm= $query->row_array();
            
            $roleupdate = array(
                    'permission'=> ($permission)?1:0
                    );
            if ($position!='') $roleupdate['position']='position';
            $this->db->where('id', $roleperm['id']);
            $this->db->update($this->table_rolecaps, $roleupdate);
        }else{//insert permission
            $roleperm=array(
                    'roleid'=>$roleid,
                    'capabilityid'=>$capabilityid,
                    'position'=>$position,
                    'permission'=>($permission)?1:0,
                    );
            $this->db->insert($this->table_rolecaps, $roleperm);
        }
    }
    
    function set_capability($capability,$weight,$context_level,$position,$visible,$roles='',$parent='',$icon=''){
        $this->db->where('capability=',$capability);
        $this->db->where('contextlevel=',$context_level);
        $query=$this->db->get($this->table_caps);
        if ($query->num_rows() > 0){//update capability
            //TODO it never changues it's supossed, only for development
            $cap= $query->row_array();
            
            $capupdate = array(
                    'weight'=>$weight,
                    'contextlevel'=> $context_level,
                    'position'=>$position,
                    'visible'=> $visible,
            );
            
            $this->db->where('id', $cap['id']);
            $this->db->update($this->table_caps, $capupdate);
        }else{//insert capability
            $cap=array(
                    'capability'=>$capability,
                    'weight'=>$weight,
                    'contextlevel'=>$context_level,
                    'position'=>$position,
                    'visible'=>$visible,
                    'roles' => $roles,
                    'parent' => $parent,
                    'icon' => $icon,
            );
            $this->db->insert($this->table_caps, $cap);
            return true;
        }
        return false;
    }
    /**
     * get the user enrolment into a context
     * @param $userid
     * @param $contextid
     * @return boolean
     */
    function get_user_enrolment($userid,$contextid,$timestart='',$timeend=''){
        if($userid!=''){
            $this->db->where('userid=',$userid);
        	$this->db->or_where('userid=',0);
        }else{
        	$this->db->where('userid=',0);
        }
        $this->db->where('contextid',$contextid);
        
        if($timestart!=''){
            $this->db->where('timestart<',$timestart);
        }
        if($timeend!=''){
            $this->db->where('timeend>',$timeend);
        }else{
        	$this->db->or_where('timeend=','NULL');
        }
        
        $this->db->order_by("userid", "desc");
        $this->db->limit(1);
        $query = $this->db->get($this->table_enrolments);
        
        if($query->num_rows()>0){
            return $query->row_array();
        }else{
            return false;
        }
    }
    /**
     * enrol or update the enrolment of a user into a context
     * @param int $userid the userid it never updates
     * @param $contextid  the context id, it never updates
     * @param $roleid the role id
     * @param $timestart the timestart of the enrolment
     * @param $timeend the timestart of the enrolment
     */
    function enrol_user($userid,$contextid,$roleid,$timestart='',$timeend=''){
        $enrolment=$this->get_user_enrolment($userid, $contextid,$timestart,$timeend);
        
        if($enrolment){
            $enrolment['roleid']=$roleid;
            if($timestart!=''){
                $enrolment['timestart']=$timestart;
            }
            if($timeend=''){
                $enrolment['timeend']=$timeend;
            }
            $this->db->where('id', $enrolment['id']);
            $this->db->update($this->table_enrolments, $enrolment);
        }else{
            $enrolment=array(
                    'userid'=>$userid,
                    'contextid'=>$contextid,
                    'roleid'=>$roleid
                    );
            if($timestart!=''){
                $enrolment['timestart']=$timestart;
            }
            if($timeend=''){
                $enrolment['timeend']=$timeend;
            }
            $this->db->insert($this->table_enrolments, $enrolment);
        }
        return true;
    }
    
    function get_menu($role,$position){
        $this->db->select('capability,parent,icon');
        $this->db->from($this->table_rolecaps);
        $this->db->join($this->table_caps, "{$this->table_caps}.id = {$this->table_rolecaps}.capabilityid", 'inner');
        $this->db->where('roleid=',$role);
        $this->db->where($this->table_rolecaps.'.position=',$position);
        $this->db->where('permission=',1);
        $this->db->where('visible=',1);
        $this->db->distinct();
        
        $query=$this->db->get();
        
        $menu_response=array();
        foreach ($query->result_array() as $item){
            if(isset($item['parent']) && $item['parent']!=''){
                $menu_response[$item['parent']]['options'][$item['capability']]=array('url'=>$item['capability'],'label'=>$item['capability'],'icon'=>$item['icon']);
            }else{
                $menu_response[$item['capability']]=array('url'=>$item['capability'],'label'=>$item['capability']);
            }
        }
        return $menu_response;
    }
    
    function get_user_menu($userid,$contextid,$position)
    {
        $menu=false;
        $role = $this->get_user_enrolment($userid,$contextid);
        if($role){
            $menu = $this->get_menu($role['roleid'],$position);
        }
        return $menu;
    }
    
}

/* End of file perms.php */
/* Location: ./application/models/auth/users.php */