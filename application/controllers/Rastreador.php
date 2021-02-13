<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rastreador extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('rastreador_model');
    }

    public function lista()
    {
       $this->rastreador_model->lista();
    }

    public function rastreadoresDisponiveis($id)
    {
        $this->rastreador_model->rastreadoresDisponiveis($id);
    }

    public function index($id = false)
    {
        $this->rastreador_model->getDados(
            'rastreador',
            'ras_id', 
            'ras_id_filial',
            'ras_id_empresa',
            'ras_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->rastreador_model->salvar(
            'rastreador',
            'ras_id', 
            'ras_id_filial',
            'ras_id_empresa',
            [ 'ras_numero_serie' => 'string', 'ras_id_filial' => 'int', 'ras_id_produto' => 'int']
        );
    }

    public function deletar()
    {
        $this->rastreador_model->deletar(
            'rastreador',
            'ras_id', 
            'ras_id_filial',
            'ras_id_empresa',
            'ras_deletado'
        );
    }
}
