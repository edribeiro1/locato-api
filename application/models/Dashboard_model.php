<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends MY_Model
{
    public function resumo()
    {

        $condFilialDocumento = "";
        $condFilialManutencao = "";
        $condFilialLocacao = "";
        if ($this->idFilial) {
            $condFilialDocumento = "AND doc_id_filial = $this->idFilial ";
            $condFilialManutencao = "AND man_id_filial = $this->idFilial ";
            $condFilialLocacao = "AND loc_id_filial = $this->idFilial ";
        }
        $result = $this->db->query("
            SELECT 
                (
                    SELECT COUNT(*)
                    FROM documento
                    WHERE doc_data_vencimento < NOW()
                    AND doc_deletado = 0
                    AND doc_status = 'Pendente'
                    AND doc_id_empresa = $this->idEmpresa
                    $condFilialDocumento
                ) AS documentos_vencidos,
                (
                    SELECT COUNT(*)
                    FROM documento
                    WHERE doc_data_vencimento < DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
                    AND doc_data_vencimento > NOW()
                    AND doc_deletado = 0
                    AND doc_status = 'Pendente'
                    AND doc_id_empresa = $this->idEmpresa
                    $condFilialDocumento
                ) as documentos_a_vencer,
                (
                    SELECT COUNT(*)
                    FROM manutencao
                    WHERE man_data_vencimento < NOW()
                    AND man_deletado = 0
                    AND man_status = 'Pendente'
                    AND man_id_empresa = $this->idEmpresa
                    $condFilialManutencao
                ) AS manutencoes_vencidas,
                (
                    SELECT COUNT(*)
                    FROM manutencao
                    WHERE man_data_vencimento < DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
                    AND man_data_vencimento > NOW()
                    AND man_deletado = 0
                    AND man_status = 'Pendente'
                    AND man_id_empresa = $this->idEmpresa
                    $condFilialManutencao
                ) as manutencoes_a_vencer,
                (
                    SELECT COUNT(*)  
                    FROM locacao
                    WHERE loc_data_devolucao_prevista > DATE_SUB(NOW(), INTERVAL 1 DAY)
                    AND loc_data_devolucao IS NOT NULL
                    AND loc_deletado = 0
                    AND loc_id_empresa = $this->idEmpresa
                    $condFilialLocacao
                ) as devolvidos,
                (
                    SELECT COUNT(*)  
                    FROM locacao
                    WHERE loc_data_devolucao_prevista < DATE_ADD(NOW(), INTERVAL 1 DAY)
                    AND loc_data_locacao IS NOT NULL
                    AND loc_data_devolucao IS NULL
                    AND loc_deletado = 0
                    AND loc_id_empresa = $this->idEmpresa
                    $condFilialLocacao
                ) as nao_devolvidos,
                (
                    SELECT COUNT(*)  
                    FROM locacao
                    WHERE loc_data_locacao_agendada > DATE_SUB(NOW(), INTERVAL 1 DAY)
                    AND loc_data_locacao_agendada < DATE_ADD(NOW(), INTERVAL 3 DAY)
                    AND loc_data_locacao is null
                    AND loc_deletado = 0
                    AND loc_id_empresa = $this->idEmpresa
                    $condFilialLocacao
                ) as locacoes_agendadas,
                (
                    SELECT COUNT(*)  
                    FROM locacao
                    WHERE loc_data_locacao is not null
                    AND loc_data_devolucao is null
                    AND loc_deletado = 0
                    AND loc_id_empresa = $this->idEmpresa
                    $condFilialLocacao
                ) as locacoes_fidelizadas
        ");



        if ($result->num_rows()) {
            send(200, $result->row_array());
        }

        send(400, null, 'Erro ao buscar resumo!');
    }


    public function agenda()
    {

        $retorno = [
            'documento' => [],
            'manutencao' => [],
            'devolucao' => [],
            'locacao' => []
        ];

        $this->db->select('doc_descricao, doc_data_vencimento');
        $this->db->where('doc_deletado', 0);
        $this->db->where('doc_status', 'Pendente');
        $this->db->where('doc_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('doc_id_filial', $this->idFilial);
        }
        $resultDocumento = $this->db->get('documento');

        if ($resultDocumento->num_rows()) {
            $retorno['documento'] = $resultDocumento->result_array();
        }

        $this->db->select('man_descricao, man_data_vencimento');
        $this->db->where('man_deletado', 0);
        $this->db->where('man_status', 'Pendente');
        $this->db->where('man_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('man_id_filial', $this->idFilial);
        }
        $resultManutencao = $this->db->get('manutencao');

        if ($resultManutencao->num_rows()) {
            $retorno['manutencao'] = $resultManutencao->result_array();
        }

        //  DEVOLUCAO >>>>
        $this->db->select('lct_nome, lct_celular_principal, loc_data_devolucao_prevista');
        $this->db->join('locatario', 'lct_id = loc_id_locatario');
        $this->db->where('loc_deletado', 0);
        $this->db->where('loc_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('loc_id_filial', $this->idFilial);
        }
        $this->db->where('loc_data_devolucao_prevista IS NOT NULL', null, false);
        $this->db->where('loc_data_locacao IS NOT NULL', null, false);
        $this->db->where('loc_data_devolucao IS NULL', null, false);

        $resultDevolucao = $this->db->get('locacao');

        if ($resultDevolucao->num_rows()) {
            $retorno['devolucao'] = $resultDevolucao->result_array();
        }
        //  DEVOLUCAO <<<<

        //  LOCACAO >>>>
        $this->db->select('lct_nome, lct_celular_principal, loc_data_locacao_agendada');
        $this->db->join('locatario', 'lct_id = loc_id_locatario');
        $this->db->where('loc_deletado', 0);
        $this->db->where('loc_id_empresa', $this->idEmpresa);
        if ($this->idFilial) {
            $this->db->where('loc_id_filial', $this->idFilial);
        }
        $this->db->where('loc_data_locacao_agendada IS NOT NULL', null, false);
        $this->db->where('loc_data_locacao IS NULL', null, false);
        $this->db->where('loc_data_devolucao IS NULL', null, false);

        $resultLocacao = $this->db->get('locacao');

        if ($resultLocacao->num_rows()) {
            $retorno['locacao'] = $resultLocacao->result_array();
        }
        //  LOCACAO <<<<
    
        send(200, $retorno);
    }
}