<?php
class Permision extends Controller {

    // set defaults
    var $permissions = array();

    function Example()
    {
        parent::Controller();

        //  load libs
        $this->load->library('permission');

        // set groupID
        $userid = ($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : 0;

        // get permissions and show error if they don't have any permissions at all
        if (!$this->permissions = $this->permission->get_user_permissions($userid))
        {
            show_error('You do not have any permissions!');
        }
    }

    function index()
    {
        // show error if they dont have access to this page
        if (!in_array('access_to_index', $this->permissions))
        {
            show_error('You do not have access to this page!');
        }

        // they got in...
        echo 'hello!';
    }

    //TODO implement this functions
    /**
     * edit role permissions
     * @param number $roleid
     */
    function role_edit($roleid=0)
    {

    }
    /**
     * Create a role
     */
    function role_create(){
        
    }
    /**
     * Instalation, create the defaults and insert into database
     */
    function install(){
        
    }


}