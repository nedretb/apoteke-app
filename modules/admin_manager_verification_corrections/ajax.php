<?php

require_once '../../configuration.php';
include_once $root . '/modules/admin_hourly_rate/functions.php';
if (DEBUG) {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

}


if (isset($_POST['request'])) {


    if ($_POST['request'] == 'request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        $data = "INSERT INTO  " . $portal_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive) VALUES (?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")), date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to)), '0', 'GO', '0'));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'travel-request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        $data = "INSERT INTO  " . $portal_travel_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive,country,travel_route,comment,total_cost) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")),
                date('Y-m-d', strtotime($from)),
                date('Y-m-d', strtotime($to)),
                '0',
                'SLUŽBENI PUT',
                '0',
                $_POST['country'], $_POST['travel_route'], $_POST['comment'], $_POST['total_cost']));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'year-add') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $_POST['year'] . "'")->rowCount();
        //echo $check;
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Godinu koju ste odabrali već postoji!') . '</div><br/>';

        } else {

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . " ");

            $total = $get2->rowCount();


            foreach ($query as $item) {

                //echo $absence_id;
                $absence_year_id = $item['user_id'];


                $data = "INSERT INTO  " . $portal_hourlyrate_year . " (
     user_id,year) VALUES (?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $absence_year_id,
                        $_POST['year']
                    )
                );
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

            }


        }
    }


    if ($_POST['request'] == 'month-add') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE month='" . $_POST['month'] . "' AND year_id='" . $_POST['year'] . "'")->rowCount();
        //echo $check;
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Mjesec koji ste odabrali već postoji!') . '</div><br/>';

        } else {


            $_user = _user(_decrypt($_SESSION['SESSION_USER']));

            $query_month = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get_month = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $yearcurr = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_POST['year'] . "'");
            $total = $get_month->rowCount();
            foreach ($yearcurr as $value2) {
                $absence_year = $value2['year'];
            }

            foreach ($query_month as $item) {

                $month = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $absence_year . "'");

                $absence_id_month = $item['user_id'];
                foreach ($month as $value) {
                    $absence_month = $value['id'];
                }

                $_user = _user(_decrypt($_SESSION['SESSION_USER']));

                $data = "INSERT INTO  " . $portal_hourlyrate_month . " (id,
      user_id,year_id,month) VALUES (?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['month'],
                        $absence_id_month,
                        $absence_month,
                        $_POST['month']
                    )
                );


                $query_calendar = $db->query("SELECT [day],[weekday] FROM  " . $portal_calendar . "  where [month]='" . $_POST['month'] . "'
   and  [year]='" . $absence_year . "'");

                foreach ($query_calendar as $cal) {
                    $day = $cal ['day'];
                    $weekday = $cal ['weekday'];
                    $data = "INSERT INTO  " . $portal_hourlyrate_day . "  (
      user_id,year_id,month_id,day,hour,weekday) VALUES (?,?,?,?,?,?)";

                    $res = $db->prepare($data);

                    {
                        $res->execute(
                            array(
                                $absence_id_month,
                                $absence_month,
                                $_POST['month'],
                                $day,
                                '8',
                                '5',
                                $weekday,
                            )
                        );
                    }

                }
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
            }

        }

    }


    if ($_POST['request'] == 'parent-day-add') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $query = $db->query("SELECT [day] FROM  " . $portal_hourlyrate_day . "   where  month_id='$getMonth'");

        foreach ($query as $item) {

            if ($item['day'] >= $FromDay && $item['day'] <= $ToDay) {

                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
      where day=?
      and month_id=?
     and year_id=?";


                $res = $db->prepare($data);
                $res->execute(
                    array(

                        $_POST['hour'],
                        $_POST['status'],
                        $item['day'],
                        $getMonth,
                        $getYear
                    )
                );

            }
        }
        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }


    if ($_POST['request'] == 'daysel') {
        $YearSel = $_POST['YearSel'];
        $MonthSel = $_POST['MonthSel'];
    }

    if ($_POST['request'] == 'user-day-check') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];
        $religious_holiday = $db->query("SELECT count(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE KindofDay='CHOLIDAY'
    AND year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND day between '" . $_POST['FromDay'] . "'  and '" . $_POST['ToDay'] . "' ");
        foreach ($religious_holiday as $valuere) {
            $reho = $valuere['Kind_ofDay'];
            if ($reho > 0) {
                echo '<a href="' . $url . '/modules/admin_hourly_rate/ajax.php"data-widget="edit" data-id="user_day:month-' . $_POST['get_month'] . '" data-text="' . __('Ima praznik') . '" class="text-danger pull-right"><i class="ion-ios-checkmark-outline"></i></a>';
            }
        }
    }


    if ($_POST['request'] == 'user-day-edit') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];

        $status2 = $_POST['status'];
        if (($_POST['FromDay'] > $_POST['ToDay']))
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Datum Od mora biti manji od datuma Do') . '</div>'; else {
            $getMonth = $_POST['get_month'];
            $getYear = $_POST['get_year'];

            //denis
            $get_count = $db->query("SELECT count(KindOfDay) as countHol FROM  " . $portal_hourlyrate_day . "  WHERE KindOfDay='CHOLIDAY' and month_id=" . $getMonth . " and year_id=" . $getYear . " and Day>=" . $FromDay . " and Day<=" . $ToDay . "");
            $countHoliday = $get_count->fetch();
            $countHol = $countHoliday ['countHol'];


            $emp = $db->query("SELECT employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  ");
            foreach ($emp as $valueemp) {
                $empid = $valueemp['employee_no'];
            }
            $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

            $holiday_go = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='CHOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");

            foreach ($holiday_go as $holidaygo) {
                $totalhogo = $holidaygo['Kind_ofDay'];
            }

            $holiday_go2 = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='HOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");

            $religious_holiday = $db->query("SELECT COUNT(KindofDay) as Kind_ofDay FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND KindofDay='CHOLIDAY'  AND day between '" . $FromDay . "' and '" . $ToDay . "'");
            foreach ($religious_holiday as $religiousholiday) {
                $totalreho = $religiousholiday['Kind_ofDay'];
            }

            foreach ($holiday_go2 as $holidaygo) {
                $totalhogo2 = $holidaygo['Kind_ofDay'];
            }


            $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='18' or status='19' or status='20') AND (date_NAV is null)");
            $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND day between '" . $FromDay . "' and '" . $ToDay . "'");
            $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
            $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
            $blooddonor = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
            $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='34') AND (date_NAV is null)");
            $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='33')) AND (date_NAV is null)");
            $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
            $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
            $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' and month_id='" . $getMonth . "'and employee_no='" . $empid . "'
   and status='19'  ");
            $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
            $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
            $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
            $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
            $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
            $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
            $curruP_7 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  and weekday<>'6' AND weekday<>'7' and [day] between '" . $FromDay . "' and '" . $ToDay . "'");
            $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
            $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
            $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
            $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
            $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
            $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
            $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
            foreach ($go as $valuego) {
                $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
                $brdanaPG = $valuego['Br_danaPG'];
                $ostaloPG = $valuego['Br_dana_ostaloPG'];
                $iskoristeno = $valuego['Br_dana_iskoristeno'];
                $ostalo = $valuego['Br_dana_ostalo'];
                $brdana = $valuego['Br_dana'];
                $totalkrv = $valuego['Blood_days'];
                $totaldeath = $valuego['S_1_used'];
                $iskoristenokrv = $valuego['P_6_used'];
                $propaloGO = $valuego['G_2 not valid'];
            }

            foreach ($blooddonor as $blood_donor) {
                $iskorenokrv = $blood_donor['sum_hour'];
                $krvukupno = ($iskorenokrv / 8) + $iskoristenokrv;
                $totalkrvloost = $totalkrv - $krvukupno;
            }


            foreach ($askedgo as $valueasked) {
                $askeddays = $valueasked['sum_hour'];
                $totalasked = $askeddays / 8;
            }
            foreach ($currgo as $valuecurrgo) {
                $iskoristenocurr = $valuecurrgo['sum_hour'];;
                $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
                $totalgoost = $brdana - $iskoristenototal;
            }
            foreach ($currgoPG as $valuecurrgoPG) {
                $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
                $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
                $totalgoostPG = $brdanaPG - $iskoristenototalPG;
                $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
                $ukupnogoost = $totalgoost + $totalgoostPG;
            }
            foreach ($pcm as $valuepcm) {
                $totalpcm = $valuepcm['Candelmas_paid_total'];
                $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
                $brdanapcm = $valuepcm['Candelmas_paid'];
            }
            foreach ($upcm as $valueupcm) {
                $totalupcm = $valueupcm['Candelmas_unpaid_total'];
                $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
                $brdanaupcm = $valueupcm['Candelmas_unpaid'];
            }
            foreach ($P_1 as $valueP_1) {
                $iskoristenoP_1 = $valueP_1['P_1_used'];
            }
            foreach ($P_1a as $valueP_1a) {
                $totalP_1 = $valueP_1a['allowed_days'];
            }
            foreach ($P_2 as $valueP_2) {
                $iskoristenoP_2 = $valueP_2['P_2_used'];
            }
            foreach ($P_2a as $valueP_2a) {
                $totalP_2 = $valueP_2a['allowed_days'];
            }
            foreach ($P_3 as $valueP_3) {
                $iskoristenoP_3 = $valueP_3['P_3_used'];
            }
            foreach ($P_3a as $valueP_3a) {
                $totalP_3 = $valueP_3a['allowed_days'];
            }
            foreach ($P_4 as $valueP_4) {
                $iskoristenoP_4 = $valueP_4['P_4_used'];
            }
            foreach ($P_4a as $valueP_4a) {
                $totalP_4 = $valueP_4a['allowed_days'];
            }
            foreach ($P_5 as $valueP_5) {
                $iskoristenoP_5 = $valueP_5['P_5_used'];
            }
            foreach ($P_5a as $valueP_5a) {
                $totalP_5 = $valueP_5a['allowed_days'];
            }
            foreach ($P_6 as $valueP_6) {
                $iskoristenoP_6 = $valueP_6['P_6_used'];
            }
            foreach ($P_6a as $valueP_6a) {
                $totalP_6 = $valueP_6a['allowed_days'];
            }
            foreach ($currpcm as $valuecurrpcm) {
                $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
                $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
                $totalpcmost = $brdanapcm - $iskoristenototalpcm;
            }
            foreach ($currupcm as $valuecurrupcm) {
                $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
                $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
                $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
            }
            foreach ($checkva as $checkvalueva) {
                $sum = $checkvalueva['sum_hour'];
            }
            foreach ($curruP_1 as $valuecurrP_1) {
                $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
                $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
                $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
            }
            foreach ($curruP_2 as $valuecurrP_2) {
                $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
                $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
                $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
            }
            foreach ($curruP_3 as $valuecurrP_3) {
                $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
                $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
                $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
            }
            foreach ($curruP_4 as $valuecurrP_4) {
                $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
                $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
                $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
            }
            foreach ($curruP_5 as $valuecurrP_5) {
                $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
                $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
                $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
            }
            foreach ($curruP_6 as $valuecurrP_6) {
                $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];;
                $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
                $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
            }

            foreach ($curruP_7 as $valuecurrP_7) {
                $iskoristenocurrP_7 = $valuecurrP_7['count_day'];
            }
            foreach ($plo as $valueplo) {
                $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_7_used'];
                $totalplo = $valueplo['Br_dana_PLO'];
            }

            foreach ($currplo as $valuecurrplo) {
                $iskoristenocurrplo = $valuecurrplo['sum_hour'];
                $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
                $totalploost = $totalplo - $iskoristenototalplo;
            }

            if ($countHol > 0 and $_POST['try'] == '1') {
                echo _message('holiday_change');
            } else {
                if ($totalasked > $totalgoost and $_POST['status'] == '18') {
                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
                } else {
                    if ($totalasked > $totalgoostPG and $_POST['status'] == '19') {
                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
                    } else {
                        if ((($totalasked > 5) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19')) {
                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana GO iz prošle godine!') . '</div>';
                        } else {
                            if ((($totalasked > 5) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19') or ($propaloGO == 1)) {
                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!') . '</div>';
                            } else {
                                if (($totalasked > $totalpcmost) and ($_POST['status'] == '21')) {
                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
                                } else {
                                    if (($totalasked > $totalupcmost) and ($_POST['status'] == '22')) {
                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
                                    } else {
                                        if ((($totalasked > $totalP_1ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '27')) {
                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                        } else {
                                            if ((($totalasked > $totalP_2ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '28')) {
                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                            } else {
                                                if ((($totalasked > $totalP_3ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '29')) {
                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                } else {
                                                    if ((($totalasked > $totalP_4ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '30')) {
                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                    } else {
                                                        if ((($totalasked > $totalP_5ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '31')) {
                                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                                        } else {
                                                            if (($totalasked > 5) and ($_POST['status'] == '30' or $_POST['status'] == '34')) {
                                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!') . '</div>';
                                                            } else {
                                                                if ((($totalasked > 1) or ($totalasked > $totalkrvloost)) and ($_POST['status'] == '32')) {
                                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 1 dan za darivanje krvi!') . '</div>';
                                                                } else {

                                                                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
      where day BETWEEN ? and ?
    and month_id=?
    and year_id=?
    ";

                                                                    $res = $db->prepare($data);
                                                                    $res->execute(
                                                                        array(
                                                                            $_POST['hour'],
                                                                            $_POST['status'],
                                                                            $FromDay,
                                                                            $ToDay,
                                                                            $getMonth,
                                                                            $getYear


                                                                        )
                                                                    );
                                                                    if ($res->rowCount() > 0) {
                                                                        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
                                                                    } else {
                                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
                                                                    }
                                                                }

                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    if ($_POST['request'] == 'day-edit') {

        $Day = $_POST['day'];
        $this_id = $_POST['request_id'];
        $status2 = $_POST['status'];

        $try = $_POST['try'];
        $get_old_status = $db->query("SELECT status, KindOfDay FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $old_status = $get_old_status->fetch();


        $emp = $db->query("SELECT employee_no,year_id,month_id FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $check = $db->query("SELECT year_id,month_id,employee_no FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
        foreach ($check as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month = $checkvalue['month_id'];
            $filter_emp = $checkvalue['employee_no'];
        }
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $filter_year . "' and month_id='" . $filter_month . "'and employee_no='" . $filter_emp . "'
   and status='19'  ");

        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
            $getYear = $valueemp['year_id'];
            $getMonth = $valueemp['month_id'];
        }

        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='32')");
        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='18' or status='19' or status='20') AND (date_NAV is null)");
        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
        $blooddonor = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='34') AND (date_NAV is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' and month_id='" . $getMonth . "'and employee_no='" . $empid . "'
   and status='19'  ");

        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='33')) AND (date_NAV is null)");

        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
     and weekday<>'6' AND weekday<>'7' AND (status='30')");
        $curruP_8 = $db->query("SELECT count(day) as count_day FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
     and weekday<>'6' AND weekday<>'7' AND (status='19')");

        foreach ($askedgo as $valueasked) {
            $askeddays = $valueasked['sum_hour'];
            $totalasked = $askeddays / 8;
        }

        foreach ($go as $valuego) {
            $totalgo = $valuego['Ukupno'];
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $iskoristeno = $valuego['Br_dana_iskoristeno'];
            $brdana = $valuego['Br_dana'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristenokrv = $valuego['P_6_used'];
            $totalkrv = $valuego['Blood_days'];
            $propaloGO = $valuego['G_2 not valid'];
        }


        foreach ($blooddonor as $blood_donor) {
            $iskorenokrv = $blood_donor['sum_hour'];
            $krvukupno = ($iskorenokrv / 8) + $iskoristenokrv;
            $totalkrvloost = $totalkrv - $krvukupno;
        }


        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }


        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
            $totalgoostPG = $brdanaPG - $iskoristenototalPG;
            $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
            $ukupnogoost = $totalgoost + $totalgoostPG;
        }
        foreach ($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm = $valuepcm['Candelmas_paid'];
        }
        foreach ($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm = $valueupcm['Candelmas_unpaid'];
        }
        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }
        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }

        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['count_day'];

        }
        foreach ($curruP_8 as $valuecurrP_8) {
            $iskoristenocurrP_8 = $valuecurrP_8['count_day'];

        }
        foreach ($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach ($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach ($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach ($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach ($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach ($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach ($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach ($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach ($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach ($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach ($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach ($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }

        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }
        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if (($old_status['status'] != '5' or $old_status['KindOfDay'] == 'CHOLIDAY') and $_POST['try'] == '1') {
            echo _message('unusual_change');
        } else {
            if (($totalgoost - 1 < 0) and $_POST['status'] == '18') {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            } else {
                if (($totalgoostPG - 1 < 0) and $_POST['status'] == '19') {
                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
                } else {
                    if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
                    } else {
                        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
                        } else {
                            if ((($totalP_1ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '27')) {
                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                            } else {
                                if ((($totalP_2ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '28')) {
                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                } else {
                                    if ((($totalP_3ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '29')) {
                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                    } else {
                                        if ((($totalP_4ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '30')) {
                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                        } else {
                                            if ((($totalP_5ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '31')) {
                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
                                            } else {
                                                if ((($totalkrvloost - 1 < 0) or ($totalP_6ost - 1 < 0)) and ($_POST['status'] == '32')) {
                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 1 dan za darivanje krvi!') . '</div>';
                                                } else {
                                                    if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog odsustva !') . '</div>';
                                                    } else {
                                                        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
                                                            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog odsustva !') . '</div>';
                                                        } else {
                                                            foreach ($checkva as $checkvalueva) {
                                                                $sum = $checkvalueva['sum_hour'];
                                                            }


                                                            if ((($iskoristenocurrP_8 - 1 >= 4) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19')) {
                                                                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana GO iz prošle godine!') . '</div>';
                                                            } else {

                                                                if ((($iskoristenocurrP_8 - 1 >= 4) and ($_POST['status'] == '19')) or ((($sum / 8) + 1 > 5) and $_POST['status'] == '19') or ($propaloGO == 1)) {
                                                                    echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!') . '</div>';
                                                                } else {
                                                                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      day = ?,
      hour = ?,
      status = ?
      WHERE id = ?
     ";

                                                                    $res = $db->prepare($data);
                                                                    $res->execute(
                                                                        array(
                                                                            $_POST['day'],
                                                                            $_POST['hour'],
                                                                            $_POST['status'],
                                                                            $this_id
                                                                        )
                                                                    );
                                                                    if ($res->rowCount() == 1) {
                                                                        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
                                                                    } else {
                                                                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    if ($_POST['request'] == 'remove-requests_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM requests WHERE request_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-year_remove') {

        $this_id = explode('-', $_POST['request_id']);
        $this_id = $this_id[1];

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
        $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
        $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  where id=$this_id");

        $total = $get2->rowCount();


        foreach ($query as $item) {

            //echo $absence_id;
            $absence_year_id = $item['user_id'];


            $data = "DELETE FROM  " . $portal_hourlyrate_year . "  WHERE id = ?";
            $delete = $db->prepare($data);
            $delete->execute(array($absence_year_id));
            if ($delete) {
                echo 1;


            }
        }
    }


    if ($_POST['request'] == 'month-remove-month') {
        $this_id = $_POST['request_id'];

        $yearcurr = $db->query("SELECT [month] FROM  " . $portal_hourlyrate_month . "  WHERE id= $this_id");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $query_month = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
        $get_month = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
        $yearcurr = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_POST['year'] . "'");
        $total = $get_month->rowCount();
        foreach ($yearcurr as $value2) {
            $absence_year = $value2['year'];
        }

        foreach ($query_month as $item) {

            $month = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $absence_year . "'");
            $absence_id_month = $item['user_id'];
            foreach ($month as $value) {
                $absence_month = $value['id'];
            }

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));

            $data = "DELETE FROM  " . $portal_hourlyrate_month . "  where 
   (id=?
      and user_id=?
    and year_id=?
    and ,month =?)";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['month'],
                    $absence_id_month,
                    $absence_month,
                    $_POST['month']
                )
            );


            $query_calendar = $db->query("SELECT [day],[weekday] FROM  " . $portal_calendar . "  where [month]='" . $_POST['month'] . "'
   and  [year]='" . $absence_year . "'");
            foreach ($query_calendar as $cal) {
                $day = $cal ['day'];
                $weekday = $cal ['weekday'];
                $data = "DELETE FROM  " . $portal_hourlyrate_day . "  (where
      user_id=?
    and year_id=?
    and month_id=?
    and day=?
    and hour=? 
    and status=?)";

                $res = $db->prepare($data);

                {
                    $res->execute(
                        array(
                            $absence_id_month,
                            $absence_month,
                            $_POST['month'],
                            $day,
                            '8',
                            '5',

                        )
                    );
                }

            }
        }
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno obrisane!') . '</div>';
        }

    }

    if ($_POST['request'] == 'remove-day_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM hourlyrate_day WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-requests_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_requests . "  SET
        is_archive = ?
        WHERE request_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'remove-tasks_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_archive = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'accept-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_accepted = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'completed-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_finished = ?,
        date_finished = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d'),
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'task-comment') {

        $data3 = "INSERT INTO  " . $portal_comments . "  (
      type,user_id,comment,date_created,comment_on) VALUES (?,?,?,?,?)";

        $res3 = $db->prepare($data3);
        $res3->execute(
            array(
                'task',
                $_POST['user_id'],
                $_POST['comment'],
                date('Y-m-d H:i:s'),
                $_POST['comment_on']
            )
        );
        if ($res3->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'proc-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        status = ?,
        date_completed = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                date('y-m-d H:i:s', strtotime("now")),
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'count-tasks') {

        $this_id = $_POST['request_id'];
        $total_0 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id'")->rowCount();
        $total_1 = $db->query("SELECT Count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id' AND status='1'")->rowCount();

        if ($total_1 == $total_0) {
            echo 'yes';
        } else {
            echo 'no';
        }

    }


    if ($_POST['request'] == 'comments') {

        $user_id = $_POST['user'];
        $parent_id = $_POST['parent'];

        $comments = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");
        $comments_no = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");

        if ($comments_no->rowCount() < 0) {

            $user = _user($user_id);
            $parent = _user($parent_id);

            foreach ($comments as $item) {
                echo '<div class="comment">';
                if ($item['user_id'] == $user_id) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-9"><div class="text-u">';
                    echo $item['comment'];
                    echo '</div><small class="text-muted">' . date('d.m.Y H:i', strtotime($item['date_created'])) . '</small></div>';
                    echo '<div class="col-xs-3 text-center">';
                    if ($user['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $user['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $user['fname'] . ' ' . $user['lname'] . '</small>';
                    echo '</div>';
                    echo '</div>';
                } else if ($item['user_id'] == $parent_id) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-3 text-center">';
                    if ($parent['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $parent['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $parent['fname'] . ' ' . $parent['lname'] . '</small>';
                    echo '</div>';
                    echo '<div class="col-xs-9"><div class="text-p">';
                    echo $item['comment'];
                    echo '</div><small class="pull-right text-muted">' . date('d.m.Y H:i', strtotime($item['date_created'])) . '</small></div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }

    }


    if ($_POST['request'] == 'profile-edit') {

        if ($_POST['f4'] != '') {
            $pass = md5($_POST['f4']);
        } else {
            $pass = $_POST['oldpass'];
        }

        $check = $db->query("SELECT * FROM  " . $portal_users . "  WHERE username='" . $_POST['username'] . "'");
        if ($check->rowCount() > 0) {
            if ($_POST['username'] == $_POST['oldusername']) {
                $username = $_POST['username'];
            } else {
                $username = false;
            }
        } else {
            $username = $_POST['username'];
        }

        if (isset($_FILES['media_file'])) {
            if (is_uploaded_file($_FILES['media_file']['tmp_name'])) {
                $p_photo = preg_replace('/[^\w\._]+/', '_', $_FILES['media_file']['name']);
                $p_photo = _checkFile($_uploadRoot . '/', $p_photo);
                $file = $_uploadRoot . '/' . $p_photo;
                if (copy($_FILES['media_file']['tmp_name'], $file)) {
                    unlink($_uploadRoot . '/' . $_POST['oldimage']);
                }
            } else {
                $p_photo = $_POST['oldimage'];
            }
        } else {
            $p_photo = $_POST['oldimage'];
        }

        if ($username != false) {

            $this_id = $_POST['request_id'];
            $data = "UPDATE  " . $portal_users . "  SET
        username = ?,
        password = ?,
        email = ?,
        image = ?,
        fname = ?,
        lname = ?,
        address = ?,
        zip = ?,
        city = ?,
        country = ?,
        phone = ?,
        lang = ?
        WHERE user_id = ?";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['username'],
                    $pass,
                    $_POST['email'],
                    $p_photo,
                    $_POST['fname'],
                    $_POST['lname'],
                    $_POST['address'],
                    $_POST['zip'],
                    $_POST['city'],
                    $_POST['country'],
                    $_POST['phone'],
                    $_POST['lang'],
                    $this_id
                )
            );
            if ($res->rowCount() == 1) {
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">' . __('Informacije su uspješno spašene!') . '</div>"}';
            }

        } else {

            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">' . __('Korisničko ime je zauzeto. Molimo pokušajte sa nekim drugim.') . '</div>"}';

        }

    }

    if ($_POST['request'] == 'task-review') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_user_reviewed = ?,
        user_rating = ?
        
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['rating'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }

    if ($_POST['request'] == 'get-sectors') {
        $region = $_POST['region'];
        echo _optionSector($region);

    }


    if ($_POST['request'] == 'task-review-item') {

        parse_str($_POST["data"], $_POST);

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        is_rated = ?,
        user_rating = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['user_rating'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo $this_id;
        }

    }


}


?>
