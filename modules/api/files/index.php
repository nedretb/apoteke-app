<?php

include_once '../../../configuration.php';
include_once $root . '/CORE/classes/MainRequest.php'; // Class for handling requests
include_once $root . '/CORE/classes/Model.php';       // Need to be extended
include_once $root . '/CORE/classes/Models/Profile.php';

$_user = _user(_decrypt($_SESSION['SESSION_USER']));

if(isset($_FILES['photo-input'])){
    $uploaddir  = $root . '\theme\images\profile-images\\';

    $name = md5($_FILES['photo-input']['name']);
    $uploadfile = $uploaddir . basename($name.'.'.pathinfo($_FILES['photo-input']['name'], PATHINFO_EXTENSION));

    if(move_uploaded_file($_FILES['photo-input']['tmp_name'], $uploadfile)){
        echo json_encode([
            'code' => '0000',
            'name' => $name,
            'user' => Profile::imageUplad('employee_no = '.$_user['employee_no'])->update([
                'image' => $name
            ])
        ]);

        try{
            $profile = Profile::imageUplad('employee_no = '.$_user['employee_no'])->first();
        }catch (\Exception $e){}
    }else{
        echo json_encode([
            'code' => '4003',
            'name' => 'Desila se greška. Pokušajte ponovo !'
        ]);
    }
}