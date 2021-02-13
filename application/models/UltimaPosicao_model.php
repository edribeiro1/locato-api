<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UltimaPosicao_model extends MY_Model
{
    public function get()
    {
        $this->db->select('
            ult_pos_id, vei_descricao, vei_placa, ult_pos_velocidade, ult_pos_ignicao, ult_pos_status_gps, gru_vei_descricao, 
            CONCAT(ras_numero_serie," - ", pro_fabricante, " - ", pro_descricao) as ras_numero_serie, 
            ST_Y(ult_pos_localizacao) as lat, ST_X(ult_pos_localizacao) as lng, 
            date_format(date_add(ult_pos_data_gps, interval -3 hour), "%d/%m/%Y %H:%i:%s") ult_pos_data_gps, 
            date_format(date_add(ult_pos_data_servidor, interval -3 hour), "%d/%m/%Y %H:%i:%s") ult_pos_data_servidor');
        $this->db->join('rastreador', 'ult_pos_id_rastreador = ras_id');
        $this->db->join('produto', 'ras_id_produto = pro_id');
        $this->db->join('veiculo', 'ult_pos_id_veiculo = vei_id');
        $this->db->join('grupo_veiculo', 'vei_id_grupo = gru_vei_id');
        if ($this->idFilial) {
            $this->db->where('ins_id_filial', $this->idFilial);
        }
        $this->db->where('ult_pos_id_empresa', $this->idEmpresa);
        $this->db->order_by('ult_pos_data_gps DESC');
        $dados = $this->db->get('ultima_posicao')->result_array();

        send(200, $dados);
    }
}
