<?php
  _pagePermission(0, true);
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-8">
        <h2>
          <?php echo __('Države'); ?>
          <small>&nbsp;</small>
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>

      </div>

    </div>


    <div class="row">

      <div class="col-sm-5">

        <?php

        if(isset($_POST['request'])){

          if($_POST['request']=='add'){

            $data = "INSERT INTO  ".$portal_countries."  (
              name) VALUES (?)";

            $res = $db->prepare($data);
            $res->execute(
              array(
                $_POST['name']
              )
            );
            if($res->rowCount()==1) {
              echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno sapšene!').'</div>';
            }


          }

        }

        ?>

        <div class="box" id="c1">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a"></a>
							<a href="#" class="ion-arrow-expand" data-widget="fullscreen" data-id="c1"></a>
						</div>
						<h3><?php echo __('Novi unos'); ?></h3>
					</div>
					<div class="content clear" id="c1a" style="display: block;">

            <form action="" method="post" id="form">

              <input type="hidden" name="request" value="add">

              <label><?php echo __('Naziv'); ?></label>
              <input type="text" name="name" class="form-control" required><br/>

              <button type="submit" class="btn btn-red pull-right"><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>

            </form>

					</div>
				</div>

      </div>
      <div class="col-sm-7">

        <?php

          $limit	= 20;

          if($_num){

            $offset = ($_num - 1) * $limit;

          }else{

            $offset = 0; $_num = 1;

          }

          $path = '?m='.$_mod.'&p='.$_page.'&pg=';
          $where = "";

          $query = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_countries."  ".$where." ORDER BY name ASC");
          $get2 = $db->query("SELECT count(*) FROM  ".$portal_countries."  ".$where." ");
		   
          $total = $get2->rowCount();

         ?>

        <div class="box clear">
          <div class="content">
          <table class="table table-hover">
    				<thead>
    					<tr>
    						<th><?php echo __('Naziv'); ?></th>
    						<th width="75"></th>
    					</tr>
    				</thead>
    				<tbody>
              <?php
                if($total<0){
                  $i = 0;
                  foreach($query as $item){
                    $i++;
                    $tools_id = $item['country_id'];
              ?>
    					<tr id="opt-<?php echo $tools_id; ?>">
    						<td><?php echo $item['name']; ?></td>
    						<td class="text-right">
                  <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_country_edit.php?id='.$tools_id; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="table-btn"><i class="ion-edit"></i></a>
                  <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn" data-widget="remove" data-id="country:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite brisati:'); ?> <?php echo $item['name']; ?>"><i class="ion-android-close"></i></a>
                </td>
    					</tr>
              <?php } } ?>
    				</tbody>
    			</table>
          <div class="text-right">
            <div class="btn-group">
            <?php echo _pagination($path, $_num, $limit, $total); ?>
            </div>
          </div>
          </div>
        </div>



      </div>

    </div>


  </div>


</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

 <script>
  $(function(){

    $('form#form').validate({
      focusCleanup:true
    });

  });
 </script>

</body>
</html>
