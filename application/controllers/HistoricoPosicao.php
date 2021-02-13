<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HistoricoPosicao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('historicoPosicao_model');
    }

    public function gerar()
    {
        $this->historicoPosicao_model->gerar();
    }
}
