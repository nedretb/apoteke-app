<?php
error_reporting(E_ALL);

use Carbon\Carbon;

global $db;
$current_year = date('Y');

//array pocetnih datuma
$starting_dates = [];

//array zavrsnih datuma
$ending_dates = [];

//array broja dana izmedju datuma
$days_number_between_dates = [];

//ukupan broj dana iskustva za go
$go_days_exp = 0;

$years = 0;
$months = 0;
$days = 0;

//TODO ubaciti entitet usera
/**** Zakonski broj dana *****/
$go_days_base = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where region='FBIH' and year =".$current_year)->fetch();

/***** Broj dana na osnovu radnog iskustva *****/
$go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");

/**** Kategorije invaliditeta ****/
$invalid_categories = $db->query('select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go]');

/**** Zaposlenici *****/
$employers = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1");

//array pocetnih datuma
$starting_dates = [];

//array zavrsnih datuma
$ending_dates = [];

//koeficijenti
$coefficients = [];

$employers = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1");
foreach ($employers as $employer) {
    //var_dump($employer['Employee No_']);
    //ukupan broj dana iskustva
    count_go($employer['Employee No_']);
//    $total_days_for_go = 0;
//
//    $curr_date = Carbon::parse($employer['Ending Date']);
//    $curr_xp = round($curr_date->diffInDays($employer['Starting Date']) * $employer['Coefficient']);
//
//    $curr_years = floor($curr_xp / 365);
//    $curr_months = floor(($curr_xp - $curr_years * 365) / 30);
//    $curr_days = $curr_xp - $curr_years * 365 - 30 * $curr_months;
//
//    $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]=" . $employer['Employee No_'].' order by [Starting Date] desc');
//
//    /******* Radnik ima prijasnje radno iskustvo ******/
//    if ($previous_companies->rowCount() < 0) {
//
//
//        $total_go_days = $go_days_base['number_of_days'];
//
//        /******* Provjera invalidnosti ******/
//        if($employer['invalid'] == "DA"){
//            foreach ($invalid_categories as $i){
//                if($employer['invalid_category'] == $i['category']){
//                    $total_go_days = $total_go_days + $i['number_of_days'];
//                }
//            }
//        }
//
//        /******* Provjera dijete sa posebnim potrebama ******/
//        if($employer['child_disabled'] == "DA"){
//            $total_go_days = $total_go_days+2;
//        }
//
//        foreach ($previous_companies as $previous_exp){
////            $rl = Carbon::parse($previous_exp['Starting Date']);
////            $tt=0;
////
////            $dt = Carbon::parse($previous_exp['Ending Date']);
////            $stuff = $dt->diff($previous_exp['Starting Date']);
////            $total_days = round(($stuff->d + ($stuff->m*30) + ($stuff->y*12*30)) * $previous_exp['Coefficient']);
////            //$total_experience_days = $total_experience_days + round($dt->diffInDays($previous_exp['Starting Date']) * $previous_exp['Coefficient']);
////            $total_days_for_go= $total_days_for_go + $total_days;
//            //$total_days_for_go = $total_days_for_go + round($rl->diffInDays($previous_exp['Ending Date']) * $previous_exp['Coefficient']);
//
//            array_push($starting_dates, $previous_exp['Starting Date']);
//            array_push($ending_dates, $previous_exp['Ending Date']);
//            array_push($coefficients, $previous_exp['Coefficient']);
//        }
//
//        $count=0;
//        for($i = 0; $i < count($starting_dates)-1; $i++){
//            $date = Carbon::parse($starting_dates[$i]);
//            if($date->diffInDays($ending_dates[$i+1]) > 15){
//                //var_dump($i);
//                $td= 0;
//                $dais = $date->diffInDays($ending_dates[$i]);
//                $td = round($dais * $coefficients[$i]);
//                $total_days_for_go= $total_days_for_go + $td;
//
//                continue;
//            }
//            else{
//                $td= 0;
//                $dais = $date->diffInDays($ending_dates[$i]);
//                $td = round($dais * $coefficients[$i]);
//                $total_days_for_go= $total_days_for_go + $td;
//                var_dump($total_days_for_go);
//                $count++;
//            }
//        }
//        var_dump($count);
//        var_dump($ending_dates);
//        var_dump($starting_dates);
//
//
//
//
//        $years = 0;
//        $months = 0;
//        $days = 0;
//
//        $years = floor($total_days_for_go/365);
//        $months = floor(($total_days_for_go - $years*365)/30);
//        $days = $total_days_for_go-$years*365- 30 * $months + 1;
//        var_dump($years);
//        var_dump($months);
//        var_dump($days);
//        /******* Provjera godine radnog iskustva ******/
//        $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
//        foreach ($go_work_experience as $gwe){
//            if ($years > $gwe['min'] and $years < $gwe['max']){
//                $total_go_days = $total_go_days + $gwe['number_of_days'];
//            }
//        }
//
//        try {
//
//            $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer['Employee No_'];
//            $dbq = $db->query($sql);
//
//            $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer['Employee No_']." and year=".$current_year;
//
//            $dbq = $db->query($sql2);
//
//
//
//        }
//        catch (Exception $e){
//            var_dump($e);
//            die();
//        }
//
//
//    }
//
//    /******* Radnik nema prijasnje radno iskustvo ******/
//    else{
//        if ($employer['current_exp_m'] > 6){
//            $curr_dt = Carbon::parse($employer['Ending Date']);
//            $curr_xp = $curr_dt->diff($employer['Starting Date']);
//
//            $total_go_days = $go_days_base['number_of_days'];
//
//            /******* Provjera invalidnosti ******/
//            if($employer['invalid'] == "DA"){
//                foreach ($invalid_categories as $i){
//                    if($employer['invalid_category'] == $i['category']){
//                        $total_go_days = $total_go_days + $i['number_of_days'];
//                    }
//                }
//            }
//
//            /******* Provjera dijete sa posebnim potrebama ******/
//            if($employer['child_disabled'] == "DA"){
//                $total_go_days = $total_go_days+2;
//            }
//
//            /******* Provjera godine radnog iskustva ******/
//            $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
//            foreach ($go_work_experience as $gwe){
//                if ($employer['current_exp_y'] > $gwe['min'] and $employer['current_exp_y'] < $gwe['max']){
//                    $total_go_days = $total_go_days + $gwe['number_of_days'];
//                }
//            }
//
//            try {
//                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer['Employee No_'];
//                $dbq = $db->query($sql);
//
//                $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer['Employee No_']." and year=".$current_year;
//                $dbq = $db->query($sql2);
//
//            }
//            catch (Exception $e){
//                var_dump($e);
//                die();
//            }
//
//        }
//
//        /******* Novi radnik bez iskustva ******/
//        else {
//            try {
//                //var_dump($employer['current_exp_m']);
//                if($employer['current_exp_m'] == null){
//                    $new_emp = 0;
//                }
//                else{
//                    $new_emp = $employer['current_exp_m'];
//                }
//                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$new_emp." where [Employee No_]=".$employer['Employee No_'];
//                $dbq = $db->query($sql);
//
//            }
//            catch (Exception $e){
//                var_dump($e);
//                die();
//            }
//        }
//
//    }
}

header('Location: ?m=work_booklet&p=all');
