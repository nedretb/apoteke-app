<?php
//// include $root . '/classes/API/SoapEvents.php';
//
//if (!class_exists('SoapClient')) {
//    die ("You haven't installed the PHP-Soap module.");
//}
//use SoapEvents as Soap;
///*
// *  To read data from Soap, you need to call make an request as show below:
// *  Soap::getData($uri, $method, ['key' => 'value'])
// *  It would return object of stdClass
// */
//
// $data = Soap::getData('https://www.w3schools.com/xml/tempconvert.asmx?wsdl', 'FahrenheitToCelsius', ['Fahrenheit' => '48']);
////
////// $data = Soap::getData('http://localhost/apoteke-app/?m=api&p=soap-srv', 'getTestName', []);
////
// var_dump($data->FahrenheitToCelsiusResult);
//
//
//
//die();
//// $data = Soap::getData('http://localhost/apoteke-app/?m=api&p=soap-srv', 'getTestName', []);
//
//// var_dump($data);
//
//
//
//$params = array(
//    'location' => 'http://localhost/apoteke-app/modules/api/pages/soap-srv.php',
//    'uri' => 'urn://localhost/apoteke-app/modules/api/pages/soap-srv.php',
//    'trace' => 1
//);
//
//try {
//    // $instance = new SoapClient(NULL, $params);
//
//    // $instance->__soapCall('getTestName', ['id' => "weehe"]);
//
//    $data = Soap::getData('http://localhost/apoteke-app/modules/api/pages/soap-srv.php?wsdl', 'getTestName', ['id' => '48']);
//
//    var_dump($instance);
//} catch (SoapFault $e) {
//    var_dump($e);
//}
