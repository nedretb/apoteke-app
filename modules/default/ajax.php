<?php
require_once '../../configuration.php';
require_once '../../configuration.php';

if(DEBUG){

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

}
// phpstorm test commit
// error_reporting(0);


ini_set('display_errors',1);
error_reporting(E_ALL);



if(isset($_POST['request'])){
    if($_POST['request']=='datum-akontacije'){



        $id = $_POST['id'];
        $date = date("Y-m-d", strtotime($_POST['datum_pocetka']));
        $dan = 6;
        $tip = 'BHOLIDAY';
        while($tip == 'BHOLIDAY' or in_array($dan,[6,7])){
            $date = date("Y-m-d", strtotime($date.'- 1 day'));
            $dan_data = $db->query("
    SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
where Date = '$date' and employee_no = (
select employee_no from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where id = $id)
");

            $dan_data = $dan_data->fetch();
            $dan=$dan_data['weekday'];
            $tip=$dan_data['KindofDay'];
        }
        $datum_akontacije = date("d.m.Y", strtotime($dan_data['Date']));
        echo json_encode($datum_akontacije);
    }

    if($_POST['request']=='tasks-add'){



        $_user = _user(_decrypt($_SESSION['SESSION_USER']));



        $date_end = date("Y/m/d", strtotime(str_replace("/","-",$_POST['final_date'])));

        //locked testa s


        //individualni a
        $check_ind = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_type=0 and (status NOT IN (4,5) or status is null) and ponder<>0 and user_id=".$_user['user_id']." AND year = ".date("Y"));
        $ind_count1 = $check_ind->fetch();
        $ind_count = $ind_count1['broj'];

        if($ind_count>=7 and $_POST['task_type']==0){
            echo '<div class="alert alert-danger text-center">'.__('Maximalan broj individualnih ciljeva je 7').'</div><br/>';
            return;
        }

        //timski
        $check_team = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_type=1 and (status NOT IN (4,5) or status is null) and ponder<>0 and user_id=".$_user['user_id']." AND year = ".date("Y"));
        $team_count1 = $check_team->fetch();
        $team_count = $team_count1['broj'];

        if($team_count>=3 and $_POST['task_type']==1){
            echo '<div class="alert alert-danger text-center">'.__('Maximalan broj timskih ciljeva je 3').'</div><br/>';
            return;
        }

        //ponder checks

        if(isset($_POST['ponder'])){

            $check_ponder = $db->query("SELECT SUM(ponder) as ponder_sum FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE user_id=".$_user['user_id']." AND task_type in (0,1) and (status NOT IN (4,5) or status is null) AND year = ".date("Y"));
            $check_ponder1 = $check_ponder->fetch();
            $ponder_sum = $check_ponder1['ponder_sum'];

            if($ponder_sum==100){
                echo '<div class="alert alert-danger text-center">'.__('Suma pondera je 100, unos dodatnog cilja je nedozvoljen').'</div><br/>';
                return;
            }

            if($ponder_sum+$_POST['ponder']>100){
                echo '<div class="alert alert-danger text-center">'.__('Suma pondera prelazi 100, unos cilja je nedozvoljen').'</div><br/>';
                return;
            }
            $ponder = $_POST['ponder'];
            $kpi = $_POST['task_kpi'];
        }
        else{
            $ponder = NULL;
            $kpi = "";
        }


        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[tasks] (
    task_name,task_description,KPI,is_accepted,user_id,employee_no,year,parent_id,hr_id,admin_id,task_type,ponder,date_created,date_end,origin,phase) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['task_name'],
                $_POST['task_description'],
                $kpi,
                '0',
                $_user['user_id'],$_user['employee_no'],date("Y"),$_user['parent'],$_user['hr'],$_user['admin'],
                $_POST['task_type'],
                $ponder,
                date('Y-m-d', strtotime("now")),
                $date_end,
                'PORTAL',
                1


            )
        );
        if($res->rowCount()==1) {

            $id = $db->lastInsertId();

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      task_id_NAV = ?
    WHERE task_id = ?";
            $res = $db->prepare($data);
            $res->execute(
                array(
                    $id,
                    $id,

                )
            );

            if(strlen($_POST['comment'])>1){

                $data3 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[comments] (
            type,user_id,comment,date_created,comment_on) VALUES (?,?,?,?,?)";

                $res3 = $db->prepare($data3);
                $res3->execute(
                    array(
                        'task',
                        $_user['user_id'],
                        $_POST['comment'],
                        date('Y-m-d', strtotime("now")),
                        $id
                    )
                );
                if($res3->rowCount()==1) {
                    echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
                    //echo $ponder_sum+$_POST['ponder'];
                }

            }else{
                echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
                //echo $ponder_sum+$_POST['ponder'];
            }
            $data = _updateCiljevi($_user['user_id']);
            _updateLastChange($id);
        }

    }

    if($_POST['request']=='experience-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date_from = date("Y/m/d", strtotime(str_replace("/","-",$_POST['date_from'])));
        $date_to = date("Y/m/d", strtotime(str_replace("/","-",$_POST['date_to'])));

        if(isset($_POST['is_end_date'])){
            $is_end_date = 1;
            $ds   = explode('/', $_POST['final_date']);
            $date_end = $ds[2].'-'.$ds[1].'-'.$ds[0];

        }else{
            $is_end_date = null;
            $date_end = null;

        }

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[Experience] (
    user_id,position,OJ,date_from,date_to,poslodavac,napomena) VALUES (?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['user_id'],
                $_POST['position'],
                $_POST['OJ'],
                $date_from,
                $date_to,
                $_POST['poslodavac'],
                $_POST['napomena']

            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }

    if($_POST['request']=='project-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date_from = date("Y/m/d", strtotime(str_replace("/","-",$_POST['date_from'])));
        $date_to = date("Y/m/d", strtotime(str_replace("/","-",$_POST['date_to'])));

        if(isset($_POST['is_end_date'])){
            $is_end_date = 1;
            $ds   = explode('/', $_POST['final_date']);
            $date_end = $ds[2].'-'.$ds[1].'-'.$ds[0];

        }else{
            $is_end_date = null;
            $date_end = null;

        }

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[Projects] (
    user_id,project_name,area,date_from,date_to,poslodavac,uloga) VALUES (?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['user_id'],
                $_POST['project_name'],
                $_POST['area'],
                $date_from,
                $date_to,
                $_POST['poslodavac'],
                $_POST['uloga']

            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }

    if($_POST['request']=='language-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[Language_Skills] (
    user_id,language,understanding,speech,writing) VALUES (?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['user_id'],
                $_POST['language'],
                $_POST['understanding'],
                $_POST['speech'],
                $_POST['writing'],
            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }

    if($_POST['request']=='lang-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$_user['employee_no']."'");
        if($get->rowCount()<0)
            $maximalni = $get->fetchAll();

        $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Qualification] (
    [Employee No_]
    
      ,[From Date]
      ,[To Date]
      ,[Qualification Code]
      ,[Type]
      ,[Description]
      ,[Institution_Company]
      ,[Cost]
      ,[Course Grade]
      ,[Employee Status]
      ,[Expiration Date]
      ,[Active]
      ,[Switch]
      ,[Computer Knowledge Code]
      ,[Computer Knowledge Description]
      ,[Language Code]
      ,[Language Level]
      ,[Language Name]
      ,[Exam Passed]
      ,[Decision No_]) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['employee_no'],
                date("Y/m/d"),
                date("Y/m/d"),
                '',
                0,
                '',
                '',
                0,
                '',
                0,
                date("Y/m/d"),
                0,
                0,
                '',
                '',
                _optionGetLanguageCodeNAV($_POST['jezik']),
                $_POST['nivo_jezik'],
                $_POST['jezik'],
                0,
                ''
            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene i biti će vidljive nakon pritiska na dugme "Spasi"').'</div>';
        }

    }

    if($_POST['request']=='cert-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$_user['employee_no']."'");
        if($get->rowCount()<0)
            $maximalni = $get->fetchAll();



        $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Qualification] (
    [Employee No_]
     
      ,[From Date]
      ,[To Date]
      ,[Qualification Code]
      ,[Type]
      ,[Description]
      ,[Institution_Company]
      ,[Cost]
      ,[Course Grade]
      ,[Employee Status]
      ,[Expiration Date]
      ,[Active]
      ,[Switch]
      ,[Computer Knowledge Code]
      ,[Computer Knowledge Description]
      ,[Language Code]
      ,[Language Level]
      ,[Language Name]
      ,[Exam Passed]
      ,[Decision No_]) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['employee_no'],

                date("Y/m/d"),
                date("Y/m/d"),
                _optionGetQualificationCodeNAV($_POST['certifikat']),
                0,
                $_POST['certifikat'],
                $_POST['certifikat_kompanija'],
                0,
                '',
                0,
                date("Y/m/d"),
                0,
                0,
                '',
                '',
                '',
                0,
                '',
                0,
                ''
            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene i biti će vidljive nakon pritiska na dugme "Spasi"').'</div>';
        }

    }

    if($_POST['request']=='remove-kvalifikacija_remove'){

        $this_id = $_POST['request_id'];
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $data = "DELETE FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Qualification] WHERE [Line No_] = ".$this_id." AND [Employee No_] ='".$_user['employee_no']."'";
        $delete = $db->prepare($data);
        $delete->execute(array());
        if($delete){
            //
        }
    }

    if($_POST['request']=='certifikat-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $zavrsetak = date("Y/m/d", strtotime(str_replace("/","-",$_POST['zavrsetak'])));

        if(isset($_POST['is_end_date'])){
            $is_end_date = 1;
            $ds   = explode('/', $_POST['final_date']);
            $date_end = $ds[2].'-'.$ds[1].'-'.$ds[0];

        }else{
            $is_end_date = null;
            $date_end = null;

        }

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[Certifikati] (
    user_id,certifikat,institucija,vrsta,zavrsetak) VALUES (?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_user['user_id'],
                $_POST['certifikat'],
                $_POST['institucija'],
                $_POST['vrsta'],
                $zavrsetak
            )
        );

        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }

    if($_POST['request']=='remove-experience_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[Experience] WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }

    if($_POST['request']=='remove-project_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[Projects] WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }

    if($_POST['request']=='remove-language_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[Language_Skills] WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }

    if($_POST['request']=='remove-certifikat_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[Certifikati] WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }

    //TASKS

    if($_POST['request']=='change-ocjena_timski_user'){

        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      user_rating = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {

            echo 1;
        }
        else{  echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';
        }


        // echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';


    }

    if($_POST['request']=='change-ocjena_timski'){


        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];

        $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_id = ".$task_id);
        if($query->rowCount()<0){
            foreach($query as $item){
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      rating = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {

            $data = _updateCiljevi($user_id);
            _updateLastChange($task_id);

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-ocjena_individualni_user'){

        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      user_rating = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-ocjena_individualni'){

        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];

        $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_id = ".$task_id);
        if($query->rowCount()<0){
            foreach($query as $item){
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      rating = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {
            $data = _updateCiljevi($user_id);
            _updateLastChange($task_id);
            echo 1;
        }
        else{  echo 'otkljucan';
        }


    }

    if($_POST['request']=='change-task-status'){

        $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_id = ".$_POST['task_id']);
        if($query->rowCount()<0){
            foreach($query as $item){
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      status = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                $_POST['task_id'],

            )
        );
        if($res->rowCount()==1) {
            $data = _updateCiljevi($user_id);
            _updateLastChange($_POST['task_id']);
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-task-ostvarenje'){

        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      ostvarenje = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ostvarenje'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {
            _updateLastChange($task_id);
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-task-KPI'){

        $pieces = explode("-", $_POST['task_id']);
        $task_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
      KPI = ?
    WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['KPI'],
                $task_id,

            )
        );
        if($res->rowCount()==1) {
            _updateLastChange($task_id);
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    //misc user fields

    if($_POST['request']=='change-ambicije'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      ambicije = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ambicije'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-mobilnost'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      mobilnost = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['mobilnost'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-lokacija'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      lokacija = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['lokacija'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-vjestina'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      vjestina = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['vjestina'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-nivo'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      nivo = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['nivo'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    //misc parent fields

    if($_POST['request']=='change-rizik_gubitka'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      rizik_gubitka = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['rizik_gubitka'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-uticaj_gubitka'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      uticaj_gubitka = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['uticaj_gubitka'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-razlog_odlaska'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      razlog_odlaska = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['razlog_odlaska'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-karijera'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      karijera = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['karijera'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-novi_zaposlenik'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      novi_zaposlenik = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['novi_zaposlenik'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-pozicija'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      pozicija = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['pozicija'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-spremnost'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      spremnost = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['spremnost'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-prezime_ime'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      prezime_ime = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['prezime_ime'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-datum'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];

        $newDate = date("Y/m/d", strtotime(str_replace("/","-",$_POST['datum'])));


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      datum = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $newDate,
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    //KOMPETENCIJE

    if($_POST['request']=='change-obavezna1_user'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      obavezna1_rating_user = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['obavezna1_rating_user'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obavezna2_user'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      obavezna2_rating_user = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['obavezna2_rating_user'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obavezna1'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      obavezna1_rating = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['obavezna1_rating'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = _updateKompetencije($user_id);
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obavezna2'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      obavezna2_rating = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['obavezna2_rating'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = _updateKompetencije($user_id);
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija1'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija1 = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija1'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija1_rating_user'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija1_rating_user = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija1_rating_user'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija1_rating'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija1_rating = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija1_rating'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = _updateKompetencije($user_id);
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija2'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija2 = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija2'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija2_rating_user'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija2_rating_user = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija2_rating_user'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija2_rating'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija2_rating = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija2_rating'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = _updateKompetencije($user_id);
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija3'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija3 = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija3'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija3_rating_user'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija3_rating_user = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija3_rating_user'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-kompetencija3_rating'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      kompetencija3_rating = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['kompetencija3_rating'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = _updateKompetencije($user_id);
        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-komentar'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      komentar = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['komentar'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-l_potencijal'){


        $user_id=  $_POST['user_id'];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Misc] SET
      l_potencijal = ?
    WHERE user_id = ?
    AND year = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['l_potencijal'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {



        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='remove-tasks_remove'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            $data1 = "DELETE FROM [c0_intranet2_apoteke].[dbo].[comments] WHERE comment_on = ? AND type = ?";
            $delete1 = $db->prepare($data1);
            $delete1->execute(array($this_id, 'task'));

            $data = _updateCiljevi($_user['user_id']);
        }

    }

    //SLANJE PORUKA

    if($_POST['request']=='send-nadredjenom'){
        $this_id = $_POST['request_id'];
        $params   = explode('-', $this_id);
        $user_id = $params[0];
        $faza = $params[1];

        // ukupni checks

        $check_ukupni = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_type in (0,1) and (status NOT IN (4,5) or status is null) and ponder<>0 and year = ".date("Y")." and user_id=".$user_id);
        $ukupni_count1 = $check_ukupni->fetch();
        $ukupni_count = $ukupni_count1['broj'];

        if($ukupni_count<5){
            echo "ukupni_ispod";
            return;
        }

        // individualni checks

        $check_ind = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_type=0 and (status NOT IN (4,5) or status is null) and ponder<>0 and year = ".date("Y")." and user_id=".$user_id);
        $ind_count1 = $check_ind->fetch();
        $ind_count = $ind_count1['broj'];

        if($ind_count<4){
            echo "ind_ispod";
            return;
        }

        //timski checks

        $check_team = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE task_type=1 and (status NOT IN (4,5) or status is null) and ponder<>0 and year = ".date("Y")." and user_id=".$user_id);
        $team_count1 = $check_team->fetch();
        $team_count = $team_count1['broj'];
        if($team_count<1){
            echo "tim_ispod";
            return;
        }

        //ponder checks

        $check_ponder = $db->query("SELECT SUM(ponder) as ponder_sum FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE user_id=".$user_id." AND task_type in (0,1) and year = ".date("Y")." and (status NOT IN (4,5) or status is null)");
        $check_ponder1 = $check_ponder->fetch();
        $ponder_sum = $check_ponder1['ponder_sum'];

        if($ponder_sum!=100){
            echo "ponder_ispod";
            return;
        }

        //status check (faza 2)
        if($faza==2){
            $check_status = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE user_id=".$user_id." AND ((task_type in (0,1) and ponder<>0) or (task_type=2 and ponder is null)) and year = ".date("Y")." and (status = '' or status is null or status=0)");
            $check_status1 = $check_status->fetch();
            $status = $check_status1['broj'];

            if($status>0){
                echo "status_ispod";
                return;
            }
        }

        //ocjena check (faza 3)
        if($faza==3){
            $check_status = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[tasks] WHERE user_id=".$user_id." AND task_type in (0,1) and (status NOT IN (4,5) or status is null) and ponder<>0 and year = ".date("Y")." and (user_rating = '' or user_rating is null or user_rating=0)");
            $check_status1 = $check_status->fetch();
            $status = $check_status1['broj'];

            $check_status_kompetencije = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Misc] WHERE user_id=".$user_id." AND 
  year = ".date("Y")." and (
  (kompetencija1_rating_user = '' or kompetencija1_rating_user is null or kompetencija1_rating_user=0) or (kompetencija2_rating_user = '' or kompetencija2_rating_user is null or kompetencija2_rating_user=0) or (kompetencija3_rating_user = '' or kompetencija3_rating_user is null or kompetencija3_rating_user=0) or (obavezna1_rating_user = '' or obavezna1_rating_user is null or obavezna1_rating_user=0) or (obavezna2_rating_user = '' or obavezna2_rating_user is null or obavezna2_rating_user=0))");
            $check_status1_kompetencije = $check_status_kompetencije->fetch();
            $status_kompetnecije = $check_status1_kompetencije['broj'];

            if($status>0 or $status_kompetnecije>0){
                echo "ocjena_ispod";
                return;
            }
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[objective_status] SET
        step1 = ?,
    status = 'poslao_radnik;'
        WHERE user_id = ?
    AND phase = ?
    AND year = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $user_id,
                $faza,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
        is_accepted = ?
        WHERE user_id = ?
    AND year = ?
    AND is_accepted = 0";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    '1',
                    $user_id,
                    date("Y")
                )
            );
            echo 1;
        }
    }

    if($_POST['request']=='send-potpisuje_radnik'){
        $this_id = $_POST['request_id'];
        $params   = explode('-', $this_id);
        $user_id = $params[0];
        $faza = $params[1];

        $data = "
    
    declare @status nvarchar(MAX)
set @status = (select status from [c0_intranet2_apoteke].[dbo].[objective_status] where user_id=".$user_id." and phase =".$faza." and year = ".date("Y").")
if((@status like '%potpisao_nadredjeni;%') or (@status like '%poslano_na_potpisivanje;%'))
set @status = @status + 'potpisao_radnik;'
else
set @status = 'potpisao_radnik;'
    
    
    UPDATE [c0_intranet2_apoteke].[dbo].[objective_status] SET
        step4 = ?,
    status = @status,
    datum_radnik = ?
        WHERE user_id = ?
    AND phase = ?
    AND year = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d', strtotime("now")),
                $user_id,
                $faza,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }
    }

    if($_POST['request']=='potvrda_razgovora'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[objective_status] SET
      potvrda4 = ?
    WHERE user_id = ?
    AND year = ?
    AND phase = 3";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['potvrda_razgovora'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='potvrda_razgovora_nadredjeni'){

        $pieces = explode("-", $_POST['user_id']);
        $user_id= $pieces[1];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[objective_status] SET
      potvrda5 = ?
    WHERE user_id = ?
    AND year = ?
    AND phase = 3";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['potvrda_razgovora_nadredjeni'],
                $user_id,
                date("Y")
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }


    if($_POST['request']=='request-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds   = explode('/', $_POST['from']);
        $de   = explode('/', $_POST['to']);

        $from = $ds[2].'-'.$ds[1].'-'.$ds[0];
        $to   = $de[2].'-'.$de[1].'-'.$de[0];

        /* functiotran date_normalizer ($d) {
    if ($d instanceof DateTime) {
      echo $d->GetTimestamp();
    }
    else
    {
      echo strtotime($d);
    }
  }strtotime($d);*/

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[requests] (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive) VALUES (?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'],$_user['parent'], date('Y-m-d', strtotime("now")),date('Y-m-d', strtotime($from)),date('Y-m-d', strtotime($to)),'0','GO','0'));
        if($res->rowCount()==1)

        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }
    }

    if($_POST['request']=='travel-request-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds   = explode('/', $_POST['from']);
        $de   = explode('/', $_POST['to']);

        $from = $ds[2].'-'.$ds[1].'-'.$ds[0];
        $to   = $de[2].'-'.$de[1].'-'.$de[0];

        /* function date_normalizer ($d) {
    if ($d instanceof DateTime) {
      echo $d->GetTimestamp();
    }
    else
    {
      echo strtotime($d);
    }
  }strtotime($d);*/

        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[travel_requests] (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive,country,travel_route,comment,total_cost) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'],$_user['parent'], date('Y-m-d', strtotime("now")),
                date('Y-m-d', strtotime($from)),
                date('Y-m-d', strtotime($to)),
                '0',
                'SLUŽBENI PUT',
                '0',
                $_POST['country'],$_POST['travel_route'],$_POST['comment'],$_POST['total_cost']));
        if($res->rowCount()==1)
        {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }


    if($_POST['request']=='year-add'){

        $check = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$_POST['year']."'" )->rowCount();
        //echo $check;
        if($check < 0){

            echo '<div class="alert alert-danger text-center">'.__('Godinu koju ste odabrali već postoji!').'</div><br/>';

        }else{

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM [c0_intranet2_apoteke].[dbo].[users]");
            $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[users]");
            $query2 = $db->query("SELECT [year] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year]");

            $total = $get2->rowCount();


            foreach($query as $item) {

                //echo $absence_id;
                $absence_year_id = $item['user_id'];


                $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[hourlyrate_year](
     user_id,year) VALUES (?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $absence_year_id,
                        $_POST['year']
                    )
                );}
            if($res->rowCount()==1)


            {
                echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';

            }


        }
    }

    if($_POST['request']=='year-add-complete'){

//izmjena
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $now = new DateTime();
        $filteryear = $now->format('Y');
        $filtermonth = $now->format('M');
        $filtertdate=$filteryear."-".$filtermonth."-1 00:00:00.000";
//////
        $check_year_exists_calendar = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[Calendar] WHERE [Year]='".$_POST['year']."'" )->rowCount();

        if(!($check_year_exists_calendar < 0)){

            //exec store procedura za kreiranje Kalendara

            $allq = $db->query("

DECLARE @StartYear  nvarchar(10) = convert(nvarchar(10),'".$_POST['year']."');
DECLARE @Date  nvarchar(20) = @StartYear + '0101';
DECLARE @StartDate date = convert(date,@Date);
DECLARE @CutoffDate date = DATEADD(DAY, -1, DATEADD(YEAR, 1, @StartDate));

;WITH seq(n) AS

(

  SELECT 0 UNION ALL SELECT n + 1 FROM seq
  WHERE n < DATEDIFF(DAY, @StartDate, @CutoffDate)

),

d(d) AS

(

  SELECT DATEADD(DAY, n, @StartDate) FROM seq

),

src AS

(

  SELECT

               Date         = CONVERT(datetime, d),

               Year         = DATEPART(YEAR,      d),

               Quarter      = DATEPART(Quarter,   d),

               Month        = DATEPART(MONTH,     d),

               Week         = DATEPART(WEEK,      d),

               Day          = DATEPART(DAY,       d),

               DayOfYear    = DATEPART(DAYOFYEAR, d),

               Weekday    = DATEPART(WEEKDAY,   d),

               Fiscal_Year         = DATEPART(YEAR,      d),

               Fiscal_Quarter      = DATEPART(Quarter,   d),

               Fiscal_Month        = DATEPART(MONTH,     d),

               KindOfDay = (case when DATEPART(WEEKDAY,   d) = 6 then 'SATURDAY'

               when DATEPART(WEEKDAY,   d) = 7 then 'SUNDAY' else 'BANKDAY' END),

               Description = NULL,

               Hr_status = NULL
  FROM d

)

INSERT INTO [c0_intranet2_apoteke].[dbo].[Calendar]

SELECT * FROM src

  ORDER BY Date

  OPTION (MAXRECURSION 0);");


        }

        $check = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$_POST['year']."'" )->rowCount();

        if($check < 0){

            echo '<div class="alert alert-danger text-center">'.__('Godinu koju ste odabrali već postoji!').'</div><br/>';

        }else{

            //izmjena/////////////////////////////

            $query = $db->query("SELECT top 1000  [user_id] FROM [c0_intranet2_apoteke].[dbo].[users] where 
 ((termination_date is null) or (termination_date ='')) and user_id<>6980 order by user_id");

//////////////////////////

            $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[hourlyrate_year](
     user_id,year) VALUES ";

            foreach($query as $item) {

                $absence_year_id = $item['user_id'];

                $values="(".$absence_year_id.",".$_POST['year']."), ";
                $data .= $values;

            }

            $data = substr($data, 0, -2);
            $res = $db->prepare($data);

            {
                $res->execute(
                    array(
                    )
                );}
//kreiranje godine od 1000-2000 wtf!?!??!?!?!? genijalno
//            $query = $db->query("SELECT [user_id] FROM [c0_intranet2_apoteke].[dbo].[users]
//   where
// ((termination_date is null) or (termination_date ='')) order by user_id offset 1000 rows");
//
//            $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[hourlyrate_year](
//     user_id,year) VALUES ";
//
//            var_dump($query);
//            foreach($query as $item) {
//
//                $absence_year_id = $item['user_id'];
//
//                $values="(".$absence_year_id.",".$_POST['year']."), ";
//                $data .= $values;
//
//            }
//            $data = substr($data, 0, -2);
//            $res = $db->prepare($data);
//
//            {
//                $res->execute(
//                    array(
//                    )
//                );}


            $poruka =  _addAllMonths($_POST['year']);

            echo $poruka;
        }
    }

    if($_POST['request']=='year-add-complete1'){
        $poruka =   _addAllMonths7($_POST['year']);
        echo $poruka;
    }




    if($_POST['request']=='month-add'){
        session_write_close();

        $check = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month] WHERE month='".$_POST['month']."' AND year_id='".$_POST['year']."'")->rowCount();
        //echo $check;
        if($check < 0){

            echo '<div class="alert alert-danger text-center">'.__('Mjesec koji ste odabrali već postoji!').'</div><br/>';

        }else{


            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $now = new DateTime();
            $filteryear = $now->format('Y');
            $filtermonth = $now->format('M');
            $filtertdate=$filteryear."-".$filtermonth."-1 00:00:00.000";
            $query_month = $db->query("SELECT [user_id] FROM [c0_intranet2_apoteke].[dbo].[users] where ((termination_date>='".$filtertdate."') or
      (termination_date is null))");
            $get_month = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[users] where ((termination_date>='".$filtertdate."') or
      (termination_date is null))  ");
            $yearcurr = $db->query("SELECT [year] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$_POST['year']."'");
            $total = $get_month->rowCount();
            foreach($yearcurr as $value2) {
                $absence_year = $value2['year'];}

            foreach($query_month as $item) {

                $month = $db->query("SELECT [id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] where [user_id]='".$item['user_id']."' and year='".$absence_year."'");

                $absence_id_month = $item['user_id'];
                foreach($month as $value) {
                    $absence_month = $value['id'];}

                $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                $emp_no = $db->query("SELECT employee_no,department_code,employment_date FROM [c0_intranet2_apoteke].[dbo].[users] where [user_id]='".$item['user_id']."'
  and ((termination_date>='".$filtertdate."') or (termination_date is null))  ");
                foreach($emp_no as $value3) {
                    $employee_no = $value3['employee_no'];
                    $edate =  DateTime::createFromFormat("Y-m-d", $value3['employment_date']);

                    /* $department= $value3['department_code'];
  $query_department= $db->query("SELECT [B_1_regions],[B_1_regions_description] FROM [c0_intranet2_apoteke].[dbo].[departments] where [code]='".$department."'
   ");  */
                    /*  foreach($query_department as $valuedep) {
     $b1=$valuedep['B_1_regions'];
  $b1desc=$valuedep['B_1_regions_description'];} */
                    $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[hourlyrate_month](id,
      user_id,year_id,month, verified, verified_corrections) VALUES (?,?,?,?,?,?)";

                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $_POST['month'],
                            $absence_id_month,
                            $absence_month,
                            $_POST['month'],
                            0,
                            0
                        )
                    );



                    $query_calendar= $db->query("SELECT [day],[weekday],[KindOfDay],[Description],[Hr_status] FROM [c0_intranet2_apoteke].[dbo].[Calendar] where [month]='".$_POST['month']."'
   and  [year]='".$absence_year."'");

                    foreach($query_calendar as $cal) {
                        $day=$cal ['day'];
                        $weekday=$cal ['weekday'];
                        $kind=$cal ['KindOfDay'];
                        $desc=$cal ['Description'];
                        $hrstat=$cal['Hr_status'];
                        if($kind=='BANKDAY') {$status='5';}
                        if($kind=='CHOLIDAY') {$status='5';}
                        if($kind=='HOLIDAY') {
                            $query_status= $db->query("SELECT [id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] where [name]='".$hrstat."'
   ");

                            foreach($query_status as $calstat) { $status=$calstat['id'];}
                        }
                        if (($kind!='BANKDAY') and ($kind!='CHOLIDAY') and ($kind!='HOLIDAY')) {$status='5';}if (($kind!='BANKDAY') and ($kind!='CHOLIDAY') and ($kind!='HOLIDAY')) {$status='5';}
                        $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[hourlyrate_day] (
      user_id,year_id,month_id,day,hour,status,weekday,KindOfDay,Description,employee_no,B_1_regions,B_1_regions_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ";

                        $res = $db->prepare($data);

                        {
                            $res->execute(
                                array(
                                    $absence_id_month,
                                    $absence_month,
                                    $_POST['month'],
                                    $day,
                                    '8',
                                    $status,
                                    $weekday,
                                    $kind,
                                    $desc,
                                    $employee_no,
                                    '',
                                    ''
                                )
                            );}

                    }
                }
            }
            if($res->rowCount()==1) {
                echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
            }

        }

    }

    if($_POST['request']=='check-month-add'){

        $query = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month]  where month = ".$_POST['month']);

        foreach ($query as $item) {
            $num_users = $item['broj'];
        }
        echo $num_users;
    }

    if($_POST['request']=='check-month-add-new'){

        $query = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month]");

        foreach ($query as $item) {
            $num_users = (18/2);
        }
        echo (string)$num_users;

    }

    if($_POST['request']=='parent-day-add'){

        $FromDay=$_POST['FromDay'];
        $ToDay=$_POST['ToDay'];
        $getMonth=$_POST['get_month'];
        $getYear=$_POST['get_year'];


        $request_id_generate = $FromDay."".$ToDay."".$getMonth."".$getYear;


        $query = $db->query("SELECT [day] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where  month_id='$getMonth' AND year_id='$getYear'");



        foreach ($query as $item) {


            if($item['day'] >= $FromDay && $item['day'] <= $ToDay ){

                $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      hour = ?,
      status = ?,
    request_id = '$request_id_generate' 
      where day=?
      and month_id=?
     and year_id=?";


                $res = $db->prepare($data);
                $res->execute(
                    array(

                        $_POST['hour'],
                        $_POST['status'],
                        $item['day'],
                        $getMonth,
                        $getYear
                    )
                );

            }}

        echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';

    }

    //GRUPNO AZURIRANJE
    if($_POST['request']=='parent-day-add_apsolute' or ($_POST['request']=='day-edit' and $_POST['status']==67)){
        global $url;
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $br_sati = $_user['br_sati'];
        $status = $_POST['status'];

        if($_POST['request']=='day-edit' and $_POST['status']==67){
            //echo 'hvata ovaj';
            $this_id = $_POST['request_id'];
            $status = $_POST['status'];

            $check = $db->query("SELECT user_id, year_id,month_id,employee_no, [Date] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id."' ");
            foreach ($check  as $checkvalue) {
                $getYear = $checkvalue['year_id'];
                $getMonth=$checkvalue['month_id'];
                $filter_emp=$checkvalue['employee_no'];
                $FromDay=$checkvalue['Date'];
                $ToDay=$checkvalue['Date'];
            }
        }
        else{
            $FromDay=$_POST['dateFrom'];
            $ToDay=$_POST['dateTo'];
            $getMonth=$_POST['get_month'];
            $getYear=$_POST['get_year'];
        }


        $dateFrom = strtotime(str_replace("/","-",$FromDay));
        $dateTo = strtotime(str_replace("/","-",$ToDay));

        $dateFromDB = date('Y/m/d',strtotime(str_replace("/","-",$FromDay)));
        $dateToDB = date('Y/m/d',strtotime(str_replace("/","-",$ToDay)));

        $datediff = $dateTo - $dateFrom;
        $day_difference = floor($datediff / (60 * 60 * 24)) + 1;

        $month_from = date("n", strtotime(str_replace("/","-",$FromDay)));
        $month_to = date("n", strtotime(str_replace("/","-",$ToDay)));

        $day_from = date("j", strtotime(str_replace("/","-",$FromDay)));
        $day_to = date("j", strtotime(str_replace("/","-",$ToDay)));

        $request_id_generate = $day_from."".$day_to."".$month_from."".$month_to."".$getYear;

        $emp=$db->query("SELECT employee_no, YEAR(Date) as godina FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."'  
  ");
        foreach($emp as $valueemp)
        {$empid=$valueemp['employee_no']; $godina = $valueemp['godina']; }
        $go=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");

        $get_count_holiday=$db->query("SELECT count(KindOfDay) as countHol FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (KindOfDay='BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countHoliday = $get_count_holiday->fetch();
        $countHol = $countHoliday['countHol'];

        $get_count_odobreno=$db->query("SELECT count(*) as countOdobreno FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (review_status='1') and (KindofDay<>'BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countOdo = $get_count_odobreno->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        $get_count_not_vikend=$db->query("SELECT count(*) as not_vikend FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE weekday not in (6,7) and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_vikend = $get_count_not_vikend->fetch();
        $not_vikend = $count_not_vikend['not_vikend'];

        if($not_vikend==0 and !in_array($_POST['status'], array(43, 44, 45, 61, 62 ,65 ,67, 68, 69, 73, 74,75,76,77, 78, 81, 105, 107, 108))){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete registrovati vikend.').'</div>';
            return;
        }

        //uputi admina na olovku

        $check_weekends_start_date = $db->query("SELECT weekday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE day = '$day_from' and month_id = '$month_from' and year_id = '$getYear'");
        $check_weekends_start_date_fetch = $check_weekends_start_date->fetch();

        $check_weekends_end_date = $db->query("SELECT weekday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE day = '$day_to' and month_id = '$month_to' and year_id = '$getYear'");
        $check_weekends_end_date_fetch = $check_weekends_end_date->fetch();


        $weekends_start = $check_weekends_start_date_fetch['weekday'];
        $weekends_end   = $check_weekends_end_date_fetch['weekday'];



        if(in_array($_POST['status'], array(5)) and (($weekends_start >= 6 and $weekends_start <= 7) and ($weekends_end  >= 6 and $weekends_end<= 7))){
            echo '<div class="alert alert-success text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete registrovati redovan rad.').'</div>';
            return;
        }

        $get_days = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and month_id<=12
   and year_id=".$getYear);

        $get_days1 = $get_days->fetchAll();

        $day_before = $day_from-1;

        $get_days2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     
   (
   (day >= ".$day_before." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_before." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);

        $get_days2 = $get_days2->fetchAll();


        $nex_year = getYearId($getYear, $_user['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $_user['user_id'], 'prev', true);

        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (status='18')) or (year_id = '".$nex_year."' and status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV is null)");

        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     weekday<>'6' AND weekday<>'7' and KindOfDay<>'BHOLIDAY' and
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);

        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (status='19')) or (year_id = '".$pre_year."' and status = '18')) AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (date_NAV is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");

        $death = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
        $pcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $plo=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");

        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");
        $P_1=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_2=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_3=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_4=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_5=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_6=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_7=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");

        $P_1a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='27'");
        $P_2a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='28'");
        $P_3a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='29'");
        $P_4a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='30'");
        $P_5a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='31'");
        $P_6a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='32'");
        $P_7a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='79'");
        foreach($go as $valuego) {
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristeno=$valuego['Br_dana_iskoristeno'];
            $ostalo = $valuego['Br_dana_ostalo'];
            $brdana=$valuego['Br_dana'];
            $totalkrv=$valuego['Blood_days'];
            $totaldeath=$valuego['S_1_used'];
            $iskoristenokrv=$valuego['P_6_used'];
            $propaloGO=$valuego['G_2 not valid'];
        }

        foreach($askedgo as $valueasked) {
            $askeddays=$valueasked['sum_hour'];
            $totalasked=$askeddays/$br_sati;}

        foreach($currgo as $valuecurrgo) {
            $iskoristenocurr=$valuecurrgo['sum_hour'];;
            $iskoristenototal=($iskoristenocurr/$br_sati)+$iskoristeno ;
            $totalgoost=$brdana-$iskoristenototal;}
        foreach($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG=$valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG=($iskoristenocurrPG/$br_sati)+$iskoristenoPG ;
            $totalgoostPG=$brdanaPG-$iskoristenototalPG;
            $ukupnogoiskoristeno=$iskoristenototalPG+$iskoristenototal;
            $ukupnogoost=$totalgoost+$totalgoostPG;
        }




        foreach($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm=$valuepcm['Candelmas_paid'];}
        foreach($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm=$valueupcm['Candelmas_unpaid'];}
        foreach($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }
        foreach($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm=$valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm=($iskoristenocurrpcm/$br_sati)+$iskoristenopcm ;
            $totalpcmost=$brdanapcm-$iskoristenototalpcm;
        }
        foreach($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm=$valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm=($iskoristenocurrupcm/$br_sati)+$iskoristenoupcm ;
            $totalupcmost=$brdanaupcm-$iskoristenototalupcm;
        }

        foreach($curruP_1  as $valuecurrP_1) {
            $iskoristenocurrP_1=$valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1=($iskoristenocurrP_1/$br_sati)+$iskoristenoP_1 ;
            $totalP_1ost=$totalP_1-$iskoristenototalP_1;
        }
        foreach($curruP_2  as $valuecurrP_2) {
            $iskoristenocurrP_2=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2=($iskoristenocurrP_2/$br_sati)+$iskoristenoP_2 ;
            $totalP_2ost=$totalP_2-$iskoristenototalP_2;
        }
        foreach($curruP_3  as $valuecurrP_3) {
            $iskoristenocurrP_3=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3=($iskoristenocurrP_3/$br_sati)+$iskoristenoP_3 ;
            $totalP_3ost=$totalP_3-$iskoristenototalP_3;
        }
        foreach($curruP_4  as $valuecurrP_4) {
            $iskoristenocurrP_4=$valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4=($iskoristenocurrP_4/$br_sati)+$iskoristenoP_4 ;
            $totalP_4ost=$totalP_4-$iskoristenototalP_4;
        }
        foreach($curruP_5  as $valuecurrP_5) {
            $iskoristenocurrP_5=$valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5=($iskoristenocurrP_5/$br_sati)+$iskoristenoP_5 ;
            $totalP_5ost=$totalP_5-$iskoristenototalP_5;
        }
        foreach($curruP_6  as $valuecurrP_6) {
            $iskoristenocurrP_6=$valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6=($iskoristenocurrP_6/$br_sati)+$iskoristenoP_6 ;
            $totalP_6ost=$totalP_6-$iskoristenototalP_6;
        }
        foreach($curruP_7  as $valuecurrP_7) {
            $iskoristenocurrP_7=$valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7=($iskoristenocurrP_7/$br_sati)+$iskoristenoP_7 ;
            $totalP_7ost=$totalP_7-$iskoristenototalP_7;
        }
        foreach($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used']+$valueplo['P_2_used']+$valueplo['P_3_used']+$valueplo['P_4_used']+$valueplo['P_5_used']+$valueplo['P_6_used']+$valueplo['P_7_used'];
            $totalplo =$valueplo['Br_dana_PLO'];
        }

        foreach($currplo as $valuecurrplo) {
            $iskoristenocurrplo=$valuecurrplo['sum_hour'];
            $iskoristenototalplo=($iskoristenocurrplo/8)+$iskoristenoplo ;
            $totalploost=$totalplo-$iskoristenototalplo;
        }

        //VJERSKI PRAZNICI
        /*
    if($_POST['status']=='84'){

  $statusi = array('0','0','0','0');

    if(($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=2){
      for ($x = 1; $x <= $day_difference; $x++) {
    $statusi[$x-1]='21';
}
      }
    elseif($iskoristenototalpcm>=2 and (($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=4)){
           for ($x = 1; $x <= $day_difference; $x++) {
    $statusi[$x-1]='22';
}
    }
      elseif($iskoristenototalpcm+$iskoristenototalupcm+$totalasked<=4){
           $count_placeni = 0;
           for ($x = 1; $x <= $day_difference; $x++) {
    if($get_days1[$x-1]['weekday']!='6' and $get_days1[$x-1]['weekday']!='7' and $get_days1[$x-1]['KindofDay']!='BHOLIDAY'){
    if($iskoristenototalpcm+$count_placeni<2){
  $statusi[$x-1]='21';
  $count_placeni++;
  }
  else{
    $statusi[$x-1]='22';
  }
  }
  else{
    if($get_days1[$x-1]['KindofDay']=='BHOLIDAY')
    $statusi[$x-1]=$get_days1[$x-1]['status'];
  else
    $statusi[$x-1]='21';

    }
}
    }
    else{
    echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika!').'</div>';
    return;
    }
  }*/

        if($_POST['status']=='84'){

            $statusi = array('0','0','0','0');

            if(($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=2){

                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x-1]='21';
                }
            }
            elseif($iskoristenototalpcm>=2 and (($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=4)){

                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x-1]='22';
                }
            }
            elseif($iskoristenototalpcm+$iskoristenototalupcm+$totalasked<=4){

                $count_placeni = 0;
                for ($x = 1; $x <= $day_difference; $x++) {
                    if($get_days1[$x-1]['weekday']!='6' and $get_days1[$x-1]['weekday']!='7' and $get_days1[$x-1]['KindofDay']!='BHOLIDAY'){
                        if($iskoristenototalpcm+$count_placeni<2){
                            $statusi[$x-1]='21';
                            $count_placeni++;
                        }
                        else{
                            $statusi[$x-1]='22';
                        }
                    }
                    else{
                        if($get_days1[$x-1]['KindofDay']=='BHOLIDAY')
                            $statusi[$x-1]=$get_days1[$x-1]['status'];
                        else{

                            if($iskoristenototalpcm < 2){
                                $statusi[$x-1]='22';
                            } else if($iskoristenototalupcm < 2){
                                $statusi[$x-1]='21';
                            } else {
                                $status[$x-1] = '5';
                            }
                        }


                    }
                }
            }
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika!').'</div>';
                return;
            }
        }


        //BOLOVANJE
        if($_POST['status']=='67'){

            $emp_bol=$db->query("SELECT top 1 user_id, employee_no FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id=".$getYear);
            foreach($emp_bol as $valueemp)
            {$emp_no=$valueemp['employee_no'];$emp_id=$valueemp['user_id'];}

            $nulifikacija = false;
            $back_popunjavanje = 0;
            $statusi = array();
            $statusi_popunjavanje = array();

            $previous_day   =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 1 days'));
            $previous_day_1 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 2 days'));
            $previous_day_2 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 3 days'));
            $previous_day_3 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 4 days'));
            $previous_day_4 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 5 days'));

            $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee] WHERE [No_]='".$emp_no."'");
            if($get->rowCount()<0){
                $row_employee = $get->fetch();
                $entitet = $row_employee['Org Entity Code'];}

            if($entitet=='FBIH' or $entitet=='BD'){
                $max_do = 42;
                $status_do = 43;
                $status_od = 44;
            }
            elseif($entitet=='RS'){
                $max_do = 30;
                $status_do = 107;
                $status_od = 108;
            }

            $get_date_bolovanje = $db->query("SELECT pocetak_bolovanja as pocetak_bolovanja FROM [c0_intranet2_apoteke].[dbo].[bolovanje] where 
    user_id = ".$emp_id);
            $pocetak_bolovanja = $get_date_bolovanje->fetch();

            if($pocetak_bolovanja['pocetak_bolovanja']==''){
                $countBolovanje_total['bolovanje43'] = 0;
                $countBolovanje_do['bolovanje43'] = 0;
            }
            else{
                $get_prekid = $db->query("SELECT count(*) as prekid FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (status <>".$status_do.")
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countPrekid = $get_prekid->fetch();

                $get_bolovanje_total = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (status =".$status_do." or (weekday in ('6','7')) or KindOfDay = 'BHOLIDAY')
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countBolovanje_total = $get_bolovanje_total->fetch();

                $get_bolovanje_do = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (status =".$status_do.")
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countBolovanje_do = $get_bolovanje_do->fetch();
            }

            $get_previous_days = $db->query("SELECT status, KindOfDay, weekday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
  WHERE 
   ([Date] in ('".$previous_day."','".$previous_day_1."','".$previous_day_2."','".$previous_day_3."','".$previous_day_4."')) and year_id=".$getYear." order by [Date] DESC");

            $previous_days_get = $get_previous_days->fetchAll();

            for ($x = 0; $x < count($previous_days_get); $x++){
                if(@$previous_days_get[$x]['status']==$status_od or @$previous_days_get[$x]['status']==$status_do){
                    $back_popunjavanje = $x;
                    break;
                }
            }

            $dan_ranije_bolovanje = (@$previous_days_get[0]['status']== $status_od or @$previous_days_get[0]['status']== $status_do);
            $dan_ranije_subota = ((@$previous_days_get[0]['weekday']=='6') and $back_popunjavanje );
            $dan_ranije_nedelja = ((@$previous_days_get[0]['weekday']=='7') and $back_popunjavanje );
            $dan_ranije_praznik = ((@$previous_days_get[0]['KindOfDay']=='BHOLIDAY') and $back_popunjavanje);

            if(
            ($dan_ranije_bolovanje or $dan_ranije_subota or $dan_ranije_nedelja or $dan_ranije_praznik)
            )
            {
                if($pocetak_bolovanja['pocetak_bolovanja']!=''){
                    for ($x = 0; $x < $max_do-$countBolovanje_total['bolovanje43']; $x++)
                        $statusi[$x]=$status_do;
                    for ($x = $max_do-$countBolovanje_total['bolovanje43']; $x < $day_difference; $x++){
                        $statusi[$x]=$status_od;
                        $nulifikacija =true;
                    }

                    for ($x = 0; $x < $max_do-$countBolovanje_do['bolovanje43']; $x++)
                        $statusi_popunjavanje[$x]=$status_do;
                    for ($x = $max_do-$countBolovanje_do['bolovanje43']; $x < $day_difference; $x++){
                        $statusi_popunjavanje[$x]=$status_od;
                    }

//nulifikacija pocetka bolovanja
                    if($nulifikacija){
                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                NULL,
                                $emp_id,
                            )
                        );
                    }
                }
                else{

                    for ($x = 0; $x < $day_difference; $x++){
                        $statusi[$x]=$status_od;
                    }
                    for ($x = 0; $x < $back_popunjavanje; $x++){
                        $statusi_popunjavanje[$x]=$status_od;
                    }

                }
            }
            elseif($day_difference<=$max_do){
                for ($x = 1; $x <= $day_difference; $x++)
                    $statusi[$x-1]=$status_do;

//Upis pocetka bolovanja
                if($pocetak_bolovanja['pocetak_bolovanja']=='' or $countPrekid['prekid']>10){
                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $dateFromDB,
                            $emp_id,
                        )
                    );
                }
            }
            elseif($day_difference>$max_do){
                for ($x = 1; $x <= $max_do; $x++)
                    $statusi[$x-1]=$status_do;
                for ($x = $max_do + 1; $x <= $day_difference; $x++)
                    $statusi[$x-1]=$status_od;
            }
        }

        //GO
        if($_POST['status']=='106'){

            $statusi = array();
            $count_prosla = $totalgoostPG;
            $count_trenutna = $totalgoost;



            $days_taken = 0;
            if($totalasked<=$totalgoostPG+$totalgoost){
                for ($x = 0; $x < $day_difference; $x++) {
                    if($count_prosla-$days_taken>0 and $get_days1[$x]['month_id']<=6){

                        $statusi[$x]='19';

                        if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY'){
                            $days_taken++;
                        }

                    } elseif($count_trenutna>0){

                        $statusi[$x]='18';
                        if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY') {
                            $days_taken++;
                            $count_trenutna--;
                        }

                    } else {
                        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                        return;
                    }
                }
            } else {
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                return;
            }


            /*
  if($totalasked<=$totalgoostPG+$totalgoost){
   for ($x = 0; $x < $day_difference; $x++) {
    if($count_prosla>0 and $get_days1[$x]['month_id']<=6){
  $statusi[$x]='19';
  if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY')
  $count_prosla--;
  }
  elseif($count_trenutna>0){
     $statusi[$x]='18';
    if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY')
  $count_trenutna--;
  }
  else{
    echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
    return;
  }
  }
  }
    else{
    echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
    return;
    }
    */
        }

        if ((($totalasked>$totalP_1ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='27')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_2ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='28')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_3ost) or ($totalasked>$totalploost)) and ($_POST['status']=='29')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_4ost) or ($totalasked>$totalploost)) and ($_POST['status']=='30')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_5ost) or ($totalasked>$totalploost)) and ($_POST['status']=='31')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_6ost) or ($totalasked>$totalploost)) and ($_POST['status']=='32')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_7ost) or ($totalasked>$totalploost)) and ($_POST['status']=='79')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if(($totalasked > 5) and ($_POST['status']=='30' or $_POST['status']=='72')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!').'</div>';return;}

        if(in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108))){
            $KindofDayString = 'OVERRIDE';
        }
        else{
            $KindofDayString = 'BHOLIDAY';
        }
        $count_for_spaseno = 0;
        date_default_timezone_set('Europe/Sarajevo');

        if(in_array($_POST['status'], array(84,67,106))){
            $d = date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 1 days'));


            $selected_new = 0;
            for ($x = 0; $x < $day_difference; $x++){
                $d = date('Y/m/d', strtotime(str_replace("/","-",$d). ' + 1 days'));



                $d2 = date('Y/m/d', strtotime(str_replace("/","-",$d). ' - 1 days'));
                $select_check = $db->prepare("SELECT [KindofDay] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
                WHERE [Date] = '$d2' and year_id = '$getYear' and employee_no = '$_user[employee_no]'");

                $select_check->execute();
                $f_c = $select_check->fetch();

                $new_request_id = $request_id_generate. "". $statusi[$x];

                if($f_c['KindofDay'] == 'BHOLIDAY'){
                    $d2_day = date('d', strtotime(str_replace("/","-",$d)));
                    $d2_month = date('m', strtotime(str_replace("/","-",$d)));
                    $d2_year = $getYear;

                    $new_request_id_x = $d2_day."".$d2_month."". $d2_year ."". $statusi[$x];
                    $selected_new = 1;
                }

                if($selected_new == 0){
                    $new_request_id = $new_request_id;
                } else {
                    $new_request_id = $new_request_id_x;
                }



                $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      hour = ?,
     hour_pre = null,
      timest_edit = ?,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
      status = ?,
    corr_status = ?,
    review_status = ?,
      corr_review_status = ?,
    employee_comment = ?,
    review_comment = ?,
    status_rejected = NULL,
    request_id = '$new_request_id'  
   where 
   [Date] = '".$d."'
   and year_id=?
   and KindofDay<>?
  and review_user is null 
  
   and [Date] >='".$_user['employment_date']."'";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['hour'],
                        date('Y-m-d h:i:s'),
                        date('Y-m-d h:i:s'),
                        $_user['employee_no'],
                        $statusi[$x],
                        $statusi[$x],
                        '0',
                        0,
                        $_POST['komentar'],
                        '',
                        $getYear,
                        $KindofDayString
                    )
                );



                if($res->rowCount()>=1) {
                    $count_for_spaseno++;
                }
            }



            if($_POST['status']=='67'){
                $bp = $back_popunjavanje + 1;
                $d = date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - '.$bp.' days'));
                $selected_new = 0;
                for ($x = 0; $x < $back_popunjavanje; $x++){

                    $d = date('Y/m/d', strtotime(str_replace("/","-",$d). ' + 1 days'));



                    $d2 = date('Y/m/d', strtotime(str_replace("/","-",$d). ' - 1 days'));
                    $select_check = $db->prepare("SELECT [KindofDay] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
                WHERE [Date] = '$d2' and year_id = '$getYear' and employee_no = '$_user[employee_no]'");

                    $select_check->execute();
                    $f_c = $select_check->fetch();

                    $new_request_id = $request_id_generate. "". $statusi_popunjavanje[$x];

                    if($f_c['KindofDay'] == 'BHOLIDAY'){
                        $d2_day = date('d', strtotime(str_replace("/","-",$d)));
                        $d2_month = date('m', strtotime(str_replace("/","-",$d)));
                        $d2_year = $getYear;

                        $new_request_id_x = $d2_day."".$d2_month."". $d2_year ."". $statusi_popunjavanje[$x];
                        $selected_new = 1;
                    }

                    if($selected_new == 0){
                        $new_request_id = $new_request_id;
                    } else {
                        $new_request_id = $new_request_id_x;
                    }

                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      hour = ?,
     hour_pre = null,
      timest_edit = ?,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
      status = ?,
    corr_status = ?,
    review_status = ?,
      corr_review_status = ?,
    employee_comment = ?,
    review_comment = ?,
    status_rejected = NULL,
    request_id = '$new_request_id' 
   where 
   [Date] = '".$d."'
   and year_id=?
   and KindofDay<>?
   and review_user is null
   and [Date] >='".$_user['employment_date']."'";

                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $_POST['hour'],
                            date('Y-m-d h:i:s'),
                            date('Y-m-d h:i:s'),
                            $_user['employee_no'],
                            $statusi_popunjavanje[$x],
                            $statusi_popunjavanje[$x],
                            '0',
                            0,
                            $_POST['komentar'],
                            '',
                            $getYear,
                            $KindofDayString
                        )
                    );
                    if($res->rowCount()>=1) {
                        $count_for_spaseno++;
                    }
                }}

        }
        else{

            if($_POST['status'] == 105){
                $KindofDayString = '';
            }
            $data = "
   declare @reqid integer
   set @reqid = 1
  
   UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
    hour = ?,
     hour_pre = null,
    timest_edit = ?,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
      status = ?,
    corr_status = ?,
    review_status = ?,
    corr_review_status = ?,
    employee_comment = ?,
    review_comment = ?,
    status_rejected = NULL,
  @reqid = case when [KindofDay] = 'BHOLIDAY' then @reqid+1 else @reqid end, 
  request_id = CONCAT(@reqid, '$request_id_generate') 
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
   and KindofDay<>?
   and review_user is null ";

            if(!in_array($status, array(43, 44, 45, 61, 62 ,65 ,67, 68, 69, 73, 74,75,76,77, 78, 81, 105, 107, 108))){
                $not_allowed = 1;
            } else {
                $not_allowed = 0;
            }
            $data .="
  and id = case when (([weekday] = '6' or [weekday] = '7')) and 1=$not_allowed then 0 else [id] end";

            $data .="
   and [Date] >='".$_user['employment_date']."'";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['hour'],
                    date('Y-m-d h:i:s'),
                    date('Y-m-d h:i:s'),
                    $_user['employee_no'],
                    $status,
                    $status,
                    '0',
                    '0',
                    $_POST['komentar'],
                    '',
                    $getYear,
                    $KindofDayString
                )
            );

            if($res->rowCount()>0) {
                $count_for_spaseno++;
            }
        }
        $setted = 0;




        if(($countHol>0 or $countOdobreno>0) and ($status != '67' and $status != '73' and $status != '81' and $status != '105') and ($count_for_spaseno <= 0 or ($count_for_spaseno > 0 and $status == '106') ) ){
            $setted = 1;
            echo '<div class="alert alert-danger text-center">'.__('Upozorenje : Državni praznici većeg prioriteta i odobrene registracije nisu ažurirani!').'</div>';}


        if($count_for_spaseno > 0){
            echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';



            // Mail notifikacija

            $get_mail_settings = $db->query("SELECT name, value FROM [c0_intranet2_apoteke].[dbo].[settings] WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
            $get_mail_fetch = $get_mail_settings->fetchAll();

            $mail_settings = array();
            foreach($get_mail_fetch as $key => $value){
                $mail_settings[$value['name']] = $value['value'];
            }

            $array_bolovanje = array( "43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27",  "105", "106", "18", "19");

            // Bolovanje i placena odsustva
            if($mail_settings['hr_notifications'] == '1'){
                if(in_array($_POST['status'], $array_bolovanje))
                {
                    // start mail

                    $status_izostanka = $status;

                    $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                    $user_edit = $_user;

                    require '../../lib/PHPMailer/PHPMailer.php';
                    require '../../lib/PHPMailer/SMTP.php';
                    require '../../lib/PHPMailer/Exception.php';
                    require '../../mails.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = "UTF-8";

                    $mail->IsSMTP();
                    $mail->isHTML(true);  // Set email format to HTML

                    $mail->Host = "barbbcom";
                    //$mail->SMTPSecure = 'tls';
                    $mail->Port = 25;

                    $parent_user = _employee($_user['parent']);



                    if(in_array($_POST['status'], array(73))){
                        // sluzbeni put svi

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
                        //$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

                    } else if(in_array($_POST['status'], array(81))){
                        // sluzbeni put EDUKACIJA

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("edukacija.hr@raiffeisengroup.ba");


                    } else if(in_array($_POST['status'], array(106, 18, 19))){
                        // godišnji odmori

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

                    } else {

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress(@$mail_settings['hr_supportt_mail']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
                    }

                    $mail->Subject  = 'Registracija izostanka';
                    //$_user=$user_edit;





                    $mail->Body     = $mails['day-edit'];


                    if(!$mail->send()) {
                        //echo 'Message was not sent.';
                        //echo 'Mailer error: ' . $mail->ErrorInfo;
                    } else {
                        //echo 'Message has been sent.';
                    }
                }
            }


            // kraj mail notifikacije






        } else {
            if($setted == 0):
                echo '<div class="alert alert-danger text-center">'.__('Odobrene registracije nisu ažurirane!').'</div>';
            endif;
        }

    }

    //GRUPNO AZURIRANJE KOREKCIJE
    if($_POST['request']=='parent-day-add_apsolute_corrections' or ($_POST['request']=='day-edit_corrections' and $_POST['status']==67)){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $br_sati = $_user['br_sati'];
        $status = $_POST['status'];

        if($_POST['request']=='day-edit_corrections' and $_POST['status']==67){
            //echo 'hvata ovaj';
            $this_id = $_POST['request_id'];
            $status = $_POST['status'];

            $check = $db->query("SELECT user_id, year_id,month_id,employee_no, [Date] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id."' ");
            foreach ($check  as $checkvalue) {
                $getYear = $checkvalue['year_id'];
                $getMonth=$checkvalue['month_id'];
                $filter_emp=$checkvalue['employee_no'];
                $FromDay=$checkvalue['Date'];
                $ToDay=$checkvalue['Date'];
            }
        }
        else{
            $FromDay=$_POST['dateFrom'];

            $ToDay=$_POST['dateTo'];
            $getMonth=$_POST['get_month'];
            $getYear=$_POST['get_year'];
        }

        $count_for_spaseno = 0;

        $dateFrom = strtotime(str_replace("/","-",$FromDay));
        $dateTo = strtotime(str_replace("/","-",$ToDay));
        $datediff = $dateTo - $dateFrom;
        $day_difference = floor($datediff / (60 * 60 * 24)) + 1;

        $dateFromDB = date('Y/m/d',strtotime(str_replace("/","-",$FromDay)));
        $dateToDB = date('Y/m/d',strtotime(str_replace("/","-",$ToDay)));

        $month_from = date("n", strtotime(str_replace("/","-",$FromDay)));
        $month_to = date("n", strtotime(str_replace("/","-",$ToDay)));

        $day_from = date("j", strtotime(str_replace("/","-",$FromDay)));
        $day_to = date("j", strtotime(str_replace("/","-",$ToDay)));

        $request_id_generate = $day_from."".$day_to."".$month_from."".$month_to."".$getYear;

        $emp=$db->query("SELECT employee_no, YEAR(Date) as godina FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."'  
  ");
        foreach($emp as $valueemp)
        {$empid=$valueemp['employee_no']; $godina = $valueemp['godina']; }
        $go=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");

        $get_count_holiday=$db->query("SELECT count(KindOfDay) as countHol FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (KindOfDay='BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countHoliday = $get_count_holiday->fetch();
        $countHol = $countHoliday['countHol'];

        $get_count_odobreno=$db->query("SELECT count(*) as countOdobreno FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_review_status='1') and (KindofDay<>'BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countOdo = $get_count_odobreno->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        $get_days = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and month_id<=12
   and year_id=".$getYear);

        $get_days1 = $get_days->fetchAll();

        $day_before = $day_from-3;

        $get_days2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     
   (
   (day >= ".$day_before." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_before." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);

        $get_days2 = $get_days2->fetchAll();

        $get_count_not_vikend=$db->query("SELECT count(*) as not_vikend FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE weekday not in (6,7) and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_vikend = $get_count_not_vikend->fetch();
        $not_vikend = $count_not_vikend['not_vikend'];

        if($not_vikend==0 and !in_array($_POST['status'], array(43, 44, 45, 61, 62 ,65 ,67, 68, 69, 73, 74,75,76,77, 78, 81, 105, 107, 108))){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete registrovati vikend.').'</div>';
            return;
        }

        $nex_year = getYearId($getYear, $_user['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $_user['user_id'], 'prev', true);

        $currgo = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (corr_status='18')) or (year_id = '".$nex_year."' and corr_status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV_corrections is null)");



        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where 
   weekday<>'6' AND weekday<>'7' and
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);


        $currgoPG = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (corr_status='19')) or (year_id = '".$pre_year."' and corr_status = '18')) AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (date_NAV_corrections is null)");


        $currgototal = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19' or corr_status='18')");

        $death = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
    AND weekday<>'6' AND weekday<>'7' AND (corr_status='72')");
        $pcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $plo=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currplo = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND ((corr_status='27') or (corr_status='28') or (corr_status='29') or (corr_status='30') or (corr_status='31')   
  or (corr_status='32') or (corr_status='79'))");
        $currpcm = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='21')");
        $upcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currupcm = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='22')");

        $curruP_1 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='27')");
        $curruP_2 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='28')");
        $curruP_3 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='29')");
        $curruP_4 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='30')");
        $curruP_5 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='31')");
        $curruP_6 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='32')");
        $curruP_7 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='79')");
        $P_1=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_2=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_3=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_4=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_5=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_6=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_7=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_1a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='27'");
        $P_2a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='28'");
        $P_3a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='29'");
        $P_4a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='30'");
        $P_5a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='31'");
        $P_6a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='32'");
        $P_7a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='79'");
        foreach($go as $valuego) {
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristeno=$valuego['Br_dana_iskoristeno'];
            $ostalo = $valuego['Br_dana_ostalo'];
            $brdana=$valuego['Br_dana'];
            $totalkrv=$valuego['Blood_days'];
            $totaldeath=$valuego['S_1_used'];
            $iskoristenokrv=$valuego['P_6_used'];
            $propaloGO=$valuego['G_2 not valid'];
        }

        foreach($askedgo as $valueasked) {
            $askeddays=$valueasked['sum_hour'];
            $totalasked=$askeddays/$br_sati;}
        foreach($currgo as $valuecurrgo) {
            $iskoristenocurr=$valuecurrgo['sum_hour'];;
            $iskoristenototal=($iskoristenocurr/$br_sati) ;
            $totalgoost=$brdana-$iskoristenototal;}
        foreach($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG=$valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG=($iskoristenocurrPG/$br_sati) ;
            $totalgoostPG=$brdanaPG-$iskoristenototalPG;
            $ukupnogoiskoristeno=$iskoristenototalPG+$iskoristenototal;
            $ukupnogoost=$totalgoost+$totalgoostPG;}
        foreach($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm=$valuepcm['Candelmas_paid'];}
        foreach($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm=$valueupcm['Candelmas_unpaid'];}
        foreach($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }
        foreach($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm=$valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm=($iskoristenocurrpcm/$br_sati) ;
            $totalpcmost=$brdanapcm-$iskoristenototalpcm;
        }
        foreach($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm=$valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm=($iskoristenocurrupcm/$br_sati) ;
            $totalupcmost=$brdanaupcm-$iskoristenototalupcm;
        }

        foreach($curruP_1  as $valuecurrP_1) {
            $iskoristenocurrP_1=$valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1=($iskoristenocurrP_1/$br_sati) ;
            $totalP_1ost=$totalP_1-$iskoristenototalP_1;
        }
        foreach($curruP_2  as $valuecurrP_2) {
            $iskoristenocurrP_2=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2=($iskoristenocurrP_2/$br_sati) ;
            $totalP_2ost=$totalP_2-$iskoristenototalP_2;
        }
        foreach($curruP_3  as $valuecurrP_3) {
            $iskoristenocurrP_3=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3=($iskoristenocurrP_3/$br_sati) ;
            $totalP_3ost=$totalP_3-$iskoristenototalP_3;
        }
        foreach($curruP_4  as $valuecurrP_4) {
            $iskoristenocurrP_4=$valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4=($iskoristenocurrP_4/$br_sati);
            $totalP_4ost=$totalP_4-$iskoristenototalP_4;
        }
        foreach($curruP_5  as $valuecurrP_5) {
            $iskoristenocurrP_5=$valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5=($iskoristenocurrP_5/$br_sati) ;
            $totalP_5ost=$totalP_5-$iskoristenototalP_5;
        }
        foreach($curruP_6  as $valuecurrP_6) {
            $iskoristenocurrP_6=$valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6=($iskoristenocurrP_6/$br_sati) ;
            $totalP_6ost=$totalP_6-$iskoristenototalP_6;
        }
        foreach($curruP_7  as $valuecurrP_7) {
            $iskoristenocurrP_7=$valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7=($iskoristenocurrP_7/$br_sati) ;
            $totalP_7ost=$totalP_7-$iskoristenototalP_7;
        }
        foreach($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used']+$valueplo['P_2_used']+$valueplo['P_3_used']+$valueplo['P_4_used']+$valueplo['P_5_used']+$valueplo['P_6_used']+$valueplo['P_7_used'];
            $totalplo =$valueplo['Br_dana_PLO'];
        }

        foreach($currplo as $valuecurrplo) {
            $iskoristenocurrplo=$valuecurrplo['sum_hour'];
            $iskoristenototalplo=($iskoristenocurrplo/$br_sati) ;
            $totalploost=$totalplo-$iskoristenototalplo;
        }

        //VJERSKI PRAZNICI
        /*
    if($_POST['status']=='84'){

  $statusi = array('0','0','0','0');

    if(($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=2){
      for ($x = 1; $x <= $day_difference; $x++) {
    $statusi[$x-1]='21';
}
      }
    elseif($iskoristenototalpcm>=2 and (($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=4)){
           for ($x = 1; $x <= $day_difference; $x++) {
    $statusi[$x-1]='22';
}
    }
      elseif($iskoristenototalpcm+$iskoristenototalupcm+$totalasked<=4){
           $count_placeni = 0;
           for ($x = 1; $x <= $day_difference; $x++) {
    if($get_days1[$x-1]['weekday']!='6' and $get_days1[$x-1]['weekday']!='7' and $get_days1[$x-1]['KindofDay']!='BHOLIDAY'){
    if($iskoristenototalpcm+$count_placeni<2){
  $statusi[$x-1]='21';
  $count_placeni++;
  }
  else{
    $statusi[$x-1]='22';
  }
  }
  else{
      if($get_days1[$x-1]['KindofDay']=='BHOLIDAY')
    $statusi[$x-1]=$get_days1[$x-1]['status'];
  else
    $statusi[$x-1]='21';

    }
}
    }
    else{
    echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika!').'</div>';
    return;
    }
  }
  */
        if($_POST['status']=='84'){

            $statusi = array('0','0','0','0');

            if(($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=2){
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x-1]='21';
                }
            }
            elseif($iskoristenototalpcm>=2 and (($iskoristenototalpcm+$iskoristenototalupcm)+$totalasked<=4)){
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x-1]='22';
                }
            }
            elseif($iskoristenototalpcm+$iskoristenototalupcm+$totalasked<=4){
                $count_placeni = 0;
                for ($x = 1; $x <= $day_difference; $x++) {
                    if($get_days1[$x-1]['weekday']!='6' and $get_days1[$x-1]['weekday']!='7' and $get_days1[$x-1]['KindofDay']!='BHOLIDAY'){
                        if($iskoristenototalpcm+$count_placeni<2){
                            $statusi[$x-1]='21';
                            $count_placeni++;
                        }
                        else{
                            $statusi[$x-1]='22';
                        }
                    }
                    else{
                        if($get_days1[$x-1]['KindofDay']=='BHOLIDAY')
                            $statusi[$x-1]=$get_days1[$x-1]['status'];
                        else
                        {
                            if($iskoristenototalpcm < 2){
                                $statusi[$x-1]='22';
                            } else if($iskoristenototalupcm < 2){
                                $statusi[$x-1]='21';
                            } else {
                                $status[$x-1] = '5';
                            }
                        }

                    }
                }
            }
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika!').'</div>';
                return;
            }
        }

        //BOLOVANJE
        if($_POST['status']=='67'){

            $emp_bol=$db->query("SELECT top 1 user_id, employee_no FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id=".$getYear);
            foreach($emp_bol as $valueemp)
            {$emp_no=$valueemp['employee_no'];$emp_id=$valueemp['user_id'];}

            $nulifikacija = false;
            $back_popunjavanje = 0;
            $statusi = array();
            $statusi_popunjavanje = array();

            $previous_day =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 1 days'));
            $previous_day_1 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 2 days'));
            $previous_day_2 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 3 days'));
            $previous_day_3 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 4 days'));
            $previous_day_4 =  date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 5 days'));

            $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee] WHERE [No_]='".$emp_no."'");
            if($get->rowCount()<0){
                $row_employee = $get->fetch();
                $entitet = $row_employee['Org Entity Code'];}

            if($entitet=='FBIH' or $entitet=='BD'){
                $max_do = 42;
                $status_do = 43;
                $status_od = 44;
            }
            elseif($entitet=='RS'){
                $max_do = 30;
                $status_do = 107;
                $status_od = 108;
            }

            $get_date_bolovanje = $db->query("SELECT pocetak_bolovanja as pocetak_bolovanja FROM [c0_intranet2_apoteke].[dbo].[bolovanje] where 
    user_id = ".$emp_id);
            $pocetak_bolovanja = $get_date_bolovanje->fetch();

            if($pocetak_bolovanja['pocetak_bolovanja']==''){
                $countBolovanje_total['bolovanje43'] = 0;
                $countBolovanje_do['bolovanje43'] = 0;
            }
            else{
                $get_prekid = $db->query("SELECT count(*) as prekid FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (corr_status <>".$status_do.")
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countPrekid = $get_prekid->fetch();

                $get_bolovanje_total = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (corr_status =".$status_do." or (weekday in ('6','7')) or KindOfDay = 'BHOLIDAY')
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countBolovanje_total = $get_bolovanje_total->fetch();

                $get_bolovanje_do = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
  WHERE 
   (corr_status =".$status_do.")
   and ([Date] >= '".$pocetak_bolovanja['pocetak_bolovanja']."' and [Date] < '".$dateFromDB."') and year_id=".$getYear);

                $countBolovanje_do = $get_bolovanje_do->fetch();
            }

            $get_previous_days = $db->query("SELECT corr_status, KindOfDay, weekday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
  WHERE 
   ([Date] in ('".$previous_day."','".$previous_day_1."','".$previous_day_2."','".$previous_day_3."','".$previous_day_4."')) and year_id=".$getYear." order by [Date] DESC");

            $previous_days_get = $get_previous_days->fetchAll();

            for ($x = 0; $x < count($previous_days_get); $x++){
                if(@$previous_days_get[$x]['corr_status']==$status_od or @$previous_days_get[$x]['corr_status']==$status_do){
                    $back_popunjavanje = $x;
                    break;
                }
            }

            $dan_ranije_bolovanje = (@$previous_days_get[0]['corr_status']== $status_od or @$previous_days_get[0]['corr_status']== $status_do);
            $dan_ranije_subota = ((@$previous_days_get[0]['weekday']=='6') and $back_popunjavanje );
            $dan_ranije_nedelja = ((@$previous_days_get[0]['weekday']=='7') and $back_popunjavanje );
            $dan_ranije_praznik = ((@$previous_days_get[0]['KindOfDay']=='BHOLIDAY') and $back_popunjavanje);

            if(
            ($dan_ranije_bolovanje or $dan_ranije_subota or $dan_ranije_nedelja or $dan_ranije_praznik)
            )
            {
                if($pocetak_bolovanja['pocetak_bolovanja']!=''){
                    for ($x = 0; $x < $max_do-$countBolovanje_total['bolovanje43']; $x++)
                        $statusi[$x]=$status_do;
                    for ($x = $max_do-$countBolovanje_total['bolovanje43']; $x < $day_difference; $x++){
                        $statusi[$x]=$status_od;
                        $nulifikacija =true;
                    }

                    for ($x = 0; $x < $max_do-$countBolovanje_do['bolovanje43']; $x++)
                        $statusi_popunjavanje[$x]=$status_do;
                    for ($x = $max_do-$countBolovanje_do['bolovanje43']; $x < $day_difference; $x++){
                        $statusi_popunjavanje[$x]=$status_od;
                    }

//nulifikacija pocetka bolovanja
                    if($nulifikacija){
                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                NULL,
                                $emp_id,
                            )
                        );
                    }
                }
                else{

                    for ($x = 0; $x < $day_difference; $x++){
                        $statusi[$x]=$status_od;
                    }
                    for ($x = 0; $x < $back_popunjavanje; $x++){
                        $statusi_popunjavanje[$x]=$status_od;
                    }

                }
            }
            elseif($day_difference<=$max_do){
                for ($x = 1; $x <= $day_difference; $x++)
                    $statusi[$x-1]=$status_do;

//Upis pocetka bolovanja
                if($pocetak_bolovanja['pocetak_bolovanja']=='' or $countPrekid['prekid']>10){
                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[bolovanje] SET
    pocetak_bolovanja = ?
   where 
  user_id=?";
                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $dateFromDB,
                            $emp_id,
                        )
                    );
                }
            }
            elseif($day_difference>$max_do){
                for ($x = 1; $x <= $max_do; $x++)
                    $statusi[$x-1]=$status_do;
                for ($x = $max_do + 1; $x <= $day_difference; $x++)
                    $statusi[$x-1]=$status_od;
            }
        }

        //GO
        if($_POST['status']=='106'){

            $statusi = array();
            $count_prosla = $totalgoostPG;
            $count_trenutna = $totalgoost;


            if($totalasked<=$totalgoostPG+$totalgoost){
                for ($x = 0; $x < $day_difference; $x++) {
                    if($count_prosla>0 and $get_days1[$x]['month_id']<=6){
                        $statusi[$x]='19';
                        if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY')
                            $count_prosla--;
                    }
                    elseif($count_trenutna>0){
                        $statusi[$x]='18';
                        if($get_days1[$x]['weekday']!='6' and $get_days1[$x]['weekday']!='7' and $get_days1[$x]['KindofDay']!='BHOLIDAY')
                            $count_trenutna--;
                    }
                    else{
                        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                        return;
                    }
                }
            }
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                return;
            }

        }

        if($totalasked>$totalgoost and $_POST['status']=='18') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !').'</div>';return;}
        if ($totalasked>$totalgoostPG and $_POST['status']=='19') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !').'</div>';return;}
        if(( $_POST['status']=='19') and ($propaloGO==1)) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Nemate pravo na godišnji iz predhodne godine!!').'</div>';return;}
        if(($totalasked>$totalpcmost) and ($_POST['status']=='21')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!').'</div>';return;}
        if(($totalasked>$totalupcmost) and ($_POST['status']=='22')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_1ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='27')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_2ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='28')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_3ost) or ($totalasked>$totalploost)) and ($_POST['status']=='29')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_4ost) or ($totalasked>$totalploost)) and ($_POST['status']=='30')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_5ost) or ($totalasked>$totalploost)) and ($_POST['status']=='31')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_6ost) or ($totalasked>$totalploost)) and ($_POST['status']=='32')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalasked>$totalP_7ost) or ($totalasked>$totalploost)) and ($_POST['status']=='79')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if(($totalasked > 5) and ($_POST['status']=='30' or $_POST['status']=='72')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!').'</div>';return;}

        if(in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108))){
            $KindofDayString = 'OVERRIDE';

        }
        else{
            $KindofDayString = 'BHOLIDAY';

        }
        $selected_new = 0;
        date_default_timezone_set('Europe/Sarajevo');
        if(in_array($_POST['status'], array(84,67,106))){
            $d = date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - 1 days'));
            for ($x = 0; $x < $day_difference; $x++){
                $d = date('Y/m/d', strtotime(str_replace("/","-",$d). ' + 1 days'));

                $d2 = date('Y/m/d', strtotime(str_replace("/","-",$d). ' - 1 days'));
                $select_check = $db->prepare("SELECT [KindofDay] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
                WHERE [Date] = '$d2' and year_id = '$getYear'");

                $select_check->execute();
                $f_c = $select_check->fetch();

                $new_request_id = $request_id_generate. "". $statusi[$x];

                if($f_c['KindofDay'] == 'BHOLIDAY'){
                    $d2_day = date('d', strtotime(str_replace("/","-",$d)));
                    $d2_month = date('m', strtotime(str_replace("/","-",$d)));
                    $d2_year = $getYear;

                    $new_request_id_x = $d2_day."".$d2_month."". $d2_year ."". $statusi[$x];
                    $selected_new = 1;
                }

                if($selected_new == 0){
                    $new_request_id = $new_request_id;
                } else {
                    $new_request_id = $new_request_id_x;
                }


                $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
     hour = ?,
    hour_pre = null,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
     corr_status = ?,
    corr_review_status = ?,
    
    employee_comment = ?,
  review_comment = ?,
  request_id = '$new_request_id' 
   where 
  [Date] = '".$d."'
  and year_id=?
  and KindofDay<>?
  and review_user is null
 and [Date] >='".$_user['employment_date']."'";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['hour'],
                        date('Y-m-d h:i:s'),
                        $_user['employee_no'],
                        $statusi[$x],
                        '0',
                        //'',
                        $_POST['komentar'],
                        '',
                        $getYear,
                        $KindofDayString
                    )
                );

                if($res->rowCount() >= 1):
                    $count_for_spaseno++;
                endif;
            }

            if($_POST['status']=='67'){
                $bp = $back_popunjavanje + 1;
                $d = date('Y/m/d', strtotime(str_replace("/","-",$FromDay). ' - '.$bp.' days'));
                $selected_new = 0;
                for ($x = 0; $x < $back_popunjavanje; $x++){
                    $d = date('Y/m/d', strtotime(str_replace("/","-",$d). ' + 1 days'));

                    $d2 = date('Y/m/d', strtotime(str_replace("/","-",$d). ' - 1 days'));
                    $select_check = $db->prepare("SELECT [KindofDay] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
                WHERE [Date] = '$d2' and year_id = '$getYear'");

                    $select_check->execute();
                    $f_c = $select_check->fetch();

                    @$new_request_id = $request_id_generate. "". $statusi_popunjavanje[$x];

                    if($f_c['KindofDay'] == 'BHOLIDAY'){
                        $d2_day = date('d', strtotime(str_replace("/","-",$d)));
                        $d2_month = date('m', strtotime(str_replace("/","-",$d)));
                        $d2_year = $getYear;

                        $new_request_id_x = $d2_day."".$d2_month."". $d2_year ."". $statusi_popunjavanje[$x];
                        $selected_new = 1;
                    }

                    if($selected_new == 0){
                        $new_request_id = $new_request_id;
                    } else {
                        $new_request_id = $new_request_id_x;
                    }

                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
     hour = ?,
    hour_pre = null,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
     corr_status = ?,
    corr_review_status = ?,
   
    employee_comment = ?,
  review_comment = ?,
  request_id = '$new_request_id' 
   where 
  [Date] = '".$d."'
  and year_id=?
  and KindofDay<>?
  and review_user is null 
  and [Date] >='".$_user['employment_date']."'";

                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $_POST['hour'],
                            date('Y-m-d h:i:s'),
                            $_user['employee_no'],
                            @$statusi_popunjavanje[$x],
                            '0',
                            // '',
                            $_POST['komentar'],
                            '',
                            $getYear,
                            $KindofDayString)
                    );
                    if($res->rowCount() >= 1):
                        $count_for_spaseno++;
                    endif;

                }}

        }
        else{
            if($status == 105){
                $KindofDayString = '';
            }



            if(in_array($status, array('84', '21', '22'))){
                $weekday_rule = "and weekday NOT IN (6,7)";
            } else {
                $weekday_rule = "";
            }

            date_default_timezone_set('Europe/Sarajevo');
            $data = "
  declare @reqid integer
  set @reqid = 1
  UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      hour = ?,
     hour_pre = null,
      timest_edit_corr = ?,
    employee_timest_edit = ?,
      corr_status = ?,
    corr_review_status = ?,
    employee_comment = ?,
    review_comment = ?,
  @reqid = case when [KindofDay] = 'BHOLIDAY' then @reqid+1 else @reqid end, 
  request_id = CONCAT(@reqid, '$request_id_generate') 
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
   and KindofDay<>?
   $weekday_rule
   and review_user is null
   ";
            if(!in_array($status, array(43, 44, 45, 61, 62 ,65 ,67, 68, 69, 73, 74,75,76,77, 78, 81, 105, 107, 108))){
                $not_allowed = 1;
            } else {
                $not_allowed = 0;
            }
            $data .="
  and id = case when ([weekday] = '6' or [weekday] = '7') and 1=$not_allowed then 0 else [id] end";

            $data .= "
   and [Date] >='".$_user['employment_date']."'";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['hour'],
                    date('Y-m-d h:i:s'),
                    $_user['employee_no'],
                    $status,
                    '0',
                    $_POST['komentar'],
                    '',
                    $getYear,
                    $KindofDayString
                )
            );
            if($res->rowCount() >= 1):
                $count_for_spaseno++;
            endif;

        }

        $setted = 0;


        if(($countHol>0 or $countOdobreno>0) and ($status != '67' and $status != '73' and $status != '81' and $status != '105') and ($count_for_spaseno <= 0 or ($count_for_spaseno > 0 and $status == '106') ) ){
            $setted = 1;
            echo '<div class="alert alert-danger text-center">'.__('Upozorenje : Državni praznici većeg prioriteta i odobrene registracije nisu ažurirani!').'</div>';}

        if($count_for_spaseno > 0){
            echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';


            // Mail notifikacija

            $get_mail_settings = $db->query("SELECT name, value FROM [c0_intranet2_apoteke].[dbo].[settings] WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
            $get_mail_fetch = $get_mail_settings->fetchAll();

            $mail_settings = array();
            foreach($get_mail_fetch as $key => $value){
                $mail_settings[$value['name']] = $value['value'];
            }

            $array_bolovanje = array( "43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27",  "105", "106", "18", "19");

            // Bolovanje i placena odsustva
            if($mail_settings['hr_notifications'] == '1'){
                if(in_array($_POST['status'], $array_bolovanje))
                {
                    // start mail

                    $status_izostanka = $status;

                    $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                    $user_edit = $_user;


                    require '../../lib/PHPMailer/PHPMailer.php';
                    require '../../lib/PHPMailer/SMTP.php';
                    require '../../lib/PHPMailer/Exception.php';
                    require '../../mails.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = "UTF-8";

                    $mail->IsSMTP();
                    $mail->isHTML(true);  // Set email format to HTML

                    $mail->Host = "barbbcom";
                    //$mail->SMTPSecure = 'tls';
                    $mail->Port = 25;


                    $parent_user = _employee($_user['parent']);



                    if(in_array($_POST['status'], array(73))){
                        // sluzbeni put svi

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
                        //$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

                    } else if(in_array($_POST['status'], array(81))){
                        // sluzbeni put EDUKACIJA

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("edukacija.hr@raiffeisengroup.ba");


                    } else if(in_array($_POST['status'], array(106, 18, 19))){
                        // godišnji odmori

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

                    } else {

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress(@$mail_settings['hr_supportt_mail']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
                    }

                    $mail->Subject  = 'Registracija izostanka';
                    //$_user=$user_edit;




                    $mail->Body     = $mails['day-edit'];
                    if(!$mail->send()) {
                        //echo 'Message was not sent.';
                        //echo 'Mailer error: ' . $mail->ErrorInfo;
                    } else {
                        //echo 'Message has been sent.';
                    }
                }
            }


            // kraj mail notifikacije




        } else {
            if($setted == 0):
                echo '<div class="alert alert-danger text-center">'.__('Odobrene registracije nisu ažurirane!').'</div>';
            endif;
        }


    }

    //OTKAZIVANJE REGISTRACIJE
    if($_POST['request']=='parent-day-cancel_apsolute'){

        //$status = $_POST['status'];

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $FromDay=$_POST['dateFrom'];
        $ToDay=$_POST['dateTo'];
        $getMonth=$_POST['get_month'];
        $getYear=$_POST['get_year'];

        $dateFrom = strtotime(str_replace("/","-",$FromDay));
        $dateTo = strtotime(str_replace("/","-",$ToDay));
        //$datediff = $dateTo - $dateFrom;
        //$day_difference = floor($datediff / (60 * 60 * 24)) + 1;

        $month_from = date("n", strtotime(str_replace("/","-",$FromDay)));
        $month_to = date("n", strtotime(str_replace("/","-",$ToDay)));

        $day_from = date("j", strtotime(str_replace("/","-",$FromDay)));
        $day_to = date("j", strtotime(str_replace("/","-",$ToDay)));

        $request_id_generate = $day_from."".$day_to."".$month_from."".$month_to."".$getYear;

        $same_days = 0;

        if($FromDay == $ToDay){
            $same_days = 1;
        }


        $get_statuses = $db->query("SELECT status FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (status<>'5') and (
     (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
     (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
     ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
     (month_id > ".$month_from." and month_id < ".$month_to.")
     )
     and year_id=".$getYear);
        $fetch_statuses = $get_statuses->fetchAll();
        $statusi_array = array();
        $kakoNazvatiovuVarijablu = 0;

        // bolovanje/sluzbeni put
        foreach($fetch_statuses as $key => $v):
            if(in_array($v['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 107, 108, 73, 81))){
                $kakoNazvatiovuVarijablu = 1;
            }
        endforeach;

        $get_count1=$db->query("SELECT count(*) as countOdobreno FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (review_status='1') and (status<>'83') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];




        // praznici check

        /***************************************/

        $get_count_praznik=$db->query("SELECT COUNT(*) as not_praznik  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ( status = '83' and KindOfDay = 'BHOLIDAY')  and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_praznik = $get_count_praznik->fetch();

        $notPraznik = $count_not_praznik['not_praznik'];

        /****************************************/



        $get_count2=$db->query("SELECT count(*) as not_redovni FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((status='5' and hour != '0' and weekday in (6,7)) or (status != '5')) and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_redovni = $get_count2->fetch();
        $notRedovni = $count_not_redovni['not_redovni'];

        $get_count3=$db->query("SELECT count(*) as req_sent FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (change_req='1') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_req_sent = $get_count3->fetch();
        $countSent = $count_req_sent['req_sent'];

        $get_count_not_vikend=$db->query("SELECT count(*) as not_vikend FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE weekday not in (6,7) and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_vikend = $get_count_not_vikend->fetch();
        $not_vikend = $count_not_vikend['not_vikend'];


        if($notPraznik == 1 and $same_days == 1 ){
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete otkazati praznik.').'</div>';
            return;
        }
        if($not_vikend==0  and $kakoNazvatiovuVarijablu == 0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete izvršiti otkazivanje redovnog rada.').'</div>';
            return;
        }

        if($countSent>0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Zahtjev za otkazivanje je već poslan!').'</div>';
            return;
        }

        if($notRedovni==0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete izvršiti otkazivanje redovnog rada.').'</div>';
            return;
        }

        //otkazivanje sl puta mail
        $send_email = false;
        foreach($fetch_statuses as $key => $v){
            if(in_array($v['status'], array(73))){
                $send_email = true;
                $statuss= 73;
            }
            if(in_array($v['status'], array(81))){
                $send_email = true;
                $statuss= 81;
            }
        }

        if($send_email){mail_cancel_trip($_POST['dateFrom'],$_POST['dateTo'],$statuss);}

        /* test svn y x z s a*/
        if($countOdobreno>0 ){
            date_default_timezone_set('Europe/Sarajevo');
            $data = "
  
  declare @k integer
  set @k = 0
  
  UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
  @k =   case when ([KindofDay] = 'BHOLIDAY' and status != '83' and review_status = '0') then 1
          when ([KindofDay] != 'BHOLIDAY' and status != '5' and review_status = '0') then 1
          else 0 end,
  
  
  status = case when ([KindofDay] = 'BHOLIDAY' and status != '83' and review_status = '0') then 83
          when ([KindofDay] != 'BHOLIDAY' and status != '5' and review_status = '0') then 5
          else [status] end,
          
  corr_status = case when ([KindofDay] = 'BHOLIDAY' and corr_status != '83' and corr_review_status = '0') then 83
  else [corr_status] end,
  
  corr_review_status = case when ([KindofDay] = 'BHOLIDAY' and @k = 1) then '1' 
             when ([KindofDay] <> 'BHOLIDAY' and @k = 1) then '0'
             else [corr_review_status] end,
          
  review_status = case when ([KindofDay] = 'BHOLIDAY' and @k = 1) then '1' 
             when ([KindofDay] <> 'BHOLIDAY' and @k = 1) then '0'
             else [review_status] end,
  
  timest_edit = '".date('Y-m-d h:i:s')."',
    change_req = case when @k = 0 then '1' else [change_req] end,
    employee_comment = case when @k = 0 then '' else [employee_comment] end,
    
    request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $getYear
                )
            );

            echo '<div class="alert alert-success text-center"><b>'.__('Obavijest :').'</b><br/>'.__('Zahtjev za otkazivanje odobrene registracije poslan!').'</div>';
            return;
        }

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      timest_edit = ?,
      status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
    corr_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
    review_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 1 else 0 end,
    KindOfDay = case when (status_bh is not null) then 'BHOLIDAY' else KindOfDay end,
    Description = case when (status_bh is not null and status_bh = '83') then case when (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date] and (a.[department name] = '' or a.[department name] = N'".$_user['Stream_description']."' or a.[department name] = N'".$_user['Team_description']."' )) IS NOT NULL then (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date] and (a.[department name] = '' or a.[department name] = N'".$_user['Stream_description']."' or a.[department name] = N'".$_user['Team_description']."' )) else Description end else Description end,
    employee_comment = ?,
    hour = case when [weekday] in (6,7) then 0 else 
      (select br_sati from c0_intranet2_apoteke.dbo.users as u 
join c0_intranet2_apoteke.dbo.hourlyrate_year as y on u.user_id = y.user_id where y.id = $getYear) end,
    request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(
                date('Y-m-d h:i:s'),
                '',
                $getYear
            )
        );

        echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';
        //echo $data;
    }

    //OTKAZIVANJE REGISTRACIJE KOREKCIJE
    if($_POST['request']=='parent-day-cancel_apsolute_corrections'){

        //$status = $_POST['status'];

        $FromDay=$_POST['dateFrom'];
        $ToDay=$_POST['dateTo'];
        $getMonth=$_POST['get_month'];
        $getYear=$_POST['get_year'];

        $dateFrom = strtotime(str_replace("/","-",$FromDay));
        $dateTo = strtotime(str_replace("/","-",$ToDay));
        //$datediff = $dateTo - $dateFrom;
        //$day_difference = floor($datediff / (60 * 60 * 24)) + 1;

        $month_from = date("n", strtotime(str_replace("/","-",$FromDay)));
        $month_to = date("n", strtotime(str_replace("/","-",$ToDay)));

        $day_from = date("j", strtotime(str_replace("/","-",$FromDay)));
        $day_to = date("j", strtotime(str_replace("/","-",$ToDay)));

        $same_days = 0;

        if($FromDay == $ToDay){
            $same_days = 1;
        }

        $request_id_generate = $day_from."".$day_to."".$month_from."".$month_to."".$getYear;



        $get_statuses = $db->query("SELECT corr_status FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_status<>'5') and (
     (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
     (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
     ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
     (month_id > ".$month_from." and month_id < ".$month_to.")
     )
     and year_id=".$getYear);
        $fetch_statuses = $get_statuses->fetchAll();
        $statusi_array = array();
        $kakoNazvatiovuVarijablu = 0;

        // bolovanje/sluzbeni put
        foreach($fetch_statuses as $key => $v):
            if(in_array($v['corr_status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 107, 108, 73, 81))){
                $kakoNazvatiovuVarijablu = 1;
            }
        endforeach;

        $get_count1=$db->query("SELECT count(*) as countOdobreno FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_review_status='1') and (KindofDay<>'BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        $get_count2=$db->query("SELECT count(*) as not_redovni FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_status<>'5') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_redovni = $get_count2->fetch();
        $notRedovni = $count_not_redovni['not_redovni'];

        // praznici check

        /***************************************/

        $get_count_praznik=$db->query("SELECT COUNT(*) as not_praznik  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ( corr_status = '83' and KindOfDay = 'BHOLIDAY')  and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_praznik = $get_count_praznik->fetch();

        $notPraznik = $count_not_praznik['not_praznik'];

        /****************************************/

        $get_count3=$db->query("SELECT count(*) as req_sent FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_change_req='1') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_req_sent = $get_count3->fetch();
        $countSent = $count_req_sent['req_sent'];

        $get_count_not_vikend=$db->query("SELECT count(*) as not_vikend FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE weekday not in (6,7) and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$getYear);
        $count_not_vikend = $get_count_not_vikend->fetch();
        $not_vikend = $count_not_vikend['not_vikend'];
        if($notPraznik == 1 and $same_days == 1 ){
            echo '<div style="background-color:#f2dede; color:#a94442;" class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete otkazati praznik.').'</div>';
            return;
        }
        if($not_vikend==0 and $kakoNazvatiovuVarijablu == 0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete izvršiti otkazivanje redovnog rada.').'</div>';
            return;
        }

        if($countSent>0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Zahtjev za otkazivanje je već poslan!').'</div>';
            return;
        }

        if($notRedovni==0){
            echo '<div class="alert alert-danger text-center"><b>'.__('Upozorenje!').'</b><br/>'.__('Ne možete izvršiti otkazivanje redovnog rada.').'</div>';
            return;
        }

        //otkazivanje sl puta mail
        $send_email = false;
        foreach($fetch_statuses as $key => $v){
            if(in_array($v['corr_status'], array(73))){
                $send_email = true;
                $statuss= 73;
            }
            if(in_array($v['corr_status'], array(81))){
                $send_email = true;
                $statuss= 81;
            }
        }

        if($send_email){mail_cancel_trip($_POST['dateFrom'],$_POST['dateTo'],$statuss);}

        if($countOdobreno>0 ){
            date_default_timezone_set('Europe/Sarajevo');
            $data = "
  
    
  declare @k integer
  set @k = 0
  
  UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
  @k =   case when ([KindofDay] = 'BHOLIDAY' and corr_status != '83' and corr_review_status = '0') then 1
          when ([KindofDay] != 'BHOLIDAY' and corr_status != '5' and corr_review_status = '0') then 1
          else 0 end,
  
  
  corr_status = case when ([KindofDay] = 'BHOLIDAY' and corr_status != '83' and corr_review_status = '0') then 83
          else [corr_status] end,
          
  corr_review_status = case when ([KindofDay] = 'BHOLIDAY' and @k = 1) then '1' 
             when ([KindofDay] <> 'BHOLIDAY' and @k = 1) then '0'
             else [corr_review_status] end,
  
  hour = case when (weekday in (6,7)) then 0
          else hour end,
  
    corr_change_req = case when @k = 0 then '1' else [corr_change_req] end,
    employee_comment = case when @k = 0 then '' else [employee_comment] end,
    timest_edit = '".date('Y-m-d h:i:s')."',
    request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
  
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $getYear
                )
            );

            echo '<div class="alert alert-success text-center"><b>'.__('obavijest :').'</b><br/>'.__('Zahtjev za otkazivanje odobrene registracije poslan!').'</div>';
            return;
        }

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      timest_edit = ?,
      corr_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or status_bh is not null) then 83 else 5 end,
    corr_review_status = case when ([KindOfDay]='BHOLIDAY' or Description<>'' or (status_bh is not null and status_bh != 5)) then 1 else 0 end,
    KindOfDay = case when (status_bh is not null) then 'BHOLIDAY' else KindOfDay end,
    Description = case when (status_bh is not null) then case when (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date]) is not null then (SELECT TOP 1 holiday_name FROM [holidays_per_department] a where a.date = [hourlyrate_day].[Date]) else Description end else Description end,
    employee_comment = ?,
    review_user = null,
    hour = case when [weekday] in (6,7) then 0 else hour end,
    request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(
                date('Y-m-d h:i:s'),
                '',
                $getYear
            )
        );


        // date_default_timezone_set('Europe/Sarajevo');
        //   $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
        //     timest_edit = ?,
        //     corr_status = ?,
        //   hour = case when (weekday in (6,7)) then 0
        //         else [hour] end,
        //   employee_comment = ?,
        //   request_id = case when [request_id] IS NULL or [request_id] = '' then '$request_id_generate' else [request_id] end
        //  where
        //  (
        //  (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
        //  (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
        //  ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
        //  (month_id > ".$month_from." and month_id < ".$month_to.")
        //  )
        //  and year_id=?
        //   ";

        //   $res = $db->prepare($data);
        //   $res->execute(
        //     array(
        //       date('Y-m-d h:i:s'),
        //       '5',
        //   '',
        //   $getYear
        //   )
        //  );


        echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';

    }

    if($_POST['request']=='day-edit' and $_POST['status']!='67'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $_user_to_send = _user(45);

        $this_id = $_POST['request_id'];
        $status = $_POST['status'];

        $request_id_generate = $this_id."".rand(1,100);

        $check = $db->query("SELECT user_id, year_id,month_id,employee_no,Date FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id."' ");
        foreach ($check  as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month=$checkvalue['month_id'];
            $filter_emp=$checkvalue['employee_no'];
            $user_edit = _user($checkvalue['user_id']);
            $FromDay = $checkvalue['Date'];
            $ToDay = $checkvalue['Date'];
        }

        $br_sati = $user_edit['br_sati'];

        $emp=$db->query("SELECT employee_no,year_id,month_id,YEAR(Date) as godina FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id ."'  ");

        foreach($emp as $valueemp)
        {$empid=$valueemp['employee_no'];
            $getYear=$valueemp['year_id'];
            $getMonth=$valueemp['month_id'];$godina = $valueemp['godina']; }

        $get_old_status=$db->query("SELECT status, KindOfDay, review_status, corr_review_status FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id ."'  ");
        $old_status = $get_old_status->fetch();


        $go=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");




        $nex_year = getYearId($getYear, $_user['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $_user['user_id'], 'prev', true);

        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (status='18')) or (year_id = '".$nex_year."' and status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV is null)");


        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (status='19')) or (year_id = '".$pre_year."' and status = '18')) AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (date_NAV is null)");


        /*
    $currgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'
  AND weekday<>'6' AND weekday<>'7' AND (status='18') AND (date_NAV is null)");
  $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."'
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
  */


        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
        $death = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
        $pcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'  
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' and employee_no='".$empid."'
   and status='19'  ");

        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");

        $plo=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");

        $P_1=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_2=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_3=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_4=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_5=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_6=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_7=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");
        $P_1a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='27'");
        $P_2a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='28'");
        $P_3a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='29'");
        $P_4a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='30'");
        $P_5a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='31'");
        $P_6a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='32'");
        $P_7a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='79'");
        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."'
     and weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");


        foreach($go as $valuego) {
            $totalgo = $valuego['Ukupno'];
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $iskoristeno=$valuego['Br_dana_iskoristeno'];
            $brdana=$valuego['Br_dana'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristenokrv=$valuego['P_6_used'];
            $totalkrv = $valuego['Blood_days'];
            $propaloGO = $valuego['G_2 not valid'];
        }

        foreach($currgo as $valuecurrgo) {
            $iskoristenocurr=$valuecurrgo['sum_hour'];;
            $iskoristenototal=($iskoristenocurr/$br_sati)+$iskoristeno ;
            $totalgoost=$brdana-$iskoristenototal;}

        foreach($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG=$valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG=($iskoristenocurrPG/$br_sati)+$iskoristenoPG ;
            $totalgoostPG=$brdanaPG-$iskoristenototalPG;
            $ukupnogoiskoristeno=$iskoristenototalPG+$iskoristenototal;
            $ukupnogoost=$totalgoost+$totalgoostPG;}

        foreach($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm=$valuepcm['Candelmas_paid'];}

        foreach($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm=$valueupcm['Candelmas_unpaid'];}

        foreach($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm=$valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm=($iskoristenocurrpcm/$br_sati)+$iskoristenopcm ;
            $totalpcmost=$brdanapcm-$iskoristenototalpcm;
        }

        foreach($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm=$valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm=($iskoristenocurrupcm/$br_sati)+$iskoristenoupcm ;
            $totalupcmost=$brdanaupcm-$iskoristenototalupcm;
        }


        foreach($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }

        foreach($curruP_1  as $valuecurrP_1) {
            $iskoristenocurrP_1=$valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1=($iskoristenocurrP_1/$br_sati)+$iskoristenoP_1 ;
            $totalP_1ost=$totalP_1-$iskoristenototalP_1;
        }

        foreach($curruP_2  as $valuecurrP_2) {
            $iskoristenocurrP_2=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2=($iskoristenocurrP_2/$br_sati)+$iskoristenoP_2 ;
            $totalP_2ost=$totalP_2-$iskoristenototalP_2;
        }
        foreach($curruP_3  as $valuecurrP_3) {
            $iskoristenocurrP_3=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3=($iskoristenocurrP_3/$br_sati)+$iskoristenoP_3 ;
            $totalP_3ost=$totalP_3-$iskoristenototalP_3;
        }
        foreach($curruP_4  as $valuecurrP_4) {
            $iskoristenocurrP_4=$valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4=($iskoristenocurrP_4/$br_sati)+$iskoristenoP_4 ;
            $totalP_4ost=$totalP_4-$iskoristenototalP_4;
        }
        foreach($curruP_5  as $valuecurrP_5) {
            $iskoristenocurrP_5=$valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5=($iskoristenocurrP_5/$br_sati)+$iskoristenoP_5 ;
            $totalP_5ost=$totalP_5-$iskoristenototalP_5;
        }
        foreach($curruP_6  as $valuecurrP_6) {
            $iskoristenocurrP_6=$valuecurrP_6['sum_hour'];
            $iskoristenototalP_6=($iskoristenocurrP_6/$br_sati)+$iskoristenoP_6 ;
            $totalP_6ost=$totalP_6-$iskoristenototalP_6;
        }
        foreach($curruP_7  as $valuecurrP_7) {
            $iskoristenocurrP_7=$valuecurrP_7['sum_hour'];
            $iskoristenototalP_7=($iskoristenocurrP_7/$br_sati)+$iskoristenoP_7 ;
            $totalP_7ost=$totalP_7-$iskoristenototalP_7;
        }

        foreach($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used']+$valueplo['P_2_used']+$valueplo['P_3_used']+$valueplo['P_4_used']+$valueplo['P_5_used']+$valueplo['P_6_used']+$valueplo['P_7_used'];
            $totalplo =$valueplo['Br_dana_PLO'];
        }
        foreach($currplo as $valuecurrplo) {
            $iskoristenocurrplo=$valuecurrplo['sum_hour'];
            $iskoristenototalplo=($iskoristenocurrplo/8)+$iskoristenoplo ;
            $totalploost=$totalplo-$iskoristenototalplo;
        }

        //VJERSKI PRAZNICI
        if($_POST['status']=='84'){

            if($iskoristenototalpcm<2)
                $status = '21';
            elseif($iskoristenototalpcm>=2 and ($iskoristenototalpcm+$iskoristenototalupcm)<4)
                $status = '22';
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika !').'</div>';
                return;
            }
        }

        //GO
        if($_POST['status']=='106'){

            if(($totalgoostPG-1>=0) and $filter_month<=6)
                $status = '19';
            elseif($totalgoost-1>=0)
                $status = '18';
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                return;
            }
        }

        //BOLOVANJE
        if($_POST['status']=='67'){

            $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee] WHERE [No_]='".$_user['employee_no']."'");
            if($get->rowCount()<0)
                $row_employee = $get->fetch();
            $entitet = $row_employee['Org Entity Code'];

            $max_do = 42;
            if($entitet=='FBIH'){
                $max_do = 42;
                $bolovanje_do = 43;
                $bolovanje_preko = 44;
            }
            elseif($entitet=='RS'){
                $max_do = 30;
                $bolovanje_do = 107;
                $bolovanje_preko = 108;
            }
            elseif($entitet=='BD'){
                $max_do = 42;
                $bolovanje_do = 43;
                $bolovanje_preko = 44;
            }


            $get_date_id= $db->query("SELECT Date as datum FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where 
    id = ".$this_id);
            $date_id = $get_date_id->fetch();

            $get_date_bolovanje = $db->query("SELECT pocetak_bolovanja as pocetak_bolovanja FROM [c0_intranet2_apoteke].[dbo].[bolovanje] where 
    user_id = ".$_user['user_id']);
            $pocetak_bolovanja = $get_date_bolovanje->fetch();

            $where_period_43 = " and (c.Date between '".$pocetak_bolovanja['pocetak_bolovanja']."' and '".$date_id['datum']."') and h.user_id=".$_user['user_id'];

            if($pocetak_bolovanja['pocetak_bolovanja']=='')
                $countBolovanje = 0;
            else{

                $get_bolovanje43 = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] h
  join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] y
  on h.year_id = y.id
  join [c0_intranet2_apoteke].[dbo].[Calendar] c
  on (c.Year = y.year and c.Day = h.day and c.Month=h.month_id)
  WHERE 
   (((h.status = 43) or (h.status = 107)) or (h.status=5 and (h.KindofDay  in ('SATURDAY','SUNDAY'))) or (h.status = 83 and h.KindofDay ='BHOLIDAY'))".
                    $where_period_43);
//NK
                $countBolovanje = $get_bolovanje43->fetch();


            }

            if($countBolovanje['bolovanje43']<$max_do)
                $status = $bolovanje_do;
            else
                $status = $bolovanje_preko;

        }

        if(($old_status['KindOfDay']=='BHOLIDAY') and !in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108,105))){echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete pregaziti praznik!').'</div>';return;}
        if($old_status['review_status']=='1' and !(($old_status['KindOfDay']=='BHOLIDAY') and in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108))) ){echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete promjeniti odobrenu registraciju!').'</div>';return;}
        if(($totalgoost-1<0)  and $_POST['status']=='18') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !').'</div>';return;}
        if (($totalgoostPG-1<0) and $_POST['status']=='19') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !').'</div>';return;}
        if(($totalpcmost-1<0) and ($_POST['status']=='21')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!').'</div>';return;}
        if(($totalupcmost-1<0) and ($_POST['status']=='22')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!').'</div>';return;}
        if ((($totalP_1ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='27')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_2ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='28')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_3ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='29')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_4ost-1<0) or ($totalploost-1<0)) and  ($_POST['status']=='30')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_5ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='31')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_6ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='32' ) ){ echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 1 dana za darivanje krvi, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_7ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='79' ) ){ echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 2 dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}


        $rev_status = 0;

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      day = ?,
      hour = ?,
    hour_pre = ?,
    timest_edit = ?,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
      status = ?,
    corr_status = ?,
    status_pre = ?,
    corr_pre = ?,
    review_status = ?,
    employee_comment = ?,
    review_comment = ?,
    request_id = '$request_id_generate' 
      WHERE id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['day'],
                $_POST['hour'],
                $_POST['hour_pre'],
                date('Y-m-d h:i:s'),
                date('Y-m-d h:i:s'),
                $_user['employee_no'],
                $status,
                $status,
                $_POST['status_pre'],
                $_POST['status_pre'],
                $rev_status,
                $_POST['komentar'],
                '',
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';


            // Mail notifikacija

            $get_mail_settings = $db->query("SELECT name, value FROM [c0_intranet2_apoteke].[dbo].[settings] WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
            $get_mail_fetch = $get_mail_settings->fetchAll();

            $mail_settings = array();
            foreach($get_mail_fetch as $key => $value){
                $mail_settings[$value['name']] = $value['value'];
            }

            $array_bolovanje = array( "43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27",  "105", "106", "18", "19");

            // Bolovanje i placena odsustva
            if($mail_settings['hr_notifications'] == '1'){
                if(in_array($_POST['status'], $array_bolovanje))
                {
                    // start mail

                    $status_izostanka = $status;

                    $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                    $user_edit = $_user;


                    require '../../lib/PHPMailer/PHPMailer.php';
                    require '../../lib/PHPMailer/SMTP.php';
                    require '../../lib/PHPMailer/Exception.php';
                    require '../../mails.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = "UTF-8";

                    $mail->IsSMTP();
                    $mail->isHTML(true);  // Set email format to HTML

                    $mail->Host = "barbbcom";
                    //$mail->SMTPSecure = 'tls';
                    $mail->Port = 25;


                    $parent_user = _employee($_user['parent']);



                    if(in_array($_POST['status'], array(73))){
                        // sluzbeni put svi

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
                        //$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

                    } else if(in_array($_POST['status'], array(81))){
                        // sluzbeni put EDUKACIJA

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("edukacija.hr@raiffeisengroup.ba");


                    } else if(in_array($_POST['status'], array(106, 18, 19))){
                        // godišnji odmori

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

                    } else {

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress(@$mail_settings['hr_supportt_mail']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
                    }

                    $mail->Subject  = 'Registracija izostanka';
                    //$_user=$user_edit;
                    //$parent_user = _employee($_user['parent']);
                    $mail->Body     = $mails['day-edit'];

                    if(!$mail->send()) {
                        //echo 'Message was not sent.';
                        //echo 'Mailer error: ' . $mail->ErrorInfo;
                    } else {
                        //echo 'Message has been sent.';
                    }
                }
            }


            // kraj mail notifikacije


            //_sendMail($_user,$_user_to_send,$status);
        }

    }

    if($_POST['request']=='day-edit_corrections' and $_POST['status']!=67){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $this_id = $_POST['request_id'];
        $status = $_POST['status'];

        $check = $db->query("SELECT user_id, year_id,month_id,employee_no,Date FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id."' ");
        foreach ($check  as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month=$checkvalue['month_id'];
            $filter_emp=$checkvalue['employee_no'];
            $user_edit = _user($checkvalue['user_id']);
            $FromDay = $checkvalue['Date'];
            $ToDay = $checkvalue['Date'];
        }

        $br_sati = $user_edit['br_sati'];

        $emp=$db->query("SELECT employee_no,year_id,month_id,YEAR(Date) as godina FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id ."'  ");

        foreach($emp as $valueemp)
        {$empid=$valueemp['employee_no'];
            $getYear=$valueemp['year_id'];
            $getMonth=$valueemp['month_id']; $godina = $valueemp['godina'];  }

        $get_old_status=$db->query("SELECT corr_status, corr_review_status, KindOfDay FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='".$this_id ."'  ");
        $old_status = $get_old_status->fetch();

        $go = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."' and year = '$godina'");


        $nex_year = getYearId($getYear, $_user['user_id'], 'next', true);
        $pre_year = getYearId($getYear, $_user['user_id'], 'prev', true);


        $currgo = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (corr_status='18')) or (year_id = '".$nex_year."' and corr_status = '19'))  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV_corrections is null)");

        $currgoPG = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE ((year_id='".$getYear."' AND (corr_status='19')) or (year_id = '".$pre_year."' and corr_status = '18')) AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (date_NAV_corrections is null)");





        $currgo = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='18')");
        $currgoPG = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19')");




        $currgototal = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19' or corr_status='18')");
        $death = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' AND employee_no='".$empid."' 
    AND weekday<>'6' AND weekday<>'7' AND (corr_status='72')");
        $pcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $currpcm = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='21')");
        $upcm=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $currupcm = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='22')");
        $checkva = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' and month_id='".$getMonth."'and employee_no='".$empid."'
   and corr_status='19'  ");

        $currplo = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND ((corr_status='27') or (corr_status='28') or (corr_status='29') or (corr_status='30') or (corr_status='31')   
  or (corr_status='32') or (corr_status='79'))");

        $plo=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");

        $P_1=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_2=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_3=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_4=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_5=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_6=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_7=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'  and year = '$godina'");
        $P_1a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='27'");
        $P_2a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='28'");
        $P_3a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='29'");
        $P_4a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='30'");
        $P_5a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='31'");
        $P_6a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='32'");
        $P_7a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='79'");
        $curruP_1 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='27')");
        $curruP_2 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='28')");
        $curruP_3 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='29')");
        $curruP_4 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='30')");
        $curruP_5 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='31')");
        $curruP_6 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='32')");
        $curruP_7 = $db->query("SELECT sum(".$br_sati.") as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$getYear."' AND month_id='".$getMonth."' 
     and weekday<>'6' AND weekday<>'7' AND (corr_status='79')");


        foreach($go as $valuego) {
            $totalgo = $valuego['Ukupno'];
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $iskoristeno=$valuego['Br_dana_iskoristeno'];
            $brdana=$valuego['Br_dana'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristenokrv=$valuego['P_6_used'];
            $totalkrv = $valuego['Blood_days'];
            $propaloGO = $valuego['G_2 not valid'];
        }

        foreach($currgo as $valuecurrgo) {
            $iskoristenocurr=$valuecurrgo['sum_hour'];;
            $iskoristenototal=($iskoristenocurr/$br_sati) ;
            $totalgoost=$brdana-$iskoristenototal;}

        foreach($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG=$valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG=($iskoristenocurrPG/$br_sati) ;
            $totalgoostPG=$brdanaPG-$iskoristenototalPG;
            $ukupnogoiskoristeno=$iskoristenototalPG+$iskoristenototal;
            $ukupnogoost=$totalgoost+$totalgoostPG;}

        foreach($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm=$valuepcm['Candelmas_paid'];}

        foreach($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm=$valueupcm['Candelmas_unpaid'];}

        foreach($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm=$valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm=($iskoristenocurrpcm/$br_sati) ;
            $totalpcmost=$brdanapcm-$iskoristenototalpcm;
        }

        foreach($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm=$valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm=($iskoristenocurrupcm/$br_sati) ;
            $totalupcmost=$brdanaupcm-$iskoristenototalupcm;
        }


        foreach($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }

        foreach($curruP_1  as $valuecurrP_1) {
            $iskoristenocurrP_1=$valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1=($iskoristenocurrP_1/$br_sati) ;
            $totalP_1ost=$totalP_1-$iskoristenototalP_1;
        }

        foreach($curruP_2  as $valuecurrP_2) {
            $iskoristenocurrP_2=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2=($iskoristenocurrP_2/$br_sati) ;
            $totalP_2ost=$totalP_2-$iskoristenototalP_2;
        }
        foreach($curruP_3  as $valuecurrP_3) {
            $iskoristenocurrP_3=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3=($iskoristenocurrP_3/$br_sati) ;
            $totalP_3ost=$totalP_3-$iskoristenototalP_3;
        }
        foreach($curruP_4  as $valuecurrP_4) {
            $iskoristenocurrP_4=$valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4=($iskoristenocurrP_4/$br_sati) ;
            $totalP_4ost=$totalP_4-$iskoristenototalP_4;
        }
        foreach($curruP_5  as $valuecurrP_5) {
            $iskoristenocurrP_5=$valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5=($iskoristenocurrP_5/$br_sati) ;
            $totalP_5ost=$totalP_5-$iskoristenototalP_5;
        }
        foreach($curruP_6  as $valuecurrP_6) {
            $iskoristenocurrP_6=$valuecurrP_6['sum_hour'];
            $iskoristenototalP_6=($iskoristenocurrP_6/$br_sati) ;
            $totalP_6ost=$totalP_6-$iskoristenototalP_6;
        }
        foreach($curruP_7  as $valuecurrP_7) {
            $iskoristenocurrP_7=$valuecurrP_7['sum_hour'];
            $iskoristenototalP_7=($iskoristenocurrP_7/$br_sati) ;
            $totalP_7ost=$totalP_7-$iskoristenototalP_7;
        }

        foreach($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used']+$valueplo['P_2_used']+$valueplo['P_3_used']+$valueplo['P_4_used']+$valueplo['P_5_used']+$valueplo['P_6_used']+$valueplo['P_7_used'];
            $totalplo =$valueplo['Br_dana_PLO'];
        }
        foreach($currplo as $valuecurrplo) {
            $iskoristenocurrplo=$valuecurrplo['sum_hour'];
            $iskoristenototalplo=($iskoristenocurrplo/$br_sati) ;
            $totalploost=$totalplo-$iskoristenototalplo;
        }

        //VJERSKI PRAZNICI
        if($_POST['status']=='84'){

            if($iskoristenototalpcm<2)
                $status = '21';
            elseif($iskoristenototalpcm>=2 and ($iskoristenototalpcm+$iskoristenototalupcm)<4)
                $status = '22';
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 4 dana vjerskih praznika !').'</div>';
                return;
            }
        }

        //GO
        if($_POST['status']=='106'){

            if(($totalgoostPG-1>=0) and $filter_month<=6)
                $status = '19';
            elseif($totalgoost-1>=0)
                $status = '18';
            else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO!').'</div>';
                return;
            }
        }

        //BOLOVANJE
        if($_POST['status']=='67'){

            $get_date_id= $db->query("SELECT Date as datum FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where 
    id = ".$this_id);
            $date_id = $get_date_id->fetch();

            $get_date_bolovanje = $db->query("SELECT pocetak_bolovanja as pocetak_bolovanja FROM [c0_intranet2_apoteke].[dbo].[bolovanje] where 
    user_id = ".$_user['user_id']);
            $pocetak_bolovanja = $get_date_bolovanje->fetch();

            if($pocetak_bolovanja['pocetak_bolovanja']=='')
                $countBolovanje = 0;
            else{

                $where_period_43 = " and (c.Date between '".$pocetak_bolovanja['pocetak_bolovanja']."' and '".$date_id['datum']."') and h.user_id=".$_user['user_id'];


                $get_bolovanje43 = $db->query("SELECT count(*) as bolovanje43 FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] h
  join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] y
  on h.year_id = y.id
  join [c0_intranet2_apoteke].[dbo].[Calendar] c
  on (c.Year = y.year and c.Day = h.day and c.Month=h.month_id)
  WHERE 
   (((h.corr_status = 43) or (h.corr_status = 107)) or (h.corr_status=5 and (h.KindofDay  in ('SATURDAY','SUNDAY'))) or (h.corr_status = 83 and h.KindofDay ='BHOLIDAY'))".
                    $where_period_43);
//NK
                $countBolovanje = $get_bolovanje43->fetch();



            }

            if($countBolovanje['bolovanje43']<42)
                $status = 43;
            else
                $status = 44;

        }

        if(($old_status['KindOfDay']=='BHOLIDAY') and !in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108, 105))){echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete pregaziti praznik!').'</div>';return;}
        if($old_status['corr_review_status']=='1' and !(($old_status['KindOfDay']=='BHOLIDAY') and in_array($_POST['status'], array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108))) ){echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete promjeniti odobrenu registraciju!').'</div>';return;}
        if(($totalgoost-1<0)  and $_POST['status']=='18') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !').'</div>';return;}
        if (($totalgoostPG-1<0) and $_POST['status']=='19') { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !').'</div>';return;}
        if(($totalpcmost-1<0) and ($_POST['status']=='21')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!').'</div>';return;}
        if(($totalupcmost-1<0) and ($_POST['status']=='22')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!').'</div>';return;}
        if ((($totalP_1ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='27')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_2ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='28')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_3ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='29')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_4ost-1<0) or ($totalploost-1<0)) and  ($_POST['status']=='30')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_5ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='31')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_6ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='32' ) ){ echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 1 dana za darivanje krvi, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
        if ((($totalP_7ost-1<0) or ($totalploost-1<0)) and ($_POST['status']=='79' ) ){ echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od 2 dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}

        if($_POST['hour']<>8)
            $rev_status = 1;
        else
            $rev_status = 0;

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      day = ?,
      hour = ?,
    hour_pre = ?,
    timest_edit_corr = ?,
    employee_timest_edit = ?,
      corr_status = ?,
    corr_pre = ?,
    employee_comment = ?,
    review_comment = ?
      WHERE id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['day'],
                $_POST['hour'],
                $_POST['hour_pre'],
                date('Y-m-d h:i:s'),
                $_user['employee_no'],
                $status,
                $_POST['status_pre'],
                $_POST['komentar'],
                '',
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';



            // Mail notifikacija

            $get_mail_settings = $db->query("SELECT name, value FROM [c0_intranet2_apoteke].[dbo].[settings] WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
            $get_mail_fetch = $get_mail_settings->fetchAll();

            $mail_settings = array();
            foreach($get_mail_fetch as $key => $value){
                $mail_settings[$value['name']] = $value['value'];
            }

            $array_bolovanje = array( "43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27",  "105", "106", "18", "19");

            // Bolovanje i placena odsustva
            if($mail_settings['hr_notifications'] == '1'){
                if(in_array($_POST['status'], $array_bolovanje))
                {
                    // start mail

                    $status_izostanka = $status;

                    $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                    $user_edit = $_user;

                    require '../../lib/PHPMailer/PHPMailer.php';
                    require '../../lib/PHPMailer/SMTP.php';
                    require '../../lib/PHPMailer/Exception.php';
                    require '../../mails.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = "UTF-8";

                    $mail->IsSMTP();
                    $mail->isHTML(true);  // Set email format to HTML

                    $mail->Host = "barbbcom";
                    //$mail->SMTPSecure = 'tls';
                    $mail->Port = 25;



                    $parent_user = _employee($_user['parent']);



                    if(in_array($_POST['status'], array(73))){
                        // sluzbeni put svi

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("sluzbeni-put-rbbh@raiffeisengroup.ba");
                        //$mail->addAddress("raiffeisen_assistance@raiffeisengroup.ba");

                    } else if(in_array($_POST['status'], array(81))){
                        // sluzbeni put EDUKACIJA

                        $mail->setFrom($_user['email_company'], "Obavijesti HR");

                        $mail->addAddress("edukacija.hr@raiffeisengroup.ba");


                    } else if(in_array($_POST['status'], array(106, 18, 19))){
                        // godišnji odmori

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail

                    } else {

                        $mail->setFrom($_user['email_company'], $_user['fname'] . ' ' . $_user['lname']);

                        $mail->addAddress($_user['email_company']);
                        $mail->addAddress(@$mail_settings['hr_supportt_mail']);
                        $mail->addAddress($parent_user['email_company']); // todo nadredjeni mail
                    }

                    $mail->Subject  = 'Registracija izostanka';
                    //$_user=$user_edit;


                    //$parent_user = _employee($_user['parent']);
                    $mail->Body     = $mails['day-edit'];

                    if(!$mail->send()) {
                        //echo 'Message was not sent.';
                        //echo 'Mailer error: ' . $mail->ErrorInfo;
                    } else {
                        //echo 'Message has been sent.';
                    }
                }
            }


            // kraj mail notifikacije




        }

    }

    ///ODOBRAVANJE

    function AcceptSveRadniciNextYear($date_od, $date_do, $yearChosen = '0', $korekcije = '0'){
        global $db;
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        if($korekcije == '0')
        {
            $column_kor = "review_status";
        } else if($korekcije == '1'){
            $column_kor = "corr_review_status";
        }
        // date_od pieces

        $date_od_explode   = strtotime(str_replace(".", "-", $date_od));
        $date_od_day     = date("j",$date_od_explode);
        $date_od_month   = date("n",$date_od_explode);
        $date_od_year    = date("Y",$date_od_explode);

        // date_do pieces

        $date_do_explode   = strtotime(str_replace(".", "-", $date_do));
        $date_do_day     = date("j",$date_do_explode);
        $date_do_month   = date("n",$date_do_explode);
        $date_do_year    = date("Y",$date_do_explode);

        if($yearChosen == '0'){
            $yearChose = $date_od_year;
        } else {
            $yearChose = $date_do_year;
        }



        if($_user['role']==4){
            $get2 = $db->query("SELECT DISTINCT [c0_intranet2_apoteke].[dbo].[users].user_id, s1.id as yearid FROM [c0_intranet2_apoteke].[dbo].[users] JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s1 ON s1.user_id = [c0_intranet2_apoteke].[dbo].[users].user_id and s1.year = '$yearChose' WHERE ".$_user['employee_no']." in ([c0_intranet2_apoteke].[dbo].[users].admin1,[c0_intranet2_apoteke].[dbo].[users].admin2,[c0_intranet2_apoteke].[dbo].[users].admin3,[c0_intranet2_apoteke].[dbo].[users].admin4,[c0_intranet2_apoteke].[dbo].[users].admin5)  ");
            $result = $get2->fetchAll();
            $total_users=$result;
        }
        elseif($_user['role']==2){
            $get2 = $db->query("SELECT DISTINCT [c0_intranet2_apoteke].[dbo].[users].user_id, s1.id as yearid FROM [c0_intranet2_apoteke].[dbo].[users] JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s1 ON s1.user_id = [c0_intranet2_apoteke].[dbo].[users].user_id and s1.year = '$yearChose' WHERE ([c0_intranet2_apoteke].[dbo].[users].parent='".$_user['employee_no']."')");
            $result = $get2->fetchAll();
            $total_users=$result;
        }


        $years_id_from   = array();
        $years_id_to   = array();

        $arr = "(";
        foreach($total_users as $key => $value){

            $years_id_from[$value['user_id']] = $value['yearid'];
            $years_id_to[$value['user_id']] = $value['yearid'];

            $arr .= $value['user_id']. ",";
        }
        $arr = rtrim($arr, ",");
        $arr .= ")";


        $get_days = $db->query("SELECT [c0_intranet2_apoteke].[dbo].[hourlyrate_day].id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
            
            INNER JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_year] s1 ON [c0_intranet2_apoteke].[dbo].[hourlyrate_day].user_id = s1.user_id and s1.year = '$yearChose'
            WHERE
              [c0_intranet2_apoteke].[dbo].[hourlyrate_day].user_id IN $arr and
              [c0_intranet2_apoteke].[dbo].[hourlyrate_day].$column_kor = 0 
              and [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = s1.id and  
              
              [c0_intranet2_apoteke].[dbo].[hourlyrate_day].status NOT IN (5,43,44,45,61,62,65,67,68,69,72, 74,75,76,77,78,79,80,105,107,108) and
              
        (
         (day >= ".$date_od_day." and month_id = ".$date_od_month." and (".$date_od_month." <> ".$date_do_month."))  OR
         (day <= ".$date_do_day." and month_id = ".$date_do_month." and (".$date_do_month." <> ".$date_od_month.")) OR
         ((day >= ".$date_od_day." and day <= ".$date_do_day.") and month_id = ".$date_od_month." and month_id = ".$date_do_month.") OR
         (month_id > ".$date_od_month." and month_id < ".$date_do_month.")
         )
         
         
         
         
    ");
        $result = $get_days->fetchAll();


        foreach($result as $key => $value){
            $this_id = $value['id'];

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
        [".$column_kor."] = ?,
        review_user = ?
        where 
         (
         (day >= ".$date_od_day." and month_id = ".$date_od_month." and (".$date_od_month." <> ".$date_do_month."))  OR
         (day <= ".$date_do_day." and month_id = ".$date_do_month." and (".$date_do_month." <> ".$date_od_month.")) OR
         ((day >= ".$date_od_day." and day <= ".$date_do_day.") and month_id = ".$date_od_month." and month_id = ".$date_do_month.") OR
         (month_id > ".$date_od_month." and month_id < ".$date_do_month.")
         )
         and id = ?
         and [".$column_kor."] = 0
        ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    "1",
                    $_user['user_id'],
                    $this_id
                )
            );

        }

    }
    if($_POST['request']=='accept-sve-radnici'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date_od = $_POST['date_od'];
        $date_do = $_POST['date_do'];

        acceptSveRadniciNextYear($date_od, $date_do, '0');
        acceptSveRadniciNextYear($date_do, $date_do, '1');

        echo "finished";
    }

    if($_POST['request']=='accept-sve-radnici-corrections'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date_od = $_POST['date_od'];
        $date_do = $_POST['date_do'];

        acceptSveRadniciNextYear($date_od, $date_do, '0', '1');
        acceptSveRadniciNextYear($date_do, $date_do, '1', '1');

        echo "finished";
    }


    if($_POST['request']=='accept-them-zahtjevi'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $is_correction = $_POST['is_correction'];

        if(!empty($_POST['zahtjevi'])){

            if($_user['role'] == "4" or $_user['role'] == "2"):

                $zahtjevi = $_POST['zahtjevi'];

                foreach($zahtjevi as $key => $value):
                    $exp = explode("$#", $value);

                    $start_id = $exp[0];
                    $end_id   = $exp[1];
                    $comment  = $exp[2];

                    $add_corr = "";
                    if($is_correction == "0"){
                        $rev_status = "review_status";
                        $add_corr = "corr_review_status = '1', ";
                    } else {
                        $rev_status = "corr_review_status";
                    }



                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
                $rev_status = ?, $add_corr
                review_comment = ?,
                review_user = ?
                where 
                id >= '$start_id' and id <= '$end_id'
              ";


                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            '1',
                            $comment,
                            $_user['user_id'],
                        )
                    );

                endforeach;

            else:
                echo "no user role";
            endif;
        } else {
            echo "no zahtjevi";
        }


        echo "finished";
    }

    if($_POST['request']=='change-odobreno'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['odobreno_id']);
        $day_from= $pieces[1];
        $month_from= $pieces[2];
        $day_to= $pieces[3];
        $month_to= $pieces[4];
        $year_id =  $pieces[5];

        $get_count=$db->query("SELECT count(*) as countReq FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (change_req='1') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$year_id);
        $countReq1 = $get_count->fetch();
        $countReq = $countReq1['countReq'];

        if($countReq>0)
            $change_req = '2';
        else
            $change_req = '';

        $emp=$db->query("SELECT user_id, employee_no FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id=".$year_id);
        foreach($emp as $valueemp)
        {$empid=$valueemp['employee_no'];
            $user_edit = _user($valueemp['user_id']);}

        $br_sati = $user_edit['br_sati'];

        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]  where
     weekday<>'6' AND weekday<>'7' and KindOfDay<>'BHOLIDAY' and
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$year_id);

        foreach($askedgo as $valueasked) {
            $askeddays=$valueasked['sum_hour'];
            $totalasked=$askeddays/$br_sati;}

        $plo=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $go=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND employee_no='".$empid."' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");

        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id='".$year_id."' AND weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");
        $P_1=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_2=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_3=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_4=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_5=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_6=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_7=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[vacation_statistics] WHERE employee_no='".$empid."'");
        $P_1a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='27'");
        $P_2a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='28'");
        $P_3a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='29'");
        $P_4a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='30'");
        $P_5a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='31'");
        $P_6a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='32'");
        $P_7a=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id='79'");

        foreach($go as $valuego) {
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristeno=$valuego['Br_dana_iskoristeno'];
            $ostalo = $valuego['Br_dana_ostalo'];
            $brdana=$valuego['Br_dana'];
            $totalkrv=$valuego['Blood_days'];
            $totaldeath=$valuego['S_1_used'];
            $iskoristenokrv=$valuego['P_6_used'];
            $propaloGO=$valuego['G_2 not valid'];
        }

        foreach($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }

        foreach($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }

        foreach($curruP_1  as $valuecurrP_1) {
            $iskoristenocurrP_1=$valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1=($iskoristenocurrP_1/$br_sati)+$iskoristenoP_1 ;
            $totalP_1ost=$totalP_1-$iskoristenototalP_1;
        }
        foreach($curruP_2  as $valuecurrP_2) {
            $iskoristenocurrP_2=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2=($iskoristenocurrP_2/$br_sati)+$iskoristenoP_2 ;
            $totalP_2ost=$totalP_2-$iskoristenototalP_2;
        }
        foreach($curruP_3  as $valuecurrP_3) {
            $iskoristenocurrP_3=$valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3=($iskoristenocurrP_3/$br_sati)+$iskoristenoP_3 ;
            $totalP_3ost=$totalP_3-$iskoristenototalP_3;
        }
        foreach($curruP_4  as $valuecurrP_4) {
            $iskoristenocurrP_4=$valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4=($iskoristenocurrP_4/$br_sati)+$iskoristenoP_4 ;
            $totalP_4ost=$totalP_4-$iskoristenototalP_4;
        }
        foreach($curruP_5  as $valuecurrP_5) {
            $iskoristenocurrP_5=$valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5=($iskoristenocurrP_5/$br_sati)+$iskoristenoP_5 ;
            $totalP_5ost=$totalP_5-$iskoristenototalP_5;
        }
        foreach($curruP_6  as $valuecurrP_6) {
            $iskoristenocurrP_6=$valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6=($iskoristenocurrP_6/$br_sati)+$iskoristenoP_6 ;
            $totalP_6ost=$totalP_6-$iskoristenototalP_6;
        }
        foreach($curruP_7  as $valuecurrP_7) {
            $iskoristenocurrP_7=$valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7=($iskoristenocurrP_7/$br_sati)+$iskoristenoP_7 ;
            $totalP_7ost=$totalP_7-$iskoristenototalP_7;
        }
        foreach($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used']+$valueplo['P_2_used']+$valueplo['P_3_used']+$valueplo['P_4_used']+$valueplo['P_5_used']+$valueplo['P_6_used']+$valueplo['P_7_used'];
            $totalplo =$valueplo['Br_dana_PLO'];
        }

        foreach($currplo as $valuecurrplo) {
            $iskoristenocurrplo=$valuecurrplo['sum_hour'];
            $iskoristenototalplo=($iskoristenocurrplo/8)+$iskoristenoplo ;
            $totalploost=$totalplo-$iskoristenototalplo;
        }

        $get_count1=$db->query("SELECT count(*) as countNotHoliday FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (KindOfDay<>'BHOLIDAY') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$year_id);
        $get_countNotHoliday = $get_count1->fetch();
        $countNotHoliday = $get_countNotHoliday['countNotHoliday'];

        if($countNotHoliday==0){
            $status_back = '83';
            $review_status = '1';
        }
        else{
            $status_back = '5';
            $review_status = '0';
        }

        //odbijanje registracije ili odobravanje otkazivanja
        if($_POST['odobreno']=='2'){

            if(isset($_POST['data_otkazivanje'])){
                if($_POST['data_otkazivanje'] == "1"){

                    $employee_no_post     = $_POST['employee_no'];


                    $get_userid = $db->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$employee_no_post."'");
                    $fetchuser  = $get_userid->fetchAll();
                    $fetched_user_id = $fetchuser[0]['user_id'];


                    $data1 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[canceled_requests] 
           (user_id, day_from, month_from, year_id_from, vrsta_odsustva, is_correction, employee_no, reviewed_user, comment_reviewer, day_to, month_to, year_id_to)
           VALUES
           (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ";

                    $res1 = $db->prepare($data1);
                    $res1->execute(
                        array(
                            $fetched_user_id,
                            $day_from,
                            $month_from,
                            $year_id,
                            $_POST['status'],
                            0,
                            $employee_no_post,
                            $_user['employee_no'],
                            $_POST['komentar'],
                            $day_to,
                            $month_to,
                            $year_id
                        )
                    );
                }
            }



            /* Odbijanje, Mail Notifikacija **/
            $get_status = $db->prepare("SELECT status, review_comment FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE  (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$year_id);
            $get_status->execute();
            $get_status1 = $get_status->fetch();


            $get_year = $db->prepare("SELECT  year FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id = '$year_id'");
            $get_year->execute();
            $get_yearf = $get_year->fetch();

            $start_date = $day_from . "." . $month_from . "." . $get_yearf['year'];
            $end_date = $day_to . "." . $month_to . "." . $get_yearf['year'];



            $to          = array("hrpodrska@raiffeisengroup.ba");
            $from        = array("hr.notifikacije@raiffeisengroup.ba");
            $type        = "odbijeno";
            $_user       = $_user;
            $_parent     = $user_edit;
            $data        = array(
                "start_date" => $start_date,
                "end_date" => $end_date,
                "vrsta_odsustva" => $get_status1['status'],
                "komentar" => $get_status1['review_comment']
            );

            MailNotifications($to, $from, $type, $_user, $_parent, $data);
            /* Odbijanje, Mail Notifikacija **/


            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
     review_status = case when [KindOfDay]='BHOLIDAY' then 1 else 0 end,
    review_comment = ?,
    status  = case when [KindOfDay]='BHOLIDAY' then 83 else 5 end,
    corr_status  = case when [KindOfDay]='BHOLIDAY' then 83 else 5 end,
    change_req = ?,
    status_rejected = ?,
    dokument = ?,
    hour = case when [weekday] in (6,7) then 0 else ".$br_sati." end,
    review_user = NULL
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['komentar'],
                    $change_req,
                    $_POST['status'],
                    NULL,
                    $year_id
                )
            );
        }
        elseif($_POST['odobreno']=='0'){
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
   change_req = ?
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['odobreno'],
                    $year_id
                )
            );
        }
        elseif($_POST['status']!=106 and $_POST['status']!=84 and $_POST['status']!=73 and $_POST['status']!=67) // odobravanje u nastavku
        {

            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
    status = ?,
    corr_status = ?,
    review_status = ?,
    corr_review_status = ?,
    review_comment = ?,
    review_user = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['status'],
                    $_POST['status'],
                    $_POST['odobreno'],
                    1,
                    $_POST['komentar'],
                    $_user['user_id'],
                    $year_id
                )
            );
        }
        else{
            {
                if ((($totalasked>$totalP_1ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='27')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_2ost ) or ($totalasked>$totalploost)) and ($_POST['status']=='28')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_3ost) or ($totalasked>$totalploost)) and ($_POST['status']=='29')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_4ost) or ($totalasked>$totalploost)) and ($_POST['status']=='30')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_5ost) or ($totalasked>$totalploost)) and ($_POST['status']=='31')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_6ost) or ($totalasked>$totalploost)) and ($_POST['status']=='32')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                if ((($totalasked>$totalP_7ost) or ($totalasked>$totalploost)) and ($_POST['status']=='79')) { echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!').'</div>';return;}
                date_default_timezone_set('Europe/Sarajevo');
                $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
    review_status = ?,
    corr_review_status = ?,
    review_comment = ?,
    review_user = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['odobreno'],
                        1,
                        $_POST['komentar'],
                        $_user['user_id'],
                        $year_id
                    )
                );
            }
        }
        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }


    //ODOBRAVANJE KOREKCIJE
    if($_POST['request']=='change-odobreno_corrections'){
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['odobreno_id']);
        $day_from= $pieces[1];
        $month_from= $pieces[2];
        $day_to= $pieces[3];
        $month_to= $pieces[4];
        $year_id =  $pieces[5];

        $get_count=$db->query("SELECT count(*) as countReq FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE (corr_change_req='1') and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=".$year_id);
        $countReq1 = $get_count->fetch();
        $countReq = $countReq1['countReq'];

        if($countReq>0)
            $change_req = '2';
        else
            $change_req = '';

        //odbijanje registracije ili odobravanje otkazivanja

        if($_POST['odobreno']=='2'){
            if(isset($_POST['data_otkazivanje'])){
                if($_POST['data_otkazivanje'] == "1"){

                    $employee_no_post     = $_POST['employee_no'];


                    $get_userid = $db->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$employee_no_post."'");
                    $fetchuser  = $get_userid->fetchAll();
                    $fetched_user_id = $fetchuser[0]['user_id'];


                    $data1 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[canceled_requests] 
           (user_id, day_from, month_from, year_id_from, vrsta_odsustva, is_correction, employee_no, reviewed_user, comment_reviewer, day_to, month_to, year_id_to)
           VALUES
           (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ";

                    $res1 = $db->prepare($data1);
                    $res1->execute(
                        array(
                            $fetched_user_id,
                            $day_from,
                            $month_from,
                            $year_id,
                            $_POST['status'],
                            1,
                            $employee_no_post,
                            $_user['employee_no'],
                            $_POST['komentar'],
                            $day_to,
                            $month_to,
                            $year_id
                        )
                    );
                }
            }



            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      corr_review_status = ?,
    review_comment = ?,
    corr_status  = ?,
    corr_change_req = ?,
    status_rejected = ?
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    '0',
                    $_POST['komentar'],
                    '5',
                    $change_req,
                    $_POST['status'],
                    $year_id
                )
            );

            //moj kod za mailove odbijanja
            $yearq=$db->query("select * from [c0_intranet2_apoteke].[dbo].[hourlyrate_year] where id = $year_id");
            $yearq=$yearq->fetch();

            $get_statuses = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id = $year_id and Date between '".$yearq['year'].'-'.$month_from.'-'.$day_from."' and '".$yearq['year'].'-'.$month_to.'-'.$day_to."'  ");
            $fetch_statuses = $get_statuses->fetchAll();

            //otkazivanje sl puta mail
            $send_email = false;
            foreach($fetch_statuses as $key => $v){
                if(in_array($v['status_rejected'], array(73))){
                    $send_email = true;
                    $statuss= 73;
                }
                if(in_array($v['status_rejected'], array(81))){
                    $send_email = true;
                    $statuss= 81;
                }
            }

            if($send_email){mail_cancel_trip( $day_from.'.'.$month_from.'.'.$yearq['year'] ,$day_to.'.'.$month_to.'.'.$yearq['year'],$statuss, $year_id );}
            //
        }
        elseif($_POST['odobreno']=='0'){
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
   corr_change_req = ?
   where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['odobreno'],
                    $year_id
                )
            );
        }
        elseif($_POST['status']!=106 and $_POST['status']!=84 and $_POST['status']!=73) // odobravanje u nastavku
        {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
    corr_status = ?,
    corr_review_status = ?,
    review_comment = ?,
    review_user = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['status'],
                    $_POST['odobreno'],
                    $_POST['komentar'],
                    $_user['user_id'],
                    $year_id
                )
            );
        }
        else{
            {
                date_default_timezone_set('Europe/Sarajevo');
                $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
    corr_review_status = ?,
    review_comment = ?,
    review_user = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['odobreno'],
                        $_POST['komentar'],
                        $_user['user_id'],
                        $year_id
                    )
                );
            }
        }
        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }




    if($_POST['request']=='change-disease_code-odsustva'){
        if(checkifAdmin() == true){


            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $pieces = explode("-", $_POST['disease_code_id']);
            $day_from= $pieces[1];
            $month_from= $pieces[2];
            $day_to= $pieces[3];
            $month_to= $pieces[4];
            $year_id =  $pieces[5];



            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      disease_code = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(

                    $_POST['disease_code'],
                    $year_id
                )
            );

            if($res->rowCount()==1) {
                echo date("Y/m/d");

            } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
        }
    }

    if($_POST['request']=='change-komentar-odsustva'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['komentar_id']);
        $day_from= $pieces[1];
        $month_from= $pieces[2];
        $day_to= $pieces[3];
        $month_to= $pieces[4];
        $year_id =  $pieces[5];



        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      review_comment = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(

                $_POST['komentar'],
                $year_id
            )
        );

        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-dokument'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['dokument_id']);
        $day_from= $pieces[1];
        $month_from= $pieces[2];
        $day_to= $pieces[3];
        $month_to= $pieces[4];
        $year_id =  $pieces[5];

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] SET
      dokument = ?
    where 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(

                $_POST['dokument'],
                $year_id
            )
        );

        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }


    if($_POST['request']=='remove-requests_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM requests WHERE request_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }



    if($_POST['request']=='remove-day_remove'){
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM hourlyrate_day WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if($delete){
            echo 1;
        }
    }


    if($_POST['request']=='remove-requests_archive'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[requests] SET
        is_archive = ?
        WHERE request_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }


    if($_POST['request']=='remove-tasks_archive'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
        is_archive = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }


    if($_POST['request']=='accept-tasks'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
        is_accepted = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }


    if($_POST['request']=='completed-tasks'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
        is_finished = ?,
        date_finished = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d'),
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }




    if($_POST['request']=='task-comment'){

        $data3 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[comments] (
      type,user_id,comment,date_created,comment_on) VALUES (?,?,?,?,?)";

        $res3 = $db->prepare($data3);
        $res3->execute(
            array(
                'task',
                $_POST['user_id'],
                $_POST['comment'],
                date('Y-m-d H:i:s'),
                $_POST['comment_on']
            )
        );
        if($res3->rowCount()==1) {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
        }

    }


    if($_POST['request']=='proc-tasks'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        status = ?,
        date_completed = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                date('y-m-d H:i:s', strtotime("now")),
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }


    if($_POST['request']=='count-tasks'){

        $this_id = $_POST['request_id'];
        $total_0 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id'")->rowCount();
        $total_1 = $db->query("SELECT Count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id' AND status='1'")->rowCount();

        if($total_1==$total_0){
            echo 'yes';
        }else{
            echo 'no';
        }

    }


    if($_POST['request']=='comments'){

        $user_id = $_POST['user'];
        $parent_id = $_POST['parent'];

        $comments = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[comments] WHERE comment_on='".$_POST['request_id']."' AND type='task'");
        $comments_no= $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[comments] WHERE comment_on='".$_POST['request_id']."' AND type='task'");

        if($comments_no->rowCount()<0){

            $user = _user($user_id);
            $parent = _user($parent_id);

            foreach($comments as $item){
                $parent = _user($item['user_id']);
                echo '<div class="comment">';
                if($item['user_id']==$user_id){
                    echo '<div class="row">';
                    echo '<div class="col-xs-9"><div class="text-u">';
                    echo $item['comment'];
                    echo '</div><small class="text-muted">'.date('d/m/Y', strtotime($item['date_created'])).'</small></div>';
                    echo '<div class="col-xs-3 text-center">';
                    if($user['image'] != 'none'){
                        echo '<img src="'.$_timthumb.$_uploadUrl.'/'.$user['image'].'&w=200&h=200" class="img-circle" style="width:70%;">';
                    }else{
                        echo '<img src="'.$_themeUrl.'/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>'.$user['fname'].' '.$user['lname'].'</small>';
                    echo '</div>';
                    echo '</div>';
                }else {
                    echo '<div class="row">';
                    echo '<div class="col-xs-3 text-center">';
                    if($parent['image'] != 'none'){
                        echo '<img src="'.$_timthumb.$_uploadUrl.'/'.$parent['image'].'&w=200&h=200" class="img-circle" style="width:70%;">';
                    }else{
                        echo '<img src="'.$_themeUrl.'/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>'.$parent['fname'].' '.$parent['lname'].'</small>';
                    echo '</div>';
                    echo '<div class="col-xs-9"><div class="text-p">';
                    echo $item['comment'];
                    echo '</div><small class="pull-right text-muted">'.date('d/m/Y', strtotime($item['date_created'])).'</small></div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }

    }

    if($_POST['request']=='change-obuka-status'){

        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id= $pieces[1];
        $faza= $pieces[2];



        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      status = ?
    WHERE user_id = ?
    AND item_id = ?
    AND obrazac_type = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                $user_id,
                $item_id,
                $faza


            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obuka-ocjena3'){

        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id= $pieces[1];



        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      ocjena3 = ?
    WHERE user_id = ?
    AND item_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $user_id,
                $item_id


            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obuka-ocjena6'){

        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id= $pieces[1];



        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      ocjena6 = ?
    WHERE user_id = ?
    AND item_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $user_id,
                $item_id


            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obuka-komentar'){


        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id= $pieces[1];
        $faza= $pieces[2];



        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      komentar = ?
    WHERE user_id = ?
    AND item_id = ?
    AND obrazac_type = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['komentar'],
                $user_id,
                $item_id,
                $faza


            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obuka-coment'){

        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id= $pieces[1];
        $faza= $pieces[2];



        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      komentar_zavrsni = ?
    WHERE user_id = ?
    AND item_id = ?
    AND obrazac_type = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['komentar'],
                $user_id,
                $item_id,
                $faza


            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-obuka-ocjena_mentora'){

        $pieces = explode("-", $_POST['obuka']);
        $item_id= $pieces[0];
        $user_id = $pieces[1];
        $faza= $pieces[2];

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_status] SET
      ocjena_mentora = ?
    WHERE user_id = ?
    AND item_id = ?
    AND obrazac_type = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $user_id,
                $item_id,
                $faza

            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='send-potpisuje_radnik_obuka'){

        $pieces = explode("-", $_POST['request_id']);
        $user_id= $pieces[0];
        $faza= $pieces[1];


        //checks

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[training_program_status] WHERE user_id=".$user_id." and obrazac_type=".$faza." and (status is null or status = 0)" );
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];

        if($status_count>0){
            echo 'nisu_popunjeni_obuka';
            return;
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_header] SET
        potpisao_radnik = ?,
    potpisao_radnik_datum = ?
    WHERE user_id = ?
    AND obrazac_type = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d', strtotime("now")),
                $user_id,
                $faza
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }
    }
    if($_POST['request']=='send-potpisuje_radnik_obuka_eval'){

        $pieces = explode("-", $_POST['request_id']);
        $user_id= $pieces[0];
        $faza= $pieces[1];
        //checks

        $check_ocjene = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[training_program_status] WHERE user_id=".$user_id." and obrazac_type=".$faza." and (ocjena_mentora is null or ocjena_mentora = 0) and item_id<>7" );
        $ocjene_count1 = $check_ocjene->fetch();
        $ocjene_count = $ocjene_count1['broj'];

        if($ocjene_count>0){
            echo 'nisu_popunjeni_obuka';
            return;
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[training_program_header] SET
        potpisao_radnik = ?,
    potpisao_radnik_datum = ?
    WHERE user_id = ?
    AND obrazac_type = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                date('Y-m-d', strtotime("now")),
                $user_id,
                $faza
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }
    }

    if($_POST['request']=='send-radnik_potpisuje_zaduznica'){
        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter="";
        if($sector_type_usr==1)
            $sector_type_filter .= " and OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Zaduznice_status] WHERE user_id=".$user_id.$sector_type_filter." and zaduzen=1 and saglasan = 0" );
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];

        if($status_count>0){
            echo 'nisu_popunjeni_odbij';
            return;
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_header] SET
        radnik_potpisao_zaduznica = ?
    WHERE user_id = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $user_id,

            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }

    if($_POST['request']=='send-radnik_odbija_zaduznica'){

        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter="";
        if($sector_type_usr==1)
            $sector_type_filter .= " and OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Zaduznice_status] WHERE user_id=".$user_id.$sector_type_filter." and zaduzen=1 and saglasan = 0 and zapisnik<>'' and zapisnik is not null" );
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if($status_count==0){
            echo 'nisu_popunjeni_odbij';
            return;
        }

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_header] SET
        potvrda_hr_zaduznica = ?,
    poslano_radniku_zaduznica = ?,
    radnik_odbio_zaduznica = ?
    WHERE user_id = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '0',
                '0',
                '1',
                $user_id,

            )
        );
        if($res->rowCount()==1) {
            //slanje maila adminima
            require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

            $user_to_send = _user($user_id);


            $mail = new PHPMailer(true);
            $mail->CharSet = "UTF-8";


            $mail->isSMTP();
//$mail->Host = '91.235.170.162';
            $mail->Host = gethostbyname('xmail.teneo.ba');                   // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                            // Enable SMTP authentication
            $mail->Username = 'nav@teneo.ba';          // SMTP username
            $mail->Password = 'DynamicsNAV16!'; // SMTP password

            $mail->Port = 587;


            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;                              // TCP port to connect to

            $mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);
            $mail->addAddress('denis.zmukic@infodom.ba');
            // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

            $mail->isHTML(true);  // Set email format to HTML
            $bodyContent = '<table style="width: 1060px;">
