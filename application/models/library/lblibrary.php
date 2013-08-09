<?php
class Lblibrary extends CI_Model{
    private $tablecats='lb_category';
    private $tableedits='lb_editorial';
    private $tablebooks='lb_book';
    
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
    public function getEditorials(){
        $query=$this->db->get($this->tableedits);
        $response=array();
        foreach($query->result_array() as $edit){
            $response[$edit['id']]=$edit['name'];
        }
        return $response;
    }
    public function getEditorial($editorialname,$forcreate=false){
        $this->db->where('name',$editorialname);
        $query=$this->db->get($this->tableedits);
        if($query->num_rows() > 0){
            return $query->row()->id;
        }elseif($forcreate){
            if($this->db->insert($this->tableedits, array('name'=>$editorialname))){
    			return  $this->db->insert_id();
    		}
        }
    	return false;
    }
    public function getCategory($catid){
        $this->db->select("{$this->tablecats}.id,{$this->tablecats}.catid,{$this->tablecats}.catparent,{$this->tablecats}.name,COUNT({$this->tablebooks}.id) as books");
        $this->db->join($this->tablebooks,"{$this->tablebooks}.categoryid={$this->tablecats}.id",'LEFT');
        $this->db->where("{$this->tablecats}.id",$catid);
        $query=$this->db->get($this->tablecats);
        $response=$query->row_array();
        
        if($response['catparent']>0){
            $this->db->where("{$this->tablecats}.id",$response['catparent']);
            $query=$this->db->get($this->tablecats);
            $responseparent=$query->row_array();
            
            if($responseparent['catparent']>0){
                $this->db->where("{$this->tablecats}.id",$responseparent['catparent']);
                $query=$this->db->get($this->tablecats);
                $responseparentb=$query->row_array();
            }
        }
        $idcat=array();
        if(isset($responseparentb)){
            $idcat[1]=$responseparentb['catid'];
        }
        if(isset($responseparent)){
            $ind=(isset($idcat[1]))?2:1;
            $idcat[$ind]=$responseparent['catid'];
        }
        $ind=(isset($idcat[2]))?3:2;
        $idcat[$ind]=$response['catid'];
        if(!isset($idcat[1])) $idcat[1]=0;
        if(!isset($idcat[2])) $idcat[2]=0;
        if(!isset($idcat[3])) $idcat[3]=0;
        
        $response['ident']=implode('',$idcat);
        
        return $response;
    }

    public function validateBook($ident){
        $this->db->where('ident',$ident);
        $query=$this->db->get($this->tablebooks);
        return ($query->num_rows()>0)?false:true;
    }
    public function addBook($title,$catid,$editorial,$author,$keywords,$year,$ident,$edition){
        $data=array(
                'ident'=>$ident,
                'title'=>$title,
                'editorialid'=>$editorial,
                'author'=>$author,
                'keywords'=>$keywords,
                'year'=>$year,
                'edition'=>$edition,
                'categoryid'=>$catid,
                'creator'=>0,
                'timecreated'=>date('Y-m-d H:i:s')
        );
        if($this->db->insert($this->tablebooks, $data)){
            return  $this->db->insert_id();
        }
    }
    public function getLastCreated(){
        $this->db->select("{$this->tablebooks}.id,{$this->tablebooks}.ident,{$this->tablebooks}.title,{$this->tablebooks}.author,
            {$this->tablebooks}.keywords,{$this->tablebooks}.year,{$this->tablebooks}.edition,{$this->tablebooks}.timecreated,{$this->tableedits}.name as editorial");
        $this->db->join($this->tableedits,"{$this->tablebooks}.editorialid={$this->tableedits}.id");
        $this->db->order_by("{$this->tablebooks}.timecreated",'DESC');
        $query=$this->db->get($this->tablebooks,100);
        return $query->result_array();
    }
}