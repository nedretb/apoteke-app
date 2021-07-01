<?php
  require_once '../../../configuration.php';
  include_once $root.'/modules/default/functions.php';

 ?>

<div class="header">
  <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
  <h4><span><?php echo __('Ažuriraj satnice'); ?></span></h4>
</div>

<section>
  <div class="content clear">
  <?php

  $get_year  = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE id='".$_GET['year']."'");
  $get_month = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE id='".$_GET['month']."'");

  $get_y  = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_year."  WHERE id='".$_GET['year']."'");
  $get_m = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_month."  WHERE id='".$_GET['month']."'");

     $result = $get_y->fetch();
     $total=$result[0];
     $result2 = $get_m->fetch();
     $total2=$result2[0];

     
    $get = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_day."  ");
    $getrow = $db->query("SELECT month_id FROM  ".$portal_hourlyrate_day." ");
      if($get->rowCount()<0){
        $row = $getrow->fetch();
        $year  = $get_year->fetch();
        $month = $get_month->fetch();

    ?>

   

    <div id="res"></div>
    <form id="popup_form" method="post">

      <input type="hidden" name="request" value="parent-day-add"/>
      <input type="hidden" name="get_month" value="<?php  echo $_GET['month'];?>"/>
      <input type="hidden" name="get_year" value="<?php  echo $_GET['year'];?>"/>


      <div class="row">
        <div class="col-sm-6">
          <label><?php echo __('Od'); ?></label>
          <select id="FromDay" style="padding:0px !important;" name="FromDay" class="form-control" required>
          <?php echo _optionDay('2016',$row['month_id'],$row['day']); ?>
          </select><br/>
        </div>
      

        <div class="col-sm-6">
          <label><?php echo __('Do'); ?></label>
          <select  style="padding:0px !important;" name="ToDay" class="form-control" required>
          <?php echo _optionDay('2016',$row['month_id'],$row['day']); ?>  
          </select><br/>
        </div>
      </div>


       <div class="row">
        <div class="col-sm-6">
          <label ><?php echo __('Broj sati'); ?></label>
          <input type="number" name="hour" value="8" min="0" max="24" class="form-control" readonly>
        </div>


        <div class="col-sm-6">
          <label><?php echo __('Status'); ?></label>
          <select style="padding:0px !important; " name="status" class="form-control" required>
            <?php echo _optionHRstatus($row['status']); ?>
          </select>
        </div>
      </div>
</br>
      <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>

<?php 

  }else{
        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
      }
?>
    </form>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
    <script>
      $( document ).ready(function(){
        $('.dialog-loader').hide();
      });
      $("#popup_form").validate({
        focusCleanup: true,
            submitHandler: function(form) {
              $('.dialog-loader').show();
              $(form).ajaxSubmit({
                url:"<?php echo $url.'/modules/default/ajax.php'; ?>",
                type:"post",
                success: function(data){
                  $("#res").html(data);
                    $('.dialog-loader').hide();
                }
              });
            }
      });
    </script>

     <?php
    
     ?>

  </div>
  <div class="dialog-loader"><i></i></div>
</section>
