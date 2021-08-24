<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';


?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Ažuriraj praznik'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php
        $get = $db->query("SELECT count(*) FROM  " . $portal_holidays_per_department . "  WHERE id='" . $_GET['id'] . "'");
        $getrow = $db->query("SELECT * FROM  " . $portal_holidays_per_department . "  WHERE id='" . $_GET['id'] . "'");
        if ($get->rowCount() < 0) {
            $row = $getrow->fetch();

            ?>

            <div id="res"></div>
            <form id="popup_form" method="post">

                <input type="hidden" name="request" value="holiday-edit"/>

                <input type="hidden" name="old_date" value="<?php echo date('d.m.Y', strtotime($row['date'])); ?>"/>

                <input type="hidden" name="request_id" value="<?php echo $_GET['id']; ?>"/>

                <?php
                $dep_name = $row['department name'];
                if ($row['department name'] == "") {
                    $dep_name = "Svi";
                }
                ?>

                <label><?php echo __('Organizaciona jedinica:'); ?></label>
                <input type="text" value="<?php echo $dep_name; ?>" readonly class="form-control"/>
                <input type="text" name="department_name" class="form-control" style="display: none;" readonly
                       value="<?php echo $row['department name']; ?>"/>


                <label><?php echo __('Datum praznika:'); ?></label>
                <div id="dt">
                    <input type="text" name="date" class="form-control" class="datepickery" id="date2"
                           placeholder="dd.mm.yyyy" value="<?php echo date('d.m.Y', strtotime($row['date'])); ?>">
                </div>


                <label><?php echo __('Ime praznika:'); ?></label>
                <div id="holiday_name">
                    <input type="text" name="holiday_name" class="form-control" spellcheck="false"
                           placeholder="<?php echo __('Ime praznika'); ?>" value="<?php echo $row['holiday_name']; ?>"
                    <br/><br/>
                </div>

                <label style="display: none;"><?php echo __('Pomični:'); ?></label>
                <select style="display:none; padding:0px !important; " name="pomicni" id="pomicni" class="form-control">
                    <?php echo _OptionPomicni($row['Pomicni']); ?>
                </select><br/>

                <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                            class="ion-ios-download-outline"></i></button>

            </form>

            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
            <!-- Bootstrap datepicker -->
            <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>
            <!-- jQuery confirm -->
            <script src="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.js"></script>
            <script>


                $(function () {

                    var today = new Date();
                    var startDate = new Date();

                    $('#date2').datepicker({
                        todayBtn: "linked",
                        format: 'dd.mm.yyyy',
                        language: 'bs'
                    });
                });


                $(document).ready(function () {


                    var today = new Date();
                    var startDate = new Date();

                    $('#date').datepicker({
                        todayBtn: "linked",
                        format: 'dd.mm.yyyy',
                        language: 'bs',
                        //startDate: startDate,
                        //endDate: new Date(year + '/12/31')
                    });


                    $('.dialog-loader').hide();
                    if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                        $('#comment_row').show();
                        $('#komentar').prop('required', true);
                    } else {
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                    }
                });
                $("#popup_form").validate({
                    focusCleanup: true,
                    submitHandler: function (form) {

                        $.confirm({
                            title: 'Potvrdite',
                            //titleClass:'raiff-blue',
                            content: 'Da li ste sigurni da želite ažurirati parametre praznika (ažuriranje ce uticati na kreirane satnice)?',
                            //contentClass:'raiff-blue',
                            buttons: {

                                da: {
                                    text: 'Da',
                                    btnClass: 'btn-red',
                                    keys: ['enter', 'shift'],
                                    action: function () {
                                        $('.dialog-loader').show();
                                        $(form).ajaxSubmit({
                                            url: "<?php echo $url . '/modules/calendar/ajax.php'; ?>",
                                            type: "post",
                                            success: function (data) {
                                                $("#res").html(data);
                                                $('.dialog-loader').hide();
                                            }
                                        });
                                    }
                                },
                                ne: {
                                    text: 'Ne',
                                    btnClass: 'btn-red',
                                    action: function () {
                                        return;
                                    }
                                }
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
                })

            </script>

            <?php
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
        }
        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
