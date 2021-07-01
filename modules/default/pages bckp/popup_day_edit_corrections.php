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
       $_user = _user(_decrypt($_SESSION['SESSION_USER']));
	  
	  $get = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_day."  WHERE id='".$_GET['id']."'");
	  $getrow = $db->query("SELECT * FROM  ".$portal_hourlyrate_day."  WHERE id='".$_GET['id']."'");
      if($get->rowCount()<0){
        $row = $getrow->fetch();
		
		if($row['weekday'] == '6' or $row['weekday'] == '7')
	    $br_sati = 0;
		else{
		if($row['hour']==$_user['br_sati'])
		$br_sati = $_user['br_sati'];
		else
		$br_sati = $row['hour'];
		}

    ?>

		<div id="res"></div>
		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="day-edit_corrections"/>

      <input type="hidden" name="request_id" value="<?php  echo $_GET['id'];?>"/>

      <div class="row">
        <div class="col-sm-3">
          <label><?php echo __('Dan'); ?></label>
          <input type="text" name="day" class="form-control" readonly value="<?php  echo $row['day'];?>"/>
        </div>
        <div class="col-sm-4">
          <label ><?php echo __('Broj sati'); ?></label>
          <input type="number" name="hour" id = "hour" value="<?php echo $br_sati; ?>" min="0" max="24" class="form-control" readonly>
        </div>
        <div class="col-sm-5">
          <label><?php echo __('Status'); ?></label>
          <select style="padding:0px !important; " name="status" id="status" class="form-control" required>
            <?php echo _optionHRstatusLevel3($row['corr_status']); ?>
          </select>
        </div>
      </div>
	    <div class="row" <?php  if($_user['B_1_regions_description']!='Kontakt centar') {echo 'style="display:none;"';} ?>>
        <div class="col-sm-2">
          <!-- <label><?php echo __('Dan'); ?></label>
          <input type="text" name="day" class="form-control" value="<?php  echo $row['day'];?>" readonly/>-->
        </div>
        <div class="col-sm-5">
          <label ><?php echo __('Broj sati pre.'); ?></label>
          <input type="number" id="hour_pre" name="hour_pre" value="<?php echo $row['hour_pre']; ?>" min="0" max="24" class="form-control" readonly >
        </div>
        <div class="col-sm-5">
          <label><?php echo __('Status pre.'); ?></label>
          <select style="padding:0px !important; " name="status_pre" id = "status_pre" class="form-control">
            <?php echo _optionHRstatusPreKontaktCentar($row['status_pre']); ?>
          </select>
        </div>
      </div>
	  
	   <hr/>
	  
	  <div class="row" id="comment_row">
	  <textarea name="komentar" id="komentar" maxlength="250" class="form-control" style="width: -webkit-fill-available" placeholder="Molimo upišite komentar."></textarea><br/>
	  </div>
	  
</br>
      <button id="spasi_registraciju" type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>


		</form>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
		<script>
      $( document ).ready(function(){
        $('.dialog-loader').hide();
		if ($("#status option:selected").text().indexOf('Bolovanje') !== -1){
	$('#comment_row').show();
	$('#komentar').prop('required',true);
	}
	else{
		$('#comment_row').hide();
		$('#komentar').prop('required',false);
		}
		if ($.inArray($("#status option:selected").val(),['5','85','86','87','88','89','90'])==-1){
			var br_sati = '<?php echo $_user['br_sati']; ?>';
$('#hour').val(br_sati);
$('#hour_pre').val('0');
$("#hour").prop('readonly', true);
$("#hour_pre").prop('readonly', true);
$('#status_pre').val('');
		}
		else{
			$("#hour").prop('readonly', false);
			$("#hour_pre").prop('readonly', false);
		}
      });
			$("#popup_form").validate({
				focusCleanup: true,
						submitHandler: function(form) {
							$('.dialog-loader').show();
							$(form).ajaxSubmit({
								url:"<?php echo $url.'/modules/core/ajax/korekcije.php'; ?>",
								type:"post",
								success: function(data){
									$("#res").html(data);
			    					$('.dialog-loader').hide();
									$('#spasi_registraciju').prop('disabled', true);
								}
							});
						}
			});
			
				$('#status').on('change', function() {
			
if ($("#status option:selected").text().indexOf('Bolovanje') !== -1){
	$('#comment_row').show();
	$('#komentar').prop('required',true);
	}
	else{
		$('#comment_row').hide();
		$('#komentar').prop('required',false);
		}
		if ($.inArray($("#status option:selected").val(),['5','85','86','87','88','89','90'])==-1){
			var br_sati = '<?php echo $br_sati; ?>';
$('#hour').val(br_sati);
$('#hour_pre').val('0');
$("#hour").prop('readonly', true);
$("#hour_pre").prop('readonly', true);
$('#status_pre').val('');
		}
		else{
			$("#hour").prop('readonly', false);
			$("#hour_pre").prop('readonly', false);
		}
})
			
		</script>

    <?php
      }else{
        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
      }
     ?>

	</div>
  <div class="dialog-loader"><i></i></div>
</section>
