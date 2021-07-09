<?php
require_once '../../../configuration.php';
//include('rjesenjeGoCyrilic.pdf.php');
include ('rjesenjeGoLatin.pdf.php');

$employee_no = $_GET['employee_no'];
$year = $_GET['year'];
$years_exp_days = 0;

$user_data = $db->query("select fname, lname, egop_radno_mjesto, egop_ustrojstvena_jedinica from [c0_intranet2_apoteke].[dbo].[users] where employee_no=".$employee_no)->fetch();
$ustrojstvena_jedinica = $db->query("select * from [c0_intranet2_apoteke].[dbo].[systematization] where id='".$user_data['egop_ustrojstvena_jedinica']. "'")->fetch();
$data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[rjesenja_go] where employee_no = ".$employee_no." and godina=".$year)->fetch();
$years_exp = floor($data['broj_dana_radnog_iskustva']/365);

generatepdf($user_data, $data, $ustrojstvena_jedinica, $years_exp);

?>