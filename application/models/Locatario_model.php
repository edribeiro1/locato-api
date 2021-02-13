<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locatario_model extends MY_Model
{

    public function locatariosDisponiveis($id)
    {

        $this->db->select('lct_id, lct_nome,');
        $this->db->join('locacao', '(loc_id_locatario = lct_id AND loc_data_devolucao IS NULL AND loc_deletado = 0)', 'left');
        $this->db->where('lct_deletado', 0);
        $this->db->where('lct_id_empresa', $this->idEmpresa);
        $this->db->where('loc_id IS NULL', null, false);

        if ($this->idFilial) {
            $this->db->where('lct_id_filial', $this->idFilial);
        }

        if (validarId($id)) {
            $this->db->or_where('loc_id', (int)$id);
        }

       
        $result = $this->db->get('locatario');

        if ($result->num_rows()) {
            send(200, ['rows' => $result->result_array()]);
        }
        send(400);
    }

    public function lista()
    {
        $params = getContents();

        $this->db->where('lct_deletado', 0);
        $this->db->where('lct_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('lct_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('locatario');

        if ($total) {
            $this->db->select('lct_id, lct_nome, lct_telefone, lct_celular_principal, lct_email, fil_nome_fantasia');
            $this->db->join('filial', 'lct_id_filial = fil_id', 'inner');
            $this->db->where('lct_deletado', 0);
            $this->db->where('lct_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('lct_id_filial', $this->idFilial);
            }
            if (isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            }
            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('locatario');
    
            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
          
        send(200, ['total' => 0, 'rows' => []]);
    }
}
