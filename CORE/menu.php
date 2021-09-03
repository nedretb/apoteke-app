<?php
//require 'config-urls.php';
// Admin menu
function pageMenu()
{
    require 'config-urls.php';
    global $_page, $_mod, $_user, $db;

    $godina = date("Y");

    $tabs_query = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[Tabs]");
    $tabs = $tabs_query->fetchAll(PDO::FETCH_ASSOC);
    $tab_hidden = array();
    $tab_name = array();
    $tab_roles = array();
    $tab_icon = array();
    for ($x = 0; $x < count($tabs); $x++) {
        $tab_hidden[$tabs[$x]['Tab']] = $tabs[$x]['Hidden'];
        $tab_name[$tabs[$x]['Tab']] = $tabs[$x]['Name'];
        $tab_icon[$tabs[$x]['Tab']] = $tabs[$x]['Icon'];
        $tab_roles[$tabs[$x]['Tab']] = explode(";", $tabs[$x]['Roles']);
    }

    $pristup_query_zaduznice_parent = $db->query("SELECT count(*) as broj FROM  " . $portal_users . "  WHERE parent = '" . $_user['employee_no'] . "'");
    $pristup_zaduznice_parent1 = $pristup_query_zaduznice_parent->fetch();
    $pristup_programi_parent = $pristup_zaduznice_parent1['broj'];

    //za admina
    $condition_admin_uprave = "";

    if ($_user['role'] == 4 or $_user['role'] == 0) {
        $get2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)");
        $result = $get2->fetch();
        $total_users = $result[0];

    } //za parenta

    elseif ($_user['role'] == 2) {
        $get2 = $db->query("SELECT count(*) FROM  " . $portal_users . "  WHERE rukovodioc='DA' and employee_no='" . $_user['employee_no']."'");
        $result = $get2->fetch();
        $total_users = $result[0];
    }


    if ($total_users > 0)
        $show_odsustva = true;
    else
        $show_odsustva = false;

    if ($show_odsustva) $zahtjevi_go_radnici_role = $_user['role']; else $zahtjevi_go_radnici_role = 69;


    $list_pages = array(
        'default' => array(
            'name' => ___($tab_name['default']),
            'icon' => $tab_icon['default'],
            'page' => 'profile',
            'role' => $tab_roles['default'],
            'subpages' => array(
                'trainings' => array(
                    'name' => ___('Moji treninzi'),
                    'icon' => 'ion-checkmark',
                    'page' => 'all',
                    'role' => array(),
                    'subpages' => array()
                ),
                'edit-profile' => array(
                    'name' => ___('Karton radnika'),
                    'role' => array('0', '1', '2', '3', '4', '5')
                ),
                'zahtjevi_go' => array(
                    'name' => ___('Moji godišnji odmori'),
                    'role' => array('0', '1', '2', '3', '4', '5')
                ),
                'zahtjevi_go_radnici' => array(
                    'name' => ___('Godišnji odmor - radnici'),
                    'role' => array($zahtjevi_go_radnici_role)
                ),
                'otkazani_zahtjevi' => array(
                    'name' => ___('Moji otkazani zahtjevi'),
                    'role' => array('0', '1', '2', '3', '4', '5'),
                    'url' => '?m=default&p=odsustva&odobreno_cancel=true&title=profile'
                ),
                'training_programs' => array(
                    'name' => ___('Programi obuke'),
                    'icon' => 'ion-university',
                    'page' => 'training_programs',
                    'role' => array(),
                    'subpages' => array()
                ),
                'zaduznice' => array(
                    'name' => ___('Zadužnice/Razdužnice'),
                    'icon' => 'ion-card',
                    'page' => 'zaduznice',
                    'role' => array(),
                    'subpages' => array()
                ),
                'odsustva' => array(
                    'name' => ___('Moj pregled odsustava'),
                    'icon' => 'ion-card',
                    'page' => 'odsustva',
                    'role' => array('0', '1', '2', '3', '4', '5', '6', '7'),
                    'subpages' => array()
                ),
                'odsustva_radnici' => array(
                    'name' => ___('Odsustva radnici'),
                    'icon' => 'ion-card',
                    'page' => 'odsustva_radnici',
                    'role' => array('0'),
                    'subpages' => array()
                ),
                'odsustva_radnici_corrections' => array(
                    'name' => ___('Odsustva - korekcije'),
                    'icon' => 'ion-card',
                    'page' => 'odsustva_radnici_corrections',
                    'role' => array('0, 4'),
                    'subpages' => array()
                )
            ),
            'hidden' => $tab_hidden['default']
        ),

        'impersonalizacija' => array(
            'name' => ___($tab_name['impersonalizacija']),
            'icon' => $tab_icon['impersonalizacija'],
            'page' => 'admin_manager_hourly_rate',
            'role' => $tab_roles['impersonalizacija'],
            'subpages' => array(
                'admin_hourly_rate' => array(
                    'name' => ___($tab_name['admin_hourly_rate']),
                    'icon' => $tab_icon['admin_hourly_rate'],
                    'page' => 'all',
                    'role' => $tab_roles['admin_hourly_rate'],
                    'subpages' => array(),
                    'hidden' => $tab_hidden['admin_hourly_rate']
                ),

//                'admin_hourly_rate_corrections' => array(
//                    'name' => ___($tab_name['admin_hourly_rate_corrections']),
//                    'icon' => $tab_icon['admin_hourly_rate_corrections'],
//                    'page' => 'all',
//                    'role' => $tab_roles['admin_hourly_rate_corrections'],
//                    'subpages' => array(),
//                    'hidden' => $tab_hidden['admin_hourly_rate_corrections']
//                ),

                'admin_manager_hourly_rate' => array(
                    'name' => ___($tab_name['admin_manager_hourly_rate']),
                    'icon' => $tab_icon['admin_manager_hourly_rate'],
                    'page' => 'all',
                    'role' => $tab_roles['admin_manager_hourly_rate'],
                    'subpages' => array(),
                    'hidden' => $tab_hidden['admin_manager_hourly_rate']
                ),


//                'admin_manager_hourly_rate_corrections' => array(
//                    'name' => ___($tab_name['admin_manager_hourly_rate_corrections']),
//                    'icon' => $tab_icon['admin_manager_hourly_rate_corrections'],
//                    'page' => 'all',
//                    'role' => $tab_roles['admin_manager_hourly_rate_corrections'],
//                    'subpages' => array(),
//                    'hidden' => $tab_hidden['admin_manager_hourly_rate_corrections']
//                ),
            ),
            'hidden' => $tab_hidden['impersonalizacija']
        ),

        'tasks' => array(
            'name' => ___($tab_name['tasks']),
            'icon' => $tab_icon['tasks'],
            'page' => 'all',
            'role' => $tab_roles['tasks'],
            'subpages' => array(),
            'hidden' => $tab_hidden['tasks']
        ),

        'trainings' => array(
            'name' => ___($tab_name['trainings']),
            'icon' => $tab_icon['trainings'],
            'page' => 'all',
            'role' => $tab_roles['trainings'],
            'subpages' => array(),
            'hidden' => $tab_hidden['trainings']
        ),
        'performance_management'=>array(
            'name'=>__($tab_name['performance_management']),
            'icon'=>$tab_icon['performance_management'],
            'page'=>'mbo',
            'role'=>array('4','2'),
            'subpages'=>array(
                'impersonation'=>array(
                    'name'=>__('Administracija - Impersonacija'),
                    'role'=>array('4', '2')
                ),
                'mbo'=>array(
                    'name'=>__('Planiranje sporazuma'),
                    'role'=>array('4', '2')
                ),
                'administrator_dodaj'=>array(
                    'name'=>__('Administratori'),
                    'role'=>array('12')
                )
            ),
            'hidden'=>$tab_hidden['performance_management']
        ),
        'business_trip' => array(
            'name' => ___($tab_name['business_trip']),
            'icon' => $tab_icon['business_trip'],
            'page' => 'all&pg=1',
            'role' => $tab_roles['business_trip'],
            'subpages' => array(
                'countries' => array(
                    'name' => ___('Države'),
                    'role' => array('4')
                ),
                'admins' => array(
                    'name' => ___('Administrator službenog puta'),
                    'role' => array('4')
                ),
//                'sifrarnici' => array(
//                    'name' => ___('Šifarnici'),
//                    'role' => array('4')
//                ),
                'akontacije_sifrarnik' => array(
                    'name' => ___('Šifarnik akontacija'),
                    'role' => array('4')
                ),
            ),
            'hidden' => $tab_hidden['business_trip']
        ),

        'users' => array(
            'name' => ___($tab_name['users']),
            'icon' => $tab_icon['users'],
            'page' => 'all',
            'role' => $tab_roles['users'],
            'subpages' => array(),
            'hidden' => $tab_hidden['users']

        ),
        'employees' => array(
            'name' => ___($tab_name['employees']),
            'icon' => $tab_icon['employees'],
            'page' => 'all',
            'role' => $tab_roles['employees'],
            'subpages' => array(),
            'hidden' => $tab_hidden['employees']
        ),

        'admin_manager_hourly_rate' => array(
            'name' => ___($tab_name['admin_manager_hourly_rate']),
            'icon' => $tab_icon['admin_manager_hourly_rate'],
            'page' => 'all',
            'role' => $tab_roles['admin_manager_hourly_rate'],
            'subpages' => array(),
            'hidden' => $tab_hidden['admin_manager_hourly_rate']
        ),


        'admin_manager_hourly_rate_corrections' => array(
            'name' => ___($tab_name['admin_manager_hourly_rate_corrections']),
            'icon' => $tab_icon['admin_manager_hourly_rate_corrections'],
            'page' => 'all',
            'role' => $tab_roles['admin_manager_hourly_rate_corrections'],
            'subpages' => array(),
            'hidden' => $tab_hidden['admin_manager_hourly_rate_corrections']
        ),


        'settings' => array(
            'name' => ___($tab_name['settings']),
            'icon' => $tab_icon['settings'],
            'page' => 'general',
            'role' => $tab_roles['settings'],
            'subpages' => array(),
            'hidden' => $tab_hidden['settings']
        ),
        'vacations' => array(
            'name' => ___($tab_name['vacations']),
            'icon' => $tab_icon['vacations'],
            'page' => 'vacations',
            'role' => $tab_roles['vacations'],
            'subpages' => array(
                'vacations' => array(
                    'name' => ___('Godišnji odmori'),
                    'role' => array('0')
                ),
                'absences' => array(
                    'name' => ___('Odsustva'),
                    'role' => array('0')
                ),

            ),
            'hidden' => $tab_hidden['vacations']
        ),
        'training_programs' => array(
            'name' => ___($tab_name['training_programs']),
            'icon' => $tab_icon['training_programs'],
            'page' => 'training_programs',
            'role' => $tab_roles['training_programs'],
            'subpages' => array(),
            'hidden' => $tabs[12]['Hidden'])
    ,
        'zaduznice' => array(
            'name' => ___($tab_name['zaduznice']),
            'icon' => $tab_icon['zaduznice'],
            'page' => 'zaduznice',
            'role' => $tab_roles['zaduznice'],
            'subpages' => array(
                'zaduznice' => array(
                    'name' => ___('Zadužnice'),
                    'role' => array('0', '1', '2', '3', '4', '5')
                ),
                'razduznice' => array(
                    'name' => ___('Razdužnice'),
                    'role' => array('0', '1', '2', '3', '4', '5')
                ),
                'zaduznice_pristupi' => array(
                    'name' => ___('Pristupi'),
                    'role' => array('0')
                )
            ),
            'hidden' => $tab_hidden['zaduznice']
        ),
        'org_chart' => array(
            'name' => ___($tab_name['org_chart']),
            'icon' => $tab_icon['org_chart'],
            'page' => 'org_chart',
            'role' => $tab_roles['org_chart'],
            'subpages' => array(),
            'hidden' => $tab_hidden['org_chart']
        ),
        'calendar' => array(
            'name' => ___($tab_name['calendar']),
            'icon' => $tab_icon['calendar'],
            'page' => 'calendar',
            'role' => $tab_roles['calendar'],
            'subpages' => array(),
            'hidden' => $tab_hidden['calendar']
        ),
        'zahtjevi' => array(
            'name' => ___($tab_name['zahtjevi']),
            'icon' => $tab_icon['zahtjevi'],
            'page' => 'calendar',
            'role' => $tab_roles['zahtjevi'],
            'subpages' => array('0'),
            'hidden' => $tab_hidden['zahtjevi']
        ),
        'zahtjevi_radnika' => array(
            'name' => ___($tab_name['zahtjevi_radnika']),
            'icon' => $tab_icon['zahtjevi_radnika'],
            'page' => 'calendar',
            'role' => $tab_roles['zahtjevi_radnika'],
            'subpages' => array('0'),
            'hidden' => $tab_hidden['zahtjevi_radnika']
        )


    );

    $vacation_visible = false;
    if ($_user['B_1_description'] == 'Uprava' or $_user['user_id'] == '273' or $_user['managment_level'] == '2' or $_user['managment_level'] == '3' or $_user['managment_level'] == '4')
        $vacation_visible = true;

