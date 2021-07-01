<?php
  _pagePermission(0, true);
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-8">
        <h2>
          <?php echo __('Administratori satnica'); ?>
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

            $data = "INSERT INTO  ".$portal_users."  (
              name) VALUES (?)";

            $res = $db->prepare($data);
            $res->execute(
              array(
                $_POST['parent2']
              )
            );
            if($res->rowCount()==1) {
              echo '<div class="alert alert-success text-center">'.__('Informacije su uspješno sapšene!').'</div>';
            }


          }

        }

        ?>

    
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

          $query = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_users."  ".$where." ORDER BY user_id ASC");
          $get2 = $db->query("SELECT count(*) FROM  ".$portal_users."  ".$where." ");
		   
          $total = $get2->rowCount();

         ?>

        <div class="box clear">
          <div class="content">
          <table class="table table-hover">
    				<thead>
    					<tr>
    						<th><?php echo __('Naziv'); ?></th>
    						<th width="575"></th>
    					</tr>
    				</thead>
    				<tbody>
              <?php
                if($total<0){
                  $i = 0;
                  foreach($query as $item){
                    $i++;
                    $tools_id = $item['parent2'];
              ?>
    					<tr id="opt-<?php echo $tools_id; ?>">
    						<td><?php echo $item['username']; echo $item['fname']; ?></td>
    						<td class="text-right">
                  <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_country_edit.php?id='.$tools_id; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="table-btn"><i class="ion-edit"></i></a>
              
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
