<?php

$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_mkt;", "intranet", "DynamicsNAV16!");
$year = date('Y');
$month = date('m');

//try {
//    $sqlStmt = $db->prepare ("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_sifra='1010' WHERE status=5");
//    $sqlStmt->execute();
//}catch (Exception $e){}

$hourlyDayStatus = $db->query("SELECT status, id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE Date>'".$year.'-'.$month."-01' and status<>5");

foreach ($hourlyDayStatus as $singleStatus){

    //godisnji odmor - stari
    if ($singleStatus['status'] == 19){
        $getApoStatus = $db->query("SELECT name FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] where id=".$singleStatus['status'])->fetch()['name'];
        $updateStatus = $getApoStatus;
    }

    //godisnji odmor - tekuci
    if ($singleStatus['status'] == 18){
        $getApoStatus = $db->query("SELECT name FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] where id=".$singleStatus['status'])->fetch()['name'];
        $updateStatus = $getApoStatus;
    }
    //praznik vjerski
    if (in_array($singleStatus['status'], array(84, 21, 22))){
        $updateStatus = 1025;
    }

    //praznik drzavni
    if (in_array($singleStatus['status'], array(84, 21, 22))){
        $updateStatus = 1026;
    }

    //1030	Bolovanje do 42 dana

    //bolovanje do 42 dana
    if (in_array($singleStatus['status'], array(43, 47))){
        $updateStatus = 1030;
    }

    //1036	Bolovanje povreda van rada

    //bolovanje do 42 dana
    if (in_array($singleStatus['status'], array(43, 47))){
        $updateStatus = 1030;
    }


}