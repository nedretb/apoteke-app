<?php
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Carbon\Carbon;

$radni_staz = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users__radni_staz]");



foreach ($radni_staz as $rs){
    $total_go_days = 0;


    $year = $rs['ukupan_radni_staz_g'];
    $months = $rs['ukupan_radni_staz_m'];
    $days = $rs['ukupan_radni_staz_d'];

    /*** Parametri za broj dana GO ***/

    $zakonski = $db->query("select number_of_days from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where region='FBIH'")->fetch()['number_of_days'];
    $godina_rada = $db->query("select number_of_days from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go] where min < ".$year." and max >=".$year." ")->fetch()['number_of_days'];
    $dezurstva = $db->query("select dezurstva from [c0_intranet2_apoteke].[dbo].[users] where employee_no=".$rs['employee_no'])->fetch()['dezurstva'];
    $koeficijent_slozenosti = $db->query("select koeficijent_slozenosti_poslova from [c0_intranet2_apoteke].[dbo].[users__poreska_olaksica_i_prevoz] where employee_no=".$rs['employee_no'])->fetch()['koeficijent_slozenosti_poslova'];
    $demo_borac = $db->query("select demobilizirani_borac, demobilizirani_borac_m from [c0_intranet2_apoteke].[dbo].[users__poreska_olaksica_i_prevoz] where employee_no=".$rs['employee_no'])->fetch();

    /*** ***/

    /*** Zakonska osnova ***/
    if($year > 0 or $months >=6){
        $total_go_days += $zakonski;
    }


    /*** Na osnovu godina radnog iskustva ***/
    if($year > 20){
        $total_go_days += 5;
    }

    $total_go_days += $godina_rada;

    /*** Na osnovu dezurstva ***/
    if ($dezurstva == 'Da'){
        $total_go_days += 1;
    }

    /*** Na osnovu ucesca u ratu 92-95 ***/
    if($demo_borac['demobilizirani_borac'] == 'Da'){
        $mjsc = $demo_borac['demobilizirani_borac_m'];

        if ($mjsc >= 12 and $mjsc < 18){
            $total_go_days += 1;
        }
        elseif ($mjsc >= 18 and $mjsc < 30){
            $total_go_days += 2;
        }
        elseif ($mjsc >= 30){
            $total_go_days += 3;
        }
    }

    /*** Na osnovu koeficijenta slozenosti poslova ***/
    if ($koeficijent_slozenosti == 1){
        $total_go_days += 1;
    }
    elseif ($koeficijent_slozenosti > 1 and $koeficijent_slozenosti <= 2){
        $total_go_days += 2;
    }
    elseif ($koeficijent_slozenosti > 2 and $koeficijent_slozenosti <= 3){
        $total_go_days += 3;
    }
    elseif ($koeficijent_slozenosti > 3 and $koeficijent_slozenosti <= 4){
        $total_go_days += 4;
    }
    elseif ($koeficijent_slozenosti > 4){
        $total_go_days += 5;
    }

    try {
        $vac_stat = $db->query("select * from [c0_intranet2_apoteke].[dbo].[vacation_statistics] where employee_no=".$rs['employee_no']);
        $statement = "update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_Dana=".$total_go_days." where employee_no=".$rs['employee_no']. " and year=".date('Y');
        $sql = $db->query($statement);

    }catch (Exception $e){

    }
}

?>