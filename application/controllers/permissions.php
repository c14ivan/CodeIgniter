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

    function add_permissions()
    {
        // in view file, make sure you have a post with name "permX" and a value of 1, X would be the ID of the permission in the database

        // set groupID
        $groupID = 1;

        // example of how you would add permissions
        $this->permission->add_permissions($groupID);

    }


}