<?php
class User extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('permissions');
        $this->lang->load('tank_auth');
        $this->load->model('tank_auth/users');
        $this->load->helper('password');
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
    function adduser(){
        if(!$this->input->is_ajax_request()) redirect();
        $data=$this->input->post();
        $contextid=$this->Permissions->get_home_context();

        $error=false;
        if($data['userid']>0){
            $this->Permissions->enrol_user($data['userid'],$contextid,$data['homerol']);
        }else{
            $email_activation = $this->config->item('email_activation', 'tank_auth');
            $use_username = $this->config->item('use_username', 'tank_auth');
            $password=get_random_password();

            if (!is_null($data = $this->tank_auth->create_user( $use_username ? $data['name'] : '',$data['email'] ,$password,$email_activation))) {									// success
                $data['site_name'] = $this->config->item('appname');
                $this->Permissions->enrol_user($data['user_id'],$contextid,$data['homerol']);
                
                if ($email_activation) {// send "activate" email
                    $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;
                    $this->_send_email('activate', $data['email'], $data);
                    unset($data['password']); // Clear password (just for any case)
                } else {
                    if ($this->config->item('email_account_details', 'tank_auth')) {// send "welcome" email
                        $this->_send_email('welcome', $data['email'], $data);
                    }
                        unset($data['password']); // Clear password (just for any case)
                }
            } else {
                $errors = $this->tank_auth->get_error_message();
                foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
                $error=true;
            }
        }
        echo json_encode(array('ok'=>!$error,'errors'=>(isset($data['errors'])?$data['errors']:false)));
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
    function _send_email($type, $email, &$data)
    {
        $this->load->library('email');
        $this->email->from($this->config->item('email_info'), $this->config->item('appname'));
        $this->email->reply_to($this->config->item('email_info'), $this->config->item('appname'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('appname')));
        $this->email->message($this->twig->getDisplay('email/'.$type.'_html', $data));
        $this->email->set_alt_message($this->twig->getDisplay('email/'.$type.'_txt', $data));
        $this->email->send();
    }
}