<?php
  _pagePermission(4, true);
  
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">
	
	<?php 
		if(isset($_POST)){
			if(!empty($_POST['logout_time']) and $_POST['logout_time'] > 0 ){
				$d = $db->prepare("UPDATE $_conf[app_database].[settings] SET value = ? WHERE name = 'logout_time' ");
				$d->execute(array($_POST['logout_time']));
			}
		}
	?>
	
    <form action="" method="post" id="form" >
    <input type="hidden" name="request" value="settings">

    <div class="row">

      <div class="col-sm-8">
        <h2>
          <?php echo __('Podešavanja'); ?><br/><br/>
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>
        <button type="submit" class="btn btn-red btn-lg">Spasi! <i class="ion-ios-download-outline"></i></button>
      </div>

    </div>

    


    <div class="row">

      <div class="col-sm-4">

        <div class="box" id="c1">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a"></a>
						</div>
						<h3><?php echo __('Vrijeme za logout'); ?></h3>
					</div>
					<div class="content" id="c1a" style="display: block;">

            <label><?php echo __('Vrijeme neaktivnosti za odjavu sa portala: (minute)'); ?></label>
			<br style="clear: both" />
            <input type="text" name="logout_time" class="form-control" value="<?php echo _settings('logout_time'); ?>"  style="width: 20%; float:left; vertical-align: middle;" required><br/>

            

					</div>
				</div>

       

    </form>


  </div>
  </div>


</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

<script>
  $(function(){
    var f = $.farbtastic('#picker');
    $('.form-color').each(function () {
      $('#picker').insertAfter(this).slideDown();
      f.linkTo(this);
    }).focus(function() {
      $('#picker').insertAfter(this).slideDown();
      f.linkTo(this);
    });
    $('#picker').hide();
    $('.form-color').focusout(function() {
      $('#picker').hide();
    });
  });
</script>

</body>
</html>
