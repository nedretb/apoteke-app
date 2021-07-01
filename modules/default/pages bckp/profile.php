 <?php
  _pagePermission(5, false);

  error_reporting(0);
 ?>

<!-- START - Main section -->
<body class="bg-rf notable">
<section class="full" style="margin-top:15px;">

<?php 
 if(isset($_GET['u'])){
     $_user = _user($_GET['u']);
     }else{}
	 
	 
		 if($_user['managment_level'] == 6){
		 	 $sel_parent = $_user['employee_no'];
		  } else {
		 	 $sel_parent = $_user['parent'];
		  }
		 
	 
	     $get_parent = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture] FROM  ".$portal_users."  WHERE employee_no='".$sel_parent."' and employee_no<>0");
		 
		 
		
		 
		 
                $get_podredjeni = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE parent='".$sel_parent."'  and (termination_date is NULL or termination_date>getdate())");
				//print_r($get_podredjeni->fetchAll());
				foreach($get_parent as $item){
				  $parent_f = $item['fname'];
				  $parent_l = _optionGetLastNameNAV($sel_parent);
				  $parent_position = $item['position'];
				  $parent_id = $item['user_id'];
				  $parent_image = $item['picture'];

				}

          $get_parent2 = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE employee_no='".$_user['parentMBO2']."' and employee_no<>0");
                $get_podredjeni2 = $db->query("SELECT * FROM  ".$portal_users."  WHERE parent='".$_user['parentMBO2']."' and (termination_date is NULL or termination_date>getdate())");
				foreach($get_parent2 as $item2){
          $parent_f2 = $item2['fname'];
          $parent_l2 = _optionGetLastNameNAV($item2['employee_no']);
				  $parent_position2 = $item2['position'];
           $parent_id2 = $item2['user_id'];
          $parent_image2 = $item2['picture'];
        }

         $get_parent3 = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE employee_no='".$_user['parentMBO3']."' and employee_no<>0");
                $get_podredjeni3 = $db->query("SELECT * FROM  ".$portal_users."  WHERE parent='".$_user['parentMBO3']."'  and (termination_date is NULL or termination_date>getdate())");
				foreach($get_parent3 as $item3){
          $parent_f3 = $item3['fname'];
          $parent_l3 = $item3['lname'];
          $parent_position3 = $item3['position'];
           $parent_id3 = $item3['user_id'];
          $parent_image3 = $item3['picture'];
        }

          $get_parent4 = $db->query("SELECT [user_id], [fname], [lname], [image], [position] , [picture], [employee_no] FROM  ".$portal_users."  WHERE employee_no='".$_user['parentMBO4']."' and employee_no<>0");
                $get_podredjeni4 = $db->query("SELECT * FROM  ".$portal_users."  WHERE parent='".$_user['parentMBO4']."'  and (termination_date is NULL or termination_date>getdate())");
				foreach($get_parent4 as $item4){
          $parent_f4 = $item4['fname'];
          $parent_l4 = _optionGetLastNameNAV($item4['employee_no']);
          $parent_position4 = $item4['position'];
           $parent_id4 = $item4['user_id'];
          $parent_image4 = $item4['picture'];
           }

           $get_parent5 = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE employee_no='".$_user['parentMBO5']."' and employee_no<>0");
                $get_podredjeni5 = $db->query("SELECT * FROM  ".$portal_users."  WHERE parent='".$_user['parentMBO5']."'  and (termination_date is NULL or termination_date>getdate())");
				foreach($get_parent5 as $item5){
          $parent_f5 = $item5['fname'];
          $parent_l5 = _optionGetLastNameNAV($item5['employee_no']);
          $parent_position5 = $item5['position'];
           $parent_id5 = $item5['user_id'];
          $parent_image5 = $item5['picture'];
           }

           $get_parent6 = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE employee_no='".$_user['parentMBO6']."' and employee_no<>0");
                $get_podredjeni6 = $db->query("SELECT * FROM  ".$portal_users."  WHERE parent='".$_user['parentMBO6']."'  and (termination_date is NULL or termination_date>getdate())");
				foreach($get_parent6 as $item6){
          $parent_f6 = $item6['fname'];
          $parent_l6 = _optionGetLastNameNAV($item6['employee_no']);
          $parent_position6 = $item6['position'];
           $parent_id6 = $item6['user_id'];
          $parent_image6 = $item6['picture'];
           }
		   
		    $get = $db->query("SELECT * FROM  ".$nav_employee."  WHERE No_='".$_user['employee_no']."'");
  if($get->rowCount()<0)
  $row_personal = $get->fetch();
	
		$x_user = _user(_decrypt($_SESSION['SESSION_USER']));
	
	
	 
