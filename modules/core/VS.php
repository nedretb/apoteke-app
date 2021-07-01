<?php

require_once('User.php');
require_once('DayStatus.php');
require_once('vendor/Collection/Collection.php');


class VS extends Model
{

    public static $table = 'vacation_statistics';


    public static function getPortalHoursExtended($columns = '')
    {
        return '
                SELECT status, hour FROM [hourlyrate_day] where weekday not in(6,7)
                and (
                        (
                            (date_NAV_corrections IS NULL and corr_status IN (' . $columns . ')
                            and status != corr_status
                         ) 
                        or (corr_status in (' . $columns . ') and status != corr_status))
                        or (date_NAV IS NULL and status in (' . $columns . '))
                    )
                and YEAR(Date) = ?
                and employee_no = ?
            ';
    }

    public static function getPortalHours($columns = '')
    {
        // var_dump($corr_sat);

        /*
        return '
            SELECT sum(hour) as sum_hour FROM [hourlyrate_day] where weekday not in(6,7)
            and (
                    (
                        (date_NAV_corrections IS NULL and corr_status IN ('.$columns.')
                        and status != corr_status
                     )
                    or (corr_status in ('.$columns.') and status != corr_status))
                    or (date_NAV IS NULL and status in ('.$columns.'))
                )
            and YEAR(Date) = ?
            and employee_no = ?
        ';
        */

        return '
                SELECT sum(hour) as sum_hour FROM [hourlyrate_day] where weekday not in(6,7)
                and (date_NAV_corrections IS NULL and corr_status IN (' . $columns . '))
                and YEAR(Date) = ?
                and employee_no = ?
            ';


    }

