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
        $this->output->enable_profiler(TRUE);
		
	}
	
    /**
     * systems administration
     */
    public function system(){
    	$systems=$this->scsystem->getsystems();
        $this->twig->display('school/system',array('systems'=>$systems));
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */