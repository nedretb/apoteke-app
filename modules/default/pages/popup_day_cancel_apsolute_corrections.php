<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';

?>

<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Otkaži registraciju'); ?></span></h4>
</div>

<section>
    <div class="content clear">
        <?php

        $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_GET['year'] . "'");
        $get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE id='" . $_GET['month'] . "'");

        $get_y = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $_GET['year'] . "'");
        $get_m = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_month . "  WHERE id='" . $_GET['month'] . "'");

        $result = $get_y->fetch();
        $total = $result[0];
        $result2 = $get_m->fetch();
        $total2 = $result2[0];


        $get = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_day . "  ");
        $getrow = $db->query("SELECT month_id FROM  " . $portal_hourlyrate_day . " ");
        if ($get->rowCount() < 0){
        $row = $getrow->fetch();
        $year = $get_year->fetch();
        $month = $get_month->fetch();

        ?>


        <div id="res"></div>
        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="parent-day-cancel_apsolute_corrections"/>
            <input type="hidden" name="get_month" value="<?php echo $_GET['month']; ?>"/>
            <input type="hidden" name="get_year" value="<?php echo $_GET['year']; ?>"/>


            <div class="row">
                <div class="col-sm-6">
                    <label><?php echo __('Od'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateFrom" class="form-control" style="width:120px;"
                               value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>" id="dateOD"
                               placeholder="dd/mm/yyyy" title="" required>
                    </div>
                    <br/>
                </div>


                <div class="col-sm-6">
                    <label><?php echo __('Do'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateTo" class="form-control" style="width:120px;"
                               value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>" id="dateDO"
                               placeholder="dd/mm/yyyy" title="" required>
                    </div>
                    <br/>
                </div>
            </div>

            </br>
            <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                        class="ion-ios-download-outline"></i></button>

            <?php

            } else {
                echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
            }
            ?>
        </form>

        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>
        <script>
            $(document).ready(function () {

                $('.dialog-loader').hide();

            });
            var today = new Date();
            var startDate = new Date();
            var startDate = new Date('<?php echo $year['year']; ?>/' + '<?php echo $_GET['month']; ?>' + '/01');
            var year = '<?php echo $year['year']; ?>';
            $('#dateOD').datepicker({
                todayBtn: "linked",
                format: 'dd.mm.yyyy',
                language: 'bs',
                startDate: startDate,
                endDate: new Date(year + '/12/31')
            });
            $('#dateDO').datepicker({
                todayBtn: "linked",
                format: 'dd.mm.yyyy',
                language: 'bs',
                startDate: startDate,
                endDate: new Date(year + '/12/31')
            });
            /*
              $("#dateOD").datepicker( "setDate" , new Date(year +'.'+'<?php echo $_GET['month']; ?>'+'.01') );
    $("#dateDO").datepicker( "setDate" , new Date(year + '.'+'<?php echo $_GET['month']; ?>'+'.01') );*/
            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function (form) {
                    $('.dialog-loader').show();
                    $(form).ajaxSubmit({
                        url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                        type: "post",
                        success: function (data) {
                            $("#res").html(data);
                            $('.dialog-loader').hide();
                        }
                    });
                }
            });
            $('#status').on('change', function () {

                if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                    $('#comment_row').show();
                    $('#komentar').prop('required', true);
                } else {
                    $('#comment_row').hide();
                    $('#komentar').prop('required', false);
                }
            });


            $("#dateOD").on('change', function (e) {
                $("#dateDO").datepicker("destroy");

                $('#dateDO').datepicker({
                    //todayBtn: "linked",
                    format: 'dd.mm.yyyy',
                    language: 'bs',
                    startDate: $("#dateOD").val()
                    //endDate: new Date(year + '/12/31')

                });
                $("#dateDO").datepicker("setDate", $("#dateOD").val());

            });
        </script>
        <?php

        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
