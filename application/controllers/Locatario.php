<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locatario extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('locatario_model');
    }

    public function lista()
    {
       $this->locatario_model->lista();
    }

    public function locatariosDisponiveis($id)
    {
        $this->locatario_model->locatariosDisponiveis($id);
    }


    public function index($id = false)
    {
        $this->locatario_model->getDados(
            'locatario',
            'lct_id', 
            'lct_id_filial',
            'lct_id_empresa',
            'lct_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->locatario_model->salvar(
            'locatario',
            'lct_id', 
            'lct_id_filial',
            'lct_id_empresa',
            [ 'lct_nome' => 'string', 'lct_celular_principal' => 'string', 'lct_id_filial' => 'int']
        );
    }

    public function deletar()
    {
        $this->locatario_model->deletar(
            'locatario',
            'lct_id', 
            'lct_id_filial',
            'lct_id_empresa',
            'lct_deletado'
        );
    }
}
