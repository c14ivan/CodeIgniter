<?php
class Scplan extends CI_Model{
	private $table_system       = 'scsystem';
	private $table_plan			= 'scplan';
	private $table_versions		= 'scplanversion';
	private $table_subjectplan  = 'scsubjectplan';
	private $table_planversion  = 'scplanversion';
	private $table_subjects		= 'scsubject';
	private $table_subversions	= 'scsubjectversion';
	
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
			return $this->db->insert_id();
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
	function getVersions($planid){
		$this->db->where('planid',$planid);
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
		{$this->table_plan}.scsystemid");
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
		$this->db->select("{$this->table_subversions}.id,{$this->table_subjects}.name,{$this->table_subversions}.name as vername,
		{$this->table_subversions}.status");
		$this->db->where($this->table_subjectplan.".scplanversionid",$versionid);
		$this->db->join($this->table_subversions,"{$this->table_subversions}.id={$this->table_subjectplan}.scsubjectversionid");
		$this->db->join($this->table_subjects,"{$this->table_subjects}.id={$this->table_subversions}.scsubjectid");
		$query = $this->db->get($this->table_subjectplan);
		$response = $query->result_array();
	}
}