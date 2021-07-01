<?php
require_once '../../../configuration.php';
include('functions.pdf.php');
include('table_used.php');

$id = $_GET['id'];
$_user = _user(_decrypt($_SESSION['SESSION_USER']));

if (!isset($id) || $id == "" || !is_numeric($id)) {
    header("Location: /$_conf[app_location_module]/modules/default/unauthorized.php");
}

$check = $db->query("SELECT * FROM $database_used.[requests] WHERE request_id = '$id' and employee_no = '$_user[employee_no]' and type = 'DEC' and status='1'");
$check->execute();

$data = $check->fetchAll();
$year = $data[0]['year'];
$employee_no = $data[0]['employee_no'];

$get_vacation_statistics_query = $db->query("SELECT br_dana, zakonska_osnova FROM $database_used.[vacation_statistics] WHERE year = '$year' and employee_no = '$employee_no'");
$vacation_data = $get_vacation_statistics_query->fetchAll();

//new
$check2 = $db->query("SELECT * FROM [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$' . "Vacation Grounds] where [Employee No_]='$employee_no' and Year= '$year' ");
$data[2] = $check2->fetch();
//

$count = $check->rowCount();
if ($count == 0) {
    header("Location: /$_conf[app_location_module]/modules/default/unauthorized.php");
}

// TODO
// if state == bih..rs..bd
//

$get = $db->query("SELECT * FROM  " . $nav_employee . "  WHERE [No_]='" . $employee_no . "'");
if ($get->rowCount() < 0) {
    $row_employee = $get->fetch();
}
$entitet = $row_employee['Org Entity Code'];

switch ($entitet) {
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