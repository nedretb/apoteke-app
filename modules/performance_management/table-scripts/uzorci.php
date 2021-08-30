<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_sporazumi_uzorci_ciljeva(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        kategorija int,
        naziv_cilja text,
        opis_cilja text,
        tezina text,
        id_usera int,
        keywor int,
        owner int,
        radno_mjesto text,
        for_me int
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}