<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{


    public function getDados($tabela = null, $campoId = null, $campoFilial = null, $campoEmpresa = null, $campoDelete = null, $id = false)
    {
        if ($id) {
            $this->db->where($campoId, $id);
            $this->db->where($campoDelete, 0);
            $this->db->where($campoEmpresa, $this->idEmpresa);
            if ($this->idFilial) {
                $this->db->where($campoFilial, $this->idFilial);
            }
            $result = $this->db->get($tabela);
    
            if ($result->num_rows()) {
                send(200, ['data' => $result->row_array()]);
            }
        }
        send(400);
    }

    public function salvar($tabela = null, $campoId = null, $campoFilial = null, $campoEmpresa = null, $validacao = [])
    {

        $params = getContents();
        $resp = array();
        $validade = true;

        $params[$campoEmpresa] = $this->idEmpresa;

        if ($this->idFilial) {
            $params[$campoFilial] = $this->idFilial;
        }


        if (is_array($validacao) && count($validacao)) {
            foreach ($validacao as $key => $value) {
                if (!isset($params[$key]) || !$params[$key]) {
                    $resp['campos'][] = $key;
                    $validade = false;
                }
            }
        }

        if ($validade) {
            if (validarId($params, $campoId)) { //UPDATE
                $id = $params[$campoId];
                unset($params[$campoId]);

                foreach ($params as $key => $param) {
                    if ($param) {
                        $this->db->set($key, $param);
                    } else {
                        $this->db->set($key, null);
                    }
                }

                $this->db->limit(1);
                $this->db->where($campoId, $id);
                $this->db->update($tabela);

            } else { //INSERT
                foreach ($params as $key => $param) {
                    if (!$param) {
                        $params[$key] = null;
                    }
                }
                $this->db->insert($tabela, $params);
            }

            if ($this->db->affected_rows()) {
                send(200, null, 'Sucesso!');
            }
        }

        send(400, $resp);
    }


    public function deletar($tabela = null, $campoId = null, $campoFilial = null, $campoEmpresa = null, $campoDelete = null)
    {

        $params = getContents();
        $ids = $params['id'];

        if ($tabela && $campoId && $campoEmpresa && $campoFilial && $campoDelete && is_array($ids) && count($ids)) {
            if ($this->idFilial) {
                $this->db->where($campoFilial, $this->idFilial);
            }
            $this->db->where($campoEmpresa, $this->idEmpresa);
            $this->db->where_in($campoId, $ids);
            $this->db->limit(count($ids));
            $this->db->set($campoDelete, 1);
            $this->db->update($tabela);

            if ($this->db->affected_rows()) {
                send(200, null, 'Sucesso!');
            }
        }
        
        send(400);
    }
}