?>

 <style>
    .box {
      background: rgba(255,255,255,.75);
    }
    span{
        line-height: 10px;
        height: 30px;
      }
      body{
        line-height: 1;
        font-family: "Arial";
      }

      .box > .head h3{
  margin:0;
  padding:0;
  font-size:18px;
  line-height: 14px;
  font-weight:400;
}
/*
  .tooltip-static .tooltip {
	  position:static !important;
  }
  body .tooltip {
    position: absolute !important;
}*/
  </style>


	<br/>

  <div class="container-fluid tooltip-static">
  <div id="res"></div>
    <div class="row">
      
		<?php
			if($x_user['user_id'] == $_user['user_id']){
		?>
	 <div class="col-sm-6 satnice-months" style="float: right">
   
        <div class="box">
          <div class="head">
            <h3><?php echo __('Satnice'); ?>
             <?php if (($_user['role'] == '0') or ($_user['role'] == '4')){ 
?>

         <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_year_add_new.php'; ?>" data-widget="ajax" data-id="opt2" data-width="200" class="btn btn-warning btn-sm pull-right" style="background: #006595;color: black;line-height:40%;padding-bottom:2%;padding-top:2%;margin-top: -1.3%;"><?php echo __('Dodaj godinu'); ?>

	 <i style="line-height: 2vw;" class="ion-ios-plus-empty"></i></a>
			
            </h3>
          </div>
          <div class="content">
             
            <div class="row"style="">
              <?php

				$get_year = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'ORDER BY year ASC");
				$get_y = $db->query("SELECT COUNT(*) FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'");
				$result = $get_y->rowCount();
        
				if($result<0){
					 
				foreach($get_year as $year){

                    echo '<div class="col-xs-3 col-sm-3" id="opt-year-'.$year['id'].'">';
                    $get_month = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE year_id='".$year['id']."' AND user_id='".$_user['user_id']."' ORDER BY month ASC");
					
                    echo '<h4>'.$year['year'];
                    
					echo '</h4>';

                    if($get_month->rowCount()<0){

                      echo '<ul >';
                      foreach($get_month as $month){

                          $blue = '';

                          if($month['month'] == date("n") and $year['year'] == date("Y")) {
                              $blue = "class='blue'";
                          }
						echo '<li ' . $blue .  ' id="opt-month-'.$month['id'].'"><a style="color:#5e5d5d;" href="'.$url.'/?m='.$_mod.'&p=hourlyrate_days&year='.$year['id'].'&month='.$month['id'].'">'._nameMonth($month['month']).'</a>';
						echo '</li>';
						}
                      echo '</ul>';

                    }
                   
                    echo '</div>';

                  }

                }else{
                  echo '<div class="text-center">'.__('Još niste počeli unositi satnice').'</div>';
                }

              ?>
            </div>
   
          </div> 
        </div>
        <?php } ?>

          
         <?php if (($_user['role'] == '1') or ($_user['role'] == '2') or ($_user['role'] == '3') or ($_user['role'] == '5')){ ?>


            </h3>
     </div>
          <div class="content">
             
            <div class="row ">
              <?php

                $get_year = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'ORDER BY year ASC");
				$get_y = $db->query("SELECT COUNT(*) FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'");
				$result = $get_y->rowCount();
                

                if($result<0){
					 

                  foreach($get_year as $year){

                    echo '<div class="col-xs-3 col-sm-3" id="opt-year-'.$year['id'].'">';

                    $get_month = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE year_id='".$year['id']."' AND user_id='".$_user['user_id']."' ORDER BY month ASC");
					
                    echo '<h4>'.$year['year'];
                    
                    echo '</h4>';

                    if($get_month->rowCount()<0){

                      echo '<ul>';
                      foreach($get_month as $month){
                          $blue = '';

                          if($month['month'] == date("n") and $year['year'] == date("Y")) {
                              $blue = "class='blue'";
                          }

						echo '<li '.$blue.' id="opt-month-'.$month['id'].'"><a style="color:#5e5d5d;" href="'.$url.'/?m='.$_mod.'&p=hourlyrate_days&year='.$year['id'].'&month='.$month['id'].'">'._nameMonth($month['month']).'</a>';
                        echo '</li>';
						}
                      echo '</ul>';

                    }

                    echo '</div>';

                  }

                }else{
                  echo '<div class="text-center">'.__('Još niste počeli unositi satnice').'</div>';
                }

              ?>
            </div>

          </div>
        
        <?php } ?>

		   <div class="col-sm-14" style="    margin-top: 25px;">
        <div class="box" show=tr >

          <div class="head"  >
		  <h3><?php echo __('Korekcije'); ?>
            
			
            </h3> 
			   </div>
          <div class="content">
             
            <div class="row" >
              <?php

                $get_yearc = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'ORDER BY year ASC");
				$get_yc = $db->query("SELECT COUNT(*) FROM  ".$portal_hourlyrate_year."  WHERE user_id='".$_user['user_id']."'");
				$resultc = $get_yc->rowCount();
        

                if($resultc<0){
					 

                  foreach($get_yearc as $yearc){

                    echo '<div class="col-xs-3 col-sm-3" id="opt-year-'.$yearc['id'].'">';

                   $get_monthc = $db->query("SELECT * FROM  ".$portal_hourlyrate_month_correctoins."  WHERE year_id='".$yearc['id']."' AND user_id='".$_user['user_id']."' ORDER BY month ASC");
                   $get_monthc = $db->query("SELECT * FROM  ".$portal_hourlyrate_month_correctoins."  WHERE year_id='".$yearc['id']."' AND user_id='".$_user['user_id']."' ORDER BY month ASC");
                    echo '<h4>'.$yearc['year'];
                   
					
                    echo '</h4>';

                    if($get_monthc->rowCount()<0){
                     
                      echo '<ul>';
                      foreach($get_monthc as $monthc){
						
					if(($monthc['month']<date("n") or (date("Y")>$yearc['year'])) and ($yearc['year']<=date("Y"))){
                        echo '<li id="opt-month-'.$month['id'].'"><a style="color:#5e5d5d;" href="'.$url.'/?m='.$_mod.'&p=hourlyrate_days_corrections&year='.$yearc['id'].'&month='.$monthc['month'].'">'._nameMonth($monthc['month']).'</a>';
						echo '</li>';
					}

                      }
                      echo '</ul>';

                    }
                    if(($get_monthc->rowCount()<12) and ($_user['role'] == '4')){
                    
                    }
                    echo '</div>';

                  }

                }else{
                  echo '<div class="text-center">'.__('Još niste počeli unositi satnice').'</div>';
                }
			?>
		</div>
    </div>
  </div>
			<?php } ?>

           <?php
           if($x_user['user_id'] == $_user['user_id']){
               ?>
           </div>
               <?php
           }
           ?>



    
	<?php if ($_user['role'] != 4){ ?>
		</div>
    </div>
	<?php } else {
	    ?>
        </div>
    <?php
    } ?>


	<div class="col-sm-6" style="float: left">
        <div class="box">
          <div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a" style="height: 30px;"></a>
						</div>
						<h3><?php echo __('Osnovni podaci'); ?></h3>
					</div>
					<div class="content" id="c1a" style="display: block;padding: 0%;">

            <table class="alt table table-bordered" style = "width: -webkit-fill-available;">
              <tr>
			  <!--- hm 01 ----->
               <td width="30%" class="text-center">
                   <div class="rounded-image erv">
                       <img data-src="<?php echo $_user['picture']; ?>" class=" lazy" style="max-width: 200px;">
                   </div>


               </td>
			   <!--- hm 01 end ----->
                <td width="70%"><b><?php echo $_user['fname'].' '._optionGetLastNameNAV($_user['employee_no']) ; ?></b><br/><br/><?php echo __('Personalni broj'); ?><br/><b><?php echo $_user['employee_no']; ?></b><br/><br/><?php echo __('Dosije broj'); ?><br/><b><?php echo $_user['dosier_no']; ?></b><br/>
