<?php

global $db;
$ending_date = date('Y-m-d');


try{
    $sql = "UPDATE [c0_intranet2_apoteke].[dbo].[work_booklet] set [Ending Date]='".$ending_date."' where Active=1";

    $dbq = $db->query($sql);
}catch(Exception $e){
    var_dump($e);
    die();
}

header('Location: ?m=work_booklet&p=all');
?>