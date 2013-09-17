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
        $this->load->model('school/Scplan');
        $this->load->model('school/Scsystem');
        $this->load->model('install/Locations');
        $this->load->model('school/enrolment');
        $this->load->library('Pdf');

    }
    function manage(){
        $plans=$this->Scplan->getPlans();
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
            $cities=$this->Locations->getCities('Colombia');
            
            $plan=$this->Scplan->getPlan($enroldata->planid);
            $cicles=$this->Scsystem->getCicles($plan->scsystemid,true);
            $ciclesubjects=array();
            
            if($enroldata->mode==2){
                $planversion=$this->Scplan->getCurrentVersion($enroldata->planid);
                $subjects=$this->Scplan->getSubjectsAsigned($planversion['id']);
                
                foreach ($subjects as $sub){
                    if(!isset($ciclesubjects[$sub['sccicleid']])){
                        $ciclesubjects[$sub['sccicleid']]=array(
                                'name'=>$cicles[$sub['sccicleid']],
                                'options'=>array()
                        );
                    }
                    $ciclesubjects[$sub['sccicleid']]['options'][$sub['id']]=$sub['shortname'].' '.$sub['name'];
                }
            }
            $this->twig->display('enrolments/inscription',array('idmethod'=>$idmethod,'data'=>$enroldata,'cities'=>$cities,'cicles'=>$cicles,'ciclesubjects'=>$ciclesubjects));
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
    function inscriptionpdf($inscriptionid){
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false,false,array('noprint_numpage'=>true));
        $pdf->SetTitle(lang('sc_inscription'));
        
        $data=$this->enrolment->get_inscription($inscriptionid);
        $data->parents=$this->enrolment->get_preparent($inscriptionid);
        $method=$this->enrolment->get_method($data->methodid);
        
        $cities=$this->Locations->getCities('Colombia',true);
        
        $plan=$this->Scplan->getPlan($method->planid);
        $cicles=$this->Scsystem->getCicles($plan->scsystemid,true);
        
        $subjects=Array();
        if($method->mode==2){//subjects
            $planversion=$this->Scplan->getCurrentVersion($method->planid);
            $subjects=$this->Scplan->getSubjectsAsigned($planversion['id']);
        }
        $pdf->SetFont('dejavusans', '', 9, '', true);
        // add a page
        $pdf->AddPage('P', 'A4');
        
        $html = $this->twig->getDisplay('enrolments/inscriptionpdf',array('data'=>$data,'method'=>$method,'cicles'=>$cicles,'subjects'=>$subjects,'cities'=>$cities));
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $htmlsigns='<table style="width:100%;">
            <tr>
                <td>________________________________________</td>
                <td>________________________________________</td>
            </tr>
            <tr>
                <td>'.lang('sc_signstudent').'</td>
                <td>'.lang('sc_signparent').'</td>
            </tr>
        </table>';
        $pdf->setY($pdf->getPageHeight()-30);
        $pdf->writeHTML($htmlsigns, true, false, true, false, 'C');
        $pdf->Output('inscription.pdf', 'I');
    }
    
    
    function inscriptionform(){
        //TODO: in the future the fieds has to be personalized
        if(!$this->input->is_ajax_request()) redirect();
        $data=$this->input->post();
        $inscriptionid=$this->enrolment->set_inscription(
                $data['methodid'],
                $data['contexts'],
                $data['lastnames'],
                $data['names'],
                $data['nuip'],
                $data['nuipfrom'],
                $data['bornday'],
                $data['bornplace'],
                $data['address'],
                $data['neighborhood'],
                $data['homeplace'],
                $data['phone'],
                $data['stratum'],
                $data['rh'],
                $data['conduct'],
                isset($data['hasbrothers'])?1:0,
                $data['ownhouse'],
                $data['relatives'],
                $data['interviewcoment'],
                $data['interviewresult'],
                isset($data['repeat'])?1:0,
                $data['schoolfrom'],
                $data['schoolwhyfrom'],
                $data['reasontoenter']
            );
        if($inscriptionid>0){
            foreach($data['parentname'] as $keyparent=>$parent){
                if(strlen(trim($data['parentname'][$keyparent]))>0){
                    $this->enrolment->set_preparent($inscriptionid,$data['parentname'][$keyparent],$data['parentrol'][$keyparent],
                        $data['parentjob'][$keyparent],$data['parentcomp'][$keyparent],$data['parentcomptime'][$keyparent],
                        $data['parentphone'][$keyparent],$data['parentemail'][$keyparent]);
                }
            }
        }
        echo json_encode(array(
                'ok'=>$inscriptionid,
                'msj'=>($inscriptionid)?lang('sc_inscriptionok'):lang('sc_inscriptionfail'),
                'url'=>($inscriptionid)?anchor('enrolments/inscriptionpdf/'.$inscriptionid,lang('view_pdf'),'target="_blank"'):''));
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