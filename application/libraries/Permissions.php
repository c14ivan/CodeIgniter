<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *    Permission Class
 *    COPYRIGHT (C) 2008-2009 Haloweb Ltd
 *    http://www.haloweb.co.uk/blog/
 *
 *    Version:    0.9.1
 *    Wiki:       http://codeigniter.com/wiki/Permission_Class/
 *
 *    Description:
 *    The Permission class uses keys in a session to allow or disallow functions
 *    or areas of a site. The keys are stored in a database and this class adds
 *    and/or takes them away. The use of IF statements are required within
 *    controllers and views, please see wiki for code.
 *
 *    Permission is hereby granted, free of charge, to any person obtaining a copy
 *    of this software and associated documentation files (the "Software"), to deal
 *    in the Software without restriction, including without limitation the rights
 *    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *    copies of the Software, and to permit persons to whom the Software is
 *    furnished to do so, subject to the following conditions:
 *
 *    The above copyright notice and this permission notice shall be included in
 *    all copies or substantial portions of the Software.
 *
 *    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *    THE SOFTWARE.
**/

class Permissions {

    // init vars
    var $ci;                        // CI instance
    var $where = array();
    var $set = array();
    var $required = array();

    function __construct()
    {
        // init vars
        $this->ci =& get_instance();

        $this->ci->load->config('permissions', TRUE);

        $this->ci->load->library('session');
        //$this->ci->load->database();

        $this->ci->load->model('permissions/Perms');

    }

    /**
     * Create a context if doesn't exist. otherwise return the id of the previous created
     * @param unknown $contextlevel
     * @param unknown $instanceid
     * @return unknown
     */
    function create_context($contextlevel,$instanceid){

        $ctx_levels=$this->ci->config->item('context_levels', 'permissions');

        if(!$prevcontext=$this->perms->get_context($ctx_levels[$contextlevel],$instanceid)){
            $prevcontext=$this->perms->create_context($ctx_levels[$contextlevel],$instanceid);
        }
        return $prevcontext;
    }
    
    /**
     * Create a role if doesn't exist another with the same name or shortname
     * @param string $name
     * @param int $weigth max 99
     * @param string $shortname
     * @param string $description
     * @return unknown
     */
    function create_role($name,$weigth,$shortname,$description=''){
        if(!$role=$this->perms->valid_role($name,$shortname)){
            $role=$this->perms->create_role();
        }
        return $role;
    }













    function get_context($instanceid,$contextlevel){
        $this->ci->db->select('id');
        $this->ci->db->where('instanceid',$instanceid);
        $this->ci->db->where('contextlevel',$contextlevel);

        // set permissions array and return
        $query = $this->ci->db->get('context');
        $result = $query->result_array();

        if ($query->num_rows())
        {
            foreach ($query->result_array() as $row)
            {
                $permissions = $row['id'];
            }

            return $permissions;
        }
        else
        {
            return false;
        }
    }
    function get_context_by_id($id){
        $this->ci->db->select('*');
        $this->ci->db->where('id',$id);

        // set permissions array and return
        $query = $this->CI->db->get('permissions');
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
    function get_user_role($userid,$contextid){
        $this->ci->db->select('roleid');
        $this->ci->db->where('userid',$userid);
        $this->ci->db->where('contextid',$contextid);

        // set permissions array and return
        $query = $this->ci->db->get('role_assignments');
        $result = $query->result_array();

        if ($query->num_rows())
        {
            foreach ($query->result_array() as $row)
            {
                $permissions = $row['roleid'];
            }

            return $permissions;
        }
        else
        {
            return false;
        }
    }
    /**
     * get the menus for a user in determinated context and for some position
     * @param unknown $userid
     * @param unknown $contextid
     * @param unknown $position the menu position.
     */
    function get_menu_user($userid,$contextid,$position){

    }
    // get permissions from for this group
    function get_user_permissions($roleid,$contextid=0)
    {
        // grab keys
        $this->ci->db->select('url');
        $this->ci->db->where('url !=','');
        $this->ci->db->where('permission',1);
        $this->ci->db->where('roleid',$roleid);
        $this->ci->db->where('contextid',$contextid);

        // set permissions array and return
        $query = $this->ci->db->get('role_capabilities');
        $result = $query->result_array();

        if ($query->num_rows())
        {
            foreach ($query->result_array() as $row)
            {
                $permissions[] = $row['url'];
            }

            return $permissions;
        }
        else
        {
            return false;
        }
    }
    /*
     // get all permissions, or permissions from a group for the purposes of listing them in a form
    function get_permissions($groupID = '')
    {
    // select
    $this->CI->db->select('DISTINCT(category)');

    // if groupID is set get on that groupID
    if ($groupID)
    {
    $this->CI->db->where_in('key', $this->get_user_permissions($groupID));
    }

    // order
    $this->CI->db->order_by('category');

    // return
    $query = $this->CI->db->get('permissions');

    if ($query->num_rows())
    {
    $result = $query->result_array();

    foreach($result as $row)
    {
    if ($cat_perms = $this->get_perms_from_cat($row['category']))
    {
    $permissions[$row['category']] = $cat_perms;
    }
    else
    {
    $permissions[$row['category']] = 'N/A';
    }
    }
    return $permissions;
    }
    else
    {
    return false;
    }
    }

    // get permissions from a category name, for the purposes of showing permissions inside a category
    function get_perms_from_cat($category = '')
    {
    // where
    if ($category)
    {
    $this->CI->db->where('category', $category);
    }

    // return
    $query = $this->CI->db->get('permissions');

    if ($query->num_rows())
    {
    return $query->result_array();
    }
    else
    {
    return false;
    }
    }

    // get the map of keys from a group ID
    function get_permission_map($groupID)
    {
    // grab keys
    $this->CI->db->select('permissionID');

    // where
    $this->CI->db->where('groupID', $groupID);

    // return
    $query = $this->CI->db->get('permission_map');

    if ($query->num_rows())
    {
    return $query->result_array();
    }
    else
    {
    return false;
    }
    }

    // get the groups, for the purposes of displaying them in a form
    function get_groups()
    {
    // where
    $this->CI->db->where('siteID', $this->siteID);

    // return
    $query = $this->CI->db->get('permission_groups');

    if ($query->num_rows())
    {
    return $query->result_array();
    }
    else
    {
    return false;
    }
    }

    // add permissions to a group, each permission must have an input name of "perm1", or "perm2" etc
    function add_permissions($groupID)
    {
    // delete all permissions on this groupID first
    $this->CI->db->where('groupID', $groupID);
    $this->CI->db->delete('permission_map');

    // get post
    $post = $this->CI->easysite->get_post();
    foreach ($post as $key => $value)
    {
    if (preg_match('/^perm([0-9]+)/i', $key, $matches))
    {
    $this->CI->db->set('groupID', $groupID);
    $this->CI->db->set('permissionID', $matches[1]);
    $this->CI->db->insert('permission_map');
    }
    }

    return true;
    }

    // a group to the permission groups table
    function add_group($groupName = '')
    {
    if ($groupName)
    {
    $this->CI->db->set('groupName', $groupName);
    $this->CI->db->insert('permission_groups');

    return $this->CI->db->insert_id();
    }
    else
    {
    return false;
    }
    }*/

}