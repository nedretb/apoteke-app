<?php

$db=new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_apoteke;", "intranet", "DynamicsNAV16!");

$file = fopen('csv1.csv', 'r');

while(($line = fgetcsv($file)) !== false){
    print_r($line);
    echo "<br>";
    $sqlStatement = "UPDATE [c0_intranet2_apoteke].[dbo].[users__podaci_o_rodjenju] SET datum_rodjena='".$line[1]."' WHERE employee_no='".$line[0]."'";
    var_dump($inject = $db->prepare($sqlStatement)->execute());
    echo '<br>';
}

fclose($file);

