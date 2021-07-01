<?php
_pagePermission(5, false);
?>

<!-- START - Main section -->
<section class="full tasks-page">

    <div class="container-fluid">
        <?php

        if (isset($_GET['year'])) {
            $year_tasks = $_GET['year'];
            if ($year_tasks == '')
                $year_tasks = date("Y");
        } else
            $year_tasks = date("Y");
        ?>

        <?php
        $query_deadline = $db->query("SELECT * FROM  " . $portal_objective_deadline . "  WHERE YEAR = " . $year_tasks);
        foreach ($query_deadline as $item) {
            $year = $item['year'];
            $period_start = $item['period_start'];
            $period_end = $item['period_end'];

            if ($item['phase'] == 1)
                $rok_cilj = $item['objective_deadline'];

            if ($item['phase'] == 2)
                $procjena = $item['objective_deadline'];

            if ($item['phase'] == 3)
                $evaluacija = $item['objective_deadline'];

            if ($item['locked'] == 0)
                $faza = $item['phase'];
        }

        if (!isset($faza)) {
            echo '<br/><br/><br/><span style="height:20px; background:#006595  ;color: #ffffff;padding-left:20px;display:block;width:300px;">SVE FAZE SU ZAKLJUČANE!</span><br/>';
            return;
        }

        ?>

        <?php
        $check_status = $db->query("SELECT * FROM  " . $portal_objective_status . "  WHERE user_id='" . $_user['user_id'] . "' AND phase =" . $faza . " AND year = " . $year_tasks);
        foreach ($check_status as $item) {
            $step1 = $item['step1'];
            $step2 = $item['step2'];
            $step3 = $item['step3'];
            $step4 = $item['step4'];
            $step5 = $item['step5'];
            $status_obrasca = $item['status'];
            $potvrda4 = $item['potvrda4'];
            $datum_radnik = $item['datum_radnik'];
            $datum_nadredjeni = $item['datum_nadredjeni'];

        }

        $check_status = $db->query("SELECT * FROM  " . $portal_objective_status . "  WHERE user_id='" . $_user['user_id'] . "' AND phase =1 AND year = " . $year_tasks);
        foreach ($check_status as $item) {
            $step51 = $item['step5'];
        }

        $check_status = $db->query("SELECT * FROM  " . $portal_objective_status . "  WHERE user_id='" . $_user['user_id'] . "' AND phase =2 AND year = " . $year_tasks);
        foreach ($check_status as $item) {
            $step52 = $item['step5'];
        }

        $check_status = $db->query("SELECT * FROM  " . $portal_objective_status . "  WHERE user_id='" . $_user['user_id'] . "' AND phase =3 AND year = " . $year_tasks);
        foreach ($check_status as $item) {
            $step53 = $item['step5'];
        }
        ?>


        <div class="row">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-3">
                        <h2>
                            <?php echo __('MBO Ciljevi'); ?><br/><br/>
                        </h2>
                        <?php if (isset($status_obrasca) and (strpos($status_obrasca, 'vraceno_radniku;') !== false))
                            echo '<span style="height:20px; background: #bb1e41;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac vraćen zaposleniku</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'vraceno_nadredjenom;') !== false))
                            echo '<span style="height:20px; background: #bb1e41;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac vraćen nadređenom</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'poslao_radnik;') !== false))
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac poslan nadređenom</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'poslano_hr') !== false))
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac poslan HR-u!</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'potpisao_radnik;') !== false))
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac potpisao zaposlenik!</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'poslano_na_potpisivanje;') !== false) and (strpos($status_obrasca, 'potpisao_radnik;') == false))
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac poslan na potpisivanje!</span><br/>';
                        if (isset($status_obrasca) and (strpos($status_obrasca, 'potpisao_nadredjeni;') !== false)) {
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac potpisao nadređeni!</span><br/>';
                            echo '<span style="height:20px; background: #2834ce;color: #ffffff;padding-left:20px;display:block;width:300px;">Obrazac Zaključan</span><br/>';
                        }
                        ?>
                    </div>
                    <div class="col-sm-3" style="margin-top:20px;">
                        <div class="btn btn-filter active-task" style="margin-bottom: 15px;width: 130px;"><a
                                    style="color:black" class="active-white" href="?m=default&p=mbo_pocetna">Nazad na
                                početnu</a></div>
                        <div class="btn btn-filter active-task" style="margin-bottom: 15px;width: 130px;"><a
                                    style="color:black" class="active-white" href="?m=default&amp;p=mbo&f=0">Moj MBO
                                Profil</a></div>
                    </div>
                    <div class="col-sm-3" style="margin-top: 5px;">
                        <h4 style="margin-bottom: 3px;margin-left: 4px;"><?php echo __('Godina'); ?></h4>
                        <select id="year_tasks" name="year_tasks" class="rcorners1" style="outline:none">
                            <?php echo _optionTaskYearMBO($year_tasks); ?>
                        </select>
                    </div>
                </div>


                <div class="btn btn-filter <?php if ($faza == '1') {
                    echo 'active-task';
                } ?>" style="margin-bottom: 15px;width: 130px;margin-right:7px;"><a style="color:black"
                                                                                    class="<?php if ($faza == '1') {
                                                                                        echo 'active-white';
                                                                                    } ?>"
                                                                                    href="?m=default&amp;p=tasks&f=1<?php echo '&year=' . $year_tasks ?>">Postavljanje
                        ciljeva</a></div>
                <a style="color:black" <?php if ($step51) {
                    echo 'href="?m=default&amp;p=tasks&f=2"';
                } else {
                    echo 'href="?m=default&amp;p=tasks&f=1"';
                } ?>><i class="fa fa-arrow-right" style="position: absolute;margin-top: 9px;"
                        aria-hidden="true"></i></a>
                <div <?php if (!$step51) {
                    echo 'disabled="true"';
                } ?>" class="btn btn-filter <?php if ($faza == '2') {
                    echo 'active-task';
                } ?>" style="margin-bottom: 15px;width: 147px;margin-left:20px;margin-right: 15px;"> <a
                        style="color:black" class="<?php if ($faza == '2') {
                    echo 'active-white';
                } ?>" href="?m=default&amp;p=tasks&f=2<?php echo '&year=' . $year_tasks ?>">Polugodišnja procjena</a>
            </div>
            <i class="fa fa-arrow-right" style="position: absolute;margin-top: 9px;" aria-hidden="true"></i><a
                    style="color:black" <?php if ($step52) {
                echo 'href="?m=default&amp;p=tasks&f=3"';
            } else {
                echo 'href="?m=default&amp;p=tasks&f=2"';
            } ?>><i class="fa fa-arrow-right" style="position: absolute;margin-top: 9px;" aria-hidden="true"></i></a>
            <div <?php if (!$step52 or !$step51) {
                echo 'disabled="true"';
            } ?>" class="btn btn-filter <?php if ($faza == '3') {
                echo 'active-task';
            } ?>" style="margin-bottom: 15px;width: 120px;margin-left:20px;margin-right: 15px;"> <a style="color:black"
                                                                                                    class="<?php if ($faza == '3') {
                                                                                                        echo 'active-white';
                                                                                                    } ?>"
                                                                                                    href="?m=default&amp;p=tasks&f=3<?php echo '&year=' . $year_tasks ?>">Evaluacija
                ciljeva</a></div>
        <i class="fa fa-arrow-right" style="position: absolute;margin-top: 9px;" aria-hidden="true"></i><a
                style="color:black" <?php if ($step52) {
            echo 'href="?m=default&amp;p=tasks&f=3"';
        } else {
            echo 'href="?m=default&amp;p=tasks&f=2"';
        } ?>><i class="fa fa-arrow-right" style="position: absolute;margin-top: 9px;" aria-hidden="true"></i></a>
        <div class="btn btn-filter <?php if ($step53) {
            echo 'active-task-red';
        } ?>" style="margin-bottom: 15px;width: 120px;margin-left:20px;"><label style="color:black;height: 0px;"
                                                                                class="<?php if ($step53) {
                                                                                    echo 'active-white';
                                                                                } ?>">Završeno</label></div>
        <div class="">
            <div class="box">
                <div class="head">
                    <div class="box-head-btn">
                        <a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a"></a>
                    </div>
                    <h3><?php echo __('Podaci o zaposleniku'); ?></h3>
                </div>
                <div class="content" id="c1a" style="display: block;">
                    <table class="alt">
                        <tr>
                            <td><b><?php echo __('Prezime i ime:'); ?></b></td>
                            <td><?php echo $_user['lname'] . ' ' . $_user['fname']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('JMB:'); ?></b></td>
                            <td><?php echo $_user['employee_no']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Naziv pozicije:'); ?></b></td>
                            <td><?php echo $_user['position']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Organizaciona jedinica:'); ?></b></td>
                            <td><?php echo $_user['sector']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Nadređena OJ:'); ?></b></td>
                            <td><?php echo _employee($_user['parent'])['sector']; ?></td>
                        </tr>
                        <tr>
                            <td><b><?php echo __('Kompanija:'); ?><b></td>
                            <td><?php echo _settings('company_name'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <span class="klasa1" style="height:20px;padding-left:20px;">Period procjene</span><br/>
        <span style="padding-left:5px;"><b>Datum od : </b></span><span
                style="height:10px;padding-left:10px;padding-right:10px;"><?php echo date('d/m/Y', strtotime($period_start)); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="padding-left:5px;"><b>Datum do : </b></span><span
                style="height:10px;padding-left:10px;padding-right:10px;"><?php echo date('d/m/Y', strtotime($period_end)); ?></span><br/><br/>
    </div>
    </div>

    <?php
    $ponder_sum = 0;
    $ocjena_ciljeva = 0;
    $ocjena_kompetencija = 0;
    $ponder_sum_ocjene = 0;
    $ocjena_ucinka = 0;

    $limit = 20;

    if ($_num)
        $offset = ($_num - 1) * $limit;
    else
        $offset = 0;
    $_num = 1;

    $where = "WHERE user_id='" . $_user['user_id'] . "' AND year = " . $year_tasks;


    if (isset($_GET['t']))
        $where .= " AND is_archive='1'";
    else
        $where .= " AND is_archive='0'";


    $query_individualni = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_tasks . "  " . $where . " AND task_type in (0)  ORDER BY task_type, date_created DESC");
    $total_individualni = $query_individualni->rowCount();

    $query_timski = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_tasks . "  " . $where . " AND task_type in (1)  ORDER BY task_type, date_created DESC");
    $total_timski = $query_timski->rowCount();

    $query_razvojni = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_tasks . "  " . $where . " AND task_type in (2)  ORDER BY task_type, date_created DESC");
    $total_razvojni = $query_razvojni->rowCount();

    $query4 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_misc . "  WHERE user_id='" . $_user['user_id'] . "' AND year = " . $year_tasks);

    foreach ($query4 as $item) {
        $kompetencija1 = $item['kompetencija1'];
        $kompetencija2 = $item['kompetencija2'];
        $kompetencija3 = $item['kompetencija3'];
        $kompetencija1_rating = $item['kompetencija1_rating'];
        $kompetencija1_rating_user = $item['kompetencija1_rating_user'];
        $kompetencija2_rating = $item['kompetencija2_rating'];
        $kompetencija2_rating_user = $item['kompetencija2_rating_user'];
        $kompetencija3_rating = $item['kompetencija3_rating'];
        $kompetencija3_rating_user = $item['kompetencija3_rating_user'];
        $obavezna1_rating = $item['obavezna1_rating'];
        $obavezna1_rating_user = $item['obavezna1_rating_user'];
        $obavezna2_rating = $item['obavezna2_rating'];
        $obavezna2_rating_user = $item['obavezna2_rating_user'];
        $komentar = $item['komentar'];
        $l_potencijal = $item['l_potencijal'];
    }


    $query5 = $db->query("SELECT TOP " . $limit . "* FROM  " . $portal_ocjene . "  WHERE user_id='" . $_user['user_id'] . "' AND year = " . $year_tasks);
    $total_ocjena = $query5->rowCount();

    if ($total_ocjena < 0) {
        foreach ($query5 as $item) {
            $ponder_sum_ocjene = $item['ponder_sum'];
        }
    }


    ?>
    <div id="ciljevi">
        <span class="klasa1"
              style="height:20px;padding-left:20px;padding-right:518px;">MBO ciljevi za <?php echo $year_tasks; ?></span><br/>
        <label style="height:20px;color: black;width: 400px;">Minimalni broj postavljenih ciljeva (Timski + Individualni
            ciljevi) je 5.</label><br/>

        <?php
        if ($total_timski < 0) {
            foreach ($query_timski as $key => $item) {

                $tools_id = $item['task_id'];

                $border = '';

                if ($item['is_accepted'] == 1) {
                    $border = 'gray';
                } else {
                    $border = 'blue';
                }


                $parent = _user($item['parent_id']);
                $user = _user($item['user_id']);

                if ($item['status'] != '4' and $item['status'] != '5') {
                    $ponder_sum += $item['ponder'];

                    $ponder_value = $item['ponder'] / 100 * $item['rating'];
                    $ocjena_ciljeva += $ponder_value;

                }

                $commentsCount2 = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $commentsCount = $db->query("SELECT count(*) as broj FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $total_comments = $commentsCount->fetch();

                ?>

                <?php if ($key == 0) { ?>
                    <div class="text-right row" style="width:125px;float:right;margin-top: -21px;margin-right: 0px;">
                        <label style="height:20px;color: red;width: 100px;">min 1 - max 3</label><br/>
                    </div>
                <?php } ?>


                <div class="box box-lborder box-lborder-<?php echo $border; ?> <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                    echo 'gray-denis';
                } ?>" id="opt-<?php echo $tools_id; ?>">
                    <div class="content">

                        <div class="row">
                            <div class="col-sm-3" style="">
                                <?php
                                echo _('<b>Timski cilj </b>| Naziv cilja : ' . $item['task_name']); ?><br/>
                                <textarea
                                        class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                            echo 'gray-denis';
                                        } ?>" readonly style="width: -webkit-fill-available"
                                        name="task_description"><?php echo $item['task_description']; ?></textarea><br/>
                                <?php echo date('d/m/Y', strtotime($item['date_created'])); ?>
                                <?php
                                if ($item['status'] == '4' or $item['status'] == '5') {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Cilj izmjenjen') . '</span>';
                                } elseif ($item['ponder'] == 0) {
                                    echo ' &nbsp; - <span style="color:red;background:none;">' . __('Cilj odbijen') . '</span>';
                                } elseif ($item['is_accepted'] == 0 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('U procesu...') . '</span>';
                                } elseif ($item['is_accepted'] == 1 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Na odobrenju nadređenog') . '</span>';
                                } elseif ($item['is_accepted'] == 2 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#aaa;background:none;">' . __('Prihvaćen') . '</span>';
                                }

                                ?>
                                <?php if ($total_comments['broj'] > 0) { ?>
                                    &nbsp; &nbsp; &nbsp; <i
                                            class="ion-chatbox-working"></i> <?php echo $total_comments['broj']; ?>
                                <?php } ?>
                            </div>
                            <?php if ($faza == '1' or $faza == '2' or $faza == '3') { ?>

                                <div class="col-sm-2" style="width:12%;">
                                    <?php echo __('KPI'); ?><br/>
                                    <textarea id="KPI_timski"
                                              style="width: -webkit-fill-available" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'readonly';
                                    } ?> class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'gray-denis';
                                    } ?>" name="<?php echo 'KPI-' . $item['task_id']; ?>"
                                              spellcheck="false"><?php echo $item['KPI']; ?></textarea><br/>
                                </div>
                            <?php } ?>


                            <?php if ($faza == '2' or $faza == '3') { ?>

                                <div class="col-sm-2" style="width:12%;">
                                    <?php echo __('Ostvarenje'); ?><br/>
                                    <textarea id="ostvarenje_timski"
                                              style="width: -webkit-fill-available" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'readonly';
                                    } ?> class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'gray-denis';
                                    } ?>" name="<?php echo 'ostvarenje-' . $item['task_id']; ?>"
                                              spellcheck="false"><?php echo $item['ostvarenje']; ?></textarea><br/>
                                </div>
                            <?php } ?>

                            <div class="col-sm-1" style="">
                                <?php echo "<b>" . __('Ponder') . "</b>"; ?><br/> <?php echo $item['ponder'] . " %"; ?>
                                <br/>
                            </div>

                            <div class="col-sm-1" style="">
                                <?php echo "<b>" . __('Rok') . "</b>"; ?>
                                <br/><?php echo date('d/m/Y', strtotime($item['date_end'])); ?><br/>
                            </div>


                            <?php if ($faza == '2') { ?>
                                <div class="col-sm-1">
                                    <?php echo "<b style='margin-left:5px'>" . __('Status') . "</b>"; ?><br/>
                                    <select id="status_timski-<?php echo $item['ponder']; ?>" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'disabled';
                                    } ?> name="<?php echo $item['task_id']; ?>"
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>"
                                            class="form-control <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" style="width:120px;outline:none;">
                                        <?php echo _optionTaskStatus($item['status']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>




                            <?php if ($faza == '3') { ?>
                                <div class="col-sm-2" style="width:10%;">
                                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                                    <select id="ocjena_user_timski"
                                            name="<?php echo 'ocjena_timski_user-' . $item['task_id']; ?>" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'disabled';
                                    } ?>
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;">
                                        <?php echo _optionTaskOcjena($item['user_rating']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>


                            <?php if ($faza == '3' and $step3) { ?>
                                <div class="col-sm-2" style="width:10%;">
                                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                                    <select id="ocjena_timski" name="<?php echo 'ocjena_timski-' . $item['task_id']; ?>"
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;" disabled>
                                        <?php echo _optionTaskOcjena($item['rating']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>


                            <div class="col-sm-2" style="width:15%;margin-left:15px;margin-top:15px;">

                                <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_view.php?id=' . $tools_id; ?>"
                                   class="table-btn alt" data-widget="ajax" data-id="opt2" data-width="600"><i
                                            class="ion-eye"></i></a>
                                <?php if ($item['is_user_reviewed'] == 1 && $item['is_archive'] == 0) { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_archive:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite arhivirati zadatak?'); ?>"><i
                                                class="ion-folder"></i></a>
                                <?php } ?>
                                <?php if ($faza == '1' and $item['ponder'] != 0) { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_remove:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite poništiti zadatak?'); ?>"><i
                                                class="ion-android-close"></i></a>
                                <?php } ?>
                            </div>

                            <?php if ($key == 0) { ?>
                                <a href="<?php echo $url . '/uploads/SMART_Postavljanje ciljeva.pptx'; ?>"
                                   style="float: right;margin-right: 20px;">
                                    <img border="0" alt="W3Schools"
                                         src="<?php echo $_uploadUrl . '/smart_ciljevi.png'; ?>" width="" height="">
                                </a>
                            <?php } ?>

                        </div>

                    </div>
                </div>

            <?php }

        } else { ?>
            <div class="text-center">
                <?php echo __('Nema spašenih timskih ciljeva'); ?>
            </div>
        <?php } ?>

        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_add.php?task_type=1&ponder_sum=' . $ponder_sum_ocjene; ?>"
           style="margin-bottom: 10px;margin-top: -10px;padding-right:75px;" data-widget="ajax-task" data-id="opt2"
           data-width="400px" class="btn btn-red btn-xs"><?php echo __('Dodaj novi timski cilj'); ?> <i
                    style="top:-5px;" class="ion-ios-plus-empty"></i></a>

        <?php
        if ($total_individualni < 0) {
            foreach ($query_individualni as $key => $item) {

                $tools_id = $item['task_id'];

                $border = '';

                if ($item['is_accepted'] == 1) {
                    $border = 'gray';
                } else {
                    $border = 'blue';
                }


                $parent = _user($item['parent_id']);
                $user = _user($item['user_id']);

                if ($item['status'] != '4' and $item['status'] != '5') {
                    $ponder_sum += $item['ponder'];

                    $ponder_value = $item['ponder'] / 100 * $item['rating'];
                    $ocjena_ciljeva += $ponder_value;

                }


                $commentsCount2 = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $commentsCount = $db->query("SELECT count(*) as broj FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $total_comments = $commentsCount->fetch();
                ?>

                <?php if ($key == 0) { ?>
                    <div class="text-right row" style="width:125px;float:right;<?php if ($step1) {
                        echo 'margin-top:-20px';
                    } ?>;margin-right: 0px;">
                        <label style="height:20px;color: red;width: 100px;">min 4 - max 7</label><br/>
                    </div>
                <?php } ?>


                <div class="box box-lborder box-lborder-<?php echo $border; ?> <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                    echo 'gray-denis';
                } ?>" id="opt-<?php echo $tools_id; ?>">
                    <div class="content">

                        <div class="row">
                            <div class="col-sm-3" style="">
                                <?php
                                echo _('<b>Individualni cilj </b>| Naziv cilja : ' . $item['task_name']); ?><br/>
                                <textarea
                                        class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                            echo 'gray-denis';
                                        } ?>" readonly style="width: -webkit-fill-available"
                                        name="task_description"><?php echo $item['task_description']; ?></textarea><br/>
                                <?php echo date('d/m/Y', strtotime($item['date_created'])); ?>
                                <?php
                                if ($item['status'] == '4' or $item['status'] == '5') {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Cilj izmjenjen') . '</span>';
                                } elseif ($item['ponder'] == 0) {
                                    echo ' &nbsp; - <span style="color:red;background:none;">' . __('Cilj odbijen') . '</span>';
                                } elseif ($item['is_accepted'] == 0 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('U procesu...') . '</span>';
                                } elseif ($item['is_accepted'] == 1 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Na odobrenju nadređenog') . '</span>';
                                } elseif ($item['is_accepted'] == 2 and $item['ponder'] != 0) {
                                    echo ' &nbsp; - <span style="color:#aaa;background:none;">' . __('Prihvaćen') . '</span>';
                                }
                                ?>
                                <?php if ($total_comments['broj'] > 0) { ?>
                                    &nbsp; &nbsp; &nbsp; <i
                                            class="ion-chatbox-working"></i> <?php echo $total_comments['broj']; ?>
                                <?php } ?>
                            </div>
                            <?php if ($faza == '1' or $faza == '2' or $faza == '3') { ?>

                                <div class="col-sm-2" style="width:12%;">
                                    <?php echo __('KPI'); ?><br/>
                                    <textarea id="KPI_individualni"
                                              style="width: -webkit-fill-available" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'readonly';
                                    } ?> class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'gray-denis';
                                    } ?>" name="<?php echo 'KPI-' . $item['task_id']; ?>"
                                              spellcheck="false"><?php echo $item['KPI']; ?></textarea><br/>
                                </div>
                            <?php } ?>


                            <?php if ($faza == '2' or $faza == '3') { ?>

                                <div class="col-sm-2" style="width:12%;">
                                    <?php echo __('Ostvarenje'); ?><br/>
                                    <textarea id="ostvarenje_individualni"
                                              style="width: -webkit-fill-available" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'readonly';
                                    } ?> class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'gray-denis';
                                    } ?>" name="<?php echo 'ostvarenje-' . $item['task_id']; ?>"
                                              spellcheck="false"><?php echo $item['ostvarenje']; ?></textarea><br/>
                                </div>
                            <?php } ?>

                            <div class="col-sm-1" style="">
                                <?php echo "<b>" . __('Ponder') . "</b>"; ?><br/> <?php echo $item['ponder'] . " %"; ?>
                                <br/>
                            </div>

                            <div class="col-sm-1" style="">
                                <?php echo "<b>" . __('Rok') . "</b>"; ?>
                                <br/><?php echo date('d/m/Y', strtotime($item['date_end'])); ?><br/>
                            </div>


                            <?php if ($faza == '2') { ?>
                                <div class="col-sm-1">
                                    <?php echo "<b style='margin-left:5px'>" . __('Status') . "</b>"; ?><br/>
                                    <select id="status_individualni-<?php echo $item['ponder']; ?>" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'disabled';
                                    } ?> name="<?php echo $item['task_id']; ?>"
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;">
                                        <?php echo _optionTaskStatus($item['status']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>




                            <?php if ($faza == '3') { ?>
                                <div class="col-sm-2" style="width:10%;">
                                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                                    <select id="ocjena_user_individualni"
                                            name="<?php echo 'ocjena_individualni_user-' . $item['task_id']; ?>" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'disabled';
                                    } ?>
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;">
                                        <?php echo _optionTaskOcjena($item['user_rating']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>


                            <?php if ($faza == '3' and $step3) { ?>
                                <div class="col-sm-2" style="width:10%;">
                                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                                    <select id="ocjena_individualni"
                                            name="<?php echo 'ocjena_individualni-' . $item['task_id']; ?>"
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo 'gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;" disabled>
                                        <?php echo _optionTaskOcjena($item['rating']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>


                            <div class="col-sm-2" style="width:15%;margin-left:15px;margin-top:15px;">

                                <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_view.php?id=' . $tools_id; ?>"
                                   class="table-btn alt" data-widget="ajax" data-id="opt2" data-width="600"><i
                                            class="ion-eye"></i></a>
                                <?php if ($item['is_user_reviewed'] == 1 && $item['is_archive'] == 0) { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_archive:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite arhivirati zadatak?'); ?>"><i
                                                class="ion-folder"></i></a>
                                <?php } ?>
                                <?php if ($faza == '1' and $item['ponder'] != 0) { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_remove:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite poništiti zadatak?'); ?>"><i
                                                class="ion-android-close"></i></a>
                                <?php } ?>
                            </div>

                        </div>

                    </div>
                </div>

            <?php }

        } else { ?>
            <div class="text-center">
                <?php echo __('Nema spašenih individualnih ciljeva'); ?>
            </div>
        <?php } ?>

        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_add.php?task_type=0&ponder_sum=' . $ponder_sum_ocjene; ?>"
           style="margin-bottom: 10px;margin-top: -10px;" data-widget="ajax-task" data-id="opt2" data-width="400px;"
           class="btn btn-red btn-xs"><?php echo __('Dodaj novi individualni cilj'); ?> <i style="top:-5px;"
                                                                                           class="ion-ios-plus-empty"></i></a>

        <?php if ($faza != 3) {
            if ($faza == 1) {
                echo '<br/><span class="col-sm-3" style="height:20px;padding-left:20px;display:inline;">Ukupna suma pondera (suma treba biti 100%) :</span>';
                echo '<span class="col-sm-3" style="height:20px;padding-left:20px;width:12%;display:inline;"></span>';
                echo '<span class="col-sm-1" style="height:20px;width:5%;display:inline;">' . $ponder_sum . ' % </span>';
                echo '<span class="col-sm-3" style="height:20px;padding-left:20px;width:58%;display:inline;"></span><br/><br/><br/>';
            } else {
                echo '<br/><span class="col-sm-3" style="height:20px;padding-left:20px;display:inline;">Ukupna suma pondera (suma treba biti 100%) :</span>';
                echo '<span class="col-sm-3" style="height:20px;padding-left:20px;width:24%;display:inline;"></span>';
                echo '<span class="col-sm-1" style="height:20px;width:5%;display:inline;">' . $ponder_sum . ' % </span>';
                echo '<span class="col-sm-3" style="height:20px;padding-left:20px;width:46%;display:inline;"></span><br/><br/><br/>';
            }
        } else {
            if ($step3) {
                echo '<br/><span class="col-sm-10" style="height:20px;padding-left:20px;width:80%;display:inline;">Ukupna ocjena ciljeva :</span>';
                echo '<span class="col-sm-1" style="height:20px;width:5%;display:inline;">' . number_format((float)$ocjena_ciljeva, 2, '.', '') . '</span>';
                echo '<span class="col-sm-3" style="height:20px;padding-left:20px;width:15%;display:inline;"></span><br/><br/><br/>';
            }
        } ?>
        <span class="klasa1" style="height:20px;padding-left:20px;padding-right:896px;">Kompetencije</span><br/><br/>
        <div class="col-sm-4">
            <img src="<?php echo $_uploadUrl; ?>/<?php echo 'kompetencije.jpg'; ?>" class="">
        </div>
        <div class="col-sm-8" style="font-family:sans-serif;">
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Sve kompetencije,</b> koje
                podržavaju naše korporativne vrijednosti, su obavezne i moraju biti uzete u obzir prlikom kreiranja
                razvojnih aktivnosti. </label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>1. Podsticanje promjena</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>2. Vlasništvo / Odgovornost za
                    postupke</b></label><br/><br/>

            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Molimo da izaberete tri izborne
                    kompetencije od ispod ponuđenih:</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b> - Sve za klijenta - Orjentisanost
                    na klijenta</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b> - Mi smo tim - Saradnja / Timski
                    Rad</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b> - Mi smo tim -
                    Fleksibilnost</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b> - Sve za klijenta - Kvalitet /
                    Tačnost</b></label><br/><br/>

            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Izborne kompetencije obavezne samo
                    za rukovodeće pozicije:</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>1. Ja sam vođa - Strateško
                    djelovanje (Obavezno samo za rukovodioce)</b></label><br/>
            <label style="height:20px;color: black;width: 843px;font-size:small;"><b>2. Ja sam vođa - Vodstvo (Obavezno
                    samo za rukovodioce)</b></label><br/><br/>

        </div>


        <div class="col-sm-8">
            <div class="col-sm-4">
                <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Obavezna kompetencija
                        1</b></label><br/>
                <label style="height:20px;color: black;width: 843px;font-size:small;">Podsticanje
                    promjena</label><br/><br/>
            </div>
            <?php if ($faza == '3') { ?>
                <div class="col-sm-2" style="width:160px;margin-left:40px;">
                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                    <select id="obavezna1_user" name="<?php echo 'obavezna1_user-' . $item['user_id']; ?>"
                            class="rcorners1" class="form-control" style="width:120px;outline:none;">
                        <?php echo _optionTaskOcjena($obavezna1_rating_user); ?>
                    </select><br/>
                </div>
            <?php } ?>


            <?php if ($faza == '3' and $step3) { ?>
                <div class="col-sm-2" style="width:160px;">
                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                    <select id="obavezna1" name="<?php echo 'obavezna1-' . $item['user_id']; ?>" class="rcorners1"
                            class="form-control" style="width:120px;outline:none;" disabled>
                        <?php echo _optionTaskOcjena($obavezna1_rating); ?>
                    </select><br/>
                </div>
                <?php
                $ponder_value_kom = 0.2 * $obavezna1_rating;
                $ocjena_kompetencija += $ponder_value_kom;
            } ?>
        </div>

        <div class="col-sm-8">
            <div class="col-sm-4">
                <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Obavezna kompetencija
                        2</b></label><br/>
                <label style="height:20px;color: black;width: 843px;font-size:small;">Vlasništvo / Odgovornost za
                    postupke</label><br/><br/>
            </div>
            <?php if ($faza == '3') { ?>
                <div class="col-sm-2" style="width:160px;margin-left:40px;">
                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                    <select id="obavezna2_user" name="<?php echo 'obavezna2_user-' . $item['user_id']; ?>"
                            class="rcorners1" class="form-control" style="width:120px;outline:none;">
                        <?php echo _optionTaskOcjena($obavezna2_rating_user); ?>
                    </select><br/>
                </div>
            <?php } ?>


            <?php if ($faza == '3' and $step3) { ?>
                <div class="col-sm-2" style="width:160px;">
                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                    <select id="obavezna2" name="<?php echo 'obavezna2-' . $item['user_id']; ?>" class="rcorners1"
                            class="form-control" style="width:120px;outline:none;" disabled>
                        <?php echo _optionTaskOcjena($obavezna2_rating); ?>
                    </select><br/>
                </div>
                <?php
                $ponder_value_kom = 0.2 * $obavezna2_rating;
                $ocjena_kompetencija += $ponder_value_kom;
            } ?>
        </div>

        <div class="col-sm-8">
            <div class="col-sm-4">
                <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Izborna kompetencija
                        1</b></label><br/>
                <select style="width:300px;outline:none" id="kompetencija1"
                        name="<?php echo 'kompetencija1-' . $item['user_id']; ?>" class="rcorners1"
                        class="form-control">
                    <?php echo _optionKompetencije($kompetencija1, $_user['user_id']); ?>
                </select>
            </div>
            <?php if ($faza == '3') { ?>
                <div class="col-sm-2" style="width:160px;margin-left:40px;">
                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                    <select id="kompetencija1_rating_user"
                            name="<?php echo 'kompetencija1_rating_user-' . $item['user_id']; ?>" class="rcorners1"
                            class="form-control" style="width:120px;outline:none;">
                        <?php echo _optionTaskOcjena($kompetencija1_rating_user); ?>
                    </select><br/>
                </div>
                <?php
            } ?>


            <?php if ($faza == '3' and $step3) { ?>
                <div class="col-sm-2" style="width:160px;">
                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                    <select id="kompetencija1_rating" name="<?php echo 'kompetencija1_rating-' . $item['user_id']; ?>"
                            class="rcorners1" class="form-control" style="width:120px;outline:none;" disabled>
                        <?php echo _optionTaskOcjena($kompetencija1_rating); ?>
                    </select><br/>
                </div>
                <?php
                $ponder_value_kom = 0.2 * $kompetencija1_rating;
                $ocjena_kompetencija += $ponder_value_kom;
            } ?>
            <br/><br/>
        </div>

        <div class="col-sm-8">
            <div class="col-sm-4">
                <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Izborna kompetencija
                        2</b></label><br/>
                <select style="width:300px;outline:none" id="kompetencija2"
                        name="<?php echo 'kompetencija2-' . $item['user_id']; ?>" class="rcorners1"
                        class="form-control">
                    <?php echo _optionKompetencije($kompetencija2, $_user['user_id']); ?>
                </select>
            </div>
            <?php if ($faza == '3') { ?>
                <div class="col-sm-2" style="width:160px;margin-left:40px;">
                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                    <select id="kompetencija2_rating_user"
                            name="<?php echo 'kompetencija2_rating_user-' . $item['user_id']; ?>" class="rcorners1"
                            class="form-control" style="width:120px;outline:none;">
                        <?php echo _optionTaskOcjena($kompetencija2_rating_user); ?>
                    </select><br/>
                </div>
            <?php } ?>


            <?php if ($faza == '3' and $step3) { ?>
                <div class="col-sm-2" style="width:160px;">
                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                    <select id="kompetencija2_rating" name="<?php echo 'kompetencija2_rating-' . $item['user_id']; ?>"
                            class="rcorners1" class="form-control" style="width:120px;outline:none;" disabled>
                        <?php echo _optionTaskOcjena($kompetencija2_rating); ?>
                    </select><br/>
                </div>
                <?php
                $ponder_value_kom = 0.2 * $kompetencija2_rating;
                $ocjena_kompetencija += $ponder_value_kom;
            } ?>
            <br/><br/>
        </div>

        <div class="col-sm-8">
            <div class="col-sm-4">
                <label style="height:20px;color: black;width: 843px;font-size:small;"><b>Izborna kompetencija
                        3</b></label><br/>
                <select style="width:300px;outline:none;" id="kompetencija3"
                        name="<?php echo 'kompetencija3-' . $item['user_id']; ?>" class="rcorners1"
                        class="form-control">
                    <?php echo _optionKompetencije($kompetencija3, $_user['user_id']); ?>
                </select><br/>
            </div>
            <?php if ($faza == '3') { ?>
                <div class="col-sm-2" style="width:160px;margin-left:40px;">
                    <?php echo __('Ocjena zaposlenika:'); ?><br/>
                    <select id="kompetencija3_rating_user"
                            name="<?php echo 'kompetencija3_rating_user-' . $item['user_id']; ?>" class="rcorners1"
                            class="form-control" style="width:120px;outline:none;">
                        <?php echo _optionTaskOcjena($kompetencija3_rating_user); ?>
                    </select><br/>
                </div>
            <?php } ?>


            <?php if ($faza == '3' and $step3) { ?>
                <div class="col-sm-2" style="width:160px;">
                    <?php echo __('Ocjena rukovodioca:'); ?><br/>
                    <select id="kompetencija3_rating" name="<?php echo 'kompetencija3_rating-' . $item['user_id']; ?>"
                            class="rcorners1" class="form-control" style="width:120px;outline:none;" disabled>
                        <?php echo _optionTaskOcjena($kompetencija3_rating); ?>
                    </select><br/>
                </div>
                <?php
                $ponder_value_kom = 0.2 * $kompetencija3_rating;
                $ocjena_kompetencija += $ponder_value_kom;
            } ?>
            <br/><br/><br/><br/>
        </div>

    </div>
    </div>

    <div id="ciljevi2">
        <?php if ($faza == 3) {
            if ($step3) {
                echo '<br/><span class="col-sm-10" style="height:20px;padding-left:20px;width:42%;display:inline;">Ukupna ocjena kompetencija :</span>';
                echo '<span class="col-sm-1" style="height:20px;width:5%;display:inline;">' . number_format((float)$ocjena_kompetencija, 2, '.', '') . '</span>';
                echo '<span class="col-sm-1" style="height:20px;width:53%;display:inline;"></span><br/><br/><br/>';
            }
        } ?>

        <span class="klasa1"
              style="height:20px;padding-left:20px;padding-right:889px;">Razvojni ciljevi za <?php echo $year_tasks; ?></span><br/>

        <?php

        if ($total_razvojni < 0) {

            foreach ($query_razvojni as $item) {
                $tools_id = $item['task_id'];

                $border = '';

                if ($item['is_accepted'] == 1) {
                    $border = 'gray';
                } else {
                    $border = 'blue';
                }


                $parent = _user($item['parent_id']);
                $user = _user($item['user_id']);

                $commentsCount2 = $db->query("SELECT * FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $commentsCount = $db->query("SELECT count(*) as broj FROM  " . $portal_comments . "  WHERE comment_on='$tools_id' AND type='task'");
                $total_comments = $commentsCount->fetch();

                ?>

                <div class="box box-lborder box-lborder-<?php echo $border; ?> <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                    echo 'gray-denis';
                } ?>" id="opt-<?php echo $tools_id; ?>">
                    <div class="content">

                        <div class="row">
                            <div class="col-sm-3" style="">
                                <?php echo _('<b>Razvojni cilj </b>|'); ?>
                                <?php echo __('Naziv cilja : '); ?><?php echo $item['task_name']; ?><br/>
                                <textarea
                                        class="<?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                            echo 'gray-denis';
                                        } ?>" readonly style="width: -webkit-fill-available"
                                        name="task_description"><?php echo $item['task_description']; ?></textarea><br/>
                                <?php echo date('d/m/Y', strtotime($item['date_created'])); ?>
                                <?php
                                if ($item['status'] == '4' or $item['status'] == '5') {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Cilj izmjenjen') . '</span>';
                                } elseif ($item['ponder'] == '0') {
                                    echo ' &nbsp; - <span style="color:red;background:none;">' . __('Cilj odbijen') . '</span>';
                                } elseif ($item['is_accepted'] == 0 and $item['ponder'] != '0') {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('U procesu...') . '</span>';
                                } elseif ($item['is_accepted'] == 1 and $item['ponder'] != '0') {
                                    echo ' &nbsp; - <span style="color:#ffaa00;background:none;">' . __('Na odobrenju nadređenog') . '</span>';
                                } elseif ($item['is_accepted'] == 2 and $item['ponder'] != '0') {
                                    echo ' &nbsp; - <span style="color:#aaa;background:none;">' . __('Prihvaćen') . '</span>';
                                }
                                ?>
                                <?php if ($total_comments['broj'] > 0) { ?>
                                    &nbsp; &nbsp; &nbsp; <i
                                            class="ion-chatbox-working"></i> <?php echo $total_comments['broj']; ?>
                                <?php } ?>
                            </div>

                            <div class="col-sm-1">
                                <?php echo "<b>" . __('Rok') . "</b>"; ?>
                                <br/><?php echo date('d/m/Y', strtotime($item['date_end'])); ?><br/>
                            </div>

                            <?php if ($faza == '2') { ?>
                                <div class="col-sm-1">
                                    <?php echo "<b style='margin-left:5px'>" . __('Status') . "</b>"; ?><br/>
                                    <select id="status_razvojni"
                                            name="<?php echo $item['task_id']; ?>" <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                        echo 'disabled';
                                    } ?>
                                            class="rcorners1 <?php if ($item['status'] == '4' or $item['status'] == '5' or $item['ponder'] == '0') {
                                                echo ' gray-denis';
                                            } ?>" class="form-control" style="width:120px;outline:none;">
                                        <?php echo _optionTaskStatus($item['status']); ?>
                                    </select><br/>
                                </div>
                            <?php } ?>

                            <div class="col-sm-2 text-right">

                                <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_view.php?id=' . $tools_id; ?>"
                                   class="table-btn alt" data-widget="ajax" data-id="opt2" data-width="600"><i
                                            class="ion-eye"></i></a>
                                <?php if ($item['is_user_reviewed'] == 1 && $item['is_archive'] == 0) { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_archive:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite arhivirati zadatak?'); ?>"><i
                                                class="ion-folder"></i></a>
                                <?php } ?>
                                <?php if ($faza == '1' and $item['ponder'] != '0') { ?>
                                    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
                                       class="table-btn alt" data-widget="remove"
                                       data-id="tasks_remove:<?php echo $tools_id; ?>"
                                       data-text="<?php echo __('Dali ste sigurni da želite poništiti zadatak?'); ?>"><i
                                                class="ion-android-close"></i></a>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

            <?php }

        } else { ?>
            <div class="text-center">
                <?php echo __('Nema spašenih razvojnih ciljeva'); ?>
            </div>
        <?php } ?>

        <a href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_add.php?task_type=2&ponder_sum=' . $ponder_sum_ocjene; ?>"
           style="margin-bottom: 10px;margin-top: -10px;" data-widget="ajax-task" data-id="opt2" data-width="400px"
           class="btn btn-red btn-xs"><?php echo __('Dodaj novi razvojni cilj'); ?> <i style="top:-5px;"
                                                                                       class="ion-ios-plus-empty"></i></a>

        <span class="klasa1" style="height:20px;padding-left:20px;padding-right:889px;">Komentar na radni učinak i kompetecije</span><br/>

        <textarea class="" maxlength="700" rows="4" cols="60" id="komentar"
                  name="<?php echo 'komentar-' . $item['user_id']; ?>"
                  placeholder="Molimo da unesete opšti komentar na  predložene ciljeve i kompetencije."
                  title="Max karaktera 700" spellcheck="false"><?php echo $komentar; ?></textarea><br/><br/>

    </div>

    <div id="ciljevi3">
        <?php if ($faza == '3' and $step3) {
            $ocjena_ucinka = 0.8 * $ocjena_ciljeva + 0.2 * $ocjena_kompetencija;
            echo '<span class="klasa1" style="height:30px; background: #FABF8F;color: black;padding-left:20px;padding-right:518px;padding-top:5px;">Ukupna ocjena radnog učinka za  ' . $year_tasks . '. godinu:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . number_format((float)$ocjena_ucinka, 2, '.', '') . '</span><br/>';
        } ?>
    </div>


    <span class="klasa1"
          style="height:20px;padding-left:20px;padding-right:889px;display: -webkit-box;margin-left:0px;"></span><br/>


    <a href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
       style="float:right;margin-right:262px;display:<?php if (!$step1) {
           echo '';
       } else {
           echo 'none';
       } ?>" class="table-btn alt1" data-widget="send"
       data-id="nadredjenom:<?php echo $_user['user_id'] . '-' . $faza; ?>"
       data-text="<?php echo __('Da li ste sigurni da želite poslati nadređenom?'); ?>"
       data-response="<?php echo __('Poslano nadredjenom!'); ?>"><i class="fa fa-arrow-circle-right"
                                                                    style="float:left;color:green;"></i><label
                style="font-size:17px;cursor: pointer;">Pošalji nadređenom</label></a>


    <?php if ($step3 and $faza == '3') { ?>


    <span class="potvrda" style="margin-left:0px;width:60%;display:<?php if (!$step4) {
        echo 'inline';
    } else {
        echo 'none';
    } ?>"> Odabirom "Potpiši obrazac", saglasan/a sam i elektronski potpisujem MBO obrazac upravljanja radnim učinom i potvrđujem tačnost navedenih podataka.</span><?php if (!$step4) {
            echo '<br/><br/>';
        } ?>
        <input id="potvrda_razgovora" type="checkbox" <?php if ($potvrda4) {
            echo 'checked="checked"';
        } ?> value="denis" name="<?php echo 'potvrda-' . $item['user_id']; ?>" style="margin-bottom:10px;">
        <span class="potvrda" style="width:57%;">Potvrđujem da sam obavio/la razgovor o učinku sa nadređenim rukovodiocem.</span>
        <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
           style="float:right;padding:0px;margin-right:262px;display:<?php if ($potvrda4 and !$step4) {
               echo '';
           } else {
               echo 'none';
           } ?>" class="table-btn alt1 <?php if (!$potvrda4) {
            echo ' not-active';
        } ?>" <?php if (!$potvrda4) {
            echo ' disabled ';
        } ?>" data-widget="send" data-id="potpisuje_radnik:<?php echo $_user['user_id'] . '-' . $faza; ?>" data-text="<?php echo __('Da li ste sigurni da želite potpisati obrazac?'); ?>" data-response="<?php echo __('Obrazac potpisan'); ?>">
        <i class="fa fa-arrow-circle-right" style="float:left;color:green;margin-left: 5px;margin-top: 1px;"></i><label
                style="font-size:17px;cursor: pointer;">Potpiši obrazac</label></a>

    <?php } ?>

    <?php if ($step3 and $faza != '3') { ?>
        <div>
            <span class="potvrda" style="margin-left:0px;width:60%;display:<?php if (!$step4) {
                echo 'inline';
            } else {
                echo 'none';
            } ?>"> Odabirom "Potpiši obrazac", saglasan/a sam i elektronski potpisujem MBO obrazac upravljanja radnim učinom i potvrđujem tačnost navedenih podataka.</span>
            <a id="link_potvrda" href="<?php echo $url . '/modules/' . $_mod . '/ajax.php'; ?>"
               style="float:right;margin-right:262px;padding:0px;display:<?php if (!$step4) {
                   echo '';
               } else {
                   echo 'none';
               } ?>" class="table-btn alt1" data-widget="send"
               data-id="potpisuje_radnik:<?php echo $_user['user_id'] . '-' . $faza; ?>"
               data-text="<?php echo __('Da li ste sigurni da želite potpisati obrazac?'); ?>"
               data-response="<?php echo __('Obrazac potpisan!'); ?>"><i class="fa fa-arrow-circle-right"
                                                                         style="float:left;color:green;margin-left: 5px;margin-top: 1px;"></i><label
                        style="font-size:17px;cursor: pointer;">Potpiši obrazac</label></a><br/>
        </div>
    <?php } ?>


    <?php if ($step4) { ?>
        <div class="col-sm-6">
            <br/> <span style="margin-left:0px;background:none;color:black;float:left;"><b>Radnik</b> </span><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Ime i prezime :'; ?></span><br/><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo $_user['fname'] . ' ' . $_user['lname']; ?></u> </span><br/><br/><br/>

            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Datum, potpis :'; ?></span><br/><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo ($datum_radnik != '') ? date('d/m/Y', strtotime($datum_radnik)) : ''; ?></u> </span><br/><br/><br/>
        </div>
    <?php } ?>
    <?php if ($step5) { ?>
        <div class="col-sm-6">
            <br/> <span
                    style="margin-left:0px;background:none;color:black;float:left;"><b>Nadređeni rukovodilac</b> </span><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Ime i prezime :'; ?></span><br/><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo _employee($_user['parent'])['fname'] . ' ' . _employee($_user['parent'])['lname']; ?></u> </span><br/><br/><br/>

            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><?php echo 'Datum, potpis :'; ?></span><br/><br/>
            <span style="margin-left:0px;width:100px;background:none;color:black;float:left;"><u><?php echo ($datum_nadredjeni != '') ? date('d/m/Y', strtotime($datum_nadredjeni)) : ''; ?></u> </span><br/><br/><br/>
        </div>
    <?php } ?>

    </div>
