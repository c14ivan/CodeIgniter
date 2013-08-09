<?php
class Library extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('library/Lblibrary');
        $this->lang->load('library');
        $this->lang->load('form_validation');
        $this->output->enable_profiler(TRUE);
    }
    public function index()
    {
        $this->twig->display('home/home');
    }
    public function admin(){
        $this->twig->display('library/categories',array('editorials'=>array_values($this->Lblibrary->getEditorials())));
    }
    public function adminbooks(){
        
    }
    public function addbook(){
        if(!$this->input->is_ajax_request()) redirect();
        
        $this->output->enable_profiler(FALSE);
        $post=$this->input->post();
        $editorialid=$this->Lblibrary->getEditorial($this->input->post('bookeditorial'),true);
        if($post['bookid']>0){
            
        }else{
            if($this->Lblibrary->validateBook($post['parentident'].'. '.$post['bookident'])){
                $post['bookid']=$this->Lblibrary->addBook($post['booktitle'],$post['libcatid'],$editorialid,$post['bookauthor'],$post['bookkeywords'],$post['bookyear'],
                    $post['parentident'].'. '.$post['bookident'],$post['bookedition']);
            }else{
                $post['error']=lang('book_othident');
            }
        }
        echo json_encode($post);
    }
    public function addcategory(){
        if(!$this->input->is_ajax_request()) redirect();
        
        $this->output->enable_profiler(FALSE);
        $post=$this->input->post();
        $catid=$this->Lblibrary->addcategorie($post['libname'],$post['libident'],$post['lbcatparent']);
        $post['catid']=$catid;
        echo json_encode($post);
    }
    public function loadcategories(){
        if(!$this->input->is_ajax_request()) redirect();
        
        $this->output->enable_profiler(FALSE);
        
        $response=$this->Lblibrary->getCategories();
        
        echo json_encode(array('cats'=>$response));
    }
    public function loadcategory(){
        if(!$this->input->is_ajax_request()) redirect();
        
        $this->output->enable_profiler(FALSE);
        
        $response=$this->Lblibrary->getCategory($this->input->post('cat'));
        
        echo json_encode(array('cat'=>$response));
    }
}