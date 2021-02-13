<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuario_model extends MY_Model
{
    public function dadosUsuarioLogado()
    {
        $this->db->select('usu_nome, fil_nome_fantasia, emp_nome_fantasia, usu_id_filial');
        $this->db->join('filial', 'usu_id_filial = fil_id', 'left');
        $this->db->join('empresa', 'usu_id_empresa = emp_id', 'inner');
        $this->db->where('usu_id', $this->idUsuario);
        $result = $this->db->get('usuario');

        if ($result->num_rows()) {
            send(200, $result->row_array());
        }   

        send(401, null, 'Usuário inválido');

    }

    public function lista()
    {   
        $params = getContents();

        $this->db->where('usu_deletado', 0);
        $this->db->where('usu_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('usu_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('usuario');

        if ($total) {

            $this->db->select('usu_id, usu_nome, usu_login, usu_email, fil_nome_fantasia');
            $this->db->join('filial', 'usu_id_filial = fil_id', 'left');
            $this->db->where('usu_deletado', 0);
            $this->db->where('usu_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('usu_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('usuario');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }
}
