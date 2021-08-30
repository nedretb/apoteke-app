<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_kalendar(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        faza int,
        datum_od date,
        datum_do date
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}