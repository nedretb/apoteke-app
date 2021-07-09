<?php
error_reporting(E_ALL);
$db=new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");

require_once '../../../CORE/app.php';
//require_once '../../../CORE/functions.main.php';
include '../functions.php';

if(isset($_POST['employee_no'])){
    $emp_no = $_POST['employee_no'];
}

if(isset($_POST['employee_name'])){
    $employee_name = explode(" ", $_POST['employee_name']);
}

if(isset($_POST['employer'])){
    $employer = $_POST['employer'];
}

if (!empty($_POST['dateFrom'])) {
    $starting_date_insert = date("Y-m-d", strtotime($_POST['dateFrom']));
    $starting_date = strtotime($_POST['dateFrom']);
}else{
    $starting_date_insert = null;
}

if (!empty($_POST['dateTo'])) {
    $ending_date_insert = date("Y-m-d", strtotime($_POST['dateTo']));
    $ending_date = strtotime($_POST['dateTo']);
}else{
    $ending_date_insert = null;
}

if(isset($_POST['coefficient'])){
    $coefficient = $_POST['coefficient'];
}

if(isset($_POST['dc'])){
    $dc = $_POST['dc'];
}

if(isset($_POST['invalid'])){
    $invalid = $_POST['invalid'];
    if ($invalid == 'DA'){
        $invalidity_category = $_POST['invalidity_category'];
    }
    else{
        $invalidity_category = 0;
    }
}


if ($_POST['tip'] == 'get_name'){
    $gg = $db->query("select fname, lname from [c0_intranet2_apoteke].[dbo].[users] where employee_no=".$_POST['employee_no'])->fetch();
    echo html_entity_decode($gg['fname'].' '.$gg['lname']);
}

if ($_POST['tip'] == 'get_emp_no'){
    $names = explode(' ', $_POST['employee_no']);
    $gg = $db->query("select employee_no from [c0_intranet2_apoteke].[dbo].[users] where fname=N'".$names[0]."' and lname=N'".$names[1]."'")->fetch();
    echo json_encode($gg['employee_no']);
}

if($_POST['tip'] == 'save'){

    if ($starting_date_insert > date('Y-m-d') or $ending_date_insert > date('Y-m-d')){
        echo json_encode('double_date');
    }
    else{
        $result = HelpClass::getDMY($starting_date, $ending_date, $coefficient);

        $emp_no_check = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1 and [Employee No_]=".$emp_no);

        if ($emp_no_check->rowCount() < 0){

            try{
                $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[work_booklet] set
                [Employee No_]=?
                ,[First Name]=?
                ,[Last Name]=?
                ,[Starting Date]=?
                ,[Ending Date]=?
                ,[Employer]=?
                ,[Coefficient]=?
                ,[invalid]=?
                ,[invalid_category]=?
                ,[child_disabled]=?
                ,[Active]=?
                ,[previous_exp_y]=?
                ,[previous_exp_m]=?
                ,[previous_exp_d]=?  where id=".$_POST['id'];
                $dbq = $db->prepare($sql);
                $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, $invalid, $invalidity_category, $dc, 1, $result['y'], $result['m'], $result['d']]);

//                $sql2= "update [c0_intranet2_apoteke].[dbo].[users] set employment_date=? where employee_no=".$emp_no;
//                $dbq2 = $db->prepare($sql2);
//                $dbq2->execute([$starting_date_insert]);

                count_go($emp_no);
            }catch(Exception $e){
                var_dump($e);
            }
            echo json_encode("ww");
        }
        else{

            $check_dates = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=".$emp_no." and ('".$starting_date_insert."' between [Starting Date] and [Ending Date]
or '".$ending_date_insert."' between [Starting Date] and [Ending Date])");


            if ($check_dates->rowCount() < 0){
                echo json_encode("double_date");
            }
            else{
                try{
                    $sql = "INSERT INTO [c0_intranet2_apoteke].[dbo].[work_booklet]
                ([Employee No_]
                ,[First Name]
                ,[Last Name]
                ,[Starting Date]
                ,[Ending Date]
                ,[Employer]
                ,[Coefficient]
                ,[invalid]
                ,[invalid_category]
                ,[child_disabled]
                ,[Active]
                ,[previous_exp_y]
                ,[previous_exp_m]
                ,[previous_exp_d]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $dbq = $db->prepare($sql);

                    $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, $invalid, $invalidity_category, $dc, 1, $result['y'], $result['m'], $result['d']]);
//                    $sql2= "update [c0_intranet2_apoteke].[dbo].[users] set employment_date=? where employee_no=".$emp_no;
//                    $dbq2 = $db->prepare($sql2);
//                    $dbq2->execute([$starting_date_insert]);

                    count_go($emp_no);
                }catch(Exception $e){
                    var_dump($e);
                }
                echo json_encode("ww");
            }

        }
    }

}


if($_POST['tip'] == 'archive'){
    $date_diff = round((($ending_date - $starting_date)/(60*60*24)) * $coefficient);
    $years = floor($date_diff / 365);
    $months = floor(($date_diff - $years * 365) / 30);
    $days = $date_diff - $years * 365 - 30 * $months + 1;

    if (isset($_POST['id'])){
        try{
            $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[work_booklet] set [Active]=0 where id=".$_POST['id'];
            $dbq = $db->prepare($sql);
            $dbq->execute();

        }catch(Exception $e){
            var_dump($e);
            die();
        }
    }
    else{
        echo 'nema id';
    }

//    try{
//        $sql = "INSERT INTO [c0_intranet2_apoteke].[dbo].[work_booklet]
//                ([Employee No_]
//                ,[First Name]
//                ,[Last Name]
//                ,[Starting Date]
//                ,[Ending Date]
//                ,[Employer]
//                ,[Coefficient]
//                ,[invalid]
//                ,[invalid_category]
//                ,[child_disabled]
//                ,[Active]
//                ,[previous_exp_y]
//                ,[previous_exp_m]
//                ,[previous_exp_d]) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//
//        $dbq = $db->prepare($sql);
//        $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, $invalid, $invalidity_category, $dc, 1, $years, $months, $days]);
//        echo json_encode('ww');
//
//    }catch(Exception $e){
//        var_dump($e);
//        die();
//    }
    echo json_encode('ww');
}


?>