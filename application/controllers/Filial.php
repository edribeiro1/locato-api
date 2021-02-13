<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Filial extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('filial_model');
    }

    public function lista()
    {
       $this->filial_model->lista();
    }

    public function index($id = false)
    {
        $this->filial_model->getDados(
            'filial',
            'fil_id', 
            'fil_id',
            'fil_id_empresa',
            'fil_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->filial_model->salvar(
            'filial',
            'fil_id', 
            'fil_id',
            'fil_id_empresa',
            [ 'fil_nome_fantasia' => 'string']
        );
    }

    public function deletar()
    {
        $this->filial_model->deletar(
            'filial',
            'fil_id', 
            'fil_id',
            'fil_id_empresa',
            'fil_deletado'
        );
    }
}
