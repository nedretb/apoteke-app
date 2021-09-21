<?php

$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_mkt;", "intranet", "DynamicsNAV16!");
$year = $_POST['year'];
$month = $_POST['month'];

try {
    $sqlStmt = $db->prepare ("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_status='1010' WHERE status=5");
    $sqlStmt->execute();
}catch (Exception $e){}
$numberOfDaysMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$hourlyDayStatus = $db->query("SELECT status, id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE Date between '".$year.'-'.$month."-01' and '".$year.'-'.$month."-".$numberOfDaysMonth."' and status<>5");

foreach ($hourlyDayStatus as $singleStatus){

    //praznik vjerski
    if (in_array($singleStatus['status'], array(84, 21, 22))){
        $updateStatus = 1025;
    }

    //praznik drzavni
    if (in_array($singleStatus['status'], array(83, 146))){
        $updateStatus = 1026;
    }

    //Plaćeno odsustvo
    if (in_array($singleStatus['status'], array(27, 28, 29, 30, 31, 32, 79, 80, 145))){
        $updateStatus = 1020;
    }

    //Bolovanje povreda na radu preko 42 dana
    if (in_array($singleStatus['status'], array(130, 131))){
        $updateStatus = 1031;
    }

//    else{
//        $getApoStatus = $db->query("SELECT name FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] where id=".$singleStatus['status'])->fetch()['name'];
//        $updateStatus = $getApoStatus;
//    }

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

    //bolovanje
    if ($singleStatus['status'] == 67){
        $updateStatus = 1030;
    }

    //bolovanje do 42 dana
    if ($singleStatus['status'] == 43){
        $updateStatus = 1030;
    }

    //Bolovanje povreda van rada do 42 dana
    if ($singleStatus['status'] == 62){
        $updateStatus = 1036;
    }

    //Bolovanje preko 42 dana
    if ($singleStatus['status'] == 44){
        $updateStatus = 1031;
    }

    //Bolovanje povreda na radu
    if ($singleStatus['status'] == 61){
        $updateStatus = 1033;
    }



    //Službeni put
    if ($singleStatus['status'] == 73){
        $updateStatus = 1024;
    }


    //Prekovremeni rad
    if ($singleStatus['status'] == 91){
        $updateStatus = 2020;
    }

    //Prekovremeni noćni rad
    if ($singleStatus['status'] == 92){
        $updateStatus = 2021;
    }

    //Prekovremeni rad vikendom
    if ($singleStatus['status'] == 93){
        $updateStatus = 2024;
    }

    //Prekovremeni noćni rad vikendom
    if ($singleStatus['status'] == 94){
        $updateStatus = 2025;
    }

    //Prekovremeni rad praznikom
    if ($singleStatus['status'] == 95){
        $updateStatus = 2022;
    }




    //Trudničko bolovanje do 42 dana 100%
    if ($singleStatus['status'] == 65){
        $updateStatus = '1042 100';
    }

    //Trudničko bolovanje preko 42 dana
    if ($singleStatus['status'] == 68){
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

    //Redovan noćni rad
    if ($singleStatus['status'] == 85){
        $updateStatus = 2011;
    }

    //Neplaćeno odsustvo
    if ($singleStatus['status'] == 40){
        $updateStatus = 1021;
    }

    //Redovan rad vikendom
    if ($singleStatus['status'] == 86){
        $updateStatus = 2015;
    }

    //Noćni rad vikendom
    if ($singleStatus['status'] == 87){
        $updateStatus = 2014;
    }

    //Bolovanje do 42 dana 80%
    if ($singleStatus['status'] == 107){
        $updateStatus = '1030 80';
    }

    //Bolovanje preko 42 dana 80%
    if ($singleStatus['status'] == 116){
        $updateStatus = '1031 80';
    }

    //Bolovanje preko 42 dana odsutnost cijeli mjesec 80%
    if ($singleStatus['status'] == 123){
        $updateStatus = '1031 CM 80';
    }

    //Bolovanje preko 42 dana 100%
    if ($singleStatus['status'] == 124){
        $updateStatus = '1031 CM100';
    }

    //Bolovanje preko 42 dana odsutnost dio mjeseca 80%
    if ($singleStatus['status'] == 125){
        $updateStatus = '1031 DM 80';
    }

    //Bolovanje preko 42 dana 80%
    if ($singleStatus['status'] == 126){
        $updateStatus = '1031 DM100';
    }

    //Bolovanje preko 42 dana 80%
    if ($singleStatus['status'] == 126){
        $updateStatus = '1031 DM100';
    }

    //Bolovanje preko 42 dana - posl.
    if ($singleStatus['status'] == 127){
        $updateStatus = '1032';
    }

    //POV na radu do 42 dana 100%
    if ($singleStatus['status'] == 129){
        $updateStatus = '1033 100';
    }

    //Bolovanje povreda na radu preko 42 dana
    if ($singleStatus['status'] == 130){
        $updateStatus = '1034';
    }

    //POV na radu preko 42 dana 100%
    if ($singleStatus['status'] == 131){
        $updateStatus = '1034 100';
    }

    //Bolovanje profesionalno oboljenje
    if ($singleStatus['status'] == 132){
        $updateStatus = '1035';
    }

    //POV van rada do 42 dana 80%
    if ($singleStatus['status'] == 133){
        $updateStatus = '1036 80';
    }

    //Bolovanje povreda van rada preko 42 dana
    if ($singleStatus['status'] == 134){
        $updateStatus = '1037';
    }

    //POV van rada preko 42 dana odsutnost cijeli mjesec 80%
    if ($singleStatus['status'] == 135){
        $updateStatus = '1037 CM 80';
    }

    //POV van rada preko 42 dana odsutnost dio mjeseca 80%
    if ($singleStatus['status'] == 136){
        $updateStatus = '1037 DM 80';
    }

    //Bolovanje teško oboljenje
    if ($singleStatus['status'] == 137){
        $updateStatus = '1038';
    }

    //Rad na nedelju
    if ($singleStatus['status'] == 138){
        $updateStatus = '2013';
    }

    //Prekovremeni rad praznikom - radni dan
    if ($singleStatus['status'] == 139){
        $updateStatus = '2023';
    }

    //Trudničko bolovanje do 42 dana
    if ($singleStatus['status'] == 140){
        $updateStatus = '1042';
    }

    //Trudničko bolovanje preko 42 dana cijeli mjesec 80%
    if ($singleStatus['status'] == 141){
        $updateStatus = '1043 CM 80';
    }

    //Trudničko bolovanje preko 42 dana cijeli mjesec 100%
    if ($singleStatus['status'] == 142){
        $updateStatus = '1043 CM100';
    }

    //Trudničko bolovanje preko 42 dana dio mjeseca 80%
    if ($singleStatus['status'] == 143){
        $updateStatus = '1043 DM 80';
    }

    //Trudničko bolovanje preko 42 dana dio mjeseca 100%
    if ($singleStatus['status'] == 144){
        $updateStatus = '1043 DM100';
    }

    //Trudničko bolovanje preko 42 dana dio mjeseca 100%
    if ($singleStatus['status'] == 144){
        $updateStatus = '1043 DM100';
    }

    //Slobodan dan
    if ($singleStatus['status'] == 145){
        $updateStatus = '1027';
    }

    //Porodiljsko odsustvo
    if ($singleStatus['status'] == 147){
        $updateStatus = '1040';
    }

    //Naknada za porodiljsko
    if ($singleStatus['status'] == 148){
        $updateStatus = '1041';
    }

    //Bolovanje do 42 dana 100%
    if ($singleStatus['status'] == 153){
        $updateStatus = '1030 100';
    }

    //Bolovanje preko 42 dana 100%
    if ($singleStatus['status'] == 155){
        $updateStatus = '1031 100';
    }

    try {
        $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_status=? where id=?";
        $prep = $db->prepare($sqlStmt);
        $prep->execute([$updateStatus, $singleStatus['id']]);
    }catch (Exception $e){}
}

$hourlyDayStatusPre = $db->query("SELECT status_pre, id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where Date between '".$year.'-'.$month."-01' and '".$year.'-'.$month."-".$numberOfDaysMonth."' and status_pre is not null");
foreach ($hourlyDayStatusPre as $status){
    $updateStatusPre = null;
    //Prekovremeni rad
    if ($status['status_pre'] == 91){
        $updateStatusPre = 2020;
    }

    //Prekovremeni noćni rad
    if ($status['status_pre'] == 92){
        $updateStatusPre = 2021;
    }

    //Prekovremeni rad vikendom
    if ($status['status_pre'] == 93){
        $updateStatusPre = 2024;
    }

    //Prekovremeni noćni rad vikendom
    if ($status['status_pre'] == 94){
        $updateStatusPre = 2025;
    }

    //Prekovremeni rad praznikom
    if ($status['status_pre'] == 95){
        $updateStatusPre = 2022;
    }

    //Redovan noćni rad
    if ($status['status_pre'] == 85){
        $updateStatusPre = 2011;
    }

    //Redovan rad vikendom
    if ($status['status_pre'] == 86){
        $updateStatusPre = 2015;
    }

    //Noćni rad vikendom
    if ($status['status_pre'] == 87){
        $updateStatusPre = 2014;
    }

    //Rad praznikom
    if ($status['status_pre'] == 88){
        $updateStatusPre = 2010;
    }

    //Noćni rad praznikom
    if ($status['status_pre'] == 89){
        $updateStatusPre = 2012;
    }

    //Rad na nedelju
    if ($status['status_pre'] == 138){
        $updateStatusPre = '2013';
    }

    try {
        $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET apoteke_status_pre=? where id=?";
        $prep = $db->prepare($sqlStmt);
        $prep->execute([$updateStatusPre, $status['id']]);
    }catch (Exception $e){}
}