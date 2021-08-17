<?php
require_once "../../../configuration.php";


if ($_POST['type'] == 'add') {
    $rukovodioc = $db->query("SELECT rukovodioc_emp_no FROM [c0_intranet2_apoteke].[dbo].[systematization] where id=" . $_POST['org_jed'])->fetch()['rukovodioc_emp_no'];

    if ($_POST['employee_no'] == 'null') {

    } else {
        $zamjenikProvjera = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[users] WHERE rukovodioc='DA' and egop_ustrojstvena_jedinica=" . $_POST['org_jed'])->fetch()[0];

        if ($zamjenikProvjera > 1) {
            $ukloniZamjenikaStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET rukovodioc='NE' where egop_ustrojstvena_jedinica=? and employee_no<>?";
            $ukloniZamjenika = $db->prepare($ukloniZamjenikaStmt);
            $ukloniZamjenika->execute([$_POST['org_jed'], $rukovodioc]);
        }

        try {
            $sqlStatement = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET rukovodioc='DA' WHERE employee_no=?";
            $prep = $db->prepare($sqlStatement);
            $prep->execute([$_POST['employee_no']]);
        } catch (Exception $e) {

        }


    }
}

if ($_POST['type'] == 'remove') {
    $rukovodioc = $db->query("SELECT rukovodioc_emp_no FROM [c0_intranet2_apoteke].[dbo].[systematization] where id=" . $_POST['org_jed'])->fetch()['rukovodioc_emp_no'];

    try {
        $ukloniZamjenikaStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET rukovodioc='NE' where egop_ustrojstvena_jedinica=? and employee_no<>?";
        $ukloniZamjenika = $db->prepare($ukloniZamjenikaStmt);
        $ukloniZamjenika->execute([$_POST['org_jed'], $rukovodioc]);
    }catch (Exception $e){}

    echo json_encode('removed');
}