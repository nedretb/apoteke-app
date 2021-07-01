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
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Godinu koju ste odabrali već postoji!') . '</div><br/>';

        } else {

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . " ");

            $total = $get2->rowCount();


            foreach ($query as $item) {
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


    if ($_POST['request'] == 'parent-day-add_apsolute' or ($_POST['request'] == 'day-edit' and $_POST['status'] == 67)) {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $status = $_POST['status'];


        if ($_POST['request'] == 'day-edit' and $_POST['status'] == 67) {
            $this_id = $_POST['request_id'];
            $status = $_POST['status'];

            $check = $db->query("SELECT user_id, year_id,month_id,employee_no, [Date] FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
            foreach ($check as $checkvalue) {
                $getYear = $checkvalue['year_id'];
                $getMonth = $checkvalue['month_id'];
                $filter_emp = $checkvalue['employee_no'];
                $FromDay = $checkvalue['Date'];
                $ToDay = $checkvalue['Date'];
            }
        } else {
            $FromDay = $_POST['dateFrom'];
            $ToDay = $_POST['dateTo'];
            $getMonth = $_POST['get_month'];
            $getYear = $_POST['get_year'];

        }

        $dateFromDB = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay)));
        $dateToDB = date('Y/m/d', strtotime(str_replace("/", "-", $ToDay)));

        $get_user = $db->query("SELECT TOP 1 user_id as user_id FROM  " . $portal_hourlyrate_day . " 
   where year_id=" . $getYear);
        $userID = $get_user->fetch();

        $dateFrom = strtotime(str_replace("/", "-", $FromDay));
        $dateTo = strtotime(str_replace("/", "-", $ToDay));
        $datediff = $dateTo - $dateFrom;
        $day_difference = floor($datediff / (60 * 60 * 24)) + 1;

        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));
        $check_weekends_start_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_from' and month_id = '$month_from' and year_id = '$getYear'");
        $check_weekends_start_date_fetch = $check_weekends_start_date->fetch();

        $check_weekends_end_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_to' and month_id = '$month_to' and year_id = '$getYear'");
        $check_weekends_end_date_fetch = $check_weekends_end_date->fetch();


        $weekends_start = $check_weekends_start_date_fetch['weekday'];
        $weekends_end = $check_weekends_end_date_fetch['weekday'];

        if ((($weekends_start >= 6 and $weekends_start <= 7) and ($weekends_end >= 6 and $weekends_end <= 7)) and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108)) and $_user['employee_no'] == $_SESSION['cc_admin']) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Molimo Vas da registraciju vikendom vršite direktno na kalendaru!') . '</div>';
            return;
        } elseif ((($weekends_start >= 6 and $weekends_start <= 7) and ($weekends_end >= 6 and $weekends_end <= 7)) and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 85, 86, 87, 88, 89, 90, 107, 108, 105))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete registrovati vikende!') . '</div>';
            return;
        }

        $request_id_generate = $day_from . "" . $day_to . "" . $month_from . "" . $month_to . "" . $getYear;

        $emp = $db->query("SELECT user_id, employee_no, YEAR(Date) as godina FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  ");
        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
            $user_edit = _user($valueemp['user_id']);
            $godina = $valueemp['godina'];
        }
        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");

        $br_sati = $user_edit['br_sati'];

        $get_count = $db->query("SELECT count(KindOfDay) as countHol FROM  " . $portal_hourlyrate_day . "  WHERE (KindOfDay='BHOLIDAY') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countHoliday = $get_count->fetch();
        $countHol = $countHoliday['countHol'];

        $get_count1 = $db->query("SELECT count(*) as countOdobreno FROM  " . $portal_hourlyrate_day . "  WHERE (review_status='1') and (KindofDay<>'BHOLIDAY') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "   where
		 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and month_id<=12
   and year_id=" . $getYear);

        $get_days1 = $get_days->fetchAll();

        $day_before = $day_from - 3;

        $get_days2 = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "   where
     
   (
   (day >= " . $day_before . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_before . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);

        $get_days2 = $get_days2->fetchAll();

        $nex_year = getYearId($getYear, $user_edit['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $user_edit['user_id'], 'prev', true);

        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $getYear . "' AND (status='18')) or (year_id = '" . $nex_year . "' and status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV is null)");


        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $getYear . "' AND (status='19')) or (year_id = '" . $pre_year . "' and status = '18')) AND employee_no='" . $empid . "' 
	AND weekday<>'6' AND weekday<>'7' AND (date_NAV is null)");


        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "   where 
   weekday<>'6' AND weekday<>'7' and  KindOfDay<>'BHOLIDAY' and
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);

        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");

        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");

        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");
        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_7 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $P_7a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='79'");
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

        foreach ($askedgo as $valueasked) {
            $askeddays = $valueasked['sum_hour'];
            $totalasked = $askeddays / $br_sati;
        }
        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / $br_sati) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }
        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / $br_sati) + $iskoristenoPG;
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

        foreach ($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach ($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }
        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / $br_sati) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }
        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / $br_sati) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / $br_sati) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }
        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / $br_sati) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / $br_sati) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / $br_sati) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / $br_sati) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / $br_sati) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7 = ($iskoristenocurrP_7 / $br_sati) + $iskoristenoP_7;
            $totalP_7ost = $totalP_7 - $iskoristenototalP_7;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }

        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / $br_sati) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        //VJERSKI PRAZNICI
        if ($_POST['status'] == '84') {

            $statusi = array('0', '0', '0', '0');

            if (($iskoristenototalpcm + $iskoristenototalupcm) + $totalasked <= 2) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '21';
                }
            } elseif ($iskoristenototalpcm >= 2 and (($iskoristenototalpcm + $iskoristenototalupcm) + $totalasked <= 4)) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '22';
                }
            } elseif ($iskoristenototalpcm + $iskoristenototalupcm + $totalasked <= 4) {
                $count_placeni = 0;
                for ($x = 1; $x <= $day_difference; $x++) {
                    if ($get_days1[$x - 1]['weekday'] != '6' and $get_days1[$x - 1]['weekday'] != '7' and $get_days1[$x - 1]['KindofDay'] != 'BHOLIDAY') {
                        if ($iskoristenototalpcm + $count_placeni < 2) {
                            $statusi[$x - 1] = '21';
                            $count_placeni++;
                        } else {
                            $statusi[$x - 1] = '22';
                        }
                    } else {
                        if ($get_days1[$x - 1]['KindofDay'] == 'BHOLIDAY')
                            $statusi[$x - 1] = $get_days1[$x - 1]['status'];
                        else
                            $statusi[$x - 1] = '21';

                    }
                }
            } else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 4 dana vjerskih praznika!') . '</div>';
                return;
            }
        }

        //BOLOVANJE
        if ($_POST['status'] == '67') {

            $emp_bol = $db->query("SELECT top 1 user_id, employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id=" . $getYear);
            foreach ($emp_bol as $valueemp) {
                $emp_no = $valueemp['employee_no'];
                $emp_id = $valueemp['user_id'];
            }

            $nulifikacija = false;
            $back_popunjavanje = 0;
            $statusi = array();
            $statusi_popunjavanje = array();

            $previous_day = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 1 days'));
            $previous_day_1 = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 2 days'));
            $previous_day_2 = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 3 days'));
            $previous_day_3 = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 4 days'));
            $previous_day_4 = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 5 days'));

            $get = $db->query("SELECT * FROM  " . $nav_employee . "  WHERE [No_]='" . $emp_no . "'");
            if ($get->rowCount() < 0) {
                $row_employee = $get->fetch();
                $entitet = $row_employee['Org Entity Code'];
            }

            if ($entitet == 'FBIH' or $entitet == 'BD') {
                $max_do = 42;
                $status_do = 43;
                $status_od = 44;
            } elseif ($entitet == 'RS') {
                $max_do = 30;
                $status_do = 107;
                $status_od = 108;
            }

            $get_date_bolovanje = $db->query("SELECT pocetak_bolovanja as pocetak_bolovanja FROM [c0_intranet2_apoteke].[dbo].[bolovanje] where 
    user_id = " . $emp_id);
            $pocetak_bolovanja = $get_date_bolovanje->fetch();

            if ($pocetak_bolovanja['pocetak_bolovanja'] == '') {
                $countBolovanje_total['bolovanje43'] = 0;
                $countBolovanje_do['bolovanje43'] = 0;
            } else {
                $get_prekid = $db->query("SELECT count(*) as prekid FROM  " . $portal_hourlyrate_day . " 
	WHERE 
   (status <>" . $status_do . ")
   and ([Date] >= '" . $pocetak_bolovanja['pocetak_bolovanja'] . "' and [Date] < '" . $dateFromDB . "') and year_id=" . $getYear);

                $countPrekid = $get_prekid->fetch();

                $get_bolovanje_total = $db->query("SELECT count(*) as bolovanje43 FROM  " . $portal_hourlyrate_day . " 
	WHERE 
   (status =" . $status_do . " or (weekday in ('6','7')) or KindOfDay = 'BHOLIDAY')
   and ([Date] >= '" . $pocetak_bolovanja['pocetak_bolovanja'] . "' and [Date] < '" . $dateFromDB . "') and year_id=" . $getYear);

                $countBolovanje_total = $get_bolovanje_total->fetch();

                $get_bolovanje_do = $db->query("SELECT count(*) as bolovanje43 FROM  " . $portal_hourlyrate_day . " 
	WHERE 
   (status =" . $status_do . ")
   and ([Date] >= '" . $pocetak_bolovanja['pocetak_bolovanja'] . "' and [Date] < '" . $dateFromDB . "') and year_id=" . $getYear);

                $countBolovanje_do = $get_bolovanje_do->fetch();
            }

            $get_previous_days = $db->query("SELECT status, KindOfDay, weekday FROM  " . $portal_hourlyrate_day . "  
  WHERE 
   ([Date] in ('" . $previous_day . "','" . $previous_day_1 . "','" . $previous_day_2 . "','" . $previous_day_3 . "','" . $previous_day_4 . "')) and year_id=" . $getYear . " order by [Date] DESC");

            $previous_days_get = $get_previous_days->fetchAll();

            for ($x = 0; $x < count($previous_days_get); $x++) {
                if (@$previous_days_get[$x]['status'] == $status_od or @$previous_days_get[$x]['status'] == $status_do) {
                    $back_popunjavanje = $x;
                    break;
                }
            }

            $dan_ranije_bolovanje = (@$previous_days_get[0]['status'] == $status_od or @$previous_days_get[0]['status'] == $status_do);
            $dan_ranije_subota = ((@$previous_days_get[0]['weekday'] == '6') and $back_popunjavanje);
            $dan_ranije_nedelja = ((@$previous_days_get[0]['weekday'] == '7') and $back_popunjavanje);
            $dan_ranije_praznik = ((@$previous_days_get[0]['KindOfDay'] == 'BHOLIDAY') and $back_popunjavanje);

            if (
            ($dan_ranije_bolovanje or $dan_ranije_subota or $dan_ranije_nedelja or $dan_ranije_praznik)
            ) {
                if ($pocetak_bolovanja['pocetak_bolovanja'] != '') {
                    for ($x = 0; $x < $max_do - $countBolovanje_total['bolovanje43']; $x++)
                        $statusi[$x] = $status_do;
                    for ($x = $max_do - $countBolovanje_total['bolovanje43']; $x < $day_difference; $x++) {
                        $statusi[$x] = $status_od;
                        $nulifikacija = true;
                    }

                    for ($x = 0; $x < $max_do - $countBolovanje_do['bolovanje43']; $x++)
                        $statusi_popunjavanje[$x] = $status_do;
                    for ($x = $max_do - $countBolovanje_do['bolovanje43']; $x < $day_difference; $x++) {
                        $statusi_popunjavanje[$x] = $status_od;
                    }

//nulifikacija pocetka bolovanja
                    if ($nulifikacija) {
                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                NULL,
                                $emp_id,
                            )
                        );
                    }
                } else {

                    for ($x = 0; $x < $day_difference; $x++) {
                        $statusi[$x] = $status_od;
                    }
                    for ($x = 0; $x < $back_popunjavanje; $x++) {
                        $statusi_popunjavanje[$x] = $status_od;
                    }

                }
            } elseif ($day_difference <= $max_do) {
                for ($x = 1; $x <= $day_difference; $x++)
                    $statusi[$x - 1] = $status_do;

//Upis pocetka bolovanja
                if ($pocetak_bolovanja['pocetak_bolovanja'] == '' or $countPrekid['prekid'] > 10) {
                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $dateFromDB,
                            $emp_id,
                        )
                    );
                }
            } elseif ($day_difference > $max_do) {
                for ($x = 1; $x <= $max_do; $x++)
                    $statusi[$x - 1] = $status_do;
                for ($x = $max_do + 1; $x <= $day_difference; $x++)
                    $statusi[$x - 1] = $status_od;
            }
        }

        //GO
        if ($_POST['status'] == '106') {

            $statusi = array();
            $count_prosla = $totalgoostPG;
            $count_trenutna = $totalgoost;


            $days_taken = 0;
            if ($totalasked <= $totalgoostPG + $totalgoost) {
                for ($x = 0; $x < $day_difference; $x++) {
                    if ($count_prosla - $days_taken > 0 and $get_days1[$x]['month_id'] <= 6) {

                        $statusi[$x] = '19';

                        if ($get_days1[$x]['weekday'] != '6' and $get_days1[$x]['weekday'] != '7' and $get_days1[$x]['KindofDay'] != 'BHOLIDAY') {
                            $days_taken++;
                        }

                    } elseif ($count_trenutna > 0) {

                        $statusi[$x] = '18';
                        if ($get_days1[$x]['weekday'] != '6' and $get_days1[$x]['weekday'] != '7' and $get_days1[$x]['KindofDay'] != 'BHOLIDAY') {
                            $days_taken++;
                            $count_trenutna--;
                        }

                    } else {
                        echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                        return;
                    }
                }
            } else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                return;
            }
        }

        if ($totalasked > $totalgoost and $_POST['status'] == '18') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            return;
        }
        if ($totalasked > $totalgoostPG and $_POST['status'] == '19') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
            return;
        }
        if (($_POST['status'] == '19') and ($propaloGO == 1)) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!!') . '</div>';
            return;
        }
        if (($totalasked > $totalpcmost) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > $totalupcmost) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_1ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '27')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_2ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '28')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_3ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '29')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_4ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '30')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_5ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '31')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_6ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '32')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_7ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '79')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > 5) and ($_POST['status'] == '30' or $_POST['status'] == '72')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!') . '</div>';
            return;
        }

        if (in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108, 105))) {
            $KindofDayString = 'OVERRIDE';
        } else {
            $KindofDayString = 'BHOLIDAY';
        }


        date_default_timezone_set('Europe/Sarajevo');

        if (in_array($_POST['status'], array(84, 67, 106))) {
            $d = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - 1 days'));

            $selected_new = 0;
            for ($x = 0; $x < $day_difference; $x++) {


                if (date('N', strtotime(str_replace("/", "-", $d))) == 5) {
                    $plus = 3;
                } else {
                    $plus = 1;
                }

                if ($_POST['status'] == 67 or $_POST['status'] == 84) {
                    $plus = 1;
                }

                $d = date('Y/m/d', strtotime(str_replace("/", "-", $d) . ' + ' . $plus . ' days'));


                /************** haris request id change ***********************/
                $d2 = date('Y/m/d', strtotime(str_replace("/", "-", $d) . ' - 1 days'));
                $select_check = $db->prepare("SELECT [KindofDay] FROM  " . $portal_hourlyrate_day . "  
								WHERE [Date] = '$d2' and year_id = '$getYear'");

                $select_check->execute();
                $f_c = $select_check->fetch();


                $new_request_id = $request_id_generate . "" . $statusi[$x];

                if ($f_c['KindofDay'] == 'BHOLIDAY') {
                    $d2_day = date('d', strtotime(str_replace("/", "-", $d)));
                    $d2_month = date('m', strtotime(str_replace("/", "-", $d)));
                    $d2_year = $getYear;

                    $new_request_id_x = $d2_day . "" . $d2_month . "" . $d2_year . "" . $statusi[$x];
                    $selected_new = 1;
                }

                if ($selected_new == 0) {
                    $new_request_id = $new_request_id;
                } else {
                    $new_request_id = $new_request_id_x;
                }

                /************** haris request id change - end ***********************/


                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
	   hour_pre = null,
      timest_edit = ?,
	   timest_edit_corr = ?,
	   employee_timest_edit = ?,
      status = ?,
      review_status=?,
      review_comment=?,
	employee_comment = ?,
	  corr_status = ?,
	  status_rejected = NULL,
	  request_id = '$new_request_id' 
   where 
   [Date] = '" . $d . "'
   and year_id=?
   and KindofDay<>?
   and review_user is null
   and [Date] >='" . $user_edit['employment_date'] . "'";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['hour'],
                        date('Y-m-d h:i:s'),
                        date('Y-m-d h:i:s'),
                        $_user['employee_no'],
                        $statusi[$x],
                        0,
                        $_POST['komentar'],
                        '',
                        $statusi[$x],
                        $getYear,
                        $KindofDayString
                    )
                );
            }
            if ($_POST['status'] == '67') {
                $bp = $back_popunjavanje + 1;
                $d = date('Y/m/d', strtotime(str_replace("/", "-", $FromDay) . ' - ' . $bp . ' days'));
                $selected_new = 0;
                for ($x = 0; $x < $back_popunjavanje; $x++) {
                    $d = date('Y/m/d', strtotime(str_replace("/", "-", $d) . ' + 1 days'));


                    $d2 = date('Y/m/d', strtotime(str_replace("/", "-", $d) . ' - 1 days'));
                    $select_check = $db->prepare("SELECT [KindofDay] FROM  " . $portal_hourlyrate_day . "  
								WHERE [Date] = '$d2' and year_id = '$getYear'");

                    $select_check->execute();
                    $f_c = $select_check->fetch();


                    if (empty($statusi_popunjavanje[$x])) continue;
                    $new_request_id = $request_id_generate . "" . $statusi_popunjavanje[$x];

                    if ($f_c['KindofDay'] == 'BHOLIDAY') {
                        $d2_day = date('d', strtotime(str_replace("/", "-", $d)));
                        $d2_month = date('m', strtotime(str_replace("/", "-", $d)));
                        $d2_year = $getYear;

                        $new_request_id_x = $d2_day . "" . $d2_month . "" . $d2_year . "" . $statusi_popunjavanje[$x];
                        $selected_new = 1;
                    }

                    if ($selected_new == 0) {
                        $new_request_id = $new_request_id;
                    } else {
                        $new_request_id = $new_request_id_x;
                    }

                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
	   hour_pre = null,
      timest_edit = ?,
	   timest_edit_corr = ?,
	   employee_timest_edit = ?,
      status = ?,
      review_status=?,
      review_comment=?,
	  employee_comment = ?,
	  corr_status = ?,
	  status_rejected = NULL,
	  request_id = '$new_request_id' 
   where 
   [Date] = '" . $d . "'
   and year_id=?
   and KindofDay<>?
   and review_user is null
   and [Date] >='" . $user_edit['employment_date'] . "'";

                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $_POST['hour'],
                            date('Y-m-d h:i:s'),
                            date('Y-m-d h:i:s'),
                            $_user['employee_no'],
                            $statusi_popunjavanje[$x],
                            0,
                            $_POST['komentar'],
                            '',
                            $statusi_popunjavanje[$x],
                            $getYear,
                            $KindofDayString
                        )
                    );
                }
            }
        } else {


            if (in_array($status, array('84', '21', '22'))) {
                $weekday_rule = "and weekday NOT IN (6,7)";
            } else {
                $weekday_rule = "";
            }

            $data = "
		declare @reqid integer
		set @reqid = 1
	  UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
	   hour_pre = null,
      timest_edit = ?,
	  timest_edit_corr = ?,
	  employee_timest_edit = ?,
      status = ?,
      review_status=?,
     review_comment=?,
	 employee_comment = ?,
	  corr_status = ?,
	  status_rejected = NULL,
	@reqid = case when [KindofDay] = 'BHOLIDAY' then @reqid+1 else @reqid end, 
	request_id = CONCAT(@reqid, '$request_id_generate') 
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
   $weekday_rule
   and KindofDay<>?
   and review_user is null
   and [Date] >='" . $user_edit['employment_date'] . "'";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['hour'],
                    date('Y-m-d h:i:s'),
                    date('Y-m-d h:i:s'),
                    $_user['employee_no'],
                    $status,
                    0,
                    $_POST['komentar'],
                    '',
                    $status,
                    $getYear,
                    $KindofDayString
                )
            );
        }

        $setted = 0;
        if (($countHol > 0 or $countOdobreno > 0) and ($status != '67' and $status != '73' and $status != '81' and $status != '105')) {
            $setted = 1;
            echo '<div class="alert alert-danger text-center">' . __('Upozorenje : Državni praznici većeg prioriteta i odobrene registracije nisu ažurirani!') . '</div>';
        }


        if ($res->rowCount() > 0) {
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

            // Mail notifikacija

            $get_mail_settings = $db->query("SELECT name, value FROM  " . $portal_settings . "  WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
            $get_mail_fetch = $get_mail_settings->fetchAll();

            $mail_settings = array();
            foreach ($get_mail_fetch as $key => $value) {
                $mail_settings[$value['name']] = $value['value'];
            }

            $array_bolovanje = array("43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27", "105", "106", "18", "19");

            // Bolovanje i placena odsustva
            if ($mail_settings['hr_notifications'] == '1') {
                if (in_array($status, $array_bolovanje)) {
                    // start mail

                    $status_izostanka = $status;


                    require '../../lib/PHPMailer/PHPMailer.php';
                    require '../../lib/PHPMailer/SMTP.php';
                    require '../../lib/PHPMailer/Exception.php';
                    require '../../mails.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = "UTF-8";

                    $mail->IsSMTP();
                    $mail->isHTML(true);  // Set email format to HTML

                    $mail->Host = "barbbcom";
                    //$mail->SMTPSecure = 'tls';
                    $mail->Port = 25;

                    $_user = $user_edit;
                    $parent_user = _employee($_user['parent']);


                    if (in_array($status, array(73))) {
                        // sluzbeni put svi

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
                        //$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

                    } else if (in_array($status, array(81))) {
                        // sluzbeni put EDUKACIJA

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("edukacija.hr@raiffeisengroup.ba");


                    } else if (in_array($status, array(106, 18, 19))) {
                        // godišnji odmori

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress(@$mail_settings['hr_supportt_mail']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

                    } else {

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
                    }


                    $mail->Subject = 'Registracija izostanka';


                    $mail->Body = $mails['day-edit'];

                    if (!$mail->send()) {
                        //echo 'Message was not sent.';
                        //echo 'Mailer error: ' . $mail->ErrorInfo;
                    } else {
                        //echo 'Message has been sent.';
                    }
                }
            }


            // kraj mail notifikacije


        } else {
            if ($setted == 0):
                echo '<div class="alert alert-danger text-center">' . __('Odobrene registracije nisu ažurirane!') . '</div>';
            endif;
        }

    }


    if ($_POST['request'] == 'day-edit' and $_POST['status'] != '67') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $Day = $_POST['day'];
        $this_id = $_POST['request_id'];
        $status = $_POST['status'];
        $status2 = $_POST['status'];

        $request_id_generate = $this_id . "" . rand(1, 100);

        if (empty($_POST['hour_pre'])) {
            $_POST['hour_pre'] = null;
        }


        $get_old_status = $db->query("SELECT status, KindOfDay, review_status, weekday FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $old_status = $get_old_status->fetch();


        $emp = $db->query("SELECT employee_no,year_id,month_id, YEAR(Date) as godina FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $check = $db->query("SELECT user_id, year_id,month_id,employee_no FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
        foreach ($check as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month = $checkvalue['month_id'];
            $filter_emp = $checkvalue['employee_no'];
            $user_edit = _user($checkvalue['user_id']);
        }

        $br_sati = $user_edit['br_sati'];


        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
            $getYear = $valueemp['year_id'];
            $getMonth = $valueemp['month_id'];
            $godina = $valueemp['godina'];
        }

        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");

        $nex_year = getYearId($getYear, $user_edit['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $user_edit['user_id'], 'prev', true);

        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $getYear . "' AND (status='18')) or (year_id = '" . $nex_year . "' and status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV is null)");


        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $getYear . "' AND (status='19')) or (year_id = '" . $pre_year . "' and status = '18')) AND employee_no='" . $empid . "' 
	AND weekday<>'6' AND weekday<>'7' AND (date_NAV is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");

        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");


        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");

        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");

        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_7 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "' and year = '$godina'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $P_7a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='79'");
        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "'
     and weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");


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

        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / $br_sati) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }

        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / $br_sati) + $iskoristenoPG;
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
            $iskoristenototalpcm = ($iskoristenocurrpcm / $br_sati) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }

        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / $br_sati) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
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

        foreach ($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach ($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / $br_sati) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }

        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / $br_sati) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / $br_sati) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / $br_sati) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / $br_sati) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / $br_sati) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['sum_hour'];
            $iskoristenototalP_7 = ($iskoristenocurrP_7 / $br_sati) + $iskoristenoP_7;
            $totalP_7ost = $totalP_7 - $iskoristenototalP_7;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }
        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / $br_sati) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if ($_POST['status'] == '84') {

            if ($iskoristenototalpcm + $iskoristenototalupcm < 2)
                $status = '21';
            elseif ($iskoristenototalpcm >= 2 and ($iskoristenototalpcm + $iskoristenototalupcm) < 4)
                $status = '22';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 4 dana vjerskih praznika !') . '</div>';
                return;
            }
        }

        if ($_POST['status'] == '106') {

            if (($totalgoostPG - 1 >= 0) and $filter_month <= 6)
                $status = '19';
            elseif ($totalgoost - 1 >= 0)
                $status = '18';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                return;
            }
        }

        if (($old_status['KindOfDay'] == 'BHOLIDAY') and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108, 105))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete pregaziti praznik!') . '</div>';
            return;
        }
        if ($old_status['review_status'] == '1' and !(($old_status['KindOfDay'] == 'BHOLIDAY') and in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108)))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete promjeniti odobrenu registraciju!') . '</div>';
            return;
        }
        if (($totalgoost - 1 < 0) and $_POST['status'] == '18') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            return;
        }
        if (($totalgoostPG - 1 < 0) and $_POST['status'] == '19') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
            return;
        }
        if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_1ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '27')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_2ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '28')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_3ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '29')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_4ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '30')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_5ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '31')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_6ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '32')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 1 dan za darivanje krvi!') . '</div>';
            return;
        }
        if ((($totalP_7ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '79')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 2 dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog odsustva !') . '</div>';
            return;
        }
        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog odsustva !') . '</div>';
            return;
        }
        if (@$_POST['hour_pre'] > 0 and $_POST['status_pre'] == "") {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Morate izabrati prekovremeni ili dodatni rad!') . '</div>';
            return;
        }
        if (($_POST['status'] == '19') and ($propaloGO == 1) and ($filter_month > 6)) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!') . '</div>';
            return;
        }

        if ($_POST['hour'] + $_POST['hour_pre'] > 24) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 24h ukupnog radnog vremena !') . '</div>';
            return;
        }
        if (!isset($_POST['status_pre'])) {
            $_POST['status_pre'] = "";
        }
        if ($_POST['status'] == @$_POST['status_pre']) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti istu vrstu rada !') . '</div>';
            return;
        }

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        if ((in_array($_POST['status'], array(85, 86, 87, 88, 89, 90)) or ($_POST['hour_pre'] != '' and $_POST['hour_pre'] != '0') or ($_POST['hour'] <> 8 and $br_sati == 8)) or ($old_status['weekday'] == '6' or $old_status['weekday'] == '7')) {
            $rev_status = 1;
            $review_user = $_user['user_id'];
        } else {
            $rev_status = 0;
            $review_user = NULL;
        }


        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      day = ?,
      hour = ?,
	  hour_pre = ?,
      timest_edit = ?,
	  timest_edit_corr = ?,
      employee_timest_edit = ?,
      status = ?,
      review_status=?,
	  corr_review_status=?,
      review_comment=?,
	  employee_comment=?,
	  corr_status = ?,
	  status_pre = ?,
	  corr_pre = ?,
	  request_id = '$request_id_generate' ,
	  review_user = ?
	  WHERE id = ?
     ";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['day'],
                $_POST['hour'],
                $_POST['hour_pre'],
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
                $_user['employee_no'],
                $status,
                $rev_status,
                $rev_status,
                $_POST['komentar'],
                '',
                $status,
                @$_POST['status_pre'],
                @$_POST['status_pre'],
                $review_user,
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';


        } else {
            echo '<div style="background-color:#3c763d;  color:#dff0d8;" class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'parent-day-cancel_apsolute') {
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $FromDay = $_POST['dateFrom'];
        $ToDay = $_POST['dateTo'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $same_days = 0;

        if ($FromDay == $ToDay) {
            $same_days = 1;
        }

        $dateFrom = strtotime(str_replace("/", "-", $FromDay));
        $dateTo = strtotime(str_replace("/", "-", $ToDay));

        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));

        $check_weekends_start_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_from' and month_id = '$month_from' and year_id = '$getYear'");
        $check_weekends_start_date_fetch = $check_weekends_start_date->fetch();

        $check_weekends_end_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_to' and month_id = '$month_to' and year_id = '$getYear'");
        $check_weekends_end_date_fetch = $check_weekends_end_date->fetch();


        $weekends_start = $check_weekends_start_date_fetch['weekday'];
        $weekends_end = $check_weekends_end_date_fetch['weekday'];


        $get_statuses = $db->query("SELECT status FROM  " . $portal_hourlyrate_day . "  WHERE (status<>'5') and (
	   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
	   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
	   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
	   (month_id > " . $month_from . " and month_id < " . $month_to . ")
	   )
	   and year_id=" . $getYear);
        $fetch_statuses = $get_statuses->fetchAll();
        $statusi_array = array();
        $kakoNazvatiovuVarijablu = 0;

        // bolovanje/sluzbeni put
        foreach ($fetch_statuses as $key => $v):
            if (in_array($v['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 107, 108, 73, 81))) {
                $kakoNazvatiovuVarijablu = 1;
            }
        endforeach;


        if ((($weekends_start >= 6 and $weekends_start <= 7) and ($weekends_end >= 6 and $weekends_end <= 7)) and ($kakoNazvatiovuVarijablu == 0 and $_user['employee_no'] != $_SESSION['cc_admin'])) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete izvršiti otkazivanje vikendom!') . '</div>';
            return;
        }


        $request_id_generate = $day_from . "" . $day_to . "" . $month_from . "" . $month_to . "" . $getYear;

        $get_count1 = $db->query("SELECT count(*) as countOdobreno FROM  " . $portal_hourlyrate_day . "  WHERE (review_status='1') and (status<>'83') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        $get_count2 = $db->query("SELECT count(*) as not_redovni FROM  " . $portal_hourlyrate_day . "  WHERE ((status='5' and hour != '0' and (hour_pre IS NOT NULL or hour_pre > 0)) or (status != '5'))  and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $count_not_redovni = $get_count2->fetch();
        $notRedovni = $count_not_redovni['not_redovni'];


        // praznici check

        /***************************************/

        $get_count_praznik = $db->query("SELECT COUNT(*) as not_praznik  FROM  " . $portal_hourlyrate_day . "  WHERE ( status = '83' and KindOfDay = 'BHOLIDAY')  and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $count_not_praznik = $get_count_praznik->fetch();

        $notPraznik = $count_not_praznik['not_praznik'];

        /****************************************/


        $get_count3 = $db->query("SELECT count(*) as req_sent FROM  " . $portal_hourlyrate_day . "  WHERE (change_req='1') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $count_req_sent = $get_count3->fetch();
        $countSent = $count_req_sent['req_sent'];

        $get_count_not_vikend = $db->query("SELECT count(*) as not_vikend FROM  " . $portal_hourlyrate_day . "  WHERE weekday not in (6,7) and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $count_not_vikend = $get_count_not_vikend->fetch();
        $not_vikend = $count_not_vikend['not_vikend'];


        $get_statuses_redovan = $db->query("SELECT status,weekday,hour,hour_pre FROM  " . $portal_hourlyrate_day . "  WHERE weekday not in (6,7)  and (
	   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
	   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
	   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
	   (month_id > " . $month_from . " and month_id < " . $month_to . ")
	   )
	   and year_id=" . $getYear);
        $fetch_statuses_redovan = $get_statuses_redovan->fetchAll();
        $samo_redovni = 1;

        foreach ($fetch_statuses_redovan as $k => $v) {
            if ($v['status'] != '5' or ($v['status'] == '5' and $v['hour'] != '0' and ($v['weekday'] == '6' or $v['weekday'] == '7')) or ($v['status'] == '5' and $v['hour'] != '0' and $v['hour_pre'] > 0)) {
                $samo_redovni = 0;
            }
        }


        if ($samo_redovni == 1) {
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Nemate šta otkazati.') . '</div>';
            return;
        }


        if ($not_vikend == 0 and $_user['employee_no'] != $_SESSION['cc_admin'] and $kakoNazvatiovuVarijablu == 0) {
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Nemate šta otkazati.') . '</div>';
            return;
        }

        if ($countSent > 0 and $_user['employee_no'] != $_SESSION['cc_admin']) {
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-success text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Zahtjev za otkazivanje je već poslan!') . '</div>';
            return;
        }

        if ($notRedovni == 0) {
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Nemate šta otkazati.') . '</div>';
            return;
        }

        if ($notPraznik == 1 and $same_days == 1) {
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Ne možete otkazati praznik.') . '</div>';
            return;
        }

        //otkazivanje sl puta mail
        $send_email = false;
        foreach ($fetch_statuses as $key => $v) {
            if (in_array($v['status'], array(73))) {
                $send_email = true;
                $statuss = 73;
            }
            if (in_array($v['status'], array(81))) {
                $send_email = true;
                $statuss = 81;
            }
        }

        if ($send_email) {
            mail_cancel_trip($_POST['dateFrom'], $_POST['dateTo'], $statuss);
        }

        if ($countOdobreno > 0 and $_user['employee_no'] != $_SESSION['cc_admin']) {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      change_req = ?,
	  employee_comment = ?,
	  request_id = '$request_id_generate' 
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    '8',
                    '1',
                    '',
                    $getYear
                )
            );

            echo '<div style="background-color:#ff6666; color:#a94442;" class="alert alert-success text-center"><b>' . __('Greška!') . '</b><br/>' . __('Zahtjev za otkazivanje odobrene registracije poslan!') . '</div>';
            return;
        }

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      timest_edit = ?,
      status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or (status_bh is not null and status_bh != 5)) then 83 else 5 end,
	  corr_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
	  review_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 1 else 0 end,
	  KindOfDay = case when (status_bh is not null) then 'BHOLIDAY' else KindOfDay end,
	  Description = case when (status_bh is not null) then case when (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date]) is not null then (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date]) else Description end else Description end,
	  employee_comment = ?,
	  review_user = NULL,