</td>
				
				</tr>
             <tr style="line-height: 0.2%;">
                <td style="padding-top: 2%;"><?php echo __('Službeni Telefon'); ?></td>
                <td><div class =  "col-sm-12" style="padding-left: 0%">
         <div class =  "col-sm-3 tooltipabsolute" style="padding-left: 0%">
        
               <select id="Phone_No_Country" name="Phone_No_Country" class="" style = "outline:none;width:100%;" class="form-control">
      <?php echo _optionCountryCodeNAV($row_personal['Country_Region Code Company H'])?>
      </select>
        </div>
        
         <div class =  "col-sm-3 tooltipabsolute" style="padding-left: 0%">
       <select id="Phone_No_Region" name="Phone_No_Region" class=""  style = "outline:none;width:100%;" class="form-control">
      <?php echo _optionRegionCodeNAVHome($row_personal['Dial Code Company Home'])?>
      </select>
        </div>
        
        <div class =  "col-sm-6 tooltipabsolute" style="padding-left: 0%">
        <input type="text" maxlength="8" id ="Phone_No" name="Phone_No" title="Obavezan broj telefona u formatu: 123 456" value="<?php echo $row_personal['Phone No_ for Company']; ?>" class="form-control" style="width:100%;display: inline;height: 20%;padding-top: 1%;padding-bottom: 1%;padding-left: 2px;padding-right: 2px;" onchange="telefeonEdit();" onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'>
        </div>
         </div></td>
              </tr>
			  <tr style="line-height: 0.2%;">
                <td style="padding-top: 2%;"><?php echo __('Službeni Mobitel'); ?></td>
                <td><div class =  "col-sm-12 tooltipabsolute" style="padding-left: 0%">
         <div class =  "col-sm-3" style="padding-left: 0%">
        
               <select id="Phone_No_Country_Mob" name="Phone_No_Country_Mob" class="" style = "outline:none;width:100%;" class="form-control">
      <?php echo _optionCountryCodeNAV($row_personal['Country_Region Code Company M'])?>
      </select>
        </div>
        
         <div class =  "col-sm-3 tooltipabsolute" style="padding-left: 0%">
       <select id="Phone_No_Region_Mob" name="Phone_No_Region_Mob" class="" style = "outline:none;width:100%;" class="form-control">
      <?php echo _optionRegionCodeNAVMobile($row_personal['Dial Code Company Mobile'])?>
      </select>
        </div>
        
        <div class =  "col-sm-6" style="padding-left: 0%">
        <input type="text" maxlength="8" id ="Phone_No_Mob" title="Obavezan broj telefona u formatu: 123 456" name="Phone_No_Mob" value="<?php echo $row_personal['Mobile Phone No_ for Company']; ?>" class="form-control" style="width:100%;display: inline;height: 20%;padding-top: 1%;padding-bottom: 1%; padding-left: 2px;padding-right: 2px;" onchange="telefeonEditMob();" onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode ==32))'>
        </div>
         </div></td>
              </tr>
              <tr>
                <td><?php echo __('Službeni E-mail'); ?></td>
                <td><b><?php echo $_user['email_company']; ?></b></td>
              </tr>

			    <tr>
                <td><?php echo __('Datum zaposlenja'); ?></td>
                <td><b><?php echo date('d.m.Y',strtotime($_user['employment_date'])); ?></b></td>
              </tr>
			   <tr>
                <td><?php echo __('Šifra i naziv radnog mjesta'); ?></td>
                <td><b><?php echo $_user['position_code'].' '.$_user['position']; ?></b></td>
              </tr>
             
			  <tr>
                <td><?php echo __('Nivo rukovodstva'); ?></td>
                <td><b><?php echo _inputNivoRukovodstva($_user['managment_level']); ?></b></td>
              </tr>
               <tr>
                <td><?php echo __('Šifra i naziv sektora'); ?></td>
                <td><b><?php echo $_user['sector'].' '.$_user['B_1_description']; ?></b></td>
              </tr> 
               
               <tr>
                <td><?php echo __('Šifra i naziv odjela'); ?></td>
                <td><b><?php echo $_user['department_code'].' '.$_user['B_1_regions_description']; ?></b></td>
              </tr>
             
                <tr>
                <td><?php echo __('Šifra i naziv grupe'); ?></td>
                <td><b><?php echo $_user['Stream_code'].' '.$_user['Stream_description'] ?></b></td>
              </tr>
			 
                <tr>
                <td><?php echo __('Šifra i naziv tima'); ?></td>
                <td><b><?php echo $_user['Team'].' '.$_user['Team_description']; ?></b></td>
              </tr>
             
			   <tr>
                <td><?php echo __('Mjesto organizacione jedinice'); ?></td>
                <td><b><?php echo $_user['mjesto_org']; ?></b></td>
              </tr>
			  <tr>
                <td><?php echo __('Mjesto rada'); ?></td>
                <td><b><?php echo $_user['mjesto_rada']; ?></b></td>
              </tr>
                <tr>
                <td><?php echo __('Adresa rada'); ?></td>
                <td><b><?php echo $_user['adress_org']; ?></b></td>
              </tr>
			  <?php 
					
					$see_podredjeni = $db->query("SELECT [user_id], [fname], [lname], [image], [position], [picture], [employee_no] FROM  ".$portal_users."  WHERE parent='".$_user['employee_no']."' and (termination_date is NULL or termination_date>getdate())");
					$fetch_podredjene = $see_podredjeni->fetchAll();
					
					$get_podredjeni = $get_podredjeni->fetchAll();
					
          $setted = 0;
					if($_user['managment_level'] == '6' || !empty($fetch_podredjene) ){
          
						if($_user['managment_level'] == '6'){
							
							$get_podredjeni_fetch = $get_podredjeni;
							$p_ceo 					= $parent_id;
							$url_ceo 				= $url;
							$parent_f_ceo 			= $parent_f;
							$parent_l_ceo 			= $parent_l;
							$parent_position_ceo 	= $parent_position;
							$parent_image_ceo 		= $parent_image;
							
						} else if(!empty($fetch_podredjene)){
							$get_podredjeni_fetch = $fetch_podredjene;
							
							$p_ceo 					= $_user['user_id'];
							$url_ceo 				= $url;
							$parent_f_ceo 			= $_user['fname'];
							$parent_l_ceo 			= _optionGetLastNameNAV($_user['employee_no']);
							$parent_position_ceo 	= $_user['position'];
							$parent_image_ceo 		= $_user['picture'];
						}
						
						//print_r($get_podredjeni_fetch);
						
						if(!isset($parent_image)){ $parent_image = ''; }
						
						?>
						<tr onclick="toggleList0();">
						   <td style="">&nbsp;</td>
							<td><b><?php echo '<div class="tooltip"><a href="'.$url_ceo.'/?m=default&p=profile&u='.$p_ceo.'" style="color:blue;">'.$parent_f_ceo.' '.$parent_l_ceo.', '.$parent_position_ceo.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image_ceo.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
					  </tr>
						<?php foreach ($get_podredjeni_fetch as $key=>$podredjen) {  $navlname = _optionGetLastNameNAV($podredjen['employee_no']); ?>
							<tr id="nadredjeni0<?php echo $key;?>">
								<td><b><?php echo '<div class="tooltip" style="position:static;"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
								<td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
							</tr>
							<?php }?>
						<?php
					} 
			  
			  ?>
              <?php if ( (isset($parent_f) and isset($parent_l) and isset($parent_position)) and $parent_id != $_user['user_id']) {?>
              <tr onclick="toggleList1();">
               <td style=""><?php echo 'Prvi nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip" ><a href="'.$url.'/?m=default&p=profile&u='.$parent_id.'" style="color:blue;">'.$parent_f.' '.$parent_l.', '.$parent_position.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
          </tr>
			  	<?php foreach ($get_podredjeni as $key=>$podredjen) { $navlname1 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni1<?php echo $key;?>">
		<td><b><?php echo '<div class="tooltip" style="position:static;"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname1.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
			 <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
			</tr>
			<?php }?>
					<?php }  ?>
			  
			  <?php if ( isset($parent_f2) and isset($parent_l2) and isset($parent_position2) ) {?>
			  <tr onclick="toggleList2();">
               <td style=""><?php echo 'Sljedeći nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$parent_id2.'" style="color:blue;">'.$parent_f2.' '.$parent_l2.', '.$parent_position2.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image2.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
				</tr>
				<?php foreach ($get_podredjeni2 as $key=>$podredjen) { $navlname2 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni2<?php echo $key;?>">
		<td><b><?php echo '<div  class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname2.'</a><span class="tooltiptext"><img src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
		 <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
		</tr>
			<?php }?>
			  <?php } ?>
			  <?php if ( isset($parent_f3) and isset($parent_l3) and isset($parent_position3) ) {?>
			  <tr onclick="toggleList3();">
                <td style=""><?php echo 'Sljedeći nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$parent_id3.'" style="color:blue;">'.$parent_f3.' '.$parent_l3.', '.$parent_position3.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image3.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
              </tr>
			  <?php foreach ($get_podredjeni3 as $key=>$podredjen) { $navlname3 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni3<?php echo $key;?>">
		<td><b><?php echo '<div  class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname3.'</a><span class="tooltiptext"><img src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
			 <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
			</tr>
			<?php }?>
			  <?php } ?>
               <?php if ( isset($parent_f4) and isset($parent_l4) and isset($parent_position) ) {?>
			   <tr onclick="toggleList4();">
               <td style=""><?php echo 'Sljedeći nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$parent_id4.'" style="color:blue;">'.$parent_f4.' '.$parent_l4.', '.$parent_position4.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
              </tr>
			  <?php foreach ($get_podredjeni4 as $key=>$podredjen) {  $navlname4 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni4<?php echo $key;?>">
		<td><b><?php echo '<div  class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname4.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
			 <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
			</tr>
			<?php }?>
			   <?php } ?>
              <?php if ( isset($parent_f5) and isset($parent_l5) and isset($parent_position5) ) {?>
			  <tr onclick="toggleList5();">
                <td style=""><?php echo 'Sljedeći nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$parent_id5.'" style="color:blue;">'.$parent_f5.' '.$parent_l5.', '.$parent_position5.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image5.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
              </tr>
			  <?php foreach ($get_podredjeni5 as $key=>$podredjen) {  $navlname5 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni5<?php echo $key;?>">
		<td><b><?php echo '<div  class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname5.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
			 <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
			</tr>
			<?php }?>
			   <?php } ?>
               <?php if ( isset($parent_f6) and isset($parent_l6) and isset($parent_position6) ) {?>
			   <tr onclick="toggleList6();">
              <td style=""><?php echo 'Sljedeći nadređeni'; ?></td>
                <td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$parent_id6.'" style="color:blue;">'.$parent_f6.' '.$parent_l6.', '.$parent_position6.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$parent_image6.'" style="width:100%;"></span></div><a class="ion-ios-arrow-down" style="float: right;cursor: pointer;" ></a></b><small>';?></td>
              </tr>
			  <?php foreach ($get_podredjeni6 as $key=>$podredjen) { $navlname6 = _optionGetLastNameNAV($podredjen['employee_no']); ?>
			 <tr id="nadredjeni6<?php echo $key;?>">
		<td><b><?php echo '<div  class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['fname'].' '.$navlname6.'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div><br/>';?></td>
			<td><b><?php echo '<div class="tooltip"><a href="'.$url.'/?m=default&p=profile&u='.$podredjen['user_id'].'" style="color:rgba(46, 109, 164, 1);">'.$podredjen['position'].'</a><span class="tooltiptext"><img class="lazy" data-src="'.$podredjen['picture'].'" style="width:100%;"></span></div></b><small>';?></td>
			</tr>
			<?php }?>
			  <?php } ?>
            
              <?php if($_user['dates_reactivate']=='0000-00-00 00:00:00'){ ?>
              <tr>
                <td><?php echo __('Datum registracije'); ?></td>
                <td><b><?php echo date('d/m/Y',strtotime($_user['dates'])); ?></b></td>
              </tr>
              <?php }else{ ?>
              
              <?php } ?>
            </table>
          </div>
        </div>
      </div>


