<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Documento_model extends MY_Model
{
    public function lista()
    {   
        $params = getContents();

        $this->db->where('doc_deletado', 0);
        $this->db->where('doc_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('doc_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('documento');

        if ($total) {

            $this->db->select('doc_id, doc_descricao, doc_data_vencimento, fil_nome_fantasia, vei_descricao');
            $this->db->join('filial', 'doc_id_filial = fil_id', 'inner');
            $this->db->join('veiculo', 'doc_id_veiculo = vei_id', 'inner');
            $this->db->where('doc_deletado', 0);
            $this->db->where('doc_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('doc_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('documento');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
        
    }
}
