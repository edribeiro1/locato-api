<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Filial_model extends MY_Model
{

    public function lista($idFilial = null)
    {   
        if ($idFilial) {
            $this->db->where('fil_id_empresa', $this->idEmpresa);
            $this->db->where('fil_id', $idFilial);
            $this->db->where('fil_deletado', 0);
            $result = $this->db->get('filial');
            if ($result->num_rows()) {
                send(200, ['dados' => $result->row_array()]);
            }
            send(204);
        } else {
            $params = getContents();

            $this->db->where('fil_deletado', 0);
            $this->db->where('fil_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('fil_id', $this->idFilial);
            }
            $total = $this->db->count_all_results('filial');

            if ($total) {

                $this->db->select('fil_id, fil_nome_fantasia, fil_razao_social');
                $this->db->where('fil_id_empresa', $this->idEmpresa);
                $this->db->where('fil_deletado', 0);
                if ($this->idFilial) {
                    $this->db->where('fil_id_filial', $this->idFilial);
                }
                if(isset($params['sort']) && isset($params['order'])) {
                    $this->db->order_by($params['sort'], $params['order']);
                }
                if (isset($params['limit']) && isset($params['offset'])) {
                    $this->db->limit($params['limit'], $params['offset']);
                }
                $result = $this->db->get('filial');
    
                if ($result->num_rows()) {
                    send(200, ['total' => $total, 'rows' => $result->result_array()]);
                }
            }
            
            send(200, ['total' => 0, 'rows' => []]);
        }
    }
}
