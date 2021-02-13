<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('UTC');

class Token_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tokenLifetime = 3600;
        $this->refreshTokenLifetime = 2592000;
        $this->tokenType = 'bearer';
    }

    public function validateToken($requiredScope)
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            send(401, null, 'Ausência do Authorization');
        }

        $authorizationToken = $headers['Authorization'];

        if (strlen($authorizationToken) < 40) {
            send(401, null, 'Token inválido');
        }

        $authorizationToken = explode(' ', $authorizationToken);

        if (!$authorizationToken || !is_array($authorizationToken) || count($authorizationToken) != 2) {
            send(401, null, 'Token inválido');
        }

        if (strtolower($authorizationToken[0]) != $this->tokenType) {
            send(401, null, "Authorization is $this->tokenType");
        }

        if (strlen($authorizationToken[1]) != 40) {
            send(401, null, 'Token inválido');
        }

        $this->db->where('access_token', $authorizationToken[1]);
        $result = $this->db->get('oauth_tokens');

        if ($result->num_rows()) {
            $dataToken = $result->row_array();
            $expirationDate = strtotime($dataToken['expires']);

            if (time() > $expirationDate) {
                send(401, null, 'Token expirado');
            }

            return $dataToken;
        } else {
            send(401, null, 'Token inválido');
        }
    }

    public function token($params)
    {
        $clientData = $this->checkClient($params);

        switch ($params['grant_type']) {
            case 'user_credentials':
                $userData = $this->checkUser($params);
                $tokenData = $this->generateToken();

                $tokenData['expires_in'] = $this->tokenLifetime;
                $tokenData['token_type'] = $this->tokenType;
                $tokenData['scope'] = '';

                $this->setToken($tokenData, $clientData, $userData);
                $this->setRefreshToken($tokenData, $clientData, $userData);

                send(200, $tokenData);
                break;

            case 'client_credentials':
                break;

            case 'refresh_token':
                break;
        }

        send(401, null, 'Erro desconhecido');
    }

    public function refreshToken($params)
    {
        $clientData = $this->checkClient($params);
        $dataRefreshToken = $this->checkRefreshToken($params);

        if (!isset($dataRefreshToken['id_usuario']) || !is_numeric($dataRefreshToken['id_usuario'])) {
            send(400, null, 'Usuário inválido');
        }

        $userData = $this->getUser($dataRefreshToken['id_usuario']);

        $tokenData = $this->generateToken();

        $tokenData['expires_in'] = $this->tokenLifetime;
        $tokenData['token_type'] = $this->tokenType;
        $tokenData['scope'] = $dataRefreshToken['scope'];

        $this->invalidateTokens($dataRefreshToken['id_usuario'], $dataRefreshToken['client_id'], $dataRefreshToken['refresh_token']);

        $this->setToken($tokenData, $clientData, $userData);
        $this->setRefreshToken($tokenData, $clientData, $userData);

        send(200, $tokenData);

    }


    private function setToken($tokenData = [], $clientData = [], $userData = [])
    {

        $insertDataToken = [
            'access_token' => $tokenData['access_token'],
            'client_id' => $clientData['client_id'],
            'id_usuario' => $userData['usu_id'],
            'id_filial' => $userData['usu_id_filial'],
            'id_empresa' => $userData['usu_id_empresa'],
            'expires' => date('Y-m-d H:i:s', (time() + $this->tokenLifetime)),
            'scope' => $tokenData['scope']
        ];

        $this->db->insert('oauth_tokens', $insertDataToken);

        if ($this->db->affected_rows() == 0) {
            send(401, null, 'Erro ao inserir token');
        }
    }

    private function setRefreshToken($tokenData = [], $clientData = [], $userData = [])
    {

        $insertDataToken = [
            'refresh_token' => $tokenData['refresh_token'],
            'client_id' => $clientData['client_id'],
            'id_usuario' => $userData['usu_id'],
            'id_filial' => $userData['usu_id_filial'],
            'id_empresa' => $userData['usu_id_empresa'],
            'expires' => date('Y-m-d H:i:s', (time() + $this->refreshTokenLifetime)),
            'scope' => $tokenData['scope']
        ];
        $insertDataRefreshToken = [];

        $this->db->insert('oauth_refresh_tokens', $insertDataToken);

        if ($this->db->affected_rows() == 0) {
            send(401, null, 'Erro ao inserir refresh token');
        }
    }

    private function checkRefreshToken($params)
    {

        if ($params['grant_type'] != 'refresh_token') {
            send(401, null, 'grant_type inválido');
        }

        if (!isset($params['refresh_token']) || !$params['refresh_token']) {
            send(401, null, 'refresh_token ausente');
        }


        $this->db->where('refresh_token', $params['refresh_token']);
        $result = $this->db->get('oauth_refresh_tokens');
        
        if ($result->num_rows()) {
            $dataRefreshToken = $result->row_array();
            $expirationDate = strtotime($dataRefreshToken['expires']);

            if (time() > $expirationDate) {
                send(401, null, 'Token expirado');
            }

            return $dataRefreshToken;
        } else {
            send(401, null, 'Refresh token inválido');
        }

    }

    private function getUser($idUsuario)
    {
        if (!$idUsuario || !is_numeric($idUsuario)) {
            send(400, null, 'Usuário inválido');
        }

        $this->db->where('usu_id', $idUsuario);
        $this->db->where('usu_deletado', 0);
        $result = $this->db->get('usuario');
        if ($result->num_rows()) {
            return $result->row_array();
        }

        send(401, null, 'Usuário inválido ou sem permissão!');
    }

    private function checkUser($params)
    {
        if (!isset($params['username']) || !$params['username']) {
            send(401, null, 'Parâmetro username inválido!');
        }

        if (!isset($params['password']) || !$params['password']) {
            send(401, null, 'Parâmetro password inválido!');
        }

        $this->db->where('usu_login', $params['username']);
        $this->db->where('usu_senha', md5($params['password']));
        $this->db->where('usu_deletado', 0);
        $result = $this->db->get('usuario');
        if ($result->num_rows()) {
            return $result->row_array();
        }

        send(401, null, 'Usuário inválido ou sem permissão!');
    }

    private function checkClient($params)
    {
        $availableGrantTypes = ['refresh_token', 'client_credentials', 'user_credentials'];

        if (!isset($params['grant_type']) || !in_array($params['grant_type'], $availableGrantTypes)) {
            send(401, null, 'Parâmetro grant_type inválido!');
        }

        if (!isset($params['client_id']) || !$params['client_id']) {
            send(401, null, 'Parâmetro client_id inválido!');
        }

        if (!isset($params['client_secret']) || !$params['client_secret']) {
            send(401, null, 'Parâmetro client_secret inválido!');
        }

        $this->db->where('client_id', $params['client_id']);
        $this->db->where('client_secret', $params['client_secret']);
        $this->db->like('grant_types', $params['grant_type']);
        $result = $this->db->get('oauth_clients');

        if ($result->num_rows()) {
            return $result->row_array();
        }

        send(401, null, 'Client inválido ou sem permissão!');
    }

    private function generateToken()
    {
        $token = "";
        while (true) {
            $token = $this->generateHashToken();
            $this->db->select('1');
            $this->db->where('access_token', $token);
            $this->db->limit(1);
            $result = $this->db->get('oauth_tokens');
            if ($result->num_rows() == 0) {
                break;
            }
        }

        $refreshToken = "";
        while (true) {
            $refreshToken = $this->generateHashToken();
            $this->db->select('1');
            $this->db->where('refresh_token', $refreshToken);
            $this->db->limit(1);
            $result = $this->db->get('oauth_refresh_tokens');
            if ($result->num_rows() == 0) {
                break;
            }
        }

        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken
        ];
    }

    public function invalidateTokens($idUsuario, $clientId, $refreshToken = null)
    {
        $dateNow = date('Y-m-d H:i:s', time());

        if ($refreshToken) {
            $this->db->set('expires', $dateNow);
            $this->db->where('refresh_token', $refreshToken);
            $this->db->update('oauth_refresh_tokens');
        } else {
            $this->db->set('expires', $dateNow);
            $this->db->where('expires >', $dateNow);
            $this->db->where('id_usuario', $idUsuario);
            $this->db->where('client_id', $clientId);
            $this->db->update('oauth_tokens');
    
            $this->db->set('expires', $dateNow);
            $this->db->where('expires >', $dateNow);
            $this->db->where('id_usuario', $idUsuario);
            $this->db->where('client_id', $clientId);
            $this->db->update('oauth_refresh_tokens');
        }

    }

    private function generateHashToken()
    {
        if (function_exists('random_bytes')) {
            $randomData = random_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        return substr(hash('sha512', $randomData), 0, 40);
    }
}
