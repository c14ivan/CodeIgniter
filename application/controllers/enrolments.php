<?php
/**

*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Enrolments extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        $this->lang->load('school');
        $this->config->load('school');
        $this->lang->load('form_validation');

    }
}