<?php
  require_once '../../../configuration.php';
?>
<div class="header">
	<a class="btn close" data-widget="close-ajax1" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Jezici'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
	<div class="content clear">

		<div id="res_lang"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="lang-add"/>

	<label style="padding-right: 1%;"><?php echo __('Jezik'); ?></label>
                  <select id="jezik" name="jezik" class="" style = "outline:none;width:50%;" class="form-control">
     <?php echo _optionLanguageCodeNAV('')?>
      </select>

      <br/><br/>
	  
	   <label style="padding-right: 1.3%;"><?php echo __('Nivo'); ?></label>
                    <select id="nivo_jezik" name="nivo_jezik" class="" style = "outline:none;width:50%;" class="form-control">
     <?php echo _optionLanguageLevelNAV('')?>
      </select>
	  
	 <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>

</form>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

    <!-- Bootstrap -->
    <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
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
									$("#res_lang").html(data);
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
