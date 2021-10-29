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
    $data = Soap::getData('http://172.16.10.38:5203/PRH_WS/WS/JU%20Apoteke%20Sarajevo/Page/PRHWS_EmployeeRelative', 'ReadMultiple',
        ['filter' => ['Field' => 'Employee_No', 'Criteria' => $user['employee_no']], 'setSize' => '']);

    if(!empty($data->ReadMultiple_Result->PRHWS_EmployeeRelative) and gettype($data->ReadMultiple_Result->PRHWS_EmployeeRelative) == 'array'){

        foreach ($data->ReadMultiple_Result->PRHWS_EmployeeRelative as $a){

            $check = checkIfExistsKids($db, $a);

            if ($check == 1){
                updateKids($db, $a);
            }
            else{
                insertKids($db, $a);
            }
        }
    }
    elseif (!empty($data->ReadMultiple_Result->PRHWS_EmployeeRelative) and gettype($data->ReadMultiple_Result->PRHWS_EmployeeRelative) == 'object'){
        $check = checkIfExistsKids($db, $data->ReadMultiple_Result->PRHWS_EmployeeRelative);
        if ($check == 1){
            updateKids($db, $data->ReadMultiple_Result->PRHWS_EmployeeRelative);
        }
        else{
            insertKids($db, $data->ReadMultiple_Result->PRHWS_EmployeeRelative);
        }
    }
}

function updateKids($db, $data){
    try {
        $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__podaci_o_djeci] SET ime_prezime_djeteta=?, datum_rodjenja=?, spol=? WHERE employee_no=".$data->Employee_No." AND ime_prezime_djeteta='".$data->First_Name.' '.$data->Last_Name."'";
        $sqlInjection = $db->prepare($sqlStmt);
        $sqlInjection->execute([$data->First_Name.' '.$data->Last_Name, $data->Birth_Date, $data->GetGender]);
    }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
}

function insertKids($db, $data){
    try {
        $sqlStmt = "INSERT INTO [c0_intranet2_apoteke].[dbo].[users__podaci_o_djeci] ([employee_no]
      ,[ime_prezime_djeteta]
      ,[datum_rodjenja]
      ,[spol]) VALUES (?, ?, ?, ?)";
        $sqlInjection = $db->prepare($sqlStmt);
        $sqlInjection->execute([$data->Employee_No, $data->First_Name.' '.$data->Last_Name, $data->Birth_Date, $data->GetGender]);
    }catch (Exception $e){ logThis($e->getMessage(), $e->getLine(), $e->getFile());}
}

function checkIfExistsKids($db, $data){
    return $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[users__podaci_o_djeci] WHERE employee_no=".$data->Employee_No." AND 
    ime_prezime_djeteta='".$data->First_Name.' '.$data->Last_Name."' AND spol='".$data->GetGender."'")->fetch()[0];
}