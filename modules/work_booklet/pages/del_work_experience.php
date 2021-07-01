<?php
//echo 'yoyo';
//    var_dump($_GET);
//    var_dump($_POST);
//    die();
//global $db;
$db=new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");

$emp_no = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where id=".$_GET['edit'])->fetch();
//$check_employer = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where id=".$_GET['edit']);
try{
    $sql = "delete from [c0_intranet2_apoteke].[dbo].[work_booklet] where id=".$_GET['edit'];
    $dbq = $db->prepare($sql);
    $dbq->execute();
}catch(Exception $e){
    var_dump($e);
    die();
}
count_go($emp_no['Employee No_']);
if($emp_no['Employer'] == "MKT"){
    header('Location: ?m=work_booklet&p=add-new&edit='.$emp_no['Employee No_'].'&pp=0');
}else{
    header('Location: ?m=work_booklet&p=add-new&edit='.$emp_no['Employee No_'].'&pp=1');
}

?>