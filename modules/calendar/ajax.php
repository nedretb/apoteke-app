<?php

require_once '../../configuration.php';
require_once '../../configuration.php';

if (DEBUG) {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

}


if (isset($_POST['request'])) {

    if ($_POST['request'] == 'tasks-add') {


        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $date_end = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['final_date'])));


        //task count checks

        //individualni
        $check_ind = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_tasks . "  WHERE task_type=0 and (status NOT IN (4,5) or status is null) and ponder<>0 and user_id=" . $_user['user_id'] . " AND year = " . date("Y"));
        $ind_count1 = $check_ind->fetch();
        $ind_count = $ind_count1['broj'];

        if ($ind_count >= 7 and $_POST['task_type'] == 0) {
            echo '<div class="alert alert-danger text-center">' . __('Maximalan broj individualnih ciljeva je 7') . '</div><br/>';
            return;
        }

        //timski
        $check_team = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_tasks . "  WHERE task_type=1 and (status NOT IN (4,5) or status is null) and ponder<>0 and user_id=" . $_user['user_id'] . " AND year = " . date("Y"));
        $team_count1 = $check_team->fetch();
        $team_count = $team_count1['broj'];

        if ($team_count >= 3 and $_POST['task_type'] == 1) {
            echo '<div class="alert alert-danger text-center">' . __('Maximalan broj timskih ciljeva je 3') . '</div><br/>';
            return;
        }

        //ponder checks

        if (isset($_POST['ponder'])) {

            $check_ponder = $db->query("SELECT SUM(ponder) as ponder_sum FROM  " . $portal_tasks . "  WHERE user_id=" . $_user['user_id'] . " AND task_type in (0,1) and (status NOT IN (4,5) or status is null) AND year = " . date("Y"));
            $check_ponder1 = $check_ponder->fetch();
            $ponder_sum = $check_ponder1['ponder_sum'];

            if ($ponder_sum == 100) {
                echo '<div class="alert alert-danger text-center">' . __('Suma pondera je 100, unos dodatnog cilja je nedozvoljen') . '</div><br/>';
                return;
            }

            if ($ponder_sum + $_POST['ponder'] > 100) {
                echo '<div class="alert alert-danger text-center">' . __('Suma pondera prelazi 100, unos cilja je nedozvoljen') . '</div><br/>';
                return;
            }
            $ponder = $_POST['ponder'];
            $kpi = $_POST['task_kpi'];
        } else {
            $ponder = NULL;
            $kpi = "";
        }


        $data = "INSERT INTO  " . $portal_tasks . "  (
    task_name,task_description,KPI,is_accepted,user_id,employee_no,year,parent_id,hr_id,admin_id,task_type,ponder,date_created,date_end,origin,phase) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";


        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['task_name'],
                $_POST['task_description'],
                $kpi,
                '0',
                $_user['user_id'], $_user['employee_no'], date("Y"), $_user['parent'], $_user['hr'], $_user['admin'],
                $_POST['task_type'],
                $ponder,
                date('Y-m-d', strtotime("now")),
                $date_end,
                'PORTAL',
                1


            )
        );
        if ($res->rowCount() == 1) {

            $id = $db->lastInsertId();

            $data = "UPDATE  " . $portal_tasks . "  SET
      task_id_NAV = ?
		WHERE task_id = ?";
            $res = $db->prepare($data);
            $res->execute(
                array(
                    $id,
                    $id,

                )
            );

            if (strlen($_POST['comment']) > 1) {

                $data3 = "INSERT INTO  " . $portal_comments . "  (
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
                if ($res3->rowCount() == 1) {
                    echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
                    //echo $ponder_sum+$_POST['ponder'];
                }

            } else {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
                //echo $ponder_sum+$_POST['ponder'];
            }
            $data = _updateCiljevi($_user['user_id']);
            _updateLastChange($id);
        }

    }

    if ($_POST['request'] == 'holiday-add') {
        error_reporting(E_ALL);

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['date'])));

        if (in_array(1, $_POST['orgjed'])){

            $orgJedName = $db->query("select * from [c0_intranet2_apoteke].[dbo].[systematization] where id=1")->fetch()['s_title'];

            $data = "INSERT INTO  " . $portal_holidays_per_department . "  (
    [department name],[date],[holiday_type],[holiday_name],[Hr_status],[Pomicni],[tip]) VALUES (?,?,?,?,?,?,?)";


            $res = $db->prepare($data);
            $res->execute(
                array(
                    $orgJedName,
                    $date,
                    'BHOLIDAY',
                    $_POST['holiday_name'],
                    'PR_1',
                    $_POST['pomicni'],
                    'all'
                )
            );
        }
        else{
            foreach ($_POST['orgjed'] as $key => $value){

                try {

                    $orgJedName = $db->query("select * from [c0_intranet2_apoteke].[dbo].[systematization] where id=".$value)->fetch()['s_title'];

                    $data = "INSERT INTO  " . $portal_holidays_per_department . "  (
    [department name],[date],[holiday_type],[holiday_name],[Hr_status],[Pomicni],[tip]) VALUES (?,?,?,?,?,?,?)";


                    $res = $db->prepare($data);
                    $res->execute(
                        array(
                            $orgJedName,
                            $date,
                            'BHOLIDAY',
                            $_POST['holiday_name'],
                            'PR_1',
                            $_POST['pomicni'],
                            'orgjed'
                        )
                    );
                } catch (Exception $e){}
            }
        }

        if ($res->rowCount() == 1){
            _addHoliday($_POST['holiday_name'], $orgJedName, $_POST['date']);
        }



        echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

    }

    if ($_POST['request'] == 'project-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $date_from = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['date_from'])));
        $date_to = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['date_to'])));

        if (isset($_POST['is_end_date'])) {
            $is_end_date = 1;
            $ds = explode('/', $_POST['final_date']);
            $date_end = $ds[2] . '-' . $ds[1] . '-' . $ds[0];

        } else {
            $is_end_date = null;
            $date_end = null;

        }

        $data = "INSERT INTO  " . $portal_projects . "  (
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

        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'language-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        if (isset($_POST['is_end_date'])) {
            $is_end_date = 1;
            $ds = explode('/', $_POST['final_date']);
            $date_end = $ds[2] . '-' . $ds[1] . '-' . $ds[0];

        } else {
            $is_end_date = null;
            $date_end = null;

        }

        $data = "INSERT INTO  " . $portal_language_skills . "  (
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

        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'certifikat-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $zavrsetak = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['zavrsetak'])));

        if (isset($_POST['is_end_date'])) {
            $is_end_date = 1;
            $ds = explode('/', $_POST['final_date']);
            $date_end = $ds[2] . '-' . $ds[1] . '-' . $ds[0];

        } else {
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

        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'remove-holiday_remove') {

        $this_id = $_POST['request_id'];
        _removeHoliday($this_id);
        $data = "DELETE FROM  " . $portal_holidays_per_department . "  WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {

        }

    }

    if ($_POST['request'] == 'remove-project_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM  " . $portal_projects . "  WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }

    if ($_POST['request'] == 'remove-language_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM  " . $portal_language_skills . "  WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }

    if ($_POST['request'] == 'remove-certifikat_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM [c0_intranet2_apoteke].[dbo].[Certifikati] WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }

    //TASKS

    if ($_POST['request'] == 'change-ocjena_timski_user') {

        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];


        $data = "UPDATE  " . $portal_tasks . "  SET
      user_rating = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {

            echo 1;
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'change-ocjena_timski') {


        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];

        $query = $db->query("SELECT * FROM  " . $portal_tasks . "  WHERE task_id = " . $task_id);
        if ($query->rowCount() < 0) {
            foreach ($query as $item) {
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE  " . $portal_tasks . "  SET
      rating = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {

            $data = _updateCiljevi($user_id);
            _updateLastChange($task_id);

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-ocjena_individualni_user') {

        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];


        $data = "UPDATE  " . $portal_tasks . "  SET
      user_rating = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-ocjena_individualni') {

        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];

        $query = $db->query("SELECT * FROM  " . $portal_tasks . "  WHERE task_id = " . $task_id);
        if ($query->rowCount() < 0) {
            foreach ($query as $item) {
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE  " . $portal_tasks . "  SET
      rating = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ocjena'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {
            $data = _updateCiljevi($user_id);
            _updateLastChange($task_id);
            echo 1;
        } else {
            echo 'otkljucan';
        }


    }

    if ($_POST['request'] == 'change-task-status') {

        $query = $db->query("SELECT * FROM  " . $portal_tasks . "  WHERE task_id = " . $_POST['task_id']);
        if ($query->rowCount() < 0) {
            foreach ($query as $item) {
                $user_id = $item['user_id'];
            }
        }


        $data = "UPDATE  " . $portal_tasks . "  SET
      status = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['status'],
                $_POST['task_id'],

            )
        );
        if ($res->rowCount() == 1) {
            $data = _updateCiljevi($user_id);
            _updateLastChange($_POST['task_id']);
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-task-ostvarenje') {

        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];


        $data = "UPDATE  " . $portal_tasks . "  SET
      ostvarenje = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['ostvarenje'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {
            _updateLastChange($task_id);
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-task-KPI') {

        $pieces = explode("-", $_POST['task_id']);
        $task_id = $pieces[1];


        $data = "UPDATE  " . $portal_tasks . "  SET
      KPI = ?
		WHERE task_id = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['KPI'],
                $task_id,

            )
        );
        if ($res->rowCount() == 1) {
            _updateLastChange($task_id);
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    //misc user fields

    if ($_POST['request'] == 'change-ambicije') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-mobilnost') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-lokacija') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-vjestina') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-nivo') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    //misc parent fields

    if ($_POST['request'] == 'change-rizik_gubitka') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-uticaj_gubitka') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-razlog_odlaska') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-karijera') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-novi_zaposlenik') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-pozicija') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-spremnost') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-prezime_ime') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-datum') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];

        $newDate = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['datum'])));


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    //KOMPETENCIJE

    if ($_POST['request'] == 'change-obavezna1_user') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obavezna2_user') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obavezna1') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            $data = _updateKompetencije($user_id);
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obavezna2') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            $data = _updateKompetencije($user_id);
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija1') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija1_rating_user') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija1_rating') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            $data = _updateKompetencije($user_id);
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija2') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija2_rating_user') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija2_rating') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            $data = _updateKompetencije($user_id);
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija3') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija3_rating_user') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-kompetencija3_rating') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            $data = _updateKompetencije($user_id);
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-komentar') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-l_potencijal') {


        $user_id = $_POST['user_id'];


        $data = "UPDATE  " . $portal_misc . "  SET
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
        if ($res->rowCount() == 1) {


        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'remove-tasks_remove') {
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $this_id = $_POST['request_id'];
        $data = "DELETE FROM  " . $portal_tasks . "  WHERE task_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            $data1 = "DELETE FROM  " . $portal_comments . "  WHERE comment_on = ? AND type = ?";
            $delete1 = $db->prepare($data1);
            $delete1->execute(array($this_id, 'task'));

            $data = _updateCiljevi($_user['user_id']);
        }

    }

    //SLANJE PORUKA

    if ($_POST['request'] == 'send-nadredjenom') {
        $this_id = $_POST['request_id'];
        $params = explode('-', $this_id);
        $user_id = $params[0];
        $faza = $params[1];

        // ukupni checks

        $check_ukupni = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_tasks . "  WHERE task_type in (0,1) and (status NOT IN (4,5) or status is null) and ponder<>0 and year = " . date("Y") . " and user_id=" . $user_id);
        $ukupni_count1 = $check_ukupni->fetch();
        $ukupni_count = $ukupni_count1['broj'];

        if ($ukupni_count < 5) {
            echo "ukupni_ispod";
            return;
        }

        // individualni checks

        $check_ind = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_tasks . "  WHERE task_type=0 and (status NOT IN (4,5) or status is null) and ponder<>0 and year = " . date("Y") . " and user_id=" . $user_id);
        $ind_count1 = $check_ind->fetch();
        $ind_count = $ind_count1['broj'];

        if ($ind_count < 4) {
            echo "ind_ispod";
            return;
        }

        //timski checks

        $check_team = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_tasks . "  WHERE task_type=1 and (status NOT IN (4,5) or status is null) and ponder<>0 and year = " . date("Y") . " and user_id=" . $user_id);
        $team_count1 = $check_team->fetch();
        $team_count = $team_count1['broj'];
        if ($team_count < 1) {
            echo "tim_ispod";
            return;
        }

        //ponder checks

        $check_ponder = $db->query("SELECT SUM(ponder) as ponder_sum FROM  " . $portal_tasks . "  WHERE user_id=" . $user_id . " AND task_type in (0,1) and year = " . date("Y") . " and (status NOT IN (4,5) or status is null)");
        $check_ponder1 = $check_ponder->fetch();
        $ponder_sum = $check_ponder1['ponder_sum'];

        if ($ponder_sum != 100) {
            echo "ponder_ispod";
            return;
        }

        //status check (faza 2)
        if ($faza == 2) {
            $check_status = $db->query("SELECT count(*) as broj FROM  " . $portal_tasks . "  WHERE user_id=" . $user_id . " AND ((task_type in (0,1) and ponder<>0) or (task_type=2 and ponder is null)) and year = " . date("Y") . " and (status = '' or status is null or status=0)");
            $check_status1 = $check_status->fetch();
            $status = $check_status1['broj'];

            if ($status > 0) {
                echo "status_ispod";
                return;
            }
        }

        //ocjena check (faza 3)
        if ($faza == 3) {
            $check_status = $db->query("SELECT count(*) as broj FROM  " . $portal_tasks . "  WHERE user_id=" . $user_id . " AND task_type in (0,1) and (status NOT IN (4,5) or status is null) and ponder<>0 and year = " . date("Y") . " and (user_rating = '' or user_rating is null or user_rating=0)");
            $check_status1 = $check_status->fetch();
            $status = $check_status1['broj'];

            $check_status_kompetencije = $db->query("SELECT count(*) as broj FROM  " . $portal_misc . "  WHERE user_id=" . $user_id . " AND 
	year = " . date("Y") . " and (
	(kompetencija1_rating_user = '' or kompetencija1_rating_user is null or kompetencija1_rating_user=0) or (kompetencija2_rating_user = '' or kompetencija2_rating_user is null or kompetencija2_rating_user=0) or (kompetencija3_rating_user = '' or kompetencija3_rating_user is null or kompetencija3_rating_user=0) or (obavezna1_rating_user = '' or obavezna1_rating_user is null or obavezna1_rating_user=0) or (obavezna2_rating_user = '' or obavezna2_rating_user is null or obavezna2_rating_user=0))");
            $check_status1_kompetencije = $check_status_kompetencije->fetch();
            $status_kompetnecije = $check_status1_kompetencije['broj'];

            if ($status > 0 or $status_kompetnecije > 0) {
                echo "ocjena_ispod";
                return;
            }
        }


        $data = "UPDATE  " . $portal_objective_status . "  SET
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
        if ($res->rowCount() == 1) {
            $data = "UPDATE  " . $portal_tasks . "  SET
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

    if ($_POST['request'] == 'send-potpisuje_radnik') {
        $this_id = $_POST['request_id'];
        $params = explode('-', $this_id);
        $user_id = $params[0];
        $faza = $params[1];

        $data = "
	  
	  declare @status nvarchar(MAX)
set @status = (select status from  " . $portal_objective_status . "  where user_id=" . $user_id . " and phase =" . $faza . " and year = " . date("Y") . ")
if((@status like '%potpisao_nadredjeni;%') or (@status like '%poslano_na_potpisivanje;%'))
set @status = @status + 'potpisao_radnik;'
else
set @status = 'potpisao_radnik;'
	  
	  
	  UPDATE  " . $portal_objective_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        }
    }

    if ($_POST['request'] == 'potvrda_razgovora') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_objective_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'potvrda_razgovora_nadredjeni') {

        $pieces = explode("-", $_POST['user_id']);
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_objective_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }


    if ($_POST['request'] == 'request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        /* functiotran date_normalizer ($d) {
    if ($d instanceof DateTime) {
      echo $d->GetTimestamp();
    }
    else
    {
      echo strtotime($d);
    }
  }strtotime($d);*/

        $data = "INSERT INTO  " . $portal_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive) VALUES (?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")), date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to)), '0', 'GO', '0'));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'travel-request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $ds = explode('/', $_POST['from']);
        $de = explode('/', $_POST['to']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $to = $de[2] . '-' . $de[1] . '-' . $de[0];

        /* function date_normalizer ($d) {
    if ($d instanceof DateTime) {
      echo $d->GetTimestamp();
    }
    else
    {
      echo strtotime($d);
    }
  }strtotime($d);*/

        $data = "INSERT INTO  " . $portal_travel_requests . "  (
      user_id,parent_id,date_created,h_from,h_to,status,type,is_archive,country,travel_route,comment,total_cost) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $res = $db->prepare($data);
        $res->execute(
            array($_user['user_id'], $_user['parent'], date('Y-m-d', strtotime("now")),
                date('Y-m-d', strtotime($from)),
                date('Y-m-d', strtotime($to)),
                '0',
                'SLUŽBENI PUT',
                '0',
                $_POST['country'], $_POST['travel_route'], $_POST['comment'], $_POST['total_cost']));
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'year-add') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $_POST['year'] . "'")->rowCount();
        //echo $check;
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Godinu koju ste odabrali već postoji!') . '</div><br/>';

        } else {

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . " ");

            $total = $get2->rowCount();


            foreach ($query as $item) {

                //echo $absence_id;
                $absence_year_id = $item['user_id'];


                $data = "INSERT INTO  " . $portal_hourlyrate_year . " (
     user_id,year) VALUES (?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $absence_year_id,
                        $_POST['year']
                    )
                );
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

            }


        }
    }

    if ($_POST['request'] == 'year-add-complete') {

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $_POST['year'] . "'")->rowCount();
        //echo $check;
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Godinu koju ste odabrali već postoji!') . '</div><br/>';

        } else {

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $query = $db->query("SELECT [user_id] FROM  " . $portal_users . " ");
            $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_users . " ");
            $query2 = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . " ");

            $total = $get2->rowCount();


            foreach ($query as $item) {

                //echo $absence_id;
                $absence_year_id = $item['user_id'];


                $data = "INSERT INTO  " . $portal_hourlyrate_year . " (
     user_id,year) VALUES (?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $absence_year_id,
                        $_POST['year']
                    )
                );
            }
            if ($res->rowCount() == 1) {
                //   $poruka = _addAllMonths4($_POST['year']);
                // echo $poruka;
            }


        }
    }


    if ($_POST['request'] == 'month-add') {
        session_write_close();

        $check = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE month='" . $_POST['month'] . "' AND year_id='" . $_POST['year'] . "'")->rowCount();
        //echo $check;
        if ($check < 0) {

            echo '<div class="alert alert-danger text-center">' . __('Mjesec koji ste odabrali već postoji!') . '</div><br/>';

        } else {


            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $now = new DateTime();
            $filteryear = $now->format('Y');
            $filtermonth = $now->format('M');
            $filtertdate = $filteryear . "-" . $filtermonth . "-1 00:00:00.000";
            $query_month = $db->query("SELECT [user_id] FROM  " . $portal_users . "  where ((termination_date>='" . $filtertdate . "') or
      (termination_date is null))");
            $get_month = $db->query("SELECT COUNT(*) FROM  " . $portal_users . "  where ((termination_date>='" . $filtertdate . "') or
      (termination_date is null))	 ");
            $yearcurr = $db->query("SELECT [year] FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_POST['year'] . "'");
            $total = $get_month->rowCount();
            foreach ($yearcurr as $value2) {
                $absence_year = $value2['year'];
            }

            foreach ($query_month as $item) {

                $month = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $absence_year . "'");

                $absence_id_month = $item['user_id'];
                foreach ($month as $value) {
                    $absence_month = $value['id'];
                }

                $_user = _user(_decrypt($_SESSION['SESSION_USER']));
                $emp_no = $db->query("SELECT employee_no,department_code,employment_date FROM  " . $portal_users . "  where [user_id]='" . $item['user_id'] . "'
	and ((termination_date>='" . $filtertdate . "') or (termination_date is null))	");
                foreach ($emp_no as $value3) {
                    $employee_no = $value3['employee_no'];
                    $edate = DateTime::createFromFormat("Y-m-d", $value3['employment_date']);

                    $data = "INSERT INTO  " . $portal_hourlyrate_month . " (id,
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


                    $query_calendar = $db->query("SELECT [day],[weekday],[KindOfDay],[Description],[Hr_status] FROM  " . $portal_calendar . "  where [month]='" . $_POST['month'] . "'
   and  [year]='" . $absence_year . "'");

                    foreach ($query_calendar as $cal) {
                        $day = $cal ['day'];
                        $weekday = $cal ['weekday'];
                        $kind = $cal ['KindOfDay'];
                        $desc = $cal ['Description'];
                        $hrstat = $cal['Hr_status'];
                        if ($kind == 'BANKDAY') {
                            $status = '5';
                        }
                        if ($kind == 'CHOLIDAY') {
                            $status = '5';
                        }
                        if ($kind == 'HOLIDAY') {
                            $query_status = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_status . "  where [name]='" . $hrstat . "'
   ");

                            foreach ($query_status as $calstat) {
                                $status = $calstat['id'];
                            }
                        }
                        if (($kind != 'BANKDAY') and ($kind != 'CHOLIDAY') and ($kind != 'HOLIDAY')) {
                            $status = '5';
                        }
                        if (($kind != 'BANKDAY') and ($kind != 'CHOLIDAY') and ($kind != 'HOLIDAY')) {
                            $status = '5';
                        }
                        $data = "INSERT INTO  " . $portal_hourlyrate_day . "  (
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
                            );
                        }

                    }
                }
            }
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
            }

        }

    }

    if ($_POST['request'] == 'check-month-add') {

        $query = $db->query("SELECT count(*) as broj FROM  " . $portal_hourlyrate_month . "   where month = " . $_POST['month']);

        foreach ($query as $item) {
            $num_users = $item['broj'];
        }
        echo $num_users;
    }

    if ($_POST['request'] == 'check-month-add-new') {

        $query = $db->query("SELECT count(*) as broj FROM  " . $portal_hourlyrate_month . " ");

        foreach ($query as $item) {
            $num_users = (18 / 2);
        }
        echo (string)$num_users;

    }

    if ($_POST['request'] == 'parent-day-add') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];


        $query = $db->query("SELECT [day] FROM  " . $portal_hourlyrate_day . "   where  month_id='$getMonth' AND year_id='$getYear'");


        foreach ($query as $item) {


            if ($item['day'] >= $FromDay && $item['day'] <= $ToDay) {

                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
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

            }
        }

        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }

    if ($_POST['request'] == 'parent-day-add_apsolute') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $status = $_POST['status'];

        $FromDay = $_POST['dateFrom'];
        $ToDay = $_POST['dateTo'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $dateFrom = strtotime(str_replace("/", "-", $FromDay));
        $dateTo = strtotime(str_replace("/", "-", $ToDay));
        $datediff = $dateTo - $dateFrom;
        $day_difference = floor($datediff / (60 * 60 * 24)) + 1;


        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));

        $emp = $db->query("SELECT employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  ");
        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
        }
        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

        $get_count = $db->query("SELECT count(KindOfDay) as countHol FROM  " . $portal_hourlyrate_day . "  WHERE (KindOfDay='BHOLIDAY') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countHoliday = $get_count->fetch();
        $countHol = $countHoliday['countHol'];

        $get_count1 = $db->query("SELECT count(*) as countOdobreno FROM  " . $portal_hourlyrate_day . "  WHERE (review_status='1') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];


        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (status='18' or status='19' or status='20') AND (date_NAV is null)");
        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "   where
		 weekday<>'6' AND weekday<>'7' and
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");

        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
  or (status='32') or (status='79')) AND (date_NAV is null)");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");

        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='27') AND (date_NAV is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='28') AND (date_NAV is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='29') AND (date_NAV is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='30') AND (date_NAV is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='31') AND (date_NAV is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='79') AND (date_NAV is null)");
        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_7 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $P_7a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='79'");
        foreach ($go as $valuego) {
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristeno = $valuego['Br_dana_iskoristeno'];
            $ostalo = $valuego['Br_dana_ostalo'];
            $brdana = $valuego['Br_dana'];
            $totalkrv = $valuego['Blood_days'];
            $totaldeath = $valuego['S_1_used'];
            $iskoristenokrv = $valuego['P_6_used'];
            $propaloGO = $valuego['G_2 not valid'];
        }

        foreach ($askedgo as $valueasked) {
            $askeddays = $valueasked['sum_hour'];
            $totalasked = $askeddays / 8;
        }
        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }
        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
            $totalgoostPG = $brdanaPG - $iskoristenototalPG;
            $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
            $ukupnogoost = $totalgoost + $totalgoostPG;
        }
        foreach ($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm = $valuepcm['Candelmas_paid'];
        }
        foreach ($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm = $valueupcm['Candelmas_unpaid'];
        }
        foreach ($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach ($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach ($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach ($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach ($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach ($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach ($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach ($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach ($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach ($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach ($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach ($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach ($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach ($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }
        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }
        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }
        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7 = ($iskoristenocurrP_7 / 8) + $iskoristenoP_7;
            $totalP_7ost = $totalP_7 - $iskoristenototalP_7;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }

        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if ($_POST['status'] == '84') {

            $statusi = array('0', '0', '0', '0');

            if (($iskoristenototalpcm + $iskoristenototalupcm) + $day_difference <= 2) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '21';
                }
            } elseif ($iskoristenototalpcm >= 2 and (($iskoristenototalpcm + $iskoristenototalupcm) + $day_difference <= 4)) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '22';
                }
            } elseif ($iskoristenototalpcm + $iskoristenototalupcm + $day_difference <= 4) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    if ($iskoristenototalpcm + $x <= 2)
                        $statusi[$x - 1] = '21';
                    else
                        $statusi[$x - 1] = '22';
                }
            } else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 4 dana vjerskih praznika !') . '</div>';
                echo $day_difference;
                return;
            }
        }

        if ($_POST['status'] == '106') {

            if (($totalasked <= $totalgoostPG) and $month_to <= 6)
                $status = '19';
            elseif ($totalasked <= $totalgoost)
                $status = '18';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                return;
            }
        }

        if ($countHol > 0 and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete pregaziti praznik!') . '</div>';
            return;
        }
        if ($countOdobreno > 0 and !($countHol > 0 and in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81)))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete promjeniti odobrenu registraciju!') . '</div>';
            return;
        }
        if ($totalasked > $totalgoost and $_POST['status'] == '18') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            return;
        }
        if ($totalasked > $totalgoostPG and $_POST['status'] == '19') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
            return;
        }
        if (($_POST['status'] == '19') and ($propaloGO == 1)) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!!') . '</div>';
            return;
        }
        if (($totalasked > $totalpcmost) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > $totalupcmost) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_1ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '27')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_2ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '28')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_3ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '29')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_4ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '30')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_5ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '31')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_6ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '32')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_7ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '79')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > 5) and ($_POST['status'] == '30' or $_POST['status'] == '72')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!') . '</div>';
            return;
        }

        if ($_POST['status'] == '84') {

            date_default_timezone_set('Europe/Sarajevo');
            for ($x = 0; $x < $day_difference; $x++) {
                $d = $day_from + $x;
                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      timest_edit = ?,
	  employee_timest_edit = ?,
      status = ?,
	  corr_status = ?,
	  employee_comment = ?
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and day = " . $d . "
   and year_id=?
    ";

                //echo $data;

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['hour'],
                        date('Y-m-d h:i:s'),
                        $_user['employee_no'],
                        $statusi[$x],
                        $statusi[$x],
                        $_POST['komentar'],
                        $getYear
                    )
                );
            }
        } else {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      timest_edit = ?,
	  employee_timest_edit = ?,
      status = ?,
	  corr_status = ?,
	  employee_comment = ?
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['hour'],
                    date('Y-m-d h:i:s'),
                    $_user['employee_no'],
                    $status,
                    $status,
                    $_POST['komentar'],
                    $getYear
                )
            );
        }


        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }

    if ($_POST['request'] == 'parent-day-add_apsolute_corrections') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $status = $_POST['status'];

        $FromDay = $_POST['dateFrom'];
        $ToDay = $_POST['dateTo'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $dateFrom = strtotime(str_replace("/", "-", $FromDay));
        $dateTo = strtotime(str_replace("/", "-", $ToDay));
        $datediff = $dateTo - $dateFrom;
        $day_difference = floor($datediff / (60 * 60 * 24)) + 1;


        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));

        $emp = $db->query("SELECT employee_no FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  ");
        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
        }
        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

        $get_count = $db->query("SELECT count(KindOfDay) as countHol FROM  " . $portal_hourlyrate_day . "  WHERE (KindOfDay='BHOLIDAY') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countHoliday = $get_count->fetch();
        $countHol = $countHoliday['countHol'];


        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='18' or corr_status='19' or corr_status='20') AND (date_NAV_corrections is null)");
        $askedgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "   where 
	 weekday<>'6' AND weekday<>'7' and
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19') AND (date_NAV_corrections is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19' or corr_status='18') AND (date_NAV_corrections is null)");

        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (corr_status='72') AND (date_NAV_corrections is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((corr_status='27') or (corr_status='28') or (corr_status='29') or (corr_status='30') or (corr_status='31')   
  or (corr_status='32') or (corr_status='79')) AND (date_NAV_corrections is null)");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='21') AND (date_NAV_corrections is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='22') AND (date_NAV_corrections is null)");

        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='27') AND (date_NAV_corrections is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='28') AND (date_NAV_corrections is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='29') AND (date_NAV_corrections is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='30') AND (date_NAV_corrections is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='31') AND (date_NAV_corrections is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='32') AND (date_NAV_corrections is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='79') AND (date_NAV_corrections is null)");
        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_7 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $P_7a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='79'");
        foreach ($go as $valuego) {
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristeno = $valuego['Br_dana_iskoristeno'];
            $ostalo = $valuego['Br_dana_ostalo'];
            $brdana = $valuego['Br_dana'];
            $totalkrv = $valuego['Blood_days'];
            $totaldeath = $valuego['S_1_used'];
            $iskoristenokrv = $valuego['P_6_used'];
            $propaloGO = $valuego['G_2 not valid'];
        }

        foreach ($askedgo as $valueasked) {
            $askeddays = $valueasked['sum_hour'];
            $totalasked = $askeddays / 8;
        }
        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }
        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
            $totalgoostPG = $brdanaPG - $iskoristenototalPG;
            $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
            $ukupnogoost = $totalgoost + $totalgoostPG;
        }
        foreach ($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm = $valuepcm['Candelmas_paid'];
        }
        foreach ($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm = $valueupcm['Candelmas_unpaid'];
        }
        foreach ($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach ($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach ($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach ($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach ($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach ($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach ($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach ($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach ($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach ($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach ($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach ($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach ($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach ($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }
        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }
        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }
        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];;
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['sum_hour'];;
            $iskoristenototalP_7 = ($iskoristenocurrP_7 / 8) + $iskoristenoP_7;
            $totalP_7ost = $totalP_7 - $iskoristenototalP_7;
        }
        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }

        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if ($_POST['status'] == '84') {

            $statusi = array('0', '0', '0', '0');

            if (($iskoristenototalpcm + $iskoristenototalupcm) + $day_difference <= 2) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '21';
                }
            } elseif ($iskoristenototalpcm >= 2 and (($iskoristenototalpcm + $iskoristenototalupcm) + $day_difference <= 4)) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    $statusi[$x - 1] = '22';
                }
            } elseif ($iskoristenototalpcm + $iskoristenototalupcm + $day_difference <= 4) {
                for ($x = 1; $x <= $day_difference; $x++) {
                    if ($iskoristenototalpcm + $x <= 2)
                        $statusi[$x - 1] = '21';
                    else
                        $statusi[$x - 1] = '22';
                }
            } else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 4 dana vjerskih praznika !') . '</div>';
                echo $day_difference;
                return;
            }
        }

        if ($_POST['status'] == '106') {

            if (($totalasked <= $totalgoostPG) and $month_to <= 6)
                $status = '19';
            elseif ($totalasked <= $totalgoost)
                $status = '18';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                return;
            }
        }

        if ($countHol > 0 and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete pregaziti praznik!') . '</div>';
            return;
        }
        if ($totalasked > $totalgoost and $_POST['status'] == '18') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            return;
        }
        if ($totalasked > $totalgoostPG and $_POST['status'] == '19') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
            return;
        }
        if (($_POST['status'] == '19') and ($propaloGO == 1)) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Nemate pravo na godišnji iz predhodne godine!!') . '</div>';
            return;
        }
        if (($totalasked > $totalpcmost) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > $totalupcmost) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_1ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '27')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_2ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '28')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_3ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '29')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_4ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '30')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_5ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '31')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_6ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '32')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za doniranje krvi, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalasked > $totalP_7ost) or ($totalasked > $totalploost)) and ($_POST['status'] == '79')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if (($totalasked > 5) and ($_POST['status'] == '30' or $_POST['status'] == '72')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti interval veći od 5 dana za smrt člana uže porodice!') . '</div>';
            return;
        }

        if ($_POST['status'] == '84') {

            date_default_timezone_set('Europe/Sarajevo');
            for ($x = 0; $x < $day_difference; $x++) {
                $d = $day_from + $x;
                $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      timest_edit_corr = ?,
	  employee_timest_edit = ?,
      corr_status = ?,
	  employee_comment = ?
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and day = " . $d . "
   and year_id=?
    ";

                //echo $data;

                $res = $db->prepare($data);
                $res->execute(
                    array(
                        $_POST['hour'],
                        date('Y-m-d h:i:s'),
                        $_user['employee_no'],
                        $statusi[$x],
                        $_POST['komentar'],
                        $getYear
                    )
                );
            }
        } else {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      timest_edit_corr = ?,
	  employee_timest_edit = ?,
      corr_status = ?,
	  employee_comment = ?
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['hour'],
                    date('Y-m-d h:i:s'),
                    $_user['employee_no'],
                    $status,
                    $_POST['komentar'],
                    $getYear
                )
            );
        }


        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }

    if ($_POST['request'] == 'parent-day-cancel_apsolute') {

        //$status = $_POST['status'];

        $FromDay = $_POST['dateFrom'];
        $ToDay = $_POST['dateTo'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];

        $dateFrom = strtotime(str_replace("/", "-", $FromDay));
        $dateTo = strtotime(str_replace("/", "-", $ToDay));

        $month_from = date("n", strtotime(str_replace("/", "-", $FromDay)));
        $month_to = date("n", strtotime(str_replace("/", "-", $ToDay)));

        $day_from = date("j", strtotime(str_replace("/", "-", $FromDay)));
        $day_to = date("j", strtotime(str_replace("/", "-", $ToDay)));

        $get_count1 = $db->query("SELECT count(*) as countOdobreno FROM  " . $portal_hourlyrate_day . "  WHERE (review_status='1') and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=" . $getYear);
        $countOdo = $get_count1->fetch();
        $countOdobreno = $countOdo['countOdobreno'];

        if ($countOdobreno > 0) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete otkazati odobrenu registraciju!') . '</div>';
            return;
        }

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      timest_edit = ?,
      status = ?,
	  employee_comment = ?
   where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '8',
                date('Y-m-d h:i:s'),
                '5',
                '',
                $getYear
            )
        );


        echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

    }


    if ($_POST['request'] == 'user-day-edit') {

        $FromDay = $_POST['FromDay'];
        $ToDay = $_POST['ToDay'];
        $getMonth = $_POST['get_month'];
        $getYear = $_POST['get_year'];
        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        if (($_POST['FromDay'] > $_POST['ToDay']))
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Datum Od mora biti manji od datuma Do') . '</div>'; else {
            $query = $db->query("SELECT [day] FROM  " . $portal_hourlyrate_day . "  where  month_id='$getMonth' AND year_id='$getYear'");

            foreach ($query as $item) {

                if ($item['day'] >= $FromDay && $item['day'] <= $ToDay) {

                    $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      hour = ?,
      status = ?
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
                }
            }
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';

        }
    }


    if ($_POST['request'] == 'holiday-edit') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $this_id = $_POST['request_id'];
        _updateHoliday($_POST['holiday_name'], $_POST['department_name'], $_POST['date'], $_POST['pomicni'], $_POST['old_date'], $this_id);
        $date = date("Y/m/d", strtotime(str_replace("/", "-", $_POST['date'])));

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_holidays_per_department . "  SET
      [department name] = ?,
      [date] = ?,
	  [holiday_name] = ?,
	  [Pomicni] = ?
	  WHERE id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['department_name'],
                $date,
                $_POST['holiday_name'],
                $_POST['pomicni'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            _updateHoliday($_POST['holiday_name'], $_POST['department_name'], $_POST['date'], $_POST['pomicni'], $_POST['old_date'], $this_id);
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'day-edit_corrections') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $this_id = $_POST['request_id'];
        $status = $_POST['status'];

        $check = $db->query("SELECT year_id,month_id,employee_no FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "' ");
        foreach ($check as $checkvalue) {
            $filter_year = $checkvalue['year_id'];
            $filter_month = $checkvalue['month_id'];
            $filter_emp = $checkvalue['employee_no'];
        }

        $emp = $db->query("SELECT employee_no,year_id,month_id FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");

        foreach ($emp as $valueemp) {
            $empid = $valueemp['employee_no'];
            $getYear = $valueemp['year_id'];
            $getMonth = $valueemp['month_id'];
        }

        $get_old_status = $db->query("SELECT status, KindOfDay FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $this_id . "'  ");
        $old_status = $get_old_status->fetch();


        $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "'  
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='18' or corr_status='19' or corr_status='20') AND (date_NAV_corrections is null)");
        $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19') AND (date_NAV_corrections is null)");
        $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='19' or corr_status='18') AND (date_NAV_corrections is null)");
        $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
    AND weekday<>'6' AND weekday<>'7' AND (corr_status='72') AND (date_NAV_corrections is null)");
        $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='21') AND (date_NAV_corrections is null)");
        $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='22') AND (date_NAV_corrections is null)");
        $checkva = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' and month_id='" . $getMonth . "'and employee_no='" . $empid . "'
   and corr_status='19'  ");

        $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' AND employee_no='" . $empid . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((corr_status='27') or (corr_status='28') or (corr_status='29') or (corr_status='30') or (corr_status='31')   
  or (corr_status='32') or (corr_status='79')) AND (date_NAV_corrections is null)");

        $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");

        $P_1 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_2 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_3 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_4 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_5 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_6 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_7 = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $empid . "'");
        $P_1a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='27'");
        $P_2a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='28'");
        $P_3a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='29'");
        $P_4a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='30'");
        $P_5a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='31'");
        $P_6a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='32'");
        $P_7a = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE id='79'");
        $curruP_1 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='27') AND (date_NAV_corrections is null)");
        $curruP_2 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='28') AND (date_NAV_corrections is null)");
        $curruP_3 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='29') AND (date_NAV_corrections is null)");
        $curruP_4 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='30') AND (date_NAV_corrections is null)");
        $curruP_5 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='31') AND (date_NAV_corrections is null)");
        $curruP_6 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
  AND weekday<>'6' AND weekday<>'7' AND (corr_status='32') AND (date_NAV_corrections is null)");
        $curruP_7 = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $getYear . "' AND month_id='" . $getMonth . "' 
     and weekday<>'6' AND weekday<>'7' AND (corr_status='79') AND (date_NAV_corrections is null)");


        foreach ($go as $valuego) {
            $totalgo = $valuego['Ukupno'];
            $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
            $iskoristeno = $valuego['Br_dana_iskoristeno'];
            $brdana = $valuego['Br_dana'];
            $brdanaPG = $valuego['Br_danaPG'];
            $ostaloPG = $valuego['Br_dana_ostaloPG'];
            $iskoristenokrv = $valuego['P_6_used'];
            $totalkrv = $valuego['Blood_days'];
            $propaloGO = $valuego['G_2 not valid'];
        }

        foreach ($currgo as $valuecurrgo) {
            $iskoristenocurr = $valuecurrgo['sum_hour'];;
            $iskoristenototal = ($iskoristenocurr / 8) + $iskoristeno;
            $totalgoost = $brdana - $iskoristenototal;
        }

        foreach ($currgoPG as $valuecurrgoPG) {
            $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
            $iskoristenototalPG = ($iskoristenocurrPG / 8) + $iskoristenoPG;
            $totalgoostPG = $brdanaPG - $iskoristenototalPG;
            $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
            $ukupnogoost = $totalgoost + $totalgoostPG;
        }

        foreach ($pcm as $valuepcm) {
            $totalpcm = $valuepcm['Candelmas_paid_total'];
            $iskoristenopcm = $valuepcm['Candelmas_paid_used'];
            $brdanapcm = $valuepcm['Candelmas_paid'];
        }

        foreach ($upcm as $valueupcm) {
            $totalupcm = $valueupcm['Candelmas_unpaid_total'];
            $iskoristenoupcm = $valueupcm['Candelmas_unpaid_used'];
            $brdanaupcm = $valueupcm['Candelmas_unpaid'];
        }

        foreach ($currpcm as $valuecurrpcm) {
            $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
            $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
            $totalpcmost = $brdanapcm - $iskoristenototalpcm;
        }

        foreach ($currupcm as $valuecurrupcm) {
            $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
            $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
            $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
        }


        foreach ($P_1 as $valueP_1) {
            $iskoristenoP_1 = $valueP_1['P_1_used'];
        }
        foreach ($P_2 as $valueP_2) {
            $iskoristenoP_2 = $valueP_2['P_2_used'];
        }
        foreach ($P_1a as $valueP_1a) {
            $totalP_1 = $valueP_1a['allowed_days'];
        }
        foreach ($P_2a as $valueP_2a) {
            $totalP_2 = $valueP_2a['allowed_days'];
        }
        foreach ($P_3 as $valueP_3) {
            $iskoristenoP_3 = $valueP_3['P_3_used'];
        }
        foreach ($P_3a as $valueP_3a) {
            $totalP_3 = $valueP_3a['allowed_days'];
        }
        foreach ($P_4 as $valueP_4) {
            $iskoristenoP_4 = $valueP_4['P_4_used'];
        }
        foreach ($P_4a as $valueP_4a) {
            $totalP_4 = $valueP_4a['allowed_days'];
        }
        foreach ($P_5 as $valueP_5) {
            $iskoristenoP_5 = $valueP_5['P_5_used'];
        }
        foreach ($P_5a as $valueP_5a) {
            $totalP_5 = $valueP_5a['allowed_days'];
        }
        foreach ($P_6 as $valueP_6) {
            $iskoristenoP_6 = $valueP_6['P_6_used'];
        }
        foreach ($P_6a as $valueP_6a) {
            $totalP_6 = $valueP_6a['allowed_days'];
        }

        foreach ($P_7 as $valueP_7) {
            $iskoristenoP_7 = $valueP_7['P_7_used'];
        }
        foreach ($P_7a as $valueP_7a) {
            $totalP_7 = $valueP_7a['allowed_days'];
        }

        foreach ($curruP_1 as $valuecurrP_1) {
            $iskoristenocurrP_1 = $valuecurrP_1['sum_hour'];;
            $iskoristenototalP_1 = ($iskoristenocurrP_1 / 8) + $iskoristenoP_1;
            $totalP_1ost = $totalP_1 - $iskoristenototalP_1;
        }

        foreach ($curruP_2 as $valuecurrP_2) {
            $iskoristenocurrP_2 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_2 = ($iskoristenocurrP_2 / 8) + $iskoristenoP_2;
            $totalP_2ost = $totalP_2 - $iskoristenototalP_2;
        }
        foreach ($curruP_3 as $valuecurrP_3) {
            $iskoristenocurrP_3 = $valuecurrP_2['sum_hour'];;
            $iskoristenototalP_3 = ($iskoristenocurrP_3 / 8) + $iskoristenoP_3;
            $totalP_3ost = $totalP_3 - $iskoristenototalP_3;
        }
        foreach ($curruP_4 as $valuecurrP_4) {
            $iskoristenocurrP_4 = $valuecurrP_4['sum_hour'];;
            $iskoristenototalP_4 = ($iskoristenocurrP_4 / 8) + $iskoristenoP_4;
            $totalP_4ost = $totalP_4 - $iskoristenototalP_4;
        }
        foreach ($curruP_5 as $valuecurrP_5) {
            $iskoristenocurrP_5 = $valuecurrP_5['sum_hour'];;
            $iskoristenototalP_5 = ($iskoristenocurrP_5 / 8) + $iskoristenoP_5;
            $totalP_5ost = $totalP_5 - $iskoristenototalP_5;
        }
        foreach ($curruP_6 as $valuecurrP_6) {
            $iskoristenocurrP_6 = $valuecurrP_6['sum_hour'];
            $iskoristenototalP_6 = ($iskoristenocurrP_6 / 8) + $iskoristenoP_6;
            $totalP_6ost = $totalP_6 - $iskoristenototalP_6;
        }
        foreach ($curruP_7 as $valuecurrP_7) {
            $iskoristenocurrP_7 = $valuecurrP_7['sum_hour'];
            $iskoristenototalP_7 = ($iskoristenocurrP_7 / 8) + $iskoristenoP_7;
            $totalP_7ost = $totalP_7 - $iskoristenototalP_7;
        }

        foreach ($plo as $valueplo) {
            $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
            $totalplo = $valueplo['Br_dana_PLO'];
        }
        foreach ($currplo as $valuecurrplo) {
            $iskoristenocurrplo = $valuecurrplo['sum_hour'];
            $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
            $totalploost = $totalplo - $iskoristenototalplo;
        }

        if ($_POST['status'] == '84') {

            if ($iskoristenototalpcm + $iskoristenototalupcm < 2)
                $status = '21';
            elseif ($iskoristenototalpcm >= 2 and ($iskoristenototalpcm + $iskoristenototalupcm) < 4)
                $status = '22';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 4 dana vjerskih praznika !') . '</div>';
                return;
            }
        }

        if ($_POST['status'] == '106') {

            if (($totalgoostPG - 1 >= 0) and $filter_month <= 6)
                $status = '19';
            elseif ($totalgoost - 1 >= 0)
                $status = '18';
            else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO!') . '</div>';
                return;
            }
        }

        if (($old_status['KindOfDay'] == 'BHOLIDAY') and !in_array($_POST['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81))) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete pregaziti praznik!') . '</div>';
            return;
        }
        if (($totalgoost - 1 < 0) and $_POST['status'] == '18') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO tekuće godine !') . '</div>';
            return;
        }
        if (($totalgoostPG - 1 < 0) and $_POST['status'] == '19') {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana GO prethodne godine !') . '</div>';
            return;
        }
        if (($totalpcmost - 1 < 0) and ($_POST['status'] == '21')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana plaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if (($totalupcmost - 1 < 0) and ($_POST['status'] == '22')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana neplaćenog vjerskog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_1ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '27')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za zaključenje braka, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_2ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '28')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za rođenje djeteta, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_3ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '29')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za njegu člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_4ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '30')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 5 dana za smrt člana uže porodice, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_5ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '31')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od dozvoljenih dana za selidbu, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_6ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '32')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 1 dana za darivanje krvi, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }
        if ((($totalP_7ost - 1 < 0) or ($totalploost - 1 < 0)) and ($_POST['status'] == '79')) {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Ne možete unijeti više od 2 dana za stručno usavršavanje, niti od ukupnog broja dana plaćenog odsustva!') . '</div>';
            return;
        }

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      day = ?,
      hour = ?,
	  timest_edit_corr = ?,
	  employee_timest_edit = ?,
      corr_status = ?,
	  employee_comment = ?
      WHERE id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                $_POST['day'],
                $_POST['hour'],
                date('Y-m-d h:i:s'),
                $_user['employee_no'],
                $status,
                $_POST['komentar'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Izmjene su uspješno spašene!') . '</div>';
        }

    }

    if ($_POST['request'] == 'change-odobreno') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['odobreno_id']);
        $day_from = $pieces[1];
        $month_from = $pieces[2];
        $day_to = $pieces[3];
        $month_to = $pieces[4];
        $year_id = $pieces[5];


        if ($_POST['odobreno'] == '2') {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      review_status = ?,
	  status  = ?
	 where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(

                    '0',
                    '5',
                    $year_id
                )
            );
        } else {
            date_default_timezone_set('Europe/Sarajevo');
            $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
	  status = ?,
	  corr_status = ?,
	  review_status = ?,
	  review_comment = ?,
	  review_user = ?
	  where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )
   and year_id=?
    ";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $_POST['status'],
                    $_POST['status'],
                    $_POST['odobreno'],
                    $_POST['komentar'],
                    $_user['user_id'],
                    $year_id
                )
            );
        }
        if ($res->rowCount() == 1) {
            echo date("Y/m/d");

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-komentar-odsustva') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['komentar_id']);
        $day_from = $pieces[1];
        $month_from = $pieces[2];
        $day_to = $pieces[3];
        $month_to = $pieces[4];
        $year_id = $pieces[5];


        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      review_comment = ?
	  where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
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

        if ($res->rowCount() == 1) {
            echo date("Y/m/d");

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-dokument') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $pieces = explode("-", $_POST['dokument_id']);
        $day_from = $pieces[1];
        $month_from = $pieces[2];
        $day_to = $pieces[3];
        $month_to = $pieces[4];
        $year_id = $pieces[5];

        date_default_timezone_set('Europe/Sarajevo');
        $data = "UPDATE  " . $portal_hourlyrate_day . "  SET
      dokument = ?
	  where 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
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

        if ($res->rowCount() == 1) {
            echo date("Y/m/d");

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }


    if ($_POST['request'] == 'remove-requests_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM requests WHERE request_id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-day_remove') {
        $this_id = $_POST['request_id'];
        $data = "DELETE FROM hourlyrate_day WHERE id = ?";
        $delete = $db->prepare($data);
        $delete->execute(array($this_id));
        if ($delete) {
            echo 1;
        }
    }


    if ($_POST['request'] == 'remove-requests_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_requests . "  SET
        is_archive = ?
        WHERE request_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'remove-tasks_archive') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_archive = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'accept-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
        is_accepted = ?
        WHERE task_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'completed-tasks') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'task-comment') {

        $data3 = "INSERT INTO  " . $portal_comments . "  (
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
        if ($res3->rowCount() == 1) {
            echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
        }

    }


    if ($_POST['request'] == 'proc-tasks') {

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
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'count-tasks') {

        $this_id = $_POST['request_id'];
        $total_0 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id'")->rowCount();
        $total_1 = $db->query("SELECT Count(*) FROM [c0_intranet2_apoteke].[dbo].[tasks_item] WHERE task_id='$this_id' AND status='1'")->rowCount();

        if ($total_1 == $total_0) {
            echo 'yes';
        } else {
            echo 'no';
        }

    }


    if ($_POST['request'] == 'comments') {

        $user_id = $_POST['user'];
        $parent_id = $_POST['parent'];

        $comments = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");
        $comments_no = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='" . $_POST['request_id'] . "' AND type='task'");

        if ($comments_no->rowCount() < 0) {

            $user = _user($user_id);
            $parent = _user($parent_id);

            foreach ($comments as $item) {
                $parent = _user($item['user_id']);
                echo '<div class="comment">';
                if ($item['user_id'] == $user_id) {
                    echo '<div class="row">';
                    echo '<div class="col-xs-9"><div class="text-u">';
                    echo $item['comment'];
                    echo '</div><small class="text-muted">' . date('d/m/Y', strtotime($item['date_created'])) . '</small></div>';
                    echo '<div class="col-xs-3 text-center">';
                    if ($user['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $user['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $user['fname'] . ' ' . $user['lname'] . '</small>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="row">';
                    echo '<div class="col-xs-3 text-center">';
                    if ($parent['image'] != 'none') {
                        echo '<img src="' . $_timthumb . $_uploadUrl . '/' . $parent['image'] . '&w=200&h=200" class="img-circle" style="width:70%;">';
                    } else {
                        echo '<img src="' . $_themeUrl . '/images/noimage-user.png" class="img-circle" style="width:70%;">';
                    }
                    echo '<br/><small>' . $parent['fname'] . ' ' . $parent['lname'] . '</small>';
                    echo '</div>';
                    echo '<div class="col-xs-9"><div class="text-p">';
                    echo $item['comment'];
                    echo '</div><small class="pull-right text-muted">' . date('d/m/Y', strtotime($item['date_created'])) . '</small></div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        }

    }

    if ($_POST['request'] == 'change-obuka-status') {

        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];
        $faza = $pieces[2];


        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obuka-ocjena3') {

        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obuka-ocjena6') {

        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];


        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obuka-komentar') {


        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];
        $faza = $pieces[2];


        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obuka-coment') {

        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];
        $faza = $pieces[2];


        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-obuka-ocjena_mentora') {

        $pieces = explode("-", $_POST['obuka']);
        $item_id = $pieces[0];
        $user_id = $pieces[1];
        $faza = $pieces[2];

        $data = "UPDATE  " . $portal_training_program_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'send-potpisuje_radnik_obuka') {

        $pieces = explode("-", $_POST['request_id']);
        $user_id = $pieces[0];
        $faza = $pieces[1];


        //checks

        $check_status = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_training_program_status . "  WHERE user_id=" . $user_id . " and obrazac_type=" . $faza . " and (status is null or status = 0)");
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];

        if ($status_count > 0) {
            echo 'nisu_popunjeni_obuka';
            return;
        }


        $data = "UPDATE  " . $portal_training_program_header . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        }
    }
    if ($_POST['request'] == 'send-potpisuje_radnik_obuka_eval') {

        $pieces = explode("-", $_POST['request_id']);
        $user_id = $pieces[0];
        $faza = $pieces[1];
        //checks

        $check_ocjene = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_training_program_status . "  WHERE user_id=" . $user_id . " and obrazac_type=" . $faza . " and (ocjena_mentora is null or ocjena_mentora = 0) and item_id<>7");
        $ocjene_count1 = $check_ocjene->fetch();
        $ocjene_count = $ocjene_count1['broj'];

        if ($ocjene_count > 0) {
            echo 'nisu_popunjeni_obuka';
            return;
        }


        $data = "UPDATE  " . $portal_training_program_header . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        }
    }

    if ($_POST['request'] == 'send-radnik_potpisuje_zaduznica') {
        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter = "";
        if ($sector_type_usr == 1)
            $sector_type_filter .= " and OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_zaduznice_status . "  WHERE user_id=" . $user_id . $sector_type_filter . " and zaduzen=1 and saglasan = 0");
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];

        if ($status_count > 0) {
            echo 'nisu_popunjeni_odbij';
            return;
        }


        $data = "UPDATE  " . $portal_zaduznice_header . "  SET
        radnik_potpisao_zaduznica = ?
		WHERE user_id = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $user_id,

            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }

    if ($_POST['request'] == 'send-radnik_odbija_zaduznica') {

        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter = "";
        if ($sector_type_usr == 1)
            $sector_type_filter .= " and OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM  " . $portal_zaduznice_status . "  WHERE user_id=" . $user_id . $sector_type_filter . " and zaduzen=1 and saglasan = 0 and zapisnik<>'' and zapisnik is not null");
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if ($status_count == 0) {
            echo 'nisu_popunjeni_odbij';
            return;
        }

        $data = "UPDATE  " . $portal_zaduznice_header . "  SET
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
        if ($res->rowCount() == 1) {
            //slanje maila adminima
            require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

            $user_to_send = _user($user_id);


            $mail = new PHPMailer(true);
            $mail->CharSet = "UTF-8";


            $mail->isSMTP();
//$mail->Host = '91.235.170.162';
            $mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
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
<p>Obavje&scaron;tavamo vas da zaposlenik <strong>' . $user_to_send['fname'] . ' ' . $user_to_send['lname'] . '</strong> u Sberbank BH d.d. nije saglasan sa određenom stavkom obrasca Zadužnice.</p>
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
</table>';


            $mail->Subject = 'Zadužnica_potvrda preuzimanja tehničke imovine i proizvoda Banke od strane radnika /zaduženje';
            $mail->Body = $bodyContent;


            if (!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
            }
            echo 1;
        }

    }

    if ($_POST['request'] == 'send-radnik_potpisuje_razduznica') {
        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter = "";
        if ($sector_type_usr == 1)
            $sector_type_filter .= " and a.OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Razduznice_status] a join  " . $portal_zaduznice_status . "  b
on a.item_id = b.item_id and a.user_id = b.user_id
 WHERE a.user_id=" . $user_id . $sector_type_filter . " and b.zaduzen<>0 and a.saglasan = 0");
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if ($status_count > 0) {
            echo 'nisu_popunjeni_odbij';
            return;
        }


        $data = "UPDATE  " . $portal_zaduznice_header . "  SET
        radnik_potpisao_razduznica = ?
		WHERE user_id = ?
		UPDATE  " . $portal_users . "  SET
        status = 1
		WHERE user_id = ?";


        $res = $db->prepare($data);
        $res->execute(
            array(
                '1',
                $user_id,
                $user_id

            )
        );
        if ($res->rowCount() == 1) {
            echo 1;
            session_destroy();
        }

    }

    if ($_POST['request'] == 'send-radnik_odbija_razduznica') {

        $user_id = $_POST['request_id'];

        $sector_type_usr = _user($user_id)['department_code_type'];
        $sector_type_filter = "";
        if ($sector_type_usr == 1)
            $sector_type_filter .= " and a.OJ<>3";

        $check_status = $db->query("SELECT COUNT(*) as broj FROM [c0_intranet2_apoteke].[dbo].[Razduznice_status] a join  " . $portal_zaduznice_status . "  b
on a.item_id = b.item_id and a.user_id = b.user_id
 WHERE a.user_id=" . $user_id . $sector_type_filter . " and b.zaduzen<>0 and a.saglasan = 0 and a.zapisnik<>'' and a.zapisnik is not null");
        $status_count1 = $check_status->fetch();
        $status_count = $status_count1['broj'];


        if ($status_count == 0) {
            echo 'nisu_popunjeni_odbij';
            return;
        }

        $data = "UPDATE  " . $portal_zaduznice_header . "  SET
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
        if ($res->rowCount() == 1) {
            //slanje maila adminima
            require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';

            $user_to_send = _user($user_id);
            if ($user_to_send['gender'] == 1)
                $postovanje = 'Po&scaron;tovana';
            else
                $postovanje = 'Po&scaron;tovani';


            $mail = new PHPMailer(true);
            $mail->CharSet = "UTF-8";


            $mail->isSMTP();
//$mail->Host = '91.235.170.162';
            $mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
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
<p>Obavje&scaron;tavamo vas da zaposlenik <strong>' . $user_to_send['fname'] . ' ' . $user_to_send['lname'] . '</strong> u Sberbank BH d.d. nije saglasan sa određenom stavkom obrasca Razdužnice.</p>
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
</table>';


            $mail->Subject = 'Razdužnica_potvrda predaje tehničke imovine/razduženje';
            $mail->Body = $bodyContent;


            if (!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
            }
            echo 1;
        }

    }

    if ($_POST['request'] == 'change-saglasan') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $pieces = explode("-", $_POST['saglasan_id']);
        $item_id = $pieces[1];
        $user_id = $pieces[2];

        $data = "UPDATE  " . $portal_zaduznice_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo date("Y/m/d");

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-saglasan_razduznica') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $pieces = explode("-", $_POST['saglasan_id']);
        $item_id = $pieces[1];
        $user_id = $pieces[2];

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
        if ($res->rowCount() == 1) {
            echo date("Y/m/d");

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-zaduznica-zapisnik') {

        $pieces = explode("-", $_POST['zapisnik_id']);
        $item_id = $pieces[1];
        $user_id = $pieces[2];

        $data = "UPDATE  " . $portal_zaduznice_status . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'change-razduznica-zapisnik') {

        $pieces = explode("-", $_POST['zapisnik_id']);
        $item_id = $pieces[1];
        $user_id = $pieces[2];

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
        if ($res->rowCount() == 1) {
            echo 1;

        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }
    }

    if ($_POST['request'] == 'export-excel') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $year = $_POST['year'];

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);


        if ($get->rowCount() < 0) {

            foreach ($get as $day) {
                $arr [_nameHRstatus($day['status'])][] = $day['review_status'];
            }
        }

        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            if ($count0 == 0) $count0 = '0';
            $count1 = count(array_keys($value, 1));
            if ($count1 == 0) $count1 = '0';
            $count2 = count(array_keys($value, 2));
            if ($count2 == 0) $count2 = '0';
            $arr1[] = array($key, $count0, $count1, $count2);
        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

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

        $writer->save($root . '/CORE/Satnice_' . $_user['username'] . '.xls');

        echo $host . '/CORE/Satnice_' . $_user['username'] . '.xls';

    }

    if ($_POST['request'] == 'export-excel-reif') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $year = $_POST['year'];

        $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
        $year_real = $get_year->fetch();

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);

        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        if ($get->rowCount() < 0) {

            $index = 0;
            foreach ($get as $key => $day) {

                if ($key == 0) {
                    $status = $day['status'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                }

                if ($day['status'] == $status and $day['Description'] == $description) {
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                } else {
                    $index = $index + 1;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                }

                $status = $day['status'];
                $description = $day['Description'];

            }


            foreach ($arr as $key => $value) {
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2);


            }
            // print_r($arr);

        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls';

    }

    if ($_POST['request'] == 'export-excel-reif-users') {

        $godina = date("Y");
        $mjesec = date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;
        // $year = $_POST['year'];

        if ($_user['role'] == 4)
            $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5)  ORDER BY department_code");
        elseif ($_user['role'] == 2)
            $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE (parent='" . $_user['employee_no'] . "')  ORDER BY department_code");

        $arr1 = array();
        foreach ($query as $item) {

            $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $item['user_id']);
            $year = $get_year->fetch();

            $get_year1 = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year['id'] . "'");
            $year_real = $get_year1->fetch();

            $arr = array();

            $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year['id']);


            if ($get->rowCount() < 0) {

                $index = 0;
                foreach ($get as $key => $day) {

                    if ($key == 0) {
                        $status = $day['status'];
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                    }

                    if ($day['status'] == $status) {
                        $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    } else {
                        $index = $index + 1;
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                        $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                    }

                    $status = $day['status'];


                }


                foreach ($arr as $key => $value) {
                    $count0 = count(array_keys($value, '0'));
                    $count1 = count(array_keys($value, '1'));
                    $count2 = count(array_keys($value, '2'));
                    $pieces = explode("-", $key);
                    $naziv_odsustva = $pieces[0];
                    $arr1[] = array($item['employee_no'], $value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2);


                }
                // print_r($arr);

            }
        }

        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls';

    }

    if ($_POST['request'] == 'export-excel-reif-users2') {

        $employee_no = $_POST['employee_no'];
        if ($employee_no == "")
            $employee_query = "";
        else
            $employee_query = " and employee_no= '" . $employee_no . "'";

        $ime_prezime = $_POST['ime_prezime'];
        if ($ime_prezime == "")
            $ime_prezime_query = "";
        else
            $ime_prezime_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE fname + ' ' + lname = N'" . $ime_prezime . "')";

        $vrsta = $_POST['vrsta'];
        if ($vrsta == "")
            $vrsta_query = "";
        else
            $vrsta_query = " and status= " . $vrsta . "";

        $filter_neodobreno = $_POST['filter_neodobreno'];
        if ($filter_neodobreno == true)
            $neodobreno_query = " and review_status = 0";
        else
            $neodobreno_query = "";


        $godina = date("Y");
        $mjesec = date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;

        if ($_user['role'] == 4) {
            $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
        } elseif ($_user['role'] == 2) {
            $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent=" . $_user['employee_no'] . "))";
        }

        $arr1 = array();

        $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
        $year_real = $get_year->fetch();


        $arr = array();

        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )" . $vrsta_query . $role_query . $employee_query . $ime_prezime_query . $neodobreno_query . ")
   order by employee_no");


        if ($get->rowCount() < 0) {

            $index = 0;
            foreach ($get as $key => $day) {

                if ($key == 0) {
                    $day_id = $day['id'] - 1;
                    $status = $day['status'];
                    $employee_no = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['ime_prezime'] = _employee($employee_no)['fname'] . ' ' . _employee($employee_no)['lname'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['employee_no'] = $employee_no;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['registrovano'] = $day['timest_edit'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                }

                if ($day['status'] == $status and $day['employee_no'] == $employee_no and $day['Description'] == $description and ($day['id'] == $day_id + 1)) {
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                } else {
                    $index = $index + 1;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['employee_no'] = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['registrovano'] = $day['timest_edit'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['ime_prezime'] = _employee($day['employee_no'])['fname'] . ' ' . _employee($day['employee_no'])['lname'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $employee_no = $day['employee_no'];
                $description = $day['Description'];

            }


            foreach ($arr as $key => $value) {
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                if ($value['employee_no'] == '1')
                    $count1 = $count1 - 1;
                $arr1[] = array($value['employee_no'], $value['ime_prezime'], $value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2, $value['registrovano'], $value['reg_korisnik']);


            }
            // print_r($arr);

        }


        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').xls';

    }

    if ($_POST['request'] == 'export-pdf-reif') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $year = $_POST['year'];

        $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
        $year_real = $get_year->fetch();

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;

        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        $arr = array();
        $arr1 = array();
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);


        if ($get->rowCount() < 0) {

            $index = 0;
            foreach ($get as $key => $day) {

                if ($key == 0) {
                    $status = $day['status'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                }

                if ($day['status'] == $status and $day['Description'] == $description) {
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                } else {
                    $index = $index + 1;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                }


                $status = $day['status'];
                $description = $day['Description'];

            }


            foreach ($arr as $key => $value) {
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2);
            }

//print_r($arr);
        }


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
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
        $rendererLibraryPath = $root . '/tcpdf';

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf';

    }

    if ($_POST['request'] == 'export-pdf-reif-users') {

        $godina = date("Y");
        $mjesec = date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;
        // $year = $_POST['year'];

        if ($_user['role'] == 4)
            $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5)  ORDER BY department_code");
        elseif ($_user['role'] == 2)
            $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE (parent='" . $_user['employee_no'] . "')  ORDER BY department_code");

        $arr1 = array();
        foreach ($query as $item) {

            $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $item['user_id']);
            $year = $get_year->fetch();

            $get_year1 = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year['id'] . "'");
            $year_real = $get_year1->fetch();

            $arr = array();

            $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year['id']);


            if ($get->rowCount() < 0) {

                $index = 0;
                foreach ($get as $key => $day) {

                    if ($key == 0) {
                        $status = $day['status'];
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                    }

                    if ($day['status'] == $status) {
                        $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    } else {
                        $index = $index + 1;
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                        $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                        $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                    }

                    $status = $day['status'];


                }


                foreach ($arr as $key => $value) {
                    $count0 = count(array_keys($value, '0'));
                    $count1 = count(array_keys($value, '1'));
                    $count2 = count(array_keys($value, '2'));
                    $pieces = explode("-", $key);
                    $naziv_odsustva = $pieces[0];
                    $arr1[] = array($item['employee_no'], $value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2);


                }
                // print_r($arr);

            }
        }

        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
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
        $rendererLibraryPath = $root . '/tcpdf';

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf';

    }

    if ($_POST['request'] == 'export-pdf-reif-users2') {

        $employee_no = $_POST['employee_no'];
        if ($employee_no == "")
            $employee_query = "";
        else
            $employee_query = " and employee_no= '" . $employee_no . "'";

        $ime_prezime = $_POST['ime_prezime'];
        if ($ime_prezime == "")
            $ime_prezime_query = "";
        else
            $ime_prezime_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE fname + ' ' + lname = N'" . $ime_prezime . "')";

        $vrsta = $_POST['vrsta'];
        if ($vrsta == "")
            $vrsta_query = "";
        else
            $vrsta_query = " and status= " . $vrsta . "";

        $godina = date("Y");
        $mjesec = date("n");

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));

        $month_from = $_POST['month_from'];
        $month_to = $_POST['month_to'];

        $day_from = $_POST['day_from'];
        $day_to = $_POST['day_to'];

        global $db, $root, $host;
        if ($_user['role'] == 4) {
            $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
        } elseif ($_user['role'] == 2) {
            $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent=" . $_user['employee_no'] . "))";
        }

        $arr1 = array();

        $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
        $year_real = $get_year->fetch();


        $arr = array();

        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and status<>5 and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )" . $vrsta_query . $role_query . $employee_query . $ime_prezime_query . ")
   order by employee_no");


        if ($get->rowCount() < 0) {

            $index = 0;
            foreach ($get as $key => $day) {

                if ($key == 0) {
                    $day_id = $day['id'] - 1;
                    $status = $day['status'];
                    $employee_no = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['ime_prezime'] = _employee($employee_no)['fname'] . ' ' . _employee($employee_no)['lname'];
                    $description = $day['Description'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['employee_no'] = $employee_no;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                }

                if ($day['status'] == $status and $day['employee_no'] == $employee_no and $day['Description'] == $description and ($day['id'] == $day_id + 1)) {
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                } else {
                    $index = $index + 1;
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['employee_no'] = $day['employee_no'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['ime_prezime'] = _employee($day['employee_no'])['fname'] . ' ' . _employee($day['employee_no'])['lname'];
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['datumDO'] = date('d/m/Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                    $arr [_nameHRstatus($day['status']) . '-' . $index][] = $day['review_status'];

                }

                $day_id = $day['id'];
                $status = $day['status'];
                $employee_no = $day['employee_no'];
                $description = $day['Description'];

            }


            foreach ($arr as $key => $value) {
                $count0 = count(array_keys($value, '0'));
                $count1 = count(array_keys($value, '1'));
                $count2 = count(array_keys($value, '2'));
                $pieces = explode("-", $key);
                $naziv_odsustva = $pieces[0];
                $arr1[] = array($value['employee_no'], $value['ime_prezime'], $value['datumOD'], $value['datumDO'], $naziv_odsustva, $count0, $count1, $count2);


            }
            // print_r($arr);

        }


        $pocetni_datum = date('d-m-Y', strtotime($day_from . '-' . $month_from . '-' . $year_real['year']));
        $krajnji_datum = date('d-m-Y', strtotime($day_to . '-' . $month_to . '-' . $year_real['year']));


        date_default_timezone_set('America/Los_Angeles');
        require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

//	Change these values to select the Rendering library that you wish to use
//		and its directory location on your server
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
        $rendererLibraryPath = $root . '/tcpdf';

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

        $writer->save($root . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf');

        echo $host . '/CORE/public/Odsustva_' . $_user['username'] . '(' . $pocetni_datum . ' - ' . $krajnji_datum . ').pdf';

    }

    if ($_POST['request'] == 'change-pagination') {

        $page = $_POST['page'];
        $limit = $_POST['limit'];


        $data = "UPDATE  " . $portal_pagination . "  SET
      Limit = ?
		WHERE Page = ?";
        $res = $db->prepare($data);
        $res->execute(
            array(
                $limit,
                $page,

            )
        );
        if ($res->rowCount() == 1) {

            echo 1;
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Izmjene nisu spašene!') . '</div>';
        }


        // echo '<div class="alert alert-success text-center">'.__('Izmjene su uspješno spašene!').'</div>';


    }


    if ($_POST['request'] == 'profile-edit') {

        if (isset($_FILES['media_file'])) {
            if (is_uploaded_file($_FILES['media_file']['tmp_name'])) {
                $p_photo = preg_replace('/[^\w\._]+/', '_', $_FILES['media_file']['name']);
                $p_photo = _checkFile($_uploadRoot . '/', $p_photo);
                $file = $_uploadRoot . '/' . $p_photo;
                if (copy($_FILES['media_file']['tmp_name'], $file)) {
                    unlink($_uploadRoot . '/' . $_POST['oldimage']);
                }
            } else {
                $p_photo = $_POST['oldimage'];
            }
        } else {
            $p_photo = $_POST['oldimage'];
        }


        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_users_edit . "  SET
     
        email = ?,
        image = ?,
        fname = ?,
        lname = ?,
        address = ?,
        zip = ?,
        city = ?,
        country = ?,
        phone = ?,
        lang = ?
        WHERE user_id = ?";

        $res = $db->prepare($data);
        $res->execute(
            array(

                $_POST['email'],
                $p_photo,
                $_POST['fname'],
                $_POST['lname'],
                $_POST['address'],
                $_POST['zip'],
                $_POST['city'],
                $_POST['country'],
                $_POST['phone'],
                $_POST['lang'],
                $this_id
            )
        );
        if ($res->rowCount() == 1) {
            $data = "UPDATE  " . $portal_users . "  SET
		image = ?,
		lang = ?,
		zamjenik = ?
        WHERE user_id = ?";

            $res = $db->prepare($data);
            $res->execute(
                array(
                    $p_photo,
                    $_POST['lang'],
                    $_POST['zamjenik'],
                    $this_id
                )
            );

            if ($_POST['zamjenik'] != '') {
                $data = "UPDATE  " . $portal_users . "  SET
		parent2 = " . $_POST['zamjenik'] . " where employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent='" . _user($this_id)['employee_no'] . "'))";

                $res = $db->prepare($data);
                $res->execute(
                    array()
                );
            } else {
                $data = "UPDATE  " . $portal_users . "  SET
		parent2 = 0 where employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent='" . _user($this_id)['employee_no'] . "'))";

                $res = $db->prepare($data);
                $res->execute(
                    array()
                );
            }

            echo '{"jsonrpc" : "2.0", "status" : "ok", "msg" : "<div class=\"alert alert-success text-center\">' . __('Informacije su uspješno spašene!') . '</div>"}';
        }


    }

    if ($_POST['request'] == 'task-review') {

        $this_id = $_POST['request_id'];
        $data = "UPDATE  " . $portal_tasks . "  SET
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
        if ($res->rowCount() == 1) {
            echo 1;
        }

    }


    if ($_POST['request'] == 'task-review-item') {

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
        if ($res->rowCount() == 1) {
            echo $this_id;
        }

    }


    /*****************trainings********************/


    if ($_POST['request'] == 'trainings-request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $ds = explode('/', $_POST['empdate']);
        $df = explode('/', $_POST['DateFrom']);
        $dt = explode('/', $_POST['DateTo']);

        $from = $ds[2] . '-' . $ds[1] . '-' . $ds[0];
        $fromD = $df[2] . '-' . $df[1] . '-' . $df[0];
        $toD = $dt[2] . '-' . $dt[1] . '-' . $dt[0];

        $user_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['parent'] . "'");

        foreach ($user_query as $uquery) {

            $email = $uquery['email'];

        }


        $user = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='" . $_user['user_id'] . "'");

        foreach ($user as $uquery2) {

            $fname = $uquery2['fname'];
            $lname = $uquery2['lname'];


            $position = $uquery2['position'];
            $departmentcode = $uquery2['department_code'];
            $sector = $uquery2['sector'];


        }


        if ($fromD <= $toD) {


            $data = "INSERT INTO  " . $portal_trainings . "  (
      user_id,parent,parent2,hr,admin,date_created,h_from,date_from,date_to,status,status_hr,status_admin,type,is_archive,country,reasons,outcome,name_of_seminar,certificate_name,costs,wage,accommodation,transport,total_costs,organizer,remark,training,training_description,comment,send_email) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $res = $db->prepare($data);
            $res->execute(
                array($_user['user_id'], $_user['parent'], $_user['parent2'], $_user['hr'], $_user['admin'],
                    date('Y-m-d', strtotime("now")),
                    date('Y-m-d', strtotime($from)),
                    date('Y-m-d', strtotime($fromD)),
                    date('Y-m-d', strtotime($toD)),
                    '0',
                    '0',
                    '0',
                    'TRENING',
                    '0',
                    $_POST['country'], $_POST['reasons'], $_POST['outcome'], $_POST['nameofseminar'], $_POST['nameofcertif'], $_POST['costsPDV'], $_POST['wage'], $_POST['accommodation'], $_POST['transport'], $_POST['totalcosts'], $_POST['organizer'], $_POST['remark'], $_POST['check_list'], $_POST['ostalo'], $_POST['comment'], '0'));
            if ($res->rowCount() == 1) {
                echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

                ?>
                <?php


                $query = $db->query("SELECT MAX(request_id) as maxrequest FROM  " . $portal_trainings . "  WHERE user_id='" . $_user['user_id'] . "'");

                foreach ($query as $item) {
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
                $mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
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


                $bodyContent = '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;">Poštovana/i, </h5 >';
                $bodyContent .= '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za ekstreni trening ' . ' ' . $_POST['nameofseminar'] . ' ' . ' za' . ' ' . $fname . ' ' . $lname . ' ' . ' zaposlenika na radnom mjestu' . ' ' . $position . ' ' . ' u  ' . ' ' . $departmentcode . ' ' . ' u periodu od ' . ' ' . date('d-m-Y', strtotime($fromD)) . ' ' . ' do' . ' ' . date('d-m-Y', strtotime($toD)) . ' ' . '. <br>Za direktan pristup odobravanju Zahtjeva, kliknite </h5 >';
                $bodyContent .= '<a  style="margin:0px; font-family: Calibri;font-size:14px;" href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=trainings&p=popup_trainings_reponse&id=' . $tools_id . '> OVDJE.</a>';
                $bodyContent .= '<h5 style="color:gray;font-weight:normal; font-family: Calibri;font-size:14px;">S poštovanjem, <br> Vaš HR Tim </h5 >';
                $mail->Subject = 'Employee Portal!';
                $mail->Body = $bodyContent;


                if (!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else { ?>


                <?php }


            }

        } else echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Datum završetka ne može biti manji od datuma početka usavršavanja!') . '</div>';

    }

    /*****************Business trip********************/


    if ($_POST['request'] == 'business-trip-request-add') {

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));


        $df = explode('/', $_POST['DateFrom']);
        $dt = explode('/', $_POST['DateTo']);
        $dp = explode('/', $_POST['DateDetermined']);
        $dc = explode('/', $_POST['DateOfCalculation']);


        $fromD = $df[2] . '-' . $df[1] . '-' . $df[0];
        $toD = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
        $dateD = $dp[2] . '-' . $dp[1] . '-' . $dp[0];
        $dateC = $dc[2] . '-' . $dc[1] . '-' . $dc[0];

        $parent_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['parent'] . "'");

        foreach ($parent_query as $uquery) {

            $email_parent = $uquery['email'];

        }

        $stream_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['stream_parent'] . "'");

        foreach ($stream_query as $uquery) {

            $email_stream_parent = $uquery['email'];

        }


        $parent_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['parent'] . "'");

        foreach ($parent_query as $uquery) {

            $email_parent = $uquery['email'];

        }


        $admin_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['to_admin'] . "'");

        foreach ($admin_query as $uquery) {

            $email_admin = $uquery['email'];

        }

        $admin2_query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $_user['to_admin2'] . "'");

        foreach ($admin2_query as $uquery) {

            $email_admin2 = $uquery['email'];

        }

        $user = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='" . $_user['user_id'] . "'");

        foreach ($user as $uquery2) {

            $fname = $uquery2['fname'];
            $lname = $uquery2['lname'];


            $position = $uquery2['position'];
            $departmentcode = $uquery2['department_code'];
            $sector = $uquery2['sector'];
            $jmb = $uquery2['JMB'];


        }


        if ($fromD <= $toD) {

            if ($_POST['countryino'] == 1) {

                $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip] (
      user_id,hr,admin,admin2,date_created,h_from,h_to,date_determined,date_of_calculation,zahtjev_vs_nalog,status,status_parent2,status_admin,type,is_archive,send_email,
      destination,country_ino,purpose_trip,reasons,time_of_seminar,
      check_list1,check_list2,transport,transport_details,number_of_employee,
      check_list3,transport_notes,accommodation,accommodation_details,
      check_list4,check_list5,limit_notes,check_list6,total_f,amount_f,number_f) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array($_user['user_id'], $_user['stream_parent'], $_user['to_admin'], $_user['to_admin2'],
                        date('Y-m-d', strtotime("now")),
                        date('Y-m-d', strtotime($fromD)) . ' ' . $_POST['TimeFrom'],
                        date('Y-m-d', strtotime($toD)) . ' ' . $_POST['TimeTo'],
                        date('Y-m-d', strtotime($dateD)),
                        date('Y-m-d', strtotime($dateC)),
                        '0',
                        '0',
                        '0',
                        '0',
                        'SLUŽBENI PUT',
                        '0',
                        '0',
                        $_POST['destination'], $_POST['countryino'], $_POST['purpose_trip'], $_POST['reasons'], $_POST['TimeOfSeminar'],
                        $_POST['check_list1'], $_POST['check_list2'], $_POST['transport'], $_POST['transport_details'], $_POST['numberOfEmployee'],
                        $_POST['check_list3'], $_POST['transport_notes'], $_POST['atribut'], $_POST['accommodation_details'],
                        $_POST['check_list4'], $_POST['check_list5'], $_POST['limit_notes'], $_POST['check_list6'], $_POST['total_f'], $_POST['amount_f'], $_POST['number_f']));
                if ($res->rowCount() == 1) {


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


                    $ukupni = array();
                    foreach ($_POST['task'] as $key => $task) {
                        $ukupni[] = array("number" => $_POST['task'][$key], "total" => $_POST['task4'][$key], "amount" => $_POST['amount'][$key], "country" => $_POST['country'][$key]);

                    }


                    foreach ($ukupni as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }

                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_item] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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

                    $ukupni_smjestaj = array();
                    foreach ($_POST['accommodation1'] as $key => $task) {
                        $ukupni_smjestaj[] = array("number" => $_POST['accommodation1'][$key], "amount" => $_POST['accommodation2'][$key], "total" => $_POST['accommodation4'][$key]);
                    }
                    foreach ($ukupni_smjestaj as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    $ukupni_prijevoz = array();
                    foreach ($_POST['transport1'] as $key => $task) {
                        $ukupni_prijevoz[] = array("number" => $_POST['transport1'][$key], "amount" => $_POST['transport2'][$key], "total" => $_POST['transport4'][$key]);
                    }

                    foreach ($ukupni_prijevoz as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_transport] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    $ukupni_ostalitroskovi = array();
                    foreach ($_POST['otherCosts1'] as $key => $task) {
                        $ukupni_ostalitroskovi[] = array("number" => $_POST['otherCosts1'][$key], "amount" => $_POST['otherCosts2'][$key], "total" => $_POST['otherCosts4'][$key]);
                    }
                    foreach ($ukupni_ostalitroskovi as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

                    ?>
                    <?php


                    $query = $db->query("SELECT MAX(request_id) as maxrequest FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE user_id='" . $_user['user_id'] . "'");

                    foreach ($query as $item) {
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
                    $mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
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

                    $bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za Službeni put za' . ' ' . $fname . ' ' . $lname . ' ' . ' zaposlenika na radnom mjestu' . ' ' . $position . ' ' . ' u  ' . ' ' . $departmentcode . ' ' . ' u periodu od ' . ' ' . date('d-m-Y', strtotime($fromD)) . ' ' . ' do' . ' ' . date('d-m-Y', strtotime($toD)) . ' ' . '. </h5 >';
                    $bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip_reponse&id=' . $tools_id . '>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >';
                    $mail->Subject = 'Employee Portal!';
                    $mail->Body = $bodyContent;

                    if (!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else { ?>


                    <?php }


                }
            }

            /******************INO DNEVNICE START ********************/
            if ($_POST['countryino'] != 1) {

                $data = "INSERT INTO [c0_intranet2_apoteke].[dbo].[business_trip] (
      user_id,parent,hr,admin,admin2,date_created,h_from,h_to,date_determined,date_of_calculation,zahtjev_vs_nalog,status,status_parent2,status_hr,status_admin,type,is_archive,send_email,
      destination,country_ino,purpose_trip,reasons,time_of_seminar,
      check_list1,check_list2,transport,transport_details,number_of_employee,
      check_list3,transport_notes,accommodation,accommodation_details,
      check_list4,check_list5,limit_notes,check_list6,total_f,amount_f,number_f) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                $res = $db->prepare($data);
                $res->execute(
                    array($_user['user_id'], $_user['parent'], $_user['stream_parent'], $_user['to_admin'], $_user['to_admin2'],
                        date('Y-m-d', strtotime("now")),
                        date('Y-m-d', strtotime($fromD)) . ' ' . $_POST['TimeFrom'],
                        date('Y-m-d', strtotime($toD)) . ' ' . $_POST['TimeTo'],
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
                        $_POST['destination'], $_POST['countryino'], $_POST['purpose_trip'], $_POST['reasons'], $_POST['TimeOfSeminar'],
                        $_POST['check_list1'], $_POST['check_list2'], $_POST['transport'], $_POST['transport_details'], $_POST['numberOfEmployee'],
                        $_POST['check_list3'], $_POST['transport_notes'], $_POST['atribut'], $_POST['accommodation_details'],
                        $_POST['check_list4'], $_POST['check_list5'], $_POST['limit_notes'], $_POST['check_list6'], $_POST['total_f'], $_POST['amount_f'], $_POST['number_f']));
                if ($res->rowCount() == 1) {


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


                    $ukupni = array();
                    foreach ($_POST['task'] as $key => $task) {
                        $ukupni[] = array("number" => $_POST['task'][$key], "total" => $_POST['task4'][$key], "amount" => $_POST['amount'][$key], "country" => $_POST['country'][$key]);

                    }


                    foreach ($ukupni as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }

                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_item] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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

                    $ukupni_smjestaj = array();
                    foreach ($_POST['accommodation1'] as $key => $task) {
                        $ukupni_smjestaj[] = array("number" => $_POST['accommodation1'][$key], "amount" => $_POST['accommodation2'][$key], "total" => $_POST['accommodation4'][$key]);
                    }
                    foreach ($ukupni_smjestaj as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_accommodation] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    $ukupni_prijevoz = array();
                    foreach ($_POST['transport1'] as $key => $task) {
                        $ukupni_prijevoz[] = array("number" => $_POST['transport1'][$key], "amount" => $_POST['transport2'][$key], "total" => $_POST['transport4'][$key]);
                    }

                    foreach ($ukupni_prijevoz as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_transport] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    $ukupni_ostalitroskovi = array();
                    foreach ($_POST['otherCosts1'] as $key => $task) {
                        $ukupni_ostalitroskovi[] = array("number" => $_POST['otherCosts1'][$key], "amount" => $_POST['otherCosts2'][$key], "total" => $_POST['otherCosts4'][$key]);
                    }
                    foreach ($ukupni_ostalitroskovi as $key => $task) {
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
                        if ($res2->rowCount() == 1) {
                            $st = 1;
                        }
                    }


                    $id2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip_otherCosts] WHERE request_id='" . $id . "'");
                    foreach ($id2 as $value) {
                        $item = $value['tripitem_id'];

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


                    echo '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';

                    ?>
                    <?php


                    $query = $db->query("SELECT MAX(request_id) as maxrequest FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE user_id='" . $_user['user_id'] . "'");

                    foreach ($query as $item) {
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
                    $mail->Host = gethostbyname('mail.teneo.ba');                   // Specify main and backup SMTP servers
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

                    $bodyContent = '<h5 style="color:gray;font-weight:normal;">Poštovana/i, </h5 >';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;"> Molimo Vas da razmotrite odobravanje podnesenog Zahtjeva za Službeni put za' . ' ' . $fname . ' ' . $lname . ' ' . ' zaposlenika na radnom mjestu' . ' ' . $position . ' ' . ' u  ' . ' ' . $departmentcode . ' ' . ' u periodu od ' . ' ' . date('d-m-Y', strtotime($fromD)) . ' ' . ' do' . ' ' . date('d-m-Y', strtotime($toD)) . ' ' . '. </h5 >';
                    $bodyContent .= '<a href=http://infodom-server.sarajevo-infodom.ba:81/app/?m=business_trip&p=popup_business_trip_reponse&id=' . $tools_id . '>Za direktan pristup odobravanju Zahtjeva, kliknite ovdje.</a>';
                    $bodyContent .= '<h5 style="color:gray;font-weight:normal;">S poštovanjem, <br> Vaš HR Tim </h5 >';
                    $mail->Subject = 'Employee Portal!';
                    $mail->Body = $bodyContent;

                    if (!$mail->send()) {
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else { ?>


                    <?php }


                }
            }

            /*******INO DNEVNICE END *****************************/


        } else echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Datum završetka ne može biti manji od datuma početka usavršavanja!') . '</div>';

    }


}

if (isset($_GET['request'])) {
    if ($_GET['request'] == 'check-month-add') {

        $query = $db->query("SELECT count(*) as broj FROM  " . $portal_hourlyrate_month . "   where month = " . $_GET['month']);

        foreach ($query as $item) {
            $num_users = $item['broj'];
        }
        echo $num_users;
    }
}

?>
