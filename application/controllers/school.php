<?php
/**

 */
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->load->model('school/scsystem');
		$this->lang->load('school');
		$this->lang->load('form_validation');
        $this->output->enable_profiler(TRUE);
		
	}
	
    /**
     * systems administration
     */
    public function system(){
    	$plans=$this->scsystem->getPlans(1);
        $this->twig->display('school/system',array());
    }
    public function plan(){
    	$this->twig->display('school/adminsubjects');
    }
    public function subjects(){
    	$this->twig->display('school/adminsubjects');
    }
    public function subject(){
    	$this->twig->display('school/adminsubjects');
    }
    
    public function addsystem(){
    	if(!$this->input->is_ajax_request()) redirect();

    	$this->output->enable_profiler(FALSE);
    	
    	$data=$this->input->post();
    	if(intval($data['scid'])>0){
    		$id=$this->scsystem->addSystem($data['scname'],$data['scdescription'],$data['scduration']);
    		if($id){
    		$data['scid']=$id;
    		$data['ok']=true;
    		}else{
    			$data['ok']=false;
    		}
    	}else{
    		if($this->scsystem->updateSystem($data['scid'],$data['scname'],$data['scdescription'],$data['scduration'])){
    			$data['ok']=true;
    		}else{
    			$data['ok']=false;
    		}
    	}
    	echo json_encode($data);
    }
    public function getsystems(){
    	if(!$this->input->is_ajax_request()) redirect();

    	$this->output->enable_profiler(FALSE);
    	$systems=$this->scsystem->getsystems();
    	echo json_encode(array('systems'=>$systems));
    }
    
    public function getsystem(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	
    	$systemid=$this->input->post('sysid');
    	$systemdata=$this->scsystem->getsystems($systemid);
    	$cicles=$this->scsystem->getCicles($systemid);
    	$plans=$this->scsystem->getPlans($systemid);
    	
    	echo json_encode(array('post'=>$this->input->post(),'id'=>$systemid,'sysdata'=>$systemdata,'cicles'=>$cicles,'inplan'=>$plans));
    }
    public function addcicle(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	 
    	$cicledata=$this->input->post();
    	
    	if($cicledata['cicleid']>0){
    		$cicledata['cicleid']=addCicle($cicledata['scsystemid'],$cicledata['ciclename'],$cicledata['cicleabbr']);
    	}else{
    		$cicledata['cicleid']=addCicle($cicledata['cicleid'],$cicledata['ciclename'],$cicledata['cicleabbr']);
    	}
    	 
    	echo json_encode(array('cicle'=>$cicledata));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */