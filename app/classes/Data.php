<?php
namespace App\classes;

Class Data {

public function Data()  {

    $dia = date("d");
    $mes = date("m");
    $ano = date("Y");

    switch ($mes) {
        case '01':
            $mes = "Janeiro";
            break;
        case '02':
            $mes = "Fevereiro";
            break;
        case '03':
            $mes = "Março";
            break;
        case '04':
            $mes = "Abril";
            break;

        case '05':
            $mes = "Maio";
            break;

        case '06':
            $mes = "Junho";
            break;

        case '07':
            $mes = "Julho";
            break;

        case '08':
            $mes = "Agosto";
            break;
        case '09':
            $mes = "Setembro";
            break;
        case '10':
            $mes = "Outubro";
            break;

        case '11':
            $mes = "Novembro";
            break;

        case '12':
            $mes = "Dezembro";
            break;

        default:
            $mes = null;
            break;
    }

    $data = [
    'mes'=>$mes,
    'ano'=>$ano,
    'dia'=>$dia
    ];

   return $data;
}

public function Meses(){
    $meses = ([
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro'
    ]);
    return $meses;
}

}
