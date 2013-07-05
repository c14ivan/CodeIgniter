<?php
class Permision extends Controller {


    function __construct()
    {
        parent::__construct();

        //  load libs
        $this->load->library('permission');
    }

    function index()
    {
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
        //1. crear contexto home, debe tener context level 10 e instanceid=0
        $home_contextid=$this->permissions->create_context('CONTEXT_SYSTEM',0);
        

        //2. crear las capabilities en DB
        $capabilities=$this->config->item('default-capabilities');
        foreach ($capabilities as $capability => $cap){
            $vis=(isset($cap['visible']))?1:0;
            $pos=(isset($cap['position']))?$cap['position']:'';
            $this->permissions->set_capability($capability,$cap['weigth'],$cap['ctx_level'],$pos,$vis);
        }
        //2.1 crear las capabilities para guest
        $guestcapabilities=$this->config->item('guest-capabilities');
        foreach ($guestcapabilities as $capability => $cap){
            $vis=(isset($cap['visible']))?1:0;
            $pos=(isset($cap['position']))?$cap['position']:'';
            $this->permissions->set_capability($capability,0,CONTEXT_HOME,$pos,$vis);
        }
        
        //3. crear los roles por defecto
        //4. crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y
        //   que esten en el context_level 0, por defecto quiero crear capacidades para el home
        $roles=$this->config->item('default-roles', 'permissions');
        foreach($roles as $role){
            $role['id'] = $this->permissions->create_role($role['name'],$role['weigth'],$role['shortname'],$role['description']);
        }
        
        //5. crear un usuario y asignar el role superadministrador para el contexto home.
        //TODO aca voy y probar
    }


}