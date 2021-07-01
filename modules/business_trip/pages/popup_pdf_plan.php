<?php
require_once '../../../configuration.php';
include('generate_pdf_plan.php');


//echo 'weeeeeee';
//var_dump($_GET);
//var_dump($_POST);
//die();
$org_jed = str_replace('-',' ', $_GET['sec']);

if ($org_jed == 'Svi'){
    $condition = "";
}
else{
    $condition = "  x.B_1_description='".$org_jed."' and ";
}

//var_dump($condition);
//die();
$data = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sl_put] as e join [c0_intranet2_apoteke].[dbo].[countries] as r on e.odredisna_drzava=r.country_id 
join [c0_intranet2_apoteke].[dbo].[users] as x on e.employee = x.employee_no
where ".$condition." YEAR(pocetak_datum)=2021 ");

//var_dump($data);

//var_dump($country);
//die();
$id=59;
$id=59;
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
$database_used = "[c0_intranet2_apoteke].[dbo]";

//$queryq = $db->query("select *  from [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$' . "Vacation Ground 2] as v
//	join  " . $nav_employee . "  as e on v.[Employee No_] = e.[No_]
//	join $database_used.[users] as u on u.employee_no = e.[No_]
//  where '" . $id . "' = v.id ");
//$data = $queryq->fetch(PDO::FETCH_ASSOC);

generatepdf('fbih', $_user, $data, $org_jed);
//$queryq = $db->query("select o.[Entity Code]  from [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$' . "Employee Contract Ledger] as c join [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$' . "ORG Dijelovi] as o on o.Code=c.[Org Dio] and o.GF=c.[GF rada code] where [Show Record] = 1 and [Employee No_]=" . $data['Employee No_'] . " and [Starting Date] <= '" . $data['Insert Date'] . "' and ([Ending Date] >= '" . $data['Insert Date'] . "' or  [Ending Date] = '1753-01-01 00:00:00.000') ");
//$dataq = $queryq->fetch(PDO::FETCH_ASSOC);
//
//
//$entitet = $dataq['Entity Code'];
//
//$vacation_data[0] = 0;
//
//switch ($entitet) {
//    case "FBIH":
//        generatepdf('fbih', $_user, $data, $vacation_data[0]);
//        break;
//
//    case "RS":
//        generatepdf('rs', $_user, $data, $vacation_data[0]);
//        break;
//
//    case "BD":
//        generatepdf('bd', $_user, $data, $vacation_data[0]);
//        break;
//}
//generatepdf('fbih', $_user, $data, $vacation_data[0]);
//generatepdf('rs', $_user, $data, $vacation_data[0]);
//generatepdf('bd', $_user, $data, $vacation_data[0]);

?>