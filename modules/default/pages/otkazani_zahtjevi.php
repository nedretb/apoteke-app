<?php
_pagePermission(5, false);

if (isset($_POST['dateFrom']))
    $godina = date("Y", strtotime(str_replace("/", "-", $_POST['dateFrom'])));
else {
    $godina = date("Y");
}

$mjesec = date("n");
$number_of_days = cal_days_in_month(CAL_GREGORIAN, $mjesec, $godina);

$get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
$get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE id = " . $mjesec);


$get_week = $db->query("SELECT [Weekday] FROM  " . $portal_calendar . "  WHERE Month='" . $mjesec . "'");

$get_y = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $godina . "'");
$get_m = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_month . "  WHERE id='" . $mjesec . "'");

$result = $get_y->fetch();
$total = $result[0];
$result2 = $get_m->fetch();
$total2 = $result2[0];
if ($total > 0 || $total2 > 0) {

    $year = $get_year->fetch();
    $month = $get_month->fetch();

    ?>

    <!-- START - Main section -->
    <section class="full">

        <div class="container" style="width:80%;">

            <div class="row">

                <div class="col-sm-4">
                    <h2 style="margin-top:50px;">

                        <?php $naslov = 'Moji otkazani zahtjevi';

                        if (isset($_GET['odobreno'])) {

                        } else {
                            $filter_odobreno = 'none';
                        }

                        $countParents = $db->query("SELECT COUNT(*) FROM  " . $portal_users . "  WHERE parent = '$_user[employee_no]'");
                        $countParentsf = $countParents->fetch();

                        $admin_filter = "";
                        if ($_user['role'] == 2 or $_user['managment_level'] != 0 or $countParentsf[0] > 0) {

                            if (isset($_GET['admin']) and $_GET['admin'] == "true") {

                                $naslov = "Otkazani zahtjevi radnika";

                                if ($_user['role'] == 2 or $_user['managment_level'] != 0) {
                                    $admin_filter = "admin";
                                } else if ($countParentsf[0] > 0) {
                                    $admin_filter = "nadredjeni";
                                }

                            }
                        } else {

                        }

                        if (isset($_GET['odobreno_cancel'])) {
                            if ($_GET['odobreno_cancel'] == 'true') {
                                $filter_odobreno_cancel = 'true';
                                $naslov = 'Moja odobrena otkazivanja';
                            } else {
                                $filter_odobreno_cancel = 'false';
                                $naslov = 'Moja neodobrena otkazivanja';
                            }
                        } else {
                            $filter_odobreno_cancel = 'none';
                        }

                        echo $naslov; ?>
                    </h2>

                </div>
                <div class="col-sm-12"><br/>
                    <div class="pull-right">

                    </div>
                </div>
            </div>

            <div class="row">

                <form id="popup_form1" method="post">

                    <input type="hidden" name="get_month" value="<?php echo $mjesec; ?>"/>
                    <input type="hidden" name="get_year" value="<?php echo $godina; ?>"/>

                    <?php //print_r(_statsDays($year['id'],$month['id'],$_user['user_id']));

                    if (isset($_POST['dateFrom']))
                        $month_from = date("n", strtotime(str_replace(".", "-", $_POST['dateFrom'])));
                    else
                        $month_from = $month['month'];

                    if (isset($_POST['dateTo']))
                        $month_to = date("n", strtotime(str_replace(".", "-", $_POST['dateTo'])));
                    else
                        $month_to = $month['month'];


                    if (isset($_POST['dateFrom']))
                        $day_from = date("j", strtotime(str_replace(".", "-", $_POST['dateFrom'])));
                    else
                        $day_from = 1;

                    if (isset($_POST['dateTo']))
                        $day_to = date("j", strtotime(str_replace(".", "-", $_POST['dateTo'])));
                    else
                        $day_to = $number_of_days;


                    if (isset($_GET['odobreno']) or isset($_GET['odobreno_cancel'])) {
                        $day_from = 1;
                        $day_to = 31;
                        $month_from = 1;
                        $month_to = 12;

                    }

                    ?>


                    <div class="row col-sm-12">
                        <div class="col-sm-1" style="width:12%">
                            <div id="dt">
                                <input type="text" name="dateFrom" class="form-control" style="width:120px;height:35px;"
                                       id="dateOD" placeholder="dd.mm.yyyy" title=""
                                       value="<?php if (isset($_POST['dateFrom'])) {
                                           echo date('d.m.Y', strtotime(str_replace("/", "-", $_POST['dateFrom'])));
                                       } else {
                                           echo date('d.m.Y', strtotime("01-" . $month_from . "-" . $year['year']));
                                       } ?>">
                            </div>
                            <br/>
                        </div>


                        <div class="col-sm-1" style="width:12%">
                            <div id="dt">
                                <input type="text" name="dateTo" class="form-control" style="width:120px;height:35px;"
                                       id="dateDO" placeholder="dd/mm/yyyy" title=""
                                       value="<?php if (isset($_POST['dateTo'])) {
                                           echo date('d.m.Y', strtotime(str_replace("/", "-", $_POST['dateTo'])));
                                       } else {
                                           echo date('d.m.Y', strtotime($number_of_days . "-" . $month_to . "-" . $year['year']));
                                       } ?>">
                            </div>
                            <br/>
                        </div>
                        <div class="col-sm-1" style="width:12%">
                            <button type="submit" class="btn btn-red "><?php echo __('Pretraži!'); ?> <i
                                        class="ion-ios-download-outline"></i></button>
                        </div>

                    </div>

                </form>
                <?php
                echo _statsDaysFreeReifOtkazani($year['id'], $month_from, $month_to, $day_from, $day_to, $admin_filter);

                ?>

            </div>
            <hr style="padding:0px; margin:5px;">

            <?php


            $go = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $_user['employee_no'] . "'");
            $currgo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND (status='18') AND (date_NAV is null)");
            $currgoPG = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND (status='19') AND (date_NAV is null)");
            $currgototal = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND (status='19' or status='18') AND (date_NAV is null)");
            $blooddonor = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='32') AND (date_NAV is null)");
            $death = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
    AND weekday<>'6' AND weekday<>'7' AND (status='72') AND (date_NAV is null)");
            $pcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $_user['employee_no'] . "'");
            $currpcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND (status='21') AND (date_NAV is null)");
            $upcm = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $_user['employee_no'] . "'");
            $currupcm = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND (status='22') AND (date_NAV is null)");
            $plo = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  WHERE employee_no='" . $_user['employee_no'] . "'");
            $currplo = $db->query("SELECT sum(hour) as sum_hour FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year['id'] . "' AND month_id='" . $month['id'] . "' AND employee_no='" . $_user['employee_no'] . "' 
	AND weekday<>'6' AND weekday<>'7' AND ((status='27') or (status='28') or (status='29') or (status='30') or (status='31')   
	or (status='33')) AND (date_NAV is null)");
            $plobylaw = $db->query("SELECT sum(allowed_days) as sum_days FROM  " . $portal_hourlyrate_status . "  WHERE   
	status_group='P – PLACENO ODSUSTVO' ");


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
                $propaloGO = $valuego['G_2 not valid'];

                $totaldeath = $valuego['S_1_used'];
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
            foreach ($currpcm as $valuecurrpcm) {
                $iskoristenocurrpcm = $valuecurrpcm['sum_hour'];;
                $iskoristenototalpcm = ($iskoristenocurrpcm / 8) + $iskoristenopcm;
                $totalpcmost = $brdanapcm - $iskoristenototalpcm;
            }

            foreach ($blooddonor as $blood_donor) {
                $iskorenokrv = $blood_donor['sum_hour'];
                $krvukupno = ($iskorenokrv / 8) + $iskoristenokrv;
                $totalkrvloost = $totalkrv - $krvukupno;

            }

            foreach ($death as $valuedeath) {
                $iskoristenodeath = $valuedeath['sum_hour'];
                $deathukupno = ($iskoristenodeath / 8) + $totaldeath;
            }
            foreach ($currupcm as $valuecurrupcm) {
                $iskoristenocurrupcm = $valuecurrupcm['sum_hour'];;
                $iskoristenototalupcm = ($iskoristenocurrupcm / 8) + $iskoristenoupcm;
                $totalupcmost = $brdanaupcm - $iskoristenototalupcm;
            }

            foreach ($currplo as $valuecurrplo) {
                $iskoristenocurrplo = $valuecurrplo['sum_hour'];;
                $iskoristenototalplo = ($iskoristenocurrplo / 8) + $iskoristenoplo;
                $totalploost = $totalplo - $iskoristenototalplo;
            }
            ?>

            <content style="display:block;margin-bottom:20px;">

                <?php if ($propaloGO != 1) { ?>

                    <div style="width:130px;float:left;display:block;">
                        <div style="padding:10px;margin-left:7px;">
                            <?php
                            echo '<span style="color:#ccc;font-size:18px;">' . $brdanaPG . '</span> &nbsp; ';
                            echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototalPG . '</span>  &nbsp; ';
                            echo '<span style="color:#00cc00;font-size:18px;">' . $totalgoostPG . '</span>  &nbsp; ';
                            ?>
                            <small style="display:block;"><?php echo __('Godišnji odmor predhodna godina') ?></small>
                        </div>
                    </div>
                <?php }
                if ($propaloGO == 1 and $month['id'] > 6) { ?>
                    <div style="width:130px;float:left;display:block;">
                        <div style="padding:10px;margin-left:7px;">
                            <?php
                            echo '<span style="color:#ccc;font-size:18px;">' . $brdanaPG . '</span> &nbsp; ';
                            echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototalPG . '</span>  &nbsp; ';
                            echo '<span style="color:#00cc00;font-size:18px;">' . $totalgoostPG = '0' . '</span>  &nbsp; ';
                            ?>
                            <small style="display:block;"><?php echo __('Godišnji odmor predhodna godina') ?></small>
                        </div>
                    </div>
                <?php } ?>


                <div style="width:130px;float:left;display:block;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php
                        echo '<span style="color:#ccc;font-size:18px;">' . $brdana . '</span> &nbsp; ';
                        echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototal . '</span>  &nbsp; ';
                        echo '<span style="color:#00cc00;font-size:18px;">' . $totalgoost . '</span>  &nbsp; ';
                        ?>
                        <small style="display:block;"><?php echo __('Godišnji odmor tekuća godina') ?></small>
                    </div>
                </div>

                <div style="width:110px;display:block;float:left;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php
                        echo '<lable style="color:#ccc;font-size:18px;">' . $brdanapcm . '</lable> &nbsp; ';
                        echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototalpcm . '</span> &nbsp;';
                        echo '<span style="color:#00cc00;font-size:18px;">' . $totalpcmost . '</span>  &nbsp; ';
                        ?>
                        <small style="display:block;"><?php echo __('Plaćeni vjerski praznici') ?></small>
                    </div>

                </div>
                <div style="width:110px;display:block;float:left;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php
                        echo '<lable style="color:#ccc;font-size:18px;">' . $brdanaupcm . '</lable> &nbsp; ';
                        echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototalupcm . '</span> &nbsp;';
                        echo '<span style="color:#00cc00;font-size:18px;">' . $totalupcmost . '</span>  &nbsp; ';
                        ?>
                        <small style="display:block;"><?php echo __('Neplaćeni vjerski praznici') ?></small>
                    </div>

                </div>

                <div style="width:110px;display:block;float:left;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php
                        echo '<lable style="color:#ccc;font-size:18px;">' . $totalplo . '</lable> &nbsp; ';
                        echo '<span style="color:#cc0000;font-size:18px;">' . $iskoristenototalplo . '</span> &nbsp;';
                        echo '<span style="color:#00cc00;font-size:18px;">' . $totalploost . '</span>  &nbsp; ';
                        ?>
                        <small style="display:block;"><?php echo __('Plaćeno odsustvo') ?></small>
                    </div>
                </div>

                <div style="width:110px;display:block;float:left;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php

                        echo '<span style="color:#cc0000;font-size:18px;">' . $deathukupno . '</span> &nbsp;';

                        ?>
                        <small style="display:block;"><?php echo __('Ostala plaćena odsustva') ?></small>
                    </div>
                </div>

                <div style="width:110px;float:left;">
                    <div style="padding:10px;margin-left:7px;">
                        <?php
                        echo '<lable style="color:#ccc;font-size:18px;">' . $totalkrv . '</lable> &nbsp; ';
                        echo '<span style="color:#cc0000;font-size:18px;">' . $krvukupno . '</span> &nbsp;';
                        echo '<span style="color:#00cc00;font-size:18px;">' . $totalkrvloost . '</span>  &nbsp; ';
                        ?>
                        <small style="display:block;"><?php echo __('Darivanje krvi') ?></small>
                    </div>
                </div>


                <div style="width:300px; display:block;">

                    <div class="small-circle" style="background:#ccc;"></div> <?php echo __('Ukupno'); ?>
                    <div class="small-circle" style="background:#cc0000;"></div> <?php echo __('Iskorišteno'); ?>
                    <div class="small-circle" style="background:#00cc00;"></div> <?php echo __('Ostalo'); ?>


                </div>


            </content>


        </div>

    </section>
    <!-- END - Main section -->

    <?php

    include $_themeRoot . '/footer.php';

} else {
    echo '<script>window.location.href="' . $url . '/modules/default/unauthorized.php";</script>';
}

?>

<script>
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }

    $(document).ready(function () {
        var today = new Date();
        var startDate = new Date();
        var year = '<?php echo $godina;?>';
        $('#dateOD').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            //startDate: startDate,
            //endDate: new Date(year + '/12/31')
        });
        $('#dateDO').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
            //startDate: startDate,
            //endDate: new Date(year + '/12/31')
        });
        $("#dateOD").on('change', function (e) {
            $("#dateDO").datepicker("destroy");
            $('#dateDO').datepicker({
                //todayBtn: "linked",
                defaultViewDate: new Date('2017/05/01'),
                format: 'dd.mm.yyyy',
                language: 'bs',
                startDate: $("#dateOD").val()
                //endDate: new Date(year + '/12/31')

            });
            $("#dateDO").datepicker("setDate", $("#dateOD").val());

        });

        $("#export_excel").click(function () {
                $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                        request: "export-excel-reif",
                        year: <?php echo $year['id'];?>,
                        month_from: <?php echo $month_from;?>,
                        month_to: <?php echo $month_to;?>,
                        day_from: <?php echo $day_from;?>,
                        day_to: <?php echo $day_to;?>},
                    function (url) {
                        window.open(url);
                    });
            }
        );
        $("#export_excel_users").click(function () {
                $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                        request: "export-excel-reif-users",
                        year: <?php echo $year['id'];?>,
                        month_from: <?php echo $month_from;?>,
                        month_to: <?php echo $month_to;?>,
                        day_from: <?php echo $day_from;?>,
                        day_to: <?php echo $day_to;?>},
                    function (url) {
                        window.open(url);
                    });
            }
        );
        $("#export_pdf").click(function (e) {
                e.preventDefault();
                $.post("<?php echo $url . '/modules/default/ajax.php'; ?>", {
                        request: "export-pdf-reif",
                        year: <?php echo $year['id'];?>,
                        month_from: <?php echo $month_from;?>,
                        month_to: <?php echo $month_to;?>,
                        day_from: <?php echo $day_from;?>,
                        day_to: <?php echo $day_to;?>},
                    function (url) {
                        window.open(url);
                    });
            }
        );
    });

</script>

</body>
</html>