<tbody>
<tr style="height: 35px;">
<td style="width: 810px; height: 35px;" colspan="2">
<p>Po&scaron;tovane kolegice i kolege,</p>
</td>
<td style="width: 438px; height: 35px;" colspan="2">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 35px;">
<td style="width: 196px; height: 35px;">
<p>&nbsp;</p>
</td>
<td style="width: 1052px; height: 35px;" colspan="3">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 35px;">
<td style="width: 955px; height: 35px;" colspan="3">
<p>Obavje&scaron;tavamo vas da zaposlenik <strong>'.$user_to_send['fname'].' '.$user_to_send['lname'].'</strong> u Sberbank BH d.d. nije saglasan sa određenom stavkom obrasca Zadužnice.</p>
</td>
<td style="width: 293px; height: 35px;">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 35px;">
<td style="width: 1248px; height: 35px;" colspan="4">
<p>Molimo da pristupite obrascu putem <u>Emplyoee portal-a </u>i provjerite unesena zaduženja, kao i dodatni komentar zaposlenika ispred Va&scaron;e organizacione jedinice &scaron;to je prije moguće.</p>
</td>
</tr>
<tr style="height: 18px;">
<td style="width: 196px; height: 18px;">
<p><strong>&nbsp;</strong></p>
</td>
<td style="width: 1052px; height: 18px;" colspan="3">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 18px;">
<td style="width: 1248px; height: 18px;" colspan="4">
<p>Ukoliko budete imali dodatnih pitanja vezano za proces, kolegice Migić Selma i Klačar Vildana ispred HR-a Vam stoje na raspolaganju za sva dodatna poja&scaron;njenja.</p>
</td>
</tr>
<tr style="height: 20px;">
<td style="width: 196px; height: 20px;">
<p>&nbsp;</p>
</td>
<td style="width: 1052px; height: 20px;" colspan="3">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 35px;">
<td style="width: 196px; height: 35px;">
<p>Srdačan pozdrav,</p>
</td>
<td style="width: 1052px; height: 35px;" colspan="3">
<p>&nbsp;</p>
</td>
</tr>
<tr style="height: 35px;">
<td style="width: 196px; height: 35px;">
<p>HR Tim potpis</p>
</td>
<td style="width: 1052px; height: 35px;" colspan="3">
<p>&nbsp;</p>
</td>
</tr>
</tbody>
</table>' ;


            $mail->Subject = 'Zadužnica_potvrda preuzimanja tehničke imovine i proizvoda Banke od strane radnika /zaduženje';
            $mail->Body    = $bodyContent;



            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {  }
            echo 1;
        }

    }

    if($_POST['request']=='send-radnik_potpisuje_razduznica'){
        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter="";
        if($sector_type_usr==1)
            $sector_type_filter .= " and a.OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Razduznice_status] a join [c0_intranet2_apoteke].[dbo].[zaduznice_status] b
on a.item_id = b.item_id and a.user_id = b.user_id
 WHERE a.user_id=".$user_id.$sector_type_filter." and b.zaduzen<>0 and a.saglasan = 0" );
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if($status_count>0){
            echo 'nisu_popunjeni_odbij';
            return;
        }


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_header] SET
        radnik_potpisao_razduznica = ?
    WHERE user_id = ?
    UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
        status = 1
    WHERE user_id = ?"
        ;


        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $user_id,
                $user_id

            )
        );
        if($res->rowCount()==1) {
            echo 1;
            session_destroy();
        }

    }

    if($_POST['request']=='send-radnik_odbija_razduznica'){

        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter="";
        if($sector_type_usr==1)
            $sector_type_filter .= " and a.OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Razduznice_status] a join [c0_intranet2_apoteke].[dbo].[zaduznice_status] b
on a.item_id = b.item_id and a.user_id = b.user_id
 WHERE a.user_id=".$user_id.$sector_type_filter." and b.zaduzen<>0 and a.saglasan = 0 and a.zapisnik<>'' and a.zapisnik is not null" );
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if($status_count==0){
            echo 'nisu_popunjeni_odbij';
            return;
        }

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_header] SET
        potvrda_hr_razduznica = ?,
    poslano_radniku_razduznica = ?,
    potvrda_hr_steta = ?,
    radnik_odbio_razduznica = ?
    WHERE user_id = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '0',
                '0',
                '0',
                '1',
                $user_id,

            )
        );
        if($res->rowCount()==1) {
            //slanje maila adminima
            require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

            $user_to_send = _user($user_id);
            if($user_to_send['gender']==1)
                $postovanje = 'Po&scaron;tovana';
            else
                $postovanje = 'Po&scaron;tovani';


            $mail = new PHPMailer(true);
            $mail->CharSet = "UTF-8";


            $mail->isSMTP();
//$mail->Host = '91.235.170.162';
            $mail->Host = gethostbyname('xmail.teneo.ba');                   // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                            // Enable SMTP authentication
            $mail->Username = 'nav@teneo.ba';          // SMTP username
            $mail->Password = 'DynamicsNAV16!'; // SMTP password

            $mail->Port = 587;


            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;                              // TCP port to connect to

            $mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);
            $mail->addAddress('denis.zmukic@infodom.ba');
            // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

            $mail->isHTML(true);  // Set email format to HTML
            $bodyContent = '<table width="0">
