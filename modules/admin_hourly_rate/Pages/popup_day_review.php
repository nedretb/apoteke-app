<?php
require_once '../../../configuration.php';


?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Obrada satnice!'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $_GET['id'] . "'");
        $get2 = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $_GET['id'] . "'");
        if ($get2->rowCount() < 0) {
            $row = $get->fetch();

            $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $row['year_id'] . "'");
            $get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE id='" . $row['month_id'] . "'");
            $year = $get_year->fetch();
            $month = $get_month->fetch();
            $user = _user($row['user_id']);
            ?>

            <div id="res"></div>

            <form id="popup_form" method="post">

                <input type="hidden" name="request" value="day-review"/>
                <input type="hidden" name="request_id" value="<?php echo $_GET['id']; ?>"/>
                <input type="hidden" name="status_id" value="<?php echo $row['status']; ?>"/>
                <?php if ($row['status'] != "5"): ?>
                    <input type="hidden" name="reg" value="1"/>
                <?php endif; ?>

                <div class="row">
                    <div class="col-sm-6">
                        <big><?php echo $row['day'] . '/' . $month['month'] . '/' . $year['year']; ?></big><br/>


                        <?php
                        if ((in_array($row['status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 73, 74, 75, 76, 77, 78, 81, 105, 107, 108, 91, 92, 93, 94, 95, 96, 85, 86, 87, 88, 89, 90)) and (($row['weekday'] == '6' or $row['weekday'] == '7')) or ($row['weekday'] != '6' and $row['weekday'] != '7') or (($row['weekday'] == '6' or $row['weekday'] == '7') and $row['hour'] > 0 and in_array($row['status'], array(5, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96))))):
                            $br_sati = $row['hour'];
                            ?>
                            <b><?php echo _nameHRstatus($row['status']); ?></b><br/>
                        <?php else: $br_sati = 0; ?>
                            <b>Redovni rad</b><br/>
                        <?php endif; ?>
                        <?php echo __('Broj sati'); ?> <b><?php echo $br_sati; ?></b>
                        <?php
                        if ($row['status_pre'] != '' and $row['status_pre'] != '0') {
                            ?>
                            <br/><b><?php echo _nameHRstatus($row['status_pre']); ?></b><br/>
                            <?php echo __('Broj sati'); ?> <b><?php echo $row['hour_pre'] + 0; ?></b>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6 text-right">
                        <label class="radio">
                            <input type="radio" name="status" value="1">
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

                <hr/>

                <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                            class="ion-ios-download-outline"></i></button>


            </form>

            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
            <script>
                $(document).ready(function () {
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
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
        }
        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
