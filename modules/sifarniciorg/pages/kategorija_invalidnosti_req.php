<?php
require_once '../../../configuration.php';


if ($_POST['type'] == 'add'){
    //var_dump($_POST);
    $category = $_POST['category'];
    $num_of_days = $_POST['number_of_days'];

    if(is_numeric($num_of_days) && floor( $num_of_days ) == $num_of_days and  ctype_digit($category) == false and $num_of_days > 0){
        $double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] where [category]='".$category."'");

        if(count($double_check_sql->fetchAll()) > 0){
            echo json_encode('duplicate');
        }
        else{
            try {
                $sql = "INSERT INTO [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] ([category],
	                    [number_of_days],
	                    [active]) values (?, ?, ?)";

                $dbq = $db->prepare($sql);
                $dbq->execute([$category, $num_of_days, 1]);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
            echo json_encode('ijjnij');
        }
    }
    else{
        echo json_encode('nonnum');
    }


}

if ($_POST['type'] == 'del'){
    try {
        //$sql = "UPDATE [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] set Active = 0 where id=".$_POST['id'];
        $sql = "DELETE FROM [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] where id=".$_POST['id'];
        $dbq = $db->query($sql);

    }catch (Exception $e){
        var_dump($e);
        die();
    }

}

if ($_POST['type'] == 'edit'){
    $category = $_POST['category'];
    $num_of_days = $_POST['number_of_days'];

    if(is_numeric($num_of_days) && floor( $num_of_days ) == $num_of_days and  ctype_digit($category) == false and $num_of_days > 0){
//        $double_check_sql = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] where [category]='".$category."' and ");
//
//        if(count($double_check_sql->fetchAll()) > 0){
//            echo json_encode('duplicate');
//        }

            try {
                $sql = "update [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] set
	                    [number_of_days]=?,
	                    [active]=? where id=". $_POST['id'];

                $dbq = $db->prepare($sql);
                $dbq->execute([$num_of_days, 1]);
            }catch (Exception $e){
                var_dump($e);
                die();
            }
            echo json_encode('ijjnij');

    }
    else{
        echo json_encode('nonnum');
    }


}


