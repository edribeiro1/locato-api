<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manutencao_model extends MY_Model
{
    public function lista()
    {   
        $params = getContents();

        $this->db->where('man_deletado', 0);
        $this->db->where('man_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('man_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('manutencao');

        if ($total) {

            $this->db->select('man_id, man_descricao, man_observacao, man_kilometragem, DATE_FORMAT(man_data_vencimento, "%d/%m/%Y %H:%i:%s") as man_data_vencimento, fil_nome_fantasia, vei_descricao', false);
            $this->db->join('filial', 'man_id_filial = fil_id', 'inner');
            $this->db->join('veiculo', 'man_id_veiculo = vei_id', 'inner');
            $this->db->where('man_deletado', 0);
            $this->db->where('man_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('man_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('manutencao');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
       
    }
}