hour = case when [weekday] in (6,7) then 0 else 
      (select br_sati from  " . $portal_users . "  as u 
join c0_intranet2_apoteke.dbo.hourlyrate_year as y on u.user_id = y.user_id where y.id = $getYear) end,	  request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end,
	  status_pre = NULL,
	  hour_pre = NULL
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(
                date('Y-m-d h:i:s'),
                '',
                $getYear
            )
        );

        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }

    if ($_POST['request'] == 'day-review') {

        $this_id = $_POST['request_id'];

        $check = $db->query("SELECT user_id, year_id,month_id,employee_no,weekday,hour FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
        foreach ($check as $checkvalue) {
            $weekday = $checkvalue['weekday'];
            $hours_no = $checkvalue['hour'];
        }

        if ($_POST['status'] == '2') {
            $status_id = '5';
            $status = '0';
            if ($weekday == 6 or $weekday == 7)
                $hour = 0;
            else
                $hour = 8;
        } else {
            $status_id = $_POST['status_id'];
            $status = $_POST['status'];
            $hour = 8;
        }

        if ($hours_no == '0') {
            echo '<div class="alert alert-danger text-center">' . __('Ne možete ažurirati redovan rad od 0h!') . '</div>';
            exit;
        }

        if ($_POST['status_id'] == '5' && empty($_POST['reg'])) {
            echo '<div class="alert alert-danger text-center">' . __('Ne možete ažurirati redovan rad!') . '</div>';
            exit;
        }

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        if ($status == 1) {
            $data = "UPDATE  " . $portal_hourlyrate_day . "  set review_status=1 where id=" . $this_id;
            $rev_user = $_user['user_id'];
            $res = $db->prepare($data);
            $res->execute();

        } else {
            $rev_user = null;

            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = " . $hour . ",
    hour_pre = case when (" . $status . "='0') then NULL else hour_pre end,
    status_pre = case when (" . $status . "='0') then NULL else status_pre end,
    review_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 1 else 0 end,
      review_comment = ?,
      review_user = ?,
    corr_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
    status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end
    
   WHERE id = ?
   AND (KindOfDay<>'BHOLIDAY' or review_status=0)";
            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['comment'],
                    $rev_user,
                    $this_id
                )
            );
        }


        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
        } else {
            echo '<div  class="alert alert-danger text-center">' . __('Ne možete izvršiti odbijanje praznika!') . '</div>';
        }

    }

    if ($_POST['request'] == 'day-review_apsolute') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $FromDay = $_POST['dateFrom'];
        $ToDay = $_POST['dateTo'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));

        /* Test 131, niko osim Anele Spahović ne može registrovati vikendom */

        $check_weekends_start_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_from' and month_id = '$month_from' and year_id = '$getYear'");
        $check_weekends_start_date_fetch = $check_weekends_start_date->fetch();

        $check_weekends_end_date = $db->query("SELECT weekday FROM  " . $portal_hourlyrate_day . "  WHERE day = '$day_to' and month_id = '$month_to' and year_id = '$getYear'");
        $check_weekends_end_date_fetch = $check_weekends_end_date->fetch();


        $weekends_start = $check_weekends_start_date_fetch['weekday'];
        $weekends_end = $check_weekends_end_date_fetch['weekday'];

        if ((($weekends_start >= 6 and $weekends_start <= 7) and ($weekends_end >= 6 and $weekends_end <= 7)) and $_user['employee_no'] != $_SESSION['cc_admin']) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete odobriti vikende!') . '</div>';
            return;
        }

        /* end test 131 */


        date_default_timezone_set('Europe/Sarajevo');
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        if ($_POST['status'] == '2') {

            $emp = $db->query("SELECT user_id, employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id=" . $getYear);
            foreach ($emp as $valueemp) {
                $empid = $valueemp['employee_no'];
                $user_edit = _user($valueemp['user_id']);
            }

            $br_sati = $user_edit['br_sati'];

            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      review_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 1 else 0 end,
      review_comment = ?,
      review_user = ?,
	  status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
	  corr_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
	   hour = case when [weekday] in (6,7) then 0 else " . $br_sati . " end
      where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
   AND (status != '83' or review_status=0)
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['comment'],
                    null,
                    $getYear
                )
            );

        } else {
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      review_status = ?,
      review_comment = ?,
      review_user = ?
      where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and status != 5
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['status'],
                    $_POST['comment'],
                    $_user['user_id'],
                    $getYear
                )
            );

        }


        if ($res->rowCount() > 0) {
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
        } else {
            echo '<div  class="alert alert-danger text-center">' . __('Ne možete izvršiti odbijanje praznika!') . '</div>';
        }

    }


    if ($_POST['request'] == 'lock-month') {

        if (isset($_POST['zakljucan'])) {
            $zakljucan = 1;
        } else {
            $zakljucan = 0;
        }
        $data = "UPDATE  " . $portal_hourlyrate_month . "  SET
      verified = ?
		WHERE user_id = ?
		AND month = ?
		AND year_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $zakljucan,
                $_POST['user_id'],
                $_POST['month_id'],
                $_POST['year_id'],
            )
        );
        if ($res->rowCount() == 1) {
            if ($zakljucan == 1) {
                echo _message('month_verified');
            } else {
                echo 'otkljucan';
            }

        } else {
            echo '<div style="background-color:#3c763d;  color:#dff0d8;" class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
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
