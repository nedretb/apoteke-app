<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_sporazumi_kompetencije_rel(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        sporazum_id int,
        kompetencija_id int,
        checked int,
        ocjena int,
        komentar text,
        created_at datetime
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}