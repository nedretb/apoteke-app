<?php
  _pagePermission(4, false);

  $ts=time();$user=$_user['user_id'];
  date_default_timezone_set('Europe/Sarajevo');

  //da li se smiju slati mailovi


  //metod za azuriranje satnica
  function registruj_put($request_id,$db,$ponisti = false){
    $provjeraputa = $db->query("SELECT  request_id,status,pocetak_datum,kraj_datum FROM [c0_intranet2_apoteke].[dbo].[sl_put] where request_id =$request_id");
    $provjeraputa = $provjeraputa->fetch();

    //postavljanje varijabli u zavisnosti od odbijanja odobravanja
    if(!$ponisti){
      $status = $provjeraputa['status'];
      if($status == 73){$hour=8;}else{$hour=4;}
    }else{
      $status = 5;$hour = 8;
    }

    if($provjeraputa['pocetak_datum'] == $provjeraputa['kraj_datum']){
      //samo jedan dan
      $prijenos = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
      set status = $status, hour=$hour, corr_status = ".$provjeraputa['status']."
      where id =$request_id");
    }else{
      //vise dana
      $earlier = new DateTime($provjeraputa['pocetak_datum']);
      $later = new DateTime($provjeraputa['kraj_datum']);
      $diff = $later->diff($earlier)->format("%a");
      $request_id2 = $request_id + $diff;

      $prijenos = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
      set status = $status, hour=$hour, corr_status = ".$provjeraputa['status']."
      where id >= $request_id and id <= $request_id2 and weekday not in (6,7)");
    }
  }
  //metod za finalno registriranje satnica
  function odobri_put($request_id,$db){
    $provjeraputaqq = $db->query("SELECT  request_id,status,pocetak_datum,kraj_datum FROM [c0_intranet2_apoteke].[dbo].[sl_put] where request_id =$request_id");
    $provjeraputa = $provjeraputaqq->fetch();

    if($provjeraputa['pocetak_datum'] == $provjeraputa['kraj_datum']){
      //samo jedan dan
      $prijenos = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
      set review_status = 1,  status = ".$provjeraputa['status'].", corr_status = ".$provjeraputa['status']."
      where id =$request_id");
    }else{
      //vise dana
      $earlier = new DateTime($provjeraputa['pocetak_datum']);
      $later = new DateTime($provjeraputa['kraj_datum']);
      $diff = $later->diff($earlier)->format("%a");
      $request_id2 = $request_id + $diff;

      $prijenos = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[hourlyrate_day] 
      set review_status = 1,  status = ".$provjeraputa['status'].", corr_status = ".$provjeraputa['status']."
      where id >= $request_id and id <= $request_id2 and weekday not in (6,7)");
    }
  }
  //metod za slanje maileva
  function send_mails($podaci,$parent,$hr,$odbijanje=false){

    require 'lib/PHPMailer/PHPMailer.php';
    require 'lib/PHPMailer/SMTP.php';
    require 'lib/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = "UTF-8";

    $mail->IsSMTP();
    $mail->isHTML(true);  // Set email format to HTML

    $mail->Host = "barbbcom";
    $mail->Port = 25;

    $mail->setFrom($podaci['email_company'], "Obavijesti HR");
    $mail->addAddress($parent['email_company']);
    $mail->addAddress('racunovodstvo@raiffeisengroup.ba');
    if($hr){
      $mail->addAddress('hr.rbbh@raiffeisengroup.ba');
      if($podaci['polazna_drzava'] == 1){
        $mail->addAddress('ured.uprave.rbbh@raiffeisengroup.ba');
      }
      if($podaci['vrsta_transporta'] == 'Službeni automobil'){
        $mail->addAddress('vozni.park@raiffeisengroup.ba');
      }

      $odobreno ="ODOBRENO";
    } else if($odbijanje) $odobreno = 'ODBIJENO';
    else {$odobreno = null;}
 
    $mail->Subject = 'Registracija poslovnog putovanja';
    $mail->Body = "
    ".$podaci['fname']." ".$podaci['lname']." je prijavi(o)la novi zahtjev Registracija Poslovnog putovanja broj ".$podaci['id']." - $odobreno<br>
    Radnik:  ".$podaci['fname']." ".$podaci['lname']."<br>
    Org.jedinica:   ".$podaci['B_1_description']."      <br>
    Radno mjesto:   ".$podaci['position']."   <br>
    Datum zaposlenja u Banci:   ".date("d.m.Y",strtotime($podaci['employment_date']))."   <br>
    JMBG:   ".$podaci['JMB']."<br>
    Direktni nadredjeni:     ".$parent['fname']." ".$parent['lname']."    <br>
    Pocetni datum:  ".$podaci['pocetak_datum']."       <br>
    Krajnji datum:  ".$podaci['kraj_datum']."       <br>
    Svrha:  ".$podaci['svrha']."                        <br>
    Odredište: ".$podaci['odredisni_grad']."  (".$podaci['odredisna_drzava']." )      ".$podaci['pocetak_datum']." - ".$podaci['kraj_datum']."<br>
    Napomena/Naziv: ".$podaci['napomena']."<br>
    Viza/Pasoš:     ".$podaci['viza']."<br>
    Broj pasoša:     ".$podaci['dokument_broj']."<br>
    Akontacija iznos: ".$podaci['iznos_akontacije']."<br>
    Vrsta prevoza: ".$podaci['vrsta_transporta']."<br>
                            <br>
    Komentar: ".$podaci['transport_napomena']."<br>
    ".$podaci['akontacija_napomena']."<br>
    ".$podaci['transport_napomena']."<br>
    ".$podaci['osiguranje_napomena']."<br>
    ";

    if (!$mail->send()) {
      var_dump($mail->ErrorInfo);
    } 
  }
  //obrada
  if (isset($_POST['obrada'])){
    $odobri_req2 = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set na_obradi = 1 where request_id =".$_POST['id']);
    $odobri_req2->execute();

    $podaci_mail = $db->query("
    SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table1.id = ".$_POST['id']."
  ");
  $podaci_mail = $podaci_mail->fetch();

    $parentq = $db->query("SELECT email_company, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
    $parent = $parentq ->fetch();
    send_mails($podaci_mail,$parent,true);

    //logs
    $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (
        sl_put_request_id
    , operation
    , user_id
    , vrijeme
    )
    VALUES (
    '".$_POST['id']."',
    'obrada',
    ".$_user['user_id']." ,
    ".time()."
    )");
  }
//odobravanje
if(isset($_GET['odobri'])){
  $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sl_put] where status_hr = '1' and request_id =".$_GET['odobri']);
  foreach ($provjeraq as $one){
    $provjera = $one['aa'];
  }
  if ($one['aa']==0){
    $odobri_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set status_hr = 1 where request_id =".$_GET['odobri']);
    $odobri_req->execute();
    $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
    VALUES (".$_GET['odobri'].", 'odobravanje', $user, $ts)");
    //prijenos na satnicama
   registruj_put($_GET['odobri'],$db,false);

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
//odbijanje
if(isset($_GET['odbij'])){
  $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sl_put] where status_hr = '2' and request_id =".$_GET['odbij']);
  foreach ($provjeraq as $one){
    $provjera = $one['aa'];
  }
  if ($one['aa']==0){
      $odbij_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set status_hr = 2 where request_id =".$_GET['odbij']);
      $odbij_req->execute();
      $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
      VALUES (".$_GET['odbij'].", 'odbijanje', $user, $ts)");
      //prijenos na satnicama
      registruj_put($_GET['odbij'],$db,true);

      $podaci_mail = $db->query("
    SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table1.id = ".$_GET['odbij']."
  ");
  $podaci_mail = $podaci_mail->fetch();
  $parent = $db->query("SELECT email_company, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
    $parent = $parent ->fetch();
      send_mails($podaci_mail,$parent,false,true);
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
      $lock_req = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sl_put] set lock = 1 where request_id =".$_GET['zakljucaj']);
      $lock_req->execute();
      $insert_log = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
      VALUES (".$_GET['zakljucaj'].", 'zakljucavanje', $user, $ts)");
      // }

      //registracija na satnicama finalno
      odobri_put($_GET['zakljucaj'],$db);
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
$admin = $db->query("SELECT count(user_id) as br FROM [c0_intranet2_apoteke].[dbo].[users] where training_admin=1 and user_id=".$_user['user_id']);
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
$no_of_records_per_page = 10;
$offset = ($pageno-1) * $no_of_records_per_page;

//hvatanje podataka obicni i admin    --(izi izi tammam tamam)--
if (isset($admin)){
  $total_rowsq = $db->query("
  SELECT count(*)  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table2.na_obradi = 1 
  or table3.user_id= ".$_user['user_id']."
  or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table1.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id']."
  ");
  $total_rows = $total_rowsq->fetch();
  $total_rows = $total_rows[0];
  $total_pages = ceil($total_rows / $no_of_records_per_page);

  $podaci = $db->query("
  SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table2.na_obradi = 1 
  or table3.user_id= ".$_user['user_id']."
  or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table1.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id']."
   order by created_at desc 
   offset $offset rows
   FETCH NEXT $no_of_records_per_page rows only
  ");
} else {
  $podaci = $db->query("
  SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  where table3.user_id = ".$_user['user_id']." or ".$_user['employee_no']." in (parent,parent2) 
  or ".$_user['employee_no']." in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d)
  or ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)
  or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table1.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id']."
  order by created_at desc
  ");
}
//
function prebaciDatumBih($datum){
  if($datum!='' and $datum!=' '){
      $niz = explode('-',$datum);
  return ((int)$niz[2]).'.'.((int)$niz[1]).'.'.((int)$niz[0]);
}
else return null;
}

 ?>

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
          <?php echo __('Priprema zahtjeva za službeni put'); ?><br/><br/>
        </h2>
      </div>
    </div>
<?php
foreach($podaci as $podatak){
  //uposlenik
  $user = $db->query("SELECT TOP 1 * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id =".$podatak['user_id']);
  $user = $user->fetch();
  //historizacija
  $logsq = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] WHERE [sl_put_request_id] =".$podatak['request_id']." order by vrijeme desc");
  $logs = $logsq->fetchAll();

  //kartica
  if($podatak['status_hr']==1) {$color = 'green';}
  elseif ($podatak['status_hr']==2) {$color = 'red';}
  elseif ($podatak['status_hr']== 0) {$color = 'yellow';}
  if ($podatak['status_hr']== 0 and $podatak['na_obradi'] != 1) $color = 'grey';
?>
<div class="box box-lborder box-lborder-<?php echo $color ?>" style='font-family: 'Titillium Web', sans-serif;'>

      <div class="content">
        <div class="row">
          <div class="col-sm-3">
            Zahtjev za službeno putovanje<br>
            Od: <b><?php echo date('d/m/Y',strtotime($podatak['pocetak_datum'])); ?></b> &nbsp;
            Do: <b><?php echo date('d/m/Y',strtotime($podatak['kraj_datum']));   ?></b>
            <br> 
            <?php if($podatak['status_hr']==1) { ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
            <?php }else if( $podatak['status_hr']==2 ){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
              <?php }else if($podatak['status_hr']==0 and $podatak['na_obradi'] != 1) { ?>
               &nbsp; &nbsp; <span style="color:#747474;"><i class="ion-android-time"></i> <?php echo __('Nije poslano na obradu!'); ?></span> 
               <?php }else if($podatak['status_hr']==0) { ?>
               &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na odobrenju administratora Sektora Finansija...'); ?></span> <?php } ?>        		
          </div>

          <div class="col-sm-4">
          Status:<br>
           <?php
           foreach($logs as $log){
            $ko = $db->query("SELECT TOP 1 fname,lname FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id =".$log['user_id']);
            $ko = $ko->fetch();
             if($log['operation']=='odobravanje'){
                $span_style = "#009900;"; $image = "ion-android-checkmark-circle"; $text = "Odobrio/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='odbijanje'){
                $span_style = "#990000;"; $image = "ion-android-close"; $text = "Odbio/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='zakljucavanje'){
                $span_style = "#000000;"; $image = "ion-key"; $text = "Zaključao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='odkljucavanje'){
               $span_style = "#1b00ff;"; $image = "ion-key"; $text = "Otključao/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
             }else if($log['operation']=='snimanje'){
                $span_style = "#5c684a;"; $image = "ion-edit"; $text = "Spremio/la: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
              }else if($log['operation']=='obrada'){
                $span_style = "#a17b0f;"; $image = "ion-android-done"; $text = "Poslao/la na obradu: ".$ko['fname'].' '.$ko['lname']." - ".date("d.m.Y H:i:s", $log['vrijeme']);
              }  ?>
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
          <?php if($admin and $podatak['lock'] != 1){?>

            <a href="<?php echo $trenutni_link.'&zakljucaj='.$podatak['request_id']; ?>"class="table-btn"><i class="ion-key" title="Zaključaj/odključaj zahtjev"></i></a>
          
          <?php } ?>
          <?php if($admin and $podatak['lock'] != 1 and $podatak['status_hr']!=1 and $podatak['na_obradi'] == 1){?>

            <a href="<?php echo $trenutni_link.'&odobri='.$podatak['request_id']; ?>" class="table-btn"><i class="ion-android-checkmark-circle" title="Odobri zahtjev"></i></a>
          
          <?php } ?>
          <?php if($admin and $podatak['lock'] != 1 and $podatak['status_hr']!=2 and $podatak['na_obradi'] == 1){?>

            <a href="<?php echo $trenutni_link.'&odbij='.$podatak['request_id']; ?>" class="table-btn"><i class="ion-android-close" title="Odbij zahtjev"></i></a>
          
          <?php } ?>

          <a href="<?php echo '/apoteke-app/?m=default&p=novi_nalog&id='.$podatak['request_id'].'&status='.$podatak['status'].'&view=1'; ?>"class="table-btn"><i class="ion-eye" title="Pregled"></i></a>

            <a href="<?php echo '/apoteke-app/?m=business_trip&p=dajexcel&id='.$podatak['request_id']; ?>"class="table-btn"><i class="ion-document" title="Preuzmite excel nalog!"></i></a>
           
            <?php if($podatak['lock'] != 1){ ?>

            <a href="<?php echo '/apoteke-app/?m=default&p=novi_nalog&id='.$podatak['request_id'].'&status='.$podatak['status']; ?>"class="table-btn"><i class="ion-edit" title="Izmjene"></i></a>

            <?php }?>
          </div>
        </div>
      </div>
    </div>
<?php
}
?>
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
<ul class="pagination">
    <li><a href="<?php dajpglink(1,$pageno,$trenutni_link);?>">Prva</a></li>
    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno <= 1){ echo '#'; } else { dajpglink($pageno-1,$pageno,$trenutni_link); } ?>">Prošla</a>
    </li>
    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
        <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { dajpglink($pageno+1,$pageno,$trenutni_link); } ?>">Sljedeća</a>
    </li>
    <li><a href="<?php dajpglink($total_pages,$pageno,$trenutni_link); ?>">Zadnja</a></li>
</ul>
</section

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
</script>