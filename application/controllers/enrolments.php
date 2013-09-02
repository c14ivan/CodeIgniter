<?php
/**

*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Enrolments extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        $this->lang->load('school');
        $this->lang->load('date');
        $this->lang->load('relatives');
        $this->config->load('school');
        $this->lang->load('form_validation');
        $this->load->model('school/scplan');
        $this->load->model('school/enrolment');

    }
    function manage(){
        $plans=$this->scplan->getPlans();
        $plansarr=array();
        foreach ($plans as $plan){
            $plansarr[$plan['id']]=$plan['name'];
        }
        $roles=$this->Permissions->get_roles(true);
        $rolesarr=array();
        foreach ($roles as $rol){
            $rolesarr[$rol['id']]=$rol['name'];
        }
        $this->twig->display('enrolments/manage',array('plans'=>$plansarr,'roles'=>$rolesarr));
    }
    function asigns(){
        $data=$this->input->post();
        if(!empty($data['action'])){
            if(strpos($data['action'],'insc_')!==false){
                $met=intval(str_replace('insc_','',$data['action'])); 
                if(intval($met>0)){
                    $this->session->set_flashdata('idmethod',intval($met));
                    redirect('enrolments/inscriptions');
                }
            }
            elseif(strpos($data['action'],'enrol_')){
                $met=intval(str_replace('insc_','',$data['action']));
                if(intval($met>0)){
                    $this->session->set_flashdata('idmethod',intval($met));
                    redirect('enrolments/enrol');
                }
            }
        }
        $this->twig->display('enrolments/asigns',array('enrolmethods'=>$this->enrolment->get_methods()));
    }
    
    function inscriptions(){
        $idmethod=$this->session->flashdata('idmethod');
        $this->session->keep_flashdata('idmethod');
        $enroldata=$this->enrolment->get_method($idmethod);
        if($enroldata->hasinscription==1 && $enroldata->statusinsc==1){
            $this->twig->display('enrolments/inscription',array('idmethod'=>$idmethod,'data'=>$enroldata));
        }else{
            redirect('');
        }
    }
    function enrol(){
        $idmethod=$this->session->flashdata('idmethod');
        $this->session->keep_flashdata('idmethod');
        $enroldata=$this->enrolment->get_method($idmethod);
        if($enroldata->hasform==1 && $enroldata->statusenrol==1){
            $this->twig->display('enrolments/enrolform',array('idmethod'=>$idmethod,'data'=>$enroldata));
        }elseif($enroldata->statusenrol==1){
            $this->twig->display('enrolments/enrol',array('idmethod'=>$idmethod,'data'=>$enroldata));
        }else{
            redirect('');
        }
    }
    
    
    
    
    function getenrolmethods(){
        if(!$this->input->is_ajax_request()) redirect();
        echo json_encode(array('ok'=>true,'data'=>$this->enrolment->get_methods()));
    }
    function setenrolmethod(){
        if(!$this->input->is_ajax_request()) redirect();
        $enroldata=$this->input->post();
        if($enroldata['scenrolmentid']>0){
            $ok=$this->enrolment->edit_method($enroldata['scenrolmentid'],$enroldata['name'],$enroldata['plan'],$enroldata['method'],isset($enroldata['hasinscription']),
                isset($enroldata['hasenrolform']),$enroldata['rol'],$enroldata['enrolmode'],
                $enroldata['statusinscription'],$enroldata['statusenrolment'],$enroldata['typelong'],$enroldata['enrollong'],
                $enroldata['enrol_fini'],$enroldata['enrol_fend']);
        }else{
            $ok=$this->enrolment->add_method($enroldata['name'],$enroldata['plan'],$enroldata['method'],isset($enroldata['hasinscription']),
                isset($enroldata['hasenrolform']),$enroldata['rol'],$enroldata['enrolmode'],
                isset($enroldata['statusinscription'])?$enroldata['statusinscription']:0,$enroldata['statusenrolment'],$enroldata['typelong'],$enroldata['enrollong'],
                $enroldata['enrol_fini'],$enroldata['enrol_fend']);
        }
        echo json_encode(array('ok'=>$ok));
    }
    function delete_enrolmeth(){
        if(!$this->input->is_ajax_request()) redirect();
        $enrolmentid=$this->input->post('id');
        $ok=$this->enrolment->delete_method($enrolmentid);
        echo json_encode(array('msj'=>$ok?lang('okdelete'):lang('ajaxerror')));
    }
    function get_method(){
        if(!$this->input->is_ajax_request()) redirect();
        $enrolmentid=$this->input->post('id');
        echo json_encode(array('method'=>$this->enrolment->get_method($enrolmentid)));
    }
    function toggle_inscription_status(){
        if(!$this->input->is_ajax_request()) redirect();
        $enrolmentid=$this->input->post('enrolmentid');
        $method=$this->enrolment->get_method($enrolmentid);
        $ok=false;
        if($method){
            $newstatus=($method->hasinscription==1 && $method->statusinsc==0)?1:0;
            $ok=$this->enrolment->status_inscription($enrolmentid,$newstatus);
        }
        echo json_encode(array('ok'=>$ok));
    }
    function toggle_enrol_status(){
        if(!$this->input->is_ajax_request()) redirect();
        $enrolmentid=$this->input->post('enrolmentid');
        $method=$this->enrolment->get_method($enrolmentid);
        $ok=false;
        if($method){
            $newstatus=($method->statusenrol==0)?1:0;
            $ok=$this->enrolment->status_enrolment($enrolmentid,$newstatus);
        }
        echo json_encode(array('ok'=>$ok));
    }
}