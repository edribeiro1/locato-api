<?php

function getContents($paramentro = false)
{
    $contents = null;

    switch (strtolower($_SERVER['REQUEST_METHOD'])) {
        case 'delete':
        case 'get':
            $contents = $_GET;
            break;

        case 'post':
        case 'put':
            $contents = $_POST;
            if (!is_array($contents) || count($contents) <= 0) {
                parse_str(file_get_contents("php://input"), $contents);
            }
            break;
    }

    if ($contents && is_array($contents) && count($contents) > 0) {

        if ($paramentro && strlen($paramentro) > 0) {
            if (isset($contents[$paramentro])) {
                return $contents[$paramentro];
            } else {
                return false;
            }
        }
        return $contents;
    }
    return false;
}

function send($statusCode = 200, $data = null, $message = null, $tag = null)
{
    $statusTexts = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );
    $params = [];
    if (is_array($data)) {
        $params = $data;
    }
    if ($message) {
        $params["message"] = $message;
    }
    if ($tag) {
        $params["tag"] = $tag;
    }

    header("HTTP/1.1 $statusCode $statusTexts[$statusCode]");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Access-Control-Allow-Methods: GET,OPTIONS,POST,PUT,DELETE');
    header('Content-Type: application/json');

    if (count($params)) {
        echo json_encode($params);
    }
    die;
}
