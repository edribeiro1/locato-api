<?php

function formataDataUtcParaDatetime($dataUtc, $timezone = 'America/Sao_Paulo', $toFormat = 'd/m/Y H:i:s', $fromFormat = 'Y-m-d H:i:s')
{
    if ($dataUtc && is_numeric($dataUtc) && (int)$dataUtc > 0) { //TIMESTAMP
        $data = DateTime::createFromFormat('U', $dataUtc, new DateTimeZone('UTC'));
    } elseif ($dataUtc && is_string($dataUtc) && strlen($dataUtc) > 0) {
        $data = DateTime::createFromFormat($fromFormat, $dataUtc, new DateTimeZone('UTC'));
    } elseif ($dataUtc instanceof DateTime) {
        $data = $dataUtc;
    } else {
        $data = false;
    }
    if ($data) {
        $data->setTimeZone(new DateTimeZone($timezone));
        if ($toFormat) {
            return $data->format($toFormat);
        } else {
            return $data;
        }
    }
    return false;
}

function formataDataParaUtc($data, $timezone = "UTC", $toFormat = 'Y-m-d H:i:s', $fromFormat = 'd/m/Y H:i:s')
{
    $datetime = DateTime::createFromFormat($fromFormat, $data, new DateTimeZone($timezone));
    if ($datetime) {
        $datetime->setTimezone(new DateTimeZone('UTC'));
        if ($toFormat) {
            return $datetime->format($toFormat);
        } else {
            return $datetime;
        }
    }
    return false;
}

function calcularOdometroHorimetroEmbarcado(&$dados,  $calcularHorimetro = true, $calcularOdometro = true)
{
    $total = count($dados);

    if ($total <= 1) {
        return array(
            'tempo_ocioso' => 0,
            'tempo_movimento' => 0,
            'tempo_desligado' => 0,
            'tempo_ligado' => 0,
            'odometro' => 0
        );
    }

    $odometro = 0;
    $tempoOcioso = 0;
    $tempoMovimento = 0;
    $tempoDesligado = 0;
    $tempoLigado = 0;

    for ($i=0; $i < ($total -1); $i++) {

        if ($calcularHorimetro) {
            if (validarId($dados[$i], 'vl_horimetro_embarcado') && validarId($dados[$i+1], 'vl_horimetro_embarcado') ) {
                $diferenca = (int)$dados[$i+1]['vl_horimetro_embarcado'] - (int)$dados[$i]['vl_horimetro_embarcado'];
                if ($diferenca > 0) {
                    $tempoLigado += $diferenca;
                    if ($dados[$i]['vl_velocidade'] <= 1) {
                        $tempoOcioso += $diferenca;
                    } else {
                        $tempoMovimento += $diferenca;
                    }
                }
            }

            if ((int)$dados[$i]['flg_ignicao'] == 0) {
                $tempoDesligado += (int)( (int)$dados[$i+1]['timestamp_gps'] - (int)$dados[$i]['timestamp_gps'] ) ;
            }
        }

        if($calcularOdometro) {
            if (validarId($dados[$i], 'vl_hodometro_embarcado') && validarId($dados[$i+1], 'vl_hodometro_embarcado') ) {
                $diferenca = (int)$dados[$i+1]['vl_hodometro_embarcado'] - (int)$dados[$i]['vl_hodometro_embarcado'];
                if ($diferenca > 0) {
                    $odometro += $diferenca;
                }
            }
        }
     
    }

    return array(
        'odometro' => $odometro,
        'tempo_ocioso' => $tempoOcioso,
        'tempo_movimento' => $tempoMovimento,
        'tempo_desligado' => $tempoDesligado,
        'tempo_ligado' => $tempoLigado
    );

}


