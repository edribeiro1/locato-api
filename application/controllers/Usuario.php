<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuario extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('usuario_model');
    }

    public function dadosUsuarioLogado()
    {
       $this->usuario_model->dadosUsuarioLogado();
    }

    public function lista()
    {
       $this->usuario_model->lista();
    }

    public function index($id = false)
    {
        $this->usuario_model->getDados(
            'usuario',
            'usu_id', 
            'usu_id_filial',
            'usu_id_empresa',
            'usu_deletado',
            $id);
    }
   
    public function salvar()
    {
        $this->usuario_model->salvar(
            'usuario',
            'usu_id', 
            'usu_id_filial',
            'usu_id_empresa',
            ['usu_login' => 'string', 'usu_senha' => 'string', 'usu_nome' => 'string', 'usu_email' => 'string']
        );
    }

    public function deletar()
    {
        $this->usuario_model->deletar(
            'usuario',
            'usu_id', 
            'usu_id_filial',
            'usu_id_empresa',
            'usu_deletado'
        );
    }
}