<tbody>
<tr>
<td colspan="2" width="202">
<p>Po&scaron;tovane kolegice i kolege,</p>
</td>
</tr>
<tr>
<td width="196">
<p>&nbsp;</p>
</td>
</tr>
<tr>
<td colspan="3" width="788">
<p>Obavje&scaron;tavamo vas da zaposlenik <strong>'.$user_to_send['fname'].' '.$user_to_send['lname'].'</strong> u Sberbank BH d.d. nije saglasan sa određenom stavkom obrasca Razdužnice.</p>
</td>
</tr>
<tr>
<td colspan="5" width="1190">
<p>Molimo da pristupite obrascu putem <u>Emplyoee portal-a </u>i provjerite unesena razduženja, kao i dodatni komentar zaposlenika ispred Va&scaron;e organizacione jedinice &scaron;to je prije moguće.</p>
</td>
</tr>
<tr>
<td colspan="5" width="1190">
<p><strong><em>Napomena: </em></strong>Proces razduženja od strane svih učesnika, uključujući i samoga zaposlenika, mora biti zavr&scaron;en najkasnije do datuma prestanka radnog odnosa gore navedenog zaposlenika. Pristup Emplyoee portal-u se sistemski zatvara i zaposlenik neće biti u mogućnosti finalizirati proces razduženja nakod datuma prestanka radnog odnosa u Banci.</p>
</td>
</tr>
<tr>
<td width="196">
<p><strong>&nbsp;</strong></p>
</td>
</tr>
<tr>
<td colspan="4" width="1048">
<p>Ukoliko budete imali dodatnih pitanja vezano za proces, kolegice Migić Selma i Klačar Vildana ispred HR-a Vam stoje na raspolaganju za sva dodatna poja&scaron;njenja.</p>
</td>
</tr>
<tr>
<td width="196">
<p>&nbsp;</p>
</td>
</tr>
<tr>
<td width="196">
<p>Srdačan pozdrav,</p>
</td>
</tr>
<tr>
<td width="196">
<p>HR Tim potpis</p>
</td>
</tr>
</tbody>
</table>' ;


            $mail->Subject = 'Razdužnica_potvrda predaje tehničke imovine/razduženje';
            $mail->Body    = $bodyContent;



            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {  }
            echo 1;
        }

    }

    if($_POST['request']=='change-saglasan'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $pieces = explode("-", $_POST['saglasan_id']);
        $item_id= $pieces[1];
        $user_id= $pieces[2];

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_status] SET
      saglasan = ?
    WHERE user_id = ?
    AND item_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['saglasan'],
                $user_id,
                $item_id,
            )
        );
        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-saglasan_razduznica'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $pieces = explode("-", $_POST['saglasan_id']);
        $item_id= $pieces[1];
        $user_id= $pieces[2];

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Razduznice_status] SET
      saglasan = ?
    WHERE user_id = ?
    AND item_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['saglasan'],
                $user_id,
                $item_id,
            )
        );
        if($res->rowCount()==1) {
            echo date("Y/m/d");

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-zaduznica-zapisnik'){

        $pieces = explode("-", $_POST['zapisnik_id']);
        $item_id= $pieces[1];
        $user_id= $pieces[2];

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Zaduznice_status] SET
    zapisnik = ?
    WHERE user_id = ?
    AND item_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['zapisnik'],
                $user_id,
                $item_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='change-razduznica-zapisnik'){

        $pieces = explode("-", $_POST['zapisnik_id']);
        $item_id= $pieces[1];
        $user_id= $pieces[2];

        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[Razduznice_status] SET
    zapisnik = ?
    WHERE user_id = ?
    AND item_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['zapisnik'],
                $user_id,
                $item_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;

        } else {echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';}
    }

    if($_POST['request']=='export-excel'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $year = $_POST['year'];

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (
   weekday<>'6' AND weekday<>'7' and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   ))
   and year_id=".$year);


        if($get->rowCount()<0){

            foreach($get as $day){
                $arr [_nameHRstatus($day['status'])][]= $day['review_status'];
            }
        }

        foreach($arr as $key=>$value){
            $count0 = count(array_keys($value, 0));
            if($count0==0) $count0 = '0';
            $count1 = count(array_keys($value, 1));
            if($count1==0) $count1 = '0';
            $count2 = count(array_keys($value, 2));
            if($count2==0) $count2 = '0';
            $arr1[] = array($key,$count0,$count1,$count2);
        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tip odsustva')
            ->setCellValue('B1', 'Spašeno')
            ->setCellValue('C1', 'Odobreno')
            ->setCellValue('D1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(10);

        $doc->getActiveSheet()->getStyle('B1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('C1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('D1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="your_name.xls"');
        header('Cache-Control: max-age=1');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save($root.'/CORE/Satnice_'.$_user['username'].'.xls');

        echo $host.'/CORE/Satnice_'.$_user['username'].'.xls';

    }

    if($_POST['request']=='export-excel-reif'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $status_query = "(status<>5)";

        $filter_odobreno = @$_POST['filter_odobreno'];
        $filter_odobreno_cancel = @$_POST['filter_odobreno_cancel'];


        if(isset($filter_odobreno) and $filter_odobreno=='true'){
            $odobreno_query=" and review_status = 1";
        }
        elseif(isset($filter_odobreno) and $filter_odobreno=='false'){
            $odobreno_query=" and review_status = 0 ";
        }
        elseif(isset($filter_odobreno) and $filter_odobreno=='rejected'){
            $status_query = "(status=5)";
            $odobreno_query=" and status_rejected is not null and review_status = 0 and change_req<>2";
        }
        else
            $odobreno_query="";

        if(isset($filter_odobreno_cancel) and $filter_odobreno_cancel=='true'){
            $odobreno_cancel_query=" and change_req = 2";
            $status_query = "status=5";
        }
        elseif(isset($filter_odobreno_cancel) and $filter_odobreno_cancel=='false')
            $odobreno_cancel_query=" and change_req = '0'";
        else
            $odobreno_cancel_query="";


        $year = $_POST['year'];

        $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$year."'");
        $year_real  = $get_year->fetch();

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (".

            $status_query." and 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )".$odobreno_query.$odobreno_cancel_query.")
   and year_id=".$year);

        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        if($get->rowCount()<0){

            $index = 0;
            foreach($get as $key=>$day){
                /* if($day['status']==83)
    break; */
                if($key==0){

                    $day_id = $day['id']-1;
                    $status = $day['status'];
                    $status_rejected = $day['status_rejected'];
                    if($day['status']==81)
                        $status=73;
                    if($day['status_rejected']==81)
                        $status_rejected=73;
                    $description = $day['Description'];

                    if($filter_odobreno=='rejected' or $filter_odobreno_cancel=='true'){
                        $var_check = $status_rejected;
                    }
                    else{
                        $var_check = $status;
                    }

                    $arr [_nameHRstatus($var_check).'-'.$index]['datumOD'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $pocetni_datum = date('d-m-Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }

                $status_curr_rejected = $day['status_rejected'];
                $status_curr = $day['status'];
                if($status_curr==81)
                    $status_curr=73;

                if($status_curr_rejected==81)
                    $status_curr_rejected=73;

                if($filter_odobreno=='rejected' or $filter_odobreno_cancel=='true'){
                    $var_check_curr = $status_curr_rejected;
                    $var_check = $status_rejected;
                }
                else{
                    $var_check_curr = $status_curr;
                    $var_check = $status;
                }



                if(($var_check_curr==$var_check) and $day['Description']==$description and ($day['id']==($day_id+1)) ){
                    if(($day['weekday']!=6 and $day['weekday']!=7) or $day['KindofDay']=='BHOLIDAY'){
                        $arr [_nameHRstatus($var_check_curr).'-'.$index]['status_rejected']= _nameHRstatus($status_curr_rejected);
                    }
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar']= $day['review_comment'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar_radnika']= $day['employee_comment'];
                    if(($day['weekday']!=6 and $day['weekday']!=7) or in_array($var_check_curr, array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)))
                        $arr [_nameHRstatus($var_check_curr).'-'.$index][]= $day['review_status'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumDO'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }
                else{
                    $index=$index+1;
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumOD'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumDO'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    if(($day['weekday']!=6 and $day['weekday']!=7) or $day['KindofDay']=='BHOLIDAY'){
                        $arr [_nameHRstatus($var_check_curr).'-'.$index]['status_rejected']= _nameHRstatus($status_curr_rejected);
                    }
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar']= $day['review_comment'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar_radnika']= $day['employee_comment'];
                    if(($day['weekday']!=6 and $day['weekday']!=7) or in_array($var_check_curr, array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)))
                        $arr [_nameHRstatus($var_check_curr).'-'.$index][]= $day['review_status'];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $status_rejected = $day['status_rejected'];
                if($day['status']==81)
                    $status=73;

                $description = $day['Description'];

                $krajnji_datum = date('d-m-Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

            }


            foreach($arr as $key=>$value){
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2);


            }
            // print_r($arr);

        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Datum od')
            ->setCellValue('B1', 'Datum do')
            ->setCellValue('C1', 'Vrsta')
            ->setCellValue('D1', 'Spašeno')
            ->setCellValue('E1', 'Odobreno')
            ->setCellValue('F1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(10);

        $doc->getActiveSheet()->getStyle('D1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="your_name.xls"');
        header('Cache-Control: max-age=1');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls';

    }

    if($_POST['request']=='export-excel-reif-users'){

        $godina=date("Y");$mjesec=date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;
        // $year = $_POST['year'];

        if($_user['role']==4)
            $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5)  ORDER BY department_code");
        elseif($_user['role']==2)
            $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='".$_user['employee_no']."')  ORDER BY department_code");

        $arr1 = array();
        foreach($query as $item){

            $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$godina."' AND user_id = ".$item['user_id']);
            $year  = $get_year->fetch();

            $get_year1  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$year['id']."'");
            $year_real  = $get_year1->fetch();

            $arr = array();

            $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   ))
   and year_id=".$year['id']);


            if($get->rowCount()<0){

                $index = 0;
                foreach($get as $key=>$day){

                    if($key==0){
                        $status = $day['status'];
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

                    }

                    if($day['status']==$status){
                        $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    }
                    else{
                        $index=$index+1;
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                        $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];

                    }

                    $status = $day['status'];


                }


                foreach($arr as $key=>$value){
                    $count0 = count(array_keys($value, '0'));
                    $count1 = count(array_keys($value, '1'));
                    $count2 = count(array_keys($value, '2'));
                    $pieces = explode("-", $key);
                    $naziv_odsustva = $pieces[0];
                    $arr1[] = array($item['employee_no'],$value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2);


                }
                // print_r($arr);

            }
        }

        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Personalni broj')
            ->setCellValue('B1', 'Datum od')
            ->setCellValue('C1', 'Datum do')
            ->setCellValue('D1', 'Vrsta')
            ->setCellValue('E1', 'Spašeno')
            ->setCellValue('F1', 'Odobreno')
            ->setCellValue('G1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('G')->setWidth(10);

        $doc->getActiveSheet()->getStyle('E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('G1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="your_name.xls"');
        header('Cache-Control: max-age=1');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls';

    }

    if($_POST['request']=='export-excel-reif-users2'){

        $employee_no = $_POST['employee_no'];
        if($employee_no=="")
            $employee_query="";
        else
            $employee_query=" and employee_no= '".$employee_no."'";

        $ime_prezime = $_POST['ime_prezime'];
        if($ime_prezime=="")
            $ime_prezime_query="";
        else
            $ime_prezime_query=" and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE fname + ' ' + lname = N'".$ime_prezime."')";

        $vrsta = $_POST['vrsta'];
        if($vrsta=="")
            $vrsta_query="";
        else
            $vrsta_query=" and status= ".$vrsta."";

        $filter_neodobreno = $_POST['filter_neodobreno'];
        if($filter_neodobreno==true)
            $neodobreno_query=" and review_status = 0";
        else
            $neodobreno_query="";



        $godina=date("Y");$mjesec=date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;


        if($_user['role']==4){
            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
        }
        elseif($_user['role']==2){
            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent=".$_user['employee_no']."))";
        }

        $arr1 = array();

        $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$godina."' AND user_id = ".$_user['user_id']);
        $year_real  = $get_year->fetch();


        $arr = array();

        $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and KindofDay<>'BHOLIDAY' and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )".$vrsta_query.$role_query.$employee_query.$ime_prezime_query.$neodobreno_query.")
   order by employee_no");


        if($get->rowCount()<0){

            $index = 0;
            foreach($get as $key=>$day){

                if($key==0){
                    $day_id = $day['id']-1;
                    $status = $day['status'];
                    $employee_no = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['ime_prezime'] = _employee($employee_no)['fname'].' '._employee($employee_no)['lname'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['employee_no'] = $employee_no;
                    $arr [_nameHRstatus($day['status']).'-'.$index]['registrovano'] = $day['timest_edit'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'].' '._employee($day['employee_timest_edit'])['lname'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

                }

                if($day['status']==$status and $day['employee_no']==$employee_no and $day['Description']==$description and ($day['id']==$day_id+1)){
                    $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }
                else{
                    $index=$index+1;
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($day['status']).'-'.$index]['employee_no'] = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['registrovano'] = $day['timest_edit'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'].' '._employee($day['employee_timest_edit'])['lname'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['ime_prezime'] = _employee($day['employee_no'])['fname'].' '._employee($day['employee_no'])['lname'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $employee_no = $day['employee_no'];
                $description = $day['Description'];

            }


            foreach($arr as $key=>$value){
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                if($value['employee_no']=='1')
                    $count1 = $count1-1;
                $arr1[] = array($value['employee_no'],$value['ime_prezime'],$value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2,$value['registrovano'],$value['reg_korisnik']);


            }
            // print_r($arr);

        }


        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Personalni broj')
            ->setCellValue('B1', 'Ime')
            ->setCellValue('C1', 'Datum od')
            ->setCellValue('D1', 'Datum do')
            ->setCellValue('E1', 'Vrsta')
            ->setCellValue('F1', 'Spašeno')
            ->setCellValue('G1', 'Odobreno')
            ->setCellValue('H1', 'Odbijeno')
            ->setCellValue('I1', 'Registrovano')
            ->setCellValue('J1', 'Registrovao/la');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('I')->setWidth(22);
        $doc->getActiveSheet()->getColumnDimension('J')->setWidth(20);

        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('G1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('H1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="your_name.xls"');
        header('Cache-Control: max-age=1');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').xls';

    }

    if($_POST['request']=='export-pdf-reif'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $status_query = "(status<>5)";

        $filter_odobreno = @$_POST['filter_odobreno'];
        $filter_odobreno_cancel = @$_POST['filter_odobreno_cancel'];


        if(isset($filter_odobreno) and $filter_odobreno=='true'){
            $odobreno_query=" and review_status = 1";
        }
        elseif(isset($filter_odobreno) and $filter_odobreno=='false'){
            $odobreno_query=" and review_status = 0 ";
        }
        elseif(isset($filter_odobreno) and $filter_odobreno=='rejected'){
            $status_query = "(status=5)";
            $odobreno_query=" and status_rejected is not null and review_status = 0 and change_req<>2";
        }
        else
            $odobreno_query="";

        if(isset($filter_odobreno_cancel) and $filter_odobreno_cancel=='true'){
            $odobreno_cancel_query=" and change_req = 2";
            $status_query = "status=5";
        }
        elseif(isset($filter_odobreno_cancel) and $filter_odobreno_cancel=='false')
            $odobreno_cancel_query=" and change_req = '0'";
        else
            $odobreno_cancel_query="";

        $year = $_POST['year'];

        $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$year."'");
        $year_real  = $get_year->fetch();

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;

        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (".

            $status_query." and 
   (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   )".$odobreno_query.$odobreno_cancel_query.")
   and year_id=".$year);


        if($get->rowCount()<0){

            $index = 0;
            foreach($get as $key=>$day){
                /* if($day['status']==83)
    break; */
                if($key==0){

                    $day_id = $day['id']-1;
                    $status = $day['status'];
                    $status_rejected = $day['status_rejected'];
                    if($day['status']==81)
                        $status=73;
                    if($day['status_rejected']==81)
                        $status_rejected=73;
                    $description = $day['Description'];

                    if($filter_odobreno=='rejected' or $filter_odobreno_cancel=='true'){
                        $var_check = $status_rejected;
                    }
                    else{
                        $var_check = $status;
                    }

                    $arr [_nameHRstatus($var_check).'-'.$index]['datumOD'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $pocetni_datum = date('d-m-Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }

                $status_curr_rejected = $day['status_rejected'];
                $status_curr = $day['status'];
                if($status_curr==81)
                    $status_curr=73;

                if($status_curr_rejected==81)
                    $status_curr_rejected=73;

                if($filter_odobreno=='rejected' or $filter_odobreno_cancel=='true'){
                    $var_check_curr = $status_curr_rejected;
                    $var_check = $status_rejected;
                }
                else{
                    $var_check_curr = $status_curr;
                    $var_check = $status;
                }



                if(($var_check_curr==$var_check) and $day['Description']==$description and ($day['id']==($day_id+1)) ){
                    if(($day['weekday']!=6 and $day['weekday']!=7) or $day['KindofDay']=='BHOLIDAY'){
                        $arr [_nameHRstatus($var_check_curr).'-'.$index]['status_rejected']= _nameHRstatus($status_curr_rejected);
                    }
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar']= $day['review_comment'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar_radnika']= $day['employee_comment'];
                    if(($day['weekday']!=6 and $day['weekday']!=7) or in_array($var_check_curr, array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)))
                        $arr [_nameHRstatus($var_check_curr).'-'.$index][]= $day['review_status'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumDO'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }
                else{
                    $index=$index+1;
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumOD'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['datumDO'] = date('d.m.Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    if(($day['weekday']!=6 and $day['weekday']!=7) or $day['KindofDay']=='BHOLIDAY'){
                        $arr [_nameHRstatus($var_check_curr).'-'.$index]['status_rejected']= _nameHRstatus($status_curr_rejected);
                    }
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar']= $day['review_comment'];
                    $arr [_nameHRstatus($var_check_curr).'-'.$index]['komentar_radnika']= $day['employee_comment'];
                    if(($day['weekday']!=6 and $day['weekday']!=7) or in_array($var_check_curr, array(43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)))
                        $arr [_nameHRstatus($var_check_curr).'-'.$index][]= $day['review_status'];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $status_rejected = $day['status_rejected'];
                if($day['status']==81)
                    $status=73;

                $description = $day['Description'];

                $krajnji_datum = date('d-m-Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

            }


            foreach($arr as $key=>$value){
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2);
            }

//print_r($arr);
        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//  Change these values to select the Rendering library that you wish to use
//    and its directory location on your server
        $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF.php';
        $rendererLibrary = 'mPDF.php';
//$rendererLibrary = 'domPDF0.6.0beta3';
        $rendererLibraryPath = $root.'/tcpdf';

        $doc = new PHPExcel();

        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Datum od')
            ->setCellValue('B1', 'Datum do')
            ->setCellValue('C1', 'Vrsta')
            ->setCellValue('D1', 'Spašeno')
            ->setCellValue('E1', 'Odobreno')
            ->setCellValue('F1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(37);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(12);


        $doc->getActiveSheet()->getStyle('D1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');

        if (!PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                '<br />' .
                'at the top of this script as appropriate for your directory structure'
            );
        }
// Redirect output to a client’s web browser (PDF)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="01simple.pdf"');
        header('Cache-Control: max-age=0');

        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'PDF');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf';

    }

    if($_POST['request']=='export-pdf-reif-users'){

        $godina=date("Y");$mjesec=date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db,$root,$host;
        // $year = $_POST['year'];

        if($_user['role']==4)
            $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5)  ORDER BY department_code");
        elseif($_user['role']==2)
            $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='".$_user['employee_no']."')  ORDER BY department_code");

        $arr1 = array();
        foreach($query as $item){

            $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$godina."' AND user_id = ".$item['user_id']);
            $year  = $get_year->fetch();

            $get_year1  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$year['id']."'");
            $year_real  = $get_year1->fetch();

            $arr = array();

            $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and KindofDay<>'BHOLIDAY' and (
   (day >= ".$day_from." and month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (day <= ".$day_to." and month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((day >= ".$day_from." and day <= ".$day_to.") and month_id = ".$month_from." and month_id = ".$month_to.") OR
   (month_id > ".$month_from." and month_id < ".$month_to.")
   ))
   and year_id=".$year['id']);


            if($get->rowCount()<0){

                $index = 0;
                foreach($get as $key=>$day){

                    if($key==0){
                        $status = $day['status'];
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

                    }

                    if($day['status']==$status){
                        $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    }
                    else{
                        $index=$index+1;
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                        $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                        $arr [_nameHRstatus($day['status']).'-'.$index][]= $day['review_status'];

                    }

                    $status = $day['status'];


                }


                foreach($arr as $key=>$value){
                    $count0 = count(array_keys($value, '0'));
                    $count1 = count(array_keys($value, '1'));
                    $count2 = count(array_keys($value, '2'));
                    $pieces = explode("-", $key);
                    $naziv_odsustva = $pieces[0];
                    $arr1[] = array($item['employee_no'],$value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2);


                }
                // print_r($arr);

            }
        }

        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//  Change these values to select the Rendering library that you wish to use
//    and its directory location on your server
        $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF.php';
        $rendererLibrary = 'mPDF.php';
//$rendererLibrary = 'domPDF0.6.0beta3';
        $rendererLibraryPath = $root.'/tcpdf';

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Personalni broj')
            ->setCellValue('B1', 'Datum od')
            ->setCellValue('C1', 'Datum do')
            ->setCellValue('D1', 'Vrsta')
            ->setCellValue('E1', 'Spašeno')
            ->setCellValue('F1', 'Odobreno')
            ->setCellValue('G1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('G')->setWidth(10);

        $doc->getActiveSheet()->getStyle('E1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('G1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');

        if (!PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                '<br />' .
                'at the top of this script as appropriate for your directory structure'
            );
        }
// Redirect output to a client’s web browser (PDF)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="01simple.pdf"');
        header('Cache-Control: max-age=0');
        // Do your stuff here
        $writer = PHPExcel_IOFactory::createWriter($doc, 'PDF');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf';

    }

    if($_POST['request']=='export-pdf-reif-users2'){
        global $db,$root,$host, $_user;


        if(isset($_POST['korekcije'] ) and $_POST['korekcije'] = '1') {
            $col_name = "corr_review_status";
        } else {
            $col_name = "review_status";
        }
        $employee_no = $_POST['employee_no'];
        if($employee_no=="")
            $employee_query="";
        else
            $employee_query=" and employee_no= '".$employee_no."'";

        $filter_praznici = $_POST['filter_praznici'];
        if($filter_praznici==true)
            $praznici_query="";
        else
            $praznici_query=" and (h.KindOfDay<>'BHOLIDAY' or h.$col_name=0)";


        $filter_zahtjevi = $_POST['filter_zahtjevi'];
        if($filter_zahtjevi==true)
            $zahtjevi_query =" and change_req = 1";
        else
            $zahtjevi_query ="";

        $grupa = $_POST['grupa'];
        if($grupa=="")
            $grupa_query="";
        else
            $grupa_query=" and status in (select distinct id from [c0_intranet2_apoteke].[dbo].[hourlyrate_status] where status_group='".$grupa."')";

        $ime_prezime = $_POST['ime_prezime'];
        if($ime_prezime=="")
            $ime_prezime_query="";
        else
            $ime_prezime_query=" and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE fname + ' ' + lname = N'".$ime_prezime."')";

        $vrsta = $_POST['vrsta'];
        if($vrsta=="")
            $vrsta_query="";
        else
            $vrsta_query=" and status= ".$vrsta."";

        $filter_neodobreno = $_POST['filter_neodobreno'];
        if($filter_neodobreno==true)
            $neodobreno_query=" and $col_name = 0";
        else
            $neodobreno_query="";

        $godina=date("Y");$mjesec=date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        $datumOD = $_POST['datumod'];
        $datumDO = $_POST['datumdo'];

        $where_month1 = " and c.Date between CONVERT(datetime,'".$datumOD."',103) and CONVERT(datetime,'".$datumDO."',103)";

        // $year = $_POST['year'];

        /*   if($_user['role']==4)
   $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5)  ORDER BY department_code");
  elseif($_user['role']==2)
  $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='".$_user['employee_no']."')  ORDER BY department_code");
 */
        if($_user['role']==4){
            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
        }
        elseif($_user['role']==2){
            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent=".$_user['employee_no']."))";
        }

        $arr1 = array();

        $get_year  = $db->query("SELECT year FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE year='".$godina."' AND user_id = ".$_user['user_id']);
        $year_real  = $get_year->fetch();


        $arr = array();

        $get = $db->query("SELECT TOP 10 h.id, h.status, h.employee_no, h.day, h.month_id, h.Description, h.$col_name FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] h 
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] y
    on h.year_id = y.id
    join [c0_intranet2_apoteke].[dbo].[Calendar] c
    on (c.Year = y.year and c.Day = h.day and c.Month=h.month_id)
    
    WHERE 
   (
   h.weekday<>'6' AND h.weekday<>'7' and h.status<>5 and (
   (h.day >= ".$day_from." and h.month_id = ".$month_from." and (".$month_from." <> ".$month_to."))  OR
   (h.day <= ".$day_to." and h.month_id = ".$month_to." and (".$month_to." <> ".$month_from.")) OR
   ((h.day >= ".$day_from." and h.day <= ".$day_to.") and h.month_id = ".$month_from." and h.month_id = ".$month_to.") OR
   (h.month_id > ".$month_from." and h.month_id < ".$month_to.")
   )".$vrsta_query.$praznici_query.$zahtjevi_query.$role_query.$where_month1.$grupa_query.$employee_query.$ime_prezime_query.$neodobreno_query.")
   order by employee_no ");


        if($get->rowCount()<0){

            $index = 0;
            foreach($get as $key=>$day){

                if($key==0){
                    $day_id = $day['id']-1;
                    $status = $day['status'];
                    $employee_no = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['ime_prezime'] = _employee($employee_no)['fname'].' '._employee($employee_no)['lname'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['employee_no'] = $employee_no;
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));

                }

                if($day['status']==$status and $day['employee_no']==$employee_no and $day['Description']==$description and ($day['id']==$day_id+1)){
                    $arr [_nameHRstatus($day['status']).'-'.$index][]= $day[$col_name];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                }
                else{
                    $index=$index+1;
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumOD'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($day['status']).'-'.$index]['employee_no'] = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['ime_prezime'] = _employee($day['employee_no'])['fname'].' '._employee($day['employee_no'])['lname'];
                    $arr [_nameHRstatus($day['status']).'-'.$index]['datumDO'] = date('d/m/Y',strtotime($day['day'].'-'.$day['month_id'].'-'.$year_real['year']));
                    $arr [_nameHRstatus($day['status']).'-'.$index][]= $day[$col_name];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $employee_no = $day['employee_no'];
                $description = $day['Description'];

            }


            foreach($arr as $key=>$value){
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['employee_no'],$value['ime_prezime'],$value['datumOD'],$value['datumDO'],$naziv_odsustva,$count0,$count1,$count2);


            }
            // print_r($arr);

        }


        $pocetni_datum = date('d-m-Y',strtotime($day_from.'-'.$month_from.'-'.$year_real['year']));
        $krajnji_datum = date('d-m-Y',strtotime($day_to.'-'.$month_to.'-'.$year_real['year']));


        date_default_timezone_set('America/Los_Angeles');






        require_once($root.'/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//  Change these values to select the Rendering library that you wish to use
//    and its directory location on your server
        $rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF.php';
        $rendererLibrary = 'mPDF.php';
//$rendererLibrary = 'domPDF0.6.0beta3';
        $rendererLibraryPath = $root.'/tcpdf';

        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Personalni broj')
            ->setCellValue('B1', 'Ime')
            ->setCellValue('C1', 'Datum od')
            ->setCellValue('D1', 'Datum do')
            ->setCellValue('E1', 'Vrsta')
            ->setCellValue('F1', 'Spašeno')
            ->setCellValue('G1', 'Odobreno')
            ->setCellValue('H1', 'Odbijeno');

        $doc->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $doc->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $doc->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $doc->getActiveSheet()->getColumnDimension('H')->setWidth(10);

        $doc->getActiveSheet()->getStyle('F1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c0c0c0')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('G1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00cc00')
                )
            )
        );
        $doc->getActiveSheet()->getStyle('H1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc0000')
                )
            )
        );

        $doc->getActiveSheet()->fromArray($arr1, '0', 'A2');

        if (!PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                '<br />' .
                'at the top of this script as appropriate for your directory structure'
            );
        }
