<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Veiculo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('veiculo_model');
    }

    public function index($id = false)
    {
        $this->veiculo_model->getDados(
            'veiculo',
            'vei_id', 
            'vei_id_filial',
            'vei_id_empresa',
            'vei_deletado',
            $id);
    }

    public function veiculosDisponiveis($id)
    {
        $this->veiculo_model->veiculosDisponiveis($id);
    }

    public function veiculosDisponiveisLocacao($id)
    {
        $this->veiculo_model->veiculosDisponiveisLocacao($id);
    }

    public function lista($id = false)
    {
       $this->veiculo_model->lista($id);
    }

    public function salvar()
    {
        $this->veiculo_model->salvar(
            'veiculo',
            'vei_id', 
            'vei_id_filial',
            'vei_id_empresa',
            [ 'vei_descricao' => 'string', 'vei_placa' => 'string', 'vei_id_filial' => 'int', 'vei_id_grupo' => 'int']
        );
    }

    public function deletar()
    {
        $this->veiculo_model->deletar(
            'veiculo',
            'vei_id', 
            'vei_id_filial',
            'vei_id_empresa',
            'vei_deletado'
        );
    }
}
