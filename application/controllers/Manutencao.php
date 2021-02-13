<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manutencao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('manutencao_model');
    }

    public function lista()
    {
       $this->manutencao_model->lista();
    }

    public function index($id = false)
    {
        $this->manutencao_model->getDados(
            'manutencao',
            'man_id', 
            'man_id_filial',
            'man_id_empresa',
            'man_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->manutencao_model->salvar(
            'manutencao',
            'man_id', 
            'man_id_filial',
            'man_id_empresa',
            [ 'man_descricao' => 'string', 'man_id_veiculo' => 'int', 'man_id_filial' => 'int']
        );
    }

    public function deletar()
    {
        $this->manutencao_model->deletar(
            'manutencao',
            'man_id', 
            'man_id_filial',
            'man_id_empresa',
            'man_deletado'
        );
    }
}
