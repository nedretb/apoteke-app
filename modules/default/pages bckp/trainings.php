<?php
  _pagePermission(5, false);

  $get = $db->query("SELECT * FROM  ".$portal_users."  WHERE user_id='".$_user['user_id']."'");
  if($get->rowCount()<0){
    $row = $get->fetch();

 

 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-6">
        <h2>
          <?php echo __('Moji treninzi'); ?><br/><br/>
        </h2>
      </div>
      <div class="col-sm-6 text-right"><br/>
        <div class="pull-right">
          
 <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_trainings_add.php'; ?>" data-widget="ajax" data-id="opt2" class="btn btn-red btn-lg"><?php echo __('Dodaj trening'); ?> <i class="ion-ios-plus-empty"></i></a>
        </div>
      </div>

    </div>


    <?php

      $limit	= 20;

      if($_num){

        $offset = ($_num - 1) * $limit;

      }else{

        $offset = 0; $_num = 1;

      }

      $where = "WHERE user_id='".$_user['user_id']."'";
      $path = '?m='.$_mod.'&p='.$_page;

      if(isset($_GET['t'])){
        $type = $_GET['t'];
        $where .= " AND is_archive='1'";
        $path .= '&t='.$type;
      }else{
        $type = '';
        $where .= " AND is_archive='0'";
      }

      $path .= '&pg=';

      $query = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_trainings."  ".$where." ORDER BY date_created DESC");
      $get2 = $db->query("SELECT COUNT(*) FROM  ".$portal_trainings."  ".$where." ");
      $result = $get2->fetch();
      //$total=$result[0];
      $total = $get2->rowCount();

      $query2 = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_users."  WHERE employee_no='".$_user['parent']."'");
      $get22 = $db->query("SELECT COUNT(*) FROM  ".$portal_trainings."  ".$where."");
      $total2 = $get2->rowCount();

       foreach($query2 as $item2){
          $parent_f = $item2['fname'];
          $parent_l = $item2['lname'];

        }


      $query3 = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_users."  WHERE employee_no='".$_user['hr']."'");
      
       foreach($query3 as $item3){
          $hr_f = $item3['fname'];
          $hr_l = $item3['lname'];

        }

         $query4 = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_users."  WHERE employee_no='".$_user['admin']."'");
      
       foreach($query4 as $item4){
          $admin_f = $item4['fname'];
          $admin_l = $item4['lname'];

        }

       

     ?>

    <a href="/app/?m=default&p=trainings" class="btn btn-filter <?php if($type == ''){ echo 'active'; } ?>"><?php echo __('Aktivni'); ?></a>
    <a href="/app/?m=default&p=trainings&t=3" class="btn btn-filter <?php if($type == '3'){ echo 'active'; } ?>"><?php echo __('Arhiva'); ?></a>

    <br/><br/>

    <?php

      if($total<0)
	  {

        foreach($query as $item){
          $tools_id = $item['request_id'];

          $border = '';

          if( ($item['status']==0) and ($item['status_hr']==0 )){
            $border = 'blue';
          }elseif(($item['status']==1) and ($item['status_hr']==0)){
            $border = 'blue';
          }elseif(($item['status']==0) and ($item['status_hr']==1)){
            $border = 'blue';
          }elseif(($item['status']==1) and ($item['status_hr']==1)){
            $border = 'green';
          }elseif(($item['status']==2) or ($item['status_hr']==2)){
            $border = 'red';
          }elseif(($item['status']==3) or ($item['status_hr']==3)){
            $border = 'gray';
          }

          $parent = _user($item['parent_id']);
          $parent_tr = _user($item['parent']);
          $_hr = _user($item['hr']);




    ?>

    <div class="box box-lborder box-lborder-<?php echo $border; ?>" id="opt-<?php echo $tools_id; ?>">
      <div class="content">
        <div class="row">
          <div class="col-sm-4">
            <?php echo __('Zahtjev za trening kreiran'); ?>
            <br/>
            <?php echo __('Od:'); ?> <b><?php echo date('d/m/Y',strtotime($item['date_from'])); ?></b> &nbsp;
            <?php echo __('Do:'); ?> <b><?php echo date('d/m/Y',strtotime($item['date_to'])); ?></b><br/>
			
            
            <?php if($item['comment'] != ''){ ?><br/>
             <b><?php echo $_user['fname'].' '.$_user['lname']; ?></b> 
            
                <?php echo $item['comment']; }?>

                <?php if($item['comment_parent'] != ''){ ?><br/>
             <b><?php echo $parent_f.' '.$parent_l; ?></b> 
            
                <?php echo $item['comment_parent']; }?> 

                <?php if($item['comment_hr'] != ''){ ?><br/>
             <b><?php echo $hr_f.' '.$hr_l; ?></b> 
            
                <?php echo $item['comment_hr']; }?> 

                 <?php if($item['comment_admin'] != ''){ ?><br/>
             <b><?php echo $admin_f.' '.$admin_l;?></b> 
            
                <?php echo $item['comment_admin']; }?>

           

              <blockquote class="comment-list">
              <?php if(($item['status']==1) and ($item['status_hr']==1 )){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
            <?php }else if(($item['status']==2) or ($item['status_hr']==2)){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
            <?php }else if(($item['status']==0) and ($item['status_hr']==0 )){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na čekanju...'); ?></span>
            <?php }else if(($item['status']==1) and ($item['status_hr']==0 )){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na čekanju...'); ?></span>
              <?php  }else if(($item['status']==0) and ($item['status_hr']==1 )){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na čekanju...'); ?></span>
              <?php } ?>


        </div>

        
		  
          <div class="col-sm-2">
		  
            
          
            <?php echo __('Zahtjev kreiran:'); ?><br/>
            <?php echo date('d/m/Y',strtotime($item['date_created'])); ?>
          </div>
          <div class="col-sm-4">
            <?php echo __('Status:'); ?><br/>
            <?php if($item['date_response'] != '1970-01-01'){ ?>
            
            <?php if($item['status']==1){ 
              ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response'])); ?></span><br>
            <?php }else if($item['status']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response']));  ?></span><br>
            <?php } ?>
             <?php if($item['status_hr']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_hr'])); ?></span><br>
            <?php }else if($item['status_hr']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y',strtotime($item['date_response_hr'])); ?></span><br>
            <?php } ?>
             <?php if($item['status_admin']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin'])); ?></span><br>
            <?php }else if($item['status_admin']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin'])); ?></span><br>
            <?php } ?>
          <br/>
        
            <?php }else{ echo 
			'&nbsp;'; } ?>
          
          </div>
          <div class="col-sm-2 text-right">
            <?php if($item['status']==0){ ?>
              <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_remove:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite poništiti zahtjev?'); ?>"><i class="ion-android-close"></i></a>
              <a href="<?php echo '/app/?m=users&p=popup_trainings_add_view&id='.$tools_id; ?>"class="table-btn alt"><i class="ion-eye"></i></a>
            <?php }else{ ?>
              <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_archive:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite arhivirati zahtjev?'); ?>"><i class="ion-folder"></i></a>
              <a href="<?php echo '/app/?m=users&p=popup_trainings_add_view&id='.$tools_id; ?>"class="table-btn alt"><i class="ion-eye"></i></a>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <?php } }else{ ?>
      <div class="text-center">
        <?php echo __('Nema spašenih zahtjeva prema odabranim parametrima.'); ?>
      </div>
    <?php } ?>

    <div class="text-center">
      <div class="btn-group">
      <?php echo _pagination($path, $_num, $limit, $total); ?>
      </div>
    </div>


  </div>


</section>

<?php
  }else{
    echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
  }
 ?>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

</body>
</html>
