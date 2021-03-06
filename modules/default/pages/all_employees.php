<?php
_pagePermission(4, false);
include_once('modules/core/Model.php');
include_once('modules/core/VS.php');
include_once('modules/core/User.php');

header('Location: ?m=default&p=employees');

?>
</div>
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

    .ui-datepicker-calendar {
        display: none;
    }


</style>

<div class="" style="padding-left: 15px;">
    <div class="header">

        <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
        <h4><span><?php echo __('Lista radnika'); ?></span></h4>
    </div>
</div>
<section>
    <div class="content clear">

        <?php

        if (isset($_POST['IDEmp'])) {
            $idemp = $_POST['IDEmp'];
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
        }
        $employment_filter = $year['id'] . "-" . $month['id'] . "-30 00:00:00.000";

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


        <div class="box" style="width:22%; display: block; float:left; margin-right:20px;">
            <div class="content">
                <table class="table table-hover">

                    <div class="row">
                        <div class="col-xs-12">

                            <form id="admin-form" method="post">

                                <input id="pg" type="hidden" name="pg" value="">
                                <div style="display:none; flex-direction:column; width:80%;height:80px; margin-bottom:30px;">
                                    <label type="hidden" class="lable-admin1"><?php echo __('Mjesec-Godina'); ?></label>
                                    <input style="margin-top:20px; width:200px;" readonly type="text" id="month" name="month" class="monthPicker"/>

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

                                </div>
                                <label class="lable-admin1"><?php echo __('Sektor'); ?></label>
                                <select id="B1" name="IDB1" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionB_1($B_1) ?>
                                </select><br/><br/>


                                <label class="lable-admin1"><?php echo __('Odjel'); ?></label>
                                <select id="regije" name="IDReg" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionRegion($B_1, $region) ?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Grupa'); ?></label>
                                <select id="streams" name="IDStream" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionStream($region, $stream) ?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Tim'); ?></label>
                                <select id="teams" name="IDTeam" class="rcorners1" style="outline:none;width:200px;"
                                        class="form-control">
                                    <?php echo _optionTeam($stream, $team) ?>
                                </select><br/><br/>

                                <label class="lable-admin1"><?php echo __('Ime'); ?></label>
                                <select id="ime_prezime" name="ime_prezime" class="rcorners1"
                                        style="outline:none;width:200px;" class="form-control"
                                        onchange="this.form.submit();">
                                    <?php echo _optionName($team, $stream, $region, $B_1, $ime_prezime, $filtertdate) ?>
                                </select><br/>
                                <br>

                                <div>
                                   <button type="submit" style="width:125px;"
                                            class="btn btn-red pull-left btn-sm"><?php echo __('Izaberi!'); ?> <i
                                            class="ion-ios-download-outline"></i></button>
                                    <button id="odustani"
                                            style="width:125px;margin-left:5px !important;background-color:#006595;"
                                            class="btn btn-red pull-left btn-sm"><?php echo __('Odustani!'); ?> <i
                                            class="ion-ios-download-outline"></i></button>
                                    <br/>
                                </div>

                            </form>
                        </div>
                    </div>


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

    if (isset($_POST['IDTeam']) and strpos($_POST['IDTeam'], '-') !== false) {
        $pieces = explode('-', $_POST['IDTeam']);
        $team_query = $pieces[0];
        $stream_query = " and Stream_description='" . $pieces[1] . "'";
    } elseif (isset($_POST['IDTeam'])) {
        $team_query = $_POST['IDTeam'];
        $stream_query = '';
    }

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

                            <th width="40" style="display:block;" class="hidden-xs"><?php echo __('Per. br.'); ?></th>
                            <th width="70;" class="hidden-xs"></th>


                            <?php

                            if (isset($_POST['IDEmp'])) {
                                $idemp = $_POST['IDEmp'];
                            }
                            if (isset($_POST['IDMonth']) and isset ($_POST['IDYear'])) {
                                $idm = $_POST['IDMonth'];
                                $idy = $_POST['IDYear'];

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

                        // bojiti satnice

                        $get_dataq = $db->query("  SELECT TOP 1000 *
  FROM  " . $nav_work_booklet . "  
  where [Employee No_]= " . $item['employee_no'] . " and [Current Company] = 1 ");
                        $get_data = $get_dataq->fetchAll(PDO::FETCH_ASSOC);


                        $i++;
                        $tools_id = $item['user_id'];
                        $emp_id = $item['employee_no'];
                        $department_name = $item['sector'];
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
                                        <img
                                             src="<?php echo $item['picture']; ?>" class="img-circle"
                                             style="width:100%;">
                                    <?php } else { ?>
                                        <img src="<?php echo $_themeUrl; ?>/images/noimage-user.png" class="img-circle">
                                    <?php } ?>

                                </td>


                                <td><a href="?module=default&p=edit-profile&u=<?php echo $item['employee_no']; ?>"><?php echo $item['First Name'] . ' ' . _optionGetLastNameNAV($item['employee_no']); ?></a><br/><small><?php echo $item['position']; ?>
                                        <br/><?php echo $item['sector'];
                                        $yearid = $db->query("SELECT [id], year FROM  " . $portal_hourlyrate_year . "  WHERE user_id='" . $item['user_id'] . "' AND year='" . $year['id'] . "'");

                                        foreach ($yearid as $value2) {
                                            $absence_year = $value2['id'];
                                            $absence_year_full = $value2['year'];
                                        }

                                        $br_sati = $item['br_sati'];
                                        ?></small>


                        </div>


                        <?php
                        if (isset($_POST['Abs'])):

                        if ($_user['role'] == '2' || $_user['role'] == '1' || $_user['role'] == '4' || $_user['role'] == '0' and (isset($_POST['IDMonth']) and isset ($_POST['IDYear']))){
                            print_r(_statsDays($absence_year, $month['id'], $item['user_id']));
                            ?>
                            <hr style="padding:0px; margin:0px;">
                            <br/>

                            <?php


                            $kvote = User::kvoteSatnice($item['employee_no'], $absence_year_full);

                            include('modules/core/views/kvote.satnice.aurora.php');

                            ?>

                            <?php $edit = $db->query("SELECT editable FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $item['employee_no'] . "' "); ?>

                            <?php if ($edit->rowCount() < 0) {
                                foreach ($edit as $valueedit) {
                                    $visible = $valueedit['editable'];
                                }
                            } else
                                $visible = 'N'; ?>


                            <?php
                            ?><br style="clear:both"/>
                            <?php if ($visible != 'N') { ?>

                                <a style="margin-top:10px;"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_day_add_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?>"
                                   data-widget="ajax" data-id="opt2" data-width="500"
                                   class="btn btn-red btn-md"><?php echo __('A??uriraj satnice'); ?> <i
                                        class="ion-ios-plus-empty"></i></a>
                                <a style="margin-top:10px;"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_day_review_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?>"
                                   data-widget="ajax" data-id="opt2" data-width="500"
                                   class="btn btn-red btn-md"><?php echo __('Odobri satnice'); ?> <i
                                        class="ion-ios-plus-empty"></i></a>
                                <a style="margin-top:10px;"
                                   href="<?php echo $url . '/modules/' . $_mod . '/pages/popup_day_cancel_apsolute.php?year=' . $absence_year . '&month=' . $month['id']; ?>"
                                   data-widget="ajax" data-id="opt2" data-width="500"
                                   class="btn btn-red btn-md"><?php echo __('Otka??i registraciju'); ?> <i
                                        class="ion-ios-plus-empty"></i></a>
                            <?php } ?>
                        <?php } else {
                        ''; ?>
                </div>
            </div>

            <?php

            }
            endif;

            if (isset($_POST['IDEmp']) and ($_POST['IDEmp']) != '') {

                $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $idemp . "' ORDER BY day");
            } else {

                $get_days = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $absence_year . "' AND month_id='" . $month['id'] . "' AND user_id='" . $item['user_id'] . "' ORDER BY day");

                $get_termination = $db->query("select [termination_date] as termination_date,[employment_date] as employment_date from  " . $portal_users . "  where user_id = " . $item['user_id']);
                $termination = $get_termination->fetch();
            }


            if ($get_days->rowCount() < 0 and (isset($_POST['Abs']) and $_POST['Abs'] != "")) {
                /*and (isset($ShowCal) and $ShowCal=="yes")*/


                echo '<div class="box days">';
                foreach ($get_days as $day) { ?>


                    <?php

                    $oboji = true;

                    foreach ($get_data as $one) {
                        if ($one['Starting Date'] <= $day['Date'] and $one['Ending Date'] >= $day['Date']) $oboji = false;
                    }

                    if (date("Y-m-d") < $day['Date']) $oboji = false;


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


                    if ($oboji) {
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

                            if ($day['review_status'] == '0'):

                                $color_not_praznik = getBoja($day['status']);
                            else:
                                $color_not_praznik = getBoja($day['status']);
                            endif;
                            echo '<div class="day" style="background-color: ' . $color_not_praznik . ';height:150px;"  id="opt-' . $day['id'] . '" ' . $css_border . '>';
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
                    } else if ($oboji and $termination['employment_date'] != '' and $day['Date'] < $termination['employment_date']) {
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

                        echo '<small style=height:60px;font-size:11px;>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . ' <b>' . '';
                        if ($day['KindofDay'] != "SUNDAY" and $day['KindofDay'] != "SATURDAY" and $day['KindofDay'] != "BANKDAY" and $day['Description'] != "") {
                            echo "<br>";
                        }
                        echo '' . $day['Description'] . '</b><br/><b>' . $dayname . '</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                    }

                    if (($day['weekday'] == '6') and ($item['B_1_regions_description'] == 'Kontakt centar' or 1 == 1) and (($day['hour_pre'] != '' and $day['hour_pre'] != '0') or ($day['hour'] != 0 and in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90))) or in_array($day['status'], array(73, 81)))) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Subota</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                    } elseif (($day['weekday'] == '6') and ($item['B_1_regions_description'] == 'Kontakt centar' or 1 == 1) and ($day['hour_pre'] == '')) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Subota</b><br/></small>';
                    } elseif (($day['weekday'] == '6') and ($item['B_1_regions_description'] != 'Kontakt centar' or 1 == 1)) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Subota</b><br/></small>';
                    } elseif (($day['weekday'] == '7') and ($item['B_1_regions_description'] == 'Kontakt centar' or 1 == 1) and (($day['hour_pre'] != '' and $day['hour_pre'] != '0') or ($day['hour'] != 0 and in_array($day['status'], array(5, 85, 86, 87, 88, 89, 90))) or in_array($day['status'], array(73, 81)))) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Nedjelja</b><br/>' . $status_prekid . $prekovremeno . $reg . '</small>';
                    } elseif (($day['weekday'] == '7') and ($item['B_1_regions_description'] == 'Kontakt centar' or 1 == 1) and ($day['hour_pre'] == '')) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Nedjelja</b><br/></small>';
                    } elseif (($day['weekday'] == '7') and ($item['B_1_regions_description'] != 'Kontakt centar' or 1 == 1)) {
                        echo '<small>' . $day['day'] . '.' . $day['month_id'] . '.' . $idy . '<br/><b>Nedjelja</b><br/></small>';
                    }

                    if (($day['review_status'] != '0') and ($visible != 'N') and (($termination['termination_date'] == '' or ($termination['termination_date'] != '' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >= $termination['employment_date']))) {
                        echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_edit.php?id=' . $day['id'] . '" class="  table-btn-icons" data-widget="ajax" data-id="opt2" data-width="200" ><i class="ion-edit"></i></a>';
                        echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_review.php?id=' . $day['id'] . '" class="  table-btn-icons " data-widget="ajax" data-id="opt2" data-width="200" ><i class="fa fa-check-square-o" aria-hidden="true"></i></a>';

                        if (($day['weekday'] == '6' or $day['weekday'] == '7') and isAllowedStatusWeekend($day['status']) == false) {
                        } else {
                            echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_view.php?id=' . $day['id'] . '" class="  table-btn-icons" data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-eye"></i></a>';
                        }
                    }
                    if (($day['review_status'] == '0') and ($visible != 'N') and (($termination['termination_date'] == '' or ($termination['termination_date'] != '' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >= $termination['employment_date']))) {
                        echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_edit.php?id=' . $day['id'] . '" class="  table-btn-icons" data-widget="ajax" data-id="opt2" data-width="200" ><i class="ion-edit"></i></a>';
                        echo '<a href="' . $url . '/modules/' . $_mod . '/pages/popup_day_review.php?id=' . $day['id'] . '" class="  table-btn-icons" data-widget="ajax" data-id="opt2" data-width="200" ><i class="fa fa-check-square-o" aria-hidden="true"></i></a>';
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
    $("#month-export").datepicker( {
        format: "m",
        startView: "months",
        minViewMode: "months",
        language: 'bs',
        //todayBtn: false,
    });
    $("#year-export").datepicker( {
        format: "yyyy",
        startView: "years",
        minViewMode: "years",
        language: 'bs',
        //todayBtn: false,
    });

    $("#export-satnice").click(function(){

        org_export 		= $("#org-export").val();
        month_export 	= $("#month-export").val();
        year_export 	= $("#year-export").val();
        verified_export = 0;

        if(org_export != '' && month_export != '' && year_export != ''){

            var wait = $.alert({
                title: 'Generisanje!',
                content: '<div style="text-align:center"><br /><img src="theme/images/5.gif" width="32"  /><br /><br />Sistem kreira satnice. Molimo pri??ekajte!</div>',
                buttons: {
                    ok: {
                        btnClass: 'hide',
                    }
                }
            });


            $.post("<?php echo $url . '/modules/admin_manager_verification/ajax.php'; ?>", {
                    request: "generate-satnice",
                    org : org_export,
                    month : month_export,
                    year : year_export,
                    verifiedal : verified_export
                },
                function(returnedData){
                    wait.close();

                    $.alert({
                        title: 'Uspje??no!',
                        content: '<div style="text-align:center"><br /><i class="fa fa-check-circle" style="font-size: 32px; color:green;"></i><br /><br />Excel fajl je uspje??no kreiran!</div>',
                        buttons: {
                            Preuzmi: {
                                btnClass: 'btn btn-success showgreen',
                                action: function(){
                                    console.log(returnedData);
                                    location.replace('uploads/' + returnedData);
                                }
                            },
                            Zatvori: {
                                btnClass: 'btn btn-danger'
                            }
                        }
                    });
                });


            /*
            $.ajax({
              method: "POST",
              url: "",
              data: { 	request: "generate-satnice",
                        org : org_export,
                        month : month_export,
                        year : year_export
                    }
            })
            .done(function( msg ) {
                alert( "Data Saved: " + msg );
            });*/



        } else {

            $.alert({
                title: 'Gre??ka!',
                content: 'Sva polja su obavezna!'
            });

        }





    });

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


        function myFunction() {
            if (document.getElementById('Abs').checked)
                var params = [{name: 'Abs', value: '1'}];
            else
                params = [];

            $.each(params, function (i, param) {
                $('<input />').attr('type', 'hidden')
                    .attr('name', param.name)
                    .attr('value', param.value)
                    .appendTo('#admin-form');
            });

            document.getElementById("admin-form").submit();

            return true;
        }

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
    $("#month").datepicker("setDate", new Date(year + '/' + month + '/01')).on('changeDate', function (ev) {

        var datum = $('#month').val();
        var arr = datum.split('-');

        $("input[name='IDMonth']").val(parseInt(arr[0]));
        $("input[name='IDYear']").val(arr[1]);
        $("#admin-form").submit();
    });


</script>


</body>
</html>
