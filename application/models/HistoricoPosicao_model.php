<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HistoricoPosicao_model extends MY_Model
{
    public function gerar()
    {
        $params = getContents();

        $arrayResult = array();

        $dataInicial = DateTime::createFromFormat('d/m/Y H:i:s', $params['data_inicial'], new DatetimeZone('America/Sao_Paulo'));
        $dataInicial->setTimezone(new DateTimeZone('UTC'));
        
        $dataFinal = DateTime::createFromFormat('d/m/Y H:i:s', $params['data_final'], new DatetimeZone('America/Sao_Paulo'));
        $dataFinal->setTimezone(new DateTimeZone('UTC'));
  
        if ((int)$params['id_veiculo'] > 0) {
            $this->db->select('
                his_pos_id, vei_descricao, vei_placa, his_pos_velocidade, his_pos_ignicao, his_pos_status_gps, gru_vei_descricao, 
                CONCAT(ras_numero_serie," - ", pro_fabricante, " - ", pro_descricao) as ras_numero_serie, 
                ST_Y(his_pos_localizacao) as lat, ST_X(his_pos_localizacao) as lng, 
                date_format(date_add(his_pos_data_gps, interval -3 hour), "%d/%m/%Y %H:%i:%s") his_pos_data_gps, 
                date_format(date_add(his_pos_data_servidor, interval -3 hour), "%d/%m/%Y %H:%i:%s") his_pos_data_servidor');
            $this->db->join('rastreador', 'his_pos_id_rastreador = ras_id');
            $this->db->join('produto', 'ras_id_produto = pro_id');
            $this->db->join('veiculo', 'his_pos_id_veiculo = vei_id');
            $this->db->join('grupo_veiculo', 'vei_id_grupo = gru_vei_id');
            if ($this->idFilial) {
                $this->db->where('ins_id_filial', $this->idFilial);
            }
            $this->db->where('his_pos_id_empresa', $this->idEmpresa);
            $this->db->where('his_pos_id_veiculo', $params['id_veiculo']);
            $this->db->where('his_pos_data_gps BETWEEN "'. $dataInicial->format('Y-m-d H:i:s') .'" AND "'. $dataFinal->format('Y-m-d H:i:s') .'"');
            $this->db->order_by('his_pos_data_gps DESC');
            $dados = $this->db->get('historico_posicao')->result_array();

            if (is_array($dados) && count($dados)) {
                send(200, $dados);
            } else {
                send(400, null, 'Nenhuma informação encontrada no período!');
            }
        }

        send(400);
    }
}
