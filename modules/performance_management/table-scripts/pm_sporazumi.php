<?php

// user_id => ID Managera -- SMIJE SE SAMO JEDNOM POJAVITI !!
// impersonator_id => ID korisnika koji ima ulogu impersonatora

try{
    $sql ="CREATE table pm_sporazumi(
        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
        user_id int, 
        sent int,
        accepted_from_supervisor int,
        accepted_from_employee int,
        status int,
        category int,
        year int,
        goal_grade text,
        competences_grade text,
        final_grade text,
        recomended_grade text,
        supervisor_comm text,
        development_plan text,
        user_comm text,        
        
        f1_user date,
        f2_user date,
        f3_user date,
        
        f1_sup date,
        f1_id int,
        f2_sup date,
        f2_id int,
        f3_sup date,
        f3_id int,
    
        created_at datetime,
        unlocked int,
        created int
     )";
    $db->exec($sql);
}catch (PDOException $e){
    die($e->getMessage());
}