//    $zaduznice_visible = false;
//    if ($pristup_zaduznice > 0 or $pristup_zaduznice_parent > 0)
//        $zaduznice_visible = true;

    $programi_visible = false;
    if ($pristup_programi_parent > 0)
        $programi_visible = true;

//    $zaduznice_visible_sub = false;
//    if (in_array($_user['user_id'], $zaduznice))
//        $zaduznice_visible_sub = true;
//
//    $zaduznice_pristupi_visible_sub = false;
//    if ($_user['employee_no'] == '473' or $_user['employee_no'] == '568' or $_user['employee_no'] == '108')
//        $zaduznice_pristupi_visible_sub = true;

//    $programi_visible_sub = false;
//    if (in_array($_user['user_id'], $programi))
//        $programi_visible_sub = true;

    $menu = '<ul>';
    $i = 0;

    $pm_adminq = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = " . $_user['user_id']);
    $pm_admin = $pm_adminq->fetch();

    $pm_admin = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_administratori] where user_id = " . $_user['user_id'])->fetchAll();
    if (count($pm_admin)) $pm_admin = true;
    else $pm_admin = false;

    $sl_put_admin = $db->query("SELECT user_id, fname, lname from  " . $portal_users . "  WHERE sl_put_admin = 1 and user_id = " . $_user['user_id']);
    $sl_put_admin = $sl_put_admin->fetch();

    foreach ($list_pages as $slug => $pages) {

        $i++;
        $count_sub = count($pages['subpages']);
        if ($_mod == $slug) {
            $sel1 = ' class="current"';
        } else {
            $sel1 = '';
        }


        if ((in_array($_user['role'], $pages['role']) or ($slug == 'impersonalizacija' and $show_odsustva and $_user['role'] == 2) or
                ($slug == 'tasks' and $_user['MBO']) or ($slug == 'vacations' and $vacation_visible) or
                ($slug == 'training_programs' and $programi_visible) or ($slug == 'org_chart' and $_user['managment_level'] != 0)) and !$pages['hidden']) {

            if ($slug == 'impersonalizacija' and $_user['role'] == 2)
                $menu .= '<li style="font-weight: bold;color:white; "' . $sel1 . '><a href="?m=admin_manager_hourly_rate&p=all"><i class="' . $pages['icon'] . '"></i> <span>' . $pages['name'] . '</span></a>';
            elseif ($slug == 'impersonalizacija' and $_user['role'] == 4)
                $menu .= '<li style="font-weight: bold;color:white; "' . $sel1 . '><a href="?m=admin_manager_hourly_rate&p=all"><i class="' . $pages['icon'] . '"></i> <span>' . $pages['name'] . '</span></a>';
            elseif ($slug == 'zahtjevi')
                $menu .= '<li id="requests" style="background-color:#006595;font-weight: bold;color:white; "' . $sel1 . '><a style="color:white;" href="?m=default&p=odsustva&odobreno=true"><i class="' . $pages['icon'] . '"></i> <span>' . $pages['name'] . '</span></a>';
            elseif ($slug == 'zahtjevi_radnika') {
                if ($show_odsustva)
                    $menu .= '<li id="requests_workers" style="font-weight: bold; "' . $sel1 . '><a href="?m=default&p=odsustva_radnici&neodobreno=true"><i class="' . $pages['icon'] . '"></i> <span>' . $pages['name'] . '</span></a>';
            } else
                $menu .= '<li style="font-weight: bold;color:white; "' . $sel1 . '><a href="?m=' . $slug . '&p=' . $pages['page'] . '"><i class="' . $pages['icon'] . '"></i> <span>' . $pages['name'] . '</span></a>';

            if ($slug == 'zahtjevi') {
                $menu .= '<i color:white;style=" "class="ion-ios-arrow-down pull-right show-ul" id="ul' . $i . '"></i>';
                $menu .= '<ul class="sub-menu-ul" id="ul' . $i . '">';
                $menu .= '<li id="absence_count1" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva&odobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="color:black; margin-right:12%;">Moji odobreni zahtjevi</span><div id="noti_Counter10">&nbsp;</div></a></li>';
                $menu .= '<li id="absence_count2" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva&odobreno=false" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="color:black;margin-right:12%;">Moji neodobreni zahtjevi</span><div id="noti_Counter11">&nbsp;</div></a></li>';
                //$menu .= '<li id="absence_count3" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva_corrections&odobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="color:black;margin-right:12%;">Moje odobrene korekcije</span><div id="noti_Counter100">&nbsp;</div></a></li>';
//                $menu .= '<li id="absence_count4" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva_corrections&odobreno=false" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="color:black;margin-right:12%;">Moje neodobrene korekcije</span><div id="noti_Counter110">&nbsp;</div></a></li>';
                $menu .= '<li id="absence_count5" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva&odobreno=rejected" style="text-decoration:none;line-height:38px;"><i class="ion-close-circled"></i> <span style="color:black;margin-right:12%;">Moji odbijeni zahtjevi</span><div id="noti_Counter12">&nbsp;</div></a></li>';
                $menu .= '<li id="absence_count6" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva&odobreno_cancel=true" style="text-decoration:none;line-height:38px;"><i class="ion-checkmark-circled"></i> <span style="color:black;margin-right:12%;">Moja odobrena otkazivanja</span><div id="noti_Counter13">&nbsp;</div></a></li>';
                $menu .= '<li id="absence_count7" style="font-weight: bold;margin-left: 0px; color:white;"><a href="?m=default&p=odsustva&odobreno_cancel=false" style="text-decoration:none;line-height:38px;"><i class="ion-close-circled"></i> <span style="color:black;margin-right:12%;">Moja neodobrena otkazivanja</span><div id="noti_Counter14">&nbsp;</div></a></li>';
                $menu .= '</ul>';
            } elseif ($slug == 'zahtjevi_radnika') {
                if ($show_odsustva == true) {


                    $menu .= '<i class="ion-ios-arrow-down pull-right show-ul" id="ul' . $i . '"></i>';
                    $menu .= '<ul class="sub-menu-ul" id="ul' . $i . '">';

                    $menu .= '<li id="absence_count8" style="color:white;font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva_radnici&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="color:black;margin-right:12%;">Nova odsustva</span><div id="noti_Counter1">&nbsp;</div></a></li>';
//                    $menu .= '<li id="absence_count9" style="color:white; font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva_radnici_corrections&neodobreno=true" style="text-decoration:none;line-height:38px;"><i class="ion-ios-people"></i> <span style="color:black;margin-right:12%;">Nova odsustva korekcije</span><div id="noti_Counter6">&nbsp;</div></a></li>';
                    $menu .= '<li id="absence_count10" style="color:white; font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva_radnici&zahtjevi=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="color:black;margin-right:12%;">Zahtjevi otkazivanje</span><div id="noti_Counter2">&nbsp;</div></a></li>';
                    //$menu .= '<li id="absence_count11" style="color:white; font-weight: bold;margin-left: 0px;"><a href="?m=default&p=odsustva_radnici_corrections&zahtjevi=true" style="text-decoration:none;color:white;line-height:38px;"><i class="ion-android-cancel"></i> <span style="color:black;margin-right:12%;">Zahtjevi otkazivanje korekcije</span><div id="noti_Counter5">&nbsp;</div></a></li>';
                    //$menu .= '<li id="absence_count9" style="font-weight: bold;margin-left: 0px;"><a href="?m=default&p=otkazani_zahtjevi&admin=true" style="text-decoration:none;line-height:38px;"><i class="ion-android-cancel"></i> <span style="margin-right:12%;">Otkazani zahtjevi radnika</span><div id="noti_Counter5">&nbsp;</div></a></li>';

                    $menu .= '</ul>';
                }
            } elseif ($count_sub > 0) {

                $menu .= '<i class="ion-ios-arrow-down pull-right show-ul" id="ul' . $i . '"></i>';
                $menu .= '<ul id="ul' . $i . '">';
                $menu .= '<li class="main"><a href="?m=' . $slug . '&p=' . $slug . '">' . $pages['name'] . '</a></li>';

                foreach ($pages['subpages'] as $slug_sub => $pages_sub) {

                    if ($_page == $slug_sub) {
                        $sel2 = ' class="current"';
                    } else {
                        $sel2 = '';
                    }

                    if (($slug_sub == 'administrator_dodaj' and $pm_admin)) {
                        $menu .= '<li style="width:175px; "' . $sel2 . '><a href="?m=' . $slug . '&p=' . $slug_sub . '">' . $pages_sub['name'] . '</a></li>';
                    }


                    if ($show_odsustva and $_user['role'] == 2 and $slug_sub == 'admin_hourly_rate')
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=admin_manager_hourly_rate&p=all">' . $pages_sub['name'] . '</a></li>';
                    if ($show_odsustva and $_user['role'] == 2 and $slug_sub == 'admin_manager_hourly_rate_corrections')
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=admin_manager_hourly_rate_corrections&p=all">' . $pages_sub['name'] . '</a></li>';

                    if ($show_odsustva and $_user['role'] == 4 and $slug_sub == 'admin_manager_hourly_rate')
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=admin_manager_hourly_rate&p=all">' . $pages_sub['name'] . '</a></li>';
                    if ($show_odsustva and $_user['role'] == 4 and $slug_sub == 'admin_manager_hourly_rate_corrections')
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=admin_manager_hourly_rate_corrections&p=all">' . $pages_sub['name'] . '</a></li>';

                    if ($_user['role'] == 4 and $slug_sub == 'odsustva_radnici_corrections') {
                        global $url;
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=settings&p=admin">HR Administratori</a></li>';
                        //$menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=work_booklet&p=all">Postava GO</a></li>';
//                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=sifarniciorg&p=all">Šifarnici</a></li>';
                        // $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="'.$url . '/modules/' . 'default' . '/pages/popup_plan_go.php'.'">Plan GO</a></li>';
                        // $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=default&p=all_employees'.'">Pregled radnika</a></li>';
                    } elseif (in_array($_user['role'], $pages_sub['role']) or (($slug_sub == 'absences' or $slug_sub == 'vacations') and $vacation_visible) or ($slug_sub == 'odsustva_radnici' and $show_odsustva) or ($slug_sub == 'odsustva_radnici_corrections' and $show_odsustva)) {
                        if (isset($pages_sub['url']) and !empty($pages_sub['url'])) {
                            $menu .= '<li style="width:175px; "' . $sel2 . '><a href="' . $pages_sub['url'] . '">' . $pages_sub['name'] . '</a></li>';
                        } else {
                            $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=' . $slug . '&p=' . $slug_sub . '">' . $pages_sub['name'] . '</a></li>';
                        }
                    }

                    if ($_user['role'] == 4 and $_user['employee_no'] == 989 and $slug_sub == 'odsustva_radnici_corrections') {
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=settings&p=logout">Podešavanja</a></li>';
                    }
                    if (($pages_sub['name'] == 'Šifarnik akontacija' or $pages_sub['name'] == 'Šifarnici' or $pages_sub['name'] == 'Države') and $_user['role'] != 4 and $sl_put_admin) {
                        $menu .= '<li style="color:white;width:175px; "' . $sel2 . '><a href="?m=' . $slug . '&p=' . $slug_sub . '">' . $pages_sub['name'] . '</a></li>';
                    }


                }

                $menu .= '</ul>';
            }
            $menu .= '</li>';


        }

    }

    $menu .= '</ul>';

    return $menu;
}

?>
