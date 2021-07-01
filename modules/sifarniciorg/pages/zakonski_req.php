<?php
require_once '../../../configuration.php';


if ($_POST['type'] == 'add'){

    $region = $_POST['region'];
    $num_of_days = $_POST['number_of_days'];
    $year = $_POST['year'];

    if(is_numeric($num_of_days) && floor( $num_of_days ) == $num_of_days and is_numeric($year) and $num_of_days > 0){
        $double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where  region='".$region."' and year=".$year);

        if(count($double_check_sql->fetchAll()) > 0){
            echo json_encode('duplicate');
        }
        else{
            try {
                $sql = "INSERT INTO [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] ([region]
      ,[number_of_days]
      ,[year]
      ,[active]) values (?, ?, ?, ?)";

                $dbq = $db->prepare($sql);
                $dbq->execute([$region, $num_of_days, $year, 1]);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
            echo json_encode('kekkeke');
            //header('Location: ?m=sifarniciorg&p=zakonski');
        }
    }
    else{
        echo json_encode('nonnum');
    }
}

if ($_POST['type'] == 'del'){
    try {
        //$sql = "UPDATE [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] set Active = 0 where id=".$_POST['id'];
        $sql = "DELETE from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where id=".$_POST['id'];
        $dbq = $db->query($sql);
        echo json_encode('nonnum');
    }catch (Exception $e){
        var_dump($e);
        die();
    }
    //header('Location: ?m=sifarniciorg&p=zakonski');
}


if ($_POST['type'] == 'edit'){

    $region = $_POST['region'];
    $num_of_days = $_POST['number_of_days'];
    $year = $_POST['year'];

    if(is_numeric($num_of_days) && floor( $num_of_days ) == $num_of_days and is_numeric($year) and $num_of_days > 0){
        //$double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where  region='".$region."' and year=".$year);

//        if(count($double_check_sql->fetchAll()) > 0){
//            echo json_encode('duplicate');
//        }

            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] set 
       [number_of_days]=?
      ,[active]=? where id=".$_POST['id'];

                $dbq = $db->prepare($sql);
                $dbq->execute([$num_of_days, 1]);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
            echo json_encode('kekkeke');
            //header('Location: ?m=sifarniciorg&p=zakonski');

    }
    else{
        echo json_encode('nonnum');
    }
}
