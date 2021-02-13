<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Instalacao_model extends MY_Model
{
    public function lista()
    {   
        $params = getContents();

        $this->db->where('ins_deletado', 0);
        $this->db->where('ins_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('ins_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('instalacao');

        if ($total) {

            $this->db->select('ins_id, ras_numero_serie, vei_descricao, fil_nome_fantasia');
            $this->db->join('filial', 'ins_id_filial = fil_id', 'left');
            $this->db->join('rastreador', 'ins_id_rastreador = ras_id', 'inner');
            $this->db->join('veiculo', 'ins_id_veiculo = vei_id', 'inner');
            $this->db->where('ins_deletado', 0);
            $this->db->where('ins_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('ins_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('instalacao');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }
}
