<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rastreador_model extends MY_Model
{

    public function rastreadoresDisponiveis($id)
    {   

        $this->db->select('ras_id, ras_numero_serie');
        $this->db->join('instalacao', '(ins_id_rastreador = ras_id AND ins_deletado = 0)', 'left');
        $this->db->where('ras_deletado', 0);
        $this->db->where('ras_id_empresa', $this->idEmpresa);
        $this->db->where('ins_id IS NULL', null, false);

        if ($this->idFilial) {
            $this->db->where('ras_id_filial', $this->idFilial);
        }

        if (validarId($id)) {
            $this->db->or_where('ins_id', $id);
        }
        $result = $this->db->get('rastreador');

        if ($result->num_rows()) {
            send(200, ['rows' => $result->result_array()]);
        }
        send(400);
    }

    public function lista()
    {   
        $params = getContents();

        $this->db->where('ras_deletado', 0);
        $this->db->where('ras_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('ras_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('rastreador');

        if ($total) {

            $this->db->select('ras_id, ras_numero_serie, ras_numero_chip, fil_nome_fantasia, pro_descricao', false);
            $this->db->join('filial', 'ras_id_filial = fil_id', 'inner');
            $this->db->join('produto', 'ras_id_produto = pro_id', 'inner');
            $this->db->where('ras_deletado', 0);
            $this->db->where('ras_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('ras_id_filial', $this->idFilial);
            }
            if(isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('rastreador');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }
}