// Redirect output to a client’s web browser (PDF)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="01simple.pdf"');
        header('Cache-Control: max-age=0');
        // Do your stuff here

        $writer = PHPExcel_IOFactory::createWriter($doc, 'PDF');

        $writer->save($root.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf');

        echo $host.'/CORE/public/Odsustva_'.$_user['username'].'('.$pocetni_datum.' - '.$krajnji_datum.').pdf';

    }

    if($_POST['request']=='change-pagination'){

        $page = $_POST['page'];
        $limit= $_POST['limit'];


        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[pagination] SET
      Limit = ?
    WHERE Page = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $limit,
                $page,

            )
        );
        if($res->rowCount()==1) {

            echo 1;
        }
        else{  echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Izmjene nisu spašene!').'</div>';
        }


        // echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';


    }







    if($_POST['request']=='profile-edit'){

        $this_id = $_POST['request_id'];

        $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee] WHERE [No_]='".$this_id."'");
        if($get->rowCount()<0)
            $row_employee = $get->fetchAll();

// hm 01
        if(isset($_FILES['media_file'])){
            if(is_uploaded_file($_FILES['media_file']['tmp_name'])){


                if($_FILES["media_file"]["type"] == "image/jpeg" || $_FILES["media_file"]["type"] == "image/jpg"){

                    $maxDimW = 225;
                    $maxDimH = 225;
                    list($width, $height, $type, $attr) = getimagesize( $_FILES['media_file']['tmp_name'] );
                    if ( $width > $maxDimW || $height > $maxDimH ) {
                        $target_filename = $_FILES['media_file']['tmp_name'];
                        $fn = $_FILES['media_file']['tmp_name'];
                        $size = getimagesize( $fn );
                        $ratio = $size[0]/$size[1]; // width/height
                        if( $ratio > 1) {
                            $width = $maxDimW;
                            $height = $maxDimH/$ratio;
                        } else {
                            $width = $maxDimW*$ratio;
                            $height = $maxDimH;
                        }
                        $src = imagecreatefromstring(file_get_contents($fn));
                        $dst = imagecreatetruecolor( $width, $height );
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );

                        imagejpeg($dst, $target_filename); // adjust format as needed
                    }
                    $destination_upload = 'C:/Temp/uploads/raiff/' . $this_id . ".jpeg";

                    $destination_upload_portal = '../../uploads/temp.jpg';

                    if(move_uploaded_file($_FILES['media_file']['tmp_name'], $destination_upload )){

                    }



                    $p_photo_x = base64_encode(file_get_contents($destination_upload));
                    $p_photo= 'data:image/jpeg;base64,'.$p_photo_x;

                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
             picture = ?
             WHERE employee_no = ?";

                    $res = $db->prepare($data);
                    $res->execute(
                        array(

                            $p_photo,
                            $this_id
                        )
                    );
                } else {
                    echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format slike nije podrzan! Koritite JPEG format.').'</div>"}';
                    return;
                }
            }
        }


