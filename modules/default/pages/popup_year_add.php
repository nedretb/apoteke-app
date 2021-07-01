<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="year-add"/>


            <select name="year" class="form-control" required>
                <?php echo _optionYear(0); ?>
            </select><br/>


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
