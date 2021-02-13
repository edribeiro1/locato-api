<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notificacao_model extends MY_Model
{

    public function get()
    {

        $params = getContents();

        $this->db->where('not_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('not_id_filial', $this->idFilial);
        }
        $this->db->where('not_deletado', 0);
        $total = $this->db->count_all_results('notificacao');

        if ($total) {

            $this->db->where('not_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('not_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            } else {
                $this->db->order_by('not_lido', 'ASC');
            }

            if(isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }

            $this->db->where('not_deletado', 0);
            
            $result = $this->db->get('notificacao');
            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }

        send(200, ['total' => 0, 'rows' => []]);
    }

    public function contar()
    {
        if ($this->idFilial) {
            $this->db->where('not_id_filial', $this->idFilial);
        }
        $this->db->where('not_id_empresa', $this->idEmpresa);
        $this->db->where('not_lido', 0);
        $this->db->where('not_deletado', 0);
        send(200, ['total' => $this->db->count_all_results('notificacao')]);
    }

    public function deletarNotificacao()
    {
        $params = getContents();
        
        if (isset($params['ids']) && count($params['ids'])) {
            if ($this->idFilial) {
                $this->db->where('not_id_filial', $this->idFilial);
            }
            $this->db->where('not_id_empresa', $this->idEmpresa);
            $this->db->where_in('not_id', $params['ids']);
            $this->db->set('not_deletado', 1);
            $this->db->update('notificacao');

            if ($this->db->affected_rows()) {
                send(200, null, 'Notificação deletada com sucesso!');
            }
        } 
        send(400, null, 'Erro ao deletar a notificação!');
    }

    public function lido()
    {
        $params = getContents();

        if (isset($params['ids']) && count($params['ids']) && isset($params['lido'])) {
            if ($this->idFilial) {
                $this->db->where('not_id_filial', $this->idFilial);
            }
            $this->db->where('not_id_empresa', $this->idEmpresa);
            $this->db->where_in('not_id', $params['ids']);
            $this->db->set('not_lido', $params['lido']);
            $this->db->update('notificacao');

            if ($this->db->affected_rows()) {
                send(200, null, 'Sucesso');
            }
        }

        send(400, null, 'Erro!');
    }
}
