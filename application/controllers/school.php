<?php
/**

*/
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('school/scsystem');
        $this->load->model('school/scplan');
        $this->load->model('school/scsubject');
        $this->lang->load('school');
        $this->config->load('school');
        $this->lang->load('form_validation');
        $this->output->enable_profiler(TRUE);

    }

    /**
     * systems administration
     */
    public function system(){
        $this->twig->display('school/system',array());
    }
    public function plan(){
    	$availableplanssys=$this->scsystem->getAvailableSystems();
    	$systems=array();
    	foreach ($availableplanssys as $avsys){
    		$systems[$avsys['id']]=$avsys['name'];
    	}
    	$subjects=$this->scsubject->getSubjects();
    	
    	//TODO permitir editar planes de estudio, ya esta hecho en system, migrar
        $this->twig->display('school/plan',array('systems'=>$systems,'subjects'=>$subjects));
    }
    public function subjects(){
        $this->twig->display('school/adminsubjects');
    }
    public function subject(){
        $this->twig->display('school/subject');
    }



    public function addsystem(){
        if(!$this->input->is_ajax_request()) redirect();

        $this->output->enable_profiler(FALSE);
         
        $data=$this->input->post();
        if(intval($data['scid'])>0){
            if($this->scsystem->updateSystem($data['scid'],$data['scname'],$data['scdescription'],$data['scduration'],$data['scstatus'])){
                $data['ok']=true;
            }else{
                $data['ok']=false;
            }
        }else{
            $id=$this->scsystem->addSystem($data['scname'],$data['scdescription'],$data['scduration'],$data['scstatus']);
            if($id){
                $data['scid']=$id;
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
        $divs=$this->scsystem->getDivisions($systemid);
        $plans=$this->scplan->getPlans($systemid);
         
        echo json_encode(array(
                'post'=>$this->input->post(),
                'id'=>$systemid,
                'sysdata'=>$systemdata,
                'cicles'=>$cicles,
                'divisions'=>$divs,
                'inplan'=>$plans));
    }
    public function addcicle(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);

        $cicledata=$this->input->post();
         
        if($cicledata['cicleid']>0){
            $cicledata['cicleid']=$this->scsystem->updateCicle($cicledata['cicleid'],$cicledata['ciclename'],$cicledata['cicleabbr'],$cicledata['cicleorder']);
        }else{
            $cicledata['cicleid']=$this->scsystem->addCicle($cicledata['scsystemid'],$cicledata['ciclename'],$cicledata['cicleabbr']);
        }

        echo json_encode(array('cicle'=>$cicledata));
    }
    public function delcicle(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
        
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
    
        $cicledata=$this->input->post();
    
        echo json_encode(array('cicle'=>$cicledata['cicle'],'ok'=>$this->scsystem->delCicle($cicledata['cicle'],$cicledata['system'])));
    }
    public function ordercicles(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
         
        $postdata=$this->input->post();
        $cicles=$this->scsystem->getCicles($postdata['system']);
        $order=$postdata['order'];
        foreach ($cicles as $cicle){
            if($cicle['order']!=$order[$cicle['id']]){
                $this->scsystem->updateCicle($cicle['id'],$cicle['name'],$cicle['abbr'],$order[$cicle['id']]);
            }
        }
        $res=true;
        $cicles=$this->scsystem->getCicles($postdata['system']);
        foreach ($cicles as $cicle){
            if($cicle['order']!=$order[$cicle['id']]){
                $res=false;
            }
        }
        echo json_encode(array('ok'=>$res));
    }
    public function adddivision(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
         
        $divdata=$this->input->post();

        if(intval($divdata['divid'])>0){
            $divdata['divid']=$this->scsystem->updateDivision($divdata['divid'],$divdata['divname'],$divdata['divorder']);
        }else{
            $divdata['divid']=$this->scsystem->addDivision($divdata['scsystemid'],$divdata['divname']);
        }
         
        echo json_encode(array('div'=>$divdata));
    }
    public function orderdivs(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);

        $postdata=$this->input->post();
        $divs=$this->scsystem->getDivisions($postdata['system']);
        $order=$postdata['order'];
        foreach ($divs as $div){
            if($div['order']!=$order[$div['id']]){
                $this->scsystem->updateDivision($div['id'],$div['name'],$order[$div['id']]);
            }
        }
        $res=true;
        $divs=$this->scsystem->getDivisions($postdata['system']);
        foreach ($divs as $div){
            if($div['order']!=$order[$div['id']]){
                $res=false;
            }
        }
        echo json_encode(array('ok'=>$res));
    }
    public function deldivision(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
    
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
    
        $cicledata=$this->input->post();
    
        echo json_encode(array('division'=>$cicledata['division'],'ok'=>$this->scsystem->delDivision($cicledata['division'],$cicledata['system'])));
    }
    public function addplan(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
         
        $postdata=$this->input->post();

        if($postdata['planid']>0){
            $postdata['planid']=$this->scplan->updatePlan($postdata['planid'],$postdata['planname'],$postdata['plandescription']);
        }else{
            $postdata['planid']=$this->scplan->addPlan($postdata['scsystemid'],$postdata['planname'],$postdata['plandescription']);
        }
        echo json_encode(array('plan'=>$postdata));
         
    }
    public function addplanversion(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	 
    	$postdata=$this->input->post();
    	
    	if($postdata['scplanversionid']>0){
    		$postdata['scplanversionid']=$this->scplan->updateVersion($postdata['scplanversionid'],$postdata['scname'],$postdata['scdescription'],$postdata['scstatus']);
    	}else{
    		$postdata['scplanversionid']=$this->scplan->createVersion($postdata['scplanid'],$postdata['scname'],$postdata['scdescription']);
    	}
    	echo json_encode(array('plan'=>$postdata));
    }
    public function getplans(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	echo json_encode(array('plans'=>$this->scplan->getPlans()));
    }
    public function getplan(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	echo json_encode(array('plan'=>$this->scplan->getPlan($this->input->post('planid'))));
    }
    public function getplanversions(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	echo json_encode(array('planversions'=>$this->scplan->getVersions($this->input->post('planid'))));
    }
    public function addarea(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
        $postdata=$this->input->post();
        if(intval($postdata['scareaid'])>0){
            $areaid=$this->scsubject->updateArea($postdata['scareaid'],$postdata['scarea'],$postdata['scdescription'],isset($postdata['scblocked']));
        }else{
            $areaid=$this->scsubject->addArea($postdata['scarea'],$postdata['scdescription'],isset($postdata['scblocked']));
            $postdata['scareaid']=$areaid;
        }
         
        echo json_encode(array('ok'=>($areaid!=false),'area'=>$postdata));
    }
    public function getareas(){
        $this->output->enable_profiler(FALSE);
        echo json_encode(array('areas'=>$this->scsubject->getAreas()));
    }
    public function getsubject(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
        echo json_encode($this->scsubject->getSubject($this->input->post('subjectid')));
    }
    public function getsubjects(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
        echo json_encode(array('subjects'=>$this->scsubject->getSubjects()));
    }
    public function getsubjectversions(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
        echo json_encode(array('versions'=>$this->scsubject->getVersions($this->input->post('subjectid'))));
    }
    public function addsubject(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
         
        $postdata=$this->input->post();
        if(intval($postdata['scsubjectid'])>0){
            $postdata['scsubjectid']=$this->scsubject->updateSubject($postdata['scsubjectid'],$postdata['scname'],
                    $postdata['scabbr'],$postdata['scdescription'],isset($postdata['scblocked']));
        }else{
            $postdata['scsubjectid']=$this->scsubject->addSubject($postdata['scarea'],$postdata['scname'],
                    $postdata['scabbr'],$postdata['scdescription'],isset($postdata['scblocked']));
        }
        echo json_encode(array('ok'=>(intval($postdata['scsubjectid'])>0),'subject'=>$postdata));
    }
    public function addsubjectversion(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);

        $postdata=$this->input->post();
        if(intval($postdata['scsubjectversionid'])>0){
            $postdata['scsubjectversionid']=$this->scsubject->updateVersion($postdata['scsubjectversionid'],$postdata['scname'],
                    $postdata['scdescription']);
        }else{
            $postdata['scsubjectversionid']=$this->scsubject->addVersion($postdata['scsubjectid'],$postdata['scname'],
                    $postdata['scdescription']);
        }
        echo json_encode(array('ok'=>(intval($postdata['scsubjectversionid'])>0),'subject'=>array('id'=>$postdata['scsubjectid'])));
    }
    public function  getdataversion(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	$versionid=$this->input->post('versionid');
    	$versiondata= $this->scplan->getVersion($versionid);
    	$cicles=$this->scsystem->getCicles($versiondata->scsystemid);
    	$subasigned= $this->scplan->getSubjectsAsigned($versionid);
    	$response=array('versionid'=>$versionid,'versiondata'=>$versiondata,'cicles'=>$cicles,'subasigned'=>$subasigned);
    	echo json_encode($response);
    }
    public function asignsubject(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	
    	$subjectid=$this->input->post('subject');
    	$planversionid=$this->input->post('version');
    	$sccicleid=$this->input->post('cicle');
    	$ih=$this->input->post('ih');
    	$credits=$this->input->post('credits');
    	$asignment=$this->scplan->asignSubject($sccicleid,$subjectid,$planversionid,$ih,$credits);
    	$subject=$this->scsubject->getSubject($subjectid);
    	$subject=$subject['subject'];
    	echo json_encode(array('ih'=>$ih,'cicle'=>$sccicleid,'credits'=>$credits,'subjectid'=>$subjectid,'subject'=>$subject->name,'shortname'=>$subject->shortname));
    	
    }
    public function unasignsubject(){
        if(!$this->input->is_ajax_request()) redirect();
        $this->output->enable_profiler(FALSE);
         
        $subjectid=$this->input->post('subject');
        $planversionid=$this->input->post('version');
        $sccicleid=$this->input->post('cicle');
        echo json_encode(array('cicle'=>$sccicleid,'subjectid'=>$subjectid,'ok'=>$this->scplan->unasignSubject($sccicleid,$subjectid,$planversionid)));
         
    }
    public function ordersubjects(){
    	if(!$this->input->is_ajax_request()) redirect();
    	$this->output->enable_profiler(FALSE);
    	$orderid=$this->input->post('order');
    	$planversionid=$this->input->post('version');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */