<?php
_pagePermission(4, false);

include_once('modules/core/Model.php');
include_once('modules/core/VS.php');
include_once('modules/core/User.php');
?>

<!-- START - Main section -->

<style>
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        border-style: solid;
        border-width: 1px;
        border-color: #006595;

    }

</style>

<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Satnice'); ?></span></h4>
</div>

<section>
    <div class="content clear">


        <div class="box" style="width:22%; display: block; float:left; margin-right:20px;">
            <div class="content">
                <table class="table table-hover">


                    <div class="row">
                        <div class="col-xs-12">

                            <form id="admin-form" method="post">
                                <?php

                                $filtertdate = '2021-01-1 00:00:00.000';

                                $admin_managementq = $db->query("SELECT [Chief Executive Administrator] from  " . $nav_human_resource_setup . "  ");
                                $admin_management = $admin_managementq->fetch();


                                if (isset($_POST['ime_prezime'])) {
                                    $idemp = $_POST['ime_prezime'];
                                }
                                if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'])) {

                                    $idm = $_POST['IDMonth'];
                                    $idy = $_POST['IDYear'];
                                    $month['id'] = $idm;
                                    $year['id'] = $idy;

                                    $get_year = $db->query("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id='41' AND year='" . $_POST['IDYear'] . "'");
                                    foreach ($get_year as $yearvalue) {
                                        $filter_year = $yearvalue['id'];
                                    }
                                } else {
                                    $now = new \DateTime('now');
                                    $currmonth = $now->format('m');
                                    $curryear = $now->format('Y');
                                    $get_year = $db->query("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id='41' AND year='" . $curryear . "' ");
                                    foreach ($get_year as $yearvalue) {
                                        $filter_year = $yearvalue['id'];
                                    }
                                    $month['id'] = $currmonth;
                                    $year['id'] = $curryear;
                                };

                                $filtertdate = $year['id'] . "-" . $month['id'] . "-1 00:00:00.000";

                                if (isset($_POST['IDB1']))
                                    $B_1 = $_POST['IDB1'];
                                else
                                    $B_1 = '';
                                if (isset($_POST['IDReg']))
                                    $region = $_POST['IDReg'];
                                else
                                    $region = '';
                                if (isset($_POST['IDStream']))
                                    $stream = $_POST['IDStream'];
                                else
                                    $stream = '';
                                if (isset($_POST['IDTeam']))
                                    $team = $_POST['IDTeam'];
                                else
                                    $team = '';
                                if (isset($_POST['ime_prezime']))
                                    $ime_prezime = $_POST['ime_prezime'];
                                else
                                    $ime_prezime = '';

                                ?>
                                <label class="lable-admin1"><?php echo __('Mjesec-Godina'); ?></label>
                                <input readonly type="text" id="month" name="month" class="monthPicker"/>

                                <input type="hidden" class="rcorners1" style="outline:none;width:200px;" name="IDYear"
                                       min="2017"
                                       max="2300" <?php if (isset($_POST['IDYear']) and ($_POST['IDYear']) != '') { ?> value="<?php echo $_POST['IDYear']; ?>"  <?php } else { ?> value="<?php echo date("Y"); ?>" <?php } ?>
                                       required
                                       oninvalid="this.setCustomValidity('Molimo unesite godinu.')"
                                       onchange="this.setCustomValidity('')"><br>

                                <input type="hidden" class="rcorners1" style="outline:none;width:200px;" name="IDMonth"
                                       min="1"
                                       max="12" <?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '') { ?> value="<?php echo $_POST['IDMonth']; ?>"  <?php } else { ?> value="<?php echo date("n"); ?>" <?php } ?>
                                       required
                                       oninvalid="this.setCustomValidity('Molimo unesite mjesec.')"
                                       onchange="this.setCustomValidity('')"><br>
                                <label class="lable-admin1"><?php echo __('Sektor'); ?></label>
                                <select id="B1" name="IDB1" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionB_1($B_1, 1) ?>
                                </select><br/><br/>


                                <label class="lable-admin1"><?php echo __('Odjel'); ?></label>
                                <select id="regije" name="IDReg" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionRegion($B_1, $region, 1) ?>
                                    <option style="color:black;"value="">Odaberi..</option>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Grupa'); ?></label>
                                <select id="streams" name="IDStream" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionStream($region, $stream) ?>
                                    <option value="">Odaberi..</option>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Tim'); ?></label>
                                <select id="teams" name="IDTeam" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionTeam($stream, $team) ?>
                                    <option value="">Odaberi..</option>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Ime'); ?></label>
                                <select id="ime_prezime" name="ime_prezime" class="rcorners1"
                                        style="outline:none;width:200px;" class="form-control"
                                        onchange="this.form.submit();">
                                    <?php echo _optionName($team, $stream, $region, $B_1, $ime_prezime, $filtertdate) ?>
                                </select><br/>

                                <label class="lable-admin"><?php echo __('Prika??i kalendar'); ?></label>
                                <input class="combo-box-admin" type="checkbox"
                                       name="Abs" <?php if ((isset($_POST['Abs']) and ($_POST['Abs']) != '')) {
                                    echo 'checked="checked"';
                                } ?>> </input>

                                <div>
                                    <hr style="margin:5px;">

                                    <button type="submit" style="margin-left:13px;width:125px;"
                                            class="btn btn-red pull-left btn-sm"><?php echo __('Izaberi!'); ?> <i
                                                class="ion-ios-download-outline"></i></button>

                                    <button id="odustani" style="width:125px;background-color:006595;"
                                            class="btn btn-red pull-right btn-sm"><?php echo __('Odustani!'); ?> <i
                                                class="ion-ios-download-outline"></i></button>
                                    <br/>
                                </div>

                            </form>


                            </select><br/>
                        </div>
                    </div>

                    <!-- START - Default section -->

                    <?php if (isset($_POST['IDMonth']) and isset($_POST['IDYear']) and isset($filter_year)) { ?>
                        <div style="width:260px!important;">
                            <lable style="margin-left:23px;"> P</lable>
                            <lable style="margin-left:23px;"> U</lable>
                            <lable style="margin-left:23px;"> S</lable>
                            <lable style="margin-left:23px;"> ??</lable>
                            <lable style="margin-left:23px;"> P</lable>
                            <lable style="margin-left:23px;"> S</lable>
                            <lable style="margin-left:23px;"> N</lable>
                        </div>

                        <?php

                        $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $filter_year . "' AND month_id='" . $_POST['IDMonth'] . "' AND user_id='41' ORDER BY day");
                        if ($get_days->rowCount() < 0) {


                            echo '<div class="box days" style="width:260px!important;margin-left:2px;">';
                            foreach ($get_days as $day) { ?>


                                <?php

                                if ($day['day'] == '1') {
                                    switch ($day['weekday']) {

                                        case '1': ?>

                                            <?php break;
                                        case '2': ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <?php break;
                                        case '3':
                                            ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>

                                            <?php break;
                                        case '4': ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>

                                            <?php break;
                                        case '5': ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>

                                            <?php break;
                                        case '6': ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>


                                            <?php break;
                                        case '7': ?>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>
                                            <div class="day" style="width:31px;height:45px;"
                                                 id="opt-'.$day['id'].'"></div>


                                            <?php break;

                                    }
                                }


                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['weekday'] == '1') and ($day['KindofDay'] != 'HOLIDAY') and ($day['KindofDay'] != 'CHOLIDAY')) {
                                    echo '<div class="day" style="width:31px;height:45px;" id="opt-' . $day['id'] . '" >';
                                }
                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['weekday'] == '2') and ($day['KindofDay'] != 'HOLIDAY') and ($day['KindofDay'] != 'CHOLIDAY')) {
                                    echo '<div class="day" style="width:31px;height:45px;" id="opt-' . $day['id'] . '" >';
                                }
                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['weekday'] == '3') and ($day['KindofDay'] != 'HOLIDAY') and ($day['KindofDay'] != 'CHOLIDAY')) {
                                    echo '<div class="day" style="width:31px;height:45px;" id="opt-' . $day['id'] . '" >';
                                }

                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['weekday'] == '4') and ($day['KindofDay'] != 'HOLIDAY') and ($day['KindofDay'] != 'CHOLIDAY')) {
                                    echo '<div class="day" style="width:31px;height:45px;" id="opt-' . $day['id'] . '" >';
                                }

                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['weekday'] == '5') and ($day['KindofDay'] != 'HOLIDAY') and ($day['KindofDay'] != 'CHOLIDAY')) {
                                    echo '<div class="day" style="width:31px;height:45px;" id="opt-' . $day['id'] . '" >';
                                }

                                if (($day['weekday'] == '6') or ($day['weekday'] == '7')) {
                                    echo '<div class="day" style="width:31px;height:45px; background-color:#DCD6D6"  id="opt-' . $day['id'] . '" >';
                                }
                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and $day['KindofDay'] == 'HOLIDAY') {
                                    echo '<div class="day" style="width:31px;height:45px; background-color: #ffb366"  id="opt-' . $day['id'] . '">';
                                }
                                if (($day['weekday'] != '6') and ($day['weekday'] != '7') and $day['KindofDay'] == 'CHOLIDAY') {
                                    echo '<div class="day" style="width:31px;height:45px;background-color: #ccffcc"  id="opt-' . $day['id'] . '" >';
                                }
                                if (($day['weekday'] != '6') and ($day['weekday'] != '7')) {
                                    $dayname = '';
                                    echo '<small>' . $day['day'] . '<br>' . $dayname . ' </small>';
                                }
                                if (($day['weekday'] == '6')) {
                                    echo '<small>' . $day['day'] . '</small>';
                                }
                                if (($day['weekday'] == '7')) {
                                    echo '<small>' . $day['day'] . ' </small>';
                                }
                                echo '<div>';

                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="text-center"></div>';
                        }
                        ?>


                        <div class="row" style="display:block;">

                            <div class="col-sm-12">
                                <label>Legenda:</label>

                                <div style="display:block;">
                                    <div class="small-circle"
                                         style="background:#fff;border:1px solid gray;"></div> <?php echo __('Redovan rad'); ?>
                                    <!-- <div class="small-circle" style="background:#ccffcc;border:1px solid gray;margin-left:40px;"></div> <?php echo __('Vjerski praznici'); ?>-->
                                    <div class="small-circle"
                                         style="background:yellow;border:1px solid gray;margin-left:39px;">
                                        </div> <?php echo __('Bolovanje'); ?>
                                </div>

                                <div style="display:block;">
                                    <div class="small-circle"
                                         style="background:#ffb366;border:1px solid gray;"></div> <?php echo __('Praznici'); ?>
                                    <div class="small-circle"
                                         style="background:#ccc;border:1px solid gray;margin-left: 66px;"></div> <?php echo __('Vikendi'); ?>

                                </div>
                                <div style="display:block;">
                                    <div class="small-circle"
                                         style="background:lightblue;border:1px solid gray;"></div> <?php echo __('Godi??nji'); ?>
                                    <div class="small-circle"
                                         style="background:#d94f4f;border:1px solid gray;margin-left:65px;"></div> <?php echo __('Prekid'); ?>
                                </div>

                            </div>
                        </div>
                        <!-- END - Default calendar section -->
                        <?php
                    }
                    ?>
                    </body>
                    </html>


                </table>
            </div>

        </div>

        <?php

        ?>
        <?php

        $limit = 5;

        if ($_num) {

            $offset = ($_num - 1) * $limit;

        } else {

            $offset = 0;
            $_num = 1;

        }

        $where = "";
        $path = '?m=' . $_mod . '&p=' . $_page;

        if (isset($_GET['t'])) {
            $type = $_GET['t'];
            if ($type == 'inactive') {
                $where .= "WHERE status='1'";
            } else {
                $where .= "WHERE status='0' AND role='$type'";
            }
            $path .= '&t=' . $type;
        } else {
            $type = '';
            $where = "WHERE status='0'";
        }

        if ($_search) {
            $where .= " AND fname LIKE '%$_search%' OR lname LIKE '%$_search%'";
            $path .= '&q=' . $_search;
        }


        $path .= '&pg=';

        //novi sistem
        $filtertdate = $year['id'] . "-" . $month['id'] . "-1 00:00:00.000";

        if ($_user['employee_no'] == $admin_management[0] and ($_user['managment_level'] == 3 or $_user['managment_level'] == 4 or $_user['managment_level'] == 2 or $_user['managment_level'] == 5)) {
            $check_management_level = ' (u.managment_level=6 or u.managment_level=8)   ';

            if (isset($_POST['ime_prezime']) and $_POST['ime_prezime'] != '') {

                $query = $db->query("select *
   from  " . $nav_employee . "  as e
     join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
     left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
     where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  ((u.fname + ' ' + u.lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI or (u.fname + ' ' + u.lname) like '" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) and cl.Active=1 and cl.[Show Record]=1");

                $get2 = $db->query("select count(*)
   from  " . $nav_employee . "  as e
     join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
     left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
     where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  ((u.fname + ' ' + u.lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI or (u.fname + ' ' + u.lname) like '" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) and cl.Active=1 and cl.[Show Record]=1 ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "   group BY department_code");
            } elseif (isset($_POST['IDTeam']) and ctype_alpha($_POST['IDTeam']) and $_POST['IDTeam'] != '') {
                $query = $db->query("select *
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and " . $check_management_level . " (cl.[Team Description]=N'" . $_POST['IDTeam'] . "' or cl.[Team Description]='" . $_POST['IDTeam'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO)) ORDER BY u.user_id ASC OFFSET $offset ROWS
         FETCH NEXT " . $limit . " ROWS ONLY
         ");
                $get2 = $db->query("select count(*)
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  " . $check_management_level . " (cl.[Team Description]=N'" . $_POST['IDTeam'] . "' or cl.[Team Description]='" . $_POST['IDTeam'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))
         ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE  (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5) or (managment_level=6 or managment_level=8)) and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
            } elseif (isset($_POST['IDStream']) and ctype_alpha($_POST['IDStream']) and $_POST['IDStream'] != '') {
                $query = $db->query("select *
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  " . $check_management_level . " (cl.[Group Description]=N'" . $_POST['IDStream'] . "' or cl.[Group Description]='" . $_POST['IDStream'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO)) ORDER BY u.user_id ASC OFFSET $offset ROWS
         FETCH NEXT " . $limit . " ROWS ONLY
         ");
                $get2 = $db->query("select count(*)
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  " . $check_management_level . " (cl.[Group Description]=N'" . $_POST['IDStream'] . "' or cl.[Group Description]='" . $_POST['IDStream'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5)) and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE  (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5) or (managment_level=6 or managment_level=8)) and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");

            } elseif (isset($_POST['IDReg']) and ctype_alpha($_POST['IDReg']) and $_POST['IDReg'] != '') {
                $query = $db->query("select *
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  " . $check_management_level . " (cl.[Department Cat_ Description]='" . $_POST['IDReg'] . "' or cl.[Department Cat_ Description]=N'" . $_POST['IDReg'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO)) ORDER BY u.user_id ASC OFFSET $offset ROWS
         FETCH NEXT " . $limit . " ROWS ONLY
        ");
                $get2 = $db->query("select count(*)
       from  " . $nav_employee . "  as e
         join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
         left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
         where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  " . $check_management_level . " (cl.[Department Cat_ Description]='" . $_POST['IDReg'] . "' or cl.[Department Cat_ Description]=N'" . $_POST['IDReg'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
         (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
         (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))
        ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5) or (managment_level=6 or managment_level=8)) and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
            } elseif (isset($_POST['IDB1']) and $_POST['IDB1'] != '') {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and (cl.[Sector Description]=N'" . $_POST['IDB1'] . "' or cl.[Sector Description]='" . $_POST['IDB1'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
       (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
       (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO) or " . $check_management_level . ") ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and   (cl.[Sector Description]=N'" . $_POST['IDB1'] . "' or cl.[Sector Description]='" . $_POST['IDB1'] . "') and cl.Active=1 and cl.[Show Record]=1 and 
       (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
       (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO) or " . $check_management_level . ")");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE  (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5) or (managment_level=6 or managment_level=8)) and B_1_description='" . $_POST['IDB1'] . "' group BY department_code ");
            } elseif (empty($_POST['ime_prezime']) and empty($_POST['IDB1']) and empty($_POST['IDReg']) and empty($_POST['IDStream']) and empty($_POST['IDTeam'])) {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where(u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  (u.managment_level=6 or u.managment_level=8)  or cl.Active=1 and cl.[Show Record]=1 and 
       (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
       (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO) or " . $check_management_level . ") ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY
       ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE  (parent='" . $_user['employee_no'] . "' or parent2='" . $_user['employee_no'] . "' or " . $_user['employee_no'] . " in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d) or (managment_level=6 or managment_level=8)  or employee_no='" . $_user['employee_no'] . "')  group BY department_code ");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  cl.Active=1 and cl.[Show Record]=1 and 
       (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
       (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO) or " . $check_management_level . ")
       ");
            }
        } elseif ($_user['employee_no'] == $admin_management[0] and $_user['managment_level'] == 7) {
            $check_management_level = '(managment_level=6 or managment_level=8)';

            if (isset($_POST['ime_prezime']) and $_POST['ime_prezime'] != '') {

                $query = $db->query("select *
   from  " . $nav_employee . "  as e
     join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
     left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
     where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  ((u.fname + ' ' + u.lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI or (u.fname + ' ' + u.lname) like '" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) and cl.Active=1 and cl.[Show Record]=1");

                $get2 = $db->query("select count(*)
   from  " . $nav_employee . "  as e
     join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
     left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
     where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  ((u.fname + ' ' + u.lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI or (u.fname + ' ' + u.lname) like '" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) and cl.Active=1 and cl.[Show Record]=1 ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "   group BY department_code");

            } elseif (isset($_POST['IDTeam']) and ctype_alpha($_POST['IDTeam']) and $_POST['IDTeam'] != '') {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and   (cl.[Team Description]=N'" . $_POST['IDTeam'] . "' or cl.[Team Description]='" . $_POST['IDTeam'] . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level . "
        ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY
       ");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and (cl.[Team Description]=N'" . $_POST['IDTeam'] . "' or cl.[Team Description]='" . $_POST['IDTeam'] . "') and cl.Active=1 and cl.[Show Record]=1 and  " . $check_management_level);
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
            } elseif (isset($_POST['IDStream']) and ctype_alpha($_POST['IDStream']) and $_POST['IDStream'] != '') {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  (cl.[Group Description]=N'" . $_POST['IDStream'] . "' or cl.[Group Description]='" . $_POST['IDStream'] . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level . "
        ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY
       ");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and   (cl.[Group Description]=N'" . $_POST['IDStream'] . "' or cl.[Group Description]='" . $_POST['IDStream'] . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level);
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
            } elseif (isset($_POST['IDReg']) and ctype_alpha($_POST['IDReg']) and $_POST['IDReg'] != '') {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and   (cl.[Department Cat_ Description]='" . $_POST['IDReg'] . "' or cl.[Department Cat_ Description]=N'" . $_POST['IDReg'] . "') and  cl.Active=1 and cl.[Show Record]=1 and  " . $check_management_level . " 
        ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY
      ");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  (cl.[Department Cat_ Description]='" . $_POST['IDReg'] . "' or cl.[Department Cat_ Description]=N'" . $_POST['IDReg'] . "') and cl.Active=1 and cl.[Show Record]=1 and  " . $check_management_level);
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " and ((fname + ' ' + lname) like N'" . $_POST['ime_prezime'] . "' COLLATE Latin1_General_CI_AI) group BY department_code ");
            } elseif (isset($_POST['IDB1']) and $_POST['IDB1'] != '') {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  (cl.[Sector Description]=N'" . $_POST['IDB1'] . "' or cl.[Sector Description]='" . $_POST['IDB1'] . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level . "
        ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY");

                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and  (cl.[Sector Description]=N'" . $_POST['IDB1'] . "' or cl.[Sector Description]='" . $_POST['IDB1'] . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level);
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " and B_1_description='" . $_POST['IDB1'] . "' group BY department_code ");
            } elseif (empty($_POST['ime_prezime']) and empty($_POST['IDB1']) and empty($_POST['IDReg']) and empty($_POST['IDStream']) and empty($_POST['IDTeam'])) {
                $query = $db->query("select *
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level . " ORDER BY u.user_id ASC OFFSET $offset ROWS
       FETCH NEXT " . $limit . " ROWS ONLY
       ");
                $queryg = $db->query("SELECT [department_code] FROM  " . $portal_users . "  WHERE " . $check_management_level . " or employee_no='" . $_user['employee_no'] . "'  group BY department_code ");
                $get2 = $db->query("select count(*)
     from  " . $nav_employee . "  as e
       join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
       left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
       where (u.[termination_date] is null or u.[termination_date]>='" . $filtertdate . "') and cl.Active=1 and cl.[Show Record]=1 and " . $check_management_level);
            }

        } else {
            $check_management_level = '';

            if (isset($_POST['ime_prezime']) and $_POST['ime_prezime'] != '') {
                $x = userList('name_surname', $_POST['ime_prezime'], $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];

            } elseif (isset($_POST['IDTeam']) and ctype_alpha($_POST['IDTeam']) and $_POST['IDTeam'] != '') {
                $x = userList('IDTeam', $_POST['IDTeam'], $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];
            } elseif (isset($_POST['IDStream']) and ctype_alpha($_POST['IDStream']) and $_POST['IDStream'] != '') {
                $x = userList('IDStream', $_POST['IDStream'], $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];
            } elseif (isset($_POST['IDReg']) and ctype_alpha($_POST['IDReg']) and $_POST['IDReg'] != '') {
                $x = userList('IDReg', $_POST['IDReg'], $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];
            } elseif (isset($_POST['IDB1']) and $_POST['IDB1'] != '') {
                $x = userList('IDB1', $_POST['IDB1'], $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];
            } elseif (empty($_POST['ime_prezime']) and empty($_POST['IDB1']) and empty($_POST['IDReg']) and empty($_POST['IDStream']) and empty($_POST['IDTeam'])) {
                $x = userList('none', '', $limit, $offset, $_user['employee_no'], $filtertdate);
                $query = $x[0];
                $get2 = $x[1];
                $queryg = $x[2];
            }
        }


        $result = $get2->fetch();
        $total = $result[0];

        $qstring = "";
        if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '' and isset($_POST['IDYear']) and ($_POST['IDYear']) != '') {
            $qstring = "SELECT count(*) FROM  " . $portal_hourlyrate_month . "  a join  " . $portal_hourlyrate_year . "  b on a.user_id =b.user_id where a.[month]=" . $_POST['IDMonth'] . " and b.[year]=" . $_POST['IDYear'] . "";
            if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '' and isset($_POST['IDYear']) and ($_POST['IDYear']) != '' and isset($_POST['ime_prezime']) and ($_POST['ime_prezime']) != '') {
                $qstring = "SELECT  count(*) from ( select a.[user_id], a.[year_id], a.[month], b.[year] FROM  " . $portal_hourlyrate_month . "  a join  " . $portal_hourlyrate_year . "  b on a.user_id =b.user_id) c join  " . $portal_users . "  d on c.user_id = d.user_id where c.[year]=" . $_POST['IDYear'] . " and c.[month]=" . $_POST['IDMonth'] . " and d.[fname]+ ' ' +d.[lname] =N'" . $_POST['ime_prezime'] . "' COLLATE 
       Latin1_General_CI_AI";
            }

            $get_month_check = $db->query($qstring);
            $result3 = $get_month_check->fetch();
            $total3 = $result3[0];


            if ($total3 == 0)
                $total = 0;

        }

        ?>


        <div class="box" style="width:76%; float:left;">
            <div class="content">
                <div class="row">

                    <div class="col-sm-12">

                        <table class="table table-hover">
                            <?php
                            if ($total > 0){
                            $i = 0;
                            ?>
                            <thead>
                            <tr>
                                <?php foreach ($queryg as $itemg) {
                                    $department = $itemg['department_code'];
                                    echo '<optgroup label="' . $department . '">';
                                } ?>

                                <th width="40" style="display:block;"
                                    class="hidden-xs"><?php echo __('Per. br.'); ?></th>
                                <th width="70;" class="hidden-xs"></th>


                                <?php

                                if (isset($_POST['ime_prezime'])) {
                                    $idemp = $_POST['ime_prezime'];
                                }
                                if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'])) {
                                    $idm = $_POST['IDMonth'];
                                    $idy = $_POST['IDYear'];


                                    echo 'Satnice za ' . $idm . '/';
                                    echo $idy;

                                    $month['id'] = $idm;
                                    $year['id'] = $idy;


                                } else {
                                    $now = new \DateTime('now');
                                    $currmonth = $now->format('m');
                                    $curryear = $now->format('Y');
                                    $month['id'] = $currmonth;
                                    $year['id'] = $curryear;
                                } ?>
                                <th><?php echo __('Ime i prezime/pozicija');


                                    ?></th>


                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach ($query

                            as $item){
                            $i++;
                            $tools_id = $item['user_id'];
                            $emp_id = $item['employee_no'];

                            $sql = "SELECT * FROM users WHERE user_id = $tools_id";
                            $sth = $db->query($sql);
                            $rowim = $sth->fetch();

                            $data = $rowim['image_no'];


                            ?>

                            <div style="float:left;">


                                <tr id="opt-<?php echo $tools_id; ?>">
                                    <td class="hidden-xs"><?php echo $emp_id; ?></td>
                                    <td class="text-center" class="hidden-xs">

                                        <?php if ($item['picture'] != 'none') { ?>
                                            <img onclick="$('#ime_prezime').val('<?php echo $item['fname'] . ' ' . $item['lname']; ?>').change();"
                                                 src="<?php echo $item['picture']; ?>" class="img-circle"
                                                 style="width:100%;">
                                        <?php } else { ?>
                                            <img src="<?php echo $_themeUrl; ?>/images/noimage-user.png"
                                                 class="img-circle">
                                        <?php } ?>

                                    </td>


                                    <td><a href="javascript:;"
                                           onclick="$('#ime_prezime').val('<?php echo $item['fname'] . ' ' . $item['lname']; ?>').change();"><?php echo $item['fname'] . ' ' . _optionGetLastNameNAV($item['employee_no']); ?></a><br/><small><?php echo $item['position']; ?>
                                            <br/><?php echo $item['sector'];
                                            $yearid = $db->query("SELECT [id], year FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $item['user_id'] . "' AND year='" . $year['id'] . "'");

                                            foreach ($yearid as $value2) {
                                                $absence_year = $value2['id'];
                                                $absence_year_full = $value2['year'];
                                            }

                                            $br_sati = $item['br_sati'];

                                            $nex_year = getYearId($absence_year_full, $item['user_id'], 'next');
                                            $pre_year = getYearId($absence_year_full, $item['user_id'], 'prev');


                                            $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $item['employee_no'] . "' and year = '$absence_year_full'");


                                            $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $absence_year . "' AND (status='18')) or (year_id = '" . $nex_year . "' and status = '19')) AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7' AND (date_NAV is null)");

                                            $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE ((year_id='" . $absence_year . "' AND (status='19')) or (year_id = '" . $pre_year . "' and status = '18')) AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7'  AND (date_NAV is null)");


                                            $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");

                                            $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $item['employee_no'] . "' and year = '$absence_year_full'");
                                            $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
                                            $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND employee_no='" . $item['employee_no'] . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
                                            $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $item['employee_no'] . "' and year = '$absence_year_full'");
                                            $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
                                            $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $item['employee_no'] . "' and year = '$absence_year_full'");
                                            $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND employee_no='" . $item['employee_no'] . "' 
  AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31') or (status='32') or (status='79')   
  ) AND (date_NAV is null)");
                                            $plobylaw = $db->query("SELECT sum(allowed_days) as sum_days FROM  " . $portal_hourlyrate_status . "  WHERE   
  status_group='P ??? PLACENO ODSUSTVO' ");


                                            foreach ($go as $valuego) {
                                                $totalgo = $valuego['Ukupno'];
                                                $iskoristenoPG = $valuego['Br_dana_iskoristenoPG'];
                                                $brdanaPG = $valuego['Br_danaPG'];
                                                $ostaloPG = $valuego['Br_dana_ostaloPG'];
                                                $iskoristeno = $valuego['Br_dana_iskoristeno'];
                                                $ostalo = $valuego['Br_dana_ostalo'];
                                                $brdana = $valuego['Br_dana'];
                                                $totalkrv = $valuego['Blood_days'];
                                                $iskoristenokrv = $valuego['P_6_used'];
                                                $totaldeath = $valuego['S_1_used'];
                                                $propaloGO = $valuego['G_2 not valid'];
                                            }
                                            if (!isset($iskoristeno)) {
                                                error_reporting(0);
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
                                            foreach ($plo as $valueplo) {
                                                $iskoristenoplo = $valueplo['P_1_used'] + $valueplo['P_2_used'] + $valueplo['P_3_used'] + $valueplo['P_4_used'] + $valueplo['P_5_used'] + $valueplo['P_6_used'] + $valueplo['P_7_used'];
                                                $totalplo = $valueplo['Br_dana_PLO'];
                                            }


                                            foreach ($plobylaw as $valueplobylaw) {
                                                //$totalplo = $valueplobylaw['sum_days'];
                                            }
                                            foreach ($currgo as $valuecurrgo) {
                                                $iskoristenocurr = $valuecurrgo['sum_hour'];;
                                                $iskoristenototal = ($iskoristenocurr / $br_sati) + $iskoristeno;
                                                $totalgoost = $brdana - $iskoristenototal;
                                            }

                                            foreach ($currgoPG as $valuecurrgoPG) {
                                                $iskoristenocurrPG = $valuecurrgoPG['sum_hour'];;
                                                $iskoristenototalPG = ($iskoristenocurrPG / $br_sati) + $iskoristenoPG;
                                                $totalgoostPG = $brdanaPG - $iskoristenototalPG;
                                                $ukupnogoiskoristeno = $iskoristenototalPG + $iskoristenototal;
                                                $ukupnogoost = $totalgoost + $totalgoostPG;
                                            }
                                            foreach ($currpcm as $valuecurrpcm) {
                                                $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
                                                $iskoristenototalpcm = ($iskoristenocurrpcm / $br_sati) + $iskoristenopcm;
                                                $totalpcmost = $brdanapcm - $iskoristenototalpcm;
                                            }

                                            foreach ($death as $valuedeath) {
                                                $iskoristenodeath = $valuedeath['sum_hour'];
                                                $deathukupno = ($iskoristenodeath / $br_sati) + $totaldeath;
                                            }
                                            foreach ($currupcm as $valuecurrupcm) {
                                                $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
                                                $iskoristenototalupcm = ($iskoristenocurrupcm / $br_sati) + $iskoristenoupcm;
                                                $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
                                            }
                                            foreach ($currplo as $valuecurrplo) {
                                                $iskoristenocurrplo = $valuecurrplo['sum_hour'];;
                                                $iskoristenototalplo = ($iskoristenocurrplo / $br_sati) + $iskoristenoplo;
                                                $totalploost = $totalplo - $iskoristenototalplo;
                                            }

                                            ?></small>

                            </div>

                            <?php

                            if (isset($_POST['Abs'])):

                            if ($_user['role'] == '2' || $_user['role'] == '3' || $_user['role'] == '4' || $_user['role'] == '0' and (isset($_POST['IDMonth']) and isset ($_POST['IDYear']))){
                            print_r(_statsDays3($absence_year, $month['id'], $item['user_id']));
                            ?>
                            <!--  <div style="margin-left:0px;">


              <div class="small-circle" style="background:#ccc;"></div> <?php echo __('Spa??eno'); ?>
               <div class="small-circle" style="background:#00cc00;"></div> <?php echo __('Odobreno'); ?>
             <div class="small-circle" style="background:#cc0000;"></div> <?php echo __('Odbijeno'); ?>



       </div>--->
                            <hr style="padding:0px; margin:0px;">

                            <br/>
                            <?php


                            $kvote = User::kvoteSatnice($item['employee_no'], $absence_year_full);

                            include('modules/core/views/kvote.satnice.aurora.php');

                            ?>
                            <br/>
                            <?php

                            $edit = $db->query("SELECT editable FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $item['employee_no'] . "' "); ?>

                            <?php if ($edit->rowCount() < 0) {
                                foreach ($edit as $valueedit) {
                                    $visible = $valueedit['editable'];
                                }
                            } else
                                $visible = 'N'; ?>

                            <?php
                            ?>
                            <br style="clear:both"/>
                            <?php if ($visible != 'N') { ?>
                                <a style="margin-top:10px;"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_day_add_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?>"
                                   data-widget="ajax" data-id="opt2" data-width="500"
                                   class="btn btn-red btn-md"><?php echo __('A??uriraj satnice'); ?> <i
                                            class="ion-ios-plus-empty"></i></a>
<!--                                <a style="margin-top:10px;"-->
<!--                                   href="--><?php //echo $url . '/modules/' . $_mod . '/pages/popup_day_review_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?><!--"-->
<!--                                   data-widget="ajax" data-id="opt2" data-width="500"-->
<!--                                   class="btn btn-red btn-md">--><?php //echo __('Odobri satnice'); ?><!-- <i-->
<!--                                            class="ion-ios-plus-empty"></i></a>-->
                                <a style="margin-top:10px;"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_day_cancel_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?>"
                                   data-widget="ajax" data-id="opt2" data-width="500"
                                   style="" class="btn btn-red btn-md"><?php echo __('Otka??i registraciju'); ?> <i
                                            class="ion-ios-plus-empty"></i></a>
                            <?php } ?>
                    </div>
                </div>
                <?php
                }
                endif;

                $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND user_id='" . $item['user_id'] . "' ORDER BY day");

                $get_termination = $db->query("select [termination_date] as termination_date,[employment_date] as employment_date from  " . $portal_users . "  where user_id = " . $item['user_id']);
                $termination = $get_termination->fetch();


                if ($get_days->rowCount() < 0 and (isset($_POST['Abs']) and $_POST['Abs'] = 1)) {

                    echo '<div class="box days">';
                    foreach ($get_days as $day) { ?>


                        <?php

                        if ($day['day'] == '1') {
                            switch ($day['weekday']) {

                                case '1': ?>

                                    <?php break;
                                case '2': ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <?php break;
                                case '3':
                                    ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>

                                    <?php break;
                                case '4': ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>

                                    <?php break;
                                case '5': ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>

                                    <?php break;
                                case '6': ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>


                                    <?php break;
                                case '7': ?>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>
                                    <div class="day" style="height:150px;" id="opt-'.$day['id'].'"></div>


                                    <?php break;

                            }
                        }


                        if (substr(_nameHRstatusName($day['status']), 0, 1) == 'B' and $day['review_status'] == '1')
                            $additional_background = 'background-color:yellow';
                        elseif (substr(_nameHRstatusName($day['status']), 0, 1) == 'G' and $day['review_status'] == '1')
                            $additional_background = 'background-color:lightblue';
                        //novi kod
                        elseif (($day['status'] == '83') and $day['review_status'] == '1')
                            $additional_background = 'background-color:#ffb366';
                        else
                            $additional_background = '';
                        //kraj novog koda

                        if ($day['review_status'] == '0') {
                            $css_border = '';


                        } elseif ($day['review_status'] == '1') {
                            $css_border = 'style="border-bottom-color:#00cc00;"';


                        } elseif ($day['review_status'] == '2') {
                            $css_border = 'style="border-bottom-color:#cc0000;"';
                        }

                        if (($termination['termination_date'] != '' and $day['Date'] > $termination['termination_date']) or ($termination['employment_date'] != '' and $day['Date'] < $termination['employment_date'])) {
                            echo '<div class="day" style="background-color: #de8b8b;height:150px ;font-size:11px;"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
                        } else {
                            if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['KindofDay'] != 'BHOLIDAY')) {
                                echo '<div class="day" style="height:150px;' . $additional_background . '"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
                            }
                            if (($day['weekday'] == '6') or ($day['weekday'] == '7')) {
                                echo '<div class="day" style="background-color:#DCD6D6;height:150px;font-size:13px;"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
                            }
                            if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['KindofDay'] == 'BHOLIDAY') and (rtrim(_nameHRstatusGroup($day['status'])) == 'Bolovanje' or rtrim(_nameHRstatusGroup($day['status'])) == 'Godi??nji odmor')) {
                                echo '<div class="day" style="height:150px;' . $additional_background . '"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
                            }

                            if (($day['weekday'] != '6') and ($day['weekday'] != '7') and ($day['KindofDay'] == 'BHOLIDAY') and (rtrim(_nameHRstatusGroup($day['status'])) != 'Bolovanje' and rtrim(_nameHRstatusGroup($day['status'])) != 'Godi??nji odmor')) {

                                /* Test 110 */
                                if (($day['review_status'] == '1' or $day['review_status'] == '0')):
                                    $color_not_praznik = getBoja($day['status']);
                                else:
                                    $color_not_praznik = getBoja($day['status']);
                                endif;

                                /* Test 110 */

                                echo '<div class="day" style="background-color:' . $color_not_praznik . ';height:150px;"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
                            }
                        }

                        if ($day['hour_pre'] != '' and $day['hour_pre'] != '0') {
                            $prekovremeno = '* ' . _nameHRstatus($day['status_pre']);
                            $prekovremeno = '<span style="color:red">' . $prekovremeno . '</span><br/>';
                        } else
                            $prekovremeno = '';

                        if ($day['status'] != '5' and $day['review_status'] == '0')
                            $reg = 'Reg:' . _nameHRstatus($day['status']);
                        else
                            $reg = '';


                        if ($termination['termination_date'] != '' and $day['Date'] > $termination['termination_date']) {
                            $status_prekid = 'Prekid rada<br/>';
                        } else if ($termination['employment_date'] != '' and $day['Date'] < $termination['employment_date']) {
                            $status_prekid = 'Nezaposlen/a<br/>';
                        } elseif ($day['review_status'] == '1') {
                            $status_prekid = _nameHRstatus($day['status']) . '<br/>';
                        } else {
                            $status_prekid = _nameHRstatus('5') . '<br/>';
                        }

                        if ($day['status'] != '5' and $day['review_status'] == '0')
                            $status_prekid = '';
                        if ($day['status'] == '83')
                            $status_prekid = '';
                        if ($day['KindofDay'] == 'BHOLIDAY' and $day['review_status'] == '1' and in_array($day['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 107, 108, 73, 81)))
                            $day['Description'] = '';

                        if ($day['KindofDay'] == 'BHOLIDAY' and $day['status'] != 83) {
                            $day['Description'] = '';
                        }
                        if ($day['review_status'] == 1 and $day['status'] == 83 and $day['Description'] == '') {
                            $status_prekid = _nameHRstatus($day['status']);
                        }

                        echo '<b><lable>' . $day['day'] . '</lable></b>';
                        if (($day['weekday'] != '6') and ($day['weekday'] != '7')) {
                            $dayname = '';


                            switch ($day['weekday']) {
                                case 1:
                                    $dayname = 'Ponedjeljak';
                                    break;
                                case 2:
                                    $dayname = 'Utorak';
                                    break;
                                case 3:
                                    $dayname = 'Srijeda';
                                    break;
                                case 4:
                                    $dayname = '??etvrtak';
                                    break;
                                case 5:
                                    $dayname = 'Petak';
                                    break;
                            }

                            echo '<small style=height:60px;font-size:11px;>' . $day['day'] . '.' . $day['month_id'] . '<b>' . '';
                            if ($day['KindofDay'] != "SUNDAY" and $day['KindofDay'] != "SATURDAY" and $day['KindofDay'] != "BANKDAY" and $day['Description'] != "") {
                                echo "<br>";
                            }
                            echo '' . $day['Description'] . '</b><br/><b>' . $dayname . '</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                        }


                        if (($day['weekday'] == '6') and (($day['hour_pre'] != '' and $day['hour_pre'] != '0') or ($day['hour'] != 0 and in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90))) or in_array($day['status'], array(73, 81)))) {
                            echo '<small>' . $day['day'] . '.' . $day['month_id'] . '<br/><b>Subota</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                        } elseif (($day['weekday'] == '6') and !in_array($day['status'], array(73, 81)) and (($day['hour_pre'] == '' and $day['hour'] == 0) or !in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90)))) {
                            echo '<small>' . $day['day'] . '.' . $day['month_id'] . '<br/><b>Subota</b><br/></small>';
                        } elseif (($day['weekday'] == '7') and (($day['hour_pre'] != '' and $day['hour_pre'] != '0') or ($day['hour'] != 0 and in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90))) or in_array($day['status'], array(73, 81)))) {
                            echo '<small>' . $day['day'] . '.' . $day['month_id'] . '<br/><b>Nedjelja</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                        } elseif (($day['weekday'] == '7') and !in_array($day['status'], array(73, 81)) and (($day['hour_pre'] == '' and $day['hour'] == 0) or !in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90)))) {
                            echo '<small>' . $day['day'] . '.' . $day['month_id'] . '<br/><b>Nedjelja</b><br/></small>';
                        } else if ($day['weekday'] == '6' or $day['weekday'] == '7') {
                            $day_type = "";
                            switch ($day['weekday']) {
                                case "6":
                                    $day_type = "Subota";
                                    break;
                                case "7":
                                    $day_type = "Nedjelja";
                                    break;
                            }
                            echo '<small>' . $day['day'] . '.' . $day['month_id'] . '<br/><b>' . $day_type . '</b><br/></small>';
                        }

                        if (($day['review_status'] != '0') and ($day['KindofDay'] != 'BHOLIDAY' or $day['review_user'] != '')) {
                            if (($day['weekday'] == '6' or $day['weekday'] == '7') and isAllowedStatusWeekend($day['status']) == false) {
                            } else {
                                echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_view.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-eye"></i></a>';
                            }
                        }

                        $contact_center_adminq = $db->query("SELECT [Administrator Contact Center] from  " . $nav_human_resource_setup . "  ");
                        $contact_center_admin = $contact_center_adminq->fetch();

                        if (($day['review_status'] != '0') and ($visible != 'N') and (($termination['termination_date'] == '' or ($termination['termination_date'] != '' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >= $termination['employment_date']))) {
                            echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_edit.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="200" ><i class="ion-edit"></i></a>';
//                            echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_review.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="200" ><i class="fa fa-check-square-o" aria-hidden="true"></i></a>';

                            if (($day['weekday'] == '6' or $day['weekday'] == '7' or $_user['employee_no'] == $contact_center_admin['Administrator Contact Center']) and isAllowedStatusWeekend($day['status']) == false) {
                            } else {
                                echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_view.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-eye"></i></a>';
                            }
                        }

                        if (($day['review_status'] == '0') and ((($day['weekday'] != '6') and ($day['weekday'] != '7')) or $_user['employee_no'] == $contact_center_admin['Administrator Contact Center']) and ($visible != 'N') and (($termination['termination_date'] == '' or ($termination['termination_date'] != '' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >= $termination['employment_date']))) {
                            echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_edit.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="200" ><i class="ion-edit"></i></a>';

//                            echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_review.php?id=' . $day['id'] . '" class=" table-btn-icons " data-widget="ajax" data-id="opt2" data-width="200" ><i class="fa fa-check-square-o" aria-hidden="true"></i></a>';

                        }

                        echo '</div>';
                    }

                    echo '</div>';
                } else {
                    echo '<div class="text-center"></div>';
                }

                ?>


                </tr>
                <?php } ?>
                </tbody>

                <?php } else {
                    echo '<tr><td colspan="3" class="text-center">' . __('Jo?? nije bilo unosa') . '</td></tr>';
                } ?>
                </table>
                <div class="text-left">
                    <div class="btn-group paginate">

                        <?php echo _pagination($path, $_num, $limit, $total); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>
<!-- END - Main section -->


<?php

include $_themeRoot . '/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<script>

    var year = '<?php if (isset($_POST['IDYear']) and ($_POST['IDYear']) != '') {
        echo $_POST['IDYear'];
    } else {
        echo date("Y");
    }?>';
    var month = '<?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '') {
        echo $_POST['IDMonth'];
    } else {
        echo date("m");
    }?>';
    var username = '<?php if (isset($_POST['ime_prezime'])) {
        echo $_POST['ime_prezime'];
    }?>';

    window.onload = function () {
        var str = '<?php if (isset($_POST['ime_prezime'])) {
            echo $_POST['ime_prezime'];
        }?>';
        console.log(str);

        if ($("#teams").val() != '') {
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-users",
                    team: $("teams").val(),
                    'year': year,
                    'month': month,
                    'username': username
                },
                function (returnedData) {
                    $('#ime_prezime').html(returnedData);
                    $("#ime_prezime").select2();
                });
        } else if ($("#streams").val() != '') {
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-teams",
                    stream: $("#streams").val()
                },
                function (returnedData) {
                    $('#teams').html(returnedData);
                    $("#teams").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-users",
                    stream: $("#streams").val(),
                    'year': year,
                    'month': month,
                    'username': username
                },
                function (returnedData) {
                    $('#ime_prezime').html(returnedData);
                    $("#ime_prezime").select2();
                });

        } else if ($("#regije").val() != '') {
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-streams",
                    region: $("#regije").val()
                },
                function (returnedData) {
                    $('#streams').html(returnedData);
                    $("#streams").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-teams",
                    stream: ''
                },
                function (returnedData) {
                    $('#teams').html(returnedData);
                    $("#teams").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-users",
                    region: $("#regije").val(),
                    'year': year,
                    'month': month,
                    'username': username
                },
                function (returnedData) {
                    $('#ime_prezime').html(returnedData);
                    $("#ime_prezime").select2();
                });

        } else if ($("#B1").val() != '') {
            console.log($("#B1").val());
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-regions",
                    B_1: $("#B1").val()
                },
                function (returnedData) {
                    $('#regije').html(returnedData);
                    $("#regije").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-streams",
                    region: ''
                },
                function (returnedData) {
                    $('#streams').html(returnedData);
                    $("#streams").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-teams",
                    stream: ''
                },
                function (returnedData) {
                    $('#teams').html(returnedData);
                    $("#teams").select2();
                });
            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "get-users",
                    b1: $("#B1").val(),
                    'year': year,
                    'month': month,
                    'username': username
                },
                function (returnedData) {
                    $('#ime_prezime').html(returnedData);
                    $("#ime_prezime").select2();
                });
        }


    };

    $("#odustani").click(function (e) {
        e.preventDefault();


        $("#B1").val("");
        $("#B1").trigger("change");
        $("#ime_prezime").val("");
        $("#ime_prezime").trigger("change");
        $("#regije").val("");
        $("#regije").trigger("change");
        $("#streams").val("");
        $("#streams").trigger("change");
        $("#teams").val("");
        $("#teams").trigger("change");
        $("#admin-form").submit();

    });
    $(".paginate a").click(function (e) {
        e.preventDefault();

        link_action = $(this).attr("href");

        $("#admin-form").attr("action", link_action);
        $("#admin-form").submit();
    });

    $("#B1").select2();
    $("#regije").select2();
    $("#streams").select2();
    $("#teams").select2();
    $("#ime_prezime").select2();

    var year = '<?php if (isset($_POST['IDYear']) and ($_POST['IDYear']) != '') {
        echo $_POST['IDYear'];
    } else {
        echo date("Y");
    }?>';
    var month = '<?php if (isset($_POST['IDMonth']) and ($_POST['IDMonth']) != '') {
        echo $_POST['IDMonth'];
    } else {
        echo date("m");
    }?>';


    $('#B1').on('change', function () {
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-regions",
                B_1: this.value
            },
            function (returnedData) {
                $('#regije').html(returnedData);
                $("#regije").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-streams",
                region: ''
            },
            function (returnedData) {
                $('#streams').html(returnedData);
                $("#streams").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-teams",
                stream: ''
            },
            function (returnedData) {
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-users",
                b1: this.value,
                'year': year,
                'month': month
            },
            function (returnedData) {
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#regije').on('change', function () {
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-streams",
                region: this.value
            },
            function (returnedData) {
                $('#streams').html(returnedData);
                $("#streams").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-teams",
                stream: ''
            },
            function (returnedData) {
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-users",
                region: this.value,
                'year': year,
                'month': month
            },
            function (returnedData) {
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#streams').on('change', function () {
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-teams",
                stream: this.value
            },
            function (returnedData) {
                $('#teams').html(returnedData);
                $("#teams").select2();
            });
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-users",
                stream: this.value,
                'year': year,
                'month': month
            },
            function (returnedData) {
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

    $('#teams').on('change', function () {
        $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                request: "get-users",
                team: this.value,
                'year': year,
                'month': month
            },
            function (returnedData) {
                $('#ime_prezime').html(returnedData);
                $("#ime_prezime").select2();
            });
    })

</script>

<script type="text/javascript">
    $(document).ready(function () {

        // If cookie is set, scroll to the position saved in the cookie.
        if ($.cookie("scroll") !== null) {
            $(document).scrollTop($.cookie("scroll"));
        }

        // When a button is clicked...
        $('a').on("click", function () {
            // Set a cookie that holds the scroll position.
            $.cookie("scroll", $(document).scrollTop());

        });

    });

    $("#month").datepicker({
        format: "m-yyyy",
        startView: "months",
        minViewMode: "months",
        language: 'bs',
        //todayBtn: false,
    });

    $("#month").datepicker("setDate", new Date(year + '/' + month + '/01')).on('changeDate', function (ev) {
        console.log('sta ima');
        var datum = $('#month').val();
        var arr = datum.split('-');

        $("input[name='IDMonth']").val(parseInt(arr[0]));
        $("input[name='IDYear']").val(arr[1]);
        $("#admin-form").submit();
    });
</script>


</body>
</html>
