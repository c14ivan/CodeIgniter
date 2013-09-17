<?php
class Locations extends CI_Model
{
    private $table_dept          = 'loc_dept';            // departments
    private $table_cities        = 'loc_cities';          // cities

    function __construct()
    {
        parent::__construct();
    }
    function getCities($country,$justarray=false) {
        $this->db->select("{$this->table_dept}.iddept,{$this->table_dept}.name deptname,idcity,{$this->table_cities}.name");
        $this->db->join($this->table_dept,"{$this->table_cities}.iddept={$this->table_dept}.iddept");
        $query=$this->db->get($this->table_cities);
        $arrCities=$query->result_array();
        $array = array();
        foreach ($arrCities as $registro) {
            if($justarray){
                $array[$registro['idcity']]=array('name'=>$registro['name'],'dept'=>$registro['deptname']);
            }else{
                if(!isset($array[$registro['iddept']])){
                    $array[$registro['iddept']]=array('name'=>$registro['deptname'],'options'=>array());
                }
                $array[$registro['iddept']]['options'][$registro['idcity']] = $registro['name'];
            }
        }
        return $array;
    }
    function load($datafolder){
        $sub = array_diff(scandir($datafolder), array('..', '.'));
        $dept = new stdClass();
        $ciudad = new stdClass();
        $fila = 0;
        foreach ($sub as $arch) {
            if (($gestor = fopen($datafolder . '/' . $arch, "r")) !== FALSE) {
                $dept->country = explode('.', $arch);
                $dept->country = $dept->country[0];
                while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                    $numero = count($datos);
                    $dept->nombre = utf8_encode($datos[0]);
                    $deptid = $this->registerDept($dept);
                    if (intval($deptid) > 0) {
                        $ciudad->nombre = utf8_encode($datos[1]);
                        $ciudad->iddept = $deptid;
                        $this->registerCity($ciudad);
                    }
                    $fila++;
                }
                fclose($gestor);
            }
        }
    }
    
    private function registerDept($data) {
        $this->db->where('name',$data->nombre);
        $this->db->where('country',$data->country);
        $queryprev=$this->db->get($this->table_dept);
        if ($queryprev->num_rows()>0) {
            $prev=$queryprev->row();
            return $prev->iddept;
        } else {
            $newDept = array(
                    'name'=>$data->nombre,
                    'country'=>$data->country
            );
            if($this->db->insert($this->table_dept, $newDept)){
                return $this->db->insert_id();
            }
            return false;
        }
    } 
    private function registerCity($data) {
        $this->db->where('name',$data->nombre);
        $this->db->where('iddept',$data->iddept);
        $queryprev=$this->db->get($this->table_cities);
        if ($queryprev->num_rows()>0) {
            return false;
        } else {
            $newCity = array(
                    'name'=>$data->nombre,
                    'iddept'=>$data->iddept
            );
            if($this->db->insert($this->table_cities, $newCity)){
                return $this->db->insert_id();
            }
            return false;
        }
    }
}