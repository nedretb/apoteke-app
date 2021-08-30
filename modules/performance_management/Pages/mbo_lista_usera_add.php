<?php
if(isset($_POST['check_attr_id'])){
    $user_id = $_POST['check_attr_id'];
    $val = ($_POST['value'] == 'true') ? 1 : 0;

    $rows = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = ".$user_id)->fetchAll());
    try{
        if($rows and $val == 0){
            $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id =".$user_id);
        }else if(!$rows and $val == 1){
            $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] (user_id) VALUES ('{$user_id}')");
        }
    }catch (PDOException $e){var_dump($e);}
}