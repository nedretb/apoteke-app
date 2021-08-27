<?php
//require __DIR__ . '/../../../vendor/autoload.php';
use Carbon\Carbon;
require 'config-urls.php';
global $db, $nav_human_resource_setup;
require_once $root.'/CORE/classes/Model.php';
foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;

//$admin_managementq = $db->query("SELECT [Chief Executive Administrator] from  ".$nav_human_resource_setup."  ");
//$admin_management = $admin_managementq->fetch();

function userList($post_type, $post_value, $limit, $offset, $user_employee_no, $filtertdate){
    global $db, $portal_users;

    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    if ($post_type == 'org_jed') {

        $sistematizacije = Sistematizacija::getIDs($post_value);
        $order = " order by user_id asc offset $offset rows fetch next ".$limit. "rows only";
        $condition = "egop_ustrojstvena_jedinica in (".implode(',', $sistematizacije).")";
    }
    elseif($post_type == 'name_surname'){
            $order = "";
            $condition = " ((fname + ' ' + lname) like N'".$post_value."' COLLATE Latin1_General_CI_AI or (fname + ' ' + lname) like '".$post_value."' COLLATE Latin1_General_CI_AI) ";
    }
    else{
        if($_user['role'] == 4){
            $sistematizacije = Sistematizacija::getIDs(1);
            $order = " order by user_id asc offset $offset rows fetch next ".$limit. "rows only";
            $condition = "egop_ustrojstvena_jedinica in (".implode(',', $sistematizacije).")";
        }else{
            $sistematizacije = Sistematizacija::getIDs($_user['egop_ustrojstvena_jedinica']);
            $order = " order by user_id asc offset $offset rows fetch next ".$limit. "rows only";
            $condition = "egop_ustrojstvena_jedinica in (".implode(',', $sistematizacije).")";
        }
    }



    $profiles = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users] where ".$condition." ".$order)->fetchAll();
    $profiles_count = $db->query("select count(*) from [c0_intranet2_apoteke].[dbo].[users] where ".$condition)->fetchAll();
    $queryg = $db->query("SELECT [egop_ustrojstvena_jedinica] FROM  " . $portal_users . "  WHERE (" . $user_employee_no . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))  group BY [egop_ustrojstvena_jedinica]");

    $array = [];
    array_push($array, $profiles);
    array_push($array, $profiles_count);
    array_push($array, $queryg);

    return $array;

}


function cleanSuskavac($text){
    $text = iconv("UTF-8", "ISO-8859-1//IGNORE", $text);

    return $text;
}

function countWorkingDays($year, $month, $ignore) {
    $count = 0;
    $counter = mktime(0, 0, 0, $month, 1, $year);
    while (date("n", $counter) == $month) {
        if (in_array(date("w", $counter), $ignore) == false) {
            $count++;
        }
        $counter = strtotime("+1 day", $counter);
    }
    return $count;
}

