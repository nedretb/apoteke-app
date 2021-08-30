<?php
$user_id = intval($_GET['user_id']);

try{
    $date = date('Y-m-d');
    $year = date('Y');

    $db->query("
        UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET 
                locked    = '$date'
             where user_id = ".$user_id." and year = ".$year
    );

    $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi] 
            (
                user_id,
                year,
                created,
                forced
            )
            VALUES (
                '$user_id',
                '$year',
                1, 1       
            )
            ");

    $new = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where status is NULL and user_id = ".$user_id)->fetch();
    $sporazum_id = $new['id'];


    $kompetencije = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista]")->fetchAll();
    foreach($kompetencije as $kometencija){
        $kompetencija_id = $kometencija['id'];

        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] (sporazum_id, kompetencija_id, checked) values ('$sporazum_id', '$kompetencija_id', '1')");
    }

}catch (\PDOException $e){}

 header('Location: ?m=performance_management&p=mbo&user_id='.$user_id.'&msg=1');
?>