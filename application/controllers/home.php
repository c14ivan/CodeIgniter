<?php
/**

 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('tank_auth');
    }
    public function index()
    {
        $this->twig->display('home/home');
    }
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */