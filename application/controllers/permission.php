<?php
class Permission extends CI_Controller {


    function __construct()
    {
        parent::__construct();

        //  load libs
        $this->load->library('permissions');
        $this->load->library('tank_auth');
        $this->lang->load('tank_auth');
        $this->lang->load('menu');
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
     * TODO make something with this, it's only for update profiles quickly on development
     */
    function update(){
        //1. crear las capabilities en DB
        $capabilities=$this->config->item('default-capabilities','permission');
        foreach ($capabilities as $capability => $cap){
            $vis=(isset($cap['visible']))?1:0;
            $pos=(isset($cap['position']))?$cap['position']:'';
            $this->permissions->set_capability($capability,$cap['weight'],$cap['ctx_level'],$pos,$vis);
        }
        //1.1 crear las capabilities para guest
        $guestcapabilities=$this->config->item('guest-capabilities','permission');
        foreach ($guestcapabilities as $capability => $cap){
            $vis=(isset($cap['visible']))?1:0;
            $pos=(isset($cap['position']))?$cap['position']:'';
            $this->permissions->set_capability($capability,0,CONTEXT_HOME,$pos,$vis);
        }
        
        //2. crear los roles por defecto, crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y
        //   que esten en el context_level 0, por defecto quiero crear capacidades para el home
        $roles=$this->config->item('default-roles', 'permission');
        $adminrole=array();
        foreach($roles as $role){
            if(!isset($adminrole['weigth']) || $role['weigth']>$adminrole['weigth']){
                $adminrole=$role;
            }
            $role['id'] = $this->permissions->update_role($role['name'],$role['weight'],$role['shortname'],$role['description']);
        }
    }
    /**
     * Instalation, create the defaults and insert into database
     */
    function install(){
        
        $post=$this->input->post();
        if (!empty($post)) {

            //1. crear las capabilities en DB
            $capabilities=$this->config->item('default-capabilities','permission');
            foreach ($capabilities as $capability => $cap){
                $vis=(isset($cap['visible']))?1:0;
                $pos=(isset($cap['position']))?$cap['position']:'';
                $this->permissions->set_capability($capability,$cap['weight'],$cap['ctx_level'],$pos,$vis);
            }
            //1.1 crear las capabilities para guest
            $guestcapabilities=$this->config->item('guest-capabilities','permission');
            foreach ($guestcapabilities as $capability => $cap){
                $vis=(isset($cap['visible']))?1:0;
                $pos=(isset($cap['position']))?$cap['position']:'';
                $this->permissions->set_capability($capability,0,CONTEXT_HOME,$pos,$vis);
            }
            
            //2. crear los roles por defecto, crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y
            //   que esten en el context_level 0, por defecto quiero crear capacidades para el home
            $roles=$this->config->item('default-roles', 'permission');
            $adminrole=array();
            foreach($roles as $role){
                if(!isset($adminrole['weigth']) || $role['weigth']>$adminrole['weigth']){
                    $adminrole=$role;
                }
                $role['id'] = $this->permissions->create_role($role['name'],$role['weight'],$role['shortname'],$role['description']);
            }
            
            //3. crear contexto home, debe tener context level 10 e instanceid=0
            $home_contextid=$this->permissions->create_context(CONTEXT_SYSTEM,0);
            //4 crear role por defecto para todos en el home
            $role=$this->permissions->get_role($this->input->post('default_role'));
            //5 asign default role to everybody in the home
            $this->permissions->enrol_user(0,$home_contextid,$role->id);
            
            //6. crear un usuario y asignar el role superadministrador para el contexto home.
            $use_username = $this->config->item('use_username', 'tank_auth');
            if(!$use_username) $post['adminlogin']='';
            $data = $this->tank_auth->create_user($post['adminlogin'],$post['adminmail'],$post['adminpass'],false);
            
            $roleadmin=$this->permissions->get_role($adminrole['name']);
            $this->permissions->enrol_user($data['user_id'],$home_contextid,$roleadmin->id);
            redirect('');
        }else{
		    if(validation_errors()!='')
		        $data['errors']['validerrors']= validation_errors();
		}
		
		$roles=$this->config->item('default-roles', 'permission');
		$rolearray=array();
		foreach ($roles as $rol){
		    $rolearray[$rol['name']]=$rol['name'];
		}
		$data=array(
		        'icon'=>'wrench',
		        'title'=> lang('install'),
		        'noauto'=>true,
		        'campos'=>array(
		                'adminlogin'=>array('tipo'=>'input','type'=>'text','name'=>'adminlogin','label'=>$this->lang->line('auth_adminname'),'append'=>array('tipo'=>'icon','name'=>'user'),'placeholder'=>$this->lang->line('auth_adminname_holder')),
		                'adminmail'=>array('tipo'=>'input','type'=>'email','name'=>'adminmail','label'=>$this->lang->line('auth_logwithemail'),'append'=>'@','placeholder'=>$this->lang->line('auth_reg_email')),
		                'adminpass'=>array('tipo'=>'input','type'=>'password','name'=>'adminpass','label'=>$this->lang->line('auth_password'),'placeholder'=>$this->lang->line('auth_reg_password'),'class'=>'passmeter'),
		                'passverify'=>array('tipo'=>'input','type'=>'password','name'=>'passverify','label'=>$this->lang->line('auth_reg_confpassword'),'placeholder'=>$this->lang->line('auth_reg_password')),
		                'default_role'=>array('tipo'=>'select','name'=>'default_role','label'=>$this->lang->line('auth_defaultrole'),'opciones'=>$rolearray),
		                ),
		        'reglas'=>array(
		                'adminlogin'=>array(
		                        'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
                                'minlength'=>array('val'=>5,'msj'=>$this->lang->line('auth_tooshort')),
		                        'noSpace'=>array('val'=>'true','msj'=>$this->lang->line('auth_nospaces')),
		                        ),
		                'adminmail'=>array(
		                        'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
		                        'email'=>array('val'=>'true','msj'=>$this->lang->line('auth_email_formatwrong')),
		                        ),
		                'adminpass'=>array(
		                        'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
		                        'password'=>array('val'=>'#username','istring'=>true),
		                        'pass_too_short'=>array('msj'=>$this->lang->line('auth_required')),
		                        'pass_similar_to_username'=>array('msj'=>$this->lang->line('auth_required')),
		                        'pass_very_weak'=>array('msj'=>$this->lang->line('auth_required')),
		                        'pass_good'=>array('msj'=>$this->lang->line('auth_required')),
		                        'pass_strong'=>array('msj'=>$this->lang->line('auth_required')),
		                        ),
		                'passverify'=>array(
		                        'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
		                        'equalTo'=>array('val'=>'#adminpass','istring'=>true,'msj'=>$this->lang->line('auth_pass_dontmatch'))
		                        ),
		                'default_role'=>array(
		                        'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
		                        )
		                )
		        );
        $this->twig->display('general/edit',$data);
    }


}