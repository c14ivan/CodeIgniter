<?php
class User extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('permissions');
        $this->lang->load('tank_auth');
        $this->load->model('tank_auth/users');
    }
    function roles(){
        $data=array(
                'capabilities'=>$this->Permissions->get_capabilities(),
                'hasactions'=>true,
        );
        $this->twig->display('user/adminroles',$data);
    }
    
    function view(){
        $roles=$this->Permissions->get_roles();
        $this->twig->display('user/users',array('roles'=>$roles));
    }
    function enrolments(){
        $this->twig->display('user/enrolments',$data);
    }
    
    
    
    function getroles(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('roles'=>$this->Permissions->get_roles(true)));
    }
    function editrole(){
        if(!$this->input->is_ajax_request()) redirect();
        $postdata=$this->input->post();
        if($postdata['roleid']>0){
            $res=$this->Permissions->update_role($postdata['name'],0,$postdata['shortname'],$postdata['description']);
        }else{
            $res=$this->Permissions->create_role($postdata['name'],0,$postdata['shortname'],$postdata['description']);
        }
        echo json_encode(array('ok'=>$res));
    }
    function getrolecapabilities(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('caps'=>$this->Permissions->get_rolePermissions($this->input->post('roleid'))));
    }
    function editpermission(){
        if(!$this->input->is_ajax_request()) redirect();
        $postdata=$this->input->post();
        $this->Permissions->set_role_permission($postdata['role'],$postdata['perm'],$postdata['val']);
        echo json_encode(array('ok'=>true));
    }
    function getusers(){
        if(!$this->input->is_ajax_request()) redirect();
        $users=$this->users->get_users();
        echo json_encode(array('users'=>$users));
    }
    function deluser(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('ok'=>$this->users->delete_user($this->input->post('userid'))));
    }
    function banuser(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('ok'=>$this->users->ban_user($this->input->post('userid'),$this->input->post('banreason'))));
    }
    function unbanuser(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('ok'=>$this->users->unban_user($this->input->post('userid'))));
    }
}