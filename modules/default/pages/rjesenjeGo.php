<?php
require_once '../../../configuration.php';
//include('rjesenjeGoCyrilic.pdf.php');
include ('rjesenjeGoLatin.pdf.php');

$employee_no = $_GET['employee_no'];
$year = $_GET['year'];
$years_exp_days = 0;
$dodatni_dani = [];

$additional_days = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] where employee_no=".$employee_no);

foreach ($additional_days as $d){

    if($d['vacation_code'] == 'STAZ' and $d['vacation_code'] != null){
        $dodatni_dani['staz'] = $d['no_of_days'];
    }elseif($d['vacation_code'] == 'STAZ' and $d['vacation_code'] == null){
        $dodatni_dani['staz'] = 0;
    }
    if($d['vacation_code'] == 'ARMIJA' and $d['vacation_code'] != null){
        $dodatni_dani['armija'] = $d['no_of_days'];
    }elseif ($d['vacation_code'] == 'ARMIJA' and $d['vacation_code'] == null){
        $dodatni_dani['armija'] = 0;
    }
    if($d['vacation_code'] == '' and $d['vacation_code'] != null){
        $dodatni_dani[''] = $d['no_of_days'];
    }elseif($d['vacation_code'] == 'KOEF_SL' and $d['vacation_code'] == null){
        $dodatni_dani['koef_sl'] = 0;
    }
    if($d['vacation_code'] == 'DIJETE' and $d['vacation_code'] != null){
        $dodatni_dani['dijete'] = $d['no_of_days'];
    }elseif($d['vacation_code'] == 'DIJETE' and $d['vacation_code'] == null){
        $dodatni_dani['dijete'] = 0;
    }
    if($d['vacation_code'] == 'DEZURA' and $d['vacation_code'] != null){
        $dodatni_dani['dezura'] = $d['no_of_days'];
    }elseif($d['vacation_code'] == 'DEZURA' and $d['vacation_code'] == null){
        $dodatni_dani['dezura'] = 0;
    }
}

if(!array_key_exists('staz', $dodatni_dani)){
    $dodatni_dani['staz'] = 0;
}
if(!array_key_exists('armija', $dodatni_dani)){
    $dodatni_dani['armija'] = 0;
}
if(!array_key_exists('koef_sl', $dodatni_dani)){
    $dodatni_dani['koef_sl'] = 0;
}
if(!array_key_exists('dijete', $dodatni_dani)){
    $dodatni_dani['dijete'] = 0;
}
if(!array_key_exists('dezura', $dodatni_dani)){
    $dodatni_dani['dezura'] = 0;
}

$user_data = $db->query("select fname, lname, egop_radno_mjesto, egop_ustrojstvena_jedinica from [c0_intranet2_apoteke].[dbo].[users] where employee_no=".$employee_no)->fetch();
$ustrojstvena_jedinica = $db->query("select * from [c0_intranet2_apoteke].[dbo].[systematization] where id='".$user_data['egop_ustrojstvena_jedinica']. "'")->fetch();
$data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[rjesenja_go] where employee_no = ".$employee_no." and godina=".$year)->fetch();
$years_exp = floor($data['broj_dana_radnog_iskustva']/365);

generatepdf($user_data, $data, $ustrojstvena_jedinica, $dodatni_dani);

?>