<?php

require_once '../../../configuration.php';

include('../Model.php');
include('../User.php');
include('../Day.php');
include('../VS.php');
include('../vendor/Helper.php');
include ('zahtjevZaGo.pdf.php');
error_reporting(E_ALL);

function kreirajZahtjevZaGO($year_id, $date_from, $date_to){
    global $db;
    $user_data = $db->query('SELECT v.user_id, b.fname, b.lname, b.parent FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] AS v 
                                      JOIN [c0_intranet2_apoteke].[dbo].[users] AS b ON v.user_id=b.user_id WHERE v.id='.$year_id)->fetch();
    $parent_data = $db->prepare("SELECT fname, lname FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user_data['parent']."'");
    //generatepdf($user_data, $parent_data, $date_from, $date_to);
}
if (isset($_POST['request'])) {

    // Ažuriranje satnica grupno

    if ($_POST['request'] == 'parent-day-add_apsolute' or ($_POST['request'] == 'day-edit')) {
        global $url;

        if($_POST['status'] == 106 or $_POST['status'] == 18 or $_POST['status'] == 19){
            if ($_POST['request'] == 'parent-day-add_apsolute'){
                kreirajZahtjevZaGO($_POST['get_year'], $_POST['dateFrom'], $_POST['dateTo']);
            }
            else{
                $get_data = Day::select('user_id, year_id,month_id,employee_no, [Date]')->where("id='" . $_POST['request_id'] . "'")->get(1);
                kreirajZahtjevZaGO($get_data['year_id'], date('d.m.Y', strtotime($get_data['Date'])), date('d.m.Y', strtotime($get_data['Date'])));
            }

        }

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $status = $_POST['status'];
        $hour = $_POST['hour'];

        if ($_POST['request'] == 'day-edit') {
            $this_id = $_POST['request_id'];
            $status = $_POST['status'];

            $check = Day::select('user_id, year_id,month_id,employee_no, [Date]')->where("id='" . $this_id . "'")->get(1);

            $dayofweek = date('l', strtotime($check['Date']));
            $getYear = $check['year_id'];
            $getMonth = $check['month_id'];
            $filter_emp = $check['employee_no'];
            $FromDay = date("d.m.Y", strtotime($check['Date']));
            $ToDay = date("d.m.Y", strtotime($check['Date']));

        } else {
            $FromDay = $_POST['dateFrom'];
            $ToDay = $_POST['dateTo'];
            $getMonth = $_POST['get_month'];
            $getYear = $_POST['get_year'];
            $dayofweek = 'Monday';
        }

        $dt_from = DateTime::createFromFormat('d.m.Y', $FromDay);
        $dt_to = DateTime::createFromFormat('d.m.Y', $ToDay);

        if (empty($dt_from)) {
            $dt_from = new DateTime($FromDay);
        }
        if (empty($dt_to)) {
            $dt_to = new DateTime($ToDay);
        }


        $request_id_generate = rand(0, 10000);


        /*
         * Employee_No i Godina
         */

        $emp = Day::select('employee_no, YEAR(Date) as godina')->where("year_id='" . $getYear . "' AND month_id='" . $getMonth . "'")->get(1);
        $empid = $emp['employee_no'];
        $godina = $emp['godina'];

        if ($empid != $_user['employee_no']) {
            $user_edit = $empid;
        }

        $go = VS::select('*')->where("employee_no='" . $empid . "' and year = '$godina'")->get(1);
        $kvote = VS::getKvote($empid, $godina);


        if (in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108))) {
            $KindofDayString = 'OVERRIDE';
        } else {
            $KindofDayString = 'BHOLIDAY';

            $provjera_praznik = Day::select('id')
                ->where("((status = '83') or (status != 5 and review_status = 1)) and employee_no = '$empid' and [Date] between '" . $dt_from->format('Y-m-d') . "' and '" . $dt_to->format('Y-m-d') . "'")
                ->get();

            if (count($provjera_praznik) > 0) {
                echo Helper::Message('alert-danger', 'Odobrene registracije i praznici neće biti registrovani!');
            }
        }


        $dt_radni_dani = Day::select('id, Date, month_id')
            ->where("KindofDay != '$KindofDayString' and ((status = 5) or (status != 5 and review_status = 0)) AND weekday not in(6, 7) and employee_no = '$empid' and [Date] between '" . $dt_from->format('Y-m-d') . "' and '" . $dt_to->format('Y-m-d') . "' ORDER BY id ASC")
            ->get();

        $dt_kalendarski_dani = Day::select('id, Date, status, weekday, date_NAV')
            ->where("employee_no = '$empid' and [Date] between '" . $dt_from->format('Y-m-d') . "' and '" . $dt_to->format('Y-m-d') . "' ORDER BY id ASC")
            ->get();

        if ($dt_kalendarski_dani[0]['date_NAV'] != null) {
            return Helper::Message('alert-danger', 'Ne možete ažurirati zaključane satnice!');
        }


        $dt_zatrazeno = Day::select('COUNT(*) as zatrazeno')
            ->where("KindofDay != '$KindofDayString' and ((status = 5) or (status != 5 and review_status = 0)) AND weekday not in(6, 7) and employee_no = '$empid' and [Date] between '" . $dt_from->format('Y-m-d') . "' and '" . $dt_to->format('Y-m-d') . "'")
            ->first();
        $dt_zatrazeno_radnih_dana = $dt_zatrazeno->zatrazeno;

        /*
         *
         * Provjera godišnjih odmora
         *
         */


        if (in_array(intval($status), array(106, 18, 19))) {
            if (isset($_POST['request_id'])){
                $checkDay = $db->query("SELECT KindofDay FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id=".$_POST['request_id'])->fetch()['KindofDay'];
                if($checkDay == 'SATURDAY' or $checkDay == 'SUNDAY'){
                    return Helper::Message('alert-danger', 'Nije moguća registracija godišnjeg odmora danima vikenda!');
                }
            }

            if ($dt_from->format('n') <= 6 and ($status == 106 or $status == 19)) {

                if ($status == 19) {
                    $plus = 0;
                } else {
                    $plus = intval($kvote['go-tekuca-godina']['slobodno']);
                }

                if ($dt_zatrazeno_radnih_dana > intval($kvote['go-prethodna-godina']['slobodno']) + $plus) {
                    return Helper::Message('alert-danger', 'Prekoračili ste broj dopuštenih dana godišnjeg odmora!');
                }
            } else {

                if (($status == 106 or $status == 18)) {
                    if ($dt_zatrazeno_radnih_dana > intval($kvote['go-tekuca-godina']['slobodno'])) {
                        return Helper::Message('alert-danger', 'Prekoračili ste broj dopuštenih dana godišnjeg odmora!');
                    }
                }
            }
        }

        /*
         * Provjera za Vjerske praznike V_0
         */


        if (in_array(intval($status), array(84, 21, 22))) {

            if ($dt_zatrazeno_radnih_dana > 4) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od 4 dana za vjerske praznike');
            }

            if ($status == 84) {
                $slobodno = intval($kvote['placeni-vjerski']['slobodno']) + intval($kvote['neplaceni-vjerski']['slobodno']);
            } else if ($status == 21) {
                $slobodno = intval($kvote['placeni-vjerski']['slobodno']);
            } else if ($status == 22) {
                $slobodno = intval($kvote['neplaceni-vjerski']['slobodno']);
            }

            if ($dt_zatrazeno_radnih_dana > $slobodno) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dozvoljenih vjerskih praznika');
            }
        }


        /*
         * Provjera za P_1 Zaključenje braka
         */
        if ($status == 27):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-1']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_2 Rođenje djeteta
         */

        if ($status == 28):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-2']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_3 Njega člana uže porodice
         */

        if ($status == 29):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-3']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_4 Smrt člana uže porodice
         */
        if ($status == 30):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-4']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_5 Selidba
         */
        if ($status == 31):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-5']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_6 Darivanje krvi
         */
        if ($status == 32):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-6']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za darivanje krvi, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;
        /*
         * Provjera za P_7 Stručno usavršavanje
         */
        if ($status == 79):
            if ($dt_zatrazeno_radnih_dana > intval($kvote['placeno-odsustvo-7']['slobodno']) or $dt_zatrazeno_radnih_dana > intval($kvote['placena-odsustva']['slobodno'])) {
                return Helper::Message('alert-danger', 'Ne možete unijeti više od dopuštenih dana dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!');
            }
        endif;


        /*
         * Ažuriranje prekovremenog rada
         *
         *
         */


        if (isset($_POST['status_pre'])) {
            $status_pre = $_POST['status_pre'];
            $hour_pre = $_POST['hour_pre'];
            $sum_hour = $hour + $hour_pre;
            if (!empty($status_pre)) {
                if (!empty($hour_pre)) {

                    if ($hour_pre < 0) {
                        return Helper::Message('alert-danger', 'Ne možete unijeti manje od 0h!');
                    }

                    if ($status_pre == $status) {
                        return Helper::Message('alert-danger', 'Ne možete unijeti istu vrstu odsustva!');
                    }

                    if ($dayofweek != 'Sunday' and $dayofweek != 'Saturday'){
                        if ($sum_hour > 7.5){
                            return Helper::Message('alert-danger', 'Suma radnih sati ne može biti veća od 7.5h!');
                        }
                    }
                    else{
                        if ($sum_hour > 8.5){
                            return Helper::Message('alert-danger', 'Suma radnih sati ne može biti veća od 8.5h!');
                        }
                    }

                    foreach ($dt_kalendarski_dani as $k => $v) {
                        Day::where("id = '" . $v['id'] . "' ")->update("hour_pre = '$hour_pre', status_pre = '$status_pre' ");
                    }
                }
            }
        }


        /*
         * ---------------------------------------------------------
         * Ažuriranje godišnjeg odmora
         * ---------------------------------------------------------
         */

        if (in_array(intval($status), array(106, 18, 19))) {

            $take = 0;

            // GO Prethodna godina


            if ($dt_from->format('n') <= 6 or $status == 19) {

                if ($dt_zatrazeno_radnih_dana > intval($kvote['go-prethodna-godina']['slobodno'])) {
                    $take = intval($kvote['go-prethodna-godina']['slobodno']);
                } else {
                    $take = $dt_zatrazeno_radnih_dana;
                }


                for ($i = 0; $i < $take; $i++) {

                    if ($dt_radni_dani[$i]['month_id'] >= 7) {
                        break;
                    }

                    Day::where("id = '" . $dt_radni_dani[$i]['id'] . "' ")
                        ->updateStatus(['status' => 19, 'corr_status' => 19], $empid);

                    unset($dt_radni_dani[$i]);
                }


            }


            if (intval($kvote['go-tekuca-godina']['slobodno']) > 0 and in_array(intval($status), array(106, 18))) {

                $dt_radni_dani = array_values($dt_radni_dani);

                foreach ($dt_radni_dani as $k => $v) {

                    Day::where("id = '" . $v['id'] . "' ")
                        ->updateStatus(['status' => 18, 'corr_status' => 18], $empid);
                }

            }
            require_once('../Mail.php');
            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');

        }

        /*
         * ---------------------------------------------------------
         * Popunjavanje Vjerskog praznika 2+2
         * ---------------------------------------------------------
         */


        if (in_array(intval($status), array(84, 21, 22))) {

            $take = 0;

            /*
             * Popunjavanje Vjerski placena odsustva
             */


            if (intval($kvote['placeni-vjerski']['slobodno']) > 0 and in_array(intval($status), array(84, 21))) {

                if ($dt_zatrazeno_radnih_dana > intval($kvote['placeni-vjerski']['slobodno'])) {
                    $take = intval($kvote['placeni-vjerski']['slobodno']);
                } else {
                    $take = $dt_zatrazeno_radnih_dana;
                }

                for ($i = 0; $i < $take; $i++) {

                    Day::where("id = '" . $dt_radni_dani[$i]['id'] . "' ")
                        ->updateStatus(['status' => 21, 'corr_status' => 21], $empid);

                    unset($dt_radni_dani[$i]);
                }
            }

            if (intval($kvote['neplaceni-vjerski']['slobodno']) > 0 and in_array(intval($status), array(84, 22))) {

                $dt_radni_dani = array_values($dt_radni_dani);

                foreach ($dt_radni_dani as $k => $v) {

                    Day::where("id = '" . $v['id'] . "' ")
                        ->updateStatus(['status' => 22, 'corr_status' => 22], $empid);
                }

            }
            require_once('../Mail.php');
            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');


        }


        /*
         *
         * ----------------------------------
         * Ažuriranje bolovanja
         * ----------------------------------
         *
         */


        if ($status == 67) {

//            $get_entity = DB::select("SELECT [Org Entity Code] FROM  " . $nav_employee . "  WHERE [No_] =  '" . $empid . "'");
//            $get_entity = $get_entity->{'Org Entity Code'};

            //TODO dodati entitet korisnika
            $get_entity = 'FBIH';
            /*
                Bolovanje u Federaciji BiH
            */
            if ($get_entity == 'FBIH' or $get_entity == 'BD') {
                $slice_days = 42;
                $status_first = 43;
                $status_second = 44;
            } else {

                /*
                 * Bolovanje u RS
                 */

                $slice_days = 30;
                $status_first = 107;
                $status_second = 108;
            }


            /*
             * Provjera dana prije
             */
            $count_take = 0;
            $id_before = 0;
            $fill_weekends = [];
            $fill_weekends_status = 0;
            $start_count = 0;


            $dan_prije = Day::select('id, status, weekday')->where("id = '" . ($dt_kalendarski_dani[0]['id'] - 1) . "' ")->first();

            if ($dan_prije->status == $status_first) {
                // Dan prije je bolovanje do 42/30 dana

                $id_before = $dan_prije->id;
                $count_take = Day::countBolovanja($id_before, $status_first);


            } else if ($dan_prije->weekday == 7 and ($dan_prije->status != $status_first or $dan_prije->status != $status_second)) {
                // Dan prije je Nedjelja i nije bolovanje - provjeravamo subotu i petak


                $dan_subota = Day::select('id, status, weekday')->where("id = '" . ($dan_prije->id - 1) . "' ")->first();
                $dan_petak = Day::select('id, status, weekday')->where("id = '" . ($dan_prije->id - 2) . "' ")->first();


                if ($dan_subota->status == $status_first) {
                    $count_before_subote = Day::countBolovanja($dan_subota->id, $status_first);

                    if ($count_before_subote <= $slice_days) {
                        $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_first];
                    } else {
                        $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_second];
                    }
                } else if ($dan_subota->status == $status_second) {
                    $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_second];
                    $status_first = $status_second;
                } else {


                    if ($dan_petak->status == $status_first) {

                        $count_before_petka = Day::countBolovanja($dan_petak->id, $status_first);

                        if ($count_before_petka < $slice_days - 1) {
                            $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_first];
                            $fill_weekends[] = ['id' => $dan_subota->id, 'status' => $status_first];
                        } else {
                            $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_second];
                        }
                    } else if ($dan_petak->status == $status_second) {
                        $fill_weekends[] = ['id' => $dan_prije->id, 'status' => $status_second];
                        $fill_weekends[] = ['id' => $dan_subota->id, 'status' => $status_second];
                        $status_first = $status_second;
                    }
                }


            } else if ($dan_prije->status == $status_second) {
                // Dan prije je bolovanje preko 42/30 - postavljamo bolovanje preko

                $status_first = $status_second;
            }


            if ($fill_weekends) {
                foreach ($fill_weekends as $key => $value) {
                    Day::where("id = '" . $value['id'] . "' ")
                        ->updateStatus(['status' => $value['status'], 'corr_status' => $value['status']], $empid);
                    Day::where("id = '" . $value['id'] . "' ")->update("Description = '' ");
                }

                $id_before = $dan_prije->id;
                $count_take = Day::countBolovanja($id_before, $status_first);
            }

            $i = 1;

            foreach ($dt_kalendarski_dani as $k => $v) {
                if ($i + $count_take <= $slice_days) {
                    Day::where("id = '" . $v['id'] . "' ")
                        ->updateStatus(['status' => $status_first, 'corr_status' => $status_first], $empid);
                    Day::where("id = '" . $v['id'] . "' ")->update("Description = '' ");
                } else {
                    Day::where("id = '" . $v['id'] . "' ")
                        ->updateStatus(['status' => $status_second, 'corr_status' => $status_second], $empid);
                    Day::where("id = '" . $v['id'] . "' ")->update("Description = '' ");
                }
                $i++;
            }

            require_once('../Mail.php');
            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');

        }


        /*
         *
         * Ažuriranje službenih puteva
         *
         */

        if (in_array(intval($status), array(73, 81))) {
            // $dt_kalendarski_dani->update(['status' => $status, 'review_status' => '0', 'hour' => $employee->br_sati]);

            foreach ($dt_kalendarski_dani as $k => $v) {
                Day::where("id = '" . $v['id'] . "' ")
                    ->updateStatus(['status' => $status, 'corr_status' => $status], $empid);
                Day::where("id = '" . $v['id'] . "' ")->update("Description = '' ");

            }
            require_once('../Mail.php');
            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');
        }

        /*
         * Ažuriranje rednovog i prekovremenog
        */


        if (in_array(intval($status), array(5, 85, 86, 87, 88, 89, 91, 92, 93, 94, 95, 96, 138))) {

            if (intval($status) == 5) {

                foreach ($dt_kalendarski_dani as $k => $v) {
                    Day::where("id = '" . $v['id'] . "' ")
                        ->update("hour = '$hour'");
                }

            } else {
                foreach ($dt_kalendarski_dani as $k => $v) {
                    Day::where("id = '" . $v['id'] . "' ")
                        ->update("status = '$status', hour = '$hour', review_status = 1, review_user = '$_user[user_id]', corr_status = '$status', corr_review_status = '1' ");
                }
            }


            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');
        }

        /*
         * Ažuriranje Praznika
         */

        if ($status == 83) {

            foreach ($dt_kalendarski_dani as $k => $v) {
                Day::where("id = '" . $v['id'] . "' and status not in (43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)")
                    ->updateStatus(['status' => $status, 'corr_status' => $status], $empid);
            }

            return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');

        }

        /*
         * Ažuriranje Plaćena odsustva i ostali statusi
         */


        if (in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 107, 108))) {

            foreach ($dt_kalendarski_dani as $k => $v) {
                Day::where("id = '" . $v['id'] . "' ")
                    ->updateStatus(['status' => $status, 'corr_status' => $status], $empid);
            }

        } else {
            foreach ($dt_radni_dani as $k => $v) {
                Day::where("id = '" . $v['id'] . "' ")
                    ->updateStatus(['status' => $status, 'corr_status' => $status], $empid);
            }
        }


        require_once('../Mail.php');
        return Helper::Message('alert-success', 'Izmjene su uspješno spašene!');


    }
}