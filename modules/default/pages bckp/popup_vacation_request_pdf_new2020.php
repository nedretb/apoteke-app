<?php
	  require_once '../../../configuration.php';
	  include('functions2020.pdf.php');
	  include('table_used.php');
	  
	  
	  $id = $_GET['id'];
	  $_user = _user(_decrypt($_SESSION['SESSION_USER'])); 	  
	  
	  
	  
$queryq = $db->query("select *  from [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK".'$'."Vacation Ground 2] as v
	join  ".$nav_employee."  as e on v.[Employee No_] = e.[No_]
	join $database_used.[users] as u on u.employee_no = e.[No_]
  where '".$_GET['id']."' = v.id and [Employee No_]=".$_user['employee_no']);
$data = $queryq->fetch(PDO::FETCH_ASSOC);



$queryq = $db->query("select o.[Entity Code]  from [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK".'$'."Employee Contract Ledger] as c join [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK".'$'."ORG Dijelovi] as o on o.Code=c.[Org Dio] and o.GF=c.[GF rada code] where [Show Record] = 1 and [Employee No_]=".$_user['employee_no']." and [Starting Date] <= '".$data['Insert Date']."' and ([Ending Date] >= '".$data['Insert Date']."' or  [Ending Date] = '1753-01-01 00:00:00.000') ");
$dataq = $queryq->fetch(PDO::FETCH_ASSOC);


	  $entitet = $dataq['Entity Code'];

$vacation_data[0] = 0;

	  switch($entitet){
			case "FBIH":
				generatepdf('fbih', $_user, $data, $vacation_data[0]);
				break;
				
			case "RS":
				generatepdf('rs', $_user, $data, $vacation_data[0]);
				break;
			
			case "BD":
				generatepdf('bd', $_user, $data, $vacation_data[0]);
				break;
	  }
	  //generatepdf('fbih', $_user, $data, $vacation_data[0]);
	  //generatepdf('rs', $_user, $data, $vacation_data[0]); 
	  //generatepdf('bd', $_user, $data, $vacation_data[0]);
	  
?>