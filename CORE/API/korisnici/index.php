<?php

require_once '../api-header.php';
include "../../../classes/API/MainClass.php";
include "classes/Korisnici.php";

$params = ['uri' => 'http://localhost/apoteke-app/CORE/API/korisnici/index.php'];
$server = new SoapServer(NULL, $params);

$server->setClass('Korisnici');
$server->handle();
