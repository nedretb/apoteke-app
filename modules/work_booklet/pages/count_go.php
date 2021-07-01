<?php
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Carbon\Carbon;

global $db;
$current_year = date('Y');

//array pocetnih datuma
$starting_dates = [];

//array zavrsnih datuma
$ending_dates = [];

//array broja dana izmedju datuma
$days_number_between_dates = [];

//ukupan broj dana iskustva
$total_experience_days = 0;

//ukupan broj dana iskustva za go
$go_days_exp = 0;

//$x = Carbon::parse('1988-07-15');
//var_dump($x->diffInDays('2021-04-28'));
//var_dump($x->diffInMonths('2021-04-28'));
//var_dump($x->diffInYears('2021-04-28'));
//var_dump($x->diff('2021-04-28'));
//$datetime1 = new DateTime('1988-07-15');
//$datetime2 = new DateTime('2021-04-28');
//$interval = $datetime1->diff($datetime2);
//var_dump($interval);
//var_dump($interval->format('%y years %m months %d days'));
//die();
//broj dana go
$go_days = 0;
$go_days_baseq = $db->query("Select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where region='FBIH' and year =".$current_year)->fetch();
$go_days_base = $go_days_baseq['number_of_days'];

$employees = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1");
$invalidity_categories = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go]")->fetchAll();
$year_exp_go = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]")->fetchAll();
var_dump($employees->fetchAll());
foreach ($employees as $employee){
    $d = Carbon::parse($employee['Starting Date']);
    $total_experience_days = $total_experience_days + $d->diffInDays($employee['Ending Date']);
    array_push($days_number_between_dates, $d->diffInDays($employee['Ending Date']));

    array_push($starting_dates, $employee['Starting Date']);
    array_push($ending_dates, $employee['Ending Date']);
    $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]=".$employee['Employee No_']);

    var_dump($employee['Employee No_']);
    if($previous_companies->rowCount() < 0){

        foreach ($previous_companies as $previous_exp){
            $dt = Carbon::parse($previous_exp['Starting Date']);
            $total_experience_days = $total_experience_days + $dt->diffInDays($previous_exp['Ending Date']);

            array_push($days_number_between_dates, $dt->diffInDays($previous_exp['Ending Date']));
            array_push($starting_dates, $previous_exp['Starting Date']);
            array_push($ending_dates, $previous_exp['Ending Date']);
        }

        $count=1;
        for($i = 0; $i < count($starting_dates)-1; $i++){
            $date = Carbon::parse($starting_dates[$i]);

            if($date->diffInDays($ending_dates[$i+1]) > 15){
                continue;
            }
            else{
                $count++;
            }
        }

        $go_days_exp = array_sum(array_slice($days_number_between_dates, 0, $count));

        if($employee['child_disabled'] == 'DA'){
            $go_days_base = $go_days_base + 2;
        }

        if($employee['invalid'] == 'DA'){
            foreach ($invalidity_categories as $a){
                if($a['category'] == $employee['invalid_category']){
                    $go_days_base = $go_days_base + $a['number_of_days'];
                }
            }
        }

        $year_exp = floor($total_experience_days/365);

        foreach ($year_exp_go as $y){
            if($year_exp > $y['min'] and $year_exp < $y['max']){
                $go_days_base = $go_days_base + $y['number_of_days'];
            }
        }

        //update tabelu sa brojem GO dana
        try{
            $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[vacation_days] set [vacation_days_no]=".$go_days_base. "where [Employee No_]=".$employee['Employee No_'];
            $dbq = $db->query($sql);

            $sql2 = "UPDATE [c0_intranet2_apoteke].[dbo].[total_experience] set [total_days]=".$total_experience_days. "where [Employee No_]=".$employee['Employee No_'];
            $dbq2 = $db->query($sql2);

            $sql3 = "UPDATE [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$go_days_base. " where employee_no=".$employee['Employee No_']." and year=2021";
            $dbq3 = $db->query($sql3);

        }catch(Exception $e){
            var_dump($e);
            die();
        }

        $go_days_base=20;
        $go_days_exp = 0;
        $total_experience_days = 0;
        $starting_dates = [];
        $ending_dates = [];
        $days_number_between_dates = [];
        $year_exp = 0;
    }
    else{

        die();
        try {
            $sql2 = "UPDATE [c0_intranet2_apoteke].[dbo].[total_experience] set [total_days]=".$total_experience_days. " where [Employee No_]=".$employee['Employee No_'];
            $dbq2 = $db->query($sql2);
        }catch (Exception $e){
            var_dump($e);
            die();
        }

        if ($total_experience_days % 30 === 0 and round($total_experience_days/30) < 6){

            $current_days = $db->query("select * from [c0_intranet2_apoteke].[dbo].[vacation_days] where [Employee No_]=".$employee['Employee No_'])->fetch();
            try{
                $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[vacation_days] set [vacation_days_no]=".($current_days['vacation_days_no'] + 1);
                $dbq = $db->query($sql);


            }catch(Exception $e){
                var_dump($e);
                die();
            }
        }
        $go_days_base=20;
        $go_days_exp = 0;
        $total_experience_days = 0;
        $starting_dates = [];
        $ending_dates = [];
        $days_number_between_dates = [];
    }
}
echo 'done';
die();
header('Location: ?m=work_booklet&p=all');
?>