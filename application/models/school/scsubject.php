<?php
class Scsubject extends CI_Model{
	private $table_areas			= 'scarea';
	private $table_subjects			= 'scsubject';
	private $table_subversions		= 'scsubjectversion';
	
	function __construct()
	{
		parent::__construct();
	
		$ci =& get_instance();
		//$this->table_name			= $ci->config->item('db_table_prefix', 'school').$this->table_name;
		//$this->profile_table_name	= $ci->config->item('db_table_prefix', 'school').$this->profile_table_name;
	}
	
	function addArea($areaname,$areadescription,$blocked){
		$areaid=false;
		$data = array(
				'name' => $areaname,
				'description' => $areadescription,
				'blocked'=> $blocked
		);
		
		if($this->db->insert($this->table_areas, $data)){
			$areaid= $this->db->insert_id();
		}
		return $areaid;
	}
	function updateArea($areaid,$areaname,$areadescription,$blocked){
		$data = array(
				'name' => $areaname,
				'description' => $areadescription,
				'blocked'=> $blocked
		);
		
		$this->db->where('id', $areaid);
		if($this->db->update($this->table_areas, $data)){
			return $areaid;
		}
		return false;
	}
	function getAreas($justnames=false){
		$query=$this->db->get($this->table_areas);
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $area){
				if($justnames){
					$response[$area['id']]=$area['name'];
				}else{
					$response[$area['id']]=$area;
				}
			}
			return $response;
		}
		return null;
	}
	function addSubject($areaid,$name,$shortname,$description,$blocked){
		$subjectid=false;
		$data = array(
				'scareaid' => $areaid,
				'name' => $name,
				'shortname' => $shortname,
				'description' => $description,
				'blocked' => $blocked,
		);
		
		if($this->db->insert($this->table_subjects, $data)){
			$subjectid= $this->db->insert_id();
		}
		return $subjectid;
	}
	function updateSubject($subjectid,$name,$shortname,$description,$blocked){
		//TODO no actualizar si ya estaba bloqueado
		$data = array(
				'name' => $name,
				'shortname' => $shortname,
				'description' => $description,
				'blocked' => $blocked,
		);
		
		$this->db->where('id', $subjectid);
		if($this->db->update($this->table_subjects, $data)){
			return $subjectid;
		}
		return false;
	}
	function getSubjects(){
		$this->db->select("{$this->table_subjects}.id,{$this->table_areas}.name as areaname,{$this->table_subjects}.name,{$this->table_subjects}.shortname,
			{$this->table_subjects}.blocked,{$this->table_areas}.name as areaname");
		$this->db->join($this->table_areas,"{$this->table_areas}.id={$this->table_subjects}.scareaid");
		$query=$this->db->get($this->table_subjects);
		return $query->result_array();
	}
	function getSubject($subjectid){
		$response=null;
		$this->db->where('id', $subjectid);
		$query=$this->db->get($this->table_subjects);
		if ($query->num_rows() > 0){
			$response=array();
			$response['subject']=$query->row();
		}
		return $response;
	}
	function getVersions($subjectid){
		$response=null;
		$this->db->where('scsubjectid', $subjectid);
		$query=$this->db->get($this->table_subversions);
		if ($query->num_rows() > 0){
			$response=$query->result_array();
		}
		return $response;
	}
	function addVersion($subject,$name,$description,$copyversion=0){
	    $this->db->where('scsubjectid',$subject);
	    $this->db->order_by('version','DESC');
	    $query=$this->db->get($this->table_subversions);
	    
	    $version=($query->num_rows() > 0)?$query->row()->version+1:1;
	    
	    $subjectversionid=false;
	    $data = array(
	            'scsubjectid' => $subject,
	            'name' => $name,
	            'version' => $version,
	            'description' => $description,
	            'status' => 0,
	    );
	    
	    if($this->db->insert($this->table_subversions, $data)){
	        $subjectversionid= $this->db->insert_id();
	    }
	    if($copyversion>0 && $subjectversionid!=false){
	        $this->copyVersion($subjectversionid, $copyversion);
	    }
	    return $subjectversionid;
	}
	function updateVersion($versionid,$name,$description,$status){
	    $data = array(
	            'name' => $name,
	            'description' => $description,
	            'status' => $status,
	    );
	    $this->db->where('id', $versionid);
	    if($this->db->update($this->table_subversions, $data)){
	        return $versionid;
	    }
	    return false;
	}
	//TODO implement this function after or subject design
	function copyVersion($newversion,$oldversion){
	    
	}
}