<?php

$db=new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");

$file = fopen('ubacit_vac_stat.csv', 'r');

//0, 5
while(($line = fgetcsv($file)) !== false){
    print_r($line);
    echo "<br>";
    $sqlStatement = "UPDATE [c0_intranet2_apoteke].[dbo].[vacation_statistics] SET Br_dana='".$line[5]."' WHERE employee_no='".$line[0]."'";
    var_dump($inject = $db->prepare($sqlStatement)->execute());
    echo '<br>';
}

fclose($file);

