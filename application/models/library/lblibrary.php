<?php
class Lblibrary extends CI_Model{
    private $tablecats='lb_category';
    
    public function getCategories(){
        $this->db->where('catparent',0);
        $query=$this->db->get($this->tablecats);
        $firstsumary=array();
        $response=array();
        foreach ($query->result_array() as $cat){
            $response[$cat['id']]=array('id'=>$cat['id'],'name'=>$cat['name'],'ident'=>$cat['catid'],'childrens'=>array());
            $firstsumary[$cat['id']]=$cat['id'];
        }
        $this->db->where_in('catparent',$firstsumary);
        $query=$this->db->get($this->tablecats);
        foreach ($query->result_array() as $cat){
            $response[$cat['catparent']]['childrens'][$cat['id']]=array('id'=>$cat['id'],'name'=>$cat['name'],'ident'=>$cat['catid'],'childrens'=>array());
            $secondsumary[$cat['id']]=$cat['catparent'];
        }
        
        $this->db->where_in('catparent',array_keys($secondsumary));
        $query=$this->db->get($this->tablecats);
        foreach ($query->result_array() as $cat){
            $response[$secondsumary[$cat['catparent']]]['childrens'][$cat['catparent']]['childrens'][$cat['id']]=array('id'=>$cat['id'],'name'=>$cat['name'],'ident'=>$cat['catid'],'childrens'=>array());
        }
        return $response;
    }
    public function addcategorie($name,$ident,$parent){
        
        $data=array('catid'=>$ident,'catparent'=>$parent,'name'=>$name);
        
        $catid=false;
        if($this->db->insert($this->tablecats, $data)){
            $catid= $this->db->insert_id();
        }
        return $catid;
    }
}