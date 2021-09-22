<?php

  _pagePermission(4, false);
  error_reporting(0);

  $ts=time();$user=$_user['user_id'];
  date_default_timezone_set('Europe/Sarajevo');

  $filtertdate=date('Y')."-".date('m')."-1 00:00:00.000";
  //var_dump($filtertdate);
  $canSendMail = $db->query("SELECT value
  FROM [c0_intranet2_apoteke].[dbo].[settings]
  where name = 'hr_notifications'");
  $canSendMail = $canSendMail->fetch();
  
//odbijanje --- prepraviti

if(isset($_POST['razlog'])){
  $podaci_mailq = $db->query("
    SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table2.id = ".$_GET['odbij']."
  ");
  $podaci_mail = $podaci_mailq->fetch();

  $parentq = $db->query("SELECT email_company, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
  $parent = $parentq ->fetch(); 

  require 'lib/PHPMailer/PHPMailer.php';
  require 'lib/PHPMailer/SMTP.php';
  require 'lib/PHPMailer/Exception.php';

  $mail = new PHPMailer\PHPMailer\PHPMailer();
  $mail->CharSet = "UTF-8";

  $mail->IsSMTP();
  $mail->isHTML(true);  // Set email format to HTML

  $mail->Host = "mailgw.rbbh.ba";
  $mail->Port = 25;

  $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
  $mail->addAddress($podaci_mail['email_company']);
  $mail->addAddress('racunovodstvo@raiffeisengroup.ba');

  $mail->Subject = 'Nedostaci na nalogu službenog puta broj -'.$_GET['odbij'];
  $mail->Body = "Poštovani,<br>
  Nalog je vraćen na korekciju. Molimo vas da izvršite korekcije.<br>
  Razlog korekcije: ".$_POST['razlog']."
  ";

  if($canSendMail)
  if (!$mail->send()) {
    //var_dump($mail->ErrorInfo);
  }

  if(isset($_GET['odbij'])){
    $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sl_put] where status_hr = '2' and id =".$_GET['odbij']);
    foreach ($provjeraq as $one){
      $provjera = $one['aa'];
    }
    if ($one['aa']==0){
        $odbij_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set status_hr = 2 where id =".$_GET['odbij']);
        $odbij_req->execute();
        $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
        VALUES (".$_GET['odbij'].", 'odbijanje', $user, $ts)");
  
    header("Location: /apoteke-app/?m=business_trip&p=all&pg=".$_GET['pg']); 
    exit();
    }
  }
}
//odobravanje

if(isset($_GET['odobri'])){
  $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sl_put] where status_hr = '1' and id =".$_GET['odobri']);
  foreach ($provjeraq as $one){
    $provjera = $one['aa'];
  }
  if ($one['aa']==0){
    $odobri_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set status_hr = 1 where request_id =".$_GET['odobri']);
    $odobri_req->execute();
    $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
    VALUES (".$_GET['odobri'].", 'odobravanje', $user, $ts)");
    //prijenos na satnicama

    $podaci_mailq = $db->query("
    SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table1.id = ".$_GET['odobri']."
  ");
  $podaci_mail = $podaci_mailq->fetch();

    $parent = $db->query("SELECT email_company, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
    $parent = $parent ->fetch();
    send_mails($podaci_mail,$parent,false);
  }
  header("Location: /apoteke-app/?m=business_trip&p=all&pg=".$_GET['pg']); 
  exit();
}



//zakljucavanje - odkljucavanje
if(isset($_GET['zakljucaj'])){
  // $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sl_put] where lock = '1' and request_id =".$_GET['zakljucaj']);
  // foreach ($provjeraq as $one){
  //   $provjera = $one['aa'];
  // }
  // if ($provjera==1){
  //   $lock_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set lock = 0 where request_id =".$_GET['zakljucaj']);
  //   $lock_req->execute();
  //   $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
  //   VALUES (".$_GET['zakljucaj'].", 'odkljucavanje', $user, $ts)");
  //   }else{
      $lock_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set lock = 1 where id =".$_GET['zakljucaj']);
      $lock_req->execute();
      $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
      VALUES (".$_GET['zakljucaj'].", 'zakljucavanje', $user, $ts)");
      // }

      header("Location: /apoteke-app/?m=business_trip&p=all&pg=".$_GET['pg']); 
      exit();
}
//trenutni link
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
  $trenutni_link = "https"."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
} else {
  $trenutni_link = "http"."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

$uri_variable = explode('&',$_SERVER['REQUEST_URI']);
foreach($uri_variable as $variabla){
    if (strpos($variabla, 'odobri') !== false or strpos($variabla, 'odbij') !== false or strpos($variabla, 'zakljucaj') !== false){
      $trenutni_link = str_replace($variabla, "", $trenutni_link);
    }
}

while (strpos($trenutni_link, '&&') !== false){
  $trenutni_link = str_replace("&&", "", $trenutni_link);
}
//admin ili korisnik provjera
$admin = $db->query("SELECT count(user_id) as br FROM [c0_intranet2_apoteke].[dbo].[users] where sl_put_admin=1 and user_id=".$_user['user_id']);
foreach($admin as $admin1){
  $admin = $admin1;
}

if ($admin1['br']==1){
  $admin=true;
} 
else {$admin=false;}

//paginacija varijable
if (isset($_GET['pg'])) {
  $pageno = $_GET['pg'];
} else {
  $pageno = 1;
}
$no_of_records_per_page = 7;
$offset = ($pageno-1) * $no_of_records_per_page;



//hvatanje podataka obicni i admin    --(izi izi tammam tamam)--
//filter isset forma pocinje

if (!empty($_GET['dod'])) {
        $dateod = $_GET['dod'];
        $date1 = date("Y/m/d", strtotime(str_replace("/", "-", $_GET['dod'])));
        $date_query_od = " and table2.pocetak_datum >='" . $date1 . "'";
    } else {
        $dateod = '';
        $date_query_od = "";
    }

if (!empty($_GET['ddo'])) {
        $datedo = $_GET['ddo'];
        $date2 = date("Y/m/d", strtotime(str_replace("/", "-", $_GET['ddo'])));
        $date_query_do = "and table2.pocetak_datum <='" . $date2 . "'";
    } else {
        $datedo = '';
        $date_query_do = "";
    }

if (!empty($_GET['dkod'])) {
        $date_kreiranja_od = $_GET['dkod'];
        $date_kreiranja_1 = strtotime($date_kreiranja_od);
        $date_kreiranja_query_od = " and table2.created_at >='" . $date_kreiranja_1 . "'";
    } else {
        $date_kreiranja_od = '';
        $date_kreiranja_query_od = "";
    }

if (!empty($_GET['dkdo'])) {
        $date_kreiranja_do = $_GET['dkdo'];
        $date_kreiranja_2 = strtotime($date_kreiranja_do) + 86400;
        $date_kreiranja_query_do = "and table2.created_at <='" . $date_kreiranja_2 . "'";
    } else {
        $date_kreiranja_do = '';
        $date_kreiranja_query_do = "";
    }

if (!empty($_GET['akood'])) {
        $akonod = $_GET['akood'];
        $akonod_query = " and table2.iznos_akontacije >='" . $akonod . "'";
    } else {
      $akonod = "";
        $akonod_query = "";
    }

if (!empty($_GET['akodo'])) {
        $akondo = $_GET['akodo'];
        $akondo_query = " and table2.iznos_akontacije <='" . $akondo . "'";
    } else {
      $akondo = "";
        $akondo_query = "";
    }

if(!empty($_GET["kid"])){
  $korisnik_id_ime_prez = "and table3.employee_no=".htmlspecialchars($_GET["kid"]);
}else{$korisnik_id_ime_prez = '';}

if(!empty($_GET["jmb"])){
  $jmb_filter = "and table3.JMB=".htmlspecialchars($_GET["jmb"]);
}else{$jmb_filter = '';}

if(!empty($_GET["mjesto"])){
  $mjesto_query = "and table2.odredisna_drzava=".htmlspecialchars($_GET["mjesto"])." or table2.odredisna_drzava2=".htmlspecialchars($_GET["mjesto"])." or table2.odredisna_drzava3=".htmlspecialchars($_GET["mjesto"])."";
}else{$mjesto_query = '';}

if(!empty($_GET["trn"])){
  $trn_query = "and table3.employee_no=".htmlspecialchars($_GET["trn"]);
}else{$trn_query = '';}

if(!empty($_GET["status"])){
if ($_GET["status"]==1) {
  $stak="and (operation = 'obrada' or operation = 'odobravanje') ";
}elseif ($_GET["status"]==12) {
  $stak="and table2.na_obradi=1 and (table2.status_hr=0 or table2.status_hr=2)";
}elseif ($_GET["status"]==2) {
  $stak="and operation = 'odbijanje' ";
}elseif ($_GET["status"]==10) {
 $stak="and table2.na_obradi IS NULL";
}elseif ($_GET["status"]==111) {
 $stak="and table2.lock=1 ";
}elseif ($_GET["status"]==69) {
  $stak = " and canceled = 1 ";
}
  $status_query = $stak;

}else{
  $status_query = '';};




if ($admin==true or $_user['role'] == 4){
  
  $total_rowsq = $db->query("
  SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  OUTER APPLY
    (
        SELECT TOP 1 *
        FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
        WHERE logs.sl_put_request_id = table2.id 
    order by logs.id desc
    ) logs
    where table1.id is not null
  $korisnik_id_ime_prez
  $jmb_filter
  $date_query_od
  $date_query_do
  $mjesto_query
  $akonod_query
  $akondo_query
  $trn_query
  $status_query
  $date_kreiranja_query_od
  $date_kreiranja_query_do

  ");

  $total_rows = $total_rowsq->fetch();
  $total_rows = $total_rows[0];
  $total_pages = ceil($total_rows / $no_of_records_per_page);

  $podaci = $db->query("
  SELECT *,table2.status as sl_put_status, table2.id as sl_put_id,
CASE
    WHEN (
  SELECT count(temp.id) from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as temp
  where temp.Date >=table2.pocetak_datum and  temp.Date <=
    CASE
      WHEN table2.kraj_datum2 is null or table2.kraj_datum2 ='' THEN table2.kraj_datum 
      ELSE table2.kraj_datum2
    END
  and temp.id between table2.request_id - 90 and table2.request_id + 90 
  and temp.status not in (73,81) and temp.corr_status not in (73,81)
  ) > 0 THEN 'DA'
    ELSE 'NE'
END AS otkazano

FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  OUTER APPLY
    (
        SELECT TOP 1 *
        FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
        WHERE logs.sl_put_request_id = table2.id 
    order by logs.id desc
    ) logs
    where table1.id is not null
  $korisnik_id_ime_prez
  $jmb_filter
  $date_query_od
  $date_query_do
  $mjesto_query
  $akonod_query
  $akondo_query
  $trn_query
  $status_query
  $date_kreiranja_query_od
  $date_kreiranja_query_do
   order by created_at desc 
   offset $offset rows
   FETCH NEXT $no_of_records_per_page rows only
  ");

} else {
   $total_rowsq = $db->query("
  SELECT count(*)  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  OUTER APPLY
    (
        SELECT TOP 1 *
        FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
        WHERE logs.sl_put_request_id = table2.id 
    order by id desc
    ) logs
  where (table3.user_id= ".$_user['user_id']."
  or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table2.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id'].")
  $korisnik_id_ime_prez
  $jmb_filter
  $date_query_od
  $date_query_do
  $mjesto_query
  $akonod_query
  $akondo_query
  $trn_query
  $status_query
  $date_kreiranja_query_od
  $date_kreiranja_query_do
  ");
  $total_rows = $total_rowsq->fetch();
  $total_rows = $total_rows[0];
  $total_pages = ceil($total_rows / $no_of_records_per_page);

  $podaci = $db->query("
  SELECT *,table2.status as sl_put_status, table2.id as sl_put_id,
CASE
    WHEN (
  SELECT count(temp.id) from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as temp
  where temp.Date >=table2.pocetak_datum and  temp.Date <=
    CASE
      WHEN table2.kraj_datum2 is null or table2.kraj_datum2 ='' THEN table2.kraj_datum 
      ELSE table2.kraj_datum2
    END
  and temp.id between table2.request_id - 90 and table2.request_id + 90 
  and temp.status not in (73,81) and temp.corr_status not in (73,81)
  ) > 0 THEN 'DA'
    ELSE 'NE'
END AS otkazano

FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  OUTER APPLY
    (
        SELECT TOP 1 *
        FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
        WHERE logs.sl_put_request_id = table2.id 
    order by id desc
    ) logs
  where (table3.user_id = ".$_user['user_id']." or ".$_user['employee_no']." in (parent,parent2) 
  or ".$_user['employee_no']." in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d)
  or ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)
  or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table2.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id'].")
  $korisnik_id_ime_prez
  $jmb_filter
  $date_query_od
  $date_query_do
  $mjesto_query
  $akonod_query
  $akondo_query
  $trn_query
  $status_query
  $date_kreiranja_query_od
  $date_kreiranja_query_do
  order by created_at desc
   offset $offset rows
   FETCH NEXT $no_of_records_per_page rows only
  ");
}


// kraj hvatanja

function prebaciDatumBih($datum){
  if($datum!='' and $datum!=' '){
      $niz = explode('-',$datum);
  return ((int)$niz[2]).'.'.((int)$niz[1]).'.'.((int)$niz[0]);
}
else return null;
}

 ?>
<style>
.container{
  width: 95% !important;
}
.table-btn{
  display:block!important;
  width:auto;
}

.documents{
    width: 100% !important;
}

.documents:hover{
    background-color: #006595;
    color: white;
}

</style>
<!-- START - Main section -->
<section class="full">
  <div class="container-fluid">
<?php 
//PORUKE
if (isset($odobri_req)){
  ?>
    <div class="alert alert-success" style='margin:20px 0 0 0;'>
      <strong>Uspješno!</strong> Uspješno ste odobrili službeno putovanje.
    </div>
  <?php
  }
else if (isset($odbij_req)){
  ?>
    <div class="alert alert-success" style='margin:20px 0 0 0;'>
      <strong>Uspješno!</strong> Uspješno ste odbili službeno putovanje.
    </div>
  <?php
  }
else if (isset($lock_req)){
?>
  <div class="alert alert-success" style='margin:20px 0 0 0;'>
    <strong>Uspješno!</strong> Uspješno ste zaključali/odključali službeno putovanje.
  </div>
<?php
}else if (isset($danger)){
  ?>
  <div class="alert alert-danger" style='margin:20px 0 0 0;'>
    <strong>Greška!</strong> Dogodila se greška, molimo pokušajte ponovo.
  </div>
  <?php
}
?>
    <div class="row">
      <div class="col-sm-6">
        <h2>
          <?php echo __('Zahtjevi za službeni put'); ?><br/>
        </h2>
      </div>
    </div>
<!-- OVDJE POCINJU FILTERI -->
<div style="margin-left: 15%;">
    <div class="row">


        <!--      <div class="col-sm-12 ">-->
        <!--filter za ime i prezime i Person ID -->
        <?php
        $style = '';
        if($_user['rukovodioc'] == 'DA' or $_user['role'] == 4 ){
            $style = '';
        }else{
            $style = 'style="display:none;"';
        }
        ?>
        <div <?php echo $style;?> class="col-sm-2">
            <label> Zaposlenik</label>
            <select class="filter_form_SL form-control" id="korisnici_filter" name="korisnici_filter" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">
                <option value="">Odaberi...</option>
                <?php
                /*
                $neki_sql=$db->query("
                  SELECT DISTINCT table3.employee_no, table3.fname, table3.lname
                  FROM  [c0_intranet2_apoteke].[dbo].[users] as table3
                   where role <> 0");*/

                if($_user['role'] == 4 or $admin){
                    $neki_sql=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (termination_date>='".$filtertdate."' or termination_date is null) order by employee_no");

                }
                elseif($_user['role'] == 2){
                    $neki_sql=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (termination_date>='".$filtertdate."' or termination_date is null) and (parent='".$_user['employee_no']."' or parent2='".$_user['employee_no']."' or ".$_user['employee_no']." in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d)) order by employee_no");

                }
                foreach ($neki_sql as $podatakID) {
                    /*$selected =  "";
                    if (isset($_GET['kid'])){
                      if($_GET['kid'] == $podatakID['employee_no']){
                      $selected =  "selected";
                    }else{
                    $selected =  "";
                    }
                    }*/
                    $emp_id = '';
                    if(strlen($podatakID['employee_no']) == 1){
                        $emp_id = '00'.$podatakID['employee_no'];
                    }
                    elseif (strlen($podatakID['employee_no']) == 2){
                        $emp_id = '0'.$podatakID['employee_no'];
                    }
                    else{
                        $emp_id = $podatakID['employee_no'];
                    }

                    echo "<option ".$selected." value=".$podatakID['employee_no'].">".$podatakID['fname'].' '.$podatakID['lname']." - ".$emp_id."</option>";
                }
                ?>
            </select>
        </div>

        <!--filter za JMBG -->
        <div style="display: none;" class="col-sm-2">
            <label>JMBG broj </label>
            <p id="jmbhid" style="display: none;"><?php if (isset($_GET['jmb'])) {echo $_GET['jmb'];}else{echo "";} ?> </p>

            <select class="filter_form_SL form-control" id="jmbg_filter" name="jmbg_filter" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg1">
                <option value="">Odaberi...</option>
                <?php
                /*
                if ($admin==true){
                    $neki_sql=$db->query("
                      SELECT DISTINCT table3.employee_no, table3.JMB
                      from [c0_intranet2_apoteke].[dbo].[users] as table3
                      ");
                    }else{
                    $neki_sql=$db->query("
                      SELECT DISTINCT table3.employee_no, table3.JMB
                      FROM  [c0_intranet2_apoteke].[dbo].[users] as table3
                      ");
                    }*/

                if($_user['role'] == 4 or $admin){
                    $neki_sql=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (termination_date>='".$filtertdate."' or termination_date is null)");

                }
                elseif($_user['role'] == 2){
                    $neki_sql=$db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE (termination_date>='".$filtertdate."' or termination_date is null) and (parent='".$_user['employee_no']."' or parent2='".$_user['employee_no']."' or ".$_user['employee_no']." in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d))");

                }

                foreach ($neki_sql as $podatakJMB) {

                    $selected =  "";
                    $vrijednost = $podatakJMB['JMB'];

                    if (isset($_GET['jmb'])) {


                        if($_GET['kid'] == $podatakJMB['employee_no']){
                            $selected =  "selected";
                            $vrijednost = "";
                            echo "<div style='display:none;' id='jmbkorisnika'>".$podatakJMB['JMB']."</div>";
                        }else{

                            $selected =  "";
                            $vrijednost = $podatakJMB['JMB'];

                        }
                        if ($_GET['jmb']!=0) {
                            if($_GET['jmb'] == $podatakJMB['JMB']){
                                $selected =  "selected";
                                $vrijednost = $podatakJMB['JMB'];
                            }
                        }

                    }



                    echo "<option ".$selected." id='jmb".$vrijednost."' value=".$vrijednost.">".$podatakJMB['JMB']."</option>";
                }
                ?>
            </select></div>

        <!--filter za Mjesto odredista -->
        <div class="col-sm-2">
            <label> Mjesto odredišta </label>
            <select class="filter_form_SL form-control" id="mjesto_filter" name="mjesto_filter" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">
                <option value="">Odaberi...</option>
                <?php
                if ($admin==true){
                    $neki_sql=$db->query("
                    SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries]
                    ");
                }else{
                    $neki_sql=$db->query("
                    SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries]
                    ");

                }

                foreach ($neki_sql as $podatakMjesto) {
                    $selected =  "";
                    if (isset($_GET['mjesto'])) {
                        if($_GET['mjesto'] == $podatakMjesto['country_id']){
                            $selected =  "selected";
                        }
                    }


                    echo "<option ".$selected." value=".$podatakMjesto['country_id'].">".$podatakMjesto['name']."</option>";

                }
                ?>
            </select></div>
        <!--filter za trn -->
        <div style="display: none;" class="col-sm-2">
            <label>TRN broj</label>
            <select class="filter_form_SL form-control" id="trn_filter" name="trn_filter" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">
                <option value="">Odaberi...</option>

            </select></div>

        <!--filter za status -->
        <div class="col-sm-2">
            <label>Status naloga </label>
            <select class="filter_form_SL form-control" id="status_filter" name="status_filter" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">

                <option  value=''>Odaberi...</option>
                <option <?php if($_GET['status'] == 1) echo 'selected'; else echo ''; ?> value='1'>Na obradi</option>
                <option <?php if($_GET['status'] == 2) echo 'selected'; ?> value='2'>Poslano na korekciju</option>
                <option <?php if($_GET['status'] == 111) echo 'selected'; ?> value='111'>Zaključano</option>
                <option <?php if($_GET['status'] == 69) echo 'selected'; ?> value='69'>Otkazano</option>
            </select>
        </div>

        <br>

        <!--</div>-->
    </div>

    <div class="row" style="margin-top: 10px;">
        <!-- Filter za datum kreiranja -->
        <div class="col-sm-2">
            <label>Datum kreiranja naloga</label>
            <input type="text" name="date_kreiranja_od" id="date_kreiranja_od" class="form-control" style="height: 39px;" placeholder="Datum od"
                   value="<?php echo $date_kreiranja_od; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">

            <input type="text" name="date_kreiranja_do" id="date_kreiranja_do"  class="form-control" style="height: 39px;" placeholder="Datum do"
                   value="<?php echo $date_kreiranja_do; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">
        </div>


        <!--filter za datum od - do  -->
        <div class="col-sm-2">
            <label>Datum početka SL puta </label>
            <input type="text" name="dateod" id="dateod" style="height: 39px;" class="form-control" placeholder="Datum od"
                   value="<?php echo $dateod; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=">

            <input type="text" name="datedo" id="datedo" style="height: 39px;" class="form-control" placeholder="Datum do"
                   value="<?php echo $datedo; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">
        </div>
        <!--filter za akontaciju  -->
        <div class="col-sm-2">
            <label>Iznos akontacije </label>
            <input type="number" name="akonod" id="akonod" style="height: 39px;" class="form-control" placeholder="Akontacija od"
                   value="<?php echo $akonod; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">

            <input type="number" name="akondo" id="akondo" style="height: 39px;"  class="form-control" placeholder="Akontacija do"
                   value="<?php echo $akondo; ?>" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">

        </div>
        <div class="col-sm-2">
            <label style="color: white;">.</label>
            <input type="button" style="height: 39px;text-align: center;" name="dugme_filter" class="form-control" id="dugme_filter" value="FILTRIRAJ" current_url="?m=<?= $_GET['m']; ?>&p=<?= $_GET['p']; ?>&pg=1">

            <a style="height: 39px ;text-align: center;" name="and $podatak['lock']!=1" class="form-control" href="/apoteke-app/?m=business_trip&p=all&pg=1">OBRIŠI FILTER</a>

        </div>



        <?php
        if (!empty($_GET["kid"])){$dataEX1 =htmlspecialchars($_GET["kid"]);}else{$dataEX1 = '';};
        if (!empty($_GET["jmb"])){$dataEX2 =htmlspecialchars($_GET["jmb"]);}else{$dataEX2 = '';};
        if (!empty($_GET["dod"])){$dataEX3 =htmlspecialchars($_GET["dod"]);}else{$dataEX3 = '';};
        if (!empty($_GET["ddo"])){$dataEX4 =htmlspecialchars($_GET["ddo"]);}else{$dataEX4 = '';};
        if (!empty($_GET["dkod"])){$dataEX5 =htmlspecialchars($_GET["dkod"]);}else{$dataEX5 = '';};
        if (!empty($_GET["dkdo"])){$dataEX6 =htmlspecialchars($_GET["dkdo"]);}else{$dataEX6 = '';};
        if (!empty($_GET["mjesto"])){$dataEX7 =htmlspecialchars($_GET["mjesto"]);}else{$dataEX7 = '';};
        if (!empty($_GET["akood"])){$dataEX8 =htmlspecialchars($_GET["akood"]);}else{$dataEX8 = '';};
        if (!empty($_GET["akodo"])){$dataEX9 =htmlspecialchars($_GET["akodo"]);}else{$dataEX9 = '';};
        if (!empty($_GET["trn"])){$dataEX10 =htmlspecialchars($_GET["trn"]);}else{$dataEX10 = '';};
        if (!empty($_GET["status"])){$dataEX11 =htmlspecialchars($_GET["status"]);}else{$dataEX11 = '';};

        ?>
        <a href="<?php echo '/apoteke-app/?m=business_trip&p=dajexcelFilter&kid='.$dataEX1.'&jmb='.$dataEX2.'&dod='.$dataEX3.'&ddo='.$dataEX4.'&dkod='.$dataEX5.'&dkdo='.$dataEX6.'&mjesto='.$dataEX7.'&akood='.$dataEX8.'&akodo='.$dataEX9.'&trn='.$dataEX10.'&status='.$dataEX11.''; ?>" style="  <?php if (isset($_GET['kid'])){echo "display: inline-block;";}else{echo "display: none;";}?>padding: 0 7px;line-height: 38px;width: 183px;text-align: center;color: #777777;
                margin: 69px 0 0px 16px;background: #fff;font-size: 15px;border-bottom: 1px solid #777777;"><i class="ion-document" title="Preuzmite excel nalog!" style="padding-right: 14px;"></i>Excell izvještaj</a>
    </div>
</div>
    <div class="col-sm-12" style="height: 10px; border-top: 0px solid white; margin-top: 14px;"></div>
    </div>

<!-- OVDJE ZAVRSAVAJU FILTERI -->
<?php
foreach($podaci as $podatak){
  $bool = false;
  if(isset($_GET["status"])){
    if($_GET["status"]==69){
      $bool = true;
    }
  }
  if (!empty($_GET["status"])){
      if ($_GET["status"]==1 and $podatak['canceled']) continue;
  }
  if (!$podatak['canceled'] and $bool) continue;

  //uposlenik
  $user = $db->query("SELECT TOP 1 * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id =".$podatak[1]);
  $user = $user->fetch();
  //historizacija
  $logsq = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] WHERE [sl_put_request_id] =".$podatak['sl_put_id']." order by id desc");
  $logs = $logsq->fetchAll();

  //kartica
  $color = 'black';
  if($podatak['operation'] == 'obrada' or $podatak['operation'] == 'odobravanje') {$color = 'green';}
  elseif ($podatak['operation'] == 'odbijanje') {$color = 'red';}
  if ($podatak['lock'] == 1 ) $color = 'grey';
  if ($podatak['canceled'] and $podatak['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE') $color = 'yellow';
?>
<div class="box box-lborder box-lborder-<?php echo $color ?>" style="font-family: 'Titillium Web', sans-serif;">

      <div class="content">
        <div class="row">
          <div class="col-sm-3">
            Zahtjev za službeno putovanje <b><?php echo $podatak['sl_put_id']; ?></b><br>
            Od: <b><?php echo date('d/m/Y',strtotime($podatak['pocetak_datum'])); ?></b> &nbsp;
            Do: <b><?php 
            if($podatak['kraj_datum2']){
              echo date('d/m/Y',strtotime($podatak['kraj_datum2']));
            }
            else echo date('d/m/Y',strtotime($podatak['kraj_datum']));   
            ?></b>
            <br> 
            <?php if ($podatak['canceled'] and $podatak['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){ ?>
              <span style="color:#ffcc00;"><i class="ion-alert"></i> <?php echo __('Otkazano'); ?></span> 
            <?php }else if ($podatak['lock'] == 1 ) {?>
              <span style="color:#000000;"><i class="ion-key"></i> <?php echo __('Zaključano'); ?></span> 
            <?php }else if($podatak['operation'] == 'obrada' or $podatak['operation'] == 'odobravanje') {?>
              <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Na obradi'); ?></span>
            <?php }elseif ($podatak['operation'] == 'odbijanje') { ?>
              <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Poslano na korekciju'); ?></span>
            <?php } ?>
    		
          </div>

          <div class="col-sm-4" style="height:80px;overflow:auto;">
          Status:<br>
           <?php
           foreach($logs as $log){
            $ko = $db->query("SELECT TOP 1 fname,lname FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id =".$log['user_id']);
            $ko = $ko->fetch();
             if($log['operation']=='odobravanje'){
                $span_style = "#009900;"; $image = "ion-android-checkmark-circle"; $text = "Poslao/la na obradu: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='odbijanje'){
                $span_style = "#990000;"; $image = "ion-android-edit"; $text = "Poslao/la na korekciju: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='zakljucavanje'){
                $span_style = "#000000;"; $image = "ion-key"; $text = "Zaključao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='odkljucavanje'){
               $span_style = "#1b00ff;"; $image = "ion-key"; $text = "Otključao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='snimanje'){
                $span_style = "#5c684a;"; $image = "ion-edit"; $text = "Ažurirao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
              }else if($log['operation']=='obrada'){
                $span_style = "#a17b0f;"; $image = "ion-android-done"; $text = "Ažurirao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
              }else if($log['operation']=='otkazivanje_satnica'){
                $span_style = "#ffcc00;"; $image = "ion-android-close"; $text = "Otkazao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
              } ?>
                <span style="font-family: 'Titillium Web', sans-serif;color:<?php echo $span_style; ?>;"><i class="<?php echo $image; ?>"></i> <?php echo $text; ?></span><br>
             <?php
           }
           ?>
          </div>

          <div class="col-sm-2">
            <b><?php echo $user['fname'].' '.$user['lname'] ?></b><br>
            <small><?php echo $user['position'] ?></small>
          </div>

          <div class="col-sm-1">
            Zahtjev kreiran:<br>
            <?php echo date('d/m/Y',$podatak['created_at']); ?>
          </div>

          <div class="col-sm-2 text-right">
          <?php if($podatak['lock'] != 1 or $admin){

            if(!$podatak['canceled'] and ($_user['employee_no'] == $podatak['employee_no'] or $_user['employee_no'] == $podatak['parent'] or $admin)){

              ?>

<a href="<?php echo '/apoteke-app/?m=default&p=novi_nalog&id='.$podatak['request_id'].'&status='.$podatak['sl_put_status'].'&sl_put_id='.$podatak['sl_put_id']; ?>"class="table-btn documents">Ažuriraj zahtjev</a>

<?php }}?>
<a href="<?php echo '/apoteke-app/?m=business_trip&p=dajexcel&id='.$podatak['request_id'].'&sl_put_id='.$podatak['sl_put_id']; ?>"class="table-btn documents">Putni nalog</a>

<a href="<?php echo '/apoteke-app/?m=default&p=novi_nalog&id='.$podatak['request_id'].'&status='.$podatak['status'].'&view=1&sl_put_id='.$podatak['sl_put_id']; ?>"class="table-btn documents">Pregled zahtjeva</a>

          
          <!-- <?php if($podatak['lock'] != 1 and $podatak['status_hr']!=1 and $podatak['na_obradi'] == 1){?>

            <a href="<?php echo $trenutni_link.'&odobri='.$podatak['request_id']; ?>" class="table-btn">Pošalji na obradu</a>
          
          <?php } ?> -->
          <?php if($admin and $podatak['lock'] != 1 and $podatak['status_hr']!=2 and $podatak['na_obradi'] == 1){?>

            <a onclick="postavi_link(this);" data-toggle="modal" data-target="#exampleModal" data-link="<?php echo $trenutni_link.'&odbij='.$podatak['sl_put_id']; ?>" class="table-btn documents">Pošalji na korekciju</a>
          
          <?php } ?>

          <?php if($admin and $podatak['lock'] != 1){?>

          <a href="<?php echo $trenutni_link.'&zakljucaj='.$podatak['sl_put_id']; ?>"class="table-btn documents">Zaključaj</a>

          <?php } ?>

          </div>
        </div>
      </div>
    </div>
<?php
}
?>
 </div>

 <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pošalji na korekciju</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <label style="padding:20px 0 0 20px">Komentar:</label>
      <form method="post" action="" id="korekcija_form">
      <div class="modal-body">
        <input type="text" placeholder="Unesite razlog slanja na korekciju" class="form-control" name="razlog">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
        <button style="background-color: #006595; color: white !important;"
        type="submit" class="btn"><b>Spasi</b></button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- ako nema rezultata -->
<?php 
 if(!isset($podatak)){
  echo ("<div class='h4 amke'>Trenutno nemate registrovanih zahtjeva za službeni put!</div>");
}
?>
<!-- paginacija -->
<?php
function dajpglink($n,$pageno,$trenutni_link){
    echo str_replace("&pg=$pageno","&pg=$n",$trenutni_link);
}

?>
<ul style="margin-left: 10% !important;" class="pagination">
    <li><a href="<?php dajpglink(1,$pageno,$trenutni_link);?>">Prva</a></li>
    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno <= 1){ echo '#'; } else { dajpglink($pageno-1,$pageno,$trenutni_link); } ?>">Prošla</a>
    </li>
    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { dajpglink($pageno+1,$pageno,$trenutni_link); } ?>">Sljedeća</a>
    </li>
    <li><a href="<?php dajpglink($total_pages,$pageno,$trenutni_link); ?>">Zadnja</a></li>
</ul>
</section>

  <?php

    include $_themeRoot.'/footer.php';

   ?>

</body>
</html>
<script>
function azuriraj_satnice(request, request_id,day,hour,status,status_hr){
  if(status_hr == 1){
    //postavljanje forme
  if (status == 73){hour=8}else{hour=4}
    $('#request').val(request);
    $('#request_id').val(request_id);
    $('#day').val(day);
    $('#hour').val(hour);
    $('#status').val(status);

    //ajax za regulisanje satnica
    $('.dialog-loader').show();
    $('.satnice_forma').ajaxSubmit({
      url:"<?php echo $url.'/modules/core/ajax/satnice.php'; ?>",
      type:"post",
      success: function(data){
          $('.dialog-loader').hide();
      }
    });
  }
}
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  alert("Kopirali ste tekst.");
}
</script>



<script type="text/javascript">
 $( document ).ready(function() {

  window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 2000);

  $(".filter_form_SL").select2();
//url generator atributa za filter
$('#dugme_filter').click(function(){
  var korisnikID = $('#korisnici_filter').val();
  var jmbfill = $('#jmbg_filter').val();
  var mjestoF = $('#mjesto_filter').val();
  var akontacija_od = $('#akonod').val();
  var akontacija_do = $('#akondo').val();
  var trn = $('#trn_filter').val();
  var status_filter = $('#status_filter').val();
  var datum_od = $('#dateod').datepicker({ dateFormat: 'dd-mm-yyyy' }).val();
  var datum_do = $('#datedo').datepicker({ dateFormat: 'dd-mm-yyyy' }).val();
  var datum_kreiranja_od = $('#date_kreiranja_od').datepicker({ dateFormat: 'dd-mm-yyyy' }).val();
  var datum_kreiranja_do = $('#date_kreiranja_do').datepicker({ dateFormat: 'dd-mm-yyyy' }).val();
            window.location = $(this).attr('current_url') + '&kid=' + korisnikID + '&jmb=' + jmbfill + '&dod=' + datum_od + '&ddo=' + datum_do + '&dkod=' + datum_kreiranja_od + '&dkdo=' + datum_kreiranja_do + '&mjesto=' + mjestoF + '&akood=' + akontacija_od + '&akodo=' + akontacija_do + '&trn=' + trn+ '&status=' + status_filter;
        });


// $('#and $podatak['lock']!=1').click(function(){
//             window.location = $(this).attr('current_url');
//         });



    });



$("#dateod").on('change', function (e) {
$("#datedo").datepicker("destroy");
$('#datedo').datepicker({
        //todayBtn: "linked",
        defaultViewDate: new Date('2017/05/01'),
    format: 'dd-mm-yyyy',
    language: 'bs',
        startDate: $("#dateod").val()
    //endDate: new Date(year + '/12/31')

    });
  let filtered_value = '<?php if(!empty($_GET['ddo'])) echo $_GET['ddo']; else echo -1; ?>';
if (filtered_value == -1){
  $("#datedo").datepicker( "setDate" , $("#dateod").val());
}
else{
  $("#datedo").datepicker( "setDate" , filtered_value);
}
});


$("#date_kreiranja_od").on('change', function (e) {
$("#date_kreiranja_do").datepicker("destroy");
$('#date_kreiranja_do').datepicker({
        //todayBtn: "linked",
        defaultViewDate: new Date('2017-05-01'),
    format: 'dd-mm-yyyy',
    language: 'bs',
        startDate: $("#date_kreiranja_od").val()
    //endDate: new Date(year + '/12/31')

    });
  
  let filtered_value = '<?php if(!empty($_GET['dkdo'])) echo $_GET['dkdo']; else echo -1; ?>';
if (filtered_value == -1){
  $("#date_kreiranja_do").datepicker( "setDate" , $("#date_kreiranja_od").val());
}
else{
  $("#date_kreiranja_do").datepicker( "setDate" , filtered_value);
}
});

    var today = new Date();
    var startDate = new Date();
    $('#dateod').datepicker({
        todayBtn: "linked",
        format: 'dd-mm-yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });
     $('#datedo').datepicker({
        todayBtn: "linked",
        format: 'dd-mm-yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });
     $('#date_kreiranja_od').datepicker({
        todayBtn: "linked",
        format: 'dd-mm-yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });

    $('#date_kreiranja_do').datepicker({
        todayBtn: "linked",
        format: 'dd-mm-yyyy',
        language: 'bs'
        //startDate: startDate,
        //endDate: new Date('2017/12/31')
    });
  

//izgled select fielda sa searchom

 function postavi_link(to){
    $('#korekcija_form').attr("action", to.getAttribute('data-link')); //Will set it
 }
</script>


