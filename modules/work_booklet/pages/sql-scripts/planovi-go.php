<?php

try{
    $sql ="CREATE table plan_go(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        uredska_godina int,
        rbr_predmeta text,
        status int, 
        created_at datetime
     )";
    $db->exec($sql);

    $sql ="CREATE table plan_go_pismena(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        uredska_godina int,
        rbr_predmeta text,
        jop varchar (200),
        status int, 
        dokument text,
        napomena text,
        created_at datetime
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}