<?php
require_once '../../../configuration.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Certifikati'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="certifikat-add"/>


            <label><?php echo __('Naziv završene edukacije / treninga:'); ?></label>
            <div id="certifikat">
                <input type="text" name="certifikat" class="form-control"
                       placeholder="<?php echo __('Naziv završene edukacije / treninga'); ?>"
                       title="Unesite naziv završene edukacije / treninga!" required><br/>
            </div>

            <label><?php echo __('Naziv institucije:'); ?></label>
            <div id="institucija">
                <input type="text" name="institucija" class="form-control"
                       placeholder="<?php echo __('Naziv institucije'); ?>" title="Unesite naziv institucije!" required><br/>
            </div>

            <label><?php echo __('Vrsta edukacije:'); ?></label>
            <div id="vrsta">
                <select id="vrsta" name="vrsta" class="rcorners1" class="form-control">
                    <?php echo _optionEducationType(0); ?>
                </select><br/><br/>
            </div>


            <label><?php echo __('Datum završetka:'); ?></label>
            <div id="dt">
                <input type="text" name="zavrsetak" class="form-control" id="zavrsetak" placeholder="dd/mm/yyyy">
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
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>


        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <script>
            $(function () {
                var today = new Date();
                var startDate = new Date();
                $('#zavrsetak').datepicker({
                    todayBtn: "linked",
                    format: 'dd/mm/yyyy',
                    language: 'bs'
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
