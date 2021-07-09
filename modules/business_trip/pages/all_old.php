<?php
  _pagePermission(5, false);


 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-6">
        <h2>
          <?php echo __('Zahtjevi za službeno putovanje'); ?><br/><br/>
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

      if($_user['role']==5){
        $where = "WHERE ";
      }else{
        $where = "WHERE parent='".$_user['employee_no']."' AND";
       
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

      $query = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[business_trip]  ORDER BY date_created DESC");
      $get2 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip] ".$where."");
      $total = $get2->rowCount();
      $_parent1 = $_user['employee_no'];



     


     ?>

     <div class="row">
       <div class="col-sm-4"><br/>
        <a href="/app/?m=business_trip&p=all" class="btn btn-filter <?php if($type == ''){ echo 'active'; } ?>"><?php echo __('Aktivni'); ?></a>
        <a href="/app/?m=business_trip&p=all&t=3" class="btn btn-filter <?php if($type == '3'){ echo 'active'; } ?>"><?php echo __('Arhiva'); ?></a>
      </div>
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
              $_parent_tr = $_user['employee_no'];
              $_parent_fname = $_user['fname'];
              $_parent_lname = $_user['lname'];
              $_hr = $_user['employee_no'];
              $_hr_tr = $_user['hr'];
              $_hr_fname = $_user['fname'];
              $_hr_lname = $_user['lname'];

              $new_parent=$_user['parent'];
              $new_hr=$_user['hr'];





              $get_users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where employee_no='$new_parent' and employee_no='$new_hr'");
              if($get_users->rowCount()<0){
                foreach($get_users as $user){
                  if($usr==$user['user_id'] and $usr==$user['hr']){ $sel = 'selected="selected"'; }else{ $sel = ''; }
                  echo '<option value="'.$user['user_id'].'" '.$sel.'>'.$user['fname'].' '.$user['lname'].'</option>';
                  
                }

              }


            ?>
          </select>
          <input type="text" name="d" class="form-control input-date pull-right" value="<?php echo $dt; ?>" style="max-width:300px;">
        </form>
      </div>
    </div><br/>

    <?php

      if($total<0){



        foreach($query as $item){
          $tools_id = $item['request_id'];

          $border = '';
		  
		  
		  
		  if ($item['country_ino'] == 1) {
		  
           if( ( ($item['status_hr']==1) or ($item['status_admin2_response']==1) or ($item['status_admin_response']==1) ) and ($item['country_ino'] == 1) ){
            $border = 'green';
          }elseif( ( ($item['status_hr']==2) or ($item['status_admin2_response']==2) or ($item['status_admin_response']==2) )  and ($item['country_ino'] == 1) ){
            $border = 'red';
          }elseif(  ( ($item['status_hr']==3) or ($item['status_admin2_response']==3) or ($item['status_admin_response']==3) ) and ($item['country_ino'] == 1) ){
            $border = 'gray';
          }
           elseif( ($item['status_hr']==0) and ($item['status_admin2_response']==0) and ($item['status_admin_response']==0)   and ($item['country_ino'] == 1) ){
            $border = 'yellow';
          }
		}
		
		
		  if ($item['country_ino'] != 1) {
		  
               if( ( ($item['status']==0) and ($item['status_parent2']==0) ) and ($item['status_hr']==0) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0)  and ($item['country_ino'] != 1) ){
            $border = 'yellow';
      }elseif( ( ($item['status']==1) or ($item['status_parent2']==1) )  and ($item['status_hr']==0) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['country_ino'] != 1) ){
            $border = 'yellow';
      }elseif( ( ($item['status']==0) and ($item['status_parent2']==0) )  and ($item['status_hr']==1) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['country_ino'] != 1) ){
            $border = 'yellow';
      }elseif(  ($item['status']==1)  and ($item['status_hr']==1 ) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['country_ino'] != 1) ){
            $border = 'green';
             }elseif(  ($item['status']==1)  and ($item['status_hr']==0 ) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1) and ($item['country_ino'] != 1) ){
            $border = 'green';
              }elseif( ($item['status']==0)  and ($item['status_hr']==0 ) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1) and ($item['country_ino'] != 1) ){
            $border = 'green';
          }elseif( ( ($item['status']==2) or ($item['status_parent2']==2) or ($item['status_hr']==2) or ($item['status_admin_response']==2) or ($item['status_admin2_response']==2) )  and ($item['country_ino'] != 1) ){
            $border = 'red';
      }elseif(  ( ($item['status']==2) or ($item['status_parent2']==2) ) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) ){
            $border = 'red';
          }elseif( ( ($item['status']==3) or ($item['status_parent2']==3)  or ($item['status_hr']==3) )  and ($item['country_ino'] != 1) ){
            $border = 'gray';
          }
     }
      
	  
          $user = _user($item['user_id']);



          

        


      $query2 = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['parent']."'");
      $get22 = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[business_trip] ".$where."");
      $total2 = $get2->rowCount();

       foreach($query2 as $item2){
          $parent_f = $item2['fname'];
          $parent_l = $item2['lname'];

        }

      $query3 = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['stream_parent']."'");
      
       foreach($query3 as $item3){
          $hr_f = $item3['fname'];
          $hr_l = $item3['lname'];

        }

      $query4 = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['to_admin']."'");
      
       foreach($query4 as $item4){
          $admin_f = $item4['fname'];
          $admin_l = $item4['lname'];

        }

         $query4 = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['to_admin2']."'");
      
       foreach($query4 as $item4){
          $admin2_f = $item4['fname'];
          $admin2_l = $item4['lname'];

        }
 
      $query5 = $db->query("SELECT TOP ".$limit." * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no='".$user['parentMBO2']."'");
      
       foreach($query5 as $item5){
          $parent2_f = $item5['fname'];
          $parent2_l = $item5['lname'];

        }

       

  

          

    ?>


    <div class="box box-lborder box-lborder-<?php echo $border; ?>">
      <div class="content">
        <div class="row">
          <div class="col-sm-3">
            <?php echo __('Zahtjev za službeno putovanje'); ?>
            <br/>
            <?php echo __('Od:'); ?> <b><?php echo date('d/m/Y',strtotime($item['h_from'])); ?></b> &nbsp;
            <?php echo __('Do:'); ?> <b><?php echo date('d/m/Y',strtotime($item['h_to'])); ?></b>
            <br>

            <?php if( (($item['status_hr']==1) or ($item['status_admin2_response']==1) or ($item['status_admin_response']==1)  )  and ($item['country_ino']==1 )){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
            <?php }else if( ( ($item['status_hr']==2) or ($item['status_admin2_response']==2) or ($item['status_admin_response']==2) )   and ($item['country_ino']==1) ){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
               <?php }else if( ($item['status_hr']==0) and ($item['status_admin2_response']==0) and ($item['status_admin_response']==0)  and ($item['country_ino']==1 ) ){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na odobrenju uprave...'); ?></span>
              <?php }
			  
			        else if( ( ($item['status']==2) or ($item['status_hr']==2) or ($item['status_admin2_response']==2) or ($item['status_admin_response']==2)  ) and ($item['country_ino']!=1 )){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
            <?php }else if(  ($item['status']==0)  and ($item['status_hr']==0 ) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['country_ino']!=1 )){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na odobrenju nadređenog...'); ?></span>
            <?php }else if( ($item['status']==1) and ($item['status_hr']==0 ) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['country_ino']!=1 )){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na odobrenju uprave...'); ?></span>
              <?php } else if( (($item['status']==1) or ($item['status_parent2']==1) ) and ($item['status_hr']==1 ) and ($item['country_ino']!=1 )){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
             <?php } else if( ($item['status']==1) and ($item['status_hr']==0 ) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1) and ($item['country_ino']!=1 )){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno od administratora'); ?></span>
            <?php }  else if( ($item['status']==0)  and ($item['status_hr']==0) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1)  and ($item['country_ino']!=1 ) ){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno od administratora'); ?></span>
            <?php }  ?>
			

           </div>
            <div class="col-sm-4">
            <?php if($item['status']==1){ 
              ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response'])); ?></span><br>
            <?php }else if($item['status']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response']));  ?></span><br>
            <?php } ?>

            <?php if($item['status_parent2']==1){ 
              ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobrio/la').' '.$parent2_f.' '.$parent2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_parent2'])); ?></span><br>
            <?php }else if($item['status_parent2']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$parent2_f.' '.$parent2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_parent2']));  ?></span><br>
            <?php } ?>

             <?php if($item['status_hr']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php if(isset($hr_f) and isset($hr_l)) { echo __('Odobrio/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_hr'])); } ?></span><br>
            <?php }else if($item['status_hr']==2){ ?> 
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i>   <?php  echo __('Odbio/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_hr'])); ?></span><br>
            <?php } ?>

              <?php if($item['status_admin2_response']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php if(isset($admin2_f) and isset($admin2_l)) { echo __('Odobrio/la').' '.$admin2_f.' '.$admin2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_status_admin2'])); } ?></span><br>
            <?php }else if($item['status_admin2_response']==2){ ?> 
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i>   <?php  echo __('Odbio/la').' '.$admin2_f.' '.$admin2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_status_admin2'])); ?></span><br>
            <?php } ?>

             <?php if($item['status_admin_response']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php if(isset($admin_f) and isset($admin_l)) { echo __('Odobrio/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_status_response_admin'])); } ?></span><br>
            <?php }else if($item['status_admin_response']==2){ ?> 
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i>   <?php  echo __('Odbio/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_status_response_admin'])); ?></span><br>
            <?php } ?>


			      <?php if($item['status_user_edit']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$user['fname'].' '.$user['lname'].' '."---".' '.date('d/m/Y H:i',strtotime($item['date_user_edit'])); ?></span><br>
            <?php }else if($item['status_user_edit']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$user['fname'].' '.$user['lname'].' '."---".' '.date('d/m/Y',strtotime($item['date_user_edit'])); ?></span><br>
            <?php } ?>

            <?php if($item['status_parent2_edit']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$parent2_f.' '.$parent2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_parent2_edit'])); ?></span><br>
            <?php }else if($item['status_parent2_edit']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$parent2_f.' '.$parent2_l.' '."---".' '.date('d/m/Y',strtotime($item['date_parent2_edit'])); ?></span><br>
            <?php } ?>

             <?php if($item['status_parent_edit']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_parent_edit'])); ?></span><br>
            <?php }else if($item['status_parent_edit']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$parent_f.' '.$parent_l.' '."---".' '.date('d/m/Y',strtotime($item['date_parent_edit'])); ?></span><br>
            <?php } ?>
             <?php if($item['status_hr_edit']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php if(isset($hr_f) and isset($hr_l)) { echo __('Editovao/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_hr_edit'])); } ?></span><br>
            <?php }else if($item['status_hr_edit']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$hr_f.' '.$hr_l.' '."---".' '.date('d/m/Y',strtotime($item['date_hr_edit'])); ?></span><br>
            <?php } ?>
             <?php if($item['status_admin']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin'])); ?></span><br>
            <?php }else if($item['status_admin']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$admin_f.' '.$admin_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin'])); ?></span><br>
            <?php } ?>
             <?php if($item['status_admin2']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Editovao/la').' '.$admin2_f.' '.$admin2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin2'])); ?></span><br>
            <?php }else if($item['status_admin2']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbio/la').' '.$admin2_f.' '.$admin2_l.' '."---".' '.date('d/m/Y H:i',strtotime($item['date_response_admin2'])); ?></span><br>
            <?php } ?>
          </div>
          <div class="col-sm-2">
            <b><?php echo $user['fname'].' '.$user['lname']; ?></b><br/>
            <small><?php echo $user['position']; ?></small>
          </div>
          <div class="col-sm-1">
            
            <?php echo __('Zahtjev kreiran:'); ?></b><br/>
            <?php echo date('d/m/Y',strtotime($item['date_created'])); ?> 
          </div>
          <div class="col-sm-2 text-right">
		  
            <?php if( ($item['status'] == 0) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0)  and ($item['parent']==$_user['employee_no']) and ($item['lock'] != 'N')){ ?>
			
            <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

              <?php if(($item['status'] == 0) and ($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['parent']==$_user['employee_no']) and ($item['lock'] == 'N')){ ?>
      
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           
            <?php } ?>

             <?php if( ($item['status'] == 0) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1)  and ($item['parent']==$_user['employee_no']) and ($item['lock'] != 'N')){ ?>
      
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

              <?php if(($item['status'] == 0) and ($item['status_admin2_response']==1 or $item['status_admin_response']==1) and ($item['parent']==$_user['employee_no']) and ($item['lock'] == 'N')){ ?>
      
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           
            <?php } ?>

           

             <?php if(($item['status'] == 1) and  ($item['parent']==$_user['employee_no']) and ($item['lock'] != 'N')){ ?>
      
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?> 


             <?php if(($item['status'] == 1)  and ($item['parent']==$_user['employee_no']) and ($item['lock'] == 'N')){ ?>
      
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
           
            <?php } ?> 

       
            <!--****************** Stream parent INO***************************-->

             <?php if( ($item['status'] == 0) and ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] != 1)  ){ ?>

          
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>


           


            <?php if( ($item['status'] == 0) and ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] != 1) ){ ?>

            <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle"></i></a>
        
           
            <?php } ?>

              <?php if( ($item['status'] == 0) and ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] != 1) ){ ?>

          
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
              <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>


            <?php if( ($item['status'] == 0) and ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] != 1) ){ ?>

            <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
           
            <?php } ?>

			
			     <?php if(  ($item['status'] == 1) and ($item['status_hr']==1) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] != 1) ){ ?>

            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if(  ($item['status'] == 1) and  ($item['status_hr']==1) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] != 1) ){ ?>

            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>


			
			      <?php if(  ($item['status'] == 1) and ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] != 1)){ ?>
			      <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if(  ($item['status'] == 1)  and  ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] != 1) ){ ?>
		
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>

            <?php } ?>

              <?php if(  ($item['status'] == 1) and ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] != 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if(  ($item['status'] == 1)  and  ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] != 1)){ ?>
    
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>

            <?php } ?>


             <!--****************** Stream parent BIH***************************-->
		
             <?php if( ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] == 1) ){ ?>
             <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip2_reponse&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
              <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==0) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>


             <?php if( ($item['status_hr']==1) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==1) and ($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==1) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==1) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] != 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_hr&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit"></i></a>
            <?php } ?>

             <?php if( ($item['status_hr']==0) and ($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['hr']==$_user['employee_no']) and ($item['lock'] == 'N') and ($item['country_ino'] == 1) ){ ?>
            <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye"></i></a>
            <?php } ?>
		
                  <!--****************** ADMINISTRATOR 1 SL. PUTA ***************************-->
                   <!--****************** INO  ***************************-->


             <?php if(($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['status']==0) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

              <?php if(($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['status']==0) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

               <?php if(($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['status']==1) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>


            <?php if(($item['status_admin_response']==0 and $item['status_admin2_response']==0) and ($item['status']==1) and ($item['status_hr']==0)  and ($item['country_ino'] != 1)  and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>



                <?php if(($item['status_admin_response']==0) and ($item['status']==1) and ($item['status_hr']==1)  and ($item['country_ino'] != 1)  and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

         <!--****************** BiH  ***************************-->
           
             <?php if(($item['status_admin_response']==0) and ($item['status_admin2_response']==0)  and ($item['status_hr']==0)  and ($item['country_ino'] == 1) and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

            <?php if(($item['status_admin_response']==0) and ($item['status_admin2_response']==0)  and ($item['status_hr']==1)  and ($item['country_ino'] == 1)  and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

                <?php if( ($item['status_admin_response']==1) and ($item['status_admin2_response']==0)   and ($item['status_hr']==0)  and ($item['country_ino'] == 1)  and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

             <?php if( ($item['status_admin_response']==0) and ($item['status_admin2_response']==1)   and ($item['status_hr']==0)  and ($item['country_ino'] == 1)  and ($item['admin']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?> 
           


             <!--****************** ADMINISTRATOR 2 SL. PUTA  ***************************-->
              <!--****************** INO  ***************************-->


             <?php if(($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['status']==0) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin2&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

            <?php if(($item['status_admin2_response']==1 or $item['status_admin_response']==1) and ($item['status']==0) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>


             <?php if(($item['status_admin_response']==1 or $item['status_admin2_response']==1) and ($item['status']==1) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

                <?php if(($item['status_admin2_response']==0) and ($item['status']==1) and ($item['status_hr']==1)  and ($item['country_ino'] != 1)  and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>


           <?php if(($item['status_admin2_response']==1) and ($item['status']==1) and ($item['status_hr']==0)  and ($item['country_ino'] != 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

             <?php if(($item['status_admin2_response']==0 and $item['status_admin_response']==0) and ($item['status']==1) and ($item['status_hr']==0)  and ($item['country_ino'] != 1)  and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

           


         <!--****************** BiH  ***************************-->
           
              <?php if(($item['status_admin2_response']==0 and $item['status_admin_response']==0)  and ($item['status_hr']==0)  and ($item['country_ino'] == 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=business_trip&p=popup_business_trip_reponse_admin2&id='.$tools_id; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobravanje zahtjeva"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

                <?php if(($item['status_admin2_response']==0 and $item['status_admin_response']==0)  and ($item['status_hr']==1)  and ($item['country_ino'] == 1)  and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

 
              <?php if( $item['status_admin2_response']==1 and $item['status_admin_response']==0  and ($item['status_hr']==0)  and ($item['country_ino'] == 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?>

 
             <?php if( $item['status_admin2_response']==0 and $item['status_admin_response']==1  and ($item['status_hr']==0)  and ($item['country_ino'] == 1) and ($item['admin2']==$_user['employee_no']) ){ ?>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_view_parent&id='.$tools_id; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>
           <a href="<?php echo '/app/?m=users&p=popup_business_trip_add_edit_admin2&id='.$tools_id; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>
            <?php } ?> 


            
        
        
            </div>
        </div>
      </div>
    </div>

    <?php } } else{ ?>
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


<!-- END - Main section -->

  <?php

    include $_themeRoot.'/footer.php';

   ?>


</body>
</html>
