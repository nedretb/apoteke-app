<?php


$curl = curl_init();

curl_setopt_array(
    $curl, array(
    CURLOPT_URL => 'http://localhost/apoteke-app/CORE/API/api-endpoint.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => "pismeno_jop="."JOP - 9201".'&pismeno_status='."1".'&pismeno_napomena='."NAPOMENICAA "
));

$output = curl_exec($curl);

var_dump($output);