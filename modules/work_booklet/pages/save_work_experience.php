<?php
//echo 'yoyo';
//var_dump($_GET);
//var_dump($_POST);
//die();
//global $db;

require __DIR__ . '/../../../vendor/autoload.php';

use Carbon\Carbon;
require '../functions.php';
require_once '../../../CORE/app.php';

$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");

if (isset($_POST['employee_no'])) {
    $emp_no = $_POST['employee_no'];
}

if (isset($_POST['employee_name'])) {
    $employee_name = explode(" ", $_POST['employee_name']);
}

if (isset($_POST['employer'])) {
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

if (isset($_POST['coefficient'])) {
    $coefficient = $_POST['coefficient'];
}

if (isset($_POST['exp_y'])) {
    $exp_y = $_POST['exp_y'];
}else{
    $exp_y = 0;
}

if (isset($_POST['exp_m'])) {
    $exp_m = $_POST['exp_m'];
}else{
    $exp_m = 0;
}

if (isset($_POST['exp_d'])) {
    $exp_d = $_POST['exp_d'];
}else{
    $exp_d = 0;
}


if ($coefficient == 1) {
    $day_extra = 1;
} else {
    $day_extra = 0;
}

if (!empty($_POST['dateTo']) and !empty($_POST['dateFrom'])){

    $date_diff = round((($ending_date - $starting_date) / (60 * 60 * 24)) * $coefficient);
    $years = floor($date_diff / 365);
    $months = floor(($date_diff - $years * 365) / 30);
    $days = $date_diff - $years * 365 - 30 * $months + $day_extra;

    $result = HelpClass::getDMY($starting_date, $ending_date, $coefficient);

}

if ($_POST['tip'] == 'edit') {
    if ($starting_date_insert > date('Y-m-d') or $ending_date_insert > date('Y-m-d')){
        echo json_encode("double_date");
    }
    else{
        $check_dates = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where id<>".$_POST['id']." and [Employee No_]=".$emp_no." and ('".$starting_date_insert."' between [Starting Date] and [Ending Date]
or '".$ending_date_insert."' between [Starting Date] and [Ending Date])");


        if ($check_dates->rowCount() < 0 or $_POST['']){
            echo json_encode("double_date");
        }
        else{

            try {
                $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[work_booklet] set
                 [Starting Date]=?
                ,[Ending Date]=?
                ,[Employer]=?
                ,[Coefficient]=?
                ,[previous_exp_y]=?
                ,[previous_exp_m]=?
                ,[previous_exp_d]=?
                where id=" . $_POST['id'] . " and [Employee No_]=" . $emp_no;
                $dbq = $db->prepare($sql);

                $dbq->execute([$starting_date_insert, $ending_date_insert, $employer, $coefficient, $result['y'], $result['m'], $result['d']]);
                count_go($emp_no);
            } catch (Exception $e) {
                var_dump($e);
                die();
            }
            echo json_encode('ww');
        }
    }


} elseif ($_POST['tip'] == 'save') {
    if ($starting_date_insert > date('Y-m-d') or $ending_date_insert > date('Y-m-d')){
        echo json_encode("double_date");
    }
    else{
        $check_dates = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where id<>".$_POST['id']." and [Employee No_]=".$emp_no." and ('".$starting_date_insert."' between [Starting Date] and [Ending Date]
or '".$ending_date_insert."' between [Starting Date] and [Ending Date])");


        if ($check_dates->rowCount() < 0){
            echo json_encode("double_date");
        }
        else{

            try {
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
                  ,[previous_exp_d]) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $dbq = $db->prepare($sql);

                $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, "NE", 0, "NE", 0, $result['y'], $result['m'], $result['d']]);

//            if(!empty($_POST['exp_y']) and !empty($_POST['exp_m']) and !empty($_POST['exp_d'])){
//                //var_dump('iset');
//                $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, "NE", 0, "NE", 0, $_POST['exp_y'], $_POST['exp_m'], $_POST['exp_d']]);
//            }
//            else{
//                //var_dump('notset');
//                $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date_insert, $ending_date_insert, $employer, $coefficient, "NE", 0, "NE", 0, $result['y'], $result['m'], $result['d']]);
//            }

                count_go($emp_no);
                echo json_encode('ww');
            } catch (Exception $e) {
                var_dump($e);
                die();
            }
        }

    }
}
//require 'count_go3.php';
//header('Location: ?m=work_booklet&p=add-new&edit='.$emp_no);
?>