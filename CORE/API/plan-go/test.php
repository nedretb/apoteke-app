<?php
require_once '../../../classes/API/SoapEvents.php';
use SoapEvents as Soap;


//try {
//
//    $client= new SoapClient("http://localhost/apoteke-app/CORE/API/plan-go/index.php");
//
//    $response=$client->GetData();
//
//} catch (SoapFault $e) {
//
//    print_r($e->getMessage());
//
//}

//$data = Soap::getData('http://localhost/apoteke-app/CORE/API/plan-go/index.php?wsdl', 'PromjeniStatusPismena', [
//        'userName' => 'Aladin',
//        'jor' => 'Aladin',
//        'rbrPredmeta' => 'Aladin',
//        'uredskaGodina' => 'Aladin',
//        'UI' => 'Aladin',
//        'statusPismena' => 'Aladin',
//        'nazivStatusaPismena' => 'Aladin',
//        'datumPotpisa' => 'Aladin',
//        'datumZadnjegUrucenja' => 'Aladin',
//        'datumNastanka' => 'Aladin',
//        'datumZaprimanja' => 'Aladin',
//        'datumZadnjeOtpreme' => 'Aladin',
//        'datumPromjeneStatusa' => 'Aladin',
//        'napomena' => 'Aladin',
//        'postojiEDokument' => 'Aladin',
//        'brojPrilogaPismena' => 'Aladin'
//    ]
//);
//
//var_dump($client->__getFunctions());



$params = array(
    'location' => 'http://localhost/apoteke-app/CORE/API/plan-go/?wsdl',
    'uri' => 'urn://localhost/apoteke-app/CORE/API/plan-go/?wsdl',
    'trace' => 1,
    'soap_version'=>SOAP_1_2,
    'wsdl_cache_enabled' => 0
);

//ini_set("xdebug.var_display_max_children", '-1');
//ini_set("xdebug.var_display_max_data", '-1');
//ini_set("xdebug.var_display_max_depth", '-1');
//
//try {
//
//
//    $instance = new SoapClient(NULL, $params);
//
//    $instance->__soapCall('PromjeniStatusPismena', [
//        'userName' => 'Aladin',
//        'jor' => 'Aladin',
//        'rbrPredmeta' => 'Aladin',
//        'uredskaGodina' => 'Aladin',
//        'UI' => 'Aladin',
//        'statusPismena' => 'Aladin',
//        'nazivStatusaPismena' => 'Aladin',
//        'datumPotpisa' => 'Aladin',
//        'datumZadnjegUrucenja' => 'Aladin',
//        'datumNastanka' => 'Aladin',
//        'datumZaprimanja' => 'Aladin',
//        'datumZadnjeOtpreme' => 'Aladin',
//        'datumPromjeneStatusa' => 'Aladin',
//        'napomena' => 'Aladin',
//        'postojiEDokument' => 'Aladin',
//        'brojPrilogaPismena' => 'Aladin'
//    ]);
//
//    var_dump($instance);
//} catch (SoapFault $e) {
//    var_dump($e);
//}


$options = array(
    'trace' => true
);
try{
    $client = new SOAPClient('http://localhost/apoteke-app/CORE/API/plan-go/index.php?wsdl', $options);
    var_dump($client->PromjeniStatusPismena(['promjeniStatus' => 10]));
}catch (\Exception $e){
    var_dump($e);
}