<?php
  _pagePermission(4, false);

 
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
        if (isset($_GET['del'])){
          $data =$db->query("DELETE  FROM [c0_intranet2_apoteke].[dbo].[countries] 
            where country_id=".$_GET['del']) ;

          if($data->rowCount()==1) {
            echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno izbrisane!').'</div>';
          }
        }
        if(isset($_POST['request'])){

          if($_POST['request']=='add'){

            $check = $db->query("SELECT * from [c0_intranet2_apoteke].[dbo].[countries] where name = N'".$_POST['name']."' ");
            $check = $check->fetch();

            if($check){
              echo '<div class="alert alert-danger text-center">'.__('Unos već postoji!').'</div>';
            }else{

            $data =$db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[countries] (
              name,wage) VALUES (N'".$_POST['name']."','".$_POST['wage']."')") ;


            if($data->rowCount()==1) {
              echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno spašene!').'</div>';
            }
            }

          }

        }

        ?>

        <div class="box" id="c1">
					<div class="head">

						<h3><?php echo __('Novi unos'); ?></h3>
					</div>
					<div class="content clear" id="c1a" style="display: block;">

            <form action="" method="post" id="form">

              <input type="hidden" name="request" value="add">

              <label><?php echo __('Naziv'); ?></label>
              <input type="text" name="name" class="form-control" required><br/>

              <label><?php echo __('Iznos dnevnice'); ?></label>

              <input type="number" name="wage" class="form-control" min ='1' max ='1000' required><br/>


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

          $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries] 
ORDER BY name COLLATE Latin1_General_CS_AS_KS_WS ASC;");
          $get2 = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[countries] ".$where." ");
		   
          $total = $get2->rowCount();

         ?>

        <div class="box clear">
          <div class="content">
          <table class="table table-hover">
    				<thead>
    					<tr>
    						<th><?php echo __('Naziv'); ?></th>
                <th>Iznos dnevnice</th>
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
                <td><?php echo $item['wage']; ?></td>
    						<td class="text-right">
                  <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_country_edit.php?id='.$tools_id; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="table-btn countries-back"><i class="ion-edit"></i></a>
                  <a href="<?php echo '/app_raiff/?m=business_trip&p=countries&del='.$item['country_id'] ?>" class="table-btn countries-back" ><i class="ion-android-close"></i></a>
                </td>
    					</tr>
              <?php } } ?>
    				</tbody>
    			</table>
          <div class="text-right">
            <div class="btn-group">
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
