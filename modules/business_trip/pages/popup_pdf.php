<?php
require_once '../../../configuration.php';
include('generate_pdf.pdf.php');

$data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sl_put] as v
join [c0_intranet2_apoteke].[dbo].[users] as x on v.employee=x.employee_no where id=".$_GET['id'])->fetch();
//var_dump($data['odredisna_drzava']);
//die();
$country = $db->query("select * from [c0_intranet2_apoteke].[dbo].[countries] where country_id=".$data['odredisna_drzava'])->fetch();

$_user = _user(_decrypt($_SESSION['SESSION_USER']));
$database_used = "[c0_intranet2_apoteke].[dbo]";



generatepdf('fbih', $_user, $data, $country, $_GET['doc']);

?>