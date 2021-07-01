<?php
require_once '../../../configuration.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Zamjenski cilj'); ?></span></h4>
</div>


<section>
    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post">

            <?php $task_type = $_GET['task_type'];
            $ponder_sum = $_GET['ponder_sum'];


            ?>

            <input type="hidden" name="request" value="tasks-add"/>
            <input type="hidden" name="task_type" value="<?php echo $task_type ?>"/>


            <label><?php echo __('Cilj:'); ?></label>
            <div id="task">
                <input type="text" style="width:300px;" name="task_name" class="form-control"
                       placeholder="<?php echo __('Naziv Cilja'); ?>" spellcheck="false" required><br/>
            </div>

            <label><?php echo 'Opis cilja:'; ?></label>
            <div id="task">
                <textarea maxlength="350" rows="3" cols="50" name="task_description" placeholder="Unesite opis cilja..."
                          title="Max 350 karaktera" spellcheck="false"></textarea>
            </div>

            <?php if ($task_type != 2) { ?>
                <label><?php echo 'KPI:'; ?></label>
                <div id="kpi">
                    <textarea maxlength="350" rows="3" cols="30" name="task_kpi" placeholder="Unesite KPI..."
                              title="Max 350 karaktera" spellcheck="false"></textarea>
                </div>
            <?php } ?>

            <?php if ($task_type != 2) { ?>
                <label><?php echo 'Ponder:'; ?></label>
                <div id="ponder1" class="" style="width:160px;display: -webkit-box;">
                    <input style="width:90px;height:30px;margin-right:10px;" type="text" id="ponder"
                           onkeypress='return event.charCode >= 48 && event.charCode <= 57' name="ponder"
                           title="Min - 5%, Max - 30%" class="form-control" placeholder="<?php echo __('Ponder'); ?>"
                           required><label style="margin-right:35px;">%</label><label>Preostalo pondera : </label>
                    <div id="preostalo_ponder"></div>
                    <label>%</label><br/>
                </div>

            <?php } ?>

            <hr/>

            <?php if ($task_type != 2) { ?>
                <label>Komentar</label><br/>
            <?php } else { ?>
                <label>Razvojna aktivnost</label><br/>
            <?php } ?>
            <textarea rows="3" cols="50" name="comment" spellcheck="false"></textarea><br/>


            <?php echo __('Rok'); ?><br/>

            <div id="dt">
                <input type="text" name="final_date" class="form-control" style="width:140px;" id="date"
                       placeholder="dd/mm/yyyy" title="Maximalni datum 31.12.2017" required>
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
                $('#date').datepicker({
                    todayBtn: "linked",
                    format: 'dd/mm/yyyy',
                    language: 'bs',
                    startDate: startDate,
                    endDate: new Date('2017/12/31')
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

            var ponder_sum = <?php echo $ponder_sum; ?>;
            $("#preostalo_ponder").html(100 - ponder_sum);

            function changeHandler(val) {
                if (Number(val.value) < 5) {
                    val.value = 5
                }
                if (Number(val.value) > 30) {
                    val.value = 30
                }


                $("#preostalo_ponder").html(100 - ponder_sum);

            }

            $('#ponder').on('change', function () {
                console.log(this.value);


                if (Number(this.value) < 5) {
                    this.value = 5
                }
                if (Number(this.value) > 30) {
                    this.value = 30
                }


                $("#preostalo_ponder").html(100 - ponder_sum - this.value);


            })


            $(document).ready(function () {
                $('input[type=radio][name=task_type]').change(function () {
                    if (this.value == '2') {
                        $("#ponder").prop('required', false);
                        $("#ponder").prop('disabled', true);
                    } else {
                        $("#ponder").prop('required', true);
                        $("#ponder").prop('disabled', false);
                    }
                });
            });


        </script>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
