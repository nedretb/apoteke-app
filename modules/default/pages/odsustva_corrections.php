<?php
_pagePermission(5, false);

if (isset($_POST['dateFrom']))
    $godina = date("Y", strtotime(str_replace("/", "-", $_POST['dateFrom'])));
else {
    $godina = date("Y");
}

$mjesec = date("n");
$number_of_days = cal_days_in_month(CAL_GREGORIAN, 12, $godina);

$get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
$get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE id = " . $mjesec);

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

        <div class="container" style="width:80%; margin-top:25px;">

            <div class="row">

                <div class="col-sm-4">
                    <h2 style="margin-top:0px;">

                        <?php $naslov = 'Moja odsustva';

                        if (isset($_GET['odobreno'])) {
                            if ($_GET['odobreno'] == 'true') {
                                $filter_odobreno = 'true';
                                $naslov = 'Moji odobreni zahtjevi - korekcije';
                            } elseif ($_GET['odobreno'] == 'false') {
                                $filter_odobreno = 'false';
                                $naslov = 'Moji neodobreni zahtjevi - korekcije';
                            } elseif ($_GET['odobreno'] == 'rejected') {
                                $filter_odobreno = 'rejected';
                                $naslov = 'Moji odbijeni zahtjevi - korekcije';
                            }
                        } else {
                            $filter_odobreno = 'none';
                        }

                        if (isset($_GET['odobreno_cancel'])) {
                            if ($_GET['odobreno_cancel'] == 'true') {
                                $filter_odobreno_cancel = 'true';
                                $naslov = 'Moja odobrena otkazivanja - korekcije';
                            } else {
                                $filter_odobreno_cancel = 'false';
                                $naslov = 'Moja neodobrena otkazivanja - korekcije';
                            }
                        } else {
                            $filter_odobreno_cancel = 'none';
                        }

                        echo $naslov; ?>
                    </h2>

                </div>
                <div class="col-sm-8"><br/>
                    <div class="pull-right">
                        <form id="popup_form1" method="post">

                            <input type="hidden" name="get_month" value="<?php echo $mjesec; ?>"/>
                            <input type="hidden" name="get_year" value="<?php echo $godina; ?>"/>

                            <?php
                            if (isset($_POST['dateFrom'])) {
                                $month_from = date("n", strtotime(str_replace(".", "-", $_POST['dateFrom'])));

                            } else {
                                $month_from = 1;
                            }

                            if (isset($_POST['dateTo']))
                                $month_to = date("n", strtotime(str_replace(".", "-", $_POST['dateTo'])));
                            else
                                $month_to = 12;

                            if (isset($_POST['dateFrom']))
                                $day_from = date("j", strtotime(str_replace(".", "-", $_POST['dateFrom'])));
                            else
                                $day_from = 1;

                            if (isset($_POST['dateTo']))
                                $day_to = date("j", strtotime(str_replace(".", "-", $_POST['dateTo'])));
                            else
                                $day_to = $number_of_days;

                            ?>


                            <div class="row col-sm-12">
                                <div class="col-sm-4" style="">
                                    <div id="dt">
                                        <input type="text" name="dateFrom" class="form-control"
                                               style="width:120px;height:35px;" id="dateOD" placeholder="dd.mm.yyyy"
                                               title="" value="<?php if (isset($_POST['dateFrom'])) {
                                            echo date('d.m.Y', strtotime(str_replace("/", "-", $_POST['dateFrom'])));
                                        } else {
                                            echo date('d.m.Y', strtotime("01-" . $month_from . "-" . $year['year']));
                                        } ?>">
                                    </div>
                                    <br/>
                                </div>


                                <div class="col-sm-4" style="">
                                    <div id="dt">
                                        <input type="text" name="dateTo" class="form-control"
                                               style="width:120px;height:35px;" id="dateDO" placeholder="dd/mm/yyyy"
                                               title="" value="<?php if (isset($_POST['dateTo'])) {
                                            echo date('d.m.Y', strtotime(str_replace("/", "-", $_POST['dateTo'])));
                                        } else {
                                            echo date('d.m.Y', strtotime($number_of_days . "-" . $month_to . "-" . $year['year']));
                                        } ?>">
                                    </div>
                                    <br/>
                                </div>
                                <div class="col-sm-4" style="">
                                    <button type="submit" class="btn btn-red "><?php echo __('Pretraži!'); ?> <i
                                                class="ion-ios-download-outline"></i></button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="row">


                <?php
                print_r(_statsDaysFreeReifCorrections($year['id'], $month_from, $month_to, $day_from, $day_to, $filter_odobreno, $filter_odobreno_cancel));
                ?>

            </div>
            <hr style="padding:0px; margin:5px;">


            <br/>
            <?php
            include_once('modules/core/Model.php');
            include_once('modules/core/VS.php');
            include_once('modules/core/User.php');

            $kvote = User::kvoteSatnice($_user['employee_no'], $year['year'], 'korekcije');

            include('modules/core/views/kvote.satnice.aurora.php');

            ?>
            <br/>


            <?php

            $br_sati = $_user['br_sati'];

            ?>
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
        });
        $('#dateDO').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
        });
        $("#dateOD").on('change', function (e) {
            $("#dateDO").datepicker("destroy");
            $('#dateDO').datepicker({
                defaultViewDate: new Date('2017/05/01'),
                format: 'dd.mm.yyyy',
                language: 'bs',
                startDate: $("#dateOD").val()
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
