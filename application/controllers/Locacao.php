<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locacao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('locacao_model');
    }

    public function lista()
    {
       $this->locacao_model->lista();
    }


    public function index($id = false)
    {
        $this->locacao_model->getDados(
            'locacao',
            'loc_id', 
            'loc_id_filial',
            'loc_id_empresa',
            'loc_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->locacao_model->salvar(
            'locacao',
            'loc_id', 
            'loc_id_filial',
            'loc_id_empresa',
            [ 'loc_id_locatario' => 'int', 'loc_id_veiculo' => 'int', 'loc_id_filial' => 'int', 'loc_data_locacao_agendada' => 'date']
        );
    }

    public function deletar()
    {
        $this->locacao_model->deletar(
            'locacao',
            'loc_id', 
            'loc_id_filial',
            'loc_id_empresa',
            'loc_deletado'
        );
    }
}
