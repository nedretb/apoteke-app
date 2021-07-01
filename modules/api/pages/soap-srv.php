<?php
// include $root . '/classes/API/SoapServer.php';
include '../../../classes/API/SoapServer.php';

$params = ['uri' => 'http://localhost/apoteke-app/modules/api/pages/soap-srv.php'];
$server = new SoapServer(NULL, $params);

$server->setClass('SoapEventServer');
$server->handle();


$params = array(
    'location' => 'http://localhost/apoteke-app/modules/api/pages/soap-srv.php',
    'uri' => 'urn://localhost/apoteke-app/modules/api/pages/soap-srv.php',
    'trace' => 1
);

try {
    $instance = new SoapClient(NULL, $params);

    $instance->__soapCall('getTestName', ['id' => "weehe"]);

    var_dump($instance);
} catch (SoapFault $e) {
    var_dump($e);
}
