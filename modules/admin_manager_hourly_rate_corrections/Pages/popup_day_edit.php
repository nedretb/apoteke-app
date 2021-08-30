<?php
require_once '../../../configuration.php';
//include_once $root . '/modules/admin_manager_hourly_rate/functions.php';

?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-siht="true" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Ažuriraj satnice'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php


        $get = $db->query("SELECT count(*) FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $_GET['id'] . "'");
        $getrow = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $_GET['id'] . "'");
        if ($get->rowCount() < 0) {
            $row = $getrow->fetch();

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $_user1 = _user($row['user_id']);

            if ($row['weekday'] == '6' or $row['weekday'] == '7') {
                if ($row['hour'] == 0)
                    $br_sati = 0;
//                    $br_sati = $_user1['br_sati'];
                else
                    $br_sati = $row['hour'];
            } else {
                if ($row['hour'] == $_user1['br_sati'])
                    $br_sati = $_user1['br_sati'];
                else
                    $br_sati = $row['hour'];
            }

            ?>

            <div id="res"></div>
            <form id="popup_form" method="post">

                <input type="hidden" name="request" value="day-edit"/>

                <input type="hidden" name="request_id" value="<?php echo $_GET['id']; ?>"/>
                <input type="hidden" name="try" value="<?php echo 1 ?>"/>


                <div class="row">
                    <div class="col-sm-3">
                        <label><?php echo __('Dan'); ?></label>
                        <input type="text" name="day" class="form-control" value="<?php echo $row['day']; ?>" readonly/>
                    </div>
                    <div class="col-sm-4">
                        <label><?php echo __('Broj sati'); ?></label>
                        <?php $get_hourq = $db->query("select count(hour) as c, hour from  " . $portal_hourlyrate_day . "  
            where id = " . $_GET['id'] . "
            group by hour 
            order by c desc ");
                        $get_hour = $get_hourq->fetch();
                        ?>
                        <input type="number" name="hour" id="hour" value="<?php echo $get_hour['hour']; ?>" min="0"
                               max="24" class="form-control">
                    </div>
                    <div class="col-sm-5">
                        <label><?php echo __('Status'); ?></label>
                        <select style="padding:0px !important; " name="status" id="status" class="form-control"
                                required>
                            <?php
                            echo _optionHRstatus($row['corr_status']); ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <!-- <label><?php echo __('Dan'); ?></label>
          <input type="text" name="day" class="form-control" value="<?php echo $row['day']; ?>" readonly/>-->
                    </div>
                    <div class="col-sm-4">
                        <label style="font-size:12px;"><?php echo __('Broj sati pre.'); ?></label>
                        <input type="text" name="hour_pre" id="hour_pre" value="<?php echo $row['hour_pre']; ?>"
                               class="form-control">
                    </div>
                    <div class="col-sm-5">
                        <label><?php echo __('Status pre.'); ?></label>
                        <select style="padding:0px !important; " name="status_pre" id="status_pre" class="form-control">
                            <?php echo _optionHRstatusPre($row['status_pre']); ?>
                        </select>
                    </div>
                </div>
                </br>

                <div class="row" id="comment_row">
                    <textarea name="komentar" id="komentar" maxlength="250" spellcheck="false" class="form-control"
                              style="width: -webkit-fill-available"
                              placeholder="Molimo upišite komentar."></textarea><br/>
                </div>


                <button type="submit" id='spasi_registraciju' class="btn btn-red pull-right"><?php echo __('Spasi!'); ?>
                    <i class="ion-ios-download-outline"></i></button>
                <button id="nalog" onclick=(alemfunction(event,<?php echo $_GET['id']; ?>))
                        class="btn btn-red pull-right"><?php echo __('Otvori nalog!'); ?> <i
                            class="ion-ios-download-outline"></i></button>
                <button style="display:none;" onclick=(alemfunction(event,<?php echo $_GET['id']; ?>)) id='sa_troskom'
                        class="btn btn-red pull-right"><?php echo __('Otvori nalog/sa troškovima'); ?> <i
                            class="ion-ios-download-outline"></i></button>
                <button style="display:none;margin-top:10px;" type="submit" id='bez_troska'
                        class="btn btn-red pull-right"><?php echo __('Registruj odstustvo/bez troškova'); ?> <i
                            class="ion-ios-download-outline"></i></button>

            </form>


            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
            <script>

                $("#status").change(function () {
                    status = $(this).val();
                    if (status == 5 || (status >= 85 && status <= 90)) {
                        stat_pre = $("#status_pre").val();

                        if (stat_pre != '') {
                            $("#hour_pre").rules("add", {
                                min: 0.1
                            });
                        }
                        $("#status_pre").removeAttr('disabled');
                    } else {
                        $("#status_pre").val('');
                        $("#status_pre").attr('disabled', 'disabled');
                        $("#hour_pre").rules("remove", "min max");
                    }
                });

                $("#status_pre").change(function () {
                    status_prex = $(this).val();
                    status = $("#status").val();
                    if (status == 5 || (status >= 85 && status <= 90)) {
                        if (status_prex != "") {
                            $('#hour_pre').prop('required', true);
                            $("#hour_pre").rules("add", {
                                min: 0.1
                            });

                        } else {
                            $('#hour_pre').prop('required', false);
                            $("#hour_pre").rules("remove", "min max");
                        }
                    } else {
                        $('#hour_pre').prop('required', false);
                        $("#hour_pre").rules("remove", "min max");
                    }
                });

                $(document).ready(function () {
                    $('.dialog-loader').hide();
                    $('#nalog').hide();
                    $('#sa_troskom').hide();
                    $('#bez_troska').hide();
                    if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                        $('#comment_row').show();
                        $('#komentar').prop('required', true);
                    } else if ($("#status option:selected").text().indexOf('SL_1 Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').show();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                    } else if ($("#status option:selected").text().indexOf('SL_2 Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').show();
                        $('#bez_troska').show();
                    } else {
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                        $('#nalog').hide();
                    }
                });
                $("#popup_form").validate({
                    rules: {
                        hour: {
                            digits: false
                        }
                    },
                    focusCleanup: true,
                    submitHandler: function (form) {
                        $('.dialog-loader').show();
                        $(form).ajaxSubmit({
                            url: "<?php echo $url . '/modules/core/ajax/korekcije.php'; ?>",
                            type: "post",
                            success: function (data) {
                                $("#res").html(data);
                                $('.dialog-loader').hide();
                                $('input[name="try"]').val(2);
                            }
                        });
                    }
                });

                $('#status').on('change', function () {

                    if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                        $('#spasi_registraciju').show();
                        $('#comment_row').show();
                        $('#komentar').prop('required', true);
                        $('#nalog').hide();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                    } else if ($("#status option:selected").text().indexOf('SL_1 Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').show();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                    } else if ($("#status option:selected").text().indexOf('SL_2 Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').show();
                        $('#bez_troska').show();
                    } else {
                        $('#spasi_registraciju').show();
                        $('#nalog').hide();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                    }
                    if ($.inArray($("#status option:selected").val(), ['5', '85', '86', '87', '88', '89', '90']) == -1) {
                        var br_sati = '<?php echo $br_sati; ?>';
                        $('#hour').val(br_sati);
                        $('#hour_pre').val('0');
                        $("#hour").prop('readonly', true);
                        $("#hour_pre").prop('readonly', true);
                        $('#status_pre').val('');
                    } else {
                        $("#hour").prop('readonly', false);
                        $("#hour_pre").prop('readonly', false);
                        $("#status_pre").prop('disabled', false);
                    }

                })

                function alemfunction(event, id) {
                    event.preventDefault();
                    let idreq = id;
                    let h = document.getElementById("status");
                    let status = h.options[h.selectedIndex].value;

                    window.location.href = '?m=default&p=novi_nalog&id=' + idreq + '&status=' + status;
                }
            </script>

            <?php
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
        }
        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
