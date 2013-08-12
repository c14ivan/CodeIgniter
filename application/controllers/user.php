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
    /*function view(){
        if (!$this->tank_auth->is_logged_in()) {
            redirect('');
        }
        $headers= array(
                'username'=>array('priority'=>1,'abbr'=>'username','valor'=>'Nombre de Usuario','celllabel'=>'Username'),
                'email'=>array('priority'=>2,'abbr'=>'e-mail','valor'=>'Correo electronico','celllabel'=>'e-mail'),
                'created'=>array('priority'=>3,'abbr'=>'Creación','valor'=>'Fecha de Creación','celllabel'=>'Creado en'),
        );
        $datos= $this->db->get('users');
         
        $data=array(
                'headers'=>$headers,
                'data'=>$datos->result(),
                'hasactions'=>true,
                'canedit'=>true,
                'candel'=>true,
                'urledit'=>site_url("auth/edituser/"),
                'urldel'=>site_url("auth/deluser/"),
                'delajax'=>false
        );
         
        $this->twig->display('general/table',$data);
    }*/
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
        
    }
    function unbanuser(){
        
    }
    function banuser(){
        
    }
}