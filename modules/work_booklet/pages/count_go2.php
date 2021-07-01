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

//ukupan broj dana iskustva
$total_experience_days = 0;

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


/************ Racunanje radnog staza**************/
foreach ($employers as $employer) {
    //var_dump($employer['Employee No_']);

    $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]=" . $employer['Employee No_']);
    $curr_dt = Carbon::parse('2021-05-10 00:00:00.000');
    $curr_xp = $curr_dt->diff('1980-01-01 00:00:00.000');
    var_dump($curr_xp);
    die();
    if ($previous_companies->rowCount() < 0) {
        foreach ($previous_companies as $previous_exp){
            //var_dump($previous_exp['id']);
            $dt = Carbon::parse($previous_exp['Ending Date']);
            //$total_experience_days = $total_experience_days + $dt->diff($previous_exp['Ending Date']);

            array_push($days_number_between_dates, $dt->diff($previous_exp['Starting Date']));
        }

        foreach ($days_number_between_dates as $d){
            $years = $years + $d->y;
            $months = $months + $d->m;
            $days = $days + $d->d;
        }


        $days_module = floor($days / 30);
        $months = $months + $days_module;
        $days = $days - $days_module*30 +1;

        $months_module = floor($months / 12);
        $years = $years + $months_module;
        $months = $months - $months_module*12;

//        var_dump($years);
//        var_dump($months);
//        var_dump($days);
        try {
            $sql = 'update [c0_intranet2_apoteke].[dbo].[work_booklet] set [previous_exp_y]=?
      ,[previous_exp_m]=?
      ,[previous_exp_d]=?
      ,[current_exp_y]=?
      ,[current_exp_m]=?
      ,[current_exp_d]=? where Active=1 and [Employee No_]='.$employer['Employee No_'];
            $sqlq = $db->prepare($sql);
            $sqlq->execute([$years, $months, $days, $curr_xp->y, $curr_xp->m, $curr_xp->m]);
        }
        catch (Exception $e){
            var_dump($e);
            die();
        }
    }
    else{
        try {
            $sql = 'update [c0_intranet2_apoteke].[dbo].[work_booklet] set
      [current_exp_y]=?
      ,[current_exp_m]=?
      ,[current_exp_d]=? where Active=1 and [Employee No_]='.$employer['Employee No_'];
            $sqlq = $db->prepare($sql);
            $sqlq->execute([$curr_xp->y, $curr_xp->m, $curr_xp->m]);
        }
        catch (Exception $e){
            var_dump($e);
            die();
        }

    }
    $years = 0;
    $months = 0;
    $days = 0;

}

//array pocetnih datuma
$starting_dates = [];

//array zavrsnih datuma
$ending_dates = [];

$employers = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1");
foreach ($employers as $employer) {
    //var_dump($employer['Employee No_']);

    $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]=" . $employer['Employee No_']);

    /******* Radnik ima prijasnje radno iskustvo ******/
    if ($previous_companies->rowCount() < 0) {
        $curr_date = Carbon::parse($employer['Ending Date']);
        $curr_xp = $curr_date->diff($employer['Starting Date']);

        $total_go_days = $go_days_base['number_of_days'];

        /******* Provjera invalidnosti ******/
        if($employer['invalid'] == "DA"){
            foreach ($invalid_categories as $i){
                if($employer['invalid_category'] == $i['category']){
                    $total_go_days = $total_go_days + $i['number_of_days'];
                }
            }
        }

        /******* Provjera dijete sa posebnim potrebama ******/
        if($employer['child_disabled'] == "DA"){
            $total_go_days = $total_go_days+2;
        }


        foreach ($previous_companies as $previous_exp){
            array_push($starting_dates, $previous_exp['Starting Date']);
            array_push($ending_dates, $previous_exp['Ending Date']);
        }
        var_dump($starting_dates);
        var_dump($ending_dates);
        var_dump($employer['Employee No_']);
        $count=0;
        for($i = 0; $i < count($starting_dates)-1; $i++){
            $date = Carbon::parse($starting_dates[$i]);

            if($date->diffInDays($ending_dates[$i+1]) > 15){
                continue;
            }
            else{
                $count++;
            }
        }
        $years = 0;
        $months = 0;
        $days = 0;
        //var_dump($count);
        for ($x = 0; $x <= $count; $x++) {
            $date = Carbon::parse($starting_dates[$x]);
            $pre_xp = $date->diff($ending_dates[$x]);
            $years = $pre_xp->y + $years;
            $months = $pre_xp->m + $months;
            $days = $pre_xp->d + $days;
        }

        $years = $employer['current_exp_y'] + $years;
        $months = $employer['current_exp_m'] + $months;
        $days = $employer['current_exp_d'] + $days;

        var_dump($years);
        var_dump($months);
        var_dump($days);
        var_dump('----------------------');

        $days_module = floor($days / 30);
        $months = $months + $days_module;
        $days = $days - $days_module*30;

        $months_module = floor($months / 12);
        $years = $years + $months_module;
        $months = $months - $months_module*12;

        var_dump($years);
        var_dump($months);
        var_dump($days);

        /******* Provjera godine radnog iskustva ******/
        $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
        foreach ($go_work_experience as $gwe){
            if ($years > $gwe['min'] and $years < $gwe['max']){
                $total_go_days = $total_go_days + $gwe['number_of_days'];
            }
        }

        try {
            $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer['Employee No_'];
            $dbq = $db->query($sql);

            $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer['Employee No_']." and year=".$current_year;
            $dbq = $db->query($sql);

        }
        catch (Exception $e){
            var_dump($e);
            die();
        }


    }

    /******* Radnik nema prijasnje radno iskustvo ******/
    else{
        if ($employer['current_exp_m'] > 6){
            $curr_dt = Carbon::parse($employer['Ending Date']);
            $curr_xp = $curr_dt->diff($employer['Starting Date']);

            $total_go_days = $go_days_base['number_of_days'];

            /******* Provjera invalidnosti ******/
            if($employer['invalid'] == "DA"){
                foreach ($invalid_categories as $i){
                    if($employer['invalid_category'] == $i['category']){
                        $total_go_days = $total_go_days + $i['number_of_days'];
                    }
                }
            }

            /******* Provjera dijete sa posebnim potrebama ******/
            if($employer['child_disabled'] == "DA"){
                $total_go_days = $total_go_days+2;
            }

            /******* Provjera godine radnog iskustva ******/
            $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
            foreach ($go_work_experience as $gwe){
                if ($employer['current_exp_y'] > $gwe['min'] and $employer['current_exp_y'] < $gwe['max']){
                    $total_go_days = $total_go_days + $gwe['number_of_days'];
                }
            }

            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer['Employee No_'];
                $dbq = $db->query($sql);

                $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer['Employee No_']." and year=".$current_year;
                $dbq = $db->query($sql);

            }
            catch (Exception $e){
                var_dump($e);
                die();
            }

        }

        /******* Novi radnik bez iskustva ******/
        else {
            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$employer['current_exp_m']." where [Employee No_]=".$employer['Employee No_'];
                $dbq = $db->query($sql);

            }
            catch (Exception $e){
                var_dump($e);
                die();
            }
        }

    }
}

//header('Location: ?m=work_booklet&p=all');
