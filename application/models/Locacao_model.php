<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locacao_model extends MY_Model
{
    public function lista()
    {   
        $params = getContents();

        $this->db->where('loc_deletado', 0);
        $this->db->where('loc_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('loc_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('locacao');

        if ($total) {

            $this->db->select('loc_id, DATE_FORMAT(loc_data_locacao_agendada, "%d/%m/%Y %H:%i:%s") AS loc_data_locacao_agendada, DATE_FORMAT(loc_data_locacao, "%d/%m/%Y %H:%i:%s") AS loc_data_locacao , DATE_FORMAT(loc_data_devolucao_prevista, "%d/%m/%Y %H:%i:%s")  AS loc_data_devolucao_prevista, DATE_FORMAT(loc_data_devolucao, "%d/%m/%Y %H:%i:%s")  AS loc_data_devolucao, lct_nome, vei_descricao, fil_nome_fantasia');
            $this->db->join('filial', 'loc_id_filial = fil_id', 'inner');
            $this->db->join('locatario', 'lct_id = loc_id_locatario', 'inner');
            $this->db->join('veiculo', 'loc_id_veiculo = vei_id', 'inner');
            $this->db->where('loc_deletado', 0);
            $this->db->where('loc_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('loc_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            } else {
                $this->db->order_by('loc_data_locacao_agendada', 'DESC');
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('locacao');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }
}
