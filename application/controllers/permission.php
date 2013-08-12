<?php
/**

*/
defined('BASEPATH') OR exit('No direct script access allowed');
class Permission extends MY_Controller {


    function __construct()
    {
        parent::__construct();

        //  load libs
        $this->load->library('tank_auth');
        $this->lang->load('tank_auth');
        $this->load->model('Permissions');
    }


    //TODO implement this functions
    /**
     * admin role permissions
     */
    function roles()
    {
        
    }
    /**
     * TODO make something with this, it's only for update profiles quickly on development
     */
    function reset(){

        $capmode=$this->config->item('permissions_mode','permission');
        //1. crear las capabilities en DB
        $capabilities=$this->config->item('capabilities','permission');
        foreach ($capabilities as $capability => $cap){
            $vis=(isset($cap['visible']) && $cap['visible'])?1:0;
            $pos=(isset($cap['position']))?$cap['position']:'';
            $roles=(isset($cap['roles']))?$cap['roles']:'';
            $parent=(isset($cap['parent']))?$cap['parent']:'';
            $icon=(isset($cap['icon']))?$cap['icon']:'';
            $this->Permissions->set_capability($capability,$cap['weight'],$cap['ctx_level'],$pos,$vis,$roles,$parent,$icon);
        }

        //2. crear los roles por defecto, crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y
        //   que esten en el context_level 0, por defecto quiero crear capacidades para el home
        $roles=$this->config->item('roles', 'permission');
        $adminrole=array();
        foreach($roles as $role){
            $role['id'] = $this->Permissions->update_role($role['name'],$role['weight'],$role['shortname'],$role['description']);
        }
        redirect();
    }
    /**
     * Instalation, create the defaults and insert into database
     */
    function install(){
        $post=$this->input->post();
        if (!empty($post)) {

            //1. crear las capabilities en DB
            $capabilities=$this->config->item('capabilities','permission');
            foreach ($capabilities as $capability => $cap){
                $vis=(isset($cap['visible']) && $cap['visible'])?1:0;
                $pos=(isset($cap['position']))?$cap['position']:'';
                $roles=(isset($cap['roles']))?$cap['roles']:'';
                $parent=(isset($cap['parent']))?$cap['parent']:'';
                $icon=(isset($cap['icon']))?$cap['icon']:'';
                $this->Permissions->set_capability($capability,$cap['weight'],$cap['ctx_level'],$pos,$vis,$roles,$parent,$icon);
            }

            //2. crear los roles por defecto, crear las capacidades de los roles, comparando los pesos de los roles con los pesos de las capacidades y
            //   que esten en el context_level 0, por defecto quiero crear capacidades para el home
            $roles=$this->config->item('roles', 'permission');
            foreach($roles as $role){
                if(!isset($adminrole)){
                    $adminrole=$role;
                }
                if(isset($role['weight']) && isset($adminrole['weight']) && $role['weight']>$adminrole['weight']){
                    $adminrole=$role;
                }
                $role['id'] = $this->Permissions->create_role($role['name'],$role['weight'],$role['shortname'],$role['description']);
            }

            //3. crear contexto home, debe tener context level 10 e instanceid=0
            $home_contextid=$this->Permissions->create_context(CONTEXT_HOME,0,'site');
            //4 crear role por defecto para todos en el home
            $role=$this->Permissions->get_role($this->config->item('default-role', 'permission'));
            if($role){
                //5 asign default role to everybody in the home
                $this->Permissions->enrol_user(0,$home_contextid,$role->id);
            }

            //6. crear un usuario y asignar el role superadministrador para el contexto home.
            $use_username = $this->config->item('use_username', 'tank_auth');
            if(!$use_username) $post['adminlogin']='';
            $data = $this->tank_auth->create_user($post['adminlogin'],$post['adminmail'],$post['adminpass'],false);

            $roleadmin=$this->Permissions->get_role($adminrole['name']);
            if(intval($data['user_id'])>0)
                $this->Permissions->enrol_user($data['user_id'],$home_contextid,$roleadmin->id);
            redirect('');
        }else{
            if(validation_errors()!='')
                $data['errors']['validerrors']= validation_errors();
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
                                'pass_too_short'=>array('msj'=>$this->lang->line('auth_tooshort')),
                                'pass_similar_to_username'=>array('msj'=>$this->lang->line('auth_passtousername')),
                                'pass_very_weak'=>array('msj'=>$this->lang->line('passtooweak')),
                                'pass_good'=>array('msj'=>$this->lang->line('passgood')),
                                'pass_strong'=>array('msj'=>$this->lang->line('passtrong')),
                        ),
                        'passverify'=>array(
                                'required'=>array('val'=>'true','msj'=>$this->lang->line('auth_required')),
                                'equalTo'=>array('val'=>'#adminpass','istring'=>true,'msj'=>$this->lang->line('auth_pass_dontmatch'))
                        )
                )
        );
        $this->twig->display('general/edit',$data);
    }
}
/* End of file permission.php */
/* Location: ./application/controllers/permission.php */