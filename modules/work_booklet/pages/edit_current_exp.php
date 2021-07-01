<?php
var_dump("edit");
var_dump($_POST);
global $db;
$emp_no = $_POST['employee_no'];
$employee_name = explode(" ", $_POST['employee_name']);
$employer = $_POST['employer'];
$starting_date = date("Y-m-d", strtotime($_POST['dateFrom']));
$ending_date = date('Y-m-d');
$coefficient = $_POST['coefficient'];
$dc = $_POST['dc'];
$invalid = $_POST['invalid'];

if ($invalid == 'DA'){
    $invalidity_category = $_POST['invalidity_category'];
}
else{
    $invalidity_category = 0;
}

$coefficient_check = $db->prepare("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where [coefficient]=".$coefficient. " and [Employee No_]=".$emp_no);

if($coefficient_check->rowCount() < 0){

}
else{
    $data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=".$emp_no)->fetch();
    try {
        $sql_coeff = "INSERT INTO [c0_intranet2_apoteke].[dbo].[coefficient_history] ([employee_no]
      ,[coefficient]
      ,[active]
      ,[date_from]
      ,[date_to]) values (?, ?, ?, ?, ?)";
        $db_coeff = $db->prepare($sql_coeff);
        $db_coeff->execute([$emp_no, $data['Coefficient'], 0, $data['Starting Date'], $data['Ending Date']]);
    }
    catch (Exception $e){}
}

try{
    $sql = "update [c0_intranet2_apoteke].[dbo].[work_booklet] set
        [Employee No_] = ?
      ,[First Name] = ?
      ,[Last Name] = ?
      ,[Starting Date] = ?
      ,[Ending Date] = ?
      ,[Employer] = ?
      ,[Coefficient] = ?
      ,[invalid] = ?
      ,[invalid_category] = ?
      ,[child_disabled]= ? where [Employee No_]=".$emp_no;

    $dbq = $db->prepare($sql);
    $dbq->execute([$emp_no, $employee_name[0], $employee_name[1], $starting_date, $ending_date, $employer, $coefficient, $invalid, $invalidity_category, $dc]);

}catch(Exception $e){
    var_dump($e);
    die();
}



header('Location: ?m=work_booklet&p=add-new&edit='.$emp_no);
?>