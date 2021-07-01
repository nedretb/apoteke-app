<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';

?>

<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Ažuriraj satnice'); ?></span></h4>
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

            <input type="hidden" name="request" value="day-review_apsolute"/>
            <input type="hidden" name="get_month" value="<?php echo $_GET['month']; ?>"/>
            <input type="hidden" name="get_year" value="<?php echo $_GET['year']; ?>"/>


            <div class="row">
                <div class="col-sm-6">
                    <!-- <big><?php echo $row['day'] . '/' . $month['month'] . '/' . $year['year']; ?></big><br/>
          <b><?php echo _nameHRstatus($row['status']); ?></b><br/>
          <?php echo __('Broj sati'); ?> <b><?php echo $row['hour']; ?></b>-->
                </div>
                <div class="col-sm-6 text-right">
                    <label class="radio">
                        <input type="radio" name="status" value="1" disabled>
                        <i class="ion-android-checkbox-outline"></i><br>
                        <?php echo __('Odobreno'); ?>
                    </label>
                    <label class="radio">
                        <input type="radio" name="status" value="2" checked="true">
                        <i class="ion-android-close"></i><br>
                        <?php echo __('Odbijeno'); ?>
                    </label>
                </div>
            </div>

            <hr/>

            <label><?php echo __('Komentar'); ?></label><br/>
            <textarea name="comment" maxlength="250" class="form-control"></textarea>


            <div class="row">
                <div class="col-sm-6">
                    <label><?php echo __('Od'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateFrom" class="form-control" style="width:120px;" id="dateOD"
                               value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>"
                               placeholder="dd/mm/yyyy" title="" required>
                    </div>
                    <br/>
                </div>


                <div class="col-sm-6">
                    <label><?php echo __('Do'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateTo" class="form-control" style="width:120px;" id="dateDO"
                               value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>"
                               placeholder="dd/mm/yyyy" title="" required>
                    </div>
                    <br/>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6">
                    <label><?php echo __('Broj sati'); ?></label>
                    <input type="number" name="hour" value="8" min="0" max="24" class="form-control" readonly>
                </div>


                <!-- <div class="col-sm-6">
          <label><?php echo __('Status'); ?></label>
          <select style="padding:0px !important; " name="status" class="form-control" required>
            <?php echo _optionHRstatus($row['status']); ?>
          </select>
        </div>
      </div>-->
                </br>
                <button type="submit" style="margin-left:15px;     margin-top: 10px;"
                        class="btn btn-red "><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i>
                </button>

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
                var today = new Date();
                var startDate = new Date();
                var startDate = new Date('<?php echo $year['year']; ?>/' + '<?php echo $_GET['month']; ?>' + '/01');
                //var date = new Date('2017/0'+'<?php echo $_GET['month']; ?>'+'/01');
                var date = new Date('<?php echo $year['year']; ?>/05/01');
                var year = '<?php echo $year['year']; ?>';
                $('#dateOD').datepicker({
                    //todayBtn: "linked",
                    defaultViewDate: new Date('<?php echo $year['year']; ?>/05/01'),
                    format: 'dd.mm.yyyy',
                    language: 'bs',
                    startDate: startDate,
                    endDate: new Date(year + '/12/31')

                });

                $('#dateDO').datepicker({
                    //todayBtn: "linked",
                    defaultViewDate: new Date('<?php echo $year['year']; ?>/05/01'),
                    format: 'dd.mm.yyyy',
                    language: 'bs',
                    startDate: startDate,
                    endDate: new Date(year + '/12/31')

                });


                $("#dateOD").on('change', function (e) {
                    console.log($("#dateOD").val());
                    $("#dateDO").datepicker("destroy");
                    console.log('destroyed');
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
                $('.dialog-loader').hide();
            });
            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function (form) {
                    $('.dialog-loader').show();
                    $(form).ajaxSubmit({
                        url: "<?php echo $url . '/modules/admin_hourly_rate/ajax.php'; ?>",
                        type: "post",
                        success: function (data) {
                            $("#res").html(data);
                            $('.dialog-loader').hide();
                        }
                    });
                }
            });
        </script>

        <?php

        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
