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
        $demo_borac_br_mjeseci = $demo_borac['demobilizirani_borac_m'];
        $demo_borac_br_dana = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] where ".$demo_borac_br_mjeseci.">= min and ".$demo_borac_br_mjeseci." <max")->fetch()['number_of_days'];
        $total_go_days += $demo_borac_br_dana;
    }

    /*** Na osnovu koeficijenta slozenosti poslova ***/
    if($koeficijent_slozenosti == null){
        $koeficijent_slozenosti = 0;
    }
    $koeficijent_slozenosti_br_dana = $db->query("select number_of_days from [c0_intranet2_apoteke].[dbo].[sifarnik_slozenosti_poslova] where min < ".$koeficijent_slozenosti." and ".$koeficijent_slozenosti." <=max")->fetch()['number_of_days'];
    $total_go_days += $koeficijent_slozenosti_br_dana;

    try {
        $updateVacationStatistics = $db->query("update [c0_intranet2_apoteke].[dbo].[vacation_statistics] set Br_Dana=".$total_go_days." where employee_no=".$rs['employee_no']. " and year=".date('Y'));
        $vac_stat = $db->query("select * from [c0_intranet2_apoteke].[dbo].[vacation_statistics] where employee_no=".$rs['employee_no']." and year=2021");
    }catch (Exception $e){

    }
}

?>