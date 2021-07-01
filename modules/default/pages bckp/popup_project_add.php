<?php
  require_once '../../../configuration.php';
  $_user = _user(_decrypt($_SESSION['SESSION_USER']));
 ?>
<div class="header">
	<a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Projekti'); ?> - <?php echo __('Novi unos'); ?></span></h4>
</div>

<section>
	<div class="content clear">

		<div id="res"></div>

		<form id="popup_form" method="post">

			<input type="hidden" name="request" value="project-add"/>


      
	  <label><?php echo __('Naziv projekta:'); ?></label>
      <div id="project_name">
        <input type="text" name="project_name" class="form-control" placeholder="<?php echo __('Naziv projekta'); ?>" title="Unesite naziv projekta!" required><br/>
      </div>
	  
	 <label><?php echo __('Područje:'); ?></label>
      <div id="area">
        <input type="text" name="area" class="form-control" placeholder="<?php echo __('Područje'); ?>" title="Unesite područje!" required><br/>
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
	  
	   <label><?php echo __('Uloga u projektu:'); ?></label>
      <div id="uloga">
        <input type="text" name="uloga" class="form-control" placeholder="<?php echo __('Uloga u projektu'); ?>" title="Unesite ulogu u projektu!" required><br/>
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
