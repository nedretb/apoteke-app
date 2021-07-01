<?php
  _pagePermission(5, false);
  //require_once '../../../configuration.php';
  //include_once $root.'/modules/users/functions.php';

  /*************************************************/
  /****************PARENT RESPONSE******************/
  /*************************************************/

 ?>


 <section class="full">
  <div class="container" style="background-color:white";>

     <?php
       $get = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_GET['id']."'");
       if($get->rowCount()<0){
         $row = $get->fetch();
         $user = _user($row['user_id']);


      $query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[business_trip] WHERE request_id='".$_GET['id']."'");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip]");
      $total = $get2->rowCount();
      foreach($query as $item){
         $tools_id = $item['request_id'];
      }

		 
	   $query2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['parent']."'");
     

       foreach($query2 as $item2){
          $parent_f = $item2['fname'];
          $parent_l = $item2['lname'];

        }

      $query3 = $db->query("SELECT  * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['hr']."'");
      
       foreach($query3 as $item3){
          $hr_f = $item3['fname'];
          $hr_l = $item3['lname'];

        }

      $query4 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['to_admin2']."'");
      
       foreach($query4 as $item4){
          $admin_f = $item4['fname'];
          $admin_l = $item4['lname'];

        }

      
         
     ?>


   <h3><span><?php echo __('Obrada zahtjeva!'); ?> </span></h3>
   <hr style="margin:5px;">
    <div id="res"></div>

    <div class="row">
      <div class="col-sm-4">
        <b><?php echo $user['fname'].' '.$user['lname']; ?></b><br/>
        <?php echo $user['position']; ?>
      </div>
      <div class="col-sm-4">
        <?php echo __('Od:'); ?> <b><?php echo date('d/m/Y',strtotime($row['h_from'])); ?></b><br/>
        <?php echo __('Do:'); ?> <b><?php echo date('d/m/Y',strtotime($row['h_to'])); ?></b>
      </div>
      <div class="col-sm-4">
        <small><?php echo __('Zahtjev kreiran:'); ?></small><br/>
        <?php echo date('d/m/Y',strtotime($row['date_created'])); ?>
      </div>
    </div>
<hr>
    <div class="row">
      <div class="col-sm-4">
        <small><?php echo __('Destinacija:'); ?></small><br/>
        <b><?php echo $row['destination']; ?></b><br/>
     
      </div>

      <div class="col-sm-4">
        <small><?php echo __('Svrha putovanja:'); ?></small><br/>
        <b><?php echo $row['purpose_trip']; ?></b><br/>
     
      </div>

       <div class="col-sm-4">
        <small><?php echo __('Pregled zahjeva:'); ?></small><br/>
        <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
     
      </div>
    </div>


    <form id="popup_form" method="post" enctype="multipart/form-data">

      <input type="hidden" name="request" value="business-trip-request-response-admin2"/>
      <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>"/>

      <hr/>

      <?php echo __('Status zahtjeva:'); ?><br/><br/>
      <label class="radio">
        <input type="radio" name="status_admin2" value="1" checked="true">
        <i class="ion-android-checkbox-outline"></i><br>
        <?php echo __('Odobreno'); ?>
      </label>
      <label class="radio">
        <input type="radio" name="status_admin2" value="2">
        <i class="ion-android-close"></i><br>
        <?php echo __('Odbijeno'); ?>
      </label>

      <hr/>

 <!--  <div class="row">
        <div class="col-sm-12">
	  <?php if($row['comment'] != null) { ?>
      <small><?php echo __('Komentar od '.' '.$user['fname'].' '.$user['lname'].':'); ?></small>
      <b><?php echo $row['comment']; ?></b><br/>
	  <?php } ?>
	   <?php if($row['comment_parent'] != null) { ?>
      <small><?php echo __('Komentar od '.' '.$parent_f.' '.$parent_l.':'); ?></small>
      <b><?php echo $row['comment_parent']; ?></b><br/>
	  <?php } ?>
	  <?php if($row['comment_hr'] != null) { ?>
      <small><?php echo __('Komentar od '.' '.$hr_f.' '.$hr_l.':'); ?></small>
      <b><?php echo $row['comment_hr']; ?></b><br/>
	   <?php } ?>
	   <?php if($row['comment_admin'] != null) { ?>
      <small><?php echo __('Komentar od '.' '.$admin_f.' '.$admin_l.':'); ?></small>
      <b><?php echo $row['comment_admin']; ?></b><br/>
      <?php } ?>
      </div>
	  
	  
      <div>
      <label style="margin-left:10px;"><?php echo __('Odgovori:'); ?></label><br/>
      <textarea name="comment_parent" style="margin:10px!important;width:98%;"></textarea> -->

      <hr/>

      <button type="submit" style="margin-bottom:20px;width:200px;margin-right:15px;" class="btn btn-red pull-right" ><?php echo __('Spasi!'); ?> <i class="ion-ios-download-outline"></i></button>


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
                url:"<?php echo $url.'/modules/business_trip/ajax.php'; ?>",
                type:"post",
                success: function(data){
                  $("#res").html(data);
                  $('.dialog-loader').hide();
                  $(".dialog").animate({scrollTop:0},600);
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
  <div class="dialog-loader"><i></i></div>
</section>
