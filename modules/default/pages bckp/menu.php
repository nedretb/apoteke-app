
<?php

// Admin menu
function pageMenu(){
  global $_page, $_mod, $_user,$db;
  
  $godina=date("Y");
  
  $tabs_query = $db->query("SELECT * FROM  ".$portal_tabs." ");
		 $tabs = $tabs_query->fetchAll(PDO::FETCH_ASSOC);
		 $tab_hidden = array();
		 $tab_name = array();
		 $tab_roles = array();
		 $tab_icon = array();
		 for($x = 0; $x < count($tabs); $x++) {
			$tab_hidden[$tabs[$x]['Tab']]=$tabs[$x]['Hidden'];
			$tab_name[$tabs[$x]['Tab']]=$tabs[$x]['Name'];
			$tab_icon[$tabs[$x]['Tab']]=$tabs[$x]['Icon'];
			$tab_roles[$tabs[$x]['Tab']] = explode(";", $tabs[$x]['Roles']);
			}
			
	     $zaduznice_query = $db->query("SELECT user_id FROM  ".$portal_zaduznice_header." ");
		 $zaduznice = $zaduznice_query->fetchAll(PDO::FETCH_COLUMN, 0);
		 
		 $programi_query = $db->query("SELECT user_id FROM  ".$portal_training_program_header." ");
		 $programi = $programi_query->fetchAll(PDO::FETCH_COLUMN, 0);
		 
		 
     $pristup_query_zaduznice = $db->query("SELECT count(*) as broj FROM  ".$portal_zaduznice_pristup."  WHERE employee_no = ".$_user['employee_no']);
		 $pristup_zaduznice1 = $pristup_query_zaduznice->fetch();
		 $pristup_zaduznice = $pristup_zaduznice1['broj'];
		 
		 $pristup_query_zaduznice_parent = $db->query("SELECT count(*) as broj FROM  ".$portal_users."  WHERE parent = '".$_user['employee_no']."'");
		 $pristup_zaduznice_parent1 = $pristup_query_zaduznice_parent->fetch();
		 $pristup_zaduznice_parent = $pristup_zaduznice_parent1['broj'];
		 $pristup_programi_parent = $pristup_zaduznice_parent1['broj'];
		 
		 $neodobreno_query=" and review_status = 0";
	     $odobreno_query=" and review_status = 1";
		 $employee_query = " and employee_no =".$_user['employee_no'];

		//za admina
		
		if($_user['role']==4 or $_user['role']==0){
	 $get2 = $db->query("SELECT count(*) FROM  ".$portal_users."  WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)");
	   $result = $get2->fetch();
      $total_users=$result[0];

	  }
	
	//za parenta
	
	elseif($_user['role']==2){
	 $get2 = $db->query("SELECT count(*) FROM  ".$portal_users."  WHERE (parent='".$_user['employee_no']."')");
	   $result = $get2->fetch();
      $total_users=$result[0];
	}
	
	if($total_users>0)
		$show_odsustva = true;
	else
		$show_odsustva = false;
		
	$list_pages = array(
    'default'=>array(
      'name'=>__($tab_name['default']),
      'icon'=>$tab_icon['default'],
      'page'=>'profile',
      'role'=>$tab_roles['default'],
      'subpages'=>array(
     
		'mbo_pocetna'=>array(
          'name'=>__('Moj MBO'),
          'role'=>array()
        ),
		'business_trip'=>array(
        'name'=>__('Moja službena putovanja'),
        'icon'=>'ion-earth',
        'page'=>'all',
        'role'=>array(),
        'subpages'=>array()
    ),
        'trainings'=>array(
        'name'=>__('Moji treninzi'),
        'icon'=>'ion-checkmark',
        'page'=>'all',
        'role'=>array(),
        'subpages'=>array()
    ),
        'edit-profile'=>array(
          'name'=>__('Karton radnika'),
          'role'=>array('0','1','2','3','4','5')
        ),
		'zahtjevi_go' => array(
			'name'=>__('Moji godišnji odmori'),
			'role'=>array('0','1','2','3','4','5')
		),
		'training_programs'=>array(
        'name'=>__('Programi obuke'),
        'icon'=>'ion-university',
        'page'=>'training_programs',
        'role'=>array(),
        'subpages'=>array()
    ),
		'zaduznice'=>array(
        'name'=>__('Zadužnice/Razdužnice'),
        'icon'=>'ion-card',
        'page'=>'zaduznice',
        'role'=>array(),
        'subpages'=>array()
    ),
	'odsustva'=>array(
        'name'=>__('Moj pregled odsustava'),
        'icon'=>'ion-card',
        'page'=>'odsustva',
        'role'=>array('0','1','2','3','4','5','6','7'),
        'subpages'=>array()
    ),
	'odsustva_radnici'=>array(
        'name'=>__('Odsustva radnici'),
        'icon'=>'ion-card',
        'page'=>'odsustva_radnici',
        'role'=>array('0'),
        'subpages'=>array()
    ),
	'odsustva_radnici_corrections'=>array(
        'name'=>__('Odsustva - korekcije'),
        'icon'=>'ion-card',
        'page'=>'odsustva_radnici_corrections',
        'role'=>array('0'),
        'subpages'=>array()
    )
      ),
		'hidden'=>$tab_hidden['default']
		),
		
		'impersonalizacija'=>array(
      'name'=>__($tab_name['impersonalizacija']),
      'icon'=>$tab_icon['impersonalizacija'],
      'page'=>'admin_manager_hourly_rate',
      'role'=>$tab_roles['impersonalizacija'],
      'subpages'=>array(
           'admin_hourly_rate'=>array(
      'name'=>__($tab_name['admin_hourly_rate']),
      'icon'=>$tab_icon['admin_hourly_rate'],
      'page'=>'all',
      'role'=>$tab_roles['admin_hourly_rate'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['admin_hourly_rate']
    ),
	 
	 'admin_hourly_rate_corrections'=>array(
      'name'=>__($tab_name['admin_hourly_rate_corrections']),
      'icon'=>$tab_icon['admin_hourly_rate_corrections'],
      'page'=>'all',
      'role'=>$tab_roles['admin_hourly_rate_corrections'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['admin_hourly_rate_corrections']
    ),
	
		'admin_manager_hourly_rate'=>array(
      'name'=>__($tab_name['admin_manager_hourly_rate']),
      'icon'=>$tab_icon['admin_manager_hourly_rate'],
      'page'=>'all',
      'role'=>$tab_roles['admin_manager_hourly_rate'],
      'subpages'=>array(
	/*   'admin_manager_verification'=>array(
      'name'=>__('Verifikacija satnica'),
      'icon'=>'ion-ios-checkmark',
      'page'=>'all',
      'role'=>array('0','4'),
		) */
		),
	  'hidden'=>$tab_hidden['admin_manager_hourly_rate']
    ),
 
	
	
	'admin_manager_hourly_rate_corrections'=>array(
      'name'=>__($tab_name['admin_manager_hourly_rate_corrections']),
      'icon'=>$tab_icon['admin_manager_hourly_rate_corrections'],
      'page'=>'all',
      'role'=>$tab_roles['admin_manager_hourly_rate_corrections'],
      'subpages'=>array(  
	/*   'admin_manager_verification_corrections'=>array(
      'name'=>__('Verifikacija korekcija'),
      'icon'=>'ion-ios-checkmark',
      'page'=>'all',
      'role'=>array('0','4'),
	) */
	),
	  'hidden'=>$tab_hidden['admin_manager_hourly_rate_corrections']
    ),
	),
		'hidden'=>$tab_hidden['impersonalizacija']
		),
		
		'tasks'=>array(
      'name'=>__($tab_name['tasks']),
      'icon'=>$tab_icon['tasks'],
      'page'=>'all',
      'role'=>$tab_roles['tasks'],
	  'subpages'=>array(),
	  'hidden'=>$tab_hidden['tasks']
    ),
	
	'trainings'=>array(
      'name'=>__($tab_name['trainings']),
      'icon'=>$tab_icon['trainings'],
      'page'=>'all',
      'role'=>$tab_roles['trainings'],
      'subpages'=>array(),
	 'hidden'=>$tab_hidden['trainings']
    ),

	'business_trip'=>array(
      'name'=>__($tab_name['business_trip']),
      'icon'=>$tab_icon['business_trip'],
      'page'=>'all',
      'role'=>$tab_roles['business_trip'],
      'subpages'=>array(),
	 'hidden'=>$tab_hidden['business_trip']
    ),
    
	'users'=>array(
      'name'=>__($tab_name['users']),
      'icon'=>$tab_icon['users'],
      'page'=>'all',
      'role'=>$tab_roles['users'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['users']

    ),
	'employees'=>array(
      'name'=>__($tab_name['employees']),
      'icon'=>$tab_icon['employees'],
      'page'=>'all',
      'role'=>$tab_roles['employees'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['employees']
	),

/*    'admin_hourly_rate'=>array(
      'name'=>__($tab_name['admin_hourly_rate']),
      'icon'=>$tab_icon['admin_hourly_rate'],
      'page'=>'all',
      'role'=>$tab_roles['admin_hourly_rate'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['admin_hourly_rate']
    ),
	 
	 'admin_hourly_rate_corrections'=>array(
      'name'=>__($tab_name['admin_hourly_rate_corrections']),
      'icon'=>$tab_icon['admin_hourly_rate_corrections'],
      'page'=>'all',
      'role'=>$tab_roles['admin_hourly_rate_corrections'],
      'subpages'=>array(),
	  'hidden'=>$tab_hidden['admin_hourly_rate_corrections']
    ), */
	
	'admin_manager_hourly_rate'=>array(
      'name'=>__($tab_name['admin_manager_hourly_rate']),
      'icon'=>$tab_icon['admin_manager_hourly_rate'],
      'page'=>'all',
      'role'=>$tab_roles['admin_manager_hourly_rate'],
      'subpages'=>array(
	/*   'admin_manager_verification'=>array(
      'name'=>__('Verifikacija satnica'),
      'icon'=>'ion-ios-checkmark',
      'page'=>'all',
      'role'=>array('0','4'),
		) */
		),
	  'hidden'=>$tab_hidden['admin_manager_hourly_rate']
    ),
 
	
	
	'admin_manager_hourly_rate_corrections'=>array(
      'name'=>__($tab_name['admin_manager_hourly_rate_corrections']),
      'icon'=>$tab_icon['admin_manager_hourly_rate_corrections'],
      'page'=>'all',
      'role'=>$tab_roles['admin_manager_hourly_rate_corrections'],
      'subpages'=>array(  
	/*   'admin_manager_verification_corrections'=>array(
      'name'=>__('Verifikacija korekcija'),
      'icon'=>'ion-ios-checkmark',
      'page'=>'all',
      'role'=>array('0','4'),
	) */
	),
	  'hidden'=>$tab_hidden['admin_manager_hourly_rate_corrections']
    ),

 
 
	'settings'=>array(
      'name'=>__($tab_name['settings']),
      'icon'=>$tab_icon['settings'],
      'page'=>'general',
      'role'=>$tab_roles['settings'],
      'subpages'=>array(
        'countries'=>array(
          'name'=>__('Države'),
          'role'=>array('0')
        )
      ),
	  'hidden'=>$tab_hidden['settings']
    ),
	 'vacations'=>array(
      'name'=>__($tab_name['vacations']),
      'icon'=>$tab_icon['vacations'],
      'page'=>'vacations',
      'role'=>$tab_roles['vacations'],
      'subpages'=>array(
	   'vacations'=>array(
          'name'=>__('Godišnji odmori'),
          'role'=>array('0')
        ),
	  'absences'=>array(
          'name'=>__('Odsustva'),
          'role'=>array('0')
        ),
		
    ),
	'hidden'=>$tab_hidden['vacations']
  ),
   'training_programs'=>array(
      'name'=>__($tab_name['training_programs']),
      'icon'=>$tab_icon['training_programs'],
      'page'=>'training_programs',
      'role'=>$tab_roles['training_programs'],
      'subpages'=>array(),
	  'hidden'=>$tabs[12]['Hidden'])
	  ,
	   'zaduznice'=>array(
      'name'=>__($tab_name['zaduznice']),
      'icon'=>$tab_icon['zaduznice'],
      'page'=>'zaduznice',
      'role'=>$tab_roles['zaduznice'],
       'subpages'=>array(
	    'zaduznice'=>array(
          'name'=>__('Zadužnice'),
          'role'=>array('0','1','2','3','4','5')
        ),
		'razduznice'=>array(
          'name'=>__('Razdužnice'),
          'role'=>array('0','1','2','3','4','5')
        ),
	  'zaduznice_pristupi'=>array(
          'name'=>__('Pristupi'),
          'role'=>array('0')
        )
    ),
	  'hidden'=>$tab_hidden['zaduznice']
	  ),
	  'org_chart'=>array(
      'name'=>__($tab_name['org_chart']),
      'icon'=>$tab_icon['org_chart'],
      'page'=>'org_chart',
      'role'=>$tab_roles['org_chart'],
      'subpages'=>array(),
	'hidden'=>$tab_hidden['org_chart']
  ),
    'calendar'=>array(
      'name'=>__($tab_name['calendar']),
      'icon'=>$tab_icon['calendar'],
      'page'=>'calendar',
      'role'=>$tab_roles['calendar'],
      'subpages'=>array(),
	'hidden'=>$tab_hidden['calendar']
  ),
	'zahtjevi'=>array(
      'name'=>__($tab_name['zahtjevi']),
      'icon'=>$tab_icon['zahtjevi'],
      'page'=>'calendar',
      'role'=>$tab_roles['zahtjevi'],
      'subpages'=>array('0'),
	'hidden'=>$tab_hidden['zahtjevi']
  ),
     'zahtjevi_radnika'=>array(
      'name'=>__($tab_name['zahtjevi_radnika']),
      'icon'=>$tab_icon['zahtjevi_radnika'],
      'page'=>'calendar',
      'role'=>$tab_roles['zahtjevi_radnika'],
      'subpages'=>array('0'),
	'hidden'=>$tab_hidden['zahtjevi_radnika']
  )
)
	  ;
  
	  $vacation_visible = false;
  if($_user['B_1_description']=='Uprava' or $_user['user_id']=='273' or $_user['managment_level']=='2' or $_user['managment_level']=='3' or $_user['managment_level']=='4')
	  $vacation_visible = true;
  
     $zaduznice_visible = false;
  if($pristup_zaduznice>0 or $pristup_zaduznice_parent>0)
	  $zaduznice_visible = true;
  
    $programi_visible = false;
  if($pristup_programi_parent>0)
	  $programi_visible = true;
	
	$zaduznice_visible_sub = false;
  if(in_array($_user['user_id'], $zaduznice))
	  $zaduznice_visible_sub = true;
  
  $zaduznice_pristupi_visible_sub = false;
  if($_user['employee_no']=='473' or $_user['employee_no']=='568' or $_user['employee_no']=='108')
	  $zaduznice_pristupi_visible_sub = true;
  
     $programi_visible_sub = false;
  if(in_array($_user['user_id'], $programi))
	  $programi_visible_sub = true;

  $menu = '<ul>';
  $i		= 0;

  foreach($list_pages as $slug=>$pages){

    $i++;
    $count_sub = count($pages['subpages']);
    if($_mod==$slug){
      $sel1 = ' class="current"';
    }else{
      $sel1 = '';
    }

    if((in_array($_user['role'], $pages['role']) or ($slug=='impersonalizacija' and $show_odsustva and $_user['role']==2) or ($slug=='tasks' and $_user['MBO']) or ($slug=='vacations' and $vacation_visible) or ($slug=='zaduznice' and $zaduznice_visible) or ($slug=='training_programs' and $programi_visible) or ($slug=='org_chart' and $_user['managment_level']!=0)) and !$pages['hidden']){

      if($slug=='impersonalizacija' and $_user['role']==2)
	  $menu .= '<li style="font-weight: bold;"'.$sel1.'><a href="?m=admin_hourly_rate&p=all"><i class="'.$pages['icon'].'"></i> <span>'.$pages['name'].'</span></a>';
	  elseif($slug=='impersonalizacija' and $_user['role']==4)
	  $menu .= '<li style="font-weight: bold;"'.$sel1.'><a href="?m=admin_manager_hourly_rate&p=all"><i class="'.$pages['icon'].'"></i> <span>'.$pages['name'].'</span></a>';
	  elseif($slug=='zahtjevi')
	  $menu .= '<li id="requests" style="font-weight: bold;"'.$sel1.'><a href="?m=default&p=odsustva&odobreno=true"><i class="'.$pages['icon'].'"></i> <span>'.$pages['name'].'</span></a>';
	  elseif($slug=='zahtjevi_radnika'){
	  if($show_odsustva)
	  $menu .= '<li id="requests_workers" style="font-weight: bold;"'.$sel1.'><a href="?m=default&p=odsustva_radnici&neodobreno=true"><i class="'.$pages['icon'].'"></i> <span>'.$pages['name'].'</span></a>';
	  }
	  else
	  $menu .= '<li style="font-weight: bold;"'.$sel1.'><a href="?m='.$slug.'&p='.$pages['page'].'"><i class="'.$pages['icon'].'"></i> <span>'.$pages['name'].'</span></a>';

	  if($slug=='zahtjevi'){
		  $menu .= '<i class="ion-ios-arrow-down pull-right show-ul" id="ul'.$i.'"></i>';
		  $menu .= '<ul id="ul'.$i.'">';
		  $menu .= '<li id="absence_count1" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva&odobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moji odobreni zahtjevi</span><div id="noti_Counter10"></div></a></li>';
	  $menu .= '<li id="absence_count2" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva&odobreno=false" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moji neodobreni zahtjevi</span><div id="noti_Counter11"></div></a></li>';
	  $menu .= '<li id="absence_count3" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva&odobreno=rejected" style="text-decoration:none;line-height:38px;"><i class="ion-close-circled"></i> <span style="margin-right:12%;">Moji odbijeni zahtjevi</span><div id="noti_Counter12"></div></a></li>';
	  $menu .= '<li id="absence_count4" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva&odobreno_cancel=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moja odobrena otkazivanja</span><div id="noti_Counter13"></div></a></li>';
	  $menu .= '<li id="absence_count5" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva&odobreno_cancel=false" style="text-decoration:none;line-height:38px;"><i class="ion-close-circled"></i> <span style="margin-right:12%;">Moja neodobrena otkazivanja</span><div id="noti_Counter14"></div></a></li>';
	  $menu .= '</ul>';
	  }
	  elseif($slug=='zahtjevi_radnika'){
			if($show_odsustva==true){
		$menu .= '<i class="ion-ios-arrow-down pull-right show-ul" id="ul'.$i.'"></i>';
		  $menu .= '<ul id="ul'.$i.'">';
		  
		  	  $menu .= '<li id="absence_count6" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="margin-right:12%;">Nova odsustva</span><div id="noti_Counter1"></div></a></li>';
	  $menu .= '<li id="absence_count7" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici_corrections&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="margin-right:12%;">Nova odsustva korekcije</span><div id="noti_Counter6"></div></a></li>';
	  $menu .= '<li id="absence_count8" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici&zahtjevi=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="margin-right:12%;">Zahtjevi otkazivanje</span><div id="noti_Counter2"></div></a></li>';
	  $menu .= '<li id="absence_count9" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici_corrections&zahtjevi=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="margin-right:12%;">Zahtjevi otkazivanje korekcije</span><div id="noti_Counter5"></div></a></li>';
		  
		   $menu .= '</ul>';
		   }
	  }
      elseif($count_sub>0){
		//echo $pages['name'].'denis';
        
		$menu .= '<i class="ion-ios-arrow-down pull-right show-ul" id="ul'.$i.'"></i>';
        $menu .= '<ul id="ul'.$i.'">';
        $menu .= '<li class="main"><a href="?m='.$slug.'&p='.$slug.'">'.$pages['name'].'</a></li>';

        foreach($pages['subpages'] as $slug_sub=>$pages_sub){

          if($_page==$slug_sub){
            $sel2 = ' class="current"';
          }else{
            $sel2 = '';
          }

		
		 if($show_odsustva and $_user['role']==2 and $slug_sub=='admin_hourly_rate'  )
            $menu .= '<li style="width:175px;"'.$sel2.'><a href="?m=admin_hourly_rate&p=all">'.$pages_sub['name'].'</a></li>';
		if($show_odsustva and $_user['role']==2 and $slug_sub=='admin_hourly_rate_corrections' )
            $menu .= '<li style="width:175px;"'.$sel2.'><a href="?m=admin_hourly_rate_corrections&p=all">'.$pages_sub['name'].'</a></li>';
		
			 if($show_odsustva and $_user['role']==4 and $slug_sub=='admin_manager_hourly_rate')
            $menu .= '<li style="width:175px;"'.$sel2.'><a href="?m=admin_manager_hourly_rate&p=all">'.$pages_sub['name'].'</a></li>';
		if($show_odsustva and $_user['role']==4 and $slug_sub=='admin_manager_hourly_rate_corrections' )
            $menu .= '<li style="width:175px;"'.$sel2.'><a href="?m=admin_manager_hourly_rate_corrections&p=all">'.$pages_sub['name'].'</a></li>';
          
		  
		 elseif(in_array($_user['role'], $pages_sub['role']) or (($slug_sub=='absences' or $slug_sub=='vacations') and $vacation_visible) or ($slug_sub=='zaduznice' and $zaduznice_visible_sub) or ($slug_sub=='zaduznice_pristupi' and $zaduznice_pristupi_visible_sub) or ($slug_sub=='training_programs' and $programi_visible_sub) or ($slug_sub=='odsustva_radnici' and $show_odsustva) or ($slug_sub=='odsustva_radnici_corrections' and $show_odsustva)){
            $menu .= '<li style="width:175px;"'.$sel2.'><a href="?m='.$slug.'&p='.$slug_sub.'">'.$pages_sub['name'].'</a></li>';
          }

        }
		$menu .= '</ul>';
	}
$menu .='</li>';

    }

  }
  
 /*  if($show_odsustva==true){
	  $menu .= '<li id="absence_count6" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="margin-right:12%;">Nova odsustva</span><div id="noti_Counter1">'.$total_absences.'</div></a></li>';
	  $menu .= '<li id="absence_count7" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici_corrections&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="margin-right:12%;">Nova odsustva korekcije</span><div id="noti_Counter6">'.$total_absences_corr.'</div></a></li>';
	  $menu .= '<li id="absence_count8" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici&zahtjevi=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="margin-right:12%;">Zahtjevi otkazivanje</span><div id="noti_Counter2">'.$total_change_req.'</div></a></li>';
	  $menu .= '<li id="absence_count9" style="font-weight: bold;margin-left: 0px;width: 200px;"><a href="?m=default&p=odsustva_radnici_corrections&zahtjevi=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="margin-right:12%;">Zahtjevi otkazivanje korekcije</span><div id="noti_Counter5">'.$total_change_req_corr.'</div></a></li>';
	  } */
	  /* $menu .= '<li id="absence_count" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva&odobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moji odobreni zahtjevi</span><div id="noti_Counter3">'.$total_accepted.'</div></a></li>';
	  $menu .= '<li id="absence_count" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva&odobreno=false" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moji neodobreni zahtjevi</span><div id="noti_Counter7">'.$total_pending.'</div></a></li>';
	  $menu .= '<li id="absence_count" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva&odobreno=rejected" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moji odbijeni zahtjevi</span><div id="noti_Counter7">'.$total_rejected.'</div></a></li>';
	  $menu .= '<li id="absence_count" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva&odobreno_cancel=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moja odobrena otkazivanja</span><div id="noti_Counter4">'.$total_accepted_cancel.'</div></a></li>';
	  $menu .= '<li id="absence_count" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva&odobreno_cancel=false" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="margin-right:12%;">Moja neodobrena otkazivanja</span><div id="noti_Counter4">'.$total_rejected_cancel.'</div></a></li>'; */
  $menu .= '</ul>';
  

  return $menu;
}

?>



