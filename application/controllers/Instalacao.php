<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Instalacao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('instalacao_model');
    }

    public function lista()
    {
       $this->instalacao_model->lista();
    }

    public function index($id = false)
    {
        $this->instalacao_model->getDados(
            'instalacao',
            'ins_id', 
            'ins_id_filial',
            'ins_id_empresa',
            'ins_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->instalacao_model->salvar(
            'instalacao',
            'ins_id', 
            'ins_id_filial',
            'ins_id_empresa',
            [ 'ins_id_rastreador' => 'int', 'ins_id_veiculo' => 'int']
        );
    }

    public function deletar()
    {
        $this->instalacao_model->deletar(
            'instalacao',
            'ins_id', 
            'ins_id_filial',
            'ins_id_empresa',
            'ins_deletado'
        );
    }
}
