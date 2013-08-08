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
    }
    public function index()
    {
        $this->twig->display('home/home');
    }
    public function admin(){
        $this->twig->display('library/categories');
    }
    public function adminbooks(){
        
    }
    
    public function loadcategories(){
        if(!$this->input->is_ajax_request()) redirect();
        
        $this->output->enable_profiler(FALSE);
        
        $response=$this->Lblibrary->getCategories();
        
        echo json_encode(array('cats'=>$response));
    }
}