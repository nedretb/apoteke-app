<?php

$db = new PDO("sqlsrv:Server=192.168.14.13\NAVDEMO;Database=c0_intranet2_mkt;", "intranet", "DynamicsNAV16!");

if (date('d') == 1) {
    try {
        $updateStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] set editable='N' where Date<?";
        $injection = $db->prepare($updateStmt);
        $injection->execute([date('Y-m-d')]);
    } catch (Exception $e) {}
}