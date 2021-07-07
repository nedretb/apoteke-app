<?php
require_once '../../../configuration.php';
include_once $root.'/modules/default/functions.php';

?>

<div class="header">
    <a class="btn close" data-widget="close-ajax" data-siht="true" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Ažuriraj satnice'); ?></span></h4>
</div>

<section>
    <div class="content clear">
        <?php

        $get_year  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$_GET['year']."'");
        $get_month = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month] WHERE id='".$_GET['month']."'");

        $get_y  = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_year] WHERE id='".$_GET['year']."'");
        $get_m = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_month] WHERE id='".$_GET['month']."'");

        $result = $get_y->fetch();
        $total=$result[0];
        $result2 = $get_m->fetch();
        $total2=$result2[0];


        $get = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] ");
        $getrow = $db->query("SELECT month_id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day]");
        if($get->rowCount()<0){
        $row = $getrow->fetch();
        $year  = $get_year->fetch();
        $month = $get_month->fetch();

        $getrow_user = $db->query("SELECT user_id, month_id FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] WHERE year_id ='".$_GET['year']."'");
        $row_user = $getrow_user->fetch();

        $_user1 = _user($row_user['user_id']);

        ?>



        <div id="res"></div>
        <form id="popup_form" method="post">

            <input type="hidden" name="request" value="parent-day-add_apsolute"/>
            <input type="hidden" name="get_month" value="<?php  echo $_GET['month'];?>"/>
            <input type="hidden" name="get_year" value="<?php  echo $_GET['year'];?>"/>


            <div class="row">
                <div class="col-sm-6">
                    <label><?php echo __('Od'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateFrom" class="form-control"  value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>" id="dateOD" placeholder="dd/mm/yyyy" title="" required>
                    </div><br/>
                </div>


                <div class="col-sm-6">
                    <label><?php echo __('Do'); ?></label>
                    <div id="dt">
                        <input type="text" name="dateTo" class="form-control"   value="01.<?php echo $_GET['month']; ?>.<?php echo $year['year']; ?>" id="dateDO" placeholder="dd/mm/yyyy" title="" required>
                    </div><br/>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6">
                    <label ><?php echo __('Broj sati'); ?></label>
                    <?php $get_hourq = $db->query("select count(hour) as c, hour from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
            where year_id = ".$_GET['year']." and month_id = ".$_GET['month']."
            group by hour 
            order by c desc ");
                    $get_hour = $get_hourq->fetch();
                    ?>
                    <input type="number" name="hour" value="<?php echo $get_hour['hour']; ?>" min="0" max="24" class="form-control" readonly>
                </div>


                <div class="col-sm-6">
                    <label><?php echo __('Status'); ?></label>
                    <select style="padding:0px !important; " name="status" id = "status" class="form-control" required>
                        <?php echo _optionHRstatus($row['status']); ?>
                    </select>
                </div>
            </div>
            </br>

            <div class="row" id="comment_row">
                <textarea name="komentar" id="komentar" maxlength="250" spellcheck="false" class="form-control" style="width: -webkit-fill-available" placeholder="Molimo upišite komentar."></textarea><br/>
            </div>


            <button style="display:none;" onclick=(alemfunction(event)) id ='sa_troskom' class="btn btn-red pull-right"><?php echo __('Otvori nalog/sa troškovima'); ?> <i class="ion-ios-download-outline"></i></button>
            <button style="display:none;margin-top:10px;" type="submit" id ='bez_troska' class="btn btn-red pull-right"><?php echo __('Registruj odstustvo/bez troškova'); ?> <i class="ion-ios-download-outline"></i></button>

            <button type="submit" id='spasi_registraciju' class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>
            <button id="nalog" onclick=(alemfunction(event)) class="btn btn-red pull-right"><?php echo __('Otvori nalog!'); ?> <i class="ion-ios-download-outline"></i></button>
            <?php

            }else{
                echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
            }
            ?>
        </form>

        <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
        <!-- Bootstrap datepicker -->
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>
        <script>
            $( document ).ready(function(){
                var today = new Date();
                var startDate = new Date();
                var startDate = new Date('<?php echo $year['year']; ?>/'+'<?php echo $_GET['month']; ?>'+'/01');
                //var date = new Date('2017/0'+'<?php echo $_GET['month']; ?>'+'/01');
                var date = new Date('<?php echo $year['year']; ?>/05/01');
                var year = '<?php echo $year['year']; ?>';
                $('#dateOD').datepicker({
                    //todayBtn: "linked",
                    defaultViewDate: new Date('<?php echo $year['year']; ?>/05/01'),
                    format: 'dd.mm.yyyy',
                    language: 'bs',
                    startDate: startDate,
                    endDate: new Date(year + '/12/31')

                });

                $('#dateDO').datepicker({
                    //todayBtn: "linked",
                    defaultViewDate: new Date('<?php echo $year['year']; ?>/05/01'),
                    format: 'dd.mm.yyyy',
                    language: 'bs',
                    startDate: startDate,
                    endDate: new Date(year + '/12/31')

                });




                $("#dateOD").on('change', function (e) {
                    console.log($("#dateOD").val());
                    $("#dateDO").datepicker("destroy");
                    console.log('destroyed');
                    $('#dateDO').datepicker({
                        //todayBtn: "linked",
                        defaultViewDate: new Date('2017/05/01'),
                        format: 'dd.mm.yyyy',
                        language: 'bs',
                        startDate: $("#dateOD").val()
                        //endDate: new Date(year + '/12/31')

                    });
                    $("#dateDO").datepicker( "setDate" , $("#dateOD").val());

                });
                $('.dialog-loader').hide();
            });
            $('#nalog').hide();

            if ($("#status option:selected").text().indexOf('Bolovanje') !== -1){
                $('#comment_row').show();
                $('#komentar').prop('required',true);
            }
            else if ($("#status option:selected").text().indexOf('1024 Službeni put') !== -1){
                $('#spasi_registraciju').hide();
                $('#nalog').show();
                $('#comment_row').hide();
                $('#komentar').prop('required',false);
            }else if($("#status option:selected").text().indexOf('1024 Službeni put') !== -1){
                $('#spasi_registraciju').hide();
                $('#nalog').hide();
                $('#comment_row').hide();
                $('#komentar').prop('required',false);
                $('#sa_troskom').show();
                $('#bez_troska').show();
            }
            else{
                $('#comment_row').hide();
                $('#komentar').prop('required',false);
            }




            $("#popup_form").validate({
                focusCleanup: true,
                submitHandler: function(form) {
                    $('.dialog-loader').show();


                    var datum_od = $("#dateOD").val();
                    var datum_do = $("#dateDO").val();

                    split_datumod = datum_od.split(".");
                    split_datumdo = datum_do.split(".");

                    if(parseInt(split_datumod[2]) != parseInt(split_datumdo[2])){
                        $("#dateDO").val('31.12.'+split_datumod[2]);
                    }

                    $(form).ajaxSubmit({
                        url:"<?php echo $url.'/modules/core/ajax/satnice.php'; ?>",
                        type:"post",
                        success: function(data){

                            if(parseInt(split_datumod[2]) != parseInt(split_datumdo[2])){
                                $("#dateOD").val('01.01.'+split_datumdo[2]);
                                $("#dateDO").val(datum_do);
                                $('[name="get_year"]').val('<?php

                                    echo getYearId($_GET['year'], $_user1['user_id'], 'next', true);

                                    ?>');

                                $(form).ajaxSubmit({
                                    url:"<?php echo $url.'/modules/core/ajax/satnice.php'; ?>",
                                    type:"post",
                                    success: function(data){

                                        console.log('succ');
                                        $("#dateOD").val(datum_od);
                                        $("#dateDO").val(datum_do);
                                        $("#res").html(data);
                                        $('.dialog-loader').hide();
                                        $('#spasi_registraciju').prop('disabled', true);
                                    }
                                });



                            } else {
                                $("#res").html(data);
                                $('.dialog-loader').hide();
                                $('#spasi_registraciju').prop('disabled', true);
                            }
                        }
                    });





                }
            });

            /*
            $("#popup_form").validate({
              focusCleanup: true,
                  submitHandler: function(form) {
                    $('.dialog-loader').show();
                    $(form).ajaxSubmit({
                      url:"<?php echo $url.'/modules/admin_manager_hourly_rate/ajax.php'; ?>",
          type:"post",
          success: function(data){
            $("#res").html(data);
              $('.dialog-loader').hide();
          }
        });
      }
});*/






            $('#status').on('change', function() {

                if ($("#status option:selected").text().indexOf('Bolovanje') !== -1){
                    $('#comment_row').show();
                    $('#komentar').prop('required',true);
                    $('#nalog').hide();
                    $('#spasi_registraciju').show();
                }

                //Alem tha king sakrivanje dugmeta Spasi

                else if ($("#status option:selected").text().indexOf('1024 Službeni put') !== -1){
                    $('#spasi_registraciju').hide();
                    $('#nalog').show();
                    $('#sa_troskom').hide();
                    $('#bez_troska').hide();
                    $('#comment_row').hide();
                    $('#komentar').prop('required',false);
                }else if($("#status option:selected").text().indexOf('1024 Službeni put') !== -1){
                    $('#spasi_registraciju').hide();
                    $('#nalog').hide();
                    $('#comment_row').hide();
                    $('#komentar').prop('required',false);
                    $('#sa_troskom').show();
                    $('#bez_troska').show();
                }

                //
                else{
                    $('#comment_row').hide();
                    $('#komentar').prop('required',false);
                    $('#sa_troskom').hide();
                    $('#bez_troska').hide();
                    $('#nalog').hide();
                    $('#spasi_registraciju').show();
                }
            })

            function alemfunction(event,id){
                event.preventDefault();
                let h= document.getElementById("status");
                let status = h.options[h.selectedIndex].value;
                let dateOD= (document.getElementById("dateOD")).value;
                let dateDO= (document.getElementById("dateDO")).value;
                let get_year= $("[name='get_year']").val();

                window.location.href='?m=default&p=novi_nalog&get_year='+get_year+'&status='+status+'&dateOD='+dateOD+'&dateDO='+dateDO;
            }
        </script>

        <?php

        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>
