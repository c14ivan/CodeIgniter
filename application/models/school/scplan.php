<?php
class Scplan extends CI_Model{
	private $table_system       = 'scsystem';
	private $table_plan			= 'scplan';
	private $table_versions		= 'scplanversion';
	private $table_subjectplan  = 'scsubjectplan';
	private $table_planversion  = 'scplanversion';
	private $table_subjects		= 'scsubject';
	private $table_subversions	= 'scsubjectversion';
	private $table_cicles       = 'sccicle';
	
	function __construct()
	{
		parent::__construct();
	
		$ci =& get_instance();
		//$this->table_name			= $ci->config->item('db_table_prefix', 'school').$this->table_name;
		//$this->profile_table_name	= $ci->config->item('db_table_prefix', 'school').$this->profile_table_name;
	}
	function getPlans($systemid=0){
		if($systemid>0){
			$this->db->select("{$this->table_plan}.id,{$this->table_plan}.name,{$this->table_plan}.description,
					count({$this->table_subjectplan}.id)");
			$this->db->where('scsystemid',$systemid);
			
			$this->db->group_by("{$this->table_plan}.id,{$this->table_plan}.name,{$this->table_plan}.description");
		}else{
			$this->db->select("{$this->table_system}.name as sysname,{$this->table_plan}.id,{$this->table_plan}.name,
					{$this->table_plan}.description,count({$this->table_subjectplan}.id)");
			$this->db->join($this->table_system,"{$this->table_system}.id={$this->table_plan}.scsystemid",'LEFT');
			
			$this->db->group_by("{$this->table_system}.name,{$this->table_plan}.id,{$this->table_plan}.name,
					{$this->table_plan}.description");
		}
		$this->db->join($this->table_planversion,"{$this->table_plan}.id={$this->table_planversion}.planid",'LEFT');
		$this->db->join($this->table_subjectplan,"{$this->table_subjectplan}.scplanversionid={$this->table_planversion}.id",'LEFT');
		
		$query=$this->db->get($this->table_plan);
		$response=array();
		if ($query->num_rows() > 0){
			$response = $query->result_array();
		}
		return $response;
	}
	function addPlan($system,$planname,$plandescription){
		$planid=false;
		$data = array(
				'scsystemid' => $system,
				'name' => $planname,
				'description' => $plandescription,
				'creator'  => 0,
				'timecreated'=> date('Y-m-d H:i:s'),
		);
		
		if($this->db->insert($this->table_plan, $data)){
			$planid= $this->db->insert_id();
		}
		if($planid){
			$this->createVersion($planid, $planname, $plandescription);
		}
		return $planid;
	}
	function updatePlan($planid,$planname,$plandescription){
		$data = array(
				'name' => $planname,
				'description' => $plandescription,
				'modifier'=> 0,
				'timemodified'=> date('Y-m-d H:i:s'),
		);
		
		$this->db->where('id', $planid);
		if($this->db->update($this->table_plan, $data)){
			return $planid;
		}
		return false;
	}
	function getCurrentVersion($planid){
	    $this->db->where('planid',$planid);
	    $this->db->where('status',1);
	    $query=$this->db->get($this->table_versions);
	    $response=array();
	    if ($query->num_rows() > 0){
	        $response = $query->row_array();
	    }
	    return $response;
	}
	function createVersion($planid,$name,$description){
		$this->db->where('planid',$planid);
		$this->db->order_by('version','DESC');
		$query=$this->db->get($this->table_versions);
		
		$version=($query->num_rows() > 0)?$query->row()->version+1:1;
		
		$data = array(
				'planid' => $planid,
				'name' => $name,
				'description' => $description,
				'status'  => 0,
		        'version'  => $version,
				'creator'  => 0,
				'timecreated'=> date('Y-m-d H:i:s'),
		);
		
		if($this->db->insert($this->table_versions, $data)){
		    $idcreated= $this->db->insert_id();
			
			$currentversion=$this->getCurrentVersion($planid);
			if($currentversion){
			    $subasigned=$this->asignSubject($currentversion['id']);
			    foreach ($subasigned as $asigned){
			        $this->asignSubject($asigned['sccicleid'],$asigned['id'],$idcreated,$asigned['ih'],$asigned['credits']);
			    }
			}
			
			$this->db->where('status',0);
			$this->db->where('planid',$planid);
			$this->db->where('id!=',$idcreated);
			$this->db->update($this->table_versions,array('status'=>3));
			return $idcreated;
		}
		return false;
		
	}
	function updateVersion($planversionid,$name,$description,$status){
		$data = array(
				'name' => $name,
				'description' => $description,
				'status'  => $status,
				'modifier'=> 0,
				'timemodified'=> date('Y-m-d H:i:s'),
		);
		
		$this->db->where('id', $planversionid);
		if($this->db->update($this->table_versions, $data)){
			return $planversionid;
		}
		return false;
	}
	function activeVersion($planversionid){
	    $versiondata=$this->getVersion($planversionid);
	    $this->db->where('planid',$versiondata->planid);
	    $this->db->update($this->table_versions,array('status'=>2));
	    
	    $this->db->where('id',$planversionid);
	    $this->db->update($this->table_versions,array('status'=>1));
	    
	    return $this->db->affected_rows()==1?true:false;
	}
	function getVersions($planid){
		$this->db->where('planid',$planid);
		$this->db->order_by("{$this->table_planversion}.timecreated","DESC");
		$response=array();
		$query=$this->db->get($this->table_planversion);
		if ($query->num_rows() > 0){
			$response = $query->result_array();
		}
		return $response;
	}
	function getPlan($planid){
		$this->db->where('id',$planid);
		$response=array();
		$query=$this->db->get($this->table_plan);
		if ($query->num_rows() > 0){
			$response = $query->row();
		}
		return $response;
	}
	function getVersion($versionid){
		$this->db->select("{$this->table_planversion}.name,{$this->table_planversion}.description,{$this->table_planversion}.status,
		{$this->table_plan}.scsystemid,{$this->table_planversion}.planid");
		$this->db->join($this->table_plan,"{$this->table_planversion}.planid={$this->table_plan}.id");
		$this->db->where($this->table_planversion.".id",$versionid);
		
		$query= $this->db->get($this->table_planversion);
		$response=false;
		if ($query->num_rows() > 0){
			$response = $query->row();
		}
		return $response;
	}
	function getSubjectsAsigned($versionid){
		$this->db->select("{$this->table_subjects}.id,{$this->table_subjects}.name,{$this->table_subjects}.shortname,
			{$this->table_subjectplan}.sccicleid,{$this->table_subjectplan}.ih,{$this->table_subjectplan}.credits");
		$this->db->where($this->table_subjectplan.".scplanversionid",$versionid);
		$this->db->join($this->table_subjects,"{$this->table_subjects}.id={$this->table_subjectplan}.scsubjectid");
		$this->db->join($this->table_cicles,"{$this->table_cicles}.id={$this->table_subjectplan}.sccicleid");
		$this->db->order_by("{$this->table_cicles}.order","ASC");
		$this->db->order_by("{$this->table_subjectplan}.order","ASC");
		$query = $this->db->get($this->table_subjectplan);
		return $query->result_array();
	}
	function asignSubject($sccicleid,$subjectid,$planversionid,$ih,$credits){
		$this->db->where('sccicleid',$sccicleid);
		$this->db->where('scsubjectid',$subjectid);
		$this->db->where('scplanversionid',$planversionid);
		$query= $this->db->get($this->table_subjectplan);
		$prev=$query->row();
		if($prev){
			$this->db->where('id', $prev->id);
			$data=array('ih'=>$ih,'credits'=>$credits);
			if($this->db->update($this->table_subjectplan, $data)){
				return $prev->id;
			}
		}else{
			$this->db->where('scplanversionid',$planversionid);
			$this->db->order_by("order","DESC");
			$query= $this->db->get($this->table_subjectplan);
			$prev=$query->row();
			
			$data=array(
					'sccicleid'=>$sccicleid,
					'scsubjectid'=>$subjectid,
					'scplanversionid'=>$planversionid,
					'order'=>($prev)?$prev->order+1:1,
					'ih'=>$ih,
					'credits'=>$credits,
			);
			if($this->db->insert($this->table_subjectplan, $data)){
				return $this->db->insert_id();
			}
		}
	}
	function unasignSubject($sccicleid,$subjectid,$planversionid){
	    $this->db->where('sccicleid',$sccicleid);
	    $this->db->where('scsubjectid',$subjectid);
	    $this->db->where('scplanversionid',$planversionid);
	    $query= $this->db->get($this->table_subjectplan);
	    $prev=$query->row();
	    if($prev){
	        $this->db->where('id', $prev->id);
	        $this->db->delete($this->table_subjectplan);
	        return $this->db->affected_rows()==1?true:false;
	    }else{
	        return false;
	    }
	}
}