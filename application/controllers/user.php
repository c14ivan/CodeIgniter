<?php
class User extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('tank_auth');
        $this->lang->load('tank_auth');
    }
    function index(){
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
                'canadd'=>false,
                'candel'=>false,
                'urledit'=>site_url("auth/edituser/"),
                'urldel'=>site_url("auth/deluser/"),
                'delajax'=>false
        );
         
        $this->twig->display('general/table',$data);
        //TODO cambiar y enrolamientos de ciudades
    }
}