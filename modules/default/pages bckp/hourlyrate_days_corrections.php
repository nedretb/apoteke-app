<?php
  _pagePermission(6, false);

  $item = $_user;

  $get_year  = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE id='".$_GET['year']."'");
  $get_month = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE id='".$_GET['month']."'");

  $get_week = $db->query("SELECT [Weekday] FROM  ".$portal_calendar."  WHERE Month='".$_GET['month']."'");
  
  $get_y  = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_year."  WHERE id='".$_GET['year']."'");
  $get_m = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_month."  WHERE id='".$_GET['month']."'");
  
  $result = $get_y->fetch();
  $total=$result[0];
  $result2 = $get_m->fetch();
  $total2=$result2[0];
  if($total>0 || $total2>0){
 
    $year  = $get_year->fetch();
    $month = $get_month->fetch();
	
	$number_of_days = cal_days_in_month(CAL_GREGORIAN, $month['month'], $year['year']);
	
	
  
 ?>
 
  <?php $edit = $db->query("SELECT editable_corrections FROM  ".$portal_hourlyrate_day."  WHERE year_id='".$year['id']."' AND month_id='".$month['id']."' AND employee_no='".$_user['employee_no']."' "); ?>

  <?php if($edit->rowCount()<0){
   foreach($edit as $valueedit) {$visible=$valueedit['editable_corrections'];}
  }
  else
	$visible='N'; ?>

<!-- START - Main section -->
<section class="full">

  <div class="container" style="width:80%;">

	<div class="row">

      <div class="col-sm-3">
        
		<h2 style="margin-bottom:-40px;">
		
          <?php echo __('Moje korekcije').' '; ?>
		 </h2>
		  <br />
		  <br />
		  <br />
		  <!--- '.$month['month'].'/'.$year['year'] --->
		  <select onChange="changeMonth();" class="month-select">
		  <?php 
			for($i = 1; $i < date("n"); $i++){ 
			?>
				<option value="<?php echo $i; ?>" <?php if($i == $month['month']){ echo "selected='selected'";} ?>><?php echo $i; ?>/<?php echo $year['year']; ?> </option>
			<?php			
			}
		  ?>
		  </select>
       
      </div>
      <div class="col-sm-9"><br/>
        <div class="pull-right">
		<?php if(($visible!='N')) {?>
          <!--<a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_day_add.php?year='.$year['id'].'&month='.$month['id']; ?>" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-lg"><?php echo __('Ažuriraj satnice'); ?> <i class="ion-ios-plus-empty"></i></a>-->
                <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_day_add_apsolute_corrections.php?year='.$year['id'].'&month='.$month['id']; ?>" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-lg"><?php echo __('Ažuriraj satnice'); ?> <i class="ion-ios-plus-empty"></i></a>
				<a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_day_cancel_apsolute_corrections.php?year='.$year['id'].'&month='.$month['id']; ?>" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-lg"><?php echo __('Otkaži registraciju'); ?> <i class="ion-ios-plus-empty"></i></a>
      <?php } {?>
        
      

		</div>
      </div>
	</div>
	
   
    <?php
  }
		$get_days = $db->query("SELECT * FROM  ".$portal_hourlyrate_day."  WHERE year_id='".$year['id']."' AND month_id='".$month['id']."' AND user_id='".$_user['user_id']."' ORDER BY day");
	  
	  $get_termination = $db->query("select [termination_date] as termination_date,[employment_date] as employment_date from  ".$portal_users."  where user_id = ".$_user['user_id']);
      $termination = $get_termination->fetch();
      
	   if($get_days->rowCount()<0){ ?>
       <div class="row">

			    <form id="popup_form1" method="post">

	  <input type="hidden" name="get_month" value="<?php  echo $_GET['month'];?>"/>
      <input type="hidden" name="get_year" value="<?php  echo $_GET['year'];?>"/>




</form>
<br />
           <div class="box" style="padding: 10px 15px;">
			
			<?php //print_r(_statsDays($year['id'],$month['id'],$_user['user_id'])); 
				 
				 if(isset($_POST['dateFrom']))
				 $month_from = date("n", strtotime(str_replace("/","-",$_POST['dateFrom'])));
			 else
				 $month_from = $month['month'];
				
				if(isset($_POST['dateTo']))
				$month_to = date("n", strtotime(str_replace("/","-",$_POST['dateTo'])));
			else
				$month_to = $month['month'];
	 
	 
	 if(isset($_POST['dateFrom']))
	 $day_from = date("j", strtotime(str_replace("/","-",$_POST['dateFrom'])));
	 else
		$day_from = 1; 
     
	 if(isset($_POST['dateTo']))
	 $day_to = date("j", strtotime(str_replace("/","-",$_POST['dateTo'])));
	 else
		$day_to = $number_of_days;  
				
				print_r(_statsDaysFreeCorrections($year['id'],$month_from,$month_to,$day_from,$day_to)); 
			?>
           </div>

	   
	   <?php

		$br_sati = $_user['br_sati'];
		 } ?>

           <div class="box" style="padding: 10px 15px;">
      <?php
      include_once('modules/core/Model.php');
      include_once('modules/core/VS.php');
      include_once('modules/core/User.php');

      $kvote = User::kvoteSatnice($_user['employee_no'], $year['year'], 'korekcije');

      include('modules/core/views/kvote.satnice.aurora.php');

      ?>
           </div>




      <?php
	  if($get_days->rowCount()<0){
        
        echo '<div class="box days">';
        foreach ($get_days as $day) {

          if($day['corr_review_status']=='0'){
            $css_border = '';


          }elseif($day['corr_review_status']=='1'){
            $css_border = 'style="border-bottom-color:#00cc00;"';


          }elseif($day['corr_review_status']=='2'){
            $css_border = 'style="border-bottom-color:#cc0000;"';
          }



  if ($day['day']=='1'){
  switch ($day['weekday']) 
  {

   case '1': ?>
   
    <?php break;
   case '2': ?>
     <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <?php break; 
   case '3':?>
      <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
      <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
     
    <?php break;
  case '4': ?>
     <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
     <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
     <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
   
    <?php break;
   case '5': ?>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    
     <?php     break;
  case '6': ?>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>

      
      <?php   break;
  case '7': ?>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>
    <div class="day" style="height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'></div>

   
       

    <?php break;

  }
}



      if(rtrim(_nameHRstatusGroup($day['corr_status']))=='Bolovanje' and $day['corr_review_status']=='1' )
            $additional_background = 'background-color:blue';
    elseif(rtrim(_nameHRstatusGroup($day['corr_status']))=='Godišnji odmor' and $day['corr_review_status']=='1')
            $additional_background = 'background-color:lightblue';
      //novi kod 
		elseif(($day['corr_status']=='83') and $day['corr_review_status']=='1')
            $additional_background = 'background-color:#ffb366';
			else
			$additional_background = '';
			//kraj novog koda

   if(($termination['termination_date']!='' and $day['Date'] >$termination['termination_date']) or ($termination['employment_date']!='' and $day['Date'] <$termination['employment_date'])){
       echo '<div class="day" style="background-color: #de8b8b;height:200px ;font-size:11px;"  id="opt-'.$day['id'].'" '.$css_border.'>';}
		else{
      if(($day['weekday']=='6') or ($day['weekday']=='7') ){
		 echo '<div class="day" style="background-color:#DCD6D6;height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'>';}
	  
	  if(($day['weekday']!='6') and ($day['weekday']!='7') and ($day['KindofDay']!='BHOLIDAY')){
		 echo '<div class="day" style="height:200px;'.$additional_background.'"  id="opt-'.$day['id'].'" '.$css_border.'>';}
		 
		 if(($day['weekday']!='6') and ($day['weekday']!='7') and ($day['KindofDay']=='BHOLIDAY') and (rtrim(_nameHRstatusGroup($day['corr_status']))=='Bolovanje' or rtrim(_nameHRstatusGroup($day['corr_status']))=='Godišnji odmor')){
		 echo '<div class="day" style="height:200px;'.$additional_background.'"  id="opt-'.$day['id'].'" '.$css_border.'>';}
		
		  if(($day['weekday']!='6') and ($day['weekday']!='7') and ($day['KindofDay']=='BHOLIDAY') and (rtrim(_nameHRstatusGroup($day['corr_status']))!='Bolovanje' and rtrim(_nameHRstatusGroup($day['corr_status']))!='Godišnji odmor')){
			  
			  /* Test 110 */
					$color_not_praznik = getBoja($day['corr_status']);
				/* Test 110 */
		 echo '<div class="day" style="background-color: '.$color_not_praznik.';height:200px;"  id="opt-'.$day['id'].'" '.$css_border.'>';}
     }
		 
		 if($day['hour_pre']!='' and $day['hour_pre']!='0'){
			  $prekovremeno = '* '._nameHRstatus($day['corr_pre']);
			  $prekovremeno =  '<span style="color:red">'.$prekovremeno.'</span><br/>';
		 }			  
		  else
			  $prekovremeno = '';
		  
		    if($day['corr_status']!=$day['status'] and $day['corr_review_status']=='0')
				$reg = 'Reg:'._nameHRstatus($day['corr_status']);
			else
				$reg = '';
          
		  if($day['corr_review_status']=='1')
			 $corr_status = _nameHRstatus($day['corr_status']);
		 else
			 $corr_status = _nameHRstatus($day['status']);

       if($termination['termination_date']!='' and $day['Date'] >$termination['termination_date']) {$status_prekid = 'Prekid rada<br/>';  } else if($termination['employment_date']!='' and $day['Date'] <$termination['employment_date']){$status_prekid = 'Nezaposlen/a<br/>';} elseif($day['corr_review_status']=='1'){$status_prekid = _nameHRstatus($day['corr_status']).'<br/>';}else{$status_prekid = _nameHRstatus($day['corr_status']).'<br/>';}
		  
 if($day['corr_status']!=$day['status'] and $day['corr_review_status']=='0' and $day['corr_status'] != '5'){ 
			 $status_prekid = '';
		 }
		   if($day['corr_status']=='83')
			$status_prekid = '';
		  if($day['KindofDay']=='BHOLIDAY' and $day['corr_review_status']=='1' and in_array($day['corr_status'], array(21,22,43,44,45,61,62,65,67,68,69,74,75,76,77,78,107,108,73,81))){
			 $day['Description'] = '';
		 }		 
		 
		 if($day['corr_review_status'] == 1 and $day['corr_status'] == 83){
			$status_prekid = _nameHRstatus($day['status']);
		 }

		 echo '<big>'.$day['day'].'</big>';
		 if(($day['weekday']!='6') and ($day['weekday']!='7')){
			 $dayname='';
			          switch ($day['weekday']) {
   case 1:
         $dayname='Ponedjeljak';
		 break;
   case 2:
         $dayname='Utorak';
		 break;
   case 3:
         $dayname='Srijeda';
		 break;
	case 4:
         $dayname='Četvrtak';
		 break;
   case 5:
         $dayname='Petak';
		 break;
}
	if($day['Description'] != ""){
		$day['Description'] = "<br />" . $day['Description'];
	}
	echo '<small style="height:80px;">'.$day['day'].'.'.$month['month'].'.'.$year['year'].' <b>'.$day['Description'].'</b><br/><b>'.$dayname.'<br/>'.$status_prekid.$prekovremeno.$reg.'</b></small>';}
	
	if($day['Description'] != ""){
		$day['Description'] = "<br />" . $day['Description'];
	}
		  
       if(($day['weekday']=='6') and !in_array($day['corr_status'], array(73,81)) and (($day['hour_pre']=='' and $day['hour']==0) or !in_array($day['corr_status'], array(5,85,86,87,88,89,90)))){
		   echo '<small>'.$day['day'].'.'.$month['month'].'.'.$year['year'].'<br/><b>Subota</b></small>';}
		       elseif(($day['weekday']=='6') and (($day['hour_pre']!='' and $day['hour_pre']!='0') or ($day['hour']!=0 and in_array($day['corr_status'], array(5,85,86,87,88,89,90))) or in_array($day['corr_status'], array(73,81))) ){
		   echo '<small style="height:80px;">'.$day['day'].'.'.$month['month'].'.'.$year['year'].' <b>'.$day['Description'].'</b><br/><b>'.'Subota<br/>'.$status_prekid.$prekovremeno.$reg.'</b></small>';}
		      elseif(($day['weekday']=='7') and !in_array($day['corr_status'], array(73,81)) and  (($day['hour_pre']=='' and $day['hour']==0) or !in_array($day['corr_status'], array(5,85,86,87,88,89,90)))){
		   echo '<small>'.$day['day'].'.'.$month['month'].'.'.$year['year'].'<br/><b>Nedjelja</b></small>';
		   }
		  elseif(($day['weekday']=='7') and (($day['hour_pre']!='' and $day['hour_pre']!='0') or ($day['hour']!=0 and in_array($day['corr_status'], array(5,85,86,87,88,89,90))) or in_array($day['corr_status'], array(73,81)))){
		   echo '<small style="height:80px;">'.$day['day'].'.'.$month['month'].'.'.$year['year'].' <b>'.$day['Description'].'</b><br/><b>'.'Nedjelja<br/>'.$status_prekid.$prekovremeno.$reg.'</b></small>';}
          echo '<div>';
          if(($day['corr_review_status'] != '0' and ($day['KindofDay']!='BHOLIDAY')) and (($termination['termination_date']=='' or ($termination['termination_date']!='' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >=$termination['employment_date'])) ){
			  
			 // if(($day['weekday'] == '6' or $day['weekday'] == '7') and !in_array($day['status'], array(67, 73, 81)) and $day['status'] < 85 and $day['status'] > 96 and ($day['hour'] == 0 and $day['status'] == 5)){
				if(($day['weekday'] == '6' or $day['weekday'] == '7') and isAllowedStatusWeekend($day['corr_status']) == false){ 
			 } else {
				 echo '<a href="'.$url.'/modules/'.$_mod.'/pages/popup_day_view_corrections.php?id='.$day['id'].'" class="table-btn" data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-eye"></i></a>';
			 }
			  
           
          }
          if(($day['corr_review_status']=='0') and ($day['weekday']!='6') and ($day['weekday']!='7') and ($day['KindofDay']=='BANKDAY' or ($day['KindofDay']=='BHOLIDAY' and $day['corr_review_status']=='0')) and  ($visible!='N') and (($termination['termination_date']=='' or ($termination['termination_date']!='' and $day['Date'] <= $termination['termination_date'])) and ($day['Date'] >=$termination['employment_date']))){
            echo '<a href="'.$url.'/modules/'.$_mod.'/pages/popup_day_edit_corrections.php?id='.$day['id'].'" class="table-btn" data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-edit"></i></a>';
          }
          echo '</div>';
          echo '</div>';
}
        echo '</div>';

      }else{
        echo '<div class="text-center">'.__('Još nije bilo unosa za:').' '.$month['month'].'/'.$year['year'].'</div>';
      }

     ?>

</div>

</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

  }else{
    echo '<script>window.location.href="'.$url.'/modules/default/unauthorized.php";</script>';
  }

 ?>
 
     <script>
	 function changeMonth(){
		 mon = jQuery(".month-select").val();
		 
		 window.location = "index.php?m=default&p=hourlyrate_days_corrections&year=<?php echo $year['id']; ?>&month="+mon;
	 }
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}     

	 $( document ).ready(function(){
  var today = new Date();
  		var startDate = new Date();
  		$('#dateODMain').datepicker({
  			todayBtn: "linked",
  			format: 'dd/mm/yyyy',
			language: 'bs',
  			//startDate: startDate,
			endDate: new Date('2017/12/31')
  		});   
	$('#dateDOMain').datepicker({
  			todayBtn: "linked",
  			format: 'dd/mm/yyyy',
			language: 'bs',
  			//startDate: startDate,
			endDate: new Date('2017/12/31')
  		}); 	
	
	});
 
    </script>

</body>
</html>
