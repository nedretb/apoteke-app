<?php

require_once '../api-header.php';
include "../../../classes/API/MainClass.php";
include "classes/PismenoUpdate.php";

$params = ['uri' => 'http://localhost/apoteke-app/CORE/API/pismeno/index.php'];
$server = new SoapServer(NULL, $params);

$_POST['OperationSucceeded'] = '1';

$server->setClass('PismenoUpdate');
$server->handle();
