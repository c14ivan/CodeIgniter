<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scsystem extends CI_Model{
	private $table_name			= 'scsystem';		// systems table
	private $table_cicles       = 'sccicle';
	private $table_plans        = 'scplan';
	private $table_planversion  = 'scplanversion';
	private $table_subjectplan  = 'scsubjectplan';
	
	function __construct()
	{
		parent::__construct();
	
		$ci =& get_instance();
		//$this->table_name			= $ci->config->item('db_table_prefix', 'school').$this->table_name;
		//$this->profile_table_name	= $ci->config->item('db_table_prefix', 'school').$this->profile_table_name;
	}
	function getsystems($id=0){
		if($id>0){
			$this->db->where('id',$id);
		}
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0 && $id==0){
			return $query->result_array();
		}elseif($id>0){
			return $query->row();
		}
		
		return NULL;
	}
	// add a new system
	function addSystem($name,$desc,$duration){
		$data = array(
				'name' => $name,
				'description' => $desc,
				'duration' => $duration,
				'creator'  => 0,
				'timecreated'=> time('Y-m-d H:i:s'),
		);
		
		if($this->db->insert($this->table_name, $data)){
			return $this->db->insert_id();
		}
		return false;
	}
	//update a system
	function updateSystem($id,$name,$desc,$duration){
		$data = array(
				'name' => $name,
				'description' => $desc,
				'duration' => $duration,
				'modifier'  => 0,
				'timemod'=> time('Y-m-d H:i:s'),
		);
		
		$this->db->where('id', $id);
		$this->db->update($this->table_name, $data);
		
		return ($this->db->affected_rows()==1)?true:false;
	}

	function getCicles($systemid){
		//TODO verificar si los ciclos se estan usando en alguna asignación. si es asi agregar atributo para no permitir eliminar
		$this->db->where('scsystemid',$systemid);
		$this->db->order_by('order','ASC');
		$query=$this->db->get($this->table_cicles);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		return array();
	}
	function getPlans($systemid=0){
		if($systemid>0){
			$this->db->select("{$this->table_plans}.id,{$this->table_plans}.name,{$this->table_plans}.description,
					{$this->table_plans}.enrol_method,count({$this->table_subjectplan}.id)");
			$this->db->where('scsystemid',$systemid);
			
			$this->db->group_by("{$this->table_plans}.id,{$this->table_plans}.name,{$this->table_plans}.description,
					{$this->table_plans}.enrol_method");
		}else{
			$this->db->select("{$this->table_name}.name,{$this->table_plans}.id,{$this->table_plans}.name,
					{$this->table_plans}.description,{$this->table_plans}.enrol_method,count({$this->table_subjectplan}.id)");
			$this->db->join($this->table_name,"{$this->table_name}.id={$this->table_plans}.scsystemid".'LEFT');
			
			$this->db->group_by("{$this->table_name}.name,{$this->table_plans}.id,{$this->table_plans}.name,
					{$this->table_plans}.description,{$this->table_plans}.enrol_method");
		}
		$this->db->join($this->table_planversion,"{$this->table_plans}.id={$this->table_planversion}.planid",'LEFT');
		$this->db->join($this->table_subjectplan,"{$this->table_subjectplan}.scplanversionid={$this->table_planversion}.id",'LEFT');
		
		$query=$this->db->get($this->table_plans);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		return array();
	}
	function addCicle($systemid,$ciclename,$cicleabbr){
		$this->db->where('cssystemid',$systemid);
		$this->db->order_by('order','DESC');
		$this->db->get($this->table_cicles);
		
		$order=($query->row())?$query->row()->order+1:1;
		
		$data = array(
				'scsystemid' =>$systemid,
				'name' => $ciclename,
				'abbr' => $cicleabbr,
				'duration' => $order,
		);
		
		if($this->db->insert($this->table_cicles, $data)){
			return $this->db->insert_id();
		}
		return false;
	}
	function updateCicle($cicleid,$ciclename,$cicleabbr){
	
		$data = array(
				'name' => $ciclename,
				'abbr' => $cicleabbr,
		);
	
		$this->db->where('id', $cicleid);
		if($this->db->update($this->table_cicles, $data)){
			return $cicleid;
		}
		return false;
	}
}