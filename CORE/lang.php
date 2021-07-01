<?php

/*
 *  Potrebno je prije prijave, kreirati sesiju o podacima - jeziku
 *  $_SESSION['lan'] = 'hr' || 'sr' || 'bs'
 */

function ___val($param, $lan){
    $string = file_get_contents("CORE/files/".(strtolower($lan)).".json");
    $data = json_decode($string, true);

    foreach ($data as $key => $val){
        if($key == $param) return $val;
    }
    return $param;
}

function ___($param){
    $lan = isset($_SESSION['lan']) ? $_SESSION['lan'] : 'BS';

    return ($lan == 'BS') ? $param : ___val($param, $lan);
}

?>