<?php
class Enrolment extends CI_Model{
    private $table_enrolmets    = 'sc_enrolmethods';
    private $table_plans        = 'scplan';
    private $table_roles        = 'role';
    private $table_inscriptions = 'sc_inscription';
    private $table_preparent    = 'sc_preparent';
    
    function __construct()
    {
        parent::__construct();
        //$this->table_name			= $ci->config->item('db_table_prefix', 'school').$this->table_name;
        //$this->profile_table_name	= $ci->config->item('db_table_prefix', 'school').$this->profile_table_name;
    }
    function get_methods(){
        $this->db->select("{$this->table_enrolmets}.idenrolmethods as id,{$this->table_enrolmets}.name,{$this->table_plans}.name as planname,{$this->table_enrolmets}.method,
            {$this->table_roles}.name as rolename,hasinscription,hasform,statusinsc,statusenrol");
        $this->db->join($this->table_plans,"{$this->table_enrolmets}.scplanid={$this->table_plans}.id");
        $this->db->join($this->table_roles,"{$this->table_enrolmets}.roleid={$this->table_roles}.id");
        $query=$this->db->get($this->table_enrolmets);
        return $query->result_array();
    }
    function get_method($enrolmentid){
        $this->db->select("{$this->table_enrolmets}.idenrolmethods as id,{$this->table_enrolmets}.name,{$this->table_plans}.id as planid,
            {$this->table_plans}.name as planname,{$this->table_enrolmets}.method,{$this->table_roles}id as roleid,
            {$this->table_roles}.name as rolename,mode,hasinscription,hasform,statusinsc,statusenrol,longmode,time,fini,fend");
        $this->db->join($this->table_plans,"{$this->table_enrolmets}.scplanid={$this->table_plans}.id");
        $this->db->join($this->table_roles,"{$this->table_enrolmets}.roleid={$this->table_roles}.id");
        $this->db->where('idenrolmethods',$enrolmentid);
        $query=$this->db->get($this->table_enrolmets);
        if($query->num_rows()>0){
            $dato=$query->row();
            $dato->fini=date('m/d/Y',strtotime($dato->fini));
            $dato->fend=date('m/d/Y',strtotime($dato->fend));
            return $dato;
        }else{
            return false;
        }
    }
    function add_method($name,$plan,$method,$hasinscription,$hasenrolform,$rol,$enrolmode,$statusinsc,$statusenrol,$longmode,$time,$fini,$fend){
        $data=array(
                'name'=>$name,
                'scplanid'=>$plan,
                'method'=>$method,
                'hasinscription'=>$hasinscription,
                'hasform'=>$hasenrolform,
                'roleid'=>$rol,
                'mode'=>$enrolmode,
                'statusinsc'=>($hasinscription)?$statusinsc:0,
                'statusenrol'=>($hasenrolform)?$statusenrol:0,
                'longmode'=>$longmode,
                'time'=>$time,
                'fini'=>date('Y-m-d H:i:s',strtotime($fini)),
                'fend'=>date('Y-m-d H:i:s',strtotime($fend)+86399),
        );
        if($this->db->insert($this->table_enrolmets, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function edit_method($methodid,$name,$plan,$method,$hasinscription,$hasenrolform,$rol,$enrolmode,$statusinsc,$statusenrol,$longmode,$time,$fini,$fend){
        $data=array(
                'name'=>$name,
                'scplanid'=>$plan,
                'method'=>$method,
                'hasinscription'=>$hasinscription,
                'hasform'=>$hasenrolform,
                'roleid'=>$rol,
                'mode'=>$enrolmode,
                'statusinsc'=>($hasinscription)?$statusinsc:0,
                'statusenrol'=>($hasenrolform)?$statusenrol:0,
                'longmode'=>$longmode,
                'time'=>$time,
                'fini'=>date('Y-m-d H:i:s',strtotime($fini)),
                'fend'=>date('Y-m-d H:i:s',strtotime($fend)+86399),
        );
        $this->db->where('idenrolmethods', $methodid);
		$this->db->update($this->table_enrolmets, $data);
		return $this->db->affected_rows()==1?true:false;
    }
    function delete_method($enrolmentid){
        $this->db->where('idenrolmethods', $enrolmentid);
        $this->db->delete($this->table_enrolmets);
        return $this->db->affected_rows()==1?true:false;
    }
    function status_inscription($enrolmentid,$status){
        $data=array('statusinsc'=>$status);
        $this->db->where('idenrolmethods',$enrolmentid);
        $this->db->update($this->table_enrolmets,$data);
        return ($this->db->affected_rows()==1)?true:false;
    }
    function status_enrolment($enrolmentid,$status){
        $data=array('statusenrol'=>$status);
        $this->db->where('idenrolmethods',$enrolmentid);
        $this->db->update($this->table_enrolmets,$data);
        return ($this->db->affected_rows()==1)?true:false;
    }
    function get_inscription($inscriptionid){
        $query=$this->db->get($this->table_inscriptions);
        if($query->num_rows()>0){
            $dato=$query->row();
            return $dato;
        }else{
            return false;
        }
    }
    function set_inscription($methodid,$contexts,$lastnames,$names,$nuip, $nuipfrom,$bornday,
            $bornplace,$address,$neighborhood,$homeplace,$phone,$stratum,$rh,$conduct,$has_brothers,$ownhouse,
            $relatives,$interview,$interview_result,$isrepeating,$schoolfrom,$whyfrom,$reasontoenter){
        $data=array(
                'methodid'=>$methodid,
                'lastnames'=>$lastnames,
                'names'=>$names,
                'nuip'=>$nuip,
                'nuipfrom'=>$nuipfrom,
                'bornday'=>date('Y-m-d H:i:s',strtotime($bornday)),
                'bornplace'=>$bornplace,
                'address'=>$address,
                'neighborhood'=>$neighborhood,
                'homeplace'=>$homeplace,
                'phone'=>$phone,
                'stratum'=>$stratum,
                'rh'=>$rh,
                'conduct'=>$conduct,
                'relatives'=>$has_brothers,
                'ownhouse'=>$ownhouse,
                'family'=>$relatives,
                'interviewcoment'=>$interview,
                'interviewresult'=>$interview_result,
                'repeating'=>$isrepeating,
                'schoolfrom'=>$schoolfrom,
                'schoolwhyfrom'=>$whyfrom,
                'whyenter'=>$reasontoenter,
                'context'=>$contexts,
                'timecreated'=>date('Y-m-d H:i:s')
        );
        if($this->db->insert($this->table_inscriptions, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function get_preparent($inscription){
        $this->db->where('inscriptionid',$inscription);
        $query=$this->db->get($this->table_preparent);
        if($query->num_rows()>0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    function set_preparent($inscription,$parentname,$kinship,$job,$company,$companytime,$phone,$email){
        $data=array(
                'inscriptionid'=>$inscription,
                'parentname'=>$parentname,
                'kinship'=>$kinship,
                'job'=>$job,
                'company'=>$company,
                'comptime'=>$companytime,
                'phone'=>$phone,
                'email'=>$email,
        );
        if($this->db->insert($this->table_preparent, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function set_enrolform(){
        
    }
    function set_enrolment(){
        
    }
}
