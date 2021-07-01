<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Carbon\Carbon;
error_reporting(0);
function count_go($employee_no){
    $starting_dates = [];
    $ending_dates = [];
    $coefficients = [];

    global $db;
    $previous_year = date('Y');
    //TODO ubaciti entitet usera
    /**** Zakonski broj dana *****/
    $go_days_base = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where region='FBIH' and year =".$previous_year)->fetch();

    /***** Broj dana na osnovu radnog iskustva *****/
    $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");

    /**** Kategorije invaliditeta ****/
    $invalid_categories = $db->query('select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go]');

    $employer_curr = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1 and [Employee No_]=" . $employee_no)->fetch();
    $total_days_for_go = 0;

    $curr_date = Carbon::parse($employer_curr['Ending Date']);
    $curr_xp = round($curr_date->diffInDays($employer_curr['Starting Date']) * $employer_curr['Coefficient']);

    $curr_years = floor($curr_xp / 365);
    $curr_months = floor(($curr_xp - $curr_years * 365) / 30);
    $curr_days = $curr_xp - $curr_years * 365 - 30 * $curr_months;

    $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0 and [Employee No_]=" . $employee_no." order by [Starting Date] desc");

    /******* Radnik ima prijasnje radno iskustvo ******/
    if ($previous_companies->rowCount() < 0) {
        $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where  [Employee No_]=" . $employee_no." order by [Starting Date] desc")->fetchAll();
        $total_go_days = 0;
        if (Carbon::parse($previous_companies[0]['Starting Date'])->diffInDays($previous_companies[1]['Ending Date']) > 15){
            $date_diff_curr = Carbon::parse($previous_companies[0]['Starting Date'])->diff($previous_companies[0]['Ending Date']);
            if($date_diff_curr->y > 1 or $date_diff_curr->m >= 6){
                if(date('Y', strtotime($employer_curr['Starting Date'])) == date('Y', strtotime($employer_curr['Ending Date']))-1){
                    try {
                        $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_danaPG=0 where employee_no=".$employer_curr['Employee No_']." and year=".date('Y', strtotime($employer_curr['Ending Date']));
                        $dbq=$db->query($sql);
                    }catch (Exception $e){
                        var_dump($e);
                        die();
                    }
                }
                $curr_dt = Carbon::parse($employer_curr['Ending Date']);
                $curr_xp = $curr_dt->diff($employer_curr['Starting Date']);

                $total_go_days = $go_days_base['number_of_days'];

                /******* Provjera invalidnosti ******/
                if($employer_curr['invalid'] == "DA"){
                    foreach ($invalid_categories as $i){
                        if($employer_curr['invalid_category'] == $i['category']){
                            $total_go_days = $total_go_days + $i['number_of_days'];
                        }
                    }
                }

                /******* Provjera dijete sa posebnim potrebama ******/
                if($employer_curr['child_disabled'] == "DA"){
                    $total_go_days = $total_go_days+2;
                }

                /******* Provjera godine radnog iskustva ******/
                $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
                foreach ($go_work_experience as $gwe){
                    if ($employer_curr['previous_exp_y'] > $gwe['min'] and $employer_curr['previous_exp_y'] < $gwe['max']){
                        $total_go_days = $total_go_days + $gwe['number_of_days'];
                    }
                }

                try {
                    $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer_curr['Employee No_'];
                    $dbq = $db->query($sql);

                    $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                    $dbq = $db->query($sql2);

                }
                catch (Exception $e){
                    var_dump($e);
                    die();
                }
            }
            else{
                if (date('Y', strtotime($employer_curr['Starting Date'])) < date('Y', strtotime($employer_curr['Ending Date']))){
                    $prev_year_ending = date('Y').'-01-01';
                    $prev_year = Carbon::parse($employer_curr['Starting Date'])->diff($prev_year_ending);
                    $curr_year_start = date('Y').'-01-01';
                    $curr_year = Carbon::parse($employer_curr['Ending Date'])->diff($curr_year_start);

                    try {
                        $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_danaPG=".$prev_year->m." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                        $dbq = $db->query($sql2);

                        $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$curr_year->m." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                        $dbq = $db->query($sql2);

                    }catch (Exception $e){
                        var_dump($e);
                        die();
                    }
                }
                else{
                    try {
                        if($employer_curr['previous_exp_m'] == null){
                            $new_emp = 0;
                        }
                        else{
                            $new_emp = $employer_curr['previous_exp_m'];
                        }
                        $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$new_emp." where [Employee No_]=".$employer_curr['Employee No_'];
                        $dbq = $db->query($sql);

                        $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$new_emp." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                        $dbq = $db->query($sql2);

                    }
                    catch (Exception $e){
                        var_dump($e);
                        die();
                    }
                }
            }
        }
        else{
            $previous_companies = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where  [Employee No_]=" . $employee_no." order by [Starting Date] desc")->fetchAll();

            /******* Provjera invalidnosti ******/
            if($employer_curr['invalid'] == "DA"){
                foreach ($invalid_categories as $i){
                    if($employer_curr['invalid_category'] == $i['category']){
                        $total_go_days = $total_go_days + $i['number_of_days'];
                    }
                }
            }

            /******* Provjera dijete sa posebnim potrebama ******/
            if($employer_curr['child_disabled'] == "DA"){
                $total_go_days = $total_go_days+2;
            }
            $prev_comp = $previous_companies;

            $c = 0;
            for ($i = 0 ; $i <= count($prev_comp)-1; $i++){
                $c++;
                $date = Carbon::parse($prev_comp[$i]['Starting Date']);
                if ($date->diffInDays($prev_comp[$i+1]['Ending Date']) > 15) {

                    $td = 0;
                    $dais = $date->diffInDays($prev_comp[$i]['Ending Date']);
                    $td = round($dais * $prev_comp[$i]['Coefficient']);
                    $total_days_for_go = $total_days_for_go + $td;
                    if ($total_days_for_go > 0){
                        break;
                    }
                } else {
                    $td = 0;
                    $dais = $date->diffInDays($prev_comp[$i]['Ending Date']);
                    $td = round($dais * $prev_comp[$i]['Coefficient']);
                    $total_days_for_go = $total_days_for_go + $td;


                }
            }

            if (date('Y', strtotime($previous_companies[$c-1]['Starting Date'])) == date('Y', strtotime("-1 year"))){
                try {
                    $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_danaPG=0 where employee_no=".$employer_curr['Employee No_']." and year=".date('Y', strtotime($employer_curr['Ending Date']));
                    $dbq=$db->query($sql);
                }catch (Exception $e){
                    var_dump($e);
                    die();
                }
            }

            $years = floor($total_days_for_go/365);
            $months = floor(($total_days_for_go - $years*365)/30);
            $days = $total_days_for_go-$years*365- 30 * $months + 1;

            if ($total_days_for_go > 180){
                $total_go_days = $total_go_days + $go_days_base['number_of_days'];
            }else{
                $total_go_days = $months;
            }

            /******* Provjera godine radnog iskustva ******/
            $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
            foreach ($go_work_experience as $gwe){
                if ($years > $gwe['min'] and $years < $gwe['max']){
                    $total_go_days = $total_go_days + $gwe['number_of_days'];
                }
            }

            try {

                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer_curr['Employee No_'];
                $dbq = $db->query($sql);

                $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;

                $dbq = $db->query($sql2);
            }
            catch (Exception $e){
                var_dump($e);
                die();
            }

        }

    }

    /******* Radnik nema prijasnje radno iskustvo ******/
    else{
        $date_diff = Carbon::parse($employer_curr['Starting Date'])->diff($employer_curr['Ending Date']);


        if(date('Y', strtotime($employer_curr['Starting Date'])) == date('Y', strtotime($employer_curr['Ending Date']))-1){
            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_danaPG=0 where employee_no=".$employer_curr['Employee No_']." and year=".date('Y', strtotime($employer_curr['Ending Date']));
                $dbq=$db->query($sql);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
        }

        if ($date_diff->y >=1 or $date_diff->m >=6){


            $curr_dt = Carbon::parse($employer_curr['Ending Date']);
            $curr_xp = $curr_dt->diff($employer_curr['Starting Date']);

            $total_go_days = $go_days_base['number_of_days'];

            /******* Provjera invalidnosti ******/
            if($employer_curr['invalid'] == "DA"){
                foreach ($invalid_categories as $i){
                    if($employer_curr['invalid_category'] == $i['category']){
                        $total_go_days = $total_go_days + $i['number_of_days'];
                    }
                }
            }

            /******* Provjera dijete sa posebnim potrebama ******/
            if($employer_curr['child_disabled'] == "DA"){
                $total_go_days = $total_go_days+2;
            }

            /******* Provjera godine radnog iskustva ******/
            $go_work_experience = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");
            foreach ($go_work_experience as $gwe){
                if ($employer_curr['previous_exp_y'] > $gwe['min'] and $employer_curr['previous_exp_y'] < $gwe['max']){
                    $total_go_days = $total_go_days + $gwe['number_of_days'];
                }
            }

            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$total_go_days." where [Employee No_]=".$employer_curr['Employee No_'];
                $dbq = $db->query($sql);

                $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$total_go_days." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                $dbq = $db->query($sql2);

            }
            catch (Exception $e){
                var_dump($e);
                die();
            }

        }

        /******* Novi radnik bez iskustva ******/
        else {
            if (date('Y', strtotime($employer_curr['Starting Date'])) < date('Y', strtotime($employer_curr['Ending Date']))){
                $prev_year_ending = date('Y').'-01-01';
                $prev_year = Carbon::parse($employer_curr['Starting Date'])->diff($prev_year_ending);
                $curr_year_start = date('Y').'-01-01';
                $curr_year = Carbon::parse($employer_curr['Ending Date'])->diff($curr_year_start);

                try {
                    $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_danaPG=".$prev_year->m." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                    $dbq = $db->query($sql2);

                    $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$curr_year->m." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                    $dbq = $db->query($sql2);

                }catch (Exception $e){
                    var_dump($e);
                    die();
                }
            }
            else{
                try {
                    if($employer_curr['previous_exp_m'] == null){
                        $new_emp = 0;
                    }
                    else{
                        $new_emp = $employer_curr['previous_exp_m'];
                    }
                    $sql = "update [c0_intranet2_apoteke].[dbo].[vacation_days] set vacation_days_no=".$new_emp." where [Employee No_]=".$employer_curr['Employee No_'];
                    $dbq = $db->query($sql);

                    $sql2 = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_dana=".$new_emp." where employee_no=".$employer_curr['Employee No_']." and year=".$previous_year;
                    $dbq = $db->query($sql2);

                }
                catch (Exception $e){
                    var_dump($e);
                    die();
                }
            }

        }

    }
}