    public static function getKvote($employee_no, $year, $korekcije_satnice = 'satnice')
    {
        global $_conf, $month;
        //var_dump($month);
        // var_dump(date('m'));
        if ($korekcije_satnice == 'satnice') {
            $column = 'status';
            $date_nav = 'date_NAV';
            $corr_sat = 1;
        } else {
            $column = 'corr_status';
            $date_nav = 'date_NAV IS NOT NULL or date_NAV_corrections';
            $corr_sat = 0;
        }

        error_reporting(0);

        $data = (object)VS::select('*')->where("employee_no = '$employee_no' and year = '$year' ")->get(1);

        $data_ispred = (object)VS::select('Br_dana_iskoristenoPG')->where("employee_no = '$employee_no' and year = '" . ($year + 1) . "' ")->get(1);

        $emp = (object)User::select('br_sati')->where("employee_no = '$employee_no'")->get(1);

        /*
         * GO Prethodna godina
         * Preuzmi sve iz vacation_statistics iz kolona za prethodnu godinu
         * Preuzmi sve iz portala $go_portal gdje je status 19 za trenutnu godinu, i status 18 za prethodnu
         */

        $go_portal_prethodna = DB::select(self::getPortalHours('19', $corr_sat), [$year, $employee_no]);
        $go_portal_prethodna_iza = DB::select(self::getPortalHours('18', $corr_sat), [$year - 1, $employee_no]);


        $go_portal_tekuca = DB::select(self::getPortalHours('18', $corr_sat), [$year, $employee_no]);
        $go_portal_tekuca_ispred = DB::select(self::getPortalHours('19', $corr_sat), [$year + 1, $employee_no]);


        $go_portal_iskoristeno_prethodna = ($go_portal_prethodna->sum_hour / $emp->br_sati) + $data->Br_dana_iskoristenoPG + ($go_portal_prethodna_iza->sum_hour / $emp->br_sati);
        $go_portal_ukupno_prethodna = $data->Br_danaPG;
        $go_portal_slobodno_prethodna = $go_portal_ukupno_prethodna - $go_portal_iskoristeno_prethodna;

        /*
         * Propalo GO prošla godina?
         */

        if (isset($month['id'])) {
            $factor = $month['id'];
        } else if (isset($_POST['IDMonth'])) {
            $factor = $_POST['IDMonth'];
        }

        if ($data->{'G_2 not valid'} == 1 and (@$factor > 6)) {
            $go_portal_slobodno_prethodna = 0;
        }

        /*
         * GO Tekuća godina
         * Preuzmi sve iz vacation_statistics iz kolona za tekucu godinu
         * Preuzmi sve iz portala $go_portal gdje je status 18 za trenutnu godinu, i status 19 za prethodnu
         */

        $nav_tekuca = $data->Br_dana_iskoristeno;

        // if($data_ispred->Br_dana_iskoristenoPG != null){
        //     $nav_tekuca = $data_ispred->Br_dana_iskoristenoPG;
        // }


        $go_portal_iskoristeno_tekuca = ($go_portal_tekuca->sum_hour / $emp->br_sati) + ($go_portal_tekuca_ispred->sum_hour / $emp->br_sati);
        $go_portal_ukupno_tekuca = $data->Br_dana;
        $go_portal_slobodno_tekuca = $go_portal_ukupno_tekuca - $go_portal_iskoristeno_tekuca;


        $placeni_vjerski_portal = DB::select(self::getPortalHours('21'), [$year, $employee_no]);
        $placeni_vjerski_iskoristeno = ($placeni_vjerski_portal->sum_hour / $emp->br_sati) + $data->Candelmas_paid_used;
        $placeni_vjerski_ukupno = $data->Candelmas_paid;
        $placeni_vjerski_slobodno = $placeni_vjerski_ukupno - $placeni_vjerski_iskoristeno;


        $neplaceni_vjerski_portal = DB::select(self::getPortalHours('22'), [$year, $employee_no]);
        $neplaceni_vjerski_iskoristeno = ($neplaceni_vjerski_portal->sum_hour / $emp->br_sati) + $data->Candelmas_unpaid_used;
        $neplaceni_vjerski_ukupno = $data->Candelmas_unpaid;
        $neplaceni_vjerski_slobodno = $neplaceni_vjerski_ukupno - $neplaceni_vjerski_iskoristeno;


        $placena_odsustva_portal = DB::select(self::getPortalHours('27, 28, 29, 30, 31, 32, 79'), [$year, $employee_no]);
        $placena_odsustva_iskoristeno = ($placena_odsustva_portal->sum_hour / $emp->br_sati) + $data->P_1_used + $data->P_2_used + $data->P_3_used + $data->P_4_used + $data->P_5_used + $data->P_6_used + $data->P_7_used;
        $placena_odsustva_ukupno = $data->Br_dana_PLO;
        $placena_odsustva_slobodno = $placena_odsustva_ukupno - $placena_odsustva_iskoristeno;


        $placena_odsustva_portal_1_7 = DB::select(self::getPortalHoursExtended('27, 28, 29, 30, 31, 32, 79'), [$year, $employee_no], 1);


        $placena_odsustva_portal_1_7_status = DayStatus::select('id, allowed_days')->where('id in (27, 28, 29, 30, 31, 32, 79)')->orderBy('id', 'DESC')->get();


        $ostala_placena = DB::select(self::getPortalHours('72'), [$year, $employee_no]);
        $ostala_placena_iskoristeno = ($ostala_placena->sum_hour / $emp->br_sati) + $data->S_1_used;

        /*
         * P_1 Zaključenje braka
         */
        $placeno_odsustvo_1_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 27) / $emp->br_sati + $data->P_1_used;
        $placeno_odsustvo_1_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 27);
        $placeno_odsustvo_1_slobodno = $placeno_odsustvo_1_ukupno - $placeno_odsustvo_1_iskoristeno;

        /*
         * P_2 Rođenje djeteta
         */

        $placeno_odsustvo_2_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 28) / $emp->br_sati + $data->P_2_used;
        $placeno_odsustvo_2_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 28);
        $placeno_odsustvo_2_slobodno = $placeno_odsustvo_2_ukupno - $placeno_odsustvo_2_iskoristeno;

        /*
         * P_3 Njega člana uže porodice
         */

        $placeno_odsustvo_3_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 29) / $emp->br_sati + $data->P_3_used;
        $placeno_odsustvo_3_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 29);
        $placeno_odsustvo_3_slobodno = $placeno_odsustvo_3_ukupno - $placeno_odsustvo_3_iskoristeno;

        /*
         * P_4 Smrt člana uže porodice
         */

        $placeno_odsustvo_4_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 30) / $emp->br_sati + $data->P_4_used;
        $placeno_odsustvo_4_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 30);
        $placeno_odsustvo_4_slobodno = $placeno_odsustvo_4_ukupno - $placeno_odsustvo_4_iskoristeno;


        /*
         * P_5 Selidba
         */

        $placeno_odsustvo_5_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 31) / $emp->br_sati + $data->P_5_used;
        $placeno_odsustvo_5_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 31);
        $placeno_odsustvo_5_slobodno = $placeno_odsustvo_5_ukupno - $placeno_odsustvo_5_iskoristeno;

        /*
         * P_6 Dobrovoljno darivanje krvi
         */

        $placeno_odsustvo_6_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 32) / $emp->br_sati + $data->P_6_used;
        $placeno_odsustvo_6_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 32);
        $placeno_odsustvo_6_slobodno = $placeno_odsustvo_6_ukupno - $placeno_odsustvo_6_iskoristeno;

        /*
         * P_7 Polaganje ispita
         */

        $placeno_odsustvo_7_iskoristeno = Arr::sum($placena_odsustva_portal_1_7, 79) / $emp->br_sati + $data->P_7_used;
        $placeno_odsustvo_7_ukupno = Arr::where($placena_odsustva_portal_1_7_status, 'id', 79);
        $placeno_odsustvo_7_slobodno = $placeno_odsustvo_7_ukupno - $placeno_odsustvo_7_iskoristeno;


        $kvote = [
            'go-prethodna-godina' =>
                [
                    'ukupno' => $go_portal_ukupno_prethodna,
                    'slobodno' => $go_portal_slobodno_prethodna,
                    'iskoristeno' => $go_portal_iskoristeno_prethodna
                ],
            'go-tekuca-godina' =>
                [
                    'ukupno' => $go_portal_ukupno_tekuca,
                    'slobodno' => $go_portal_slobodno_tekuca,
                    'iskoristeno' => $go_portal_iskoristeno_tekuca
                ],
            'placeni-vjerski' =>
                [
                    'ukupno' => $placeni_vjerski_ukupno,
                    'slobodno' => $placeni_vjerski_slobodno,
                    'iskoristeno' => $placeni_vjerski_iskoristeno
                ],
            'neplaceni-vjerski' =>
                [
                    'ukupno' => $neplaceni_vjerski_ukupno,
                    'slobodno' => $neplaceni_vjerski_slobodno,
                    'iskoristeno' => $neplaceni_vjerski_iskoristeno
                ],
            'placena-odsustva' =>
                [
                    'ukupno' => $placena_odsustva_ukupno,
                    'slobodno' => $placena_odsustva_slobodno,
                    'iskoristeno' => $placena_odsustva_iskoristeno
                ],
            'placeno-odsustvo-1' =>
                [
                    'ukupno' => $placeno_odsustvo_1_ukupno,
                    'slobodno' => $placeno_odsustvo_1_slobodno,
                    'iskoristeno' => $placeno_odsustvo_1_iskoristeno
                ],
            'placeno-odsustvo-2' =>
                [
                    'ukupno' => $placeno_odsustvo_2_ukupno,
                    'slobodno' => $placeno_odsustvo_2_slobodno,
                    'iskoristeno' => $placeno_odsustvo_2_iskoristeno
                ],
            'placeno-odsustvo-3' =>
                [
                    'ukupno' => $placeno_odsustvo_3_ukupno,
                    'slobodno' => $placeno_odsustvo_3_slobodno,
                    'iskoristeno' => $placeno_odsustvo_3_iskoristeno
                ],
            'placeno-odsustvo-4' =>
                [
                    'ukupno' => $placeno_odsustvo_4_ukupno,
                    'slobodno' => $placeno_odsustvo_4_slobodno,
                    'iskoristeno' => $placeno_odsustvo_4_iskoristeno
                ],
            'placeno-odsustvo-5' =>
                [
                    'ukupno' => $placeno_odsustvo_5_ukupno,
                    'slobodno' => $placeno_odsustvo_5_slobodno,
                    'iskoristeno' => $placeno_odsustvo_5_iskoristeno
                ],
            'placeno-odsustvo-6' =>
                [
                    'ukupno' => $placeno_odsustvo_6_ukupno,
                    'slobodno' => $placeno_odsustvo_6_slobodno,
                    'iskoristeno' => $placeno_odsustvo_6_iskoristeno
                ],
            'placeno-odsustvo-7' =>
                [
                    'ukupno' => $placeno_odsustvo_7_ukupno,
                    'slobodno' => $placeno_odsustvo_7_slobodno,
                    'iskoristeno' => $placeno_odsustvo_7_iskoristeno
                ],
            'ostala-placena' =>
                [
                    'iskoristeno' => $ostala_placena_iskoristeno
                ]
        ];

        foreach ($kvote as $key => $value) {
            if ($value['slobodno'] < 0) {
                $kvote[$key]['slobodno'] = 0;
            }
        }

        return $kvote;

    }
}