<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_lista_korisnika(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        user_id int
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}