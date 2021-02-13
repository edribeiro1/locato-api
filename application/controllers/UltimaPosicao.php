<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UltimaPosicao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ultimaPosicao_model');
    }

    public function index()
    {
        $this->ultimaPosicao_model->get();
    }
}
