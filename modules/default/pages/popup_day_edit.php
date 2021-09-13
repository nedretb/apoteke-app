<?php
require_once '../../../configuration.php';
include_once $root . '/modules/default/functions.php';


?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Registruj odsustvo'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php

        $_user = _user(_decrypt($_SESSION['SESSION_USER']));
        $get = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='" . $_GET['id'] . "'");
        $getrow = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE id='" . $_GET['id'] . "'");
        if ($get->rowCount() < 0) {
            $row = $getrow->fetch();

            if ($row['weekday'] == '6' or $row['weekday'] == '7')
                $br_sati = 0;
            else {
                if ($row['hour'] == $_user['br_sati'])
                    $br_sati = $_user['br_sati'];
                else
                    $br_sati = $row['hour'];
            }

            ?>

            <div id="res"></div>
            <form id="popup_form" method="post">

                <input type="hidden" name="request" value="day-edit"/>

                <input type="hidden" name="request_id" value="<?php echo $_GET['id']; ?>"/>

                <div class="row">
                    <div class="col-sm-3">
                        <label><?php echo __('Dan'); ?></label>
                        <input type="text" name="day" class="form-control" readonly value="<?php echo $row['day']; ?>"/>
                    </div>
                    <div class="col-sm-4">
                        <label><?php echo __('Broj sati'); ?></label>
                        <input type="number" name="hour" id="hour" value="<?php echo $br_sati; ?>" min="0" max="24"
                               class="form-control" readonly>
                    </div>
                    <div class="col-sm-5">
                        <label><?php echo __('Status'); ?></label>
                        <select style="padding:0px !important; " name="status" id="status" class="form-control"
                                required>
                            <?php if ($_user['B_1_regions_description'] == 'Kontakt centar') {
                                echo _optionHRstatusLevelKontakCentarRadnik($row['status']);
                            } else {
                                echo _optionHRstatusLevel3($row['status']);
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="row" <?php if ($_user['B_1_regions_description'] != 'Kontakt centar' or $_user['B_1_regions_description'] == 'Kontakt centar') {
                    echo 'style="display:none;"';
                } ?>>
                    <div class="col-sm-2">
                        <!-- <label><?php echo __('Dan'); ?></label>
          <input type="text" name="day" class="form-control" value="<?php echo $row['day']; ?>" readonly/>-->
                    </div>
                    <div class="col-sm-5">
                        <label><?php echo __('Broj sati pre.'); ?></label>
                        <input type="number" id="hour_pre" name="hour_pre" value="<?php echo $row['hour_pre']; ?>"
                               min="0" max="24" class="form-control" readonly>
                    </div>
                    <div class="col-sm-5">
                        <label><?php echo __('Status pre.'); ?></label>
                        <select style="padding:0px !important; " name="status_pre" id="status_pre" class="form-control">
                            <?php echo _optionHRstatusPreKontaktCentar($row['status_pre']); ?>
                        </select>
                    </div>
                </div>

                <hr/>

                <div class="row" id="comment_row">
                    <textarea name="komentar" id="komentar" maxlength="250" spellcheck="false" class="form-control"
                              style="width: -webkit-fill-available"
                              placeholder="Molimo upišite komentar."></textarea><br/>
                </div>

                </br>

                <button type="submit" id='spasi_registraciju' class="btn btn-red pull-right"><?php echo __('Spasi!'); ?>
                    <i class="ion-ios-download-outline"></i></button>
                <button id="nalog" onclick=(alemfunction(event,<?php echo $_GET['id']; ?>))
                        class="btn btn-red pull-right"><?php echo __('Otvori nalog!'); ?> <i
                            class="ion-ios-download-outline"></i></button>
                <button style="display:none;" onclick=(alemfunction(event,<?php echo $_GET['id']; ?>)) id='sa_troskom'
                        class="btn btn-red pull-right"><?php echo __('Otvori nalog/sa troškovima'); ?> <i
                            class="ion-ios-download-outline"></i></button>
                <button style="display:none;margin-top:10px;" type="submit" id='bez_troska'
                        class="btn btn-red pull-right"><?php echo __('Registruj odsustvo/bez troškova'); ?> <i
                            class="ion-ios-download-outline"></i></button>
            </form>

            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
            <script>
                $(document).ready(function () {
                    $('.dialog-loader').hide();
                    $('#nalog').hide();
                    $('#sa_troskom').hide();
                    $('#bez_troska').hide();
                    if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                        $('#comment_row').show();
                        $('#komentar').prop('required', true);
                    } else if ($("#status option:selected").text().indexOf('Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').show();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                    } else if ($("#status option:selected").text().indexOf('Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').show();
                        $('#bez_troska').show();
                    } else {
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#nalog').hide();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                    }


                });
                $("#popup_form").validate({
                    focusCleanup: true,
                    submitHandler: function (form) {
                        $('.dialog-loader').show();
                        $(form).ajaxSubmit({
                            url: "<?php echo $url . '/modules/core/ajax/satnice.php'; ?>",
                            type: "post",
                            success: function (data) {
                                $("#res").html(data);
                                $('.dialog-loader').hide();
                                $('#spasi_registraciju').prop('disabled', true);
                            }
                        });
                    }
                });

                $('#status').on('change', function () {

                    if ($("#status option:selected").text().indexOf('Bolovanje') !== -1) {
                        $('#comment_row').show();
                        $('#komentar').prop('required', true);
                        $('#spasi_registraciju').show();
                        $('#nalog').hide();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                    }

                    //Alem tha king sakrivanje dugmeta Spasi

                    else if ($("#status option:selected").text().indexOf('Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').show();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                    } else if ($("#status option:selected").text().indexOf('Službeni put') !== -1) {
                        $('#spasi_registraciju').hide();
                        $('#nalog').hide();
                        $('#comment_row').hide();
                        $('#komentar').prop('required', false);
                        $('#sa_troskom').show();
                        $('#bez_troska').show();
                    }

                    //
                    else {
                        $('#comment_row').hide();
                        $('#nalog').hide();
                        $('#sa_troskom').hide();
                        $('#bez_troska').hide();
                        $('#komentar').prop('required', false);
                        $('#spasi_registraciju').show();
                    }


                })


                var br_sati = '<?php echo $br_sati; ?>';
                $('#hour').val(br_sati);
                $('#hour_pre').val('0');
                $("#hour").prop('readonly', true);
                $("#hour_pre").prop('readonly', true);
                $('#status_pre').val('');


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