function calcularOdometroHorimetroGps(&$dados, $desconsiderarStatus = false, $calcularHorimetro = true, $calcularOdometro = true)
{
    $total = count($dados);

    if ($total <= 1) {
        return array(
            'tempo_ocioso' => 0,
            'tempo_movimento' => 0,
            'tempo_desligado' => 0,
            'tempo_ligado' => 0,
            'odometro' => 0
        );
    }

    $tempoOcioso = 0;
    $tempoMovimento = 0;
    $tempoDesligado = 0;
    $tempoLigado = 0;
    $odometro = 0;
    $ultimoEvento = null;

    for ($i=0; $i < $total; $i++) {

        $atualEvento = $dados[$i];

        //  HORIMETRO >>>>>
        if ($calcularHorimetro ) {
            if ($i < $total-1) {
                $proxEvento = $dados[$i+1];
                $diferencaSegundos = (int)( (int)$proxEvento['timestamp_gps'] - (int)$atualEvento['timestamp_gps'] ) ;

                if ((int)$atualEvento['flg_ignicao'] == 1) {
                    $tempoLigado += $diferencaSegundos;

                    if ($atualEvento['vl_velocidade'] <= 1) {
                        $tempoOcioso += $diferencaSegundos;
                    } else {
                        $tempoMovimento += $diferencaSegundos;
                    }
                } else {
                    $tempoDesligado += $diferencaSegundos;
                }
            }
        }
        //  HORIMETRO <<<<<
       
        //ODOMETRO >>>>>
        if ($calcularOdometro) {
            if ((int)$atualEvento['flg_status_gps'] || $desconsiderarStatus) {
                $atualEvento['lst_localizacao'] = json_decode($atualEvento['lst_localizacao']);
                
                if (is_null($ultimoEvento)) {
                    if ($i < $total-1) {
                        $proxEvento = $dados[$i+1];
                        $proxEvento['lst_localizacao'] = json_decode($proxEvento['lst_localizacao']);

                        if (validarDistanciaEntrePosicoes($atualEvento, $proxEvento)) {
                            $ultimoEvento = $atualEvento;
                        }
                    }
                } else {
                    if (validarDistanciaEntrePosicoes($ultimoEvento, $atualEvento)) {
                        if ((int)$ultimoEvento['flg_ignicao']) {
                            $odometro += calcularDistanciaEntrePontos($ultimoEvento['lst_localizacao'], $atualEvento['lst_localizacao']);
                        }
                        $ultimoEvento = $atualEvento;
                    }
                }
            }
        }
        //ODOMETRO <<<<<

    }

    return array(
        'odometro' => $odometro,
        'tempo_ocioso' => $tempoOcioso,
        'tempo_movimento' => $tempoMovimento,
        'tempo_desligado' => $tempoDesligado,
        'tempo_ligado' => $tempoLigado
    );
}

function formataHorarioParaSegundos($horario)
{
    $segundos = 0;
    $horario = explode(':', $horario);

    $segundos += ((int)$horario[0] * 60) * 60;
    $segundos += (int)$horario[1] * 60;
    $segundos += (int)$horario[2];

    return $segundos;
}

function somarHorario($tempo1, $tempo2)
{
    $segundos = 0;

    if (is_numeric($tempo1)) {
        $tempo1 = formataSegundosParaHorario($tempo1);
    }

    if (is_numeric($tempo2)) {
        $tempo2 = formataSegundosParaHorario($tempo2);
    }

    if (!$tempo1) {
        $tempo1 = "00:00:00";
    }

    if (!$tempo2) {
        $tempo2 = "00:00:00";
    }

    $tempo1 = explode(':', $tempo1);
    $tempo2 = explode(':', $tempo2);

    $segundos += ((int)$tempo1[0] * 60) * 60;
    $segundos += ((int)$tempo2[0] * 60) * 60;
    $segundos += (int)$tempo1[1] * 60;
    $segundos += (int)$tempo2[1] * 60;
    $segundos += (int)$tempo1[2];
    $segundos += (int)$tempo2[2];

    $hora = (int)($segundos / 3600);
    $minuto = (int)(($segundos % 3600) / 60);
    $segundo = (int)(($segundos % 3600) % 60);

    return ($hora < 10 ? '0'.$hora : $hora).':'.($minuto < 10 ? '0'.$minuto : $minuto).':'.($segundo < 10 ? '0'.$segundo : $segundo);
}


function formataSegundosParaHorario($segundos = 0)
{
    if (is_numeric($segundos) && (int)$segundos > 0) {
        $hora = (int)($segundos / 3600);
        $minuto = (int)(($segundos % 3600) / 60);
        $segundo = (int)(($segundos % 3600) % 60);
        return ($hora < 10 ? '0'.$hora : $hora).':'.($minuto < 10 ? '0'.$minuto : $minuto).':'.($segundo < 10 ? '0'.$segundo : $segundo);
    } else {
        return '00:00:00';
    }
}

function formataOdometro($odometro)
{
    return number_format(((int)$odometro / 1000), 2, ',', '');
}

function calculaDiferencaData($data1, $data2) {
    $data1 = formataDataParaUtc($data1, 'UTC', false);
    $data2 = formataDataParaUtc($data2, 'UTC', false);
    if ($data1 && $data2) {
        $diferenca = $data1->getTimestamp() - $data2->getTimestamp();

        if ($diferenca < 0 ) {
            $diferenca *= -1;
        }

        return formataSegundosParaHorario($diferenca);
    }
    return '00:00:00';
}

