<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
    }

    public function index()
    {
        $this->dashboard_model->resumo();
    }

    public function agenda()
    {
        $this->dashboard_model->agenda();
    }
}
