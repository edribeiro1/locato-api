<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produto extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('produto_model');
    }

    public function lista()
    {
       $this->produto_model->lista();
    }
}
