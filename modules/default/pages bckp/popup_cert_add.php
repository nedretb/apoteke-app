<?php
  require_once '../../../configuration.php';
?>
<div class="header">
	<a class="btn close" data-widget="close-ajax1" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Certifikat'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>

	<div class="content clear">

		<div id="res_cert"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="cert-add"/>
			
	<label style="padding-right: 0.7%;"><?php echo __('Naziv institucije'); ?></label>
               <select id="certifikat_kompanija" name="certifikat_kompanija" class="" style = "outline:none;width:50%;" class="form-control">
     <?php echo _optionInstitutionCodeNAV('')?>
      </select>
	
		<br/><br/>
		
		    <label style="padding-right: 1%;"><?php echo __('Opis certifikata'); ?></label>
                 <select id="certifikat" name="certifikat" class="" style = "outline:none;width:50%;" class="form-control">
     <?php echo _optionCertifikatCodeNAV('')?>
      </select>
	  
<button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>

</form>

    <!-- Bootstrap -->
 
	<link href="<?php echo $_pluginUrl; ?>/select2/select2.min.css" rel="stylesheet">
	<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
	<script src="<?php echo $_pluginUrl; ?>/select2/select2.min.js"></script>
	

    <!-- Bootstrap -->
    <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
	<!-- jQuery confirm -->
<script src="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.js"></script>
		<script>
    $(function(){
     $( document ).ready(function(){
          $('.dialog-loader').hide();
		
        });

    });
			$("#popup_form").validate({
				
						focusCleanup: true,
						submitHandler: function(form) {
									
               			$('.dialog-loader').show();
							$(form).ajaxSubmit({
								url:"<?php echo $url.'/modules/default/ajax.php'; ?>",
								type:"post",
								success: function(data){
									$("#popup_form")[0].reset();
									$("#res_cert").html(data);
			    					$('.dialog-loader').hide();
			    					//setTimeout(function(){ window.location.reload();  }, 500);
								}
							});
		             }
			});
		</script>
		</div>
  <div class="dialog-loader"><i></i></div>
</section>
