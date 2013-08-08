<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Scsystem extends CI_Model{
    private $table_name			= 'scsystem';		// systems table
    private $table_cicles       = 'sccicle';
    //private $table_plans        = 'scplan';
    //private $table_planversion  = 'scplanversion';
    //private $table_subjectplan  = 'scsubjectplan';
    private $table_divs			= 'scsystemdiv';

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
    function getAvailableSystems(){
        $this->db->where('status',1);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0){
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
                'timecreated'=> date('Y-m-d H:i:s'),
        );

        if($this->db->insert($this->table_name, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    //update a system
    function updateSystem($id,$name,$desc,$duration,$status){
        $prev=$this->getsystems($id);
        if($prev->status==0)
        {
            $data = array(
                    'name' => $name,
                    'description' => $desc,
                    'duration' => $duration,
                    'status'   => $status,
                    'modifier'  => 0,
                    'timemod'=> date('Y-m-d H:i:s'),
            );
        }elseif($prev->status==1 && $status>1){
            $data = array(
                    'status'   => $status,
                    'modifier'  => 0,
                    'timemod'=> date('Y-m-d H:i:s'),
            );
        }else{
            return false;
        }
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $data);

        return ($this->db->affected_rows()==1)?true:false;
    }

    function getDivisions($systemid){
        $this->db->where('scsystemid',$systemid);
        $this->db->order_by('order','ASC');
        $query=$this->db->get($this->table_divs);
        if ($query->num_rows() > 0){
            return $query->result_array();
        }
        return array();
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
    //TODO en estas funciones verificar primero que el sistema este en diseño sino return false; prevenir perros
    function addCicle($systemid,$ciclename,$cicleabbr){
        $this->db->where('scsystemid',$systemid);
        $this->db->order_by('order','DESC');
        $query=$this->db->get($this->table_cicles);

        $order=($query->num_rows() > 0)?$query->row()->order+1:1;

        $data = array(
                'scsystemid' =>$systemid,
                'name' => $ciclename,
                'abbr' => $cicleabbr,
                'order' => $order,
        );

        if($this->db->insert($this->table_cicles, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateCicle($cicleid,$ciclename,$cicleabbr,$cicleorder){

        $data = array(
                'name' => $ciclename,
                'abbr' => $cicleabbr,
                'order'=> $cicleorder,
        );

        $this->db->where('id', $cicleid);
        if($this->db->update($this->table_cicles, $data)){
            return $cicleid;
        }
        return false;
    }
    function delCicle($cicleid,$systemid){
        $this->db->delete($this->table_cicles, array('id' => $cicleid,'scsystemid'=>$systemid));
        return ($this->db->affected_rows()==1?true:false);
    }
    function addDivision($systemid,$divname){
        $this->db->where('scsystemid',$systemid);
        $this->db->order_by('order','DESC');
        $query=$this->db->get($this->table_divs);

        $order=($query->num_rows() > 0)?$query->row()->order+1:1;

        $data = array(
                'scsystemid' =>$systemid,
                'name' => $divname,
                'order' => $order,
        );

        if($this->db->insert($this->table_divs, $data)){
            return $this->db->insert_id();
        }
        return false;
    }
    function updateDivision($divid,$divname,$divorder){
        $data = array(
                'name' => $divname,
                'order'=> $divorder,
        );

        $this->db->where('id', $divid);
        if($this->db->update($this->table_divs, $data)){
            return $divid;
        }
        return false;
    }
    function delDivision($divid,$systemid){
        $this->db->delete($this->table_divs, array('id' => $divid,'scsystemid'=>$systemid));
        return ($this->db->affected_rows()==1?true:false);
    }
}