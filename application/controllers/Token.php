<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Token extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('token_model');
    }

    public function index()
    {
        if ($this->input->method() == 'post') {
            $post = getContents();

            if ($post) {
                $this->token_model->token($post);
            } else {
                send(400, null, "Sem Parâmetros");
            }
        } else {
            send(400, null, "Método não disponível");
        }
    }

    public function refresh()
    {
        if ($this->input->method() == 'post') {
            $post = getContents();

            if ($post) {
                $this->token_model->refreshToken($post);
            } else {
                send(400, null, "Sem Parâmetros");
            }
        
        } else {
            send(400, null, "Método não disponível");
        }
    }
}