</div>


</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';
	
	if($x_user['user_id'] != $_user['user_id']){
			?>
			<script>
				$("input").attr("disabled", "disabled");
				$("select").attr("disabled", "disabled");
			</script>
			<?php
		}
 ?>

 <script>
 
		/* Phone Number checker */ 
			
		sluzbeni_broj_country		 = $("#Phone_No_Country");
		sluzbeni_broj_region		 = $("#Phone_No_Region");
		sluzbeni_broj_telefona 		 = $("#Phone_No");
		
		if(sluzbeni_broj_country.val() == '' || sluzbeni_broj_region.val() == ''){
			sluzbeni_broj_telefona.attr('disabled', 'disabled');
		}
		
		var ready = 0;
		
		sluzbeni_broj_country.change(function(){
			if($(this).val() == ''){ready--; sluzbeni_broj_telefona.attr('disabled', 'disabled'); }else{ready++;}
			
			if(ready == 2){ sluzbeni_broj_telefona.prop('disabled', false);  }
		});
		sluzbeni_broj_region.change(function(){
			if($(this).val() == ''){ready--; sluzbeni_broj_telefona.attr('disabled', 'disabled'); }else{ready++;}
			if(ready == 2){ sluzbeni_broj_telefona.prop('disabled', false); }
		});
		
		
		/* Phone Number checker */ 
			
		mob_sluzbeni_broj_country		 = $("#Phone_No_Country_Mob");
		mob_sluzbeni_broj_region		 = $("#Phone_No_Region_Mob");
		mob_sluzbeni_broj_telefona 		 = $("#Phone_No_Mob");
		
		if(mob_sluzbeni_broj_country.val() == '' || mob_sluzbeni_broj_region.val() == ''){
			mob_sluzbeni_broj_telefona.attr('disabled', 'disabled');
		}
		
		var mob_ready = 0;
		
		mob_sluzbeni_broj_country.change(function(){
			if($(this).val() == ''){mob_ready--; mob_sluzbeni_broj_telefona.attr('disabled', 'disabled'); }else{if(mob_ready < 2){mob_ready++;}}
			
			if(mob_ready == 2){ mob_sluzbeni_broj_telefona.prop('disabled', false);  }
		});
		mob_sluzbeni_broj_region.change(function(){
			if($(this).val() == ''){mob_ready--; mob_sluzbeni_broj_telefona.attr('disabled', 'disabled'); }else{if(mob_ready < 2){mob_ready++;}}
			if(mob_ready == 2){ mob_sluzbeni_broj_telefona.prop('disabled', false); }
		});
		
		
		
 
 var dtl =  $( "input" );
