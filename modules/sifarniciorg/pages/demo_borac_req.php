<?php
require_once '../../../configuration.php';


if ($_POST['type'] == 'add'){
    $min_god = $_POST['min_god'];
    $max_god = $_POST['max_god'];
    $number_of_days = $_POST['number_of_days'];

    if (is_numeric($min_god) and is_numeric($max_god) and is_numeric($number_of_days) and $min_god > 0 and $max_god > 0 and $number_of_days > 0){

        if( $min_god >= $max_god){
            echo json_encode('minmax');
        }
        else{
            $double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] where [min]='".$min_god."' and [max]=".$max_god);

            if($double_check_sql->rowCount() < 0){
                echo json_encode('duplicate');
            }
            else{
                try {
                    $sql = "INSERT INTO [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] ([min]
                              ,[max]
                              ,[number_of_days]
                              ,[active]) values (?, ?, ?, ?)";

                    $dbq = $db->prepare($sql);
                    $dbq->execute([$min_god, $max_god, $number_of_days, 1]);
                }catch (Exception $e){
                    var_dump($e);
                    die();
                }
                echo json_encode('tttt');
            }
        }

    }
    else{
        echo json_encode('nonnum');
    }


}

if ($_POST['type'] == 'del'){
    try {
        //$sql = "UPDATE [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] set Active = 0 where id=".$_POST['id'];
        $sql = "DELETE FROM [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] where id=".$_POST['id'];
        $dbq = $db->query($sql);

    }catch (Exception $e){
        var_dump($e);
        die();
    }

}

if ($_POST['type'] == 'edit'){
    $min_god = $_POST['min_god'];
    $max_god = $_POST['max_god'];
    $number_of_days = $_POST['number_of_days'];

    if (is_numeric($min_god) and is_numeric($max_god) and is_numeric($number_of_days) and $min_god > 0 and $max_god > 0 and $number_of_days > 0){

        if( $min_god >= $max_god){
            echo json_encode('minmax');
        }
        else{
            //$double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] where [min]='".$min_god."' and [max]=".$max_god);

//            if($double_check_sql->rowCount() < 0){
//                echo json_encode('duplicate');
//            }
//            else{
            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[sifarnik_demobilizirani_borac] set [number_of_days]=? where id=".$_POST['id'];

                $dbq = $db->prepare($sql);
                $dbq->execute([$number_of_days]);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
            echo json_encode('tttt');
            //}
        }

    }
    else{
        echo json_encode('nonnum');
    }


}
