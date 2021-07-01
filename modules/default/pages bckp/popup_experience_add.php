<?php
  require_once '../../../configuration.php';
  $_user = _user(_decrypt($_SESSION['SESSION_USER']));
 ?>
<div class="header">
	<a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Radno iskustvo'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
	<div class="content clear">

		<div id="res"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="experience-add"/>


      
	  <label><?php echo __('Naziv pozicije:'); ?></label>
      <div id="position">
        <input type="text" name="position" class="form-control" placeholder="<?php echo __('Naziv pozicije'); ?>" title="Unesite naziv pozicije!" required ><br/>
      </div>

	  
	 <label><?php echo __('Naziv OJ:'); ?></label>
      <div id="OJ">
        <input type="text" name="OJ" class="form-control" placeholder="<?php echo __('Naziv OJ'); ?>" title="Unesite naziv OJ!" required><br/>
      </div>
	  
	  

      <div id="dt">
        <input type="text" name="date_from" class="form-control" id="date_from" placeholder="dd/mm/yyyy">
      </div>
		<br/>
		
		<div id="dt">
        <input type="text" name="date_to" class="form-control" id="date_to" placeholder="dd/mm/yyyy">
      </div>
		<br/>
		
		 <label><?php echo __('Poslodavac:'); ?></label>
      <div id="poslodavac">
        <input type="text" name="poslodavac" class="form-control" placeholder="<?php echo __('Poslodavac'); ?>" title="Unesite poslodavca!" required><br/>
      </div>
	  
	   <label><?php echo __('Napomena:'); ?></label>
      <div id="napomena">
        <input type="text" name="napomena" class="form-control" placeholder="<?php echo __('Napomena'); ?>" <br/>
      </div>

      <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>


		</form>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

    <!-- Bootstrap -->
    <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

    <!-- Bootstrap datepicker -->
    <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>

    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
		<script>
    $(function(){
      var today = new Date();
  		var startDate = new Date();
  		$('#date_from').datepicker({
  			todayBtn: "linked",
  			format: 'dd/mm/yyyy',
			language: 'bs'
  			//startDate: startDate,
			//endDate: new Date('2017/12/31')
  		});

  		$('#date_to').datepicker({
  			todayBtn: "linked",
  			format: 'dd/mm/yyyy',
			language: 'bs'
  			//startDate: startDate,
			//endDate: new Date('2017/12/31')
  		});
  
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
									$("#res").html(data);
			    					$('.dialog-loader').hide();
								}
							});
						}
			});
		
			  function changeHandler(val)
  {
    if (Number(val.value) < 5)
    {
       val.value = 5
	 }
	 if (Number(val.value) > 30)
    {
       val.value = 30
	 }
  }

		
		
		</script>

	</div>
  <div class="dialog-loader"><i></i></div>
</section>