function calculaDiferencaHorario($horaInicial, $horaFinal, $returnHorario = true) 
{

    if (is_numeric($horaInicial)) {
        $horaInicial = formataSegundosParaHorario($horaInicial);
    }

    if (is_numeric($horaFinal)) {
        $horaFinal = formataSegundosParaHorario($horaFinal);
    }

    $horaInicial = explode(':', $horaInicial);
    $horaFinal = explode(':', $horaFinal);

    $segundosInicial = 0;
    $segundosFinal = 0;

    $segundosInicial += ((int)$horaInicial[0] * 60) * 60;
    $segundosInicial += (int)$horaInicial[1] * 60;
    $segundosInicial += (int)$horaInicial[2];

    $segundosFinal +=  ((int)$horaFinal[0] * 60) * 60;
    $segundosFinal += (int)$horaFinal[1] * 60;
    $segundosFinal += (int)$horaFinal[2];


    $diferencaSegundos = (int)$segundosFinal -  (int)$segundosInicial;
    
    if ($diferencaSegundos < 0) {
        $diferencaSegundos = $diferencaSegundos * -1;
    }
    $hora = (int)($diferencaSegundos / 3600);
    $minuto = (int)(($diferencaSegundos % 3600) / 60);
    $segundo = (int)(($diferencaSegundos % 3600) % 60);

    if ($returnHorario) {
        return ($hora < 10 ? '0'.$hora : $hora).':'.($minuto < 10 ? '0'.$minuto : $minuto).':'.($segundo < 10 ? '0'.$segundo : $segundo);
    }
    return $diferencaSegundos;
}

function calcularDistanciaEntrePontos($lat_lng1, $lat_lng2)
{
    $r = 6371.0;
    $p1LA = ($lat_lng1[0] * pi()) / 180.0;
    $p1LO = ($lat_lng1[1] * pi()) / 180.0;
    $p2LA = ($lat_lng2[0] * pi()) / 180.0;
    $p2LO = ($lat_lng2[1] * pi()) / 180.0;
    $dLat = $p2LA - $p1LA;
    $dLong = $p2LO - $p1LO;
    $a = sin($dLat / 2) * sin($dLat / 2) + cos($p1LA) * cos($p2LA) * sin($dLong / 2) * sin($dLong / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $metros = round($r * $c * 1000);
    return $metros;
}

function validarDistanciaEntrePosicoes($pos1 = array(), $pos2 = array()) {

    if (count($pos1) > 0 && count($pos2) > 0) {
        $km = (calcularDistanciaEntrePontos($pos2['lst_localizacao'], $pos1['lst_localizacao'])) / 1000;
        $hora = ( ((int)$pos2['timestamp_gps'] - (int)$pos1['timestamp_gps']) / 60) / 60;

        if(($km == 0 && $hora == 0)) {
            return true;
        }
        else if($km > 0 && $hora == 0) {
            return false;
        }
        else if (($km / $hora) < 200) {
            return true;
        }

    }
    return false;
}

function consultarSpectrum($params)
{
    $data = formataDataParaUtc($params['dt_inicial'], 'UTC', false, 'Y-m-d H:i:s');
    if (time() - $data->getTimestamp() > 7776000) { //maior que 3 meses
        return true;
    } else {
        return false;
    }
}


function calcularVelocidadeMedia($odometro, $horimetro)
{
    if (is_numeric($odometro) & $odometro > 0 && is_numeric($horimetro) & $horimetro > 0) {
        $velocidadeMedia = ($odometro/$horimetro) * 3.6;
        return number_format($velocidadeMedia, 2, '.', '');
    }
    return 0;
}


function validarId($idOuArrayAssoc = null, $chave = null)
{
    if (is_array($idOuArrayAssoc)) {
        if (isset($idOuArrayAssoc[$chave]) && $idOuArrayAssoc[$chave] && is_numeric($idOuArrayAssoc[$chave]) && (int)$idOuArrayAssoc[$chave] > 0) {
            return true;
        }
    } elseif ($idOuArrayAssoc && is_numeric($idOuArrayAssoc) && (int)$idOuArrayAssoc > 0) {
        return true;
    }

    return false;
}


function correcaoMotorista(&$dados, $order)
{
    $total = count($dados);

    if ($order == 'ASC') {
        for ($i=0; $i<$total-1; $i++) {
            if ($dados[$i]['flg_ignicao'] == 1 && (int)$dados[$i]['id_motorista'] != 9999 && $dados[$i+1]['flg_ignicao'] == 1 && $dados[$i+1]['id_motorista'] == 9999) {
                $dados[$i+1]['id_motorista'] = $dados[$i]['id_motorista'];
                $dados[$i+1]['desc_motorista'] = $dados[$i]['desc_motorista'];
            }
        }   
    }
    else {
        for ($i=$total-1; $i > 0; $i--) {
            if ($dados[$i]['flg_ignicao'] == 1 && (int)$dados[$i]['id_motorista'] != 9999 && $dados[$i-1]['flg_ignicao'] == 1 && $dados[$i-1]['id_motorista'] == 9999) {
                $dados[$i-1]['id_motorista'] = $dados[$i]['id_motorista'];
                $dados[$i-1]['desc_motorista'] = $dados[$i]['desc_motorista'];
            }
        }   
    }
  

}

