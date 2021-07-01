<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
    <?php $num_users = _count_user(); ?>
    <div class="content clear">

        <div id="progressbar" style="height:20px;"></div>
        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="year-add-complete"/>


            <select name="year" id="year" class="form-control" required>
                <?php echo _optionYear(0); ?>
            </select><br/>


            <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                        class="ion-ios-download-outline"></i></button>


        </form>

        <link href="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.css" rel="stylesheet">
        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <script>
            $(document).ready(function () {
                $('.dialog-loader').hide();
            });
            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function (form) {
                    console.log('pokrenuto');
                    //$('.dialog-loader').show();
                    var myVar = setInterval(myTimer, 3000);
                    $("#progressbar").progressbar({
                        value: 0,
                        max: <?php echo $num_users; ?>
                    });
                    $(form).ajaxSubmit({
                        url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                        type: "post",
                        success: function (data) {
                            clearInterval(myVar);
                            $("#popup_form")[0].reset();
                            $("#res").html(data);
                            $('.dialog-loader').hide();
                        }
                    });
                }
            });

            function myTimer() {
                var year_send = $("#year").val();
                $.get("<?php echo $url . '/modules/default/ajax_get.php'; ?>", {
                        request: "check-month-add-new",
                        year: year_send
                    },
                    function (returnedData) {
                        $("#res").html(returnedData + '/' + <?php echo $num_users; ?>);
                        $("#progressbar").progressbar("option", "value", parseInt(returnedData));
                    });


            }
        </script>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
