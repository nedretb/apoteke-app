<?php
require_once '../../../configuration.php';
include_once $root . '/modules/settings/functions.php';
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Moji Zahtjevi'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="request-add"/>

            <label><?php echo __('Period od-do:'); ?></label>

            <div class="input-group input-daterange">
                <input type="text" name="from" class="form-control" required>
                <span class="input-group-addon"><?php echo __('do'); ?></span>
                <input type="text" name="to" class="form-control" required>
            </div>

            <br/>

            <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                        class="ion-ios-download-outline"></i></button>


        </form>

        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

        <!-- Bootstrap -->
        <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <script>
            $(function () {
                var today = new Date();
                var startDate = new Date();
                $('.input-daterange').datepicker({
                    todayBtn: "linked",
                    format: 'dd/mm/yyyy',
                    startDate: startDate
                });
                $(document).ready(function () {
                    $('.dialog-loader').hide();
                });
            });
            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function (form) {
                    $('.dialog-loader').show();
                    $(form).ajaxSubmit({
                        url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                        type: "post",
                        success: function (data) {
                            $("#popup_form")[0].reset();
                            $("#res").html(data);
                            $('.dialog-loader').hide();
                        }
                    });
                }
            });
        </script>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
