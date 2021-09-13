<?php

$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_mkt;", "intranet", "DynamicsNAV16!");
$year = date('Y');
$month = date('m');

//try {
//    $sqlStmt = $db->prepare ("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_status='1010' WHERE status=5");
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
    if (in_array($singleStatus['status'], array(84, 21, 22, 83))){
        $updateStatus = 1026;
    }

    //bolovanje do 42 dana
    if (in_array($singleStatus['status'], array(43, 107))){
        $updateStatus = 1030;
    }

    //Bolovanje povreda van rada do 42 dana
    if ($singleStatus['status'] == 129){
        $updateStatus = 1036;
    }

    //Bolovanje povreda van rada preko 42 dana
    if (in_array($singleStatus['status'], array(135, 136))){
        $updateStatus = 1037;
    }

    //Bolovanje preko 42 dana
    if (in_array($singleStatus['status'], array(44, 116, 123, 124, 125, 126, 127, 134))){
        $updateStatus = 1031;
    }

    //Bolovanje povreda na radu
    if ($singleStatus['status'] == 129){
        $updateStatus = 1033;
    }

    //Bolovanje povreda na radu preko 42 dana
    if (in_array($singleStatus['status'], array(130, 131))){
        $updateStatus = 1031;
    }

    //Službeni put
    if ($singleStatus['status'] == 73){
        $updateStatus = 1024;
    }

    //Porodiljsko odsustvo
    if (in_array($singleStatus['status'], array(74, 75, 76, 77, 78, 105))){
        $updateStatus = 1040;
    }

    //Prekovremeni rad
    if ($singleStatus['status'] == 91){
        $updateStatus = 2020;
    }

    //Prekovremeni rad vikendom
    if ($singleStatus['status'] == 93){
        $updateStatus = 2024;
    }

    //Plaćeno odsustvo
    if (in_array($singleStatus['status'], array(28, 29, 30, 31, 32, 79, 80))){
        var_dump('wwwwww');
        $updateStatus = 1020;
    }

    //Trudničko bolovanje do 42 dana
    if ($singleStatus['status'] == 140){
        $updateStatus = 1042;
    }

    //Trudničko bolovanje preko 42 dana
    if (in_array($singleStatus['status'], array(141, 142, 143, 144))){
        $updateStatus = 1043;
    }

    //Rad praznikom
    if ($singleStatus['status'] == 88){
        $updateStatus = 2010;
    }

    //Noćni rad praznikom
    if ($singleStatus['status'] == 89){
        $updateStatus = 2012;
    }

    //Noćni rad praznikom
    if ($singleStatus['status'] == 85){
        $updateStatus = 2011;
    }

    //Neplaćeno odsustvo
    if ($singleStatus['status'] == 40){
        $updateStatus = 1021;
    }

    var_dump($updateStatus);
    try {
        $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_status=? where id=?";
        $prep = $db->prepare($sqlStmt);
        $prep->execute([$updateStatus, $singleStatus['id']]);
    }catch (Exception $e){}
}