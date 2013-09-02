<?php
class MY_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('tank_auth');
        $this->load->model('Permissions');
        
        $user_id = $this->session->userdata('user_id');
        $context = $this->session->userdata('context');
        
        $segment_one=$this->uri->segment(1);
        $segment_two=$this->uri->segment(2);
        
        if(empty($segment_one)){
            $segment_one='home';
        }
        if(empty($segment_two)) $segment_two= 'index';
        $url = $segment_one.'/'.$segment_two;

        if(empty($context)){
            $context = $this->Permissions->get_home_context();
            
            $this->session->set_userdata(array('context'=>$context));
        }
        
        if(!$context && $url!='permission/install' ){
            redirect('permission/install');
        }
        if($context && $url=='permission/install'){
            redirect('home');
        }

        if(empty($user_id) && $url!='auth/login' && $context) redirect('auth/login'); //&user_id=0;
        
        if($context && !$this->Permissions->has_permission($segment_one.'/'.$segment_two,$user_id,$context)){
            $this->session->set_flashdata('nopermission', lang('nopermission'));
            redirect('auth/login');
        }
        if(!$this->input->is_ajax_request())
        $this->output->enable_profiler(TRUE);
    }
}