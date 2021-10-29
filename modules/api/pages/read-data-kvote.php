<?php
include '../../../configuration.php';
include $root . '/classes/API/SoapEvents.php';
include 'func.php';

if (!class_exists('SoapClient')) {
    die ("You haven't installed the PHP-Soap module.");
}
use SoapEvents as Soap;

$users = $db->query("SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users]");

foreach ($users as $user){
    $data = Soap::getData('http://172.16.10.38:5203/PRH_WS/WS/JU%20Apoteke%20Sarajevo/Page/PRHWS_VacationPlan', 'ReadMultiple',
        ['filter' => [['Field' => 'Employee_No', 'Criteria' => $user['employee_no']], ['Field' => 'Year_of_Vacation', 'Criteria' => 2021]], 'setSize' => '']);

    if(!empty($data->ReadMultiple_Result->PRHWS_VacationPlan) and gettype($data->ReadMultiple_Result->PRHWS_VacationPlan) == 'array'){

        foreach ($data->ReadMultiple_Result->PRHWS_VacationPlan as $a){

            $check = checkIfExists($db, $a);

            if ($check == 1){
                updateAdditionalDays($db, $a);
            }
            else{
                insertAdditionalDays($db, $a);
            }
        }
    }
    elseif (!empty($data->ReadMultiple_Result->PRHWS_VacationPlan) and gettype($data->ReadMultiple_Result->PRHWS_VacationPlan) == 'object'){
        $check = checkIfExists($db, $data->ReadMultiple_Result->PRHWS_VacationPlan);
        if ($check == 1){
            updateAdditionalDays($db, $data->ReadMultiple_Result->PRHWS_VacationPlan);
        }
        else{
            insertAdditionalDays($db, $data->ReadMultiple_Result->PRHWS_VacationPlan);
        }
    }
    else{logThis('User has no data employee_no: '.$user['employee_no'], '', '');}

}

function updateAdditionalDays($db, $data){
    try {
        $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] SET no_of_days=? WHERE employee_no=".$data->Employee_No." AND vacation_code='".$data->Vacation_Term_Code."' AND year=".$data->Year_of_Vacation;
        $sqlInjection = $db->prepare($sqlStmt);
        $sqlInjection->execute([$data->Days]);
    }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
}

function insertAdditionalDays($db, $data){
    try {
        $sqlStmt = "INSERT INTO [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] ([employee_no]
      ,[vacation_code]
      ,[year]
      ,[no_of_days]) VALUES (?, ?, ?, ?)";
        $sqlInjection = $db->prepare($sqlStmt);
        $sqlInjection->execute([$data->Employee_No, $data->Vacation_Term_Code, $data->Days, $data->Year_of_Vacation]);
    }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
}

function checkIfExists($db, $data){
    return $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[dodatni_dani_go] WHERE employee_no=".$data->Employee_No." AND 
    vacation_code='".$data->Vacation_Term_Code."' AND year=".$data->Year_of_Vacation)->fetch()[0];
}