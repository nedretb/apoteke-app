<?php
  require_once '../../../configuration.php';
  include_once $root.'/modules/settings/functions.php';
 ?>
<div class="header">
	<a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
	<div class="content clear">

		<div id="res"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="lang-add"/>

      <label><?php echo __('Naziv'); ?></label>
      <input type="text" name="name" class="form-control" required><br/>

      <div class="row">

        <div class="col-xs-6">
          <label><?php echo __('Kod'); ?></label>
          <input type="text" name="code" class="form-control" required><br/>
        </div>
        <div class="col-xs-6">
          <label><?php echo __('Direkcija teksta'); ?></label>
          <select name="direction" class="form-control">
            <option value="ltr">ltr</option>
            <option value="rtl">rtl</option>
          </select>
        </div>

      </div><br/>

      <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>


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
								url:"<?php echo $url.'/modules/settings/ajax.php'; ?>",
								type:"post",
								success: function(data){
									$("#popup_form")[0].reset();
									$("#res").html(data);
			    					$('.dialog-loader').hide();
								}
							});
						}
			});
		</script>

	</div>
  <div class="dialog-loader"><i></i></div>
</section>
