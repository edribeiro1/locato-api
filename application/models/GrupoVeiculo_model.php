<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GrupoVeiculo_model extends MY_Model
{

    public function get($id = false)
    {

        if ($id) {
            $this->db->where('gru_vei_id', $id);
            $this->db->where('gru_vei_deletado', 0);
            $this->db->where('gru_vei_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('gru_vei_id_filial', $this->idFilial);
            }
            $result = $this->db->get('grupo_veiculo');
    
            if ($result->num_rows()) {
                send(200, ['data' => $result->row_array()]);
            }
        }
        send(400);
    }

    public function lista()
    {   
        $params = getContents();

        $this->db->where('gru_vei_deletado', 0);
        $this->db->where('gru_vei_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('gru_vei_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('grupo_veiculo');

        if ($total) {

            $this->db->select('gru_vei_id, gru_vei_descricao, fil_nome_fantasia');
            $this->db->join('filial', 'fil_id = gru_vei_id_filial', 'left');
            $this->db->where('gru_vei_deletado', 0);
            $this->db->where('gru_vei_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('gru_vei_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }

            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('grupo_veiculo');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    
    }
}
