<?php
include '../../../configuration.php';
include $root . '/classes/API/SoapEvents.php';
include 'func.php';

if (!class_exists('SoapClient')) {
    die ("You haven't installed the PHP-Soap module.");
}
use SoapEvents as Soap;


$data = Soap::getData('http://172.16.10.38:5203/PRH_WS/WS/JU%20Apoteke%20Sarajevo/Page/PRHWS_VacationPlan', 'ReadMultiple',
    ['filter' => [['Field' => 'Employee_No', 'Criteria' => 424], ['Field' => 'Year_of_Vacation', 'Criteria' => 2021]], 'setSize' => '']);


foreach ($data->ReadMultiple_Result->PRHWS_VacationPlan as $a){
    $check = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] WHERE employee_no=".$a->Employee_No." AND 
    vacation_code='".$a->Vacation_Term_Code."' AND year=".$a->Year_of_Vacation)->fetch()[0];

    if ($check == 1){
        try {
            $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] SET no_of_days=? WHERE employee_no=".$a->Employee_No." AND vacation_code='".$a->Vacation_Term_Code."' AND year=".$a->Year_of_Vacation;
            $sqlInjection = $db->prepare($sqlStmt);
            $sqlInjection->execute([$a->Days]);
        }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
    }
    else{
        try {
            $sqlStmt = "INSERT INTO [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] ([employee_no]
      ,[vacation_code]
      ,[year]
      ,[no_of_days]) VALUES (?, ?, ?, ?)";
            $sqlInjection = $db->prepare($sqlStmt);
            $sqlInjection->execute([$a->Employee_No, $a->Vacation_Term_Code, $a->Days, $a->Year_of_Vacation]);
        }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
    }
}