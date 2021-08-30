<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_ocjene(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        value int,
        name text
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}