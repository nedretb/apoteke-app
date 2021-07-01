<?php
  require_once '../../../configuration.php';
 ?>
<div class="header">
	<a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Ažuriranje!'); ?></span></h4>
</div>

<section>
	<div class="content clear">

    <?php
      $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries] WHERE country_id='".$_GET['id']."'");
      if($get->rowCount()<0){
        $row = $get->fetch();
    ?>

		<div id="res"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="country-edit"/>
      <input type="hidden" name="request_id" value="<?php echo $row['country_id']; ?>"/>

      <label><?php echo __('Naziv'); ?></label>
      <input type="text" name="name" value="<?php echo $row['name']; ?>" class="form-control" required><br/>

      <label><?php echo __('Iznos dnevnice'); ?></label>

	  <input type="number" name="wage" value="<?php echo $row['wage']; ?>" min='1' max='1000' class="form-control" required><br/>


      <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>


		</form>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
		<script>
			$("#popup_form").validate({
				focusCleanup: true,
						submitHandler: function(form) {
							$('.loader').show();
							$(form).ajaxSubmit({
								url:"<?php echo $url.'/modules/settings/ajax.php'; ?>",
								type:"post",
								success: function(data){
									$("#res").html(data);
			    					$('.loader').hide();
								}
							});
						}
			});
		</script>

    <?php
      }else{
        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
      }
     ?>

	</div>
</section>
