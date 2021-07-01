<?php


    include('configuration.php');
    include('modules/core/Model.php');
    include('modules/core/VS.php');

    error_reporting(E_ALL);
    $users = $db->prepare("SELECT employee_no, fname, lname FROM  ".$portal_users." ");
    $users->execute();

    $users_all = $users->fetchAll();


    $i = 1;

    $pronadjeni = [];

    foreach($users_all as $user){
        $kvote = VS::getKvote($user['employee_no'], 2020);

        echo $i . ".";

        if($kvote['go-prethodna-godina']['slobodno'] > 0 and $kvote['go-tekuca-godina']['iskoristeno'] > 0){
            echo $user['fname'] . " ". $user['lname'] . " - (!) UPOZORENJE.";

            $pronadjeni[] = $user['employee_no'];

            $prebaciti = 0;

            if($kvote['go-tekuca-godina']['iskoristeno'] > $kvote['go-prethodna-godina']['slobodno']){
                $prebaciti = $kvote['go-prethodna-godina']['slobodno'];
            } else {
                $prebaciti = $kvote['go-tekuca-godina']['iskoristeno'];
            }

            echo "\n(!) Zapocinje azuriranja satnica za godišnji odmor!";

            $d = $db->prepare("
                
                SELECT id FROM  ".$portal_hourlyrate_day."  WHERE
                
                status = 18 and corr_status = 18 and employee_no = '" . $user['employee_no'] . "'
                and YEAR(Date) = 2019
                
            ");

            $d->execute();

            $f = $d->fetchAll();



            $write = '';
			$write .= '-- ' . $user['fname'] . " " . $user['lname'];

            $x = 1;

            foreach($f as $key => $value){

                $write .= "
                    UPDATE  ".$portal_hourlyrate_day."  
                    SET status = 19, corr_status = 19 WHERE id = '$value[id]'
                ";
                if($x == $prebaciti){
                    break;
                }
                $x++;
            }

            $file = fopen("ljudi.sql", "a");
            fwrite($file, $write);
            fclose($file);

        } else {
            echo $user['fname']. " " . $user['lname'] . " je OK!";
        }

        $i++;
    }

    foreach($pronadjeni as $k){
        echo $k . ", ";
    }

?>
