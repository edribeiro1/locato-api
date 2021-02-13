<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrupoVeiculo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('grupoVeiculo_model');
    }

    public function lista()
    {
       $this->grupoVeiculo_model->lista();
    }

    public function index($id = false)
    {
        $this->grupoVeiculo_model->getDados(
            'grupo_veiculo',
            'gru_vei_id', 
            'gru_vei_id_filial',
            'gru_vei_id_empresa',
            'gru_vei_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->grupoVeiculo_model->salvar(
            'grupo_veiculo',
            'gru_vei_id', 
            'gru_vei_id_filial',
            'gru_vei_id_empresa',
            [ 'gru_vei_descricao' => 'string']
        );
    }

    public function deletar()
    {
        $this->grupoVeiculo_model->deletar(
            'grupo_veiculo',
            'gru_vei_id', 
            'gru_vei_id_filial',
            'gru_vei_id_empresa',
            'gru_vei_deletado'
        );
    }
}
