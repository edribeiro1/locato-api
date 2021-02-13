<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->input->method() == 'options') {
            send();
        }
        
        $this->idUsuario = null;
        $this->idFilial = null;
        $this->idEmpresa = null;

        $this->load->model('token_model', 'token');
        $requiredScope = (isset($GLOBALS['requiredScope']) && $GLOBALS['requiredScope'] ? implode(' ', $GLOBALS['requiredScope'])  : null);
        $this->tokenData = $this->token->validateToken($requiredScope);

        if (validarId($this->tokenData, 'id_usuario')) {
            $this->idUsuario = (int)$this->tokenData['id_usuario'];
        }

        if (validarId($this->tokenData, 'id_filial')) {
            $this->idFilial = (int)$this->tokenData['id_filial'];
        }

        if (validarId($this->tokenData, 'id_empresa')) {
            $this->idEmpresa = (int)$this->tokenData['id_empresa'];
        }

        $availableMethods = (isset($GLOBALS['availableMethods']) && $GLOBALS['availableMethods'] ? $GLOBALS['availableMethods'] : array('get', 'post', 'put', 'delete'));
        if (!in_array($this->input->method(), $availableMethods)) {
            send(400, null, 'Método não disponível');
        }
    }
}
