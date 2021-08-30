<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_sporazumi_kompetencije(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        id_sporazuma int,
        kategorija int,
        naziv_cilja text,
        opis_cilja text,
        kvalitativno text,
        kvantitativno text,
        tezina text,
        naziv_sa_opisom text,
        
        realizacija_cilja text,
        ocjena int,
        komentar text,
        
        created_at datetime
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}
