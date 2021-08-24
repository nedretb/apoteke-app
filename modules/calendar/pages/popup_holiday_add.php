<?php
require_once '../../../configuration.php';
$_user = _user(_decrypt($_SESSION['SESSION_USER']));
?>


<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Praznik'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>

    <?php
    $get2 = $db->query("SELECT distinct Stream_description as Stream_description FROM  " . $portal_users . "  where Stream_description<>'' and Stream_description is not null");
    $streams = $get2->fetchAll();

    $get2 = $db->query("SELECT distinct Team_description FROM  " . $portal_users . "  where Team_description<>'' and Team_description is not null");
    $teams = $get2->fetchAll();

    $streams_js = json_encode($streams);
    ?>

    <div class="content clear">

        <div id="res"></div>

        <form id="popup_form" method="post" class="filijala_popup">

            <input type="hidden" name="request" value="holiday-add"/>


            <style>
                .select2-container {
                    z-index: 150000 !important;
                }

                [aria-labelledby="select2-naz_praznik-container"] {
                    display: none !important;
                }

                [aria-labelledby="select2-pod_primjene-container"] {
                    display: none !important;
                }

                [aria-labelledby="select2-pomicni-container"] {
                    display: none !important;
                }

                .orgjed.collapsable .select2-container {
                    width: 100% !important;
                }
            </style>

            <div class="filijala-agencija collapsable">
                <br/>

                <label><?php echo __('Organizaciona jedinica:'); ?></label>
                <select style="padding:0px !important; " name="orgjed[]" id="orgjed"
                        class="form-control orgajed" multiple="multiple" data-placeholder="Odaberi">
                    <?php

                    $d = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[systematization]");
                    $d->execute();
                    $f = $d->fetchAll();

                    foreach ($f as $k => $v) {

                        ?>
                        <option value="<?php echo $v['id']; ?>"><?php echo $v['s_title']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>


            <div class="centrala collapsable" style="display:none;">
                <br/>
                <label><?php echo __('Drugo:'); ?></label>
                <select style="padding:0px !important; " name="department_name_free" required id="department_name_free"
                        class="form-control resetAgency">
                    <?php echo _OptionSlobodanUnos(''); ?>
                </select>
            </div>


            <br/>


            <label><?php echo __('Datum praznika:'); ?></label>
            <div id="dt1">
                <input autocomplete="off" type="text" name="date" class="form-control" id="date1" required placeholder="dd.mm.yyyy">
            </div>
            <br/>

            <label><?php echo __('Ime praznika:'); ?></label>
            <div id="holiday_name">
                <input type="text" name="holiday_name" class="form-control" maxlength="50" required spellcheck="false"
                       placeholder="<?php echo __('Ime praznika'); ?>" <br/><br/>
            </div>

            <select style="padding:0px !important; display: none;" name="pomicni" id="pomicni" required class="form-control">
                <?php echo _OptionPomicni('0'); ?>
            </select><br/>

            <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i
                        class="ion-ios-download-outline"></i></button>


        </form>


        <!-- Bootstrap -->

        <link href="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.css" rel="stylesheet">
        <link href="<?php echo $_pluginUrl; ?>/select2/select2.min.css" rel="stylesheet">
        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/select2/select2.min.js"></script>


        <!-- Bootstrap -->
        <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>

        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <!-- jQuery confirm -->
        <script src="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.js"></script>


        <script>

            $(".resetAgency").change(function () {
                thisval = $(this).val();


                if (thisval != '' || thisval == undefined) {
                    $("#department_name option:selected").removeAttr("selected");
                    $("#department_name option[value='']").remove();
                    $("#department_name").select2("destroy");
                    $("#department_name").select2();
                } else {

                }
            });


            $(".filijala").select2();

            $(".orgajed").select2();

            $('#department_name option').mousedown(function (e) {
                e.preventDefault();
                var originalScrollTop = $(this).parent().scrollTop();
                console.log(originalScrollTop);
                $(this).prop('selected', $(this).prop('selected') ? false : true);
                var self = this;
                $(this).parent().focus();
                setTimeout(function () {
                    $(self).parent().scrollTop(originalScrollTop);
                }, 0);

                return false;
            });

            $(function () {
                var today = new Date();
                var startDate = new Date();
                $('#date1').datepicker({
                    todayBtn: "linked",
                    format: 'dd.mm.yyyy',
                    language: 'bs'
                    //startDate: startDate,
                    //endDate: new Date('2017/12/31')
                });


                $(document).ready(function () {
                    $('.dialog-loader').hide();
                    //$("#department_name").chosen();
                });

            });
            $("#popup_form").validate({


                focusCleanup: true,
                submitHandler: function (form) {
                    $.confirm({
                        title: 'Potvrdite',
                        //titleClass:'raiff-blue',
                        content: 'Da li ste sigurni da želite unijeti novi praznik (unos će uticati na kreirane satnice)?',
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
                                            $("#popup_form")[0].reset();
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

            function changeHandler(val) {
                if (Number(val.value) < 5) {
                    val.value = 5
                }
                if (Number(val.value) > 30) {
                    val.value = 30
                }
            }

            $('#department_name').on('change', function () {
                if ($("#department_name option:selected").val() != '')
                    $("#department_name_free").val("");
                $("#department_name_free").removeAttr("required");
                $("#department_name").attr("required", "required");
            })

            $("#department_name_free").change(function () {
                if ($("#department_name_free option:selected").val() != '')
                    $("#department_name").val("");
                $("#department_name_free").attr("required", "required");
                $("#department_name").removeAttr("required");
            });


            $("#field_type").change(function () {
                $(".collapsable").css('display', 'none');

                $("." + $(this).val()).css('display', 'block');
            });


        </script>


    </div>
    <div class="dialog-loader"><i></i></div>
</section>
