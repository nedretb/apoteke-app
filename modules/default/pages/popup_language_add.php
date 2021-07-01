<?php
require_once '../../../configuration.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Jezici'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="language-add"/>


            <label><?php echo __('Jezik:'); ?></label>
            <div id="language">
                <select id="language" name="language" class="rcorners1" class="form-control" title="Odaberite jezik!"
                        required>
                    <?php echo _optionLang(0); ?>
                </select><br/>
            </div>

            <label><?php echo __('Razumijevanje:'); ?></label>
            <div id="understanding">
                <select id="understanding" name="understanding" class="rcorners1" class="form-control">
                    <?php echo _optionLanguageSkill(0); ?>
                </select><br/>
            </div>

            <label><?php echo __('Govor:'); ?></label>
            <div id="speech">
                <select id="speech" name="speech" class="rcorners1" class="form-control">
                    <?php echo _optionLanguageSkill(0); ?>
                </select><br/>
            </div>

            <label><?php echo __('Pisanje:'); ?></label>
            <div id="writing">
                <select id="writing" name="writing" class="rcorners1" class="form-control">
                    <?php echo _optionLanguageSkill(0); ?>
                </select><br/>
            </div>


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
                $('#date_from').datepicker({
                    todayBtn: "linked",
                    format: 'yyyy/mm/dd',
                    //startDate: startDate,
                    //endDate: new Date('2017/12/31')
                });

                $('#date_to').datepicker({
                    todayBtn: "linked",
                    format: 'yyyy/mm/dd',
                    //startDate: startDate,
                    //endDate: new Date('2017/12/31')
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

            function changeHandler(val) {
                if (Number(val.value) < 5) {
                    val.value = 5
                }
                if (Number(val.value) > 30) {
                    val.value = 30
                }
            }


        </script>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