dtl.tooltip();
	jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

	$('tr:regex(id, .*nadredjeni0.*)').hide();
	$('tr:regex(id, .*nadredjeni1.*)').hide();
	$('tr:regex(id, .*nadredjeni2.*)').hide();
	$('tr:regex(id, .*nadredjeni3.*)').hide();
	$('tr:regex(id, .*nadredjeni4.*)').hide();
	$('tr:regex(id, .*nadredjeni5.*)').hide();
	$('tr:regex(id, .*nadredjeni6.*)').hide();
	
	function toggleList0()
	{
if($('tr:regex(id, .*nadredjeni0.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni0.*)').show();
else
  $('tr:regex(id, .*nadredjeni0.*)').hide();
	}

	function toggleList1()
	{
if($('tr:regex(id, .*nadredjeni1.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni1.*)').show();
else
  $('tr:regex(id, .*nadredjeni1.*)').hide();
	}
	function toggleList2()
	{
	if($('tr:regex(id, .*nadredjeni2.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni2.*)').show();
else
  $('tr:regex(id, .*nadredjeni2.*)').hide();
	}
	function toggleList3()
	{
	if($('tr:regex(id, .*nadredjeni3.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni3.*)').show();
else
  $('tr:regex(id, .*nadredjeni3.*)').hide();
	}
	function toggleList4()
	{
	if($('tr:regex(id, .*nadredjeni4.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni4.*)').show();
else
  $('tr:regex(id, .*nadredjeni4.*)').hide();
	}
	function toggleList5()
	{
	if($('tr:regex(id, .*nadredjeni5.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni5.*)').show();
else
  $('tr:regex(id, .*nadredjeni5.*)').hide();
	}
	function toggleList6()
	{
	if($('tr:regex(id, .*nadredjeni6.*)').is(':hidden'))
  $('tr:regex(id, .*nadredjeni6.*)').show();
else
  $('tr:regex(id, .*nadredjeni6.*)').hide();
	}
	
	function telefeonEdit()
	{
	$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "telefon-edit", request_id : '<?php echo $_user['employee_no']; ?>' , Phone_No_Company_country : $( "#Phone_No_Country option:selected" ).text(), Phone_No_Company_region : $( "#Phone_No_Region option:selected" ).text(),Phone_No_Company : $( "#Phone_No" ).val()}, 
    function(returnedData){
      var obj = jQuery.parseJSON(returnedData);
               $("#res").html(obj.msg);
   setTimeout(function(){ window.location.reload();  }, 500);
});
	}

  function telefeonEditMob()
  {
  $.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "telefon-edit-mob", request_id : '<?php echo $_user['employee_no']; ?>' , Phone_No_Company_country_mob : $( "#Phone_No_Country_Mob option:selected" ).text(), Phone_No_Company_region_mob : $( "#Phone_No_Region_Mob option:selected" ).text(),Phone_No_Company_mob : $( "#Phone_No_Mob" ).val()}, 
    function(returnedData){
     var obj = jQuery.parseJSON(returnedData);
               $("#res").html(obj.msg);
   setTimeout(function(){ window.location.reload();  }, 500);
});
  }

 </script>

<script type="text/javascript" src="theme\js\jquery.lazy.min.js"></script>

<script> $(function() {
       $('.lazy').Lazy(
		{
			delay: 1000
		}
		);
    });
    </script>

</body>
</html>
