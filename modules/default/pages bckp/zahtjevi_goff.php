<?php
 // _pagePermission(2, false);
 

 ?>

<!-- START - Main section -->
<br />
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-6">
        <h2>
          <?php echo __('Rješenja o korištenju godišnjeg odmora'); ?><br/><br/>
        </h2>
      </div>
      <div class="col-sm-6 text-right"><br/>

      </div>

    </div>

    <?php
	
      $limit	= 20;

      if($_num){

        $offset = ($_num - 1) * $limit;

      }else{

        $offset = 0; $_num = 1;

      }
		
      if($_user['role']==0){
        $where = "WHERE ";
      }else{
        $where = "WHERE parent_id='".$_user['user_id']."' AND";
      }

      $path = '?m='.$_mod.'&p='.$_page;

      if(isset($_GET['t'])){
        $type = $_GET['t'];
        $where .= " status != '0'";
        $path .= '&t='.$type;
      }else{
        $type = '';
        $where .= " status='0'";
      }

      if(isset($_GET['u'])){
        $usr = $_GET['u'];
        if($usr != ''){
          $where .= " AND user_id='".$usr."'";
          $path .= '&u='.$usr;
        }else{
          $usr = '';
          $where .= "";
        }
      }else{
        $usr = '';
        $where .= "";
      }

      if(isset($_GET['d'])){
        $dt = $_GET['d'];
        if($dt != ''){
          $where .= " AND date_created LIKE '%$dt%'";
          $path .= '&d='.$dt;
        }else{
          $dt = '';
          $where .= "";
        }
      }else{
        $dt = '';
        $where .= "";
      }

      $path .= '&pg=';
