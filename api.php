<?php

// Include Soap Events file
include "classes/API/SoapEvents.php";
use SoapEvents as Soap;


if(isset($_GET['_token'])){
    $_token = $_GET['token'];


}

$data = Soap::getData('http://localhost/apoteke-app/modules/api/pages/soap-srv.php?wsdl', 'getTestName', ['id' => '48']);