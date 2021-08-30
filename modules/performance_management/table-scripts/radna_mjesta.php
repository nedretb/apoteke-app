<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_radna_mjesta(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        naziv text,
        created_at datetime
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}