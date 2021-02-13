<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificacao_model');
    }

    public function index()
    {
        $this->notificacao_model->get();
    }

    public function contar()
    {
       $this->notificacao_model->contar();
    }

    public function deletar()
    {
        $this->notificacao_model->deletarNotificacao();
    }

    public function lido()
    {
        $this->notificacao_model->lido();
    }
}
