<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documento extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('documento_model');
    }

    public function lista()
    {
       $this->documento_model->lista();
    }

    public function index($id = false)
    {
        $this->documento_model->getDados(
            'documento',
            'doc_id', 
            'doc_id_filial',
            'doc_id_empresa',
            'doc_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->documento_model->salvar(
            'documento',
            'doc_id', 
            'doc_id_filial',
            'doc_id_empresa',
            [ 'doc_descricao' => 'string', 'doc_id_veiculo' => 'int', 'doc_id_filial' => 'int']
        );
    }

    public function deletar()
    {
        $this->documento_model->deletar(
            'documento',
            'doc_id', 
            'doc_id_filial',
            'doc_id_empresa',
            'doc_deletado'
        );
    }
}
