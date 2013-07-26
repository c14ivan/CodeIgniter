<?php
class Scplan extends CI_Model{
	private $table_plan			= 'scplan';
	private $table_versions		= 'scplanversion';
	private $table_subjectplan  = 'scsubjectplan';
	private $table_planversion  = 'scplanversion';
	
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
			$this->db->select("{$this->table_name}.name,{$this->table_plan}.id,{$this->table_plan}.name,
					{$this->table_plan}.description,count({$this->table_subjectplan}.id)");
			$this->db->join($this->table_name,"{$this->table_name}.id={$this->table_plan}.scsystemid".'LEFT');
			
			$this->db->group_by("{$this->table_name}.name,{$this->table_plan}.id,{$this->table_plan}.name,
					{$this->table_plan}.description");
		}
		$this->db->join($this->table_planversion,"{$this->table_plan}.id={$this->table_planversion}.planid",'LEFT');
		$this->db->join($this->table_subjectplan,"{$this->table_subjectplan}.scplanversionid={$this->table_planversion}.id",'LEFT');
		
		$query=$this->db->get($this->table_plan);
		$response=array();
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $plan){
				$response[$plan['id']]=$plan;
				$response[$plan['id']]['url']=anchor('school/plan/'.$plan['id'],$plan['name']);
			}
			return $response;
		}
		return array();
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
		$this->db->order_by('order','DESC');
		$query=$this->db->get($this->table_versions);
		
		$version=($query->num_rows() > 0)?$query->row()->order+1:1;
		
		$data = array(
				'planid' => $planid,
				'name' => $name,
				'description' => $description,
				'status'  => 0,
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
}