</section>
<!-- END - Main section -->

<?php

include $_themeRoot . '/footer.php';

?>

<script>

    jQuery.expr[':'].regex = function (elem, index, match) {
        var matchParams = match[3].split(','),
            validLabels = /^(data|css):/,
            attr = {
                method: matchParams[0].match(validLabels) ?
                    matchParams[0].split(':')[0] : 'attr',
                property: matchParams.shift().replace(validLabels, '')
            },
            regexFlags = 'ig',
            regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g, ''), regexFlags);
        return regex.test(jQuery(elem)[attr.method](attr.property));
    }

    function insertParam(key, value) {
        key = encodeURI(key);
        value = encodeURI(value);

        var kvp = document.location.search.substr(1).split('&');

        var i = kvp.length;
        var x;
        while (i--) {
            x = kvp[i].split('=');

            if (x[0] == key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        //this will reload the page, it's likely better to store this until finished
        document.location.search = kvp.join('&');
    }

    $("#year_tasks").on('change', function (e) {
        insertParam('year', this.value);
        setTimeout(function () {
            window.location.reload();
        }, 1000);

    });


    $('select:regex(id, .*ocjena_user_timski.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-ocjena_timski_user",
                ocjena: this.value,
                task_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('select:regex(id, .*ocjena_timski.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-ocjena_timski",
                ocjena: this.value,
                task_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('select:regex(id, .*ocjena_user_individualni.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-ocjena_individualni_user",
                ocjena: this.value,
                task_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('select:regex(id, .*ocjena_individualni.*)').on('change', function () {
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-ocjena_individualni",
                ocjena: this.value,
                task_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })


    $('select:regex(id, .*status_timski.*)').on('change', function () {
        var task_id = this.name;
        var status = this.value;
        var arr = this.id.split('-');
        var ponder = arr[1];

        console.log(ponder);
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-status",
                status: this.value,
                task_id: this.name
            },
            function (returnedData) {
                if (status == 4 || status == 5)
                    otvoriZamjenski(1, ponder);
                else
                    window.location.reload();
            });
    })

    $('select:regex(id, .*status_individualni.*)').on('change', function () {
        var status = this.value;
        var arr = this.id.split('-');
        var ponder = arr[1];
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-status",
                status: this.value,
                task_id: this.name
            },
            function (returnedData) {
                if (status == 4 || status == 5)
                    otvoriZamjenski(0, ponder);
                else
                    window.location.reload();
            });
    })

    $('select:regex(id, .*status_razvojni.*)').on('change', function () {
        var status = this.value;
        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-status",
                status: this.value,
                task_id: this.name
            },
            function (returnedData) {
                if (status == 4 || status == 5)
                    otvoriZamjenski(2, 10);
                else
                    window.location.reload();
            });
    })

    $('textarea:regex(id, .*ostvarenje_timski.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-ostvarenje",
                ostvarenje: this.value,
                task_id: this.name
            },
            function (returnedData) {

            });
    })

    $('textarea:regex(id, .*ostvarenje_individualni.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-ostvarenje",
                ostvarenje: this.value,
                task_id: this.name
            },
            function (returnedData) {

            });
    })

    $('textarea:regex(id, .*KPI_timski.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-KPI",
                KPI: this.value,
                task_id: this.name
            },
            function (returnedData) {

            });
    })

    $('textarea:regex(id, .*KPI_individualni.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-KPI",
                KPI: this.value,
                task_id: this.name
            },
            function (returnedData) {

            });
    })

    $('textarea:regex(id, .*ostvarenje_razvojni.*)').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-task-ostvarenje",
                ostvarenje: this.value,
                task_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#obavezna1_user').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-obavezna1_user",
                obavezna1_rating_user: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#obavezna2_user').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-obavezna2_user",
                obavezna2_rating_user: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#obavezna1').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-obavezna1",
                obavezna1_rating: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#obavezna2').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-obavezna2",
                obavezna2_rating: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#kompetencija1').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija1",
                kompetencija1: this.value,
                user_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })
    $('#kompetencija1_rating_user').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija1_rating_user",
                kompetencija1_rating_user: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })
    $('#kompetencija1_rating').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija1_rating",
                kompetencija1_rating: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })
    $('#kompetencija2').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija2",
                kompetencija2: this.value,
                user_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('#kompetencija2_rating_user').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija2_rating_user",
                kompetencija2_rating_user: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })
    $('#kompetencija2_rating').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija2_rating",
                kompetencija2_rating: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })
    $('#kompetencija3').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija3",
                kompetencija3: this.value,
                user_id: this.name
            },
            function (returnedData) {
                window.location.reload();
            });
    })

    $('#kompetencija3_rating_user').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija3_rating_user",
                kompetencija3_rating_user: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })
    $('#kompetencija3_rating').on('change', function () {

        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-kompetencija3_rating",
                kompetencija3_rating: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });
    })

    $('#komentar').on("blur", function (e) {


        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "change-komentar",
                komentar: this.value,
                user_id: this.name
            },
            function (returnedData) {

            });

    })

    function otvoriZamjenski(type, ponder) {
        var user = <?php echo $item['user_id']; ?>;
        var ukupna = <?php echo $ponder_sum_ocjene; ?>;
        var trenutna = ukupna - ponder;

        var ajax_id = "opt2";
        var ajax_width = "400px";
        var ajax_href = "<?php echo $url . '/modules/' . $_mod . '/pages/popup_tasks_add_zamjenski.php?task_type=';?>" + type + "&ponder_sum=" + trenutna + "&user_id=" + user;
        var ajax_overlay = $('<div class="dialog" id="' + ajax_id + '"></div>');
        var _w = $(window).width();
        $.ajax({
            url: ajax_href,
            cache: false,
            beforeSend: function (xhr) {
                ajax_overlay.appendTo('body').end().fadeIn('fast');
                $('#' + ajax_id).append('<div class="cssload-speeding-wheel" id="' + ajax_id + '"></div>');
            },
            success: function (html) {
                $('#' + ajax_id + '.cssload-speeding-wheel').remove();

                $('#' + ajax_id).append('<div class="dialog-main" style="width:' + ajax_width + ';margin-left:-762px;">' + html + '</div>');


                if ($('body').height() > $('div#' + ajax_id).find('.dialog-main').height()) {
                    var ajax_top = ($('body').height() - $('div#' + ajax_id).find('.dialog-main').height()) / 2;
                    $('#' + ajax_id).find('.dialog-main').css('top', ajax_top + 'px');
                }
            },
            error: function (xhr, status, error) {
                $('#' + ajax_id + '.cssload-speeding-wheel').remove();
                $('#' + ajax_id).append('<div class="dialog-error"><big><i class="ion-alert-circled"></i> GREÅ KA</big><br/>' + error + '<br/><br/><a href="#" class="btn close" data-widget="close-ajax" data-id="' + ajax_id + '"><i class="ion-android-close"></i> Zatvori</a></div>');
            }
        });
    }


    function updatePotvrda() {

        if (document.getElementById('potvrda_razgovora').checked) {
            var potvrda_razgovora_value = 1
        } else {
            var potvrda_razgovora_value = 0
        }


        $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                request: "potvrda_razgovora",
                potvrda_razgovora: potvrda_razgovora_value,
                user_id: <?php echo '"potvrda-' . $item['user_id'] . '"'; ?>},
            function (returnedData) {
                window.location.reload();

            });
    }

    $("#potvrda_razgovora").on("ifChanged", updatePotvrda);

    $(document).ready(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
            increaseArea: '20%' // optional
        });
        $('input[name="l_potencijal"]').on('ifClicked', function (event) {
            $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                    request: "change-l_potencijal",
                    l_potencijal: $(this).val(),
                    user_id: <?php echo '"' . $item['user_id'] . '"'; ?>},
                function (returnedData) {
                    window.location.reload();
                });
        });
        var is_locked_after_send = <?php echo $step1; ?>;
        if (is_locked_after_send) {
            $('select:not(#year_tasks)').attr('disabled', true);
            $('#ciljevi a').css('display', 'none');
            $('#ciljevi2 a').css('display', 'none');
            $('#ciljevi3 a').css('display', 'none');
            $("textarea").attr('readonly', 'readonly');
        }
        $("[type='number']").keypress(function (evt) {
            evt.preventDefault();
        });


        var faza_finished = <?php echo $step4; ?>;
        if (faza_finished) {
            $("select:not(#year_tasks) option:not(:selected)").prop("disabled", true);
            $("input:not(#year_tasks)").prop('disabled', true);
            $("textarea").prop('disabled', true);
        }


    });


</script>

</body>
</html>
