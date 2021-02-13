<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produto_model extends MY_Model
{
    public function lista()
    {   
        $params = getContents();

        $this->db->where('pro_ativo', 1);
        $total = $this->db->count_all_results('produto');

        if ($total) {

            $this->db->select('pro_id, pro_descricao, pro_fabricante');
            $this->db->where('pro_ativo', 1);
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('produto');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }
}
