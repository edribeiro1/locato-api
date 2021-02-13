<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Veiculo_model extends MY_Model
{

    public function veiculosDisponiveis($id)
    {

        $this->db->select('vei_id, vei_descricao, vei_placa');
        $this->db->join('instalacao', '(ins_id_veiculo = vei_id AND ins_deletado = 0)', 'left');
        $this->db->where('vei_deletado', 0);
        $this->db->where('vei_id_empresa', $this->idEmpresa);
        $this->db->where('ins_id IS NULL', null, false);
        if ($this->idFilial) {
            $this->db->where('vei_id_filial', $this->idFilial);
        }

        if (validarId($id)) {
            $this->db->or_where('ins_id', $id);
        }
        $result = $this->db->get('veiculo');

        if ($result->num_rows()) {
            send(200, ['rows' => $result->result_array()]);
        }
        send(400);
    }

    public function veiculosDisponiveisLocacao($id)
    {

        $this->db->select('vei_id, vei_descricao, vei_placa');
        $this->db->join('locacao', '(loc_id_veiculo = vei_id AND loc_data_devolucao IS NULL AND loc_deletado = 0)', 'left');
        $this->db->where('vei_deletado', 0);
        $this->db->where('vei_id_empresa', $this->idEmpresa);
        $this->db->where('loc_id IS NULL', null, false);
        if ($this->idFilial) {
            $this->db->where('vei_id_filial', $this->idFilial);
        }

        if (validarId($id)) {
            $this->db->or_where('loc_id', (int)$id);
        }

       
        $result = $this->db->get('veiculo');

        if ($result->num_rows()) {
            send(200, ['rows' => $result->result_array()]);
        }
        send(400);
    }

    public function lista()
    {
        $params = getContents();

        $this->db->where('vei_deletado', 0);
        $this->db->where('vei_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('vei_id_filial', $this->idFilial);
        }
        $total = $this->db->count_all_results('veiculo');

        if ($total) {
            $this->db->select('vei_id, vei_descricao, vei_placa, gru_vei_descricao, vei_kilometragem, vei_tipo_combustivel, vei_cor, vei_modelo, 
            vei_ano_modelo, vei_fabricante, vei_ano_fabricacao, vei_chassi, vei_renavam, fil_nome_fantasia');
            $this->db->join('grupo_veiculo', 'gru_vei_id = vei_id_grupo', 'left');
            $this->db->join('filial', 'fil_id = vei_id_filial', 'left');
            $this->db->where('vei_deletado', 0);
            $this->db->where('vei_id_empresa', $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where('vei_id_filial', $this->idFilial);
            }
            if (isset($params['sort']) && isset($params['order'])) {
                $this->db->order_by($params['sort'], $params['order']);
            } else {
                $this->db->order_by('vei_id', 'DESC');
            }

            if (isset($params['limit']) && isset($params['offset'])) {
                $this->db->limit($params['limit'], $params['offset']);
            }
            $result = $this->db->get('veiculo');

            if ($result->num_rows()) {
                send(200, ['total' => $total, 'rows' => $result->result_array()]);
            }
        }
        
        send(200, ['total' => 0, 'rows' => []]);
    }

    // public function salvar($chaveId = null, $validacao = [])
    // {
    //     parent::salvar('vei_id', [
    //         'vei_descricao' => 'string',
    //         'vei_placa' => 'string'
    //     ]);
    // }
}
