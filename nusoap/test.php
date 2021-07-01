<?php

include "lib/nusoap.php";
//
//// Create object
//$client = new nusoap_client('http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl', true);
////Setting credentials for Authentication
//$a = $client->setCredentials('VM\epinsatest',"Sarajevo101","basic");

//
//$soapParameters = Array('login' => 'VMpinsatest', 'password' => "Sarajevo101") ;
//
//$soapclient = new SoapClient('http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl', $soapParameters);
//
//
////
////$soapclient = new SoapClient('http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl');
////$soapclient->setCredentials("user","password");
//
//die($a);

//$wsdl_url = 'http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl';
//
//$params = [
//    'userName' => 'VM\epinsatest',
//    'upisnaKnjiga' => 'NP',
//    'vrstaPredmeta' => 12,
//    'nadleznaOrgJedinica' => 7,
//    'subjektOznaka' => 15
//];
//
//
//$proxyusername = 'VM\epinsatest';
//$proxypassword = "Sarajevo101";
//$client = new nusoap_client('http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl', 'wsdl');
//$client->setCredentials($proxyusername, $proxypassword, 'realm');
//$err = $client->getError();
//if ($err) {
//    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
//}
//// Doc/lit parameters get wrapped
////$param = array('Symbol' => 'IBM');
//// echo "<pre>";
//// var_dump($client); exit();
//$result = $client->call('KreirajPredmet2', $params, '', '', false, true);
//// Check for a fault
//if ($client->fault) {
//    echo '<h2>Fault</h2><pre>';
//    print_r($result);
//    echo '</pre>';
//}else{
//    var_dump($result);
//}


?>

    <title>client</title>
    <meta charset="utf-8">
<?php
ini_set("soap.wsdl_cache_enabled", "0");
$username = 'VM\epinsatest';
$password = 'Sarajevo101';

// $client = new nusoap_client('http://'.$username.':'.$password.'@10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl', true);

$client = new nusoap_client("http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?wsdl", false);
$client->setCredentials($username, $password, 'basic');

$result = $client->call('KreirajPredmet2', array(
    'userName' => 'VM\epinsatest',
    'upisnaKnjiga' => 'NP',
    'vrstaPredmeta' => 12,
    'nadleznaOrgJedinica' => 7,
    'subjektOznaka' => 15
));

if ($client->fault) {
    echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
    print_r($result);
    echo '</pre>';
} else {
    $err = $client->getError();
    if ($err) {
        echo '<h2>Error</h2><pre>' . $err . '</pre>';
    } else {
        echo '<h2>Result</h2><pre>';
        var_dump($result);
        echo '</pre>';
    }
}

echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