function explainStatus($status){
    $explanation = "";
    switch($status){
        case "P":
            $explanation 		= "Praznik";
            break;
        case "VP":
            $explanation 		= "Vjerski praznik";
            break;
        case "TB":
            $explanation 		= "Porodiljsko odsustvo, roditeljski dopust";
            break;
        case "SP":
            $explanation 		= "Službeni put";
            break;
        case "BL":
            $explanation 		= "Bolovanje";
            break;
        case "GO":
            $explanation 		= "Godišnji odmor";
            break;
        case "NO":
            $explanation 		= "Neplaćeno odsustvo";
            break;
        case "PO":
            $explanation 		= "Plaćeno odsustvo ";
            break;
        case "DP":
            $explanation 		= "Državni praznik ";
            break;
        case "RR":
            $explanation 		= "Redovan rad ";
            break;
        case "S":
            $explanation 		= "Suspenzija ";
            break;

    }
    return $explanation;
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

function createColumnsArray($end_column, $first_letters = '')
{
    $columns = array();
    $length = strlen($end_column);
    $letters = range('A', 'Z');

    foreach ($letters as $letter) {
        $column = $first_letters . $letter;

        $columns[] = $column;

        if ($column == $end_column)
            return $columns;
    }

    foreach ($columns as $column) {
        if (!in_array($end_column, $columns) && strlen($column) < $length) {
            $new_columns = createColumnsArray($end_column, $column);
            $columns = array_merge($columns, $new_columns);
        }
    }

    return $columns;
}


function _shortcode($status){
    global $db, $_conf;

    $check = $db->prepare("SELECT shortcode FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_status] WHERE id = '$status'");
    $check->execute();
    $get = $check->fetch();

    $shortcode = $get['shortcode'];

    return $shortcode;
}

function getHourlyrateData($status, $hour, $hour_pre, $weekday){
    global $db, $_conf;

    $hour_ret = 0;
    if((in_array($weekday, array(6, 7)) and ($hour_pre == 0 or $hour_pre == ""))){
        $ret_data = "";
        $hour_ret = 0;
    } else if((in_array($weekday, array(6, 7)) and $hour > 0 or $hour_pre > 0) and ($status == 5 or ($status >= 85 and $status <= 96))){
        $ret_data =  $hour;
        $hour_ret = $hour;
    }

    if($status == 5 or ($status >= 85 and $status <= 96)){
        $ret_data =  $hour;
        $hour_ret = $hour;
    } else {
        $sg = _shortcode($status);

        if($sg == ""){
            $sg = "D";
        }
        $ret_data =  $sg;

        $hour_ret = $hour;


    }

    if(in_array($weekday, array(6, 7)) and $status != 5 and !in_array($status, array(85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96))){
        $hour_ret = 0;
        $ret_data = "";
    }



    return array($ret_data, $hour_ret);

}

function monthBosnian($month){

    $month_bos = "";
    switch($month){
        case 1:
            $month_bos = "Januar";
            break;
        case 2:
            $month_bos = "Februar";
            break;
        case 3:
            $month_bos = "Mart";
            break;
        case 4:
            $month_bos = "April";
            break;
        case 5:
            $month_bos = "Maj";
            break;
        case 6:
            $month_bos = "Juni";
            break;
        case 7:
            $month_bos = "Juli";
            break;
        case 8:
            $month_bos = "August";
            break;
        case 9:
            $month_bos = "Septembar";
            break;
        case 10:
            $month_bos = "Oktobar";
            break;
        case 11:
            $month_bos = "Novembar";
            break;
        case 12:
            $month_bos = "Decembar";
            break;
    }

    return $month_bos;
}



//sl put mail
function mail_cancel_trip($from, $to, $status, $year_id = null)
{
    require 'config-urls.php';
    global $db;
    $q = $db->prepare("select email_company from  " . $portal_users . "  where sl_put_admin=1");
    $q->execute();
    $sl_put_admin = $q->fetchAll();

    if (isset($year_id)) {
        $_POST['get_year'] = $year_id;
    }

    global $db;
    include '../../lib/PHPMailer/PHPMailer.php';
    include '../../lib/PHPMailer/SMTP.php';
    include '../../lib/PHPMailer/Exception.php';
    //provjera da li je parcijalno odbijanje i ispis poruke za regularni sl put

    if ($status == 73) {
        $check = $db->query("SELECT count(*) as c FROM  " . $portal_sl_put . "  as s
    join  " . $portal_hourlyrate_day . "  as d on s.request_id=d.id
    join " . $portal_users . "  as u on d.employee_no= u.employee_no
    join  " . $portal_hourlyrate_year . "  as y on y.user_id=u.user_id
    where y.id = " . $_POST['get_year'] . " and  pocetak_datum = '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . "' = case 
    when kraj_datum2='' then kraj_datum
    else kraj_datum2
    end ");
        $check = $check->fetch();

        if ($check['c'] == 0) {
            echo '<div class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Djelomična izmjena službenog puta je moguća samo na nalogu službenog puta!') . '</div>';
            die();
        }
    } elseif ($status == 81) {
        //data <4 sata
        $check = $db->query("SELECT count(*) as k FROM  " . $portal_hourlyrate_day . "  as d  
    join  " . $portal_sl_put . "  as s on s.request_id=d.id 
    join " . $portal_users . "  as u on d.employee_no= u.employee_no 
    join  " . $portal_hourlyrate_year . "  as y on y.user_id=u.user_id 
    where y.id = " . $_POST['get_year'] . " and (
    (pocetak_datum < '" . date("Y-m-d", strtotime($to)) . "' and '" . date("Y-m-d", strtotime($to)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or 
    (pocetak_datum < '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or
    (pocetak_datum < '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($from)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or

    (pocetak_datum <= '" . date("Y-m-d", strtotime($to)) . "' and '" . date("Y-m-d", strtotime($to)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or 
    (pocetak_datum <= '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or
    (pocetak_datum <= '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($from)) . "' < case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or

    (pocetak_datum < '" . date("Y-m-d", strtotime($to)) . "' and '" . date("Y-m-d", strtotime($to)) . "' <= case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or 
    (pocetak_datum < '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . "' <= case when kraj_datum2='' then kraj_datum else kraj_datum2 end) or
    (pocetak_datum < '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($from)) . "' <= case when kraj_datum2='' then kraj_datum else kraj_datum2 end)
    )");
        $check = $check->fetch();
        if ($check['k'] == 1) {
            echo '<div class="alert alert-danger text-center"><b>' . __('Upozorenje!') . '</b><br/>' . __('Djelomična izmjena službenog puta je moguća samo na nalogu službenog puta!') . '</div>';
            die();
        } else {
            //mail <4 sata

            $podaci_mailq = $db->query("
  SELECT * FROM  " . $portal_users . "  as u 
  join  " . $portal_hourlyrate_year . "  as y on y.user_id=u.user_id
  where y.id = " . $_POST['get_year']);
            $podaci_mail = $podaci_mailq->fetch();

            $parent = $db->query("SELECT email_company, fname, lname from  " . $portal_users . "  WHERE employee_no = " . $podaci_mail['parent'] . " ");
            $parent = $parent->fetch();

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->CharSet = "UTF-8";

            $mail->IsSMTP();
            $mail->isHTML(true);

            $mail->Host = "mailgw.rbbh.ba";
            $mail->Port = 25;

            $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
            $mail->addAddress($podaci_mail['email_company']);
            $mail->addAddress($parent['email_company']);

            $mail->Subject = 'OTKAZIVANJE poslovnog putovanja';
            $mail->Body =
                '<strong>' . $podaci_mail['fname'] . ' ' . $podaci_mail['lname'] . '</strong> je prijavi(o)la novi zahtjev.<br />
  <table style="">
<tbody>
<tr style="">
  <td style="" colspan="2">
    Ime:
  </td>
  <td style="" colspan="2">
    <strong>' . $podaci_mail['fname'] . ' ' . $podaci_mail['lname'] . '</strong>
  </td>
</tr>
<tr style="">
  <td style="" colspan="2">
    Direktni nadredjeni:
  </td>
  <td style="" colspan="2">
    <strong>' . $parent['fname'] . ' ' . $parent['lname'] . '</strong>
  </td>
</tr>
<tr style="">
  <td style="" colspan="2">
    Početni datum:
  </td>
  <td style="" colspan="2">
    <strong>' . date("Y-m-d", strtotime($from)) . '</strong>
  </td>
</tr>
<tr style="">
  <td style="" colspan="2">
    Krajnji datum:
  </td>
  <td style="" colspan="2">
    <strong>' . date("Y-m-d", strtotime($to)) . '</strong>
  </td>
</tr>
<tr style="">
  <td style="" colspan="2">
    Vrsta Odsustva:
  </td>
  <td style="" colspan="2">
    <strong>OTKAZANO</strong>
  </td>
</tr>
</tbody>
</table>';
            $canSendMail = $db->query("SELECT value
  FROM  " . $portal_settings . " 
  where name = 'hr_notifications'");
            $canSendMail = $canSendMail->fetch();

            if ($canSendMail)
                if (!$mail->send()) {
                    var_dump($mail->ErrorInfo);
                }

            //ako postoji sl put i nije jos odobren od admina otkazi ga

            $checkq = $db->query(" select id FROM  " . $portal_sl_put . "  
 where canceled != 1 and status = 81 and employee = " . $podaci_mail['employee_no'] . " and pocetak_datum = '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . "' = 
  case
    when kraj_datum2 = '' then kraj_datum else kraj_datum2 end order by id desc ");
            $check = $checkq->fetch();

            $check2q = $db->query("select corr_review_status from  " . $portal_hourlyrate_day . "  where corr_review_status != 1 and Date = '" . date("Y-m-d", strtotime($from)) . "' and employee_no = " . $podaci_mail['employee_no']);
            $check2 = $check2q->fetch();

            if ($check and $check2) {
                //otkazivanje log
                $insert_log = $db->query("INSERT INTO  " . $portal_sl_put_logs . "  (sl_put_request_id, operation, user_id, vrijeme) 
    VALUES (" . $check['id'] . ", 'otkazivanje_satnica', " . _decrypt($_SESSION['SESSION_USER']) . ", " . time() . ")");

                $update = $db->query("UPDATE  " . $portal_sl_put . "  
    SET canceled = 1 WHERE id = " . $check['id'] . " ");
                $update->execute();
                //
            }
            return;
        }
    }

    //slanje maila regularni sl put

    $podaci_mailq = $db->query("
  SELECT *,s.id as sl_put_id FROM  " . $portal_sl_put . "  as s
  join  " . $portal_hourlyrate_day . "  as d on s.request_id=d.id
  join " . $portal_users . "  as u on d.employee_no= u.employee_no
  join  " . $portal_hourlyrate_year . "  as y on y.user_id=u.user_id
  left  join  " . $portal_countries . "  as c on c.country_id = s.odredisna_drzava
  where svrha != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE' and y.id = " . $_POST['get_year'] . " and '" . date("Y-m-d", strtotime($from)) . "'=pocetak_datum and '" . date("Y-m-d", strtotime($to)) . "' =
  (case
    when kraj_datum2 = '' then kraj_datum
    else kraj_datum2
  end) order by s.id desc
  ");
    $podaci_mail = $podaci_mailq->fetch();

    $parent = $db->query("SELECT email_company, fname, lname from  " . $portal_users . "  WHERE employee_no = " . $podaci_mail['parent'] . " ");
    $parent = $parent->fetch();

    if ($podaci_mail['canceled'] != 1) {

        //otkazivanje log
        $insert_log = $db->query("INSERT INTO  " . $portal_sl_put_logs . "  (sl_put_request_id, operation, user_id, vrijeme) 
    VALUES (" . $podaci_mail['sl_put_id'] . ", 'otkazivanje_satnica', " . _decrypt($_SESSION['SESSION_USER']) . ", " . time() . ")");

        $update = $db->query("UPDATE  " . $portal_sl_put . "  
    SET canceled = 1 WHERE id = " . $podaci_mail['sl_put_id'] . " ");
        $update->execute();
        //
    }
    //status
    $logq = $db->query("
            SELECT  *
            FROM  " . $portal_sl_put_logs . "  
            WHERE sl_put_request_id = " . $podaci_mail['sl_put_id'] . "
      order by id desc
      offset 1 rows
      fetch next 1 rows only ");
    $log = $logq->fetch();

    $podaci_mail['operation'] = $log['operation'];


    $statuss = 'Na obradi';

    if ($podaci_mail['operation'] == 'obrada' or $podaci_mail['operation'] == 'odobravanje') {
        $statuss = 'Na obradi';
    } elseif ($podaci_mail['operation'] == 'odbijanje') {
        $statuss = 'Poslano na korekciju';
    }
    if ($podaci_mail['lock'] == 1) $statuss = 'Zaključano';

    //

    if ($parent) {

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = "UTF-8";

        $mail->IsSMTP();
        $mail->isHTML(true);

        $mail->Host = "mailgw.rbbh.ba";
        $mail->Port = 25;

        $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
        $mail->addAddress($podaci_mail['email_company']);
        $mail->addAddress($parent['email_company']);
        //$mail->addAddress('racunovodstvo@raiffeisengroup.ba');
        foreach ($sl_put_admin as $email) {
            $mail->addAddress($email['email_company']);
        }

        if ($podaci_mail['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE') {
            if ($podaci_mail['name'] != 'Bosna i Hercegovina') {
                $mail->addAddress('raiffeisen_assistance@raiffeisengroup.ba');
                if (strtolower($podaci_mail['osiguranje']) == 'da') {
                    $mail->addAddress('Banko.uniqa@uniqa.ba');
                }
            }

            if ($podaci_mail['svrha'] == 'Edukacija/trening') {
                $mail->addAddress('hr.rbbh@raiffeisengroup.ba');
            }

            if ($podaci_mail['vrsta_transporta'] == 'Avion') {
                $mail->addAddress('ured.uprave.rbbh@raiffeisengroup.ba');
            }

            if ($podaci_mail['vrsta_transporta'] == 'Službeno vozilo' or $podaci_mail['vrsta_transporta'] == 'Službeno vozilo sa vozačem') {
                // $mail->addAddress('vozni.park@raiffeisengroup.ba');
            }
        }

        $mail->Subject = 'OTKAZIVANJE poslovnog putovanja';
        $mail->Body = "<style>body{font-family: Arial,Verdana,Segoe,sans-serif;font-size:12px;line-height: 200%;}</style>
    <body>
    <b>" . $podaci_mail['fname'] . " " . $podaci_mail['lname'] . "</b> je prijavi(o)la novi zahtjev 
    <span style='color:red;'>OTKAZIVANJE</span> Poslovnog putovanja broj <b>" . $podaci_mail['sl_put_id'] . " </b><br>
    Radnik:  <b>" . $podaci_mail['fname'] . " " . $podaci_mail['lname'] . "</b><br>
    Status: <b>$statuss</b><br>
    Org.jedinica:   <b>" . $podaci_mail['B_1_description'] . "</b><br>
    Radno mjesto:   <b>" . $podaci_mail['position'] . "   </b><br>
    Datum zaposlenja u Banci:   <b>" . date("d.m.Y", strtotime($podaci_mail['employment_date'])) . "</b><br>
    JMBG:   <b>" . $podaci_mail['JMB'] . "</b><br>
    Direktni nadređeni: <b>" . $parent['fname'] . " " . $parent['lname'] . "    </b><br>
    Pocetni datum:  <b>" . date("d.m.Y", strtotime($podaci_mail['pocetak_datum'])) . "</b><br>
    Krajnji datum:  <b>" . date("d.m.Y", strtotime($podaci_mail['kraj_datum2'] ? $podaci_mail['kraj_datum2'] : $podaci_mail['kraj_datum'])) . "</b><br>
    Svrha:  <b>" . $podaci_mail['svrha'] . "</b><br>
    Odredište: <b>" . $podaci_mail['odredisni_grad'] . " - " . $podaci_mail['odredisni_grad2'] . "</b><br>
    Razlog putovanja: <b>" . $podaci_mail['razlog_putovanja'] . "</b><br>
    Napomena: <b>" . $podaci_mail['napomena'] . "</b><br>
    Osiguranje: <b>" . $podaci_mail['osiguranje'] . "</b><br>
    Viza potrebna: <b>" . $podaci_mail['viza'] . "</b>   Broj pasoša: <b>" . $podaci_mail['dokument_broj'] . "</b> &nbsp &nbsp &nbsp    Napomena: <b>" . $podaci_mail['osiguranje_napomena'] . "</b><br>
    Akontacija iznos:<b> " . $podaci_mail['iznos_akontacije'] . "</b> &nbsp &nbsp &nbsp   Napomena:<b>" . $podaci_mail['akontacija_napomena'] . "</b><br>
    Sredstvo transporta: <b>" . $podaci_mail['vrsta_transporta'] . "</b> &nbsp &nbsp &nbsp    Napomena: <b>" . $podaci_mail['transport_napomena'] . "</b><br>
    Smještaj napomena: <b>" . $podaci_mail['smjestaj_napomena'] . "</b>
    
    <br>
    <br>
    </body>
    ";

        $canSendMail = $db->query("SELECT value
  FROM  " . $portal_settings . " 
  where name = 'hr_notifications'");
        $canSendMail = $canSendMail->fetch();

        if ($canSendMail)
            if (!$mail->send()) {
                var_dump($mail->ErrorInfo);
            }
    }
}

// check page permission
function _pagePermission($level, $strict = false)
{
    require 'config-urls.php';
    global $url;

    if (!isset($_SESSION['SESSION_USER']) || (trim($_SESSION['SESSION_USER']) == '')) {
        echo '<script>window.location.href="' . $url . '/modules/default/login.php";</script>';
    } else {

        global $_user;

        if ($strict == false) {
            if ($_user['role'] > $level) {
                echo '<script>window.location.href="' . $url . '/modules/default/unauthorized.php";</script>';
            }
        } else {
            if ($_user['role'] != $level) {
                echo '<script>window.location.href="' . $url . '/modules/default/unauthorized.php";</script>';
            }
        }

    }

}

function getYearFull($year_id)
{
    global $db, $portal_hourlyrate_year;

    $d = $db->prepare("SELECT year FROM  " . $portal_hourlyrate_year . "  WHERE id = '$year_id' ");
    $d->execute();
    $f = $d->fetch();

    return $f['year'];

}

function getYearFullId($year, $user_id)
{
    global $db, $portal_hourlyrate_year;

    $d = $db->prepare("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE year = '$year' and user_id = '$user_id' ");
    $d->execute();
    $f = $d->fetch();

    return $f['id'];

}

function getYearId($current_year, $user_id, $which = 'next', $year_id_parameter = false)
{

    global $db, $_conf, $portal_hourlyrate_year;

    if ($year_id_parameter == true) {
        $get_y = $db->prepare("SELECT year FROM  " . $portal_hourlyrate_year . "  WHERE user_id = '$user_id' and id = '$current_year' ");
        $get_y->execute();
        $get_f = $get_y->fetch();
        $current_year = $get_f['year'];
    }

    $next_year = $current_year + 1;
    $check_next_year = $db->prepare("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id = '$user_id' and year = '" . $next_year . "' ");
    $check_next_year->execute();
    $get_next_year_id = $check_next_year->fetch();
    $nex_year = $get_next_year_id['id'];

    $prev_year = $current_year - 1;

    $check_prev_year = $db->prepare("SELECT id FROM  " . $portal_hourlyrate_year . "  WHERE user_id = '$user_id' and year = '" . $prev_year . "' ");
    $check_prev_year->execute();
    $get_prev_year_id = $check_prev_year->fetch();
    $pre_year = $get_prev_year_id['id'];

    if ($which == 'next') {
        return $nex_year;
    } else {
        return $pre_year;
    }
}

function getWorkingDays($startDate, $endDate, $holidays)
{
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    } else {
        if ($the_first_day_of_week == 7) {
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                $no_remaining_days--;
            }
        } else {
            $no_remaining_days -= 2;
        }
    }

    $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0) {
        $workingDays += $no_remaining_days;
    }

    foreach ($holidays as $holiday) {
        $time_stamp = strtotime($holiday);
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7)
            $workingDays--;
    }

    return ceil($workingDays);
}


function isAllowedStatusWeekend($status)
{
    if (in_array($status, array(18, 19, 21, 22, 27, 28, 29, 30, 31, 32, 40, 41, 49, 50, 51, 52, 53, 54, 55, 79, 80, 82, 84, 106))) {
        return false;
    } else {
        return true;
    }
}

function _updateCiljevi($user_id)
{
    require 'config-urls.php';
    global $db, $_conf;
    $ocjena_ciljeva = 0;
    $ocjena_kompetencija = 0;
    $suma_pondera = 0;
    $year = date("Y");

    $query = $db->query("SELECT * FROM  " . $portal_tasks . "  WHERE task_type in (0,1) and (status NOT IN (4,5) or status is null) AND user_id = " . $user_id . " AND year = " . date("Y"));
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ponder = $item['ponder'];
            $ponder_value = $item['ponder'] / 100 * $item['rating'];
            $ocjena_ciljeva += $ponder_value;
            $suma_pondera += $item['ponder'];
        }

        $query = $db->query("SELECT * FROM  " . $portal_ocjene . "  WHERE user_id=" . $user_id);
        if ($query->rowCount() == 0) {

            $data = "INSERT INTO  " . $portal_ocjene . "  (user_id, ciljevi,ponder_sum,year) VALUES (" . $user_id . "," . $ocjena_ciljeva . "," . $suma_pondera . "," . $year . ")";
            $res = $db->prepare($data);
            $res->execute(
                array()
            );
            if ($res->rowCount() == 1) {

                //successo

            } else {//fail
            }
        } else {

            $data1 = "UPDATE  " . $portal_ocjene . "  SET
      ciljevi = ?,
    ponder_sum = ?
    WHERE user_id = ?";
            $res = $db->prepare($data1);
            $res->execute(
                array(
                    $ocjena_ciljeva,
                    $suma_pondera,
                    $user_id,

                )
            );
            if ($res->rowCount() == 1) {

                //successo

            } else {//fail
            }
        }
    } else {
        $data3 = "DELETE FROM  " . $portal_ocjene . " 
  WHERE user_id = ?
  AND year = ?";
        $res = $db->prepare($data3);
        $res->execute(
            array(
                $user_id,
                $year
            )
        );
        if ($res->rowCount() == 1) {

            //successo

        } else {//fail
        }
    }
    return 1;
}

function _updateKompetencije($user_id)
{
    require 'config-urls.php';
    global $db, $_conf;
    $year = date("Y");

    $query = $db->query("SELECT * FROM  " . $portal_misc . "  WHERE user_id = " . $user_id . " AND year = " . date("Y"));
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ocjena_kompetencija = ($item['kompetencija1_rating'] + $item['kompetencija2_rating'] + $item['kompetencija3_rating'] + $item['obavezna1_rating'] + $item['obavezna2_rating']) / 5;
        }

        $query = $db->query("SELECT * FROM  " . $portal_ocjene . "  WHERE user_id=" . $user_id);
        if ($query->rowCount() == 0) {

            $data = "INSERT INTO  " . $portal_ocjene . "  (user_id, ciljevi,kompetencije,ponder_sum,ucinak,year) VALUES (" . $user_id . ",0," . $ocjena_kompetencija . ",0,0," . $year . ");";
            $res = $db->prepare($data);
            $res->execute(
                array()
            );
            if ($res->rowCount() == 1) {

                //successo

            } else {//fail
            }
        } else {

            $data1 = "UPDATE  " . $portal_ocjene . "  SET
      kompetencije = ?
    WHERE user_id = ?";
            $res = $db->prepare($data1);
            $res->execute(
                array(
                    $ocjena_kompetencija,
                    $user_id,

                )
            );
            if ($res->rowCount() == 1) {

                //successo

            } else {//fail
            }
        }
    }
    return $data;
}

function _updateLastChange($task_id)
{

    global $db, $_conf, $portal_tasks;
    $_user = _user(_decrypt($_SESSION['SESSION_USER']));
    $user_id = $_user['user_id'];

    $data1 = "UPDATE  " . $portal_tasks . "  SET
      last_changed_by = ?
    WHERE task_id = ?";
    $res = $db->prepare($data1);
    $res->execute(
        array(
            $user_id,
            $task_id
        )
    );
    if ($res->rowCount() == 1) {

        //successo

    } else {//fail
    }

}


// Dropdown select for roles
function _optionRole($current = null)
{
    global $db, $_conf;

    $roles = array(
        '0' => __('Administrator'),
        '1' => __('HR'),
        '2' => __('Nadređeni'),
        '3' => __('Administrator satnica'),
        '4' => __('Menadžer administratora satnica'),
        '5' => __('Zaposlenik')
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionVerification($current = null)
{
    global $db, $_conf;

    $roles = array(
        '0' => __('NE'),
        '1' => __('DA'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionPomicni($current = null)
{
    global $db, $_conf;

    $roles = array(
        '0' => __('NE'),
        '1' => __('DA'),
    );

    $opt = '<option value="-1">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionRizikGubitka($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Visok'),
        '2' => __('Srednji'),
        '3' => __('Nizak'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionUticajGubitka($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Visok'),
        '2' => __('Srednji'),
        '3' => __('Nizak'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionRazlogOdlaska($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Prelazak u drugi odjel'),
        '2' => __('Prelazak u drugu kompaniju'),
        '3' => __('Penzionisanje'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionKarijera($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Rukovodilac'),
        '2' => __('Ekspert'),
        '3' => __('Ostvarena karijera'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionNoviZaposlenik($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Da'),
        '2' => __('Ne'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionSpremnost($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Spreman(na) sada'),
        '2' => __('Spreman(na) za 1-3 godine'),
        '3' => __('Spreman(na) 3-5 godina'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _OptionSlobodanUnos($current = null)
{
    global $db, $_conf;

    $roles = array(
        '1' => __('Centrala'),
        '2' => __('Svi'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}


// Dropdown select for languages
function _optionLang($current)
{
    global $db, $_conf, $portal_languages;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_languages . "  ORDER BY lang_name ASC");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['lang_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['lang_id'] . '" ' . $sel . '>' . $item['lang_name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema dostupnih jezika.') . '</option>';

    }

    return $opt;

}

function _getLanguageById($id)
{
    global $db, $_conf, $portal_languages;

    $query = $db->query("SELECT * FROM  " . $portal_languages . "  where lang_id=" . $id);
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $language = $item['lang_name'];
        }
    }


    return $language;

}


// Dropdown select for languages
function _optionUser($current)
{
    global $db, $_conf, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE role='0' ORDER BY fname ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(0) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }
    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE role='1' ORDER BY fname ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(1) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE role='3' ORDER BY fname ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(3) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }
    return $opt;

}

function _optionUserAbs($current, $id)
{
    global $db, $_conf, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_users . "  ORDER BY role ASC");
    if ($query->rowCount() < 0) {


        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }

        return $opt;
    }
}

function _optionUser2($current)
{
    global $db, $_conf, $portal_departments, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_departments . "  group BY department_code ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(0) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }
    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE role='1' ORDER BY fname ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(1) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE role='3' ORDER BY fname ASC");
    if ($query->rowCount() < 0) {

        $opt .= '<optgroup label="' . _role(3) . '">';
        foreach ($query as $item) {
            if ($current == $item['user_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['user_id'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }
        $opt .= '</optgroup>';

    }
    return $opt;

}

function _optionB_1($current, $notadmin = 0){
    global $db, $_conf, $admin_management, $nav_employee, $nav_employee_contract_ledger, $portal_users;
    $x_user = _user(_decrypt($_SESSION['SESSION_USER']));


    $opt = '';


    $data = ($x_user['role'] == 4) ? Sistematizacija::getSys() : Sistematizacija::getSys($x_user);

        foreach ($data as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            if ($item['id'] != 1){
                $opt .= '<option value="'.$item['id'].'"'.$sel.'>'.$item['title'].'</option>';
            }
    }

    return $opt;

}

function _optionRegion($B_1, $current, $notadmin = 0)
{
    global $db, $_conf, $admin_management, $nav_employee, $nav_employee_contract_ledger, $portal_users;
    $x_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $opt = '<option value="">' . __('Odaberi...') . '</option>';
    if ($x_user['employee_no'] == $admin_management[0] and $x_user['role'] == 2) {
        $check_management_level = " (cl.[Management Level]=6 or cl.[Management Level]=8) or ";
    } else {
        $check_management_level = "";
    }


    if ($B_1 == '') {
        $query = $db->query("
    select distinct cl.[Department Cat_ Description] as B_1_description
    from  " . $nav_employee . "  as e
    join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
    left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
    where " . $check_management_level . " cl.[Department Cat_ Description] !='' and cl.Active=1 and cl.[Show Record]=1 and (" . $x_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $x_user['employee_no'] . " or parent2 = " . $x_user['employee_no'] . " or  " . $x_user['employee_no'] . " in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))
    ");
    } else {
        $query = $db->query("select distinct cl.[Department Cat_ Description] as B_1_description
      from  " . $nav_employee . "  as e
      join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
      left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
      where " . $check_management_level . " (cl.[Sector Description]=N'" . $B_1 . "' or cl.[Sector Description]='" . $B_1 . "') and cl.[Department Cat_ Description] !='' and cl.Active=1 and cl.[Show Record]=1 and 
      (" . $x_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $x_user['employee_no'] . " or parent2 = " . $x_user['employee_no'] . " or  " . $x_user['employee_no'] . " in 
      (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");
    }


    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['B_1_description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['B_1_description'] . '" ' . $sel . '>' . $item['B_1_description'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionStream($region, $current)
{
    global $db, $_conf, $admin_management, $nav_employee_contract_ledger, $nav_employee, $portal_users;
    $x_user = _user(_decrypt($_SESSION['SESSION_USER']));
    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $add = '';
    if (isset($_POST['sektor'])) $add .= "and p.[Sector  Description] = '" . $_POST['sektor'] . "'";

    if ($x_user['employee_no'] == $admin_management[0] and $x_user['role'] == 2) {
        $check_management_level = " (cl.[Management Level]=6 or cl.[Management Level]=8) or ";
    } else {
        $check_management_level = "";
    }


    if ($region == '') {
        $query = $db->query("select distinct cl.[Group Description] as grupe from  " . $nav_employee . "  as e
      join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
      left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
      where " . $check_management_level . " cl.[Group Description] !='' and cl.Active=1 and cl.[Show Record]=1 and 
      (" . $x_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $x_user['employee_no'] . " or parent2 = " . $x_user['employee_no'] . " or  " . $x_user['employee_no'] . " in 
      (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");

    } else {
        $query = $db->query("select distinct cl.[Group Description] as grupe from  " . $nav_employee . "  as e
        join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
        left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
        where " . $check_management_level . " (cl.[Department Cat_ Description]='" . $region . "' or cl.[Department Cat_ Description]=N'" . $region . "') and cl.[Group Description] !='' and cl.Active=1 and cl.[Show Record]=1 and 
        (" . $x_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $x_user['employee_no'] . " or parent2 = " . $x_user['employee_no'] . " or  " . $x_user['employee_no'] . " in 
        (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");
    }

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['grupe']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['grupe'] . '" ' . $sel . '>' . $item['grupe'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionTeam($stream, $current)
{
    global $db, $_conf, $admin_management, $nav_employee_contract_ledger, $nav_employee, $portal_users;

    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $employee_no = $_user['employee_no'];

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $add = '';
    if (isset($_POST['sektor'])) $add .= "and p.[Sector  Description] = '" . $_POST['sektor'] . "'";

    if ($employee_no == $admin_management[0] and $_user['role'] == 2) {
        $check_management_level = " (cl.[Management Level]=6 or cl.[Management Level]=8) or ";
    } else {
        $check_management_level = "";
    }

    if ($stream == '') {

        $query = $db->query("select distinct cl.[Team Description] as tim from  " . $nav_employee . "  as e
    join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
    left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
    where " . $check_management_level . " cl.[Team Description] !='' and cl.Active=1 and cl.[Show Record]=1 and 
    (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
    (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");

    } else {
        $query = $db->query("select distinct cl.[Team Description] as tim from  " . $nav_employee . "  as e
      join  " . $nav_employee_contract_ledger . "  as cl on e.No_ = cl.[Employee No_]
      left join  " . $portal_users . "  as u on e.[No_] = u.employee_no
      where " . $check_management_level . " (cl.[Group Description]='" . $stream . "' or cl.[Group Description]=N'" . $stream . "') and cl.[Team Description] !='' and cl.Active=1 and cl.[Show Record]=1 and 
      (" . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8) or parent = " . $_user['employee_no'] . " or parent2 = " . $_user['employee_no'] . " or  " . $_user['employee_no'] . " in 
      (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO6,MBO))");
    }

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['tim']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['tim'] . '" ' . $sel . '>' . $item['tim'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionName($team, $stream, $region, $b1, $current, $filtertdate)
{
    require 'config-urls.php';
    global $db, $_conf, $admin_management, $nav_employee_contract_ledger, $portal_users;
    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $employee_no = $_user['employee_no'];

    if (strpos($team, '-') !== false) {
        $pieces = explode('-', $team);
        $team = $pieces[0];
        $stream_query = " and (Stream_description='" . $pieces[1] . "' or Stream_description=N'" . $pieces[1] . "')";
    } else {

        $stream_query = '';
    }

    if ($_user['employee_no'] == $admin_management[0] and $_user['role'] == 2) {
        $check_management_level = " (cl.[Management Level]=6 or cl.[Management Level]=8) or ";
    } else {
        $check_management_level = "";
    }

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    if ($b1 != '') {

        $sistematizacije = Sistematizacija::getIDs($b1);
    }else{
        if($_user['role'] == 4){
            $sistematizacije = Sistematizacija::getIDs(1);
        }else{
            $sistematizacije = Sistematizacija::getIDs($_user['egop_ustrojstvena_jedinica']);
        }
    }



    $query = $db->query("select fname +' '+ lname as ime from [c0_intranet2_apoteke].[dbo].[users] where egop_ustrojstvena_jedinica in (".implode(',', $sistematizacije).") order by user_id");
    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['ime']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            $opt .= '<option value="' . $item['ime'] . '" ' . $sel . '>' . $item['ime'] . '</option>';


        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionNameEditProfile($current, $filtertdate)
{
    global $db, $_conf, $portal_users;
    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $employee_no = $_user['employee_no'];

    if ($_user['role'] == 4 or $_user['role'] == 0)
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE " . $employee_no . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
    elseif ($_user['role'] == 2)
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent='" . $_user['employee_no'] . "' or parent2='" . $_user['employee_no'] . "' or " . $_user['employee_no'] . " in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d)))";

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("select fname + ' ' + lname as name, user_id as user_id, employee_no as employee_no from users where user_id not in (23,41,45) and (termination_date>='" . $filtertdate . "' or termination_date is null) " . $role_query);

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['employee_no']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['employee_no'] . '" ' . $sel . '>' . $item['name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionSrodnici($current, $filtertdate)
{
    global $db, $_conf, $portal_users;
    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $employee_no = $_user['employee_no'];

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("select lname + ' ' + fname as name, user_id as user_id, employee_no as employee_no from " . $portal_users . " where user_id not in (23,41,45) and (termination_date>='" . $filtertdate . "' or termination_date is null) order by lname");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['employee_no']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['employee_no'] . '" ' . $sel . '>' . $item['name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionSrodstvo($current)
{
    global $db, $_conf, $nav_relative;
    $_user = _user(_decrypt($_SESSION['SESSION_USER']));

    $employee_no = $_user['employee_no'];

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT DISTINCT [Description] AS relative_code, [Code] FROM  " . $nav_relative . " where Relation=5");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['relative_code']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Code'] . '" ' . $sel . '>' . $item['relative_code'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionGrupaIzostanka($current)
{
    global $db, $_conf;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    global $portal_hourlyrate_status;

    $query = $db->query("select distinct status_group from  " . $portal_hourlyrate_status . "  where status_group<>'' and status_group is not null");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['status_group']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            if($item['status_group'] == 'R_REDOVNO' or $item['status_group'] == 'G - GODIŠNJI ODMOR' or $item['status_group'] == 'G - GODIŠNJI ODMOR' or
                $item['status_group'] == 'SL- SLUŽBENI PUT' or $item['status_group'] == 'SL- SLUŽBENI PUT'){
                $opt .= '<option value="' . $item['status_group'] . '" ' . $sel . '>' . $item['status_group'] . '</option>';
            }

        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}


function getBoja($status)
{
    global $db, $_conf;

    // Praznici
    $praznici = array(83, 84);

    // Godišnji
    $godisnji = array(18, 19, 106);

    // Bolovanje
    $bolovanje = array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 105);

    // Redovan rad
    $redovan = array(5);

    if (in_array($status, $redovan)) {
        // Redovan rad
        return "white";
    } else if (in_array($status, $praznici)) {
        // Praznici
        return "#ffb366";
    } else if (in_array($status, $godisnji)) {
        // Godisnji
        return "lightblue";
    } else if (in_array($status, $bolovanje)) {
        // Bolovanje
        return "blue";
    } else {
        return "white";
    }

}

function _optionStreamTeamWithGF($current)
{
    global $db, $_conf, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $get2 = $db->query("SELECT distinct Stream_description as Stream_description FROM  " . $portal_users . "  where Stream_description<>'' and Stream_description is not null order by Stream_description");
    $streams = $get2->fetchAll();

    $get2 = $db->query("SELECT distinct Team_description FROM  " . $portal_users . "  where Team_description<>'' and Team_description is not null order by Team_description");
    $teams = $get2->fetchAll();

    $get2 = $db->query("SELECT distinct B_1_regions_description FROM  " . $portal_users . "  where B_1_regions_description<>'' and B_1_regions_description is not null order by B_1_regions_description");
    $B_1_regions = $get2->fetchAll();

    $get2 = $db->query("SELECT distinct B_1_description FROM  " . $portal_users . "  where B_1_description<>'' and B_1_description is not null order by B_1_description");
    $B_1_description = $get2->fetchAll();


    $sel = '';
    foreach ($streams as $item) {
        if ($current == $item['Stream_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['Stream_description'] . '" ' . $sel . '>' . $item['Stream_description'] . '</option>';
    }

    foreach ($teams as $item) {
        if ($current == $item['Team_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['Team_description'] . '" ' . $sel . '>' . $item['Team_description'] . '</option>';
    }

    foreach ($B_1_regions as $item) {
        if ($current == $item['B_1_regions_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['B_1_regions_description'] . '" ' . $sel . '>' . $item['B_1_regions_description'] . '</option>';
    }

    foreach ($B_1_description as $item) {
        if ($current == $item['B_1_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['B_1_description'] . '" ' . $sel . '>' . $item['B_1_description'] . '</option>';
    }


    return $opt;

}

function _optionStreamTeam($current)
{
    global $db, $_conf, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $get2 = $db->query("SELECT distinct Stream_description as Stream_description FROM  " . $portal_users . "  where Stream_description<>'' and Stream_description is not null order by Stream_description");
    $streams = $get2->fetchAll();

    $get2 = $db->query("SELECT distinct Team_description FROM  " . $portal_users . "  where Team_description<>'' and Team_description is not null order by Team_description");
    $teams = $get2->fetchAll();


    $sel = '';
    foreach ($streams as $item) {
        if ($current == $item['Stream_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['Stream_description'] . '" ' . $sel . '>' . $item['Stream_description'] . '</option>';
    }

    foreach ($teams as $item) {
        if ($current == $item['Team_description']) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $item['Team_description'] . '" ' . $sel . '>' . $item['Team_description'] . '</option>';
    }


    return $opt;

}

function _optionPodrucjePrimjene($current)
{
    global $db, $_conf, $portal_holidays_per_department;

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';

    $query = $db->query("select distinct [department name] from  " . $portal_holidays_per_department . " ");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($item['department name'] == '')
                $dep_name = 'Svi';
            else
                $dep_name = $item['department name'];
            if ($current == $item['department name']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $dep_name . '" ' . $sel . '>' . $dep_name . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionNazivPraznika($current)
{
    global $db, $_conf, $portal_holidays_per_department;

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';

    $query = $db->query("select distinct [holiday_name] from  " . $portal_holidays_per_department . " ");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {

            if ($current == $item['holiday_name']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['holiday_name'] . '" ' . $sel . '>' . $item['holiday_name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionStreamVacations($current)
{
    global $db, $_conf;
    global $_user, $portal_departments;


    $opt = '<option value="">' . __('Odaberi Regiju') . '</option>';
    $query = $db->query("select distinct B_1_regions_description FROM  " . $portal_departments . "  where B_1_regions_description<>'' and B_1_regions_description is not null");

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['B_1_regions_description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['B_1_regions_description'];
            $opt .= '<option value="' . $item['B_1_regions_description'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    }

    return $opt;

}

function _optionSectorVacations($b1, $current)
{
    global $db, $_conf;
    global $_user, $portal_users;

    if ($_user['managment_level'] == '2') {
        $query = $db->query("select distinct sector FROM  " . $portal_users . "  where B_1_description = N'" . $b1 . "'");
        $count = $db->query("select count(distinct sector) as broj FROM  " . $portal_users . "  where B_1_description = N'" . $b1 . "'");
        $count1 = $count->fetch();
        $count_sector = $count1['broj'];
    } elseif ($_user['managment_level'] == '4' or $_user['managment_level'] == '3')
        $query = $db->query("select distinct sector FROM  " . $portal_users . "  where B_1_description = N'" . $b1 . "' and sector = N'" . $_user['sector'] . "'");


    $opt = '<option value="">' . __('Odaberi OJ') . '</option>';

    if ($_user['managment_level'] == '2') {
        echo ($query->rowCount()) . 'denis';
        if ($query->rowCount() < 0) {

            $sel = '';
            foreach ($query as $item) {
                if ($current == $item['sector'] or $count_sector == 1) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
                $sector = $item['sector'];
                $opt .= '<option value="' . $item['sector'] . '" ' . $sel . '>' . $sector . '</option>';
            }

        } else {
            $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';
        }
    } else
        $opt = '<option value="' . $_user['sector'] . '" selected="selected">' . $_user['sector'] . '</option>';

    return $opt;

}

function _optionNameVacations($sector, $current)
{
    global $db, $_conf;
    global $_user, $portal_users;

    if ($sector == '') {
        if ($_user['managment_level'] == '2')
            $query = $db->query("select lname + ' ' + fname as name from  " . $portal_users . "  where B_1_description = N'" . $_user['B_1_description'] . "' and termination_date is null");
        elseif ($_user['managment_level'] == '4' or $_user['managment_level'] == '3')
            $query = $db->query("select lname + ' ' + fname as name from  " . $portal_users . "  where sector = N'" . $_user['sector'] . "' and termination_date is null");
    } else
        $query = $db->query("select lname + ' ' + fname as name from  " . $portal_users . "  where sector = N'" . $sector . "' and termination_date is null");


    $opt = '<option value="">' . __('Odaberi Ime') . '</option>';


    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['name']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['name'];
            $opt .= '<option value="' . $item['name'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}


function _optionRegionVacations($stream, $current)
{
    global $db, $_conf, $portal_users;

    if ($stream == '') {
        $query = $db->query("select distinct B_1_regions_description FROM  " . $portal_users . "  where B_1_regions_description is not null and B_1_regions_description<>''");
    } else
        $query = $db->query("select distinct B_1_regions_description FROM  " . $portal_users . "  where B_1_regions_description is not null and B_1_regions_description<>'' and Stream_description='" . $stream . "'");

    $opt = '<option value="">' . __('Odaberi regiju') . '</option>';

    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['B_1_regions_description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['B_1_regions_description'];
            $opt .= '<option value="' . $item['B_1_regions_description'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionTypeVacations($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi šifru') . '</option>';

    $query = $db->query("select distinct type FROM  " . $portal_hourlyrate_status . "  where id<>5");
    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['type']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['type'];
            $opt .= '<option value="' . $item['type'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionType_DescriptionVacations($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi opis') . '</option>';

    $query = $db->query("select distinct type_description FROM  " . $portal_hourlyrate_status . "  where id<>5 and type_description<>''");
    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['type_description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['type_description'];
            $opt .= '<option value="' . $item['type_description'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}

function _optionGroup_DescriptionVacations($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi grupu') . '</option>';

    $query = $db->query("select distinct group_description FROM  " . $portal_hourlyrate_status . "  where id<>5 and group_description<>''");
    if ($query->rowCount() < 0) {

        $sel = '';
        foreach ($query as $item) {
            if ($current == $item['group_description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $sector = $item['group_description'];
            $opt .= '<option value="' . $item['group_description'] . '" ' . $sel . '>' . $sector . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih opcija') . '</option>';

    }

    return $opt;

}


function _optionTaskStatus($current)
{
    $roles = array(
        '1' => __('Iznad plana'),
        '2' => __('U toku'),
        '3' => __('U kašnjenju'),
        '4' => __('Izmjena cilja'),
        '5' => __('Nije relevantan'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionTaskYearMBO($current)
{
    global $db, $_conf, $portal_objective_deadline;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT distinct year as year FROM  " . $portal_objective_deadline . "  ORDER BY year ASC");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['year']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['year'] . '" ' . $sel . '>' . $item['year'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema dostupnih jezika.') . '</option>';

    }

    return $opt;

}

function _optionObukaStatus($current)
{
    $roles = array(
        '1' => __('Nije započeto'),
        '2' => __('U toku'),
        '3' => __('U kašnjenju'),
        '4' => __('Završeno'),
        '5' => __('Odgođeno'),
        '6' => __('Nije relevantno'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionObukaTestStatus($current)
{
    $roles = array(
        '4' => __('U potpunosti savladano'),
        '3' => __('Djelomično savladano'),
        '2' => __('Nije savladano'),
        '1' => __('Nije relevantno'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionObukaOcjena($current)
{
    $roles = array(
        '1' => __('1'),
        '2' => __('2'),
        '3' => __('3'),
        '4' => __('4'),
        '5' => __('5'),
        '6' => __('Nije relevantno'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionPages($current)
{
    $roles = array(
        '20' => __('20'),
        '50' => __('50'),
        '100' => __('100'),
        '150' => __('150'),
        '200' => __('200'),
    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionObukaOcjenaMentora($current)
{
    $roles = array(
        '1' => __('U potpunosti se slažem'),
        '2' => __('Slažem se'),
        '3' => __('Nisam siguran/na'),
        '4' => __('Ne slažem se'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionZaduzeno($current)
{
    $roles = array(
        '2' => __('Odaberi...'),
        '0' => __('NE'),
        '1' => __('DA'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {

        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionOdobreno($current)
{
    $roles = array(
        '' => __('Odaberi...'),
        '2' => __('NE'),
        '1' => __('DA'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {

        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionOtkazano($current)
{
    $roles = array(
        '1' => __('Odaberi...'),
        '2' => __('DA'),
        '0' => __('NE'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {

        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionSaglasan($current)
{
    $roles = array(
        //  '2'=>__('Odaberi...'),
        '0' => __('NE'),
        '1' => __('DA'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {

        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _transformSaglasan($current)
{
    if ($current == 0)
        return 'NE';
    else
        return 'DA';
}

function _transformZaduzeno($current)
{
    if ($current == 0)
        return 'NE';
    elseif ($current == 1)
        return 'DA';
    else
        return '';
}

function _optionTaskOcjena($current)
{
    $roles = array(
        '1' => __('1'),
        '2' => __('2'),
        '3' => __('3'),
        '4' => __('4'),
        '5' => __('5'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;


}

function _optionMBOProfil($current)
{
    $roles = array(
        '0' => __('Lični podaci'),
        '1' => __('Podaci o poziciji'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionMBOProfilHR($current)
{
    $roles = array(
        '0' => __('Naziv OJ - Ime i prezime zaposlenika'),
        '1' => __('Moj MBO profil'),
        '2' => __('Naziv B-1 - Naziv OJ - Ime i prezime zaposlenika'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionLanguageSkill($current)
{
    $roles = array(
        '0' => __('Početni nivo'),
        '1' => __('Srednji nivo'),
        '2' => __('Napredni nivo'),
        '3' => __('Ekspertni nivo'),
    );

    $opt = '';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _getSkillById($id)
{
    $roles = array(
        '0' => __('Početni nivo'),
        '1' => __('Srednji nivo'),
        '2' => __('Napredni nivo'),
        '3' => __('Ekspertni nivo'),
    );

    return $roles[$id];

}

function _optionEducationType($current)
{
    $roles = array(
        '0' => __('Seminar'),
        '1' => __('Konferencija'),
        '2' => __('Funkcionalne vještine'),
        '3' => __('Rukovodstvene vještine'),
        '4' => __('IT usavršavanje'),
        '5' => __('Certifikat'),
        '6' => __('Licenca'),
        '7' => __('Obavezno'),
        '8' => __('Ostalo'),

    );

    $opt = '';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionMaritalStatus($current)
{
    $roles = array(

        '1' => __('Oženjen/Udata'),
        '2' => __('Neoženjen/Neudata'),
        '3' => __('Razveden/Razvedena'),
        '4' => __('Udovac/Udovica'),
        '5' => __('Vanbračna zajednica')

    );

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionBloodTypeNAV($current)
{
    $roles = array(

        '1' => __('0-'),
        '2' => __('0+'),
        '3' => __('A-'),
        '4' => __('A+'),
        '5' => __('B-'),
        '6' => __('B+'),
        '7' => __('AB-'),
        '8' => __('AB+'),

    );

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionVozackaNAV($current)
{
    $roles = array(

        '0' => __('NE'),
        '1' => __('DA'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionAktivanVozacNAV($current)
{
    $roles = array(

        '0' => __('NE'),
        '1' => __('DA'),

    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionTipVozackeNAV($current)
{
    $roles = array(

        '1' => __('A'),
        '2' => __('B'),
        '3' => __('BE'),
        '4' => __('C'),
        '5' => __('CE'),
        '6' => __('D'),
        '7' => __('DE'),

    );

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionEducationLevelNAV($current)
{
    $roles = array(

        '1' => __('NSS - Niža stručna sprema'),
        '2' => __('SSS - Srednja stručna sprema'),
        '3' => __('KV   - Kvalificirani radnik'),
        '4' => __('VKV - Visoko kvalificirani radnik'),
        '5' => __('VS   - Viša stručna sprema'),
        '6' => __('VSS - Visoka stručna sprema'),
        '7' => __('MR   - Magistar'),
        '8' => __('DR   - Doktor nauka'),

    );

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _inputEducationLevelNAV($current)
{
    $roles = array(
        '' => __(' '),
        '1' => __('NSS - Niža stručna sprema'),
        '2' => __('SSS - Srednja stručna sprema'),
        '3' => __('KV   - Kvalificirani radnik'),
        '4' => __('VKV - Visoko kvalificirani radnik'),
        '5' => __('VS   - Viša stručna sprema'),
        '6' => __('VSS - Visoka stručna sprema'),
        '7' => __('MR   - Magistar'),
        '8' => __('DR   - Doktor nauka'),
        '9' => __('PK- Priučeni-Polukvalifikovani'),
        '10' => __('NK-Nekvalifikovani '),

    );

    return $roles[$current];

}

function _inputNivoRukovodstva($current)
{
    $roles = array(
        '' => __('-'),
        '0' => __(' '),
        '1' => __('B'),
        '2' => __('B1'),
        '3' => __('B2'),
        '4' => __('B3'),
        '5' => __('B4'),
        '6' => __('CEO'),
        '7' => __('E'),
        '8' => __('Exe'),
        'B' => __('B'),
        'B1' => __('B1'),
        'B2' => __('B2'),
        'B3' => __('B3'),
        'B4' => __('B4'),
        'CEO' => __('CEO'),
        'E' => __('E'),
        'Exe' => __('Exe'),
    );

    return $roles[$current];

}

function _inputLanguageLevelNAV($current)
{
    $roles = array(

        '0' => __('Osnovno'),
        '1' => __('Srednje'),
        '2' => __('Aktivno'),
        '3' => __('Napredno'),


    );

    return $roles[$current];

}

function _optionLanguageLevelNAV($current)
{
    $roles = array(

        '0' => __('Osnovno'),
        '1' => __('Srednje'),
        '2' => __('Aktivno'),
        '3' => __('Napredno'),


    );

    $opt = '<option value="">' . __('Odaberi...') . '</option>';


    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _optionLanguageCodeNAV($current)
{
    global $db, $_conf, $nav_languages;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_languages . "  ORDER BY [Description]");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Description'] . '" ' . $sel . '>' . $item['Description'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih jezika.') . '</option>';

    }

    return $opt;

}

function _optionGetLanguageCodeNAV($current)
{
    global $db, $_conf, $nav_languages;


    $query = $db->query("SELECT [Code] as Code FROM  " . $nav_languages . "  where [Description] = '$current'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['Code'];
        }

    } else {
        $ime = '';
    }

    return $ime;

}

function _optionCertifikatCodeNAV($current)
{
    global $db, $_conf, $nav_qualification;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_qualification . "  ORDER BY [Description]");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Description'] . '" ' . $sel . '>' . $item['Description'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih certifikata.') . '</option>';

    }

    return $opt;

}

function _optionGetQualificationCodeNAV($current)
{
    global $db, $_conf, $nav_qualification;


    $query = $db->query("SELECT [Code] as Code FROM  " . $nav_qualification . "  where [Description] = N'" . $current . "'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['Code'];
        }

    } else {
        $ime = '';
    }

    return $ime;

}

function _optionInstitutionCodeNAV($current)
{
    global $db, $_conf, $nav_institution_company;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_institution_company . "  WHERE [Type] = 1");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Description']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Description'] . '" ' . $sel . '>' . $item['Description'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih institucija.') . '</option>';

    }

    return $opt;

}

function _optionCountryCodeNAV($current)
{
    global $db, $_conf, $nav_country_region;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT DISTINCT [Country Code] FROM  " . $nav_country_region . "  WHERE [Country Code]<>''");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Country Code']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Country Code'] . '" ' . $sel . '>' . $item['Country Code'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}

function _optionCountryCodeNAVBirth($current)
{
    global $db, $_conf, $nav_country_region;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_country_region . " ");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Code']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Code'] . '" ' . $sel . '>' . $item['Code'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}

function _optionCityCodeNAVBirth($current)
{
    global $db, $_conf, $nav_post_code;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_post_code . " ");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['City']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['City'] . '" ' . $sel . '>' . $item['City'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih gradova.') . '</option>';

    }

    return $opt;

}

function _optionRegionCodeNAVHome($current)
{
    global $db, $_conf, $nav_dial_codes;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT distinct [No_] FROM  " . $nav_dial_codes . " ");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['No_']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['No_'] . '" ' . $sel . '>' . $item['No_'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih regija.') . '</option>';

    }

    return $opt;

}

function _optionRegionCodeNAVMobile($current)
{
    global $db, $_conf, $nav_dial_codes;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $nav_dial_codes . "  WHERE [Type] = 1");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['No_']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['No_'] . '" ' . $sel . '>' . $item['No_'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}

function _optionMunicipalityCodeNAV($current)
{
    global $db, $_conf, $nav_municipality;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT [Code],[Name] FROM  " . $nav_municipality . " ");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['Code']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['Code'] . '" ' . $sel . '>' . $item['Code'] . ' ' . $item['Name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}

function _optionGetNamebyCodeMunNAV($current)
{
    global $db, $_conf, $nav_municipality;


    $query = $db->query("SELECT [Name] as name, [City] as city FROM  " . $nav_municipality . "  where Code = '$current'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['name'] . ";" . $item['city'];
        }

    } else {
    }

    return $ime;

}

function _optionGetLastNameNAV($employee_no)
{
    global $db, $_conf, $nav_employee;

    $query = $db->query("SELECT [Last Name] FROM  " . $nav_employee . "  where No_ = '$employee_no'");
    $fetch = $query->fetchAll();

    $result = $fetch[0]['Last Name'];

    return $result;
}

function _optionGetCounryDescNAV($current)
{
    global $db, $_conf, $nav_citizenship_description;


    $query = $db->query("SELECT [Description] as Description FROM  " . $nav_citizenship_description . "  where No_ = '$current'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['Description'];
        }

    } else {
        $ime = "";
    }

    return $ime;

}

function _optionGetDisabilityLevelNAV($current)
{
    global $db, $_conf, $nav_employee_level_of_disability;


    $query = $db->query("SELECT [Description] as Description FROM  " . $nav_employee_level_of_disability . "  where Code = '$current'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['Description'];
        }

    } else {
        $ime = '';
    }

    return $ime;

}

function _optionGetUnionNameNAV($current)
{
    global $db, $_conf, $nav_union;


    $query = $db->query("SELECT [Name] as Name FROM  " . $nav_union . "  where Code = '$current'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            $ime = $item['Name'];
        }

    } else {
        $ime = '';
    }

    return $ime;

}

function _getEducationById($id)
{
    $roles = array(
        '0' => __('Seminar'),
        '1' => __('Konferencija'),
        '2' => __('Funkcionalne vještine'),
        '3' => __('Rukovodstvene vještine'),
        '4' => __('IT usavršavanje'),
        '5' => __('Team building'),
        '6' => __('Sberbank Akademija'),
        '7' => __('Obavezno'),
        '8' => __('Ostalo'),
    );

    return $roles[$id];

}

function _optionKompetencije($current, $user)
{

    global $_user;
    $management_level = _user($user)['managment_level'];
    $rukovodioci = array(2, 3, 4);
    if (in_array($management_level, $rukovodioci))
        $roles = array(
            '0' => __('Sve za klijenta - Orjentisanost na klijenta'),
            '1' => __('Mi smo tim - Saradnja / Timski Rad'),
            '2' => __('Mi smo tim - Fleksibilnost'),
            '3' => __('Sve za klijenta - Kvalitet / Tačnost'),
            '4' => __('Ja sam vođa - Strateško djelovanje (obavezno samo za rukovodioce)'),
            '5' => __('Ja sam vođa - Vodstvo (Obavezno samo za rukovodioce)'),
        );
    else
        $roles = array(
            '0' => __('Sve za klijenta - Orjentisanost na klijenta'),
            '1' => __('Mi smo tim - Saradnja / Timski Rad'),
            '2' => __('Mi smo tim - Fleksibilnost'),
            '3' => __('Sve za klijenta - Kvalitet / Tačnost'),
        );
    $opt = '';

    foreach ($roles as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $opt .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }

    return $opt;

}

function _section_by_department($department)
{
    global $db, $_conf, $portal_departments;

    $query = $db->query("select code,description from   " . $portal_departments . "  where code = '" . $department . "'");
    foreach ($query as $item) {
        $opt = $item['description'];
    }

    if (isset($opt))
        return $opt;
    else
        return '';

}

function _getSexById($id)
{
    $roles = array(
        '1' => __('Ženski'),
        '2' => __('Muški'),

    );

    return $roles[$id];

}


// Dropdown select for countries
function _optionCountry($current)
{
    global $db, $_conf, $portal_countries;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_countries . "  ORDER BY name ASC");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['country_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['country_id'] . '" ' . $sel . '>' . $item['name'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}

function _optionPodredzeni($current)
{
    global $db, $_conf;
    global $_user, $portal_users;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_users . "  where parent = '" . $_user['employee_no'] . "'");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['employee_no']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['employee_no'] . '" ' . $sel . '>' . $item['fname'] . ' ' . $item['lname'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih zamjenika.') . '</option>';

    }

    return $opt;

}

function _optionCountryWage($current)
{
    global $db, $_conf, $portal_countries;

    $opt = '<option value="0">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_countries . "   where country_id = " . $current . "");
    if ($query->rowCount() < 0) {

        foreach ($query as $item) {
            if ($current == $item['country_id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['wage'] . '" ' . $sel . '>' . $item['wage'] . '</option>';
        }

    } else {

        $opt = '<option value="">' . __('Nema unesenih država.') . '</option>';

    }

    return $opt;

}


// Dropdown select for status (hourlyrate)
function _optionHRstatus($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level >=3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='G - GODIŠNJI ODMOR' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('G - GODIŠNJI ODMOR') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='V - VJERSKI' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('V – VJERSKI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group=N'P – OSTALA PLAĆENA ODSUSTVA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – OSTALA PLAĆENA ODSUSTVA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NE – NEOPRAVDANO ODSUSTVO' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NE – NEOPRAVDANO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - BOLEST' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - BOLEST') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - POVREDA NA RADU' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - POVREDA NA RADU') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - CUVANJE TRUDNOCE' and level >=3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - ČUVANJE TRUDNOĆE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – PORODILJSKO ODSUSTVO' and level >=3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – PORODILJSKO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE NJEGA CLANA OBITELJI' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE NJEGA ČLANA OBITELJI') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='D – DRŽAVNI PRAZNIK' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('D – DRŽAVNI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PLACENO ODSUSTVO' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PLACENO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SL- SLUŽBENI PUT' and level >=3 and id=73 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SL- SLUŽBENI PUT') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='S – SUSPENZIJA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('S – SUSPENZIJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PRAZNIK' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    return $opt;

}

function _optionHRstatusOdsustva($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level >=3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='G - GODIŠNJI ODMOR' and level is null ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('G - GODIŠNJI ODMOR') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='V – VJERSKI PRAZNIK' and level is null ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('V – VJERSKI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group=N'P – OSTALA PLAĆENA ODSUSTVA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – OSTALA PLAĆENA ODSUSTVA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NE – NEOPRAVDANO ODSUSTVO' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NE – NEOPRAVDANO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - BOLEST' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - BOLEST') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - POVREDA NA RADU' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - POVREDA NA RADU') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE - CUVANJE TRUDNOCE' and level >=3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE - ČUVANJE TRUDNOĆE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – PORODILJSKO ODSUSTVO' and level >=3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – PORODILJSKO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJE NJEGA CLANA OBITELJI' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJE NJEGA ČLANA OBITELJI') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level =4 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='D – DRŽAVNI PRAZNIK' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('D – DRŽAVNI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PLACENO ODSUSTVO' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PLACENO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SL- SLUŽBENI PUT' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SL- SLUŽBENI PUT') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='S – SUSPENZIJA' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('S – SUSPENZIJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PRAZNIK' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    return $opt;

}

function _optionHRstatusPre($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PR –  PREKOVREMENI RAD' and level >=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PR –  PREKOVREMENI RAD') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    return $opt;

}

function _optionHRstatusPreKontaktCentar($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 and name<>'R_7' ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    return $opt;

}

// Dropdown select for status (hourlyrate) level 3 - contact centar
function _optionHRstatusLevelKontakCentarWeekend($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 and name<>'R_7' ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    return $opt;

}

// Dropdown select for status (hourlyrate) level 3 - contact centar
function _optionHRstatusLevelKontakCentar($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='G - GODIŠNJI ODMOR' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('G - GODIŠNJI ODMOR') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='V - VJERSKI' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('V – VJERSKI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='S – SMRTNI SLUCAJ' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('S – SMRTNI SLUCAJ') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SD – SLOBODAN DAN' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SD – SLOBODAN DAN') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – PORODILJSKO ODSUSTVO' and level =3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – PORODILJSKO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='D – DRŽAVNI PRAZNIK' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('D – DRŽAVNI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PLACENO ODSUSTVO' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PLACENO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SL- SLUŽBENI PUT' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SL- SLUŽBENI PUT') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R – DODATNO' and level >=3 and name<>'R_7' ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R – DODATNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    return $opt;

}

function _optionHRstatusLevelKontakCentarRadnik($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='G - GODIŠNJI ODMOR' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('G - GODIŠNJI ODMOR') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='V - VJERSKI' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('V – VJERSKI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='S – SMRTNI SLUCAJ' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('S – SMRTNI SLUCAJ') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SD – SLOBODAN DAN' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SD – SLOBODAN DAN') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – PORODILJSKO ODSUSTVO' and level =3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – PORODILJSKO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='D – DRŽAVNI PRAZNIK' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('D – DRŽAVNI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PLACENO ODSUSTVO' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PLACENO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SL- SLUŽBENI PUT' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SL- SLUŽBENI PUT') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    return $opt;

}

// Dropdown select for status (hourlyrate) level 3
function _optionHRstatusLevel3($current)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $opt = '<option value="">' . __('Odaberi...') . '</option>';

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='R_REDOVNO' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('R_REDOVNO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='G - GODIŠNJI ODMOR' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('G - GODIŠNJI ODMOR') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='V - VJERSKI' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('V – VJERSKI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='S – SMRTNI SLUCAJ' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('S – SMRTNI SLUCAJ') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }
        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SD – SLOBODAN DAN' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SD – SLOBODAN DAN') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('PU - PLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE' and level =3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('NU – NEPLACENO ODSUSTVO UZ ODLUKU UPRAVE') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – BOLOVANJA' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – BOLOVANJA') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='B – PORODILJSKO ODSUSTVO' and level =3 ORDER BY id ASC");
    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('B – PORODILJSKO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }


    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='D – DRŽAVNI PRAZNIK' and level=3 ORDER BY id ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('D – DRŽAVNI PRAZNIK') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='P – PLACENO ODSUSTVO' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('P – PLACENO ODSUSTVO') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  where status_group='SL- SLUŽBENI PUT' and level =3 ORDER BY name ASC");

    if ($query->rowCount() < 0) {
        $opt .= '<optgroup label="' . __('SL- SLUŽBENI PUT') . '">';
        foreach ($query as $item) {
            if ($current == $item['id']) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }
            $opt .= '<option value="' . $item['id'] . '" ' . $sel . '>' . $item['name'] . '' . __(' ') . '' . $item['description'] . '</option>';
        }


        $opt .= '</optgroup>';
    }
    return $opt;

}

// Get name of cointry by ID
function _nameCountry($id)
{
    global $db, $_conf, $portal_countries;

    $query = $db->query("SELECT TOP 1 name FROM  " . $portal_countries . "  WHERE country_id='$id'");
    if ($query) {

        $row = $query->fetch();
        return __($row['name']);

    }

}

//Get message by message_id
function _message($message_id)
{
    global $db, $_conf, $portal_messages;

    $query = $db->query("SELECT TOP 1 message FROM  " . $portal_messages . "  WHERE message_id='$message_id'");
    if ($query) {

        $row = $query->fetch();
        return $row['message'];

    }

}

global $portal_hourlyrate_status, $db;
$hr_status = $db->prepare("SELECT * FROM  " . $portal_hourlyrate_status . " ");
$hr_status->execute();
$hr_statusi = $hr_status->fetchAll();
$hr_fill = array();

foreach ($hr_statusi as $ky => $vy) {
    $hr_fill[$vy['id']] = $vy['description'];
}


// Get name of HRstatus by ID
function _nameHRstatus($id)
{
    if ($id == null)
        return '';
    global $db, $_conf, $hr_fill;
    /*
  $query = $db->query("SELECT TOP 1 * FROM  ".$portal_hourlyrate_status."  WHERE id='$id'");
  if($query){

    $row = $query->fetch();
    return __($row['description']);

  } */

    return $hr_fill[$id];


}

// Get group_description of HRstatus by ID
function _nameHRstatusGroup($id)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $query = $db->query("SELECT TOP 1 * FROM  " . $portal_hourlyrate_status . "  WHERE id='$id'");
    if ($query) {

        $row = $query->fetch();
        return __($row['group_description']);

    }

}

// Get name of HRstatus by ID
function _nameHRstatusName($id)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $query = $db->query("SELECT TOP 1 * FROM  " . $portal_hourlyrate_status . "  WHERE id='$id'");
    if ($query) {

        $row = $query->fetch();
        return __($row['name']);

    }

}


// Get username by login session ID
function _user($id)
{
    global $db, $_conf, $portal_users;

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='$id'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

// Get employee by employeee_no
function _employee($id)
{
    global $db, $_conf, $portal_users;

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='$id'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

// Get employee by employeee_no
function _employeeNAV($id)
{
    global $db, $_conf, $nav_employee;

    $query = $db->query("SELECT * FROM  " . $nav_employee . "  WHERE [No_]='$id'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

// Get employee by username
function _username($username)
{
    global $db, $_conf, $portal_users;

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE username='$username'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

// Get user by name
function _name($name)
{
    global $db, $_conf, $portal_users;

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE fname + ' ' + lname=N'$name'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

//get tab by name
function _tab($tab)
{
    global $db, $_conf, $portal_tabs;

    $query = $db->query("SELECT * FROM  " . $portal_tabs . "  WHERE Tab='$tab'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

// Get type description from type
function _type_description($type)
{
    global $db, $_conf, $portal_hourlyrate_status;

    $query = $db->query("SELECT * FROM  " . $portal_hourlyrate_status . "  WHERE type='$type'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row['type_description'];

    }

}


function _role($role)
{

    if ($role == 0) {
        return __('Administrator');
    } else if ($role == 1) {
        return __('HR');
    } else if ($role == 2) {
        return __('Nadređeni');
    } else if ($role == 3) {
        return __('Administrator satnica');
    } else if ($role == 4) {
        return __('Menadžer administratora satnica');
    } else {
        return __('Zaposlenik');
    }

}


// Encrypt string
function _encrypt($q)
{

    //$encoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( ENC_KEY ), $q, MCRYPT_MODE_CBC, md5( md5( ENC_KEY ) ) ) );
    //return( $encoded );
    $key = md5(ENC_KEY);
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($q, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}


// Decrypt string
function _decrypt($q)
{

    //$decoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( ENC_KEY ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( ENC_KEY ) ) ), "\0");
    //return( $decoded );
    $key = md5(ENC_KEY);
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($q), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);

}


// Pagination
function _pagination($path, $page, $limit, $total)
{
    //$total++;

    $adjacents = 3;
    $prev = $page - 1;
    $next = $page + 1;
    if ($total % $limit == 0 or $total == 1) {
        $lastpage = ceil($total / $limit);
    } else {
        $lastpage = ceil($total / $limit);
    }
    $lpm1 = $lastpage - 1;
    $pagination = "";
    if ($lastpage > 1) {
        if ($page > 1)
            $pagination .= "<a class='btn btn-default' href='" . $path . $prev . "'><i class='ion-ios-arrow-left'></i></a>";
        else
            $pagination .= "<span  class='btn btn-default disabled'>«</span>";
        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= "<span  class='btn btn-default active'>" . $counter . "</span>";
                else
                    $pagination .= "<a class='btn btn-default' href='" . $path . $counter . "'>$counter</a>";
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span  class='btn btn-default active'>" . $counter . "</span>";
                    else
                        $pagination .= "<a class='btn btn-default' href='" . $path . $counter . "'>" . $counter . "</a>";
                }
                $pagination .= "<span  class='btn btn-default'>...</span>";
                $pagination .= "<a class='btn btn-default' href='" . $path . $lpm1 . "'>" . $lpm1 . "</a>";
                $pagination .= "<a class='btn btn-default' href='" . $path . $lastpage . "'>" . $lastpage . "</a>";
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<a class='btn btn-default' href='" . $path . "1'>1</a>";
                $pagination .= "<a class='btn btn-default' href='" . $path . "2'>2</a>";
                $pagination .= "<span  class='btn btn-default'>...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span  class='btn btn-default active'>" . $counter . "</span>";
                    else
                        $pagination .= "<a class='btn btn-default' href='" . $path . $counter . "'>" . $counter . "</a>";
                }
                $pagination .= "<span  class='btn btn-default'>...</span>";
                $pagination .= "<a class='btn' href='" . $path . $lpm1 . "'>" . $lpm1 . "</a>";
                $pagination .= "<a class='btn' href='" . $path . $lastpage . "'>" . $lastpage . "</a>";
            } else {
                $pagination .= "<a class='btn btn-default' href='" . $path . "1'>1</a>";
                $pagination .= "<a class='btn btn-default' href='" . $path . "2'>2</a>";
                $pagination .= "<span  class='btn btn-default'>...</span>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span  class='btn btn-default active'>" . $counter . "</span>";
                    else
                        $pagination .= "<a class='btn btn-default' href='" . $path . $counter . "'>" . $counter . "</a>";
                }
            }
        }
        if ($page < $counter - 1)
            $pagination .= "<a class='btn btn-default' href='" . $path . $next . "'><i class='ion-ios-arrow-right'></i></a>";
        else
            $pagination .= "<span  class='btn btn-default disabled'>»</span>";
    }
    return $pagination;
}


function _url($string)
{
    setlocale(LC_ALL, 'en_US.UTF8');
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    $string = strtolower($string);
    $string = substr($string, 0, 128);
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    $string = preg_replace("/[\s-]+/", " ", $string);
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}


function __mailer($arr)
{
    global $site_domain, $root;

    if (_settings('smtp_status') == '1') {

        $site_domain = _settings('smtp_email');
        $site_mail = _settings('smtp_email');
        $smtp_secure = _settings('smtp_security');
        $smtp_host = _settings('smtp_host');
        $smtp_port = _settings('smtp_port');
        $smtp_user = _settings('smtp_user');
        $smtp_pass = _settings('smtp_password');

        require_once($root . '/CORE/smtp/PHPMailerAutoload.php');

        $subject = '=?UTF-8?B?' . base64_encode($arr['subject']) . '?=';
        $body = $arr['message'];
        $from = $arr['reply'];

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Host = $smtp_host;
        $mail->Port = $smtp_port;
        $mail->Username = $smtp_user;
        $mail->Password = $smtp_pass;
        $mail->CharSet = 'utf-8';
        $mail->AddReplyTo($from, '..');
        $mail->SetFrom($from, $site_domain);
        $mail->Subject = $subject;
        $mail->ContentType = 'text/html';
        $mail->IsHTML(true);
        $mail->Body = $body;
        $mail->AddAddress($arr['to'], '...');

        if (!$mail->Send()) {
            return false;
        } else {
            return true;
        }

    } else {

        $subject = '=?UTF-8?B?' . base64_encode($arr['subject']) . '?=';
        $body = $arr['message'];
        $from = $arr['reply'];

        $mime_boundary = "----[REA-studio]Kontakt forma ----" . md5(time());
        $headers = "From: <$from>\n";
        $headers .= "Reply-To: <$from>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
        $message = "--$mime_boundary\n";
        $message .= "Content-Type: text/html; charset=UTF-8\n";
        $message .= "Content-Transfer-Encoding: 8bit\n\n";
        $message .= $body;

        if (mail($arr['to'], $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }

    }
}


function _settings($key, $default = NULL)
{
    global $db, $_conf, $portal_settings;

    $get = $db->query("SELECT value FROM  " . $portal_settings . "  WHERE name='$key'");

    if ($get->rowCount() < 0) {
        $row = $get->fetch();
        return $row['value'];
    } else {
        if ($default == NULL) {
            return '';
        } else {
            return $default;
        }
    }

}

function _vacation_statistics($key, $default = NULL)
{
    global $db, $_conf, $portal_vacation_statistics;

    $query = $db->query("SELECT * FROM  " . $portal_vacation_statistics . "  where User_ID = '$key'");
    $get2 = $db->query("SELECT COUNT(*) FROM  " . $portal_vacation_statistics . "  where User_ID = '$key'");
    $result = $get2->fetch();
    $total = $get2->rowCount();


    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}


function _selected($type, $name)
{

    $sel = '';
    $value = _settings($name);

    if ($value == 1) {

        if ($type == 'checkbox') {
            $sel = 'CHECKED';
        } elseif ($type == 'select') {
            $sel = 'selected="selected"';
        }

    } else {

        $sel = '';

    }

    return $sel;

}


function _checkFile($path, $filename)
{

    if ($pos = strrpos($filename, '.')) {
        $name = substr($filename, 0, $pos);
        $ext = substr($filename, $pos);
    } else {
        $name = $filename;
    }

    $newpath = $path . $filename;
    $newname = $filename;
    $counter = 0;

    while (file_exists($newpath)) {
        $newname = $name . '_' . $counter . $ext;
        $newpath = $path . $newname;
        $counter++;
    }

    return $newname;

}

function _checkFile1($path, $filename, $employee_no)
{
    $filename1 = $employee_no;

    $arr = array();
    $arr = explode(".", $filename);
    $filename = $filename1 . "." . $arr[1];


    if ($pos = strrpos($filename, '.')) {
        $name = substr($filename, 0, $pos);
        $ext = substr($filename, $pos);
    } else {
        $name = $filename;
    }

    $newpath = $path . $filename;
    $newname = $filename;

    return $newname;
}


function rmdir_recursive($dir)
{

    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    if (rmdir($dir)) {
        return true;
    }

}


$months = array(
    '1' => __('Januar'),
    '2' => __('Februar'),
    '3' => __('Mart'),
    '4' => __('April'),
    '5' => __('Maj'),
    '6' => __('Juni'),
    '7' => __('Juli'),
    '8' => __('Avgust'),
    '9' => __('Septembar'),
    '10' => __('Oktobar'),
    '11' => __('Novembar'),
    '12' => __('Decembar')
);


function _optionYear($current)
{

    $year = '<option value="">' . __('Odaberi...') . '</option>';
    $to_current = date('Y') - 5;
    $to_past = $to_current;
    $to_next = $to_current + 20;

    for ($i = $to_past; $i <= $to_next; $i++) {
        if ($current == $i) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }


        $year .= '<option value="' . $i . '" ' . $sel . '>' . $i . '</option>';

    }

    return $year;

}

function _optionHiddenTab($tab_name, $hidden)
{

    if ($hidden)
        $checked = 'checked';
    else
        $checked = '';

    $option = '<div class="row">
              <div class="col-sm-5">
                <label>' . $tab_name . '</label>
              </div>
              <div class="col-sm-7">
             <input type="checkbox" name="tab_' . $tab_name . '" id="tab_' . $tab_name . '" class="form-control" style = "width: 20px;margin-top: -10px;" ' . $checked . ' value="1"/><br/>
              </div>
            </div>';

    return $option;
}

function _optionNameTab($tab_name, $name)
{


    $option = '<div class="row">
              <div class="col-sm-5">
                <label>' . $tab_name . '</label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="name_' . $tab_name . '" id="name_' . $tab_name . '" class="form-control" value="' . $name . '"/><br/>
              </div>
            </div>';

    return $option;
}

function _optionRoleTab($tab_name, $role)
{


    $option = '<div class="row">
              <div class="col-sm-5">
                <label>' . $tab_name . '</label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="role_' . $tab_name . '" id="role_' . $tab_name . '" class="form-control" value="' . $role . '"/><br/>
              </div>
            </div>';

    return $option;
}


function _optionMonth($current)
{

    global $months;
    $month = '<option value="">' . __('Odaberi...') . '</option>';

    foreach ($months as $key => $value) {
        if ($current == $key) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $month .= '<option value="' . $key . '" ' . $sel . '>' . $value . '</option>';
    }
    return $month;

}

function _optionDay($year, $month, $current)
{

    $date = $year . '-' . $month . '-01';
    $end = $year . '-' . $month . '-' . date('t', strtotime($date));
    $day = '<option value="">' . __('Odaberi...') . '</option>';

    while (strtotime($date) <= strtotime($end)) {
        $day_num = date('d', strtotime($date));
        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        if ($current == $day_num) {
            $sel = 'selected="selected"';
        } else {
            $sel = '';
        }
        $day .= '<option value="' . $day_num . '" ' . $sel . '>' . $day_num . '</option>';
    }

    return $day;

}

function _nameMonth($month)
{

    global $months;

    return $months[$month];

}


function _statsDays3($year, $month, $user)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year . "' AND month_id='" . $month . "' AND user_id='" . $user . "'
    AND weekday<>'6' AND weekday<>'7'");
    if ($get->rowCount() < 0) {

        foreach ($get as $day) {
            if ($day['status'] == '43' or $day['status'] == '44' or $day['status'] == '45' or $day['status'] == '61' or $day['status'] == '62' or $day['status'] == '65'
                or $day['status'] == '68' or $day['status'] == '69') {
                $arr [_nameHRstatus('67')][] = $day['review_status'];
            } else {
                $arr [_nameHRstatus($day['status'])][] = $day['review_status'];
            }

        }

        $items .= '<div class="row" >';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="col-md-2 kvote"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span> &nbsp; ';
            $items .= '<span class="badge badge-pill badge-success">' . $count1 . '</span>  &nbsp; ';
            $items .= '<span class="badge badge-pill badge-danger">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';
        }
        $items .= '</div>';

        return $items;

    }

}

function _statsDaysCorrections3($year, $month, $user)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year . "' AND month_id='" . $month . "' AND user_id='" . $user . "'
    AND weekday<>'6' AND weekday<>'7'");
    if ($get->rowCount() < 0) {

        foreach ($get as $day) {
            if ($day['status'] == '43' or $day['status'] == '44' or $day['status'] == '45' or $day['status'] == '61' or $day['status'] == '62' or $day['status'] == '65'
                or $day['status'] == '68' or $day['status'] == '69') {
                $arr [_nameHRstatus('67')][] = $day['review_status'];
            } else {
                $arr [_nameHRstatus($day['corr_status'])][] = $day['review_status'];
            }

        }
        $counter = 0;
        $items .= '<div class="row" style="margin-left:-56px;">';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="kvote col-md-2"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span> &nbsp; ';
            $items .= '<span class="badge badge-pill badge-success">' . $count1 . '</span>  &nbsp; ';
            $items .= '<span class="badge badge-pill badge-danger">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';

            $counter++;

            if ($counter == 5) {
                $items .= '
              </div>
              <div class="row" style="margin-left:-56px;">
        ';
                $counter = 0;
            }
        }
        $items .= '</div>';

        return $items;

    }

}

function _statsDays($year, $month, $user)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year . "' AND month_id='" . $month . "' AND user_id='" . $user . "'
    AND weekday<>'6' AND weekday<>'7'");
    if ($get->rowCount() < 0) {

        foreach ($get as $day) {

            $arr [_nameHRstatus($day['status'])][] = $day['review_status'];

        }

        $items .= '<div class="row" >';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="col-md-2 kvote"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span>  ';
            $items .= '<span  class="badge badge-pill badge-danger">' . $count1 . '</span>   ';
            $items .= '<span  class="badge badge-pill badge-success">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';
        }
        $items .= '</div>';

        return $items;

    }

}

function _statsDaysCorrections($year, $month, $user)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE year_id='" . $year . "' AND month_id='" . $month . "' AND user_id='" . $user . "'
    AND weekday<>'6' AND weekday<>'7'");
    if ($get->rowCount() < 0) {

        foreach ($get as $day) {

            $arr [_nameHRstatus($day['corr_status'])][] = $day['review_status'];

        }

        $items .= '<div class="row">';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="col-md-2 kvote"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span>  ';
            $items .= '<span  class="badge badge-pill badge-danger">' . $count1 . '</span>   ';
            $items .= '<span  class="badge badge-pill badge-success">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';
        }
        $items .= '</div>';

        return $items;

    }

}

function _statsDaysFree($year, $month_from, $month_to, $day_from, $day_to)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);


    if ($get->rowCount() < 0) {

        foreach ($get as $day) {

            $arr [_nameHRstatus($day['status'])][] = $day['review_status'];

        }

        $items .= '<div class="row">';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="col-md-2 kvote"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span>  ';
            $items .= '<span  class="badge badge-pill badge-danger">' . $count1 . '</span>   ';
            $items .= '<span  class="badge badge-pill badge-success">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';
        }
        $items .= '</div>';

        return $items;

    }

}

function _statsDaysFreeCorrections($year, $month_from, $month_to, $day_from, $day_to)
{

    global $db, $_conf, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);


    if ($get->rowCount() < 0) {

        foreach ($get as $day) {

            $arr [_nameHRstatus($day['corr_status'])][] = $day['review_status'];

        }

        $items .= '<div class="row">';
        foreach ($arr as $key => $value) {
            $count0 = count(array_keys($value, 0));
            $count1 = count(array_keys($value, 1));
            $count2 = count(array_keys($value, 2));
            $items .= '<div class="col-md-2 kvote"><div class="num">';
            $items .= '<span class="badge badge-pill badge-secondary">' . $count0 . '</span>  ';
            $items .= '<span  class="badge badge-pill badge-danger">' . $count1 . '</span>   ';
            $items .= '<span  class="badge badge-pill badge-success">' . $count2 . '</span>';
            $items .= '</div><small>' . $key . '</small></div>';
        }
        $items .= '</div>';

        return $items;

    }

}

function formatDateNicely($str)
{
    return str_replace("/", ".", $str);
}

function _statsDaysFreeReifOtkazani($year, $month_from, $month_to, $day_from, $day_to, $admin_filter)
{
    global $db, $_user, $portal_hourlyrate_day, $portal_canceledreq, $portal_users;


    $where_month = "";
    $a_count = 0;
    if ($admin_filter == "nadredjeni") {
        $a_filter = "INNER JOIN  " . $portal_users . "  s1 ON  " . $portal_canceledreq . " .user_id = s1.user_id and s1.parent = '$_user[employee_no]'";
        $a_count = 1;
    } elseif ($admin_filter == "admin") {
        $a_filter = "INNER JOIN  " . $portal_users . "  s1 ON  " . $portal_canceledreq . " .user_id = s1.user_id WHERE 1=1";
        $a_count = 1;
    } else {
        $a_filter = "INNER JOIN  " . $portal_users . "  s1 ON  " . $portal_canceledreq . " .user_id = s1.user_id WHERE  " . $portal_canceledreq . " .user_id = '$_user[user_id]'";
    }

    if (isset($month_from) and isset($month_to) and isset($day_from) and isset($month_to)) {

        $where_month = " and month_from >= $month_from and month_to <= $month_to ";

    } else {
        $where_month = "";
    }

    $get_rows_query = $db->query("SELECT  " . $portal_canceledreq . " .*, s1.fname, s1.lname FROM  " . $portal_canceledreq . "  $a_filter $where_month");
    $fetch_rows = $get_rows_query->fetchAll();


    $items = "";
    $items .= '<table class="alt col-sm-12">';
    $items .= '<tr>';
    if ($a_count == 1) {
        $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Ime radnika</th>';
    }
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 200px;" >Datum od</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 200px;" >Datum do</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Otkazani izostanak</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Komentar</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 100px;" >Br. Dana</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 100px;" >Odobreno</th>';
    $items .= '</tr>';
    $items .= '<tbody>';
    global $portal_hourlyrate_year;

    foreach ($fetch_rows as $k => $v) {

        $get_year_rows = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE user_id = '$v[user_id]' and id = '$v[year_id_from]'");
        $fetch_rows_year = $get_year_rows->fetchAll();

        $datefrom = date_create($v['day_from'] . "-" . $v['month_from'] . "-" . $fetch_rows_year[0]['year']);
        $dateto = date_create($v['day_to'] . "-" . $v['month_to'] . "-" . $fetch_rows_year[0]['year']);
        $diff = date_diff($datefrom, $dateto);
        $finaldays = $diff->format("%a");
        $finaldays += 1;

        $items .= "<tr>";
        if ($a_count == 1) {
            $items .= '<td >' . $v['fname'] . ' ' . $v['lname'] . '</th>';
        }
        $items .= "<td>" . $v['day_from'] . "." . $v['month_from'] . "." . $fetch_rows_year[0]['year'] . "</td>";
        $items .= "<td>" . $v['day_to'] . "." . $v['month_to'] . "." . $fetch_rows_year[0]['year'] . "</td>";
        $items .= "<td>" . _nameHRstatus($v['vrsta_odsustva']) . "</td>";
        $items .= "<td>$v[comment_reviewer]</td>";
        $items .= "<td>" . $finaldays . "</td>";
        $items .= "<td>DA</td>";
        $items .= "</tr>";
    }

    $items .= "</tbody>";
    $items .= "</table>";

    echo $items;
}

function getNotificationsNovaOdsustvaRadnik($type = '', $filter_odobreno = 'false', $filter_odobreno_cancel = 'false')
{
    global $_user, $db, $portal_hourlyrate_year, $portal_pagination;

    $godina = date("Y");
    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
    $year = $get_year->fetch();
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, 12, $godina);

    $datumOD = date('d/m/Y', strtotime("01 January " . $godina));
    $datumDO = date('d/m/Y', strtotime("31 December " . $godina));
    $offset = 0;

    $get_limit = $db->query("SELECT * FROM  " . $portal_pagination . "  WHERE Page = 'odsustva_radnici'");
    $get_limit1 = $get_limit->fetch();
    $limit = $get_limit1['Limit'];

    if ($type == ''):


        return _statsDaysFreeReif($year['id'], 1, 12, 1, $number_of_days, $filter_odobreno, $filter_odobreno_cancel, 1);

    elseif ($type == 'corrections'):

        return _statsDaysFreeReifCorrections($year['id'], 1, 12, 1, $number_of_days, $filter_odobreno, $filter_odobreno_cancel, 1);

    endif;
}

function _statsDaysFreeReif($year, $month_from, $month_to, $day_from, $day_to, $filter_odobreno, $filter_odobreno_cancel, $counter = 0)
{
    global $db, $_conf, $portal_hourlyrate_year, $portal_hourlyrate_day;

    $count_num = 0;

    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
    $year_real = $get_year->fetch();

    $status_query = "((status<>5 and status<>83) or (status_pre is not null)) ";

    if ($filter_odobreno == 'true') {
        $odobreno_query = " and (review_status = 1)";
    } elseif ($filter_odobreno == 'false') {
        $odobreno_query = " and status != '5' and (review_status = 0)";
    } elseif ($filter_odobreno == 'rejected') {
        $status_query = "(status=5 or status = 83)";
        $odobreno_query = " and ((status_rejected is not null and review_status = 0 and change_req<>2) or (status=83 and status_rejected is not null))";
    } else
        $odobreno_query = "";

    if ($filter_odobreno_cancel == 'true') {
        $odobreno_cancel_query = " and (change_req = 2)";
        $status_query = "((status=5) or (status=83))";
    } elseif ($filter_odobreno_cancel == 'false')
        $odobreno_cancel_query = " and (change_req = '0')";
    else
        $odobreno_cancel_query = "";

    $items = '';

    $items .= '<table class="alt col-sm-12 sortable">';
    $items .= '<tr>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Datum od</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Datum do</th>';
    if (!($filter_odobreno == 'rejected'))
        $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Vrsta izostanka</th>';
    else
        $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Odbijeni izostanak</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Komentar</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Komentar podnosioca zahtjeva</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 100px;" >Br. Dana</th>';
    $items .= '</tr>';
    $arr = array();


    if ($filter_odobreno == "rejected" or $filter_odobreno_cancel == 'true') {
        $statusnotequal = "1=1 ";
    } else {
        $statusnotequal = "(status!=5)";
    }

    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
  $statusnotequal and 
   " .
        $status_query . " and 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ) " . $odobreno_query . $odobreno_cancel_query . " 
   and year_id=" . $year . " order by id asc");


    $fet = $get->fetchAll();


    //if($get->rowCount()<0){

    if (count($fet) > 0) {

        $index = 0;
        foreach ($fet as $key => $day) {

            if ($key == 0) {

                $day_id = $day['id'] - 1;
                $status = $day['status'];
                $status_rejected = $day['status_rejected'];

                $description = $day['Description'];

                if ($filter_odobreno == 'rejected' or $filter_odobreno_cancel == 'true') {
                    $var_check = $status_rejected;
                } else {
                    $var_check = $status;
                }

                $arr [_nameHRstatus($var_check) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $pocetni_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
            }

            $status_curr_rejected = $day['status_rejected'];
            $status_curr = $day['status'];

            if ($filter_odobreno == 'rejected' or $filter_odobreno_cancel == 'true') {
                $var_check_curr = $status_curr_rejected;
                $var_check = $status_rejected;
            } else {
                $var_check_curr = $status_curr;
                $var_check = $status;
            }

            if (($var_check_curr == $var_check) and ($day['id'] == ($day_id + 1))) {


                if (($day['weekday'] != 6 and $day['weekday'] != 7) or $day['KindofDay'] == 'BHOLIDAY') {
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_rejected'] = _nameHRstatus($status_curr_rejected);
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_rejected_id'] = $status_curr_rejected;
                }
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_y'] = $day['status'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['hour_pre'] = $day['hour_pre'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($var_check_curr, array(5, 85, 86, 87, 88, 89, 90, 43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 105, 107, 108)))
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index][] = $day['review_status'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));


                /** AR-292 start part 1 **/
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                /** AR-292 end **/


            } else {
                $index = $index + 1;
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or $day['KindofDay'] == 'BHOLIDAY') {
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_rejected'] = _nameHRstatus($status_curr_rejected);
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_rejected_id'] = $status_curr_rejected;
                }
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['status_y'] = $day['status'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['hour_pre'] = $day['hour_pre'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($var_check_curr, array(5, 85, 86, 87, 88, 89, 90, 43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 105, 107, 108)))
                    $arr [_nameHRstatus($var_check_curr) . '-' . $index][] = $day['review_status'];

                /** AR-292 start part 2 **/
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($var_check_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                /** AR-292 end **/

            }

            $day_id = $day['id'];
            $status = $day['status'];
            $status_rejected = $day['status_rejected'];


            $description = $day['Description'];

            $krajnji_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

        }

        $items .= '<div class="row" style="margin-top:50px;">';


        foreach ($arr as $key => $value) {

            $count_num++;


            $count0 = count(array_keys($value, '0'));
            $count1 = count(array_keys($value, '1'));
            $count2 = count(array_keys($value, '2'));
            if (!isset($value['status_rejected'])) {
                $value['status_rejected'] = '';
            }

            if (!($filter_odobreno == 'rejected')) {
                $pieces = explode("-", $key);

                if (isset($pieces)) {
                    $naziv_odsustva = $pieces[0];
                }
            } else
                $naziv_odsustva = $value['status_rejected'];

            $newpieces = explode("-", $key);

            /** AR-292 start part 3 */
            if ($value['employee_no'] == '1')
                $count1 = $count1 - 1;
            if ($value['hour_pre'] != null)
                $count1 = $count1 - 1;
            if ($value['danOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['danDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['ima_dokument'] == '1')
                $count1 = $count1 - 1;
            if ($value['komentar_radnika'] == '1')
                $count1 = $count1 - 1;
            $br_dana = $count0 + $count1;
            /** AR-292 **/

            if (($filter_odobreno == 'rejected')) {
                $value['status_y'] = $status_curr_rejected;
            }
            if ($filter_odobreno_cancel == 'true') {
                $value['status_y'] = $value['status_rejected_id'];
            }


            if (in_array($value['status_y'], array(43, 44, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 107, 108, 73, 81, 74, 75, 76, 77, 78))) {

                $date11 = strtotime($value['datumOD']);
                $date22 = strtotime($value['datumDO']);
                $diff = $date22 - $date11;
                $br_dana = floor($diff / (60 * 60 * 24)) + 1;


            } else {
                $br_dana = getWorkingDays($value['datumOD'], $value['datumDO'], array());
            }


            if (strpos($key, 'Redovni rad-') !== false) {
                if ($value['hour_pre'] > 0):
                    $naziv_odsustva = "Prekovremeni rad";
                else:
                    $naziv_odsustva = "Redovan rad";
                endif;
            }
            if ($value['status_y'] == "81"):
                $naziv_odsustva = _nameHRstatus($value['status_y']);
            endif;


            if (empty($naziv_odsustva)) {
                $naziv_odsustva = $newpieces[0];
            }

            // AR-292 fix
            $get_working_days = getWorkingDays($value['datumOD'], $value['datumDO'], array());

            $items .= '<tr>' . '<td>' . $value['datumOD'] . '</td>' . '<td>' . $value['datumDO'] . '</td>' . '<td>' . $naziv_odsustva . '</td>' . '<td>' . $value['komentar'] . '</td>' . '<td>' . $value['komentar_radnika'] . '</td>' . '<td>' . $br_dana . '</td></tr>';
        }
        $items .= '</table>';


    }

    if ($counter == 0):
        return $items;
    else:
        return $count_num;
    endif;
}

function _statsDaysFreeReifCorrections($year, $month_from, $month_to, $day_from, $day_to, $filter_odobreno, $filter_odobreno_cancel, $counter = 0)
{

    global $db, $_conf, $portal_hourlyrate_year, $portal_hourlyrate_day;

    $count_num = 0;

    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
    $year_real = $get_year->fetch();

    $status_query = "(corr_status<>5 and corr_status<>83 or (status_pre is not null))";

    if ($filter_odobreno == 'true') {
        $odobreno_query = " and corr_review_status = 1";
    } elseif ($filter_odobreno == 'false') {
        $odobreno_query = " and corr_status != '5' and corr_review_status = 0 ";
    } elseif ($filter_odobreno == 'rejected') {
        $status_query = "(corr_status=5)";
        $odobreno_query = " and status_rejected is not null and corr_review_status = 0";
    } else
        $odobreno_query = "";

    if ($filter_odobreno_cancel == 'true') {
        $odobreno_cancel_query = " and change_req = 2";
        $status_query = "corr_status=5";
    } elseif ($filter_odobreno_cancel == 'false')
        $odobreno_cancel_query = " and change_req = '0'";
    else
        $odobreno_cancel_query = "";

    $items = '';

    $items .= '<table class="alt col-sm-12">';
    $items .= '<tr>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Datum od</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Datum do</th>';
    if (!($filter_odobreno == 'rejected'))
        $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Vrsta izostanka</th>';
    else
        $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Odbijeni izostanak</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Komentar</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 300px;" >Komentar podnosioca zahtjevagit</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;width: 100px;" >Br. Dana</th>';
    $items .= '</tr>';
    $arr = array();


    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (" .

        $status_query . " and corr_status != status and 
   (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   )" . $odobreno_query . $odobreno_cancel_query . ")
   and year_id=" . $year . " order by id asc");


    if ($get->rowCount() < 0) {

        $index = 0;
        foreach ($get as $key => $day) {
            if ($key == 0) {
                $day_id = $day['id'] - 1;
                $status = $day['corr_status'];
                $status_rejected = $day['status_rejected'];

                $description = $day['Description'];
                $arr [_nameHRstatus($status) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $pocetni_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
            }

            $status_curr_rejected = $day['status_rejected'];
            $status_curr = $day['corr_status'];

            if (($status_curr == $status) and ($status_curr_rejected == $status_rejected) and ($day['id'] == ($day_id + 1))) {
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or $day['KindofDay'] == 'BHOLIDAY') {
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['status_rejected'] = _nameHRstatus($status_curr_rejected);
                }
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['status_y'] = $day['corr_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['hour_pre'] = $day['hour_pre'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($status_curr, array(5, 85, 86, 87, 88, 89, 90, 43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['corr_review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

                /** AR-292 start part 1 **/
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                /** AR-292 end **/


            } else {
                $index = $index + 1;
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or $day['KindofDay'] == 'BHOLIDAY') {
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['status_rejected'] = _nameHRstatus($status_curr_rejected);
                }
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['status_y'] = $day['corr_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['hour_pre'] = $day['hour_pre'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($status_curr, array(5, 85, 86, 87, 88, 89, 90, 43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 107, 108)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['corr_review_status'];

                /** AR-292 start part 2 **/
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                /** AR-292 end **/

            }

            $day_id = $day['id'];
            $status = $day['corr_status'];
            $status_rejected = $day['status_rejected'];

            $description = $day['Description'];

            $krajnji_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

        }

        $items .= '<div class="row" style="margin-top:50px;">';
        foreach ($arr as $key => $value) {
            $count_num++;
            $count0 = count(array_keys($value, '0'));
            $count1 = count(array_keys($value, '1'));
            $count2 = count(array_keys($value, '2'));
            if (!isset($value['status_rejected'])) {
                $value['status_rejected'] = '';
            }

            if (!($filter_odobreno == 'rejected')) {
                $pieces = explode("-", $key);
                if (isset($pieces)) {
                    $naziv_odsustva = $pieces[0];
                }
            } else
                $naziv_odsustva = $value['status_rejected'];


            /** AR-292 start part 3 */
            if ($value['employee_no'] == '1')
                $count1 = $count1 - 1;
            if ($value['hour_pre'] != null)
                $count1 = $count1 - 1;
            if ($value['danOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['danDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['ima_dokument'] == '1')
                $count1 = $count1 - 1;
            if ($value['komentar_radnika'] == '1')
                $count1 = $count1 - 1;
            $br_dana = $count0 + $count1;
            /** AR-292 **/

            // var_dump($value['status_y']);
            if (in_array($value['status_y'], array(43, 44, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 107, 108, 73, 81, 74, 75, 76, 77, 78))) {


                $date11 = strtotime($value['datumOD']);
                $date22 = strtotime($value['datumDO']);
                $diff = $date22 - $date11;
                $br_dana = floor($diff / (60 * 60 * 24)) + 1;


            } else {
                $br_dana = getWorkingDays($value['datumOD'], $value['datumDO'], array());
            }

            if (strpos($key, 'Redovni rad-') !== false) {
                if ($value['hour_pre'] > 0):
                    $naziv_odsustva = "Prekovremeni rad";
                else:
                    $naziv_odsustva = "Redovan rad";
                endif;
            }
            if ($value['status_y'] == "81"):
                $naziv_odsustva = _nameHRstatus($value['status_y']);
            endif;


            // AR-292 fix
            $get_working_days = getWorkingDays($value['datumOD'], $value['datumDO'], array());

            $items .= '<tr>' . '<td>' . $value['datumOD'] . '</td>' . '<td>' . $value['datumDO'] . '</td>' . '<td>' . $naziv_odsustva . '</td>' . '<td>' . $value['komentar'] . '</td>' . '<td>' . $value['komentar_radnika'] . '</td>' . '<td>' . $br_dana . '</td></tr>';
        }
        $items .= '</table>';


    }

    if ($counter == 0):
        return $items;
    else:
        return $count_num;
    endif;

}

function checkifAdmin()
{
    global $_user;

    $admin_array = array($_user['admin1'], $_user['admin2'], $_user['admin3'], $_user['admin4'], $_user['admin5'], $_user['admin6'], $_user['admin7'], $_user['admin8']);

    if (in_array($_user['employee_no'], $admin_array)) {
        return true;
    } else {
        return false;
    }
}

function getNotificationsNovaOdsustva($type = '', $zahtjevi = false)
{
    global $_user, $db, $portal_hourlyrate_year, $portal_pagination;

    $godina = date("Y");
    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE year='" . $godina . "' AND user_id = " . $_user['user_id']);
    $year = $get_year->fetch();
    $number_of_days = cal_days_in_month(CAL_GREGORIAN, 12, $godina);

    $datumOD = date('d/m/Y', strtotime("01 January " . $godina));
    $datumDO = date('d/m/Y', strtotime("31 December " . $godina));
    $offset = 0;

    $get_limit = $db->query("SELECT * FROM  " . $portal_pagination . "  WHERE Page = 'odsustva_radnici'");
    $get_limit1 = $get_limit->fetch();
    $limit = $get_limit1['Limit'];

    if ($type == ''):
        if ($zahtjevi == true):
            $neodobreno = false;
        else:
            $neodobreno = true;
        endif;

        return _statsDaysFreeReifUsers4($year['id'], $datumOD, $datumDO, $offset, $limit, '', '', '', '', $neodobreno, false, $zahtjevi, '', false, 1);

    elseif ($type == 'corrections'):

        if ($zahtjevi == true):
            $neodobreno = false;
        else:
            $neodobreno = true;
        endif;

        return _statsDaysFreeReifUsers4Corrections($year['id'], $datumOD, $datumDO, $offset, $limit, '', '', '', '', $neodobreno, false, $zahtjevi, '', false, 1);

    endif;
}


function _statsDaysFreeReifUsers4($year, $datumOD, $datumDO, $offset, $limit, $employee_no, $ime_prezime, $vrsta, $grupa, $filter_neodobreno, $filter_praznici, $filter_zahtjevi, $per_broj, $filter_dokument, $counter = 0)
{

    global $db, $_conf;
    global $_user;
    global $portal_hourlyrate_year;
    global $portal_users, $portal_hourlyrate_day, $portal_calendar, $portal_hourlyrate_status;

    $count_num = 0;
    if ($employee_no == "")
        $employee_query = "";
    else
        $employee_query = " and employee_no= '" . $employee_no . "'";

    if ($ime_prezime == "")
        $ime_prezime_query = "";
    else
        $ime_prezime_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE fname + ' ' + lname = N'" . $ime_prezime . "')";

    if ($vrsta == "")
        $vrsta_query = "";
    else {
        if ($vrsta == "106")
            $vrsta_query = " and status in (18,19)";
        elseif ($vrsta == "84")
            $vrsta_query = " and status in (21,22)";
        else
            $vrsta_query = " and status= " . $vrsta . "";
    }

    if ($grupa == "")
        $grupa_query = "";
    else
        $grupa_query = " and status in (select distinct id from  " . $portal_hourlyrate_status . "  where status_group='" . $grupa . "')";

    if ($filter_neodobreno == true)
        $neodobreno_query = " and review_status = 0";
    else
        $neodobreno_query = "";

    $praznici_query = "";

    if ($filter_zahtjevi == true)
        $zahtjevi_query = " and change_req = 1";
    else
        $zahtjevi_query = "";

    if ($filter_dokument != "") {
        if ($filter_dokument == 0) {

            $filter_dokument = "IS NULL or dokument = 0)";
        } else {
            $filter_dokument = " = '" . $filter_dokument . "')";
        }
        $dokument_query = " and (dokument " . $filter_dokument;
    } else
        $dokument_query = "";

    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
    $year_real = $get_year->fetch();

    if ($_user['role'] == 4) {
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
    } elseif ($_user['role'] == 2) {
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent=" . $_user['employee_no'] . "))";
    }

    if ($per_broj == "")
        $per_query = "";
    else {
        $per_query = " and employee_no= '" . $per_broj . "'";
        $role_query = "";
        //kontrola intervala
        $day_from = 1;
        $day_to = 31;
        $month_from = 1;
        $month_to = 12;
    }


    $items = '';
    $items .= '<table class="alt col-sm-12">';
    $items .= '<tr>';
    if ($filter_zahtjevi != true):
        $items .= '<th title="Označi sve zahtjeve" style="height:20px; background: #c7bebe;color: black;"><input type="checkbox"  id="select-all" /></th>';
    endif;
    $items .= '<th style="height:20px; background: #c7bebe;color: black;">Personalni broj</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width:20%;">Ime</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Datum od</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Datum do</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width: 20%;" >Vrsta izostanka</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Br. Dana</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Registrovano</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width:11%;" >Registrovao/la</th>';
    if ($filter_zahtjevi == true)
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Otkaži</th>';
    else
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Odobreno</th>';
    if ($_user['role'] == 4)
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Detalji</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Ažuriraj</th>';
    if ($_user['role'] == 4)
//        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Ima dokument</th>';
//    if (checkifAdmin() == true) {
//        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Šifra bolesti</th>';
//    }
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Komentar</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Komentar podnosioca zahtjeva</th>';

    $items .= '</tr>';

    $arr = array();


    $where_period = " and c.Date between CONVERT(datetime,'" . $datumOD . "',103) and CONVERT(datetime,'" . $datumDO . "',103)";

    $get = $db->query("SELECT h.id, h.day, h.month_id, h.status, h.year_id, h.Date, h.timest_edit, h.employee_timest_edit, h.dokument, h.review_comment, h.disease_code, h.request_id, h.review_status, h.employee_no, h.weekday, h.employee_comment, h.Description FROM  " . $portal_hourlyrate_day . "  h with(nolock)
  join  " . $portal_hourlyrate_year . "  y with(nolock)
  on h.year_id = y.id
  join  " . $portal_calendar . "  c with(nolock)
  on (c.Year = y.year and c.Day = h.day and c.Month=h.month_id)
  WHERE 
   (
   status not in (5,85,86,87,88,89,90)" .
        $where_period . $vrsta_query . $grupa_query . $role_query . $employee_query . $ime_prezime_query . $neodobreno_query . $praznici_query . $zahtjevi_query . $per_query . $dokument_query . ")
   order by h.id,employee_no ");

    if ($get->rowCount() < 0 or 1 == 1) {

        $index = 0;
        foreach ($get as $key => $day) {

            if ($key == 0) {
                $arr [_nameHRstatus($day['status']) . '-' . $index]['year_id'] = $day['year_id'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['godina'] = date('Y', strtotime($day['Date']));
                $day_id = $day[0] - 1;
                $status = $day['status'];

                $employee_no = $day['employee_no'];
                $description = $day['Description'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['start_id'] = $day['id'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['danOD'] = (int)date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['employee_no'] = $employee_no;
                $arr [_nameHRstatus($day['status']) . '-' . $index]['ime_prezime'] = _employee($employee_no)['fname'] . ' ' . _employee($employee_no)['lname'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['registrovano'] = date("d.m.Y", strtotime($day['timest_edit']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['registrovano_year'] = date("Y", strtotime($day['timest_edit']));
                if ($day['employee_timest_edit'] != '')
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                else
                    $arr [_nameHRstatus($day['status']) . '-' . $index]['reg_korisnik'] = '';
                $pocetni_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['ima_dokument'] = $day['dokument'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['komentar'] = $day['review_comment'];

                $arr [_nameHRstatus($day['status']) . '-' . $index]['disease_code'] = $day['disease_code'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['status'] = $day['status'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['request_id'] = $day['request_id'];
            }

            $status_curr = $day['status'];


            if (($status_curr == $status) and ($day['employee_no'] == $employee_no) and ($day[0] == ($day_id + 1))) {
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($status_curr, array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 105, 107, 108)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['rev_status'] = $day['review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['end_id'] = $day['id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($day['status']) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
            } else {
                $index = $index + 1;
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($status_curr, array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81, 105, 107, 108)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['rev_status'] = $day['review_status'];
                $arr [_nameHRstatus($day['status']) . '-' . $index]['godina'] = date('Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['year_id'] = $day['year_id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['start_id'] = $day['id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['ime_prezime'] = _employee($day['employee_no'])['fname'] . ' ' . _employee($day['employee_no'])['lname'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['registrovano'] = date("d.m.Y", strtotime($day['timest_edit']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['registrovano_year'] = date("Y", strtotime($day['timest_edit']));
                if ($day['employee_timest_edit'] != '')
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                else
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['reg_korisnik'] = '';
                $arr [_nameHRstatus($status_curr) . '-' . $index]['end_id'] = $day['id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));

                $arr [_nameHRstatus($status_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['disease_code'] = $day['disease_code'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['request_id'] = $day['request_id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['status'] = $status_curr;

            }

            $day_id = $day[0];
            $status = $day['status'];

            $employee_no = $day['employee_no'];
            $description = $day['Description'];

            $krajnji_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

        }

        foreach ($arr as $key => $value) {

            if ($value['registrovano_year'] < "1990"):
                continue;
            endif;
            $count_num++;
            $count0 = count(array_keys($value, '0'));
            $count1 = count(array_keys($value, '1'));
            $count2 = count(array_keys($value, '2'));
            $pieces = explode("-", $key);
            $naziv_odsustva = $pieces[0];

            $status1 = $value['status'];
            if ($status1 == 18 or $status1 == 19)
                $status1 = 106;
            if ($status1 == 21 or $status1 == 22)
                $status1 = 84;


            if ($value['employee_no'] == '1')
                $count1 = $count1 - 1;
            if ($value['danOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['danDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['ima_dokument'] == '1')
                $count1 = $count1 - 1;
            if ($value['komentar_radnika'] == '1')
                $count1 = $count1 - 1;
            if ($value['komentar'] == '1')
                $count1 = $count1 - 1;
            $br_dana = $count0 + $count1;

            if (in_array($status1, array(43, 44, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 107, 108, 73, 81, 74, 75, 76, 77, 78))) {

                $date11 = strtotime($value['datumOD']);
                $date22 = strtotime($value['datumDO']);
                $diff = $date22 - $date11;
                $br_dana = floor($diff / (60 * 60 * 24)) + 1;


            } else {
                $br_dana = getWorkingDays($value['datumOD'], $value['datumDO'], array());
            }


            if ($value['rev_status'] == '1')
                $odobreno = '1';
            else
                $odobreno = '';

            $dis_nad = '';


            if ($_user['role'] == 4) {
                $disejbld = '';
                if ($odobreno == '1') {
                    $disejbld = "disabled='disabled'";
                }
                $naziv_odsustvaOpcija = '<select ' . $disejbld . ' id="status-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" title="' . _nameHRstatus($status1) . '" name="vrsta1" class="rcorners1 tootip"  class="form-control" style="width:100%;outline:none;">' . _optionHRstatus($status1) .
                    '</select>';
            } else if ($_user['role'] == 2) {
                $naziv_odsustvaOpcija = '<select disabled id="status-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="vrsta1" class="rcorners1" class="form-control" style="width:100%;outline:none;">' . _optionHRstatus($status1) .
                    '</select>';
            }


            if ($filter_zahtjevi == true)
                $optionOdobriOtkazi = ' <td> <select data-otkazivanje="1" data-user-id="' . $value['employee_no'] . '" id="odobreno-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="odobreno" class="rcorners1" class="form-control" style="width:120px;outline:none;">' . _optionOtkazano($odobreno);
            else {

                if ($odobreno == '1' and $_user['role'] == 2)
                    $dis_nad = 'disabled';
                $optionOdobriOtkazi = ' <td> <select ' . $dis_nad . ' data-user-id="' . $value['employee_no'] . '" id="odobreno-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="odobreno" class="rcorners1" class="form-control" style="width:100px;outline:none;">' . _optionOdobreno($odobreno);
            }

            if ($_user['role'] == 4)
                $detalji = ' <td> <button type="button" id="detalji-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['godina'] . '-' . $value['ime_prezime'] . '" name="detalji" class="rcorners1" class="form-control" style="width:64px;outline:none;">Detalji</button></td>';
            else
                $detalji = '';

            $satnice = '<td><a style="width: 141px;" href="' . $_conf['app_location'] . '' . $_conf['app_location_module'] . '/modules/admin_manager_hourly_rate/pages/popup_day_add_apsolute.php?year=' . $value['year_id'] . '&month=' . $value['mjesecOD'] . '" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-md">Ažuriraj satnice<i class="ion-ios-plus-empty"></i></a></td>';

            $pieces = explode(" ", $value['registrovano']);
            $registrovano = $pieces[0];

            if ($value['ima_dokument'] == '1')
                $dokument_checked = 'checked="checked"';
            else
                $dokument_checked = '';
            $items .= '<tr data-row-id="datarow-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '">';
            if ($filter_zahtjevi != true):
                $items .= '
      <td>
        <input comment-id="' . $value['start_id'] . '$#' . $value['end_id'] . '" class="odobri-ids" type="checkbox" name="odobri[]" value="' . $value['start_id'] . '$#' . $value['end_id'] . '" />
      </td>
      ';
            endif;
            $items .= '<td>' . $value['employee_no'] . '</td>' . '<td>' . $value['ime_prezime'] . '</td>' . '<td>' . $value['datumOD'] . '</td>' . '<td>' . $value['datumDO'] . '</td>' . '<td>' . $naziv_odsustvaOpcija . '</td>' . '<td>' . $br_dana . '</td>' . '<td>' . $registrovano . '</td>' . '<td>' . $value['reg_korisnik'] . '</td>' .
                $optionOdobriOtkazi .
                '</select></td>' . $detalji . $satnice;

//            $dokument_input =
//                '<td><input id="dokument-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" type="checkbox" ' . $dokument_checked . ' value="1" name="dokument-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" style=""></td>';
//
//            if ($_user['role'] == 4)
//                $items .= $dokument_input;

//            if (checkifAdmin() == true) {
//                $items .= '<td><input type="text" maxlength="250" style="max-width: 50px;height: 46px; border:solid 1px grey;" id="disease_code-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" value="' . $value['disease_code'] . '" name="disease_code-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '"></td>';
//            }

            $items .= '<td><textarea data-comment-id="' . $value['start_id'] . '$#' . $value['end_id'] . '" style="max-width: 110px; border:solid 1px grey; height: 46px;" maxlength="250" id="komentar-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name=id="komentar-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" spellcheck="false">' . $value['komentar'] . '</textarea></td>' .
                '<td><textarea maxlength="250" style="max-width: 110px; border:solid 1px grey; height:46px;" id="comment_employee" name="comment_employee" readonly>' . $value['komentar_radnika'] . '</textarea></td>';


            $items .= '</tr>';

        }
    }

    $items .= '</table>';

    if ($counter == 0):
        return $items;
    else:
        return $count_num;
    endif;

}

function _statsDaysFreeReifUsers4Corrections($year, $datumOD, $datumDO, $offset, $limit, $employee_no, $ime_prezime, $vrsta, $grupa, $filter_neodobreno, $filter_praznici, $filter_zahtjevi, $per_broj, $filter_dokument, $counter = 0)
{

    global $db, $_conf, $portal_calendar, $nav_employee_absence, $nav_cause_of_absence, $_user, $portal_users, $portal_hourlyrate_status, $portal_hourlyrate_year, $portal_hourlyrate_day;

    $count_num = 0;
    if ($employee_no == "")
        $employee_query = "";
    else
        $employee_query = " and employee_no= '" . $employee_no . "'";

    if ($ime_prezime == "")
        $ime_prezime_query = "";
    else
        $ime_prezime_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE fname + ' ' + lname = N'" . $ime_prezime . "')";

    if ($vrsta == "")
        $vrsta_query = "";
    else {
        if ($vrsta == "106")
            $vrsta_query = " and corr_status in (18,19)";
        elseif ($vrsta == "84")
            $vrsta_query = " and corr_status in (21,22)";
        else
            $vrsta_query = " and corr_status= " . $vrsta . "";
    }

    if ($grupa == "")
        $grupa_query = "";
    else
        $grupa_query = " and corr_status in (select distinct id from  " . $portal_hourlyrate_status . "  where status_group='" . $grupa . "')";

    if ($filter_neodobreno == true) {


        $neodobreno_query = " and corr_review_status = 0";

        if ($filter_zahtjevi == 'true') {
            $neodobreno_query = "and corr_review_status = 1";
        }
    } else {
        $neodobreno_query = "";
        if ($filter_zahtjevi == 'true') {
            $neodobreno_query = "and corr_review_status = 1";
        }
    }


    if ($filter_praznici == true)
        $praznici_query = "";
    else
        $praznici_query = " and h.KindOfDay<>'BHOLIDAY'";

    if ($filter_zahtjevi == true) {


        $zahtjevi_query = " and corr_change_req = 1";
    } else
        $zahtjevi_query = "";

    if ($filter_dokument != "") {
        if ($filter_dokument == 0) {

            $filter_dokument = "IS NULL )";
        } else {
            $filter_dokument = " = '" . $filter_dokument . "' )";
        }
        $dokument_query = " and (dokument " . $filter_dokument;
    } else
        $dokument_query = "";

    $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $year . "'");
    $year_real = $get_year->fetch();

    if ($_user['role'] == 4) {
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE " . $_user['employee_no'] . " in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8))";
    } elseif ($_user['role'] == 2) {
        $role_query = " and employee_no in (SELECT employee_no FROM  " . $portal_users . "  WHERE (parent=" . $_user['employee_no'] . "))";
    }

    if ($per_broj == "")
        $per_query = "";
    else {
        $per_query = " and employee_no= '" . $per_broj . "'";
        $role_query = "";
        //kontrola intervala
        $day_from = 1;
        $day_to = 31;
        $month_from = 1;
        $month_to = 12;
    }


    $items = '';
    $items .= '<table class="alt col-sm-12">';
    $items .= '<tr>';
    if ($filter_zahtjevi != true):
        $items .= '<th style="height:20px; background: #c7bebe;color: black;w"><input type="checkbox"  id="select-all" /></th>';
    endif;
    $items .= '<th style="height:20px; background: #c7bebe;color: black;w">Personalni broj</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width:20%;">Ime</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Datum od</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Datum do</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width:20%;" >Vrsta izostanka</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Br. Dana</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Registrovano</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black; width:11%;" >Registrovao/la</th>';
    if ($filter_zahtjevi == true)
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Otkaži</th>';
    else
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Odobreno</th>';
    if ($_user['role'] == 4)
        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Detalji</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Ažuriraj</th>';
    if ($_user['role'] == 4)
//        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Ima dokument</th>';
//    if (checkifAdmin() == true) {
//        $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Šifra bolesti</th>';
//    }
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Komentar</th>';
    $items .= '<th style="height:20px; background: #c7bebe;color: black;" >Komentar podnosioca zahtjeva</th>';
    $items .= '</tr>';

    $arr = array();


    $where_month1 = " and c.Date between CONVERT(datetime,'" . $datumOD . "',103) and CONVERT(datetime,'" . $datumDO . "',103)";
    try {
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  h
  join  " . $portal_hourlyrate_year . "  y
  on h.year_id = y.id
  join  " . $portal_calendar . "  c
  on (c.Year = y.year and c.Day = h.day and c.Month=h.month_id)
  WHERE 
   (
   --weekday<>'6' AND weekday<>'7'
   corr_status not in (5,85,86,87,88,89,90,83)" .
            $where_month1 . $vrsta_query . $grupa_query . $role_query . $employee_query . $ime_prezime_query . $neodobreno_query . $praznici_query . $zahtjevi_query . $per_query . $dokument_query . " and (status != corr_status))
   order by h.id,employee_no");
    } catch (Exception $e) {
        var_dump($e);
    }

    if ($get->rowCount() < 0) {

        $index = 0;
        foreach ($get as $key => $day) {


            if ($key == 0) {
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['year_id'] = $day['year_id'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['godina'] = date('Y', strtotime($day['Date']));
                $day_id = $day[0] - 1;
                $status = $day['corr_status'];

                $employee_no = $day['employee_no'];
                $description = $day['Description'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['start_id'] = $day[0];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['danOD'] = (int)date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['employee_no'] = $employee_no;
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['ime_prezime'] = _employee($employee_no)['fname'] . ' ' . _employee($employee_no)['lname'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['registrovano'] = $day['timest_edit_corr'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['request_id'] = $day['request_id'];
                if ($day['employee_timest_edit'] != '')
                    $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                else
                    $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['reg_korisnik'] = '';
                $pocetni_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['ima_dokument'] = $day['dokument'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['disease_code'] = $day['disease_code'];
                $arr [_nameHRstatus($day['corr_status']) . '-' . $index]['status'] = $day['corr_status'];
            }

            $status_curr = $day['corr_status'];

            if (($status_curr == $status) and ($day['employee_no'] == $employee_no) and ($day['Description'] == $description) and ($day[0] == ($day_id + 1))) {
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($day['corr_status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['corr_review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['rev_status'] = $day['corr_review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['end_id'] = $day[0];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));
            } else {
                $index = $index + 1;
                if (($day['weekday'] != 6 and $day['weekday'] != 7) or in_array($day['corr_status'], array(43, 44, 45, 61, 62, 65, 67, 68, 69, 74, 75, 76, 77, 78, 73, 81)))
                    $arr [_nameHRstatus($status_curr) . '-' . $index][] = $day['corr_review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['rev_status'] = $day['corr_review_status'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['year_id'] = $day['year_id'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['godina'] = date('Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['start_id'] = $day[0];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumOD'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danOD'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecOD'] = date('n', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['employee_no'] = $day['employee_no'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['ime_prezime'] = _employee($day['employee_no'])['fname'] . ' ' . _employee($day['employee_no'])['lname'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['registrovano'] = $day['timest_edit_corr'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['request_id'] = $day['request_id'];
                if ($day['employee_timest_edit'] != '')
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['reg_korisnik'] = _employee($day['employee_timest_edit'])['fname'] . ' ' . _employee($day['employee_timest_edit'])['lname'];
                else
                    $arr [_nameHRstatus($status_curr) . '-' . $index]['reg_korisnik'] = '';
                $arr [_nameHRstatus($status_curr) . '-' . $index]['datumDO'] = date('d.m.Y', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['end_id'] = $day[0];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['danDO'] = date('j', strtotime($day['Date']));
                $arr [_nameHRstatus($status_curr) . '-' . $index]['mjesecDO'] = date('n', strtotime($day['Date']));

                $arr [_nameHRstatus($status_curr) . '-' . $index]['ima_dokument'] = $day['dokument'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar'] = $day['review_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['komentar_radnika'] = $day['employee_comment'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['disease_code'] = $day['disease_code'];
                $arr [_nameHRstatus($status_curr) . '-' . $index]['status'] = $status_curr;

            }

            $day_id = $day[0];
            $status = $day['corr_status'];

            $employee_no = $day['employee_no'];
            $description = $day['Description'];

            $krajnji_datum = date('d-m-Y', strtotime($day['day'] . '-' . $day['month_id'] . '-' . $year_real['year']));

        }

        foreach ($arr as $key => $value) {

            $count_num++;
            $count0 = count(array_keys($value, '0'));
            $count1 = count(array_keys($value, '1'));
            $count2 = count(array_keys($value, '2'));
            $pieces = explode("-", $key);
            $naziv_odsustva = $pieces[0];

            $status1 = $value['status'];
            if ($status1 == 18 or $status1 == 19)
                $status1 = 106;
            if ($status1 == 21 or $status1 == 22)
                $status1 = 84;


            if ($value['employee_no'] == '1')
                $count1 = $count1 - 1;
            if ($value['danOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['danDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecOD'] == '1')
                $count1 = $count1 - 1;
            if ($value['mjesecDO'] == '1')
                $count1 = $count1 - 1;
            if ($value['ima_dokument'] == '1')
                $count1 = $count1 - 1;
            if ($value['komentar_radnika'] == '1')
                $count1 = $count1 - 1;
            $br_dana = $count0 + $count1;


            if (in_array($status1, array(43, 44, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 107, 108, 73, 81, 74, 75, 76, 77, 78))) {

                $date11 = strtotime($value['datumOD']);
                $date22 = strtotime($value['datumDO']);
                $diff = $date22 - $date11;
                $br_dana = floor($diff / (60 * 60 * 24)) + 1;


            } else {
                $br_dana = getWorkingDays($value['datumOD'], $value['datumDO'], array());
            }

            if ($value['rev_status'] == '1')
                $odobreno = '1';
            else
                $odobreno = '';


            if ($_user['role'] == 4) {
                $disejbld = '';
                if ($odobreno == '1') {
                    $disejbld = "disabled='disabled'";
                }
                $naziv_odsustvaOpcija = '<select ' . $disejbld . ' id="status-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="vrsta1" title="' . _nameHRstatus($status1) . '" class="rcorners1 tootip" class="form-control" style="width:100%;outline:none;">' . _optionHRstatus($status1) .
                    '</select>';
            } else if ($_user['role'] == 2) {
                $naziv_odsustvaOpcija = '<select disabled id="status-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="vrsta1" class="rcorners1" class="form-control" style="width:100%;outline:none;">' . _optionHRstatus($status1) .
                    '</select>';
            }

            if ($filter_zahtjevi == true)
                $optionOdobriOtkazi = ' <td> <select data-otkazivanje="1" data-user-id="' . $value['employee_no'] . '" id="odobreno-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="odobreno" class="rcorners1" class="form-control" style="width:120px;outline:none;">' . _optionOtkazano($odobreno);
            else
                $optionOdobriOtkazi = ' <td> <select data-user-id="' . $value['employee_no'] . '" id="odobreno-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name="odobreno" class="rcorners1" class="form-control" style="width:120px;outline:none;">' . _optionOdobreno($odobreno);

            $detalji = '';

            if ($_user['role'] == 4)
                $detalji = ' <td> <button type="button" id="detalji-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['godina'] . '-' . $value['ime_prezime'] . '" name="detalji" class="rcorners1" class="form-control" style="width:120px;outline:none;">Detalji</button></td>';

            $satnice = '<td><a style="width: 141px;" href="' . $_conf['app_location'] . '' . $_conf['app_location_module'] . '/modules/admin_manager_hourly_rate_corrections/pages/popup_day_add_apsolute.php?year=' . $value['year_id'] . '&month=' . $value['mjesecOD'] . '" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-md">Ažuriraj satnice<i class="ion-ios-plus-empty"></i></a></td>';

            $pieces = explode(" ", $value['registrovano']);
            $registrovano = $pieces[0];
            $registrovano_x = date("d.m.Y", strtotime($registrovano));

            if ($value['ima_dokument'] == '1')
                $dokument_checked = 'checked="checked"';
            else
                $dokument_checked = '';
            $items .= '<tr  data-row-id="datarow-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '">';
            if ($filter_zahtjevi != true):
                $items .= '
      <td>
        <input comment-id="' . $value['start_id'] . '$#' . $value['end_id'] . '" class="odobri-ids" type="checkbox" name="odobri[]" value="' . $value['start_id'] . '$#' . $value['end_id'] . '" />
      </td>
      ';
            endif;
            $items .= '<td>' . $value['employee_no'] . '</td>' . '<td>' . $value['ime_prezime'] . '</td>' . '<td>' . $value['datumOD'] . '</td>' . '<td>' . $value['datumDO'] . '</td>' . '<td>' . $naziv_odsustvaOpcija . '</td>' . '<td>' . $br_dana . '</td>' . '<td>' . $registrovano_x . '</td>' . '<td>' . $value['reg_korisnik'] . '</td>' .
                $optionOdobriOtkazi .
                '</select></td>' . $detalji . $satnice;

//            $dokument_input = '<td><input id="dokument-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" type="checkbox" ' . $dokument_checked . ' value="1" name="dokument-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" style=""></td>';

            if ($_user['role'] == 4)
//                $items .= $dokument_input;


            if (checkifAdmin() == true) {
//                $items .= '<td><input type="text" style="max-width: 50px;height: 46px;" id="disease_code-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" value="' . $value['disease_code'] . '" name="disease_code-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '"></td>';
            }

            $items .= '<td><textarea data-comment-id="' . $value['start_id'] . '$#' . $value['end_id'] . '" maxlength="250" style="max-width: 110px;" id="komentar-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" name=id="komentar-' . $value['danOD'] . '-' . $value['mjesecOD'] . '-' . $value['danDO'] . '-' . $value['mjesecDO'] . '-' . $value['year_id'] . '" spellcheck="false">' . $value['komentar'] . '</textarea></td>' .
                '<td><textarea id="comment_employee" style="max-width: 110px;" name="comment_employee" readonly>' . $value['komentar_radnika'] . '</textarea></td>' .
                '</tr>';
        }
    }

    $items .= '</table>';

    if ($counter == 1):
        return $count_num;
    else:
        return $items;
    endif;


}


function _exportExcel($year, $month_from, $month_to, $day_from, $day_to)
{

    global $db, $root, $portal_hourlyrate_day;

    $items = '';
    $arr = array();
    $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE 
   (
   weekday<>'6' AND weekday<>'7' and (
   (day >= " . $day_from . " and month_id = " . $month_from . " and (" . $month_from . " <> " . $month_to . "))  OR
   (day <= " . $day_to . " and month_id = " . $month_to . " and (" . $month_to . " <> " . $month_from . ")) OR
   ((day >= " . $day_from . " and day <= " . $day_to . ") and month_id = " . $month_from . " and month_id = " . $month_to . ") OR
   (month_id > " . $month_from . " and month_id < " . $month_to . ")
   ))
   and year_id=" . $year);


    if ($get->rowCount() < 0) {

        foreach ($get as $day) {

            $arr [_nameHRstatus($day['status'])][] = $day['review_status'];

        }


    }

    date_default_timezone_set('America/Los_Angeles');
    require_once($root . '/CORE/PHPExcel-1.8/Classes/PHPExcel.php');

    $doc = new PHPExcel();
    $doc->setActiveSheetIndex(0)->setCellValue('A1', 'Hello')
        ->setCellValue('B2', 'world!')
        ->setCellValue('C1', 'Hello')
        ->setCellValue('D2', 'world!');

    // $doc->getActiveSheet()->fromArray($arr, null, 'A1');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="your_name.xls"');
    header('Cache-Control: max-age=0');

    // Do your stuff here
    $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

    $writer->save('php://output');


}


function _result2()
{
    //$data =  $db->query("SELECT username FROM  ".$portal_users."  WHERE user_id='587'");
    //foreach ($data as $valuedata) {echo $valuedata['username'];}

    echo 'n';

}

function generate_user($length, $type)
{
    if ($type == 1) {
        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ?!_-+';
    } elseif ($type == 0) {
        $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    }
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function _count_user()
{

    global $db, $_conf, $portal_users;

    $query = $db->query("SELECT count(*) as broj FROM  " . $portal_users . " ");

    foreach ($query as $item) {
        $num_users = $item['broj'];
    }
    return $num_users;
}

//citav kalendar stavljen u array, korekcije i obicne u istom insertu
function _addAllMonths($year)
{
    try {
        session_write_close();
        global $db, $portal_hourlyrate_status, $_conf, $portal_users, $portal_hourlyrate_day, $portal_calendar, $portal_hourlyrate_year, $portal_hourlyrate_month, $portal_hourlyrate_month_correctoins, $portal_holidays_per_department;

        $now = new DateTime();
        $filteryear = $now->format('Y');
        $filtermonth = $now->format('m');
        $filtermonthEmployment = $filtermonth + 1;
        $filtertdate = $filteryear . "-" . $filtermonth . "-1 00:00:00.000";
        $filtertdateEmploy = $filteryear . "-" . $filtermonthEmployment . "-1 00:00:00.000";

        $query_users = $db->query("SELECT [user_id], employee_no,Stream_description,Team_description,centrala, br_sati FROM  " . $portal_users . "  where ((termination_date>='" . $filtertdate . "') or
      (termination_date is null))
      order by user_id");


        $query_calendar = $db->query("SELECT [Date],[day],[weekday],[KindOfDay],[Description],[Hr_status],[Month] FROM  " . $portal_calendar . "  where [year]='" . $year . "'");
        $query_calendar_total = $query_calendar->fetchAll();

        foreach ($query_users as $item) {
            try {
                $year_id_get = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_year . "  where [user_id]='" . $item['user_id'] . "' and year='" . $year . "'");
                $year_id1 = $year_id_get->fetch();
                $year_id = $year_id1['id'];

                $employee_no = $item['employee_no'];
                $isCentrala = $item['centrala'];
                $hour_radnik = $item['br_sati'];

                if ($isCentrala == '1')
                    $Stream_description = 'CENTRALA';
                else
                    $Stream_description = $item['Stream_description'];

                $Team_description = $item['Team_description'];

                $data1 = "INSERT INTO  " . $portal_hourlyrate_month . " (id,
      user_id,year_id,month, verified, verified_corrections) VALUES ";
                $data2 = "INSERT INTO  " . $portal_hourlyrate_month_correctoins . " (
      user_id,year_id,month) VALUES ";


                for ($m = 1; $m <= 12; $m++) {
                    $d1 = "(" . $m . "," . $item['user_id'] . "," . $year_id . "," . $m . "," . "0" . "," . "0" . "), ";
                    $d2 = "(" . $item['user_id'] . "," . $year_id . "," . $m . "), ";
                    $data1 .= $d1;
                    $data2 .= $d2;
                }

                $data1 = substr($data1, 0, -2);
                $data2 = substr($data2, 0, -2);

                try {
                    $res = $db->prepare($data1);
                    $res->execute(
                        array()
                    );
                    $res1 = $db->prepare($data2);
                    $res1->execute(
                        array()
                    );

                } catch (PDOException $e) {

                }


                for ($m = 1; $m <= 12; $m++) {

                    $query_calendar_arr = array_filter($query_calendar_total, function ($var) use ($m) {
                        return ($var['Month'] == $m);
                    });

//                     $query_calendar_arr=$query_calendar->fetchAll();

                    $query_calendar_holiday = $db->query("SELECT * FROM  " . $portal_holidays_per_department . "  where [department name]='" . $Stream_description . "' or [department name]='" . $Team_description . "' or ([department name]='')");

                    if ($query_calendar_holiday->rowCount() < 0) {
                        foreach ($query_calendar_holiday as $cal_hol) {
                            foreach ($query_calendar_arr as &$cal) {

                                if ($cal_hol['Pomicni'] == '0') {
                                    if (($cal['day'] == date('j', strtotime($cal_hol['date']))) and ($cal['Month'] == date('n', strtotime($cal_hol['date'])))) {
                                        $cal['KindOfDay'] = $cal_hol['holiday_type'];
                                        $cal['Description'] = $cal_hol['holiday_name'];
                                        $cal['Hr_status'] = $cal_hol['Hr_status'];
                                        break;
                                    }
                                } else {
                                    if ($cal['Date'] == $cal_hol['date']) {
                                        $cal['KindOfDay'] = $cal_hol['holiday_type'];
                                        $cal['Description'] = $cal_hol['holiday_name'];
                                        $cal['Hr_status'] = $cal_hol['Hr_status'];
                                        break;
                                    }
                                }
                            }
                            unset($cal);
                        }
                    }

                    $data = "INSERT INTO  " . $portal_hourlyrate_day . "  (
      user_id,year_id,month_id,day,hour,status,corr_status,review_status,corr_review_status,weekday,KindOfDay,Description,employee_no,B_1_regions,B_1_regions_description,[Date]) VALUES ";

                    foreach ($query_calendar_arr as $cal) {
                        $day = $cal ['day'];
                        $weekday = $cal ['weekday'];
                        $kind = $cal ['KindOfDay'];
                        $desc = $cal ['Description'];
                        $hrstat = $cal['Hr_status'];
                        $date = $cal['Date'];
                        if ($kind == 'BANKDAY') {
                            $status = '5';
                            $hour = $hour_radnik;
                            $review_status = '0';
                            $corr_review_status = '0';
                        }
                        if ($kind == 'BHOLIDAY') {
                            $query_status = $db->query("SELECT [id] FROM  " . $portal_hourlyrate_status . "  where [name]='" . $hrstat . "'
   ");

                            foreach ($query_status as $calstat) {
                                $status = $calstat['id'];
                                $hour = $hour_radnik;
                                $review_status = '1';
                                $corr_review_status = '1';
                            }
                        }
                        if (($kind != 'BANKDAY') and ($kind != 'BHOLIDAY')) {
                            $status = '5';
                            $hour = '0';
                            $review_status = '0';
                            $corr_review_status = '0';
                        }

                        $values = "(" . $item['user_id'] . "," . $year_id . "," . $m . "," . $day . "," . $hour . "," . $status . "," . $status . "," . $review_status . "," . $corr_review_status . "," . $weekday . ",'" . $kind . "','" . $desc . "'," . $employee_no . "," . "''" . "," . "''" . ",'" . $date . "'), ";

                        $data .= $values;
                    }

                    $data = substr($data, 0, -2);
                    $res = $db->prepare($data);

                    {
                        $res->execute(
                            array()
                        );
                    }

                }
            } catch (Exception $e) {

            }
        }

        return '<div class="alert alert-success-raiff text-center">' . __('Informacije su uspješno spašene!') . '</div>';
    } catch (PDOException $e) {

    }

}


//citav kalendar stavljen u array, korekcije i obicne u istom insertu
function _addHoliday($holidayName, $orgJed, $holidayDate)
{
    session_write_close();
    global $db, $_conf, $nav_employee, $portal_users, $portal_hourlyrate_year, $portal_hourlyrate_day;

    $orgJedId = $db->query("select id from [c0_intranet2_apoteke].[dbo].[systematization] where s_title=N'".$orgJed."' or s_title='".$orgJed."'")->fetch()['id'];

    if ($orgJed == 'Apoteke Sarajevo'){
        $condition  = '';
    }
    else{
        $condition = " where egop_ustrojstvena_jedinica=".$orgJedId;
    }

    $orgJedUsers = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] ".$condition);

    foreach ($orgJedUsers as $user){
        try {
            $sqlQuery = "update [c0_intranet2_apoteke].[dbo].[hourlyrate_day] set
                    status=83,
                    KindofDay='BHOLIDAY',
                    corr_status=83,
                    review_status=1,
                    corr_review_status=1,                                                     
                    Description='".$holidayName."' where employee_no='".$user['employee_no']."' and Date='".date('Y-m-d', strtotime($holidayDate))."'
                    and status not in (43,44,45,61,62,65,67,68,69,74,75,76,77,78,73,81,107,108)";
            $sql = $db->prepare($sqlQuery);
            $sql->execute();
        }catch (Exception $e){
            var_dump($e);
        }
    }

    // if($res->rowCount()<0) {
    return '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
    // }
}

function _updateHoliday($holiday_name, $department_name, $holiday_date, $pomicni, $old_date, $this_id)
{
    session_write_close();
    global $db, $_conf, $portal_hourlyrate_year, $portal_holidays_per_department, $nav_employee, $portal_users, $portal_hourlyrate_day;

    _removeHoliday($this_id);
    _addHoliday($holiday_name, $department_name, $holiday_date);

    // if($res->rowCount()<0) {
    return '<div class="alert alert-success text-center">' . __('Informacije su uspješno spašene!') . '</div>';
    // }
}

function _removeHoliday($this_id){
    session_write_close();
    global $db, $_conf, $portal_holidays_per_department, $nav_employee, $portal_users, $portal_hourlyrate_year, $portal_hourlyrate_day, $nav_employee_contract_ledger;

    $holidayInfo = $db->query("SELECT * FROM  [c0_intranet2_apoteke].[dbo].[holidays_per_department]  where id=" . $this_id)->fetch();
    $orgJedId = $db->query("select id from [c0_intranet2_apoteke].[dbo].[systematization] where s_title=N'".$holidayInfo['department name']."' or s_title='".$holidayInfo['department name']."'")->fetch()['id'];

    if($orgJedId == 1){
        $condition = "";
    }
    else{
        $condition = " where egop_ustrojstvena_jedinica=".$orgJedId;
    }
    $orgJedUsers = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users] ".$condition);

    $day = date('D', strtotime('2021-07-31'));

    //Sun, Sat
    switch ($day){
        case 'Sat':
            $kindOfDay = 'SATURDAY';
            break;
        case 'Sun':
            $kindOfDay = 'SUNDAY';
            break;
        default:
            $kindOfDay = 'BANKDAY';
    }

    foreach ($orgJedUsers as $user){
        try {
            $sqlQuery = "update [c0_intranet2_apoteke].[dbo].[hourlyrate_day] set
                    status=5,
                    KindofDay='".$kindOfDay."',
                    corr_status=5,
                    review_status=0,
                    corr_review_status=0,                                                     
                    Description='' where employee_no='".$user['employee_no']."' and Date='".date('Y-m-d', strtotime($holidayInfo['date']))."'";
            $sql = $db->prepare($sqlQuery);
            $sql->execute();
        }catch (Exception $e){
            var_dump($e);
        }
    }

}

function insertMonth1($m, $user_id, $year_id)
{
    global $db, $_conf, $portal_hourlyrate_month, $portal_hourlyrate_month_correctoins;

    $data = "INSERT INTO  " . $portal_hourlyrate_month . " (id,
      user_id,year_id,month, verified, verified_corrections) VALUES (" . $m . "," . $user_id . "," . $year_id . "," . $m . "," . "0" . "," . "0" . ")  " .
        "INSERT INTO  " . $portal_hourlyrate_month_correctoins . " (
      user_id,year_id,month) VALUES (" . $user_id . "," . $year_id . "," . $m . ")";

    $res = $db->prepare($data);
    $res->execute(
        array()
    );
}

function _sendMail($_user, $_user_to_send, $status_izostanka)
{

    require '../../send-email-from-localhost-php/PHPMailer/PHPMailerAutoload.php';
    require '../../mails.php';

    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";

    $mail->isSMTP();
    $mail->Host = gethostbyname('barbbcom');                   // Specify main and backup SMTP servers
    $mail->SMTPAuth = false;                            // Enable SMTP authentication
//$mail->Username = 'nav@teneo.ba';          // SMTP username
//$mail->Password = 'DynamicsNAV16!'; // SMTP password
    $mail->Port = 25;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;                              // TCP port to connect to

    $mail->setFrom('HRpodrska@raiffeisen.ba', 'Employee Portal Info'); // sender
    $mail->addAddress($_user_to_send['email']); //recipient

    $mail->isHTML(true);  // Set email format to HTML

    $mail->Subject = 'Registracija izostanka'; // mail subject
    $mail->Body = $mails['day-edit']; // mail content

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
    }
}


function MailNotifications($to, $from, $type, $_user, $_parent, $data)
{
    global $db, $_conf, $portal_settings;


    if (in_array($data['vrsta_odsustva'], array(43, 44, 45, 61, 62, 63, 64, 65, 66, 67, 68, 69, 107, 108, 73, 81, 74, 75, 76, 77, 78))) {

        $date11 = strtotime($data['start_date']);
        $date22 = strtotime($data['end_date']);
        $diff = $date22 - $date11;
        $br_dana = floor($diff / (60 * 60 * 24)) + 1;

    } else {
        $br_dana = getWorkingDays($data['start_date'], $data['end_date'], array());
    }

    if ($type == 'odbijeno'):
        $subjecty = 'Odbijena registracija';

        $mail_body = '
        <strong>Postovani/Postovana ' . $_user['fname'] . ' ' . $_user['lname'] . '</strong>,<br />
        <br />
        HR/Direktni nadredjeni (adm #' . $_parent['employee_no'] . ') je odbio Vas zahtjev Registracija Odsustva (' . date("d.m.Y G:i") . ')
        <br /><br />
        Detalji:
        <br />
        <table style="">
      <tbody>
      <tr style="">
        <td style="" colspan="2">
          Pocetni datum:
        </td>
        <td style="" colspan="2">
          <strong>' . $data['start_date'] . '</strong>
        </td>
      </tr>
      <tr style="">
        <td style="" colspan="2">
          Krajnji datum:
        </td>
        <td style="" colspan="2">
          <strong>' . $data['end_date'] . '</strong>
        </td>
      </tr>
      <tr style="">
        <td style="" colspan="2">
          Ukupan broj dana:
        </td>
        <td style="" colspan="2">
          <strong>' . $br_dana . '</strong>
        </td>
      </tr>
     
      <tr style="">
        <td style="" colspan="2">
          Vrsta Odsustva:
        </td>
        <td style="" colspan="2">
          <strong>' . _nameHRstatus($data['vrsta_odsustva']) . '</strong>
        </td>
      </tr>
       

      
      </tbody>
      </table>
    Dodatni komentar: ' . $data['komentar'] . '
      ';

    endif;


    // Mail notifikacija

    $get_mail_settings = $db->query("SELECT name, value FROM  " . $portal_settings . "  WHERE name LIKE '%hr%mail' or name = 'hr_notifications'");
    $get_mail_fetch = $get_mail_settings->fetchAll();

    $mail_settings = array();
    foreach ($get_mail_fetch as $key => $value) {
        $mail_settings[$value['name']] = $value['value'];
    }

    $array_bolovanje = array("43", "44", "45", "61", "62", "65", "67", "68", "69", "72", "73", "74", "75", "76", "77", "78", "81", "107", "108", "79", "80", "28", "29", "30", "31", "32", "27", "105", "106", "18", "19");

    // Bolovanje i placena odsustva
    if ($mail_settings['hr_notifications'] == '1') {
        if (in_array($_POST['status'], $array_bolovanje)) {
            // start mail

            $status_izostanka = $status;

            $_user = _user(_decrypt($_SESSION['SESSION_USER']));
            $user_edit = $_user;

            require '../../lib/PHPMailer/PHPMailer.php';
            require '../../lib/PHPMailer/SMTP.php';
            require '../../lib/PHPMailer/Exception.php';


            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->CharSet = "UTF-8";

            $mail->IsSMTP();
            $mail->isHTML(true);  // Set email format to HTML

            $mail->Host = "barbbcom";
            $mail->Port = 25;


            //$mail->setFrom($_user['email'], $_user['fname'] . ' ' . $_user['lname']);
            $mail->setFrom($from, "Infodom");

            //$mail->addAddress($_user['email']);
            //$mail->addAddress($mail_settings['hr_supportt_mail']);
            foreach ($to as $key) {
                $mail->addAddress($key);
            }

            //
            //$mail->addAddress($parent_user['email']); // todo nadredjeni mail

            $mail->Subject = $subjecty;
            //$_user=$user_edit;


            //$parent_user = _employee($_user['parent']);
            //$mail->Body     = $mails['odbijena-registracija'];
            $mail->Body = $mail_body;

            if (!$mail->send()) {
                //echo 'Message was not sent.';
                //echo 'Mailer error: ' . $mail->ErrorInfo;
            } else {
                //echo 'Message has been sent.';
            }
        }
    }


}


function ___formatDate($date){
    try{
        return Carbon::parse($date)->format('d.m.Y');
    }catch (\Exception $e){ ; }
}