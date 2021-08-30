<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_sporazumi_ciljevi(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        id_sporazuma int,
        kategorija int,
        naziv_cilja text,
        opis_cilja text,
        tezina text,
        disabled int,
        
        realizacija_cilja text,
        ocjena int,
        komentar text,
        last_one int,
        
        created_at datetime
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}