/*
	$query = $db->query("SELECT * FROM  ".$portal_requests."  WHERE user_id='".$_user['user_id']."' ORDER BY year ASC");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2].[dbo].[[requests]] ");
      $total = $get2->rowCount();
	 */   
	 
		include('table_used.php');
		$query = $db->query("SELECT * FROM $database_used.[requests] WHERE employee_no = '$_user[employee_no]'  ");
		$total = $query->rowCount();
       



     ?>

     <div class="row">
       <div class="col-sm-4"><br/>
	   <?php if(1==2){ ?>
        <a href="/<?php echo $_conf['app_location_module']; ?>/?m=travel_requests&p=all" class="btn btn-filter <?php if($type == ''){ echo 'active'; } ?>"><?php echo __('Aktivni'); ?></a>
        <a href="/<?php echo $_conf['app_location_module']; ?>/?m=travel_requests&p=all&t=3" class="btn btn-filter <?php if($type == '3'){ echo 'active'; } ?>"><?php echo __('Arhiva'); ?></a>
	   <?php } ?>
	   <a href="/<?php echo $_conf['app_location_module']; ?>/?m=default&p=zahtjevi_go" class="btn btn-filter <?php if($type == ''){ echo 'active'; } ?>"><?php echo __('Rješenja'); ?></a>
      </div>
	   <?php if(1==2){ ?>
      <div class="col-sm-8 pull-right">
        <form action="" method="get" class="">
          <input type="hidden" name="m" value="<?php echo $_mod; ?>">
          <input type="hidden" name="p" value="<?php echo $_page; ?>">
          <?php if(isset($_GET['t'])){ ?>
          <input type="hidden" name="t" value="<?php echo $_GET['t']; ?>">
          <?php } ?>
          <?php if($dt != '' || $usr != ''){ ?>
            <a href="<?php echo $url.'/?m='.$_mod.'&p='.$_page; ?>" class="btn-search pull-right"><i class="ion-android-close"></i></a>
          <?php } ?>
          
		 
         <button type="submit" class="btn-search pull-right"><i class="ion-android-search"></i></button>
          <select name="u" class="form-control pull-right" style="max-width:150px;">
            <option value=""><?php echo __('Odaberi'); ?></option>
            <?php
              $_user_role = $_user['role'];
              $get_users = $db->query("SELECT * FROM [c0_intranet2].[dbo].[users] WHERE role > '$_user_role'");
              if($get_users->rowCount()<0){
                foreach($get_users as $user){
                  if($usr==$user['user_id']){ $sel = 'selected="selected"'; }else{ $sel = ''; }
                  echo '<option value="'.$user['user_id'].'" '.$sel.'>'.$user['fname'].' '.$user['lname'].'</option>';
                }
              }
            ?>
          </select>
          <input type="text" name="d" class="form-control input-date pull-right" value="<?php echo $dt; ?>" style="max-width:300px;">
        </form>
      </div>
	   <?php } ?>
    </div><br/>

    <?php

      if($total<0){

        foreach($query as $item){
			
			//print_r($item);
          $tools_id = $item['request_id'];

          $border = '';

          if($item['status']==0){
            $border = 'blue';
          }elseif($item['status']==1){
            $border = 'green';
          }elseif($item['status']==2){
            $border = 'red';
          }elseif($item['status']==3){
            $border = 'gray';
          }

          $user = _user($item['user_id']);
    ?>

    <div class="box box-lborder box-lborder-<?php echo $border; ?>">
      <div class="content">
        <div class="row">
          <div class="col-sm-5">
		  <?php if($item['type'] == "DEC"){
			  ?>
				 <?php echo __('Rješenje o korištenju godišnjeg odmora'); ?>
			  <?php 
			  
		  } else if($item['type'] == "GO") {
			  ?>
			   <?php echo __('Zahtjev za godišnji odmor'); ?>
			  <?php 
		  } ?>
           
            <br/>
			
            <?php echo __('Od:'); ?> <b><?php echo date('d.m.Y',strtotime($item['h_from'])); ?></b> &nbsp;
            <?php echo __('Do:'); ?> <b><?php echo date('d.m.Y',strtotime($item['h_to'])); ?></b>
			
			<?php if($item['type'] != "DEC"){ ?>
            <?php if($item['status']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
            <?php }else if($item['status']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
            <?php } ?>
            <?php } ?>
          </div>
          <div class="col-sm-2">
            <b><?php echo $user['fname'].' '.$user['lname']; ?></b><br/>
            <small><?php echo $user['position']; ?></small>
          </div>
          <div class="col-sm-2">
            
			<?php if($item['type'] == "DEC"){
			  ?>
				 <?php echo __('Rješenje kreirano:'); ?></b><br/>
			  <?php 
			  
		  } else if($item['type'] == "GO") {
			  ?>
			   <?php echo __('Zahtjev kreiran:'); ?></b><br/>
			  <?php 
		  } ?>
            
            <?php echo date('d.m.Y',strtotime($item['date_created'])); ?> 
          </div>
          <div class="col-sm-3 text-right">
		  
            <?php if($item['status'] == 1){ ?>
			
			       <?php if(date('Y',strtotime($item['date_created']))==2018){ ?>
                <a target="_blank" href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_vacation_request_pdf2018.php?id='.$tools_id; ?>" style="width:180px;"  class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i> Preuzmite rješenje </a>
            <?php }else{ ?>
            <a target="_blank" href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_vacation_request_pdf.php?id='.$tools_id; ?>" style="width:180px;"  class="table-btn"><i style="font-size:16px;" class="ion-ios-copy-outline"></i> Preuzmite rješenje </a>
            <?php } ?>

            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <?php } }else{ ?>
      <div class="text-center">
	  <?php 
			  ?>
				 <?php echo __('Nema spašenih rješenja prema odabranim parametrima.'); ?>
			  <?php 
			  
		   ?>
        
      </div>
    <?php } ?>

    <div class="text-center">
      <div class="btn-group">
      <?php echo _pagination($path, $_num, $limit, $total); ?>
      </div>
    </div>


  </div>


</section>
<!-- END - Main section -->

  <?php

    include $_themeRoot.'/footer.php';

   ?>


</body>
</html>
