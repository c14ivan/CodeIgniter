<?php
class Lblibrary extends CI_Model{
    private $tablecats='lb_category';
    
    public function getCategories(){
        $query=$this->db->get($this->tablecats);
        
        $response=array();
        foreach ($query->result_array() as $cat){
            if($cat['catparent']>0){
                if(!isset($response[$cat['catparent']])){
                    $response[$cat['catparent']]=array('id'=>0,'name'=>'','childrens'=>array());
                }
                $response[$cat['catparent']]['childrens'][$cat['id']]=array('id'=>$cat['id'],'name'=>$cat['name'],'ident'=>$cat['catid']);
            }else{
                $response[$cat['id']]=array('id'=>$cat['id'],'name'=>$cat['name'],'ident'=>$cat['catid'],'childrens'=>array());
            }
        }
        return $response;
    }
    
}