// hm 01 end
        $count_dijete = 0;
        $count_pastorak = 0;
        $count_srodnici_banka = 0;

        $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."' AND [Relative Code] = 'OTAC'");
        if($get->rowCount()<0)
            $row_otac = $get->fetchAll();

        $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."' AND [Relative Code] = 'MAJKA'");
        if($get->rowCount()<0)
            $row_majka = $get->fetchAll();

        $get = $db->query("SELECT * FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."' AND [Relative Code] = N'SUPRUŽNIK'");
        if($get->rowCount()<0)
            $row_supruznik = $get->fetchAll();


        if(isset($row_otac))
            $count_otac = count($row_otac);
        else
            $count_otac = 0;
        if(isset($row_majka))
            $count_majka = count($row_majka);
        else
            $count_majka = 0;
        if(isset($row_supruznik))
            $count_supruznik = count($row_supruznik);
        else
            $count_supruznik = 0;

        if(isset($_POST['phone_emergency_person']))
            $phone_emergency_person = $_POST['phone_emergency_person'];
        else
            $phone_emergency_person = '';


// hm 02 kod od 6509 do 6542
        if(!isset($_POST['Phone_No'])){ $_POST['Phone_No'] = "";}

        if((((strpos($_POST['Phone_No'], ' ') <3 && $_POST['Phone_No'] != "") or (strpos($_POST['Phone_No'], ' ') > 3 && $_POST['Phone_No'] != "")) and $_POST['country_region'] != "") or (((strpos($_POST['phone_mob'], ' ') <3 && $_POST['phone_mob'] != "") or (strpos($_POST['phone_mob'], ' ') > 3 && $_POST['phone_mob'] != "")) and $_POST['country_region_mobile'] != "") ){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        if($_POST['country_region'] != ""){
            if(substr_count($_POST['Phone_No'], ' ')>1 && $_POST['Phone_No'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }

            if(strlen($_POST['Phone_No'])<7 && $_POST['Phone_No'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }
        }
        if($_POST['country_region_mobile'] != ""){
            if(substr_count($_POST['phone_mob'], ' ')>1 && $_POST['phone_mob'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }

            if(strlen($_POST['phone_mob'])<7 && $_POST['phone_mob'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }
        }
        // hm 02 ----- && $_POST['phone_emergency_person'] != ""
        if($phone_emergency_person!=''){

            if((strpos($_POST['phone_emergency_person'], ' ') <3 && $_POST['phone_emergency_person'] != "" ) or (strpos($_POST['phone_emergency_person'], ' ') > 3 && $_POST['phone_emergency_person'] != "")){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }

            if(substr_count($_POST['phone_emergency_person'], ' ')>1 && $_POST['phone_emergency_person'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }
            // hm 02 ----- && $_POST['phone_emergency_person'] != ""
            if(strlen($_POST['phone_emergency_person'])<7 && $_POST['phone_emergency_person'] != ""){
                echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
                return;
            }
        }




        $this_id = $_POST['request_id'];
        $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee] SET
    [Country_Region Code Home] = ?,
    [Dial Code Home] = ?,
    [Phone No_] = ?,
    [Full Phone No_] = ?,
    [Country_Region Code Mobile] = ?,
    [Dial Code Mobile] = ?,
    [Mobile Phone No_] = ?,
    [Company Mobile Phone No_] =?,
    [E-Mail] = ?,
    [Related Person to be informed] = ?,
    [Relationship with Related Per_] = ?,
    [Country_Region Code Emergency] = ?,
    [Dial Code Emergency] = ?,
    [Phone No_ Emergency] = ?,
    [Mother Name] = ?,
    [Father Name] = ?,
    [Mother Maiden Name] = ?,
    [Marital status] = ?,
    [Spouse Name] = ?,
    [Blood Type] = ?,
    [Driving Licence] = ?,
    [Driving Llicence Category] = ?,
    [Active Driver] = ?,
    [Municipality Code of Birth] = ?,
    [Municipality Name of Birth] = ?,
    [Place of birth] = ?,
    [City of Birth] = ?,
    [Country_Region Code of Birth] = ?
    where [No_] = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['country_region'],
                $_POST['country_region_region'],
                $_POST['Phone_No'],
                $_POST['country_region'].' '.$_POST['country_region_region'].' '.$_POST['Phone_No'],
                $_POST['country_region_mobile'],
                $_POST['country_region_region_mobile'],
                $_POST['phone_mob'],
                $_POST['country_region_mobile'].' '.$_POST['country_region_region_mobile'].' '.$_POST['phone_mob'],
                $_POST['email'],
                mb_strtoupper($_POST['fname_lname_emergency_person'], "UTF-8"),
                mb_strtoupper($_POST['relationship_emergency_person'], "UTF-8"),
                $_POST['country_rel_person'],
                $_POST['country_region_region_rel_person'],
                $phone_emergency_person,
                mb_strtoupper($_POST['ime_majke'], "UTF-8").' '.mb_strtoupper($_POST['prezime_majke'], "UTF-8"),
                mb_strtoupper($_POST['ime_oca'], "UTF-8").' '. mb_strtoupper($_POST['prezime_oca'], "UTF-8"),
                mb_strtoupper($_POST['m_djevojacko'], "UTF-8"),
                $_POST['m_status'],
                mb_strtoupper($_POST['ime_supruznika'], "UTF-8").' '.mb_strtoupper($_POST['prezime_supruznika'], "UTF-8"),
                $_POST['krvna_grupa'],
                $_POST['vozacka'],
                $_POST['kategorija'],
                $_POST['akt_vozac'],
                $_POST['code_sifra_rodjenje_mun'],
                $_POST['mun_name'],
                $_POST['mjesto_rodjenja'],
                $_POST['grad_rodjenja'],
                $_POST['code_sifra_rodjenje_drz'],
                $this_id
            )
        );

        if($count_otac>0){

            if($_POST['rodjenje_otac']=='')
                $rodjenje_otac = '1753-01-01 00:00:00.000';
            else
                $rodjenje_otac = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_otac'])));

            $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] SET
    [First Name] = ?,
    [Last Name] = ?,
    [Birth Date] = ?
    WHERE [Employee No_] = ?
    AND [Relative Code] = 'OTAC'";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    mb_strtoupper($_POST['ime_oca'], "UTF-8"),
                    mb_strtoupper($_POST['prezime_oca'], "UTF-8"),
                    $rodjenje_otac,
                    $this_id
                )
            );
        }
        else{

            if($_POST['rodjenje_otac']=='')
                $rodjenje_otac = '1753-01-01 00:00:00.000';
            else
                $rodjenje_otac = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_otac'])));


            $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."'");
            if($get->rowCount()<0)
                $maximalni = $get->fetchAll();

            $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] ([Employee No_],[Line No_],
    [Relative Code], [First Name],[Middle Name], [Last Name],[Birth Date],[Phone No_],[Relative_s Employee No_],[Sex],[Vacation Ease],
    [Age],[Health Insurance],[Relation],[Mother_s Maiden Name],[Parent Relation],[Spouse],[Date Of Input Info],[Disabled Child],[Relative_s Employee Full Name])
VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $this_id,
                    $maximalni[0]['maximalni']+1,
                    'OTAC',
                    mb_strtoupper($_POST['ime_oca'], "UTF-8"),
                    '',
                    mb_strtoupper($_POST['prezime_oca'], "UTF-8"),
                    $rodjenje_otac,
                    '',
                    '',
                    1,
                    0,
                    0,
                    0,
                    2,
                    mb_strtoupper($_POST['m_djevojacko'], "UTF-8"),
                    0,
                    0,
                    date("Y/m/d"),
                    0,
                    ''
                )
            );
        }

        if($count_majka>0){

            if($_POST['rodjenje_majka']=='')
                $rodjenje_majka = '1753-01-01 00:00:00.000';
            else
                $rodjenje_majka = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_majka'])));

            $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] SET
    [First Name] = ?,
    [Last Name] = ?,
    [Birth Date] = ?,
    [Mother_s Maiden Name] = ?
    WHERE [Employee No_] = ?
    AND [Relative Code] = 'MAJKA'";


            $res = $db->prepare($data);
            $res->execute(
                array(
                    mb_strtoupper($_POST['ime_majke'], "UTF-8"),
                    mb_strtoupper($_POST['prezime_majke'], "UTF-8"),
                    $rodjenje_majka,
                    mb_strtoupper($_POST['m_djevojacko'],'UTF-8'),
                    $this_id
                )
            );

        }
        else{

            if($_POST['rodjenje_majka']=='')
                $rodjenje_majka = '1753-01-01 00:00:00.000';
            else
                $rodjenje_majka = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_majka'])));

            $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."'");
            if($get->rowCount()<0)
                $maximalni = $get->fetchAll();

            $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] ([Employee No_],[Line No_],
    [Relative Code], [First Name],[Middle Name], [Last Name],[Birth Date],[Phone No_],[Relative_s Employee No_],[Sex],[Vacation Ease],
    [Age],[Health Insurance],[Relation],[Mother_s Maiden Name],[Parent Relation],[Spouse],[Date Of Input Info],[Disabled Child],[Relative_s Employee Full Name])
VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $this_id,
                    $maximalni[0]['maximalni']+1,
                    'MAJKA',
                    mb_strtoupper($_POST['ime_majke'], "UTF-8"),
                    '',
                    mb_strtoupper($_POST['prezime_majke'],'UTF-8'),
                    $rodjenje_majka,
                    '',
                    '',
                    1,
                    0,
                    0,
                    0,
                    1,
                    mb_strtoupper($_POST['m_djevojacko'], "UTF-8"),
                    0,
                    0,
                    date("Y/m/d"),
                    0,
                    ''
                )
            );
        }

        if($count_supruznik>0){

            if($_POST['rodjenje_supruznik']=='')
                $rodjenje_supruznik = '1753-01-01 00:00:00.000';
            else
                $rodjenje_supruznik = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_supruznik'])));

            $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] SET
     [First Name] = ?,
     [Last Name] = ?,
    [Birth Date] = ?
    WHERE [Employee No_] = ?
    AND [Relative Code] = N'SUPRUŽNIK'";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    mb_strtoupper($_POST['ime_supruznika'], "UTF-8"),
                    mb_strtoupper($_POST['prezime_supruznika'], "UTF-8"),
                    $rodjenje_supruznik,
                    $this_id
                )
            );
        }
        else{

            if($_POST['rodjenje_supruznik']=='')
                $rodjenje_supruznik = '1753-01-01 00:00:00.000';
            else
                $rodjenje_supruznik = date("Y/m/d", strtotime(str_replace("/",".",$_POST['rodjenje_supruznik'])));

            $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."'");
            if($get->rowCount()<0)
                $maximalni = $get->fetchAll();

            $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] ([Employee No_],[Line No_],
    [Relative Code], [First Name],[Middle Name], [Last Name],[Birth Date],[Phone No_],[Relative_s Employee No_],[Sex],[Vacation Ease],
    [Age],[Health Insurance],[Relation],[Mother_s Maiden Name],[Parent Relation],[Spouse],[Date Of Input Info],[Disabled Child],[Relative_s Employee Full Name])
VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $this_id,
                    $maximalni[0]['maximalni']+1,
                    'SUPRUŽNIK',
                    mb_strtoupper($_POST['ime_supruznika'], "UTF-8"),
                    '',
                    mb_strtoupper($_POST['prezime_supruznika'], "UTF-8"),
                    $rodjenje_supruznik,
                    '',
                    '',
                    1,
                    0,
                    0,
                    0,
                    4,
                    mb_strtoupper($_POST['m_djevojacko'], "UTF-8"),
                    0,
                    0,
                    date("Y/m/d"),
                    0,
                    ''
                )
            );
        }

        $ukupni_jezici  = array();
        if(isset($_POST['nivo_jezik'])){
            foreach($_POST['nivo_jezik'] as $key=>$nivo){
                $ukupni_jezici[] = array("nivo_jezik"=>$_POST['nivo_jezik'][$key], "jezik"=>$_POST['jezik'][$key],"line_no"=>$_POST['line_no'][$key]);
            }

            foreach($ukupni_jezici as $key=>$nivo){
                $data2 = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee Qualification] SET [Language Level] = ".$nivo['nivo_jezik'].", [Language Name]= '".$nivo['jezik']."' WHERE [Line No_] = ".$nivo['line_no']." AND [Employee No_] ='".$this_id."'";

                $res2 = $db->prepare($data2);
                $res2->execute(
                    array()
                );
            }
        }


        $ukupni_certifikati  = array();
        if(isset($_POST['certifikat_opis'])){
            foreach($_POST['certifikat_opis'] as $key=>$certifikat){
                $ukupni_certifikati[] = array("opis"=>$_POST['certifikat_opis'][$key], "kompanija"=>$_POST['certifikat_kompanija'][$key],"line_no"=>$_POST['line_no_certifikat'][$key]);
            }

            foreach($ukupni_certifikati as $key=>$certifikat){
                $data2 = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee Qualification] SET [Description] = '".$certifikat['opis']."', [Institution_Company]= '".$certifikat['kompanija']."' WHERE [Line No_] = ".$certifikat['line_no']." AND [Employee No_] ='".$this_id."'";

                $res2 = $db->prepare($data2);
                $res2->execute(
                    array()
                );
            }
        }





        if(@$_POST['zamjenik']!=''){

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    zamjenik = ".$_POST['zamjenik']." where employee_no ='"._employee($this_id)['employee_no']."'";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parent2 = ".$_POST['zamjenik']." where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='"._employee($this_id)['employee_no']."'))";



            $res = $db->prepare($data);
            $res->execute(
                array()
            );
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO2d = ".$_POST['zamjenik']." where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO2='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO3d = ".$_POST['zamjenik']." where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO3='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO4d = ".$_POST['zamjenik']." where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO4='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO5d = ".$_POST['zamjenik']." where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO5='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );
        }
        else
        {
            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    zamjenik = NULL where employee_no ='"._employee($this_id)['employee_no']."'";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parent2 = 0 where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO2d = 0 where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO2='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO3d = 0 where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO3='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO4d = 0 where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO4='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );

            $data = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
    parentMBO5d = 0 where employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parentMBO5='"._employee($this_id)['employee_no']."'))";

            $res = $db->prepare($data);
            $res->execute(
                array()
            );
        }

        echo '{"jsonrpc" : "2.0", "status" : "oki", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';
    }

    if($_POST['request']=='telefon-edit'){

        if(((strpos($_POST['Phone_No_Company'], ' ') <3) or (strpos($_POST['Phone_No_Company'], ' ') > 3)) && $_POST['Phone_No_Company'] != ""){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        if(substr_count($_POST['Phone_No_Company'], ' ')>1 && $_POST['Phone_No_Company'] != ""){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        if(strlen($_POST['Phone_No_Company'])<7 && $_POST['Phone_No_Company'] != ""){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        $this_id = $_POST['request_id'];
        $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee] SET
  [Country_Region Code Company H] = ?,
  [Dial Code Company Home] = ?,
  [Phone No_ for Company] = ?,
  [Company Phone No_] = ?
  where [No_] = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['Phone_No_Company_country'],
                $_POST['Phone_No_Company_region'],
                $_POST['Phone_No_Company'],

                $_POST['Phone_No_Company_country'].' '.$_POST['Phone_No_Company_region'].' '.$_POST['Phone_No_Company'],

                $this_id
            )
        );

        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';
    }

    if($_POST['request']=='telefon-edit-mob'){

        if(((strpos($_POST['Phone_No_Company_mob'], ' ') <3) or (strpos($_POST['Phone_No_Company_mob'], ' ') > 3)) && $_POST['Phone_No_Company_mob'] != ''){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        if(substr_count($_POST['Phone_No_Company_mob'], ' ')>1 && $_POST['Phone_No_Company_mob'] != ''){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        if(strlen($_POST['Phone_No_Company_mob'])<7 && $_POST['Phone_No_Company_mob'] != ''){
            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-danger text-center\">'.__('Format telefona nije ispravan').'</div>"}';
            return;
        }

        $this_id = $_POST['request_id'];
        $data = "UPDATE $_conf[nav_database].[RAIFFAISEN BANK\$Employee] SET
  [Country_Region Code Company M] = ?,
  [Dial Code Company Mobile] = ?,
  [Mobile Phone No_ for Company] = ?,
  [Company Mobile Phone No_] =?
  where [No_] = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['Phone_No_Company_country_mob'],
                $_POST['Phone_No_Company_region_mob'],
                $_POST['Phone_No_Company_mob'],
                $_POST['Phone_No_Company_country_mob'].' '.$_POST['Phone_No_Company_region_mob'].' '.$_POST['Phone_No_Company_mob'],
                $this_id
            )
        );

        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';
    }

    if($_POST['request']=='insert-srodnik'){

        $this_id = $_POST['request_id'];

        $get = $db->query("SELECT max([Line No_]) as maximalni FROM $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] WHERE [Employee No_]='".$this_id."'");
        if($get->rowCount()<0)
            $maximalni = $get->fetchAll();

        $name = $_POST['name'];

        $pieces = explode(" ", $name);
        $fname = $pieces[0];
        $lname = $pieces[1];
        $broj_srodnika = $_POST['broj_srodnika'];

        $_srodnik = _employeeNAV($broj_srodnika);

        $srodstvo = $_POST['srodstvo'];
        $code_srodstva= $db->query("SELECT [Code] as sifras FROM $_conf[nav_database].[RAIFFAISEN BANK\$Relative] WHERE [Code]='".$_POST['srodstvo']."'");
        foreach ($code_srodstva as $sifra_srodstva) {
            $sifra=$sifra_srodstva['sifras'];
        }
        $data = "INSERT INTO $_conf[nav_database].[RAIFFAISEN BANK\$Employee Relative] ([Employee No_],[Line No_],
    [Relative Code], [First Name],[Middle Name], [Last Name],[Birth Date],[Phone No_],[Relative_s Employee No_],[Sex],[Vacation Ease],
    [Age],[Health Insurance],[Relation],[Mother_s Maiden Name],[Parent Relation],[Spouse],[Date Of Input Info],[Disabled Child],[Relative_s Employee Full Name])
VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?,?, ?, ?, ?, ?)";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $this_id,
                $maximalni[0]['maximalni']+1,
                $sifra,
                $fname,
                '',
                $lname,
                date('Y/m/d', strtotime($_srodnik['Birth Date'])),
                '',
                $broj_srodnika,
                1,
                0,
                0,
                0,
                5,
                '',
                0,
                0,
                date("Y/m/d"),
                0,
                ''
            )
        );

        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';
    }

    if($_POST['request']=='get-name-mun'){

        $this_id = $_POST['request_id'];
        echo _optionGetNamebyCodeMunNAV($this_id);
    }

    if($_POST['request']=='profile-sync'){

        $this_id = $_POST['request_id'];
        $data = "EXEC [c0_intranet2_apoteke].[dbo].[EmployeeUpdate] '".$this_id."'";

        $res = $db->prepare($data);
        $res->execute(
            array()
        );
        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';
    }

    if($_POST['request']=='profile-sync-nav-portal-user'){

        $this_id = $_POST['request_id'];

        $data = "EXEC [c0_intranet2_apoteke].[dbo].[get_nav_data_employee_user] '".$this_id."'
  EXEC [c0_intranet2_apoteke].[dbo].[get_nav_data_employee_contract_user] '".$this_id."'";
        $res = $db->prepare($data);
        $res->execute(
            array()
        );

        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene!').'</div>"}';

    }

    if($_POST['request']=='profile-sync-nav-portal'){



        $data = "EXEC [c0_intranet2_apoteke].[dbo].[get_nav_data_employee]";
        $res = $db->prepare($data);
        $res->execute(
            array()
        );

        $data = "EXEC [c0_intranet2_apoteke].[dbo].[get_nav_data_employee_contract]";
        $res = $db->prepare($data);
        $res->execute(
            array()
        );

        echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">'.__('Informacije su uspješno spašene_global!').'</div>"}';
    }

    if($_POST['request']=='task-review'){

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks] SET
        is_user_reviewed = ?,
        user_rating = ?
        
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['rating'],
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo 1;
        }

    }


    if($_POST['request']=='task-review-item'){

        parse_str($_POST["data"], $_POST);

        $this_id = $_POST['request_id'];
        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[tasks_item] SET
        is_rated = ?,
        user_rating = ?
        WHERE taskitem_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $_POST['user_rating'],
                $this_id
            )
        );
        if($res->rowCount()==1) {
            echo $this_id;
        }

    }


    /*****************trainings********************/


    if($_POST['request']=='trainings-request-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $ds   = explode('/', $_POST['empdate']);
        $df   = explode('/', $_POST['DateFrom']);
        $dt   = explode('/', $_POST['DateTo']);

        $from = $ds[2].'-'.$ds[1].'-'.$ds[0];
        $fromD = $df[2].'-'.$df[1].'-'.$df[0];
        $toD = $dt[2].'-'.$dt[1].'-'.$dt[0];

        $user_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['parent']."'");

        foreach($user_query as $uquery) {

            $email = $uquery['email'];

        }


        $user  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id='".$_user['user_id']."'");

        foreach($user as $uquery2) {

            $fname = $uquery2['fname'];
            $lname = $uquery2['lname'];


            $position = $uquery2['position'];
            $departmentcode = $uquery2['department_code'];
            $sector = $uquery2['sector'];




        }



        if($fromD <= $toD ){



            $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[trainings] (
      user_id,parent,parent2,hr,admin,date_created,h_from,date_from,date_to,status,status_hr,status_admin,type,is_archive,country,reasons,outcome,name_of_seminar,certificate_name,costs,wage,accommodation,transport,total_costs,organizer,remark,training,training_description,comment,send_email) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $res = $db->prepare($data);
            $res->execute(
                array($_user['user_id'],$_user['parent'],$_user['parent2'],$_user['hr'],$_user['admin'],
                    date('Y-m-d', strtotime("now")),
                    date('Y-m-d', strtotime($from)),
                    date('Y-m-d', strtotime($fromD)),
                    date('Y-m-d', strtotime($toD)),
                    '0',
                    '0',
                    '0',
                    'TRENING',
                    '0',
                    $_POST['country'],$_POST['reasons'],$_POST['outcome'],$_POST['nameofseminar'],$_POST['nameofcertif'],$_POST['costsPDV'],$_POST['wage'],$_POST['accommodation'],$_POST['transport'],$_POST['totalcosts'],$_POST['organizer'],$_POST['remark'],$_POST['check_list'],$_POST['ostalo'],$_POST['comment'],'0'));
            if($res->rowCount()==1)

            {
                echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';

                ?>
                <?php



                $query = $db->query("SELECT MAX(request_id) as maxrequest FROM [c0_intranet2_apoteke].[dbo].[trainings] WHERE user_id='".$_user['user_id']."'");

                foreach($query as $item){
                    $tools_id = $item['maxrequest'];
                }



                /*********************************************/
                /***************PHP MAILER********************/
                /*********************************************/
                require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

                $mail = new PHPMailer(true);
                $mail->CharSet = "UTF-8";


                $mail->isSMTP();
//$mail->Host = '91.235.170.162';
                $mail->Host = gethostbyname('xmail.teneo.ba');                   // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                            // Enable SMTP authentication
                $mail->Username = 'nav@teneo.ba';          // SMTP username
                $mail->Password = 'DynamicsNAV16!'; // SMTP password

                $mail->Port = 587;


                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;                              // TCP port to connect to

                $mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email);
                $mail->addAddress('irma.hrelja@infodom.ba');
                // Add a recipient
//$mail->addCC('nermina.kraljic@infodom.ba');
//$mail->addBCC('bcc@example.com');

                $mail->isHTML(true);  // Set email format to HTML


                $bodyContent = '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;">Poštovana/i, </h5 >' ;
                $bodyContent .= '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za ekstreni trening '.' '.$_POST['nameofseminar'].' '.' za'.' '.$fname.' '.$lname.' '.' zaposlenika na radnom mjestu'.' '.$position.' '.' u  '.' '.$departmentcode.' '.' u periodu od '.' '.date('d-m-Y', strtotime($fromD)).' '.' do'.' '.date('d-m-Y', strtotime($toD)).' '.'. <br>Za direktan pristup odobravanju Zahtjeva, kliknite </h5 >' ;
                $bodyContent .= '<a  style="margin:0px; font-family: Calibri;font-size:14px;" href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=trainings&p=popup_trainings_reponse&id='.$tools_id.'> OVDJE.</a>';
                $bodyContent .= '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
                $mail->Subject = 'Employee Portal!';
                $mail->Body    = $bodyContent;


                if(!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else { ?>


                <?php }



            }

        }else echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Datum završetka ne može biti manji od datuma početka usavršavanja!').'</div>';

    }

    /*****************Business trip********************/


    if($_POST['request']=='business-trip-request-add'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $df   = explode('/', $_POST['DateFrom']);
        $dt   = explode('/', $_POST['DateTo']);
        $dp   = explode('/', $_POST['DateDetermined']);
        $dc   = explode('/', $_POST['DateOfCalculation']);


        $fromD = $df[2].'-'.$df[1].'-'.$df[0];
        $toD = $dt[2].'-'.$dt[1].'-'.$dt[0];
        $dateD = $dp[2].'-'.$dp[1].'-'.$dp[0];
        $dateC = $dc[2].'-'.$dc[1].'-'.$dc[0];

        $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['parent']."'");

        foreach($parent_query as $uquery) {

            $email_parent = $uquery['email'];

        }



        // $parent2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['parentMBO2']."'");

        // foreach($parent2_query as $uquery) {

        // $email_parent2 = $uquery['email'];

        // }


        $stream_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['stream_parent']."'");

        foreach($stream_query as $uquery) {

            $email_stream_parent = $uquery['email'];

        }


        $parent_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['parent']."'");

        foreach($parent_query as $uquery) {

            $email_parent = $uquery['email'];

        }


        $admin_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['to_admin']."'");

        foreach($admin_query as $uquery) {

            $email_admin = $uquery['email'];

        }

        $admin2_query  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$_user['to_admin2']."'");

        foreach($admin2_query as $uquery) {

            $email_admin2 = $uquery['email'];

        }

        $user  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id='".$_user['user_id']."'");

        foreach($user as $uquery2) {

            $fname = $uquery2['fname'];
            $lname = $uquery2['lname'];


            $position = $uquery2['position'];
            $departmentcode = $uquery2['department_code'];
            $sector = $uquery2['sector'];
            $jmb = $uquery2['JMB'];




        }



        if($fromD <= $toD ){

            if ($_POST['countryino'] == 1)
            {

                $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip] (
      user_id,hr,admin,admin2,date_created,h_from,h_to,date_determined,date_of_calculation,zahtjev_vs_nalog,status,status_parent2,status_admin,type,is_archive,send_email,
      destination,country_ino,purpose_trip,reasons,time_of_seminar,
      check_list1,check_list2,transport,transport_details,number_of_employee,
      check_list3,transport_notes,accommodation,accommodation_details,
      check_list4,check_list5,limit_notes,check_list6,total_f,amount_f,number_f) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array($_user['user_id'],$_user['stream_parent'],$_user['to_admin'],$_user['to_admin2'],
                        date('Y-m-d', strtotime("now")),
                        date('Y-m-d', strtotime($fromD)).' '.$_POST['TimeFrom'],
                        date('Y-m-d', strtotime($toD)).' '.$_POST['TimeTo'],
                        date('Y-m-d', strtotime($dateD)),
                        date('Y-m-d', strtotime($dateC)),
                        '0',
                        '0',
                        '0',
                        '0',
                        'SLUŽBENI PUT',
                        '0',
                        '0',
                        $_POST['destination'],$_POST['countryino'],$_POST['purpose_trip'],$_POST['reasons'],$_POST['TimeOfSeminar'],
                        $_POST['check_list1'],$_POST['check_list2'],$_POST['transport'],$_POST['transport_details'],$_POST['numberOfEmployee'],
                        $_POST['check_list3'],$_POST['transport_notes'],$_POST['atribut'],$_POST['accommodation_details'],
                        $_POST['check_list4'],$_POST['check_list5'],$_POST['limit_notes'],$_POST['check_list6'],$_POST['total_f'],$_POST['amount_f'],$_POST['number_f']));
                if($res->rowCount()==1)

                {



                    $id = $db->lastInsertId();

                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
    request_id_NAV = ?
    WHERE request_id = ?";
                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $id,
                            $id,

                        )
                    );


                    $ukupni  = array();
                    foreach($_POST['task'] as $key=>$task){
                        $ukupni[] = array("number"=>$_POST['task'][$key], "total"=>$_POST['task4'][$key],"amount"=>$_POST['amount'][$key],"country"=>$_POST['country'][$key]);

                    }


                    foreach($ukupni as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_item] (
        number,total,amount,country,date_completed,request_id) VALUES (?,?,?,?,?,?)";


                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["total"],
                                $task["amount"],
                                $task["country"],
                                date('Y-m-d', strtotime("now")),
                                $id

                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }

                    }



                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_item] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_item] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }

                    $ukupni_smjestaj  = array();
                    foreach($_POST['accommodation1'] as $key=>$task){
                        $ukupni_smjestaj[] = array("number"=>$_POST['accommodation1'][$key], "amount"=>$_POST['accommodation2'][$key], "total"=>$_POST['accommodation4'][$key]);
                    }
                    foreach($ukupni_smjestaj as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] (
          number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }




                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }



                    $ukupni_prijevoz  = array();
                    foreach($_POST['transport1'] as $key=>$task){
                        $ukupni_prijevoz[] = array("number"=>$_POST['transport1'][$key], "amount"=>$_POST['transport2'][$key], "total"=>$_POST['transport4'][$key]);
                    }

                    foreach($ukupni_prijevoz as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_transport] (
         number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }




                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_transport] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_transport] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }





                    $ukupni_ostalitroskovi  = array();
                    foreach($_POST['otherCosts1'] as $key=>$task){
                        $ukupni_ostalitroskovi[] = array("number"=>$_POST['otherCosts1'][$key], "amount"=>$_POST['otherCosts2'][$key], "total"=>$_POST['otherCosts4'][$key]);
                    }
                    foreach($ukupni_ostalitroskovi as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] (
         number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        //echo $ukupni_test=$task["total"][$task];

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }





                    echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';

                    ?>
                    <?php



                    $query = $db->query("SELECT MAX(request_id) as maxrequest FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE user_id='".$_user['user_id']."'");

                    foreach($query as $item){
                        $tools_id = $item['maxrequest'];
                    }



                    /*********************************************/
                    /***************PHP MAILER********************/
                    /*********************************************/
                    require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

                    $mail = new PHPMailer(true);
                    $mail->CharSet = "UTF-8";


                    $mail->isSMTP();
//$mail->Host = '91.235.170.162';
                    $mail->Host = gethostbyname('xmail.teneo.ba');                   // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                            // Enable SMTP authentication
                    $mail->Username = 'nav@teneo.ba';          // SMTP username
                    $mail->Password = 'DynamicsNAV16!'; // SMTP password

                    $mail->Port = 587;


                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;                              // TCP port to connect to

                    $mail->setFrom('nav@teneo.ba', 'Employee Portal Info');
//$mail->addReplyTo('irma.hrelja@infodom.ba', 'Irma');
//$mail->addAddress($email_stream_parent);
//$mail->addAddress($email_admin);
//$mail->addAddress($email_admin2);
                    $mail->addAddress('nav@teneo.ba');
                    // Add a recipient
//$mail->addBCC('bcc@example.com');

                    $mail->isHTML(true);  // Set email format to HTML

                    $bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za Službeni put za'.' '.$fname.' '.$lname.' '.' zaposlenika na radnom mjestu'.' '.$position.' '.' u  '.' '.$departmentcode.' '.' u periodu od '.' '.date('d-m-Y', strtotime($fromD)).' '.' do'.' '.date('d-m-Y', strtotime($toD)).' '.'. </h5 >' ;
                    $bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip_reponse&id='.$tools_id.'>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
                    $mail->Subject = 'Employee Portal!';
                    $mail->Body    = $bodyContent;

                    if(!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else { ?>


                    <?php }



                }
            }

            /******************INO DNEVNICE START ********************/
            if ($_POST['countryino'] != 1)
            {

                $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip] (
      user_id,parent,hr,admin,admin2,date_created,h_from,h_to,date_determined,date_of_calculation,zahtjev_vs_nalog,status,status_parent2,status_hr,status_admin,type,is_archive,send_email,
      destination,country_ino,purpose_trip,reasons,time_of_seminar,
      check_list1,check_list2,transport,transport_details,number_of_employee,
      check_list3,transport_notes,accommodation,accommodation_details,
      check_list4,check_list5,limit_notes,check_list6,total_f,amount_f,number_f) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array($_user['user_id'],$_user['parent'],$_user['stream_parent'],$_user['to_admin'],$_user['to_admin2'],
                        date('Y-m-d', strtotime("now")),
                        date('Y-m-d', strtotime($fromD)).' '.$_POST['TimeFrom'],
                        date('Y-m-d', strtotime($toD)).' '.$_POST['TimeTo'],
                        date('Y-m-d', strtotime($dateD)),
                        date('Y-m-d', strtotime($dateC)),
                        '0',
                        '0',
                        '0',
                        '0',
                        '0',
                        'SLUŽBENI PUT INO',
                        '0',
                        '0',
                        $_POST['destination'],$_POST['countryino'],$_POST['purpose_trip'],$_POST['reasons'],$_POST['TimeOfSeminar'],
                        $_POST['check_list1'],$_POST['check_list2'],$_POST['transport'],$_POST['transport_details'],$_POST['numberOfEmployee'],
                        $_POST['check_list3'],$_POST['transport_notes'],$_POST['atribut'],$_POST['accommodation_details'],
                        $_POST['check_list4'],$_POST['check_list5'],$_POST['limit_notes'],$_POST['check_list6'],$_POST['total_f'],$_POST['amount_f'],$_POST['number_f']));
                if($res->rowCount()==1)

                {



                    $id = $db->lastInsertId();

                    $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip] SET
    request_id_NAV = ?
    WHERE request_id = ?";
                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $id,
                            $id,

                        )
                    );


                    $ukupni  = array();
                    foreach($_POST['task'] as $key=>$task){
                        $ukupni[] = array("number"=>$_POST['task'][$key], "total"=>$_POST['task4'][$key],"amount"=>$_POST['amount'][$key],"country"=>$_POST['country'][$key]);

                    }


                    foreach($ukupni as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_item] (
        number,total,amount,country,date_completed,request_id) VALUES (?,?,?,?,?,?)";


                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["total"],
                                $task["amount"],
                                $task["country"],
                                date('Y-m-d', strtotime("now")),
                                $id

                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }

                    }



                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_item] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_item] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }

                    $ukupni_smjestaj  = array();
                    foreach($_POST['accommodation1'] as $key=>$task){
                        $ukupni_smjestaj[] = array("number"=>$_POST['accommodation1'][$key], "amount"=>$_POST['accommodation2'][$key], "total"=>$_POST['accommodation4'][$key]);
                    }
                    foreach($ukupni_smjestaj as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] (
          number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }




                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }



                    $ukupni_prijevoz  = array();
                    foreach($_POST['transport1'] as $key=>$task){
                        $ukupni_prijevoz[] = array("number"=>$_POST['transport1'][$key], "amount"=>$_POST['transport2'][$key], "total"=>$_POST['transport4'][$key]);
                    }

                    foreach($ukupni_prijevoz as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_transport] (
         number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }




                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_transport] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_transport] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }





                    $ukupni_ostalitroskovi  = array();
                    foreach($_POST['otherCosts1'] as $key=>$task){
                        $ukupni_ostalitroskovi[] = array("number"=>$_POST['otherCosts1'][$key], "amount"=>$_POST['otherCosts2'][$key], "total"=>$_POST['otherCosts4'][$key]);
                    }
                    foreach($ukupni_ostalitroskovi as $key=>$task){
                        $data2 = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] (
         number,amount,total,date_completed,request_id) VALUES (?,?,?,?,?)";

                        //echo $ukupni_test=$task["total"][$task];

                        $res2 = $db->prepare($data2);
                        $res2->execute(
                            array(
                                $task["number"],
                                $task["amount"],
                                $task["total"],
                                date('Y-m-d', strtotime("now")),
                                $id
                            )
                        );
                        if($res2->rowCount()==1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] WHERE request_id='".$id."'");
                    foreach ($id2 as $value) {
                        $item=$value['tripitem_id'];

                        $data = "UPDATE [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] SET
    request_id_NAV = ?,
    tripitem_id_NAV = ?
    WHERE request_id = ? and tripitem_id= ?";
                        $res = $db->prepare($data);
                        $res->execute(
                            array(
                                $id,
                                $item,
                                $id,
                                $item

                            )
                        );

                    }





                    echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';

                    ?>
                    <?php



                    $query = $db->query("SELECT MAX(request_id) as maxrequest FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE user_id='".$_user['user_id']."'");

                    foreach($query as $item){
                        $tools_id = $item['maxrequest'];
                    }



                    /*********************************************/
                    /***************PHP MAILER********************/
                    /*********************************************/
                    require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

                    $mail = new PHPMailer(true);
                    $mail->CharSet = "UTF-8";


                    $mail->isSMTP();
//$mail->Host = '91.235.170.162';
                    $mail->Host = gethostbyname('xmail.teneo.ba');                   // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                            // Enable SMTP authentication
                    $mail->Username = 'nav@teneo.ba';          // SMTP username
                    $mail->Password = 'DynamicsNAV16!'; // SMTP password

                    $mail->Port = 587;


                    $mail->SMTPSecure = false;
                    $mail->SMTPAutoTLS = false;                              // TCP port to connect to

                    $mail->setFrom('nav@teneo.ba', 'Employee Portal Info');

//$mail->addAddress($email_parent);
//$mail->addAddress($email_admin);
//$mail->addAddress($email_admin2);

                    $mail->addAddress('nav@teneo.ba');
                    // Add a recipient

//$mail->addBCC('bcc@example.com');

                    $mail->isHTML(true);  // Set email format to HTML

                    $bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >' ;
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za Službeni put za'.' '.$fname.' '.$lname.' '.' zaposlenika na radnom mjestu'.' '.$position.' '.' u  '.' '.$departmentcode.' '.' u periodu od '.' '.date('d-m-Y', strtotime($fromD)).' '.' do'.' '.date('d-m-Y', strtotime($toD)).' '.'. </h5 >' ;
                    $bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip_reponse&id='.$tools_id.'>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >' ;
                    $mail->Subject = 'Employee Portal!';
                    $mail->Body    = $bodyContent;

                    if(!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else { ?>


                    <?php }



                }
            }

            /*******INO DNEVNICE END *****************************/



        }else echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Datum završetka ne može biti manji od datuma početka usavršavanja!').'</div>';

    }

    if($_POST['request']=='get-info'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $godina=date("Y");

        $neodobreno_query=" and review_status = 0";
        $odobreno_query=" and review_status = 1";
        $rejected_query = " and status_rejected is not null";
        $neodobreno_query_corrections=" and corr_review_status = 0";
        $odobreno_query_corrections=" and corr_review_status = 1";
        $employee_query = " and employee_no =".$_user['employee_no'];
        $praznici_query = " and (KindOfDay <>'BHOLIDAY')";

        /*
  $get_accepted = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   status<>999 ".$employee_query.$odobreno_query.$praznici_query.")
   and year = ".$godina." GROUP BY [request_id]");
     $result2 = $get_accepted->fetchAll();*/
        $total_accepted= getNotificationsNovaOdsustvaRadnik('', 'true', '');


        /*
         $get_pending = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
       join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
       WHERE
   (
   status<>5 ".$employee_query.$neodobreno_query.$praznici_query.")
   and year = ".$godina." GROUP BY [request_id]");
     $result2 = $get_pending->fetchAll();*/
        $total_pending=getNotificationsNovaOdsustvaRadnik('', 'false', '');

        /*
    $get_accepted_corrections = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   corr_status<>5 ".$employee_query.$odobreno_query_corrections.$praznici_query.")
   and year = ".$godina." GROUP BY [request_id]");
     $result2 = $get_accepted_corrections->fetchAll();*/
        $total_accepted_corrections=getNotificationsNovaOdsustvaRadnik('corrections', 'true', '');

        /*
         $get_pending_corrections = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
       join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
       WHERE
   (
   corr_status<>5 ".$employee_query.$neodobreno_query_corrections.$praznici_query.")
   and year = ".$godina." GROUP BY [request_id]");
     $result2 = $get_pending_corrections->fetchAll();*/
        $total_pending_corrections=getNotificationsNovaOdsustvaRadnik('corrections', 'false', '');

        /*
           $get_rejected = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
       join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
       WHERE
   (
   status=5 ".$employee_query.$neodobreno_query.$rejected_query.$praznici_query.")
   and year = ".$godina." and change_req != 2
   and timest_edit is not null GROUP BY [request_id]");
     $result2 = $get_rejected->fetchAll();*/
        $total_rejected=getNotificationsNovaOdsustvaRadnik('', 'rejected', '');

        /*
    $get_accepted_cancel = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   weekday<>'6' AND weekday<>'7'  ".$employee_query.$praznici_query.") and change_req=2 and status=5
   and year = ".$godina." GROUP BY [request_id]");
     $result3 = $get_accepted_cancel->fetchAll();*/
        $total_accepted_cancel=getNotificationsNovaOdsustvaRadnik('', '', 'true');

        /*
    $get_rejected_cancel = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   weekday<>'6' AND weekday<>'7'  ".$employee_query.$praznici_query.") and change_req='0' and review_status != 0
   and year = ".$godina." GROUP BY [request_id]");
     $result3 = $get_rejected_cancel->fetchAll();*/
        $total_rejected_cancel=getNotificationsNovaOdsustvaRadnik('', '', 'false');

        echo $total_accepted.','.$total_pending.','.$total_accepted_corrections.','.$total_pending_corrections.','.$total_rejected.','.$total_accepted_cancel.','.$total_rejected_cancel;

    }

    if($_POST['request']=='get-info_workers'){

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $godina=date("Y");

        $neodobreno_query=" and review_status = 0";
        $corr_neodobreno_query=" and corr_review_status = 0";
        $odobreno_query=" and review_status = 1";
        $corr_odobreno_query=" and corr_review_status = 1";
        $employee_query = " and employee_no =".$_user['employee_no'];
        $praznici_query = " and (KindOfDay <>'BHOLIDAY')";

//za admina

        if($_user['role']==4 or $_user['role']==0){
            $get2 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)");
            $result = $get2->fetch();
            $total_users=$result[0];

            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";


            /*
    $get_absences = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   status<>5 ".$role_query.$neodobreno_query.$praznici_query.") GROUP BY [request_id]");




     $result = $get_absences->fetchAll();*/

            $total_absences = getNotificationsNovaOdsustva('', false);

            /*
      $get_absences_corr = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
     join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   corr_status<>5 ".$role_query.$corr_neodobreno_query.$praznici_query."  and review_status != 1) GROUP BY [request_id]");
   //print_r($get_absences_corr);

     $result = $get_absences_corr->fetchAll();
   */
            $total_absences_corr=getNotificationsNovaOdsustva('corrections', false);


            /*
      $get_chane_req = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
     join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and change_req=1 ".$role_query.$odobreno_query.$praznici_query.") GROUP BY [request_id]");


    $result1 = $get_chane_req->fetchAll();*/

            $total_change_req=getNotificationsNovaOdsustva('', true);

            /*
      $get_chane_req_corr = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   weekday<>'6' AND weekday<>'7' and corr_status<>5 and corr_change_req=1 ".$role_query.$corr_odobreno_query.$praznici_query.") GROUP BY [request_id]");

     $result1 = $get_chane_req_corr->fetchAll();

      $total_change_req_corr=count($result1);*/
            $total_change_req_corr = getNotificationsNovaOdsustva('corrections', true);

        }

        //za parenta

        elseif($_user['role']==2){
            $get2 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent='".$_user['employee_no']."')");
            $result = $get2->fetch();
            $total_users=$result[0];

            $role_query = " and employee_no in (SELECT employee_no FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (parent=".$_user['employee_no']."))";
            /*
   $get_absences = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
   WHERE
   (
   status<>5 ".$role_query.$neodobreno_query.$praznici_query.") GROUP BY [request_id]");

     $result = $get_absences->fetchAll();*/
            $total_absences = getNotificationsNovaOdsustva('', false);

            /*
        $get_absences_corr = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
        join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
      WHERE
   (
   corr_status<>5 ".$role_query.$corr_neodobreno_query.$praznici_query."  and review_status != 1) GROUP BY [request_id]");

    $result = $get_absences_corr->fetchAll();*/
            $total_absences_corr=getNotificationsNovaOdsustva('corrections', false);

            /*
        $get_chane_req = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
       join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
      WHERE
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and change_req=1 ".$role_query.$odobreno_query.$praznici_query.") GROUP BY [request_id]");

     $result1 = $get_chane_req->fetchAll();*/
            $total_change_req=getNotificationsNovaOdsustva('', true);
            /*
      $get_chane_req_corr = $db->query("SELECT [request_id] FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]
    join [c0_intranet2_apoteke].[dbo].[hourlyrate_year] on
  [c0_intranet2_apoteke].[dbo].[hourlyrate_day].year_id = [c0_intranet2_apoteke].[dbo].[hourlyrate_year].id
    WHERE
   (
   weekday<>'6' AND weekday<>'7' and corr_status<>5 and corr_change_req=1 ".$role_query.$corr_odobreno_query.$praznici_query.") GROUP BY [request_id]");

     $result1 = $get_chane_req_corr->fetchAll();*/
            $total_change_req_corr = getNotificationsNovaOdsustva('corrections', true);

        }
        echo $total_absences.','.$total_absences_corr.','.$total_change_req.','.$total_change_req_corr;
    }
}

if(isset($_GET['request'])){
    if($_GET['request']=='check-month-add'){

        $query = $db->query("SELECT count(*) as broj FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month]  where month = ".$_GET['month']);

        foreach ($query as $item) {
            $num_users = $item['broj'];
        }
        echo $num_users;
    }
}





?>
