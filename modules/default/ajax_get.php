<?php

require_once '../../configuration.php';
require_once '../../configuration.php';

  if(DEBUG){

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

  }


if(isset($_GET['request'])){
	  if($_GET['request']=='check-month-add'){
		

   $query = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month]  where month = ".$_GET['month']);
     
	foreach ($query as $item) {
		$num_users = $item['broj'];
}
		echo $num_users;
}
 if($_GET['request']=='check-month-add-new'){
	 
	 $year = $_GET['year'];

   $query = $db->query("SELECT count(*) as broj
  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month]
  join
  [c0_intranet2_apoteke].[dbo].[hourlyrate_year]
  on [c0_intranet2_apoteke].[dbo].[hourlyrate_month].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
  where [c0_intranet2_apoteke].[dbo].[hourlyrate_year].year=".$year);
     
	foreach ($query as $item) {
		$num_users = floor($item['broj']/12);
}
		echo $num_users;
    
}
}

?>
