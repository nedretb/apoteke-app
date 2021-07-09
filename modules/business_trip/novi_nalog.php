<?php
  _pagePermission([2,4], false);
  $user_id = _decrypt($_SESSION['SESSION_USER']);
  $ts=time();$user=$_user['user_id'];
  //admin ili korisnik provjera
$admin = $db->query("SELECT count(user_id) as br FROM [c0_intranet2_raiff].[dbo].[users] where sl_put_admin=1 and user_id=".$_user['user_id']);
$admin = $admin->fetch();

if ($admin['br']==1){
  $admin=true;
} 
else {$admin=false;}

  $canSendMail = $db->query("SELECT value
  FROM [c0_intranet2_raiff].[dbo].[settings]
  where name = 'hr_notifications'");
  $canSendMail = $canSendMail->fetch();

include 'lib/PHPMailer/PHPMailer.php';
    include 'lib/PHPMailer/SMTP.php';
    include 'lib/PHPMailer/Exception.php';


//metod za slanje maileva
  function send_mails($podaci,$parent,$hr,$odbijanje=false){

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = "UTF-8";

    $mail->IsSMTP();
    $mail->isHTML(true);  // Set email format to HTML

    $mail->Host = "mailgw.rbbh.ba";
    $mail->Port = 25;

    $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
    $mail->addAddress($podaci['email_company']);
    $mail->addAddress($parent['email_company']);
    $mail->addAddress('racunovodstvo@raiffeisengroup.ba');

    if($podaci['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
      if($podaci['name'] != 'Bosna i Hercegovina'){
        $mail->addAddress('raiffeisen_assistance@raiffeisengroup.ba');
        if(strtolower($podaci['osiguranje']) == 'da'){
          $mail->addAddress('Banko.uniqa@uniqa.ba');
        }
      }

      if($podaci['svrha'] == 'Edukacija/trening'){
        $mail->addAddress('hr.rbbh@raiffeisengroup.ba');
      }

      if($podaci['vrsta_transporta'] == 'Avion'){
        $mail->addAddress('ured.uprave.rbbh@raiffeisengroup.ba');
      }

      if($podaci['vrsta_transporta'] == 'Službeno vozilo' or $podaci['vrsta_transporta'] == 'Službeno vozilo sa vozačem'){
        // $mail->addAddress('vozni.park@raiffeisengroup.ba');
      }
    }

    
    $mail->Subject = 'Registracija poslovnog putovanja';
    $mail->Body = "<style>body{font-family: Arial,Verdana,Segoe,sans-serif;font-size:12px;line-height: 200%;}</style>
    <body>
    <b>".$podaci['fname']." ".$podaci['lname']."</b> je prijavi(o)la novi zahtjev Registracija Poslovnog putovanja broj <b>".$podaci['id']." </b><br>
    Radnik:  <b>".$podaci['fname']." ".$podaci['lname']."</b><br>
    Org.jedinica:   <b>".$podaci['B_1_description']."      </b><br>
    Radno mjesto:   <b>".$podaci['position']."   </b><br>
    Datum zaposlenja u Banci:   <b>".date("d.m.Y",strtotime($podaci['employment_date']))."</b><br>
    JMBG:   <b>".$podaci['JMB']."</b><br>
    Direktni nadređeni: <b>".$parent['fname']." ".$parent['lname']."    </b><br>
    Pocetni datum:  <b>".date("d.m.Y",strtotime($podaci['pocetak_datum']))."       </b><br>
    Krajnji datum:  <b>".date("d.m.Y",strtotime($podaci['kraj_datum2'] ? $podaci['kraj_datum2'] : $podaci['kraj_datum']))."</b><br>
    Svrha:  <b>".$podaci['svrha']."</b><br>
    Odredište: <b>".$podaci['odredisni_grad']." - ".$podaci['odredisni_grad2']."</b><br>
    Razlog putovanja: <b>".$podaci['razlog_putovanja']."</b><br>
    Napomena: <b>".$podaci['napomena']."</b><br>
    Osiguranje: <b>".$podaci['osiguranje']. "</b><br>
    Viza potrebna: <b>".$podaci['viza']."</b>   Broj pasoša: <b>".$podaci['dokument_broj']."</b> &nbsp &nbsp &nbsp    Napomena:<b>".$podaci['osiguranje_napomena']."</b><br>
    Akontacija iznos:<b> ".$podaci['iznos_akontacije']."</b> &nbsp &nbsp &nbsp   Napomena:<b>".$podaci['akontacija_napomena']."</b><br>
    Sredstvo transporta: <b>".$podaci['vrsta_transporta']."</b> &nbsp &nbsp &nbsp    Napomena:<b>".$podaci['transport_napomena']."</b><br>
    Smještaj napomena: <b>".$podaci['smjestaj_napomena']."</b>
    
    <br>
    <br>
    </body>
    ";

  if($canSendMail)
    if (!$mail->send()) {
      var_dump($mail->ErrorInfo);
    }
  }

// Registracija Sl put vise dana -> reg se 1 dan u biti
// trazenje id a od pocetnog dana
if (isset($_GET['dateOD']) and isset($_GET['dateDO'])){
    $date_OD = $_GET['dateOD'];
    $date_DO = $_GET['dateDO'];
    
    $djelovi_datuma = explode('.', $date_OD);
    $godina_poc = (int) $djelovi_datuma[2];
    $mjesec_poc = (int) $djelovi_datuma[1];
    $dan_poc    = (int) $djelovi_datuma[0];

    $year_id = $db->query("SELECT TOP 1 id FROM [c0_intranet2_raiff].[dbo].[hourlyrate_year] where year = $godina_poc and user_id = $user_id");
    $year_id = $year_id->fetch();
    $year_id = $year_id['id'];

    $get_id = $db->query("SELECT top 1 id from [c0_intranet2_raiff].[dbo].[hourlyrate_day] where day = $dan_poc and month_id = $mjesec_poc and year_id = $year_id");
    $get_id = $get_id->fetch();
    $_GET['id'] = $get_id['id'];
}
// 73 normalni sl put, 81 < 4

//Planiranje sl puta
if($_POST){
  // new check dates and send errors ako nije avl automobil u sl svrhe

    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $req = $_POST['request_id'];
    $get_sl_put['status'] = $_POST['status'];
    $get_sl_put['request_id'] = $_POST['request_id'];
    $get_sl_put['pocetak_datum'] = $_POST['pocetak_datum'];
    $get_sl_put['kraj_datum'] = $_POST['kraj_datum'];
    $get_sl_put['kraj_datum2'] = $_POST['kraj_datum2'];
    $begin_date = date("Y-m-d", strtotime($_POST['pocetak_datum']));
    if(strlen($_POST['kraj_datum2'])!= 0) {
      $end_date = date("Y-m-d", strtotime($_POST['kraj_datum2']));
    }  else $end_date = date("Y-m-d", strtotime($_POST['kraj_datum']));
    
    //new sl put
    if(empty($_POST['sl_put_id'])){

      if(isset($_GET['get_year'])){
        $check_status = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] as h
inner join [c0_intranet2_raiff].[dbo].[hourlyrate_year] as y on y.user_id = h.user_id
where y.id = ".$_GET['get_year']." and Date >='".$begin_date."' and  Date <='".$end_date."' and 
employee_no = ".$_POST['employee_no']." and (status not in (83, 5) and corr_status not in (83, 5) or change_req = 1)");
      $check_status = $check_status->fetch();

      }else{
       $check_status = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] where Date >='".$begin_date."' and  Date <='".$end_date."' and 
employee_no = ".$_POST['employee_no']." and (status not in (83, 5) and corr_status not in (83, 5) or change_req = 1)");
      $check_status = $check_status->fetch(); 
      }
      //status

      if($check_status['c'] != 0 and $_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
        $_SESSION['datumi_err'] = 1;
        header("Location: ".$actual_link); 
        exit();
      }

      //check korekcije editable
      $time_poc = strtotime(date('Y-m-1'));
      $time_kraj = strtotime(date('Y-m-t'));
      $check_editable['c'] =0;
      if($time_poc > strtotime($begin_date)){
        $check_editable = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] where Date >='".$begin_date."' and  Date <='".$end_date."' and 
employee_no = ".$_POST['employee_no']." and editable_corrections ='N' ");
      $check_editable = $check_editable->fetch();

      }else {
        //check satnice editable
        $check_editable = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] where Date >='".$begin_date."' and  Date <='".$end_date."' and 
employee_no = ".$_POST['employee_no']." and editable ='N' ");
      $check_editable = $check_editable->fetch();
      }

        if($check_editable['c'] != 0 and $_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
          $_SESSION['zakljucano_err'] = 1;
          header("Location: ".$actual_link); 
          exit();
        }
      
    }else{
      //editing sl put
      $get_sl_put = $db->query("SELECT id,sp.status,sp.request_id,sp.pocetak_datum,sp.kraj_datum,sp.kraj_datum2 FROM [c0_intranet2_raiff].[dbo].[sl_put] as sp where sp.id='".$_POST['sl_put_id']."'");
      $get_sl_put = $get_sl_put->fetch();
      $begin_date_old = date("Y-m-d", strtotime($get_sl_put['pocetak_datum']));
      if(strlen($get_sl_put['kraj_datum2'])!= 0) {
        $end_date_old = date("Y-m-d", strtotime($get_sl_put['kraj_datum2']));
      }  else $end_date_old = date("Y-m-d", strtotime($get_sl_put['kraj_datum']));

      //check status
        $check_status = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
          where Date >='".$begin_date."' and  Date <='".$end_date."' and 
  employee_no = ".$_POST['employee_no']." and (status not in (83, 5) and corr_status not in (83, 5) or change_req = 1) and id not in (
   SELECT id FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
   where Date >='".$begin_date_old."' and Date <='".$end_date_old."' 
   and employee_no = ".$_POST['employee_no'].")");
        $check_status = $check_status->fetch();

        if($check_status['c'] != 0 and $_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
          $_SESSION['datumi_err'] = 1;
          header("Location: ".$actual_link); 
          exit();
        }
      //check editable
      $time_poc = strtotime(date('Y-m-1'));
      $time_kraj = strtotime(date('Y-m-t'));
      $check_editable['c'] =0;
      if($time_poc > strtotime($begin_date)){
        $check_editable = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
                  where Date >='".$begin_date."' and  Date <='".$end_date."' and 
          employee_no = ".$_POST['employee_no']." and editable_corrections = 'N' and id not in (
           SELECT id FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
           where Date >='".$begin_date_old."' and Date <='".$end_date_old."' 
           and employee_no = ".$_POST['employee_no'].")");
        $check_editable = $check_editable->fetch();
      }else{
        $check_editable = $db->query("SELECT count(*) as c FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
                  where Date >='".$begin_date."' and  Date <='".$end_date."' and 
          employee_no = ".$_POST['employee_no']." and editable = 'N' and id not in (
           SELECT id FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] 
           where Date >='".$begin_date_old."' and Date <='".$end_date_old."' 
           and employee_no = ".$_POST['employee_no'].")");
        $check_editable = $check_editable->fetch();
      }

        

        if($check_editable['c'] != 0 and $_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
          $_SESSION['zakljucano_err'] = 1;
          header("Location: ".$actual_link); 
          exit();
        }
      
    }

  //end check


  //provjera satnica

  if(strtotime($get_sl_put['pocetak_datum']) != strtotime($_POST['pocetak_datum']) or strtotime($get_sl_put['kraj_datum']) != strtotime($_POST['kraj_datum']) or strtotime($get_sl_put['kraj_datum2']) != strtotime($_POST['kraj_datum2']) or empty($_POST['sl_put_id']) or $get_sl_put['svrha'] != $_POST['svrha']){
    
    //da li se moze mjenjati ista
    $datum_pocetka = date("Y-m-d", strtotime($_POST['pocetak_datum']));
    
    if(strlen($_POST['kraj_datum2'])!= 0) {
      $datum_kraja = date("Y-m-d", strtotime($_POST['kraj_datum2']));
    }else $datum_kraja = date("Y-m-d", strtotime($_POST['kraj_datum']));

    $req1 = $get_sl_put['request_id'] - 200; 
    $req2 = $get_sl_put['request_id'] + 200;

      //registracija sl puta
      //na satnice
      if ($_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
        //hvatanje i brisajne prethodnog sl puta
        $prethodni_pocetak = date("Y-m-d", strtotime($get_sl_put['pocetak_datum']));
        if(strlen($get_sl_put['kraj_datum2'])!= 0) {
          $prethodni_kraj = date("Y-m-d", strtotime($get_sl_put['kraj_datum2']));
        }else $prethodni_kraj = date("Y-m-d", strtotime($get_sl_put['kraj_datum']));

        if(!empty($_POST['sl_put_id'])){
          $prethodni_put = $db->query("
          declare @k integer
          set @k = 0
          
          UPDATE [c0_intranet2_raiff].[dbo].[hourlyrate_day] SET
          @k =   case when ([KindofDay] = 'BHOLIDAY' and status != '83' and review_status = '0') then 1
                  when ([KindofDay] != 'BHOLIDAY' and status != '5' and review_status = '0') then 1
                  else 0 end,
          
          
          status = case when ([KindofDay] = 'BHOLIDAY' and status != '83' and review_status = '0') then 83
                  when ([KindofDay] != 'BHOLIDAY' and status != '5' and review_status = '0') then 5
                  else [status] end,
                  
          corr_status = case when ([KindofDay] = 'BHOLIDAY' and corr_status != '83' and corr_review_status = '0') then 83
          else [corr_status] end,
          
          corr_review_status = case when ([KindofDay] = 'BHOLIDAY' and @k = 1) then '1' 
                     when ([KindofDay] <> 'BHOLIDAY' and @k = 1) then '0'
                     else [corr_review_status] end,
                  
          review_status = case when ([KindofDay] = 'BHOLIDAY' and @k = 1) then '1' 
                     when ([KindofDay] <> 'BHOLIDAY' and @k = 1) then '0'
                     else [review_status] end,
          
          timest_edit = '".date('Y-m-d h:i:s')."',
            employee_comment = case when @k = 0 then '' else [employee_comment] end
            
            where Date >='$prethodni_pocetak' and  Date <='$prethodni_kraj' and  employee_no = ".$_POST['employee_no']);
            $prethodni_put = $prethodni_put->execute();
        }
        

        // registrovanje sl puta
        $prvi_datum= date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $novi_put = $db->query("UPDATE [c0_intranet2_raiff].[dbo].[hourlyrate_day] set 
          status = 
          case 
            when Date >= '".$prvi_datum."'  then ".$get_sl_put['status']."
            else status
          end ,
          timest_edit_corr  = '".date('Y-m-d h:i:s')."',
          employee_timest_edit= ".$_user['employee_no'].",
          timest_edit= 
          case 
            when Date >= '".$prvi_datum."'  then '".date('Y-m-d h:i:s')."'
            else timest_edit
          end ,
          corr_status = ".$get_sl_put['status'].",
          hour_pre = null,
          Description = '',
          review_status = 0,
          corr_review_status = 0
          where Date >='$datum_pocetka' and  Date <='$datum_kraja' and employee_no = ".$_POST['employee_no']);
        $novi_put = $novi_put->execute();
      }
  }

  if(true){
    try {
      $ts = time();      
    
    if (empty($_GET['sl_put_id'])){
  $data = $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[sl_put] ([request_id],[status],[svrha],[pocetak_datum],[pocetak_vrijeme],[kraj_datum],[kraj_vrijeme],[polazna_drzava],[grad_polaska],[odredisna_drzava],[odredisni_grad],[odredisna_drzava2],[odredisni_grad2],[odredisna_drzava3],[odredisni_grad3],[razlog_putovanja],[napomena],[iznos_akontacije],[valuta],[datum_akontacije],[primanje_sredstva],[akontacija_napomena],[vrsta_transporta],[transport_pocetak_datum],[transport_pocetak_vrijeme],[transport_kraj_datum],[transport_kraj_vrijeme],[transport_polazna_drzava],[transport_grad_polaska],[transport_odredisna_drzava],[transport_odredisni_grad],[transport_napomena],[vrsta_smjestaja],[smjestaj_pocetak_datum],[smjestaj_pocetak_vrijeme],[smjestaj_kraj_datum],[smjestaj_kraj_vrijeme],[smjestaj_drzava],[smjestaj_grad],[smjestaj_adresa],[osiguranje],[osiguranje_pocetak_datum],[osiguranje_pocetak_vrijeme],[osiguranje_kraj_datum],[osiguranje_kraj_vrijeme],[dokument_broj],[viza],[osiguranje_napomena],[lock],[status_hr],[created_at],kraj_datum2,kraj_vrijeme2,kategorija_hotela,placeno_biznis_karticom,za_isplatu_povrat,smjestaj_napomena,employee)
    VALUES ("."'".
     $_POST['request_id'] ."','".
     $_POST['status'] ."','".
     $_POST['svrha'] ."','".
     prebaciDatumStandard($_POST['pocetak_datum']) ."','".
     $_POST['pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['kraj_datum']) ."','".
     $_POST['kraj_vrijeme'] ."','".
     $_POST['polazna_drzava'] ."','".
     $_POST['grad_polaska'] ."','".
     $_POST['odredisna_drzava'] ."','".
     $_POST['odredisni_grad'] ."','".
     $_POST['odredisna_drzava2'] ."','".
     $_POST['odredisni_grad2'] ."','".
     $_POST['odredisna_drzava3'] ."','".
     $_POST['odredisni_grad3'] ."','".
     $_POST['razlog_putovanja'] ."','".
     $_POST['napomena'] ."','".
     $_POST['iznos_akontacije'] ."','".
     $_POST['valuta'] ."','".
     prebaciDatumStandard($_POST['datum_akontacije']) ."','".
     $_POST['primanje_sredstva'] ."','".
     $_POST['akontacija_napomena'] ."','".
     $_POST['vrsta_transporta'] ."','".
     prebaciDatumStandard($_POST['transport_pocetak_datum']) ."','".
     $_POST['transport_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['transport_kraj_datum']) ."','".
     $_POST['transport_kraj_vrijeme'] ."','".
     $_POST['transport_polazna_drzava'] ."','".
     $_POST['transport_grad_polaska'] ."','".
     $_POST['transport_odredisna_drzava'] ."','".
     $_POST['transport_odredisni_grad'] ."','".
     $_POST['transport_napomena'] ."','".
     $_POST['vrsta_smjestaja'] ."','".
     prebaciDatumStandard($_POST['smjestaj_pocetak_datum']) ."','".
     $_POST['smjestaj_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['smjestaj_kraj_datum']) ."','".
     $_POST['smjestaj_kraj_vrijeme'] ."','".
     $_POST['smjestaj_drzava'] ."','".
     $_POST['smjestaj_grad'] ."','".
     $_POST['smjestaj_adresa'] ."','".
     $_POST['osiguranje'] ."','".
     prebaciDatumStandard($_POST['osiguranje_pocetak_datum']) ."','".
     $_POST['osiguranje_pocetak_vrijeme'] ."','".
     prebaciDatumStandard($_POST['osiguranje_kraj_datum']) ."','".
     $_POST['osiguranje_kraj_vrijeme'] ."','".
     $_POST['dokument_broj'] ."','".
     $_POST['viza'] ."','".
    $_POST['osiguranje_napomena'] ."',
    0 ,
    0 ,
    $ts,
    '".prebaciDatumStandard($_POST['kraj_datum2']) ."',
        '".$_POST['kraj_vrijeme2'] ."',
    '".$_POST['kategorija_hotela']."',
    '".$_POST['placeno_biznis_karticom']."',
    '".$_POST['za_isplatu_povrat']."',
    '".$_POST['smjestaj_napomena']."',
    '".$_POST['employee_no']."'
    )");

$data_ostalo = $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[sl_put_ostali_info] (
        [request_id],
        [trosak1],
        [trosak2],
        [kolicina1],
        [kolicina2],
        [iznos1],
        [iznos2],
        [ost_trosak1],
        [ost_trosak2],
        [ost_trosak3],
        [ost_trosak4],
        [ost_trosak5],
        [ost_trosak6],
        [ost_kolicina1],
        [ost_kolicina2],
        [ost_kolicina3],
        [ost_kolicina4],
        [ost_kolicina5],
        [ost_kolicina6],
        [ost_iznos1],
        [ost_iznos2],
        [ost_iznos3],
        [ost_iznos4],
        [ost_iznos5],
        [ost_iznos6],
        [kol_gorivo],
        [iznos_gorivo],
        [izdaci_naziv1],
        [izdaci_naziv2],
        [izdaci_naziv3],
        [izdaci_kol1],
        [izdaci_kol2],
        [izdaci_kol3],
        [izdaci_iznos1],
        [izdaci_iznos2],
        [izdaci_iznos3],
        [ost_kratkiopis],
        [dacheck],
        [necheck],
        [ost_specopis],
        [sl_put_id_fk]
        )VALUES(
        '".$_POST['request_id'] ."',
        '".$_POST['trosak1'] ."',
        '".$_POST['trosak2'] ."',
        '".$_POST['kolicina1'] ."',
        '".$_POST['kolicina2'] ."',
        '".$_POST['iznos1'] ."',
        '".$_POST['iznos2'] ."',
        '".$_POST['ost_trosak1'] ."',
        '".$_POST['ost_trosak2'] ."',
        '".$_POST['ost_trosak3'] ."',
        '".$_POST['ost_trosak4'] ."',
        '".$_POST['ost_trosak5'] ."',
        '".$_POST['ost_trosak6'] ."',
        '".$_POST['ost_kolicina1'] ."',
        '".$_POST['ost_kolicina2'] ."',
        '".$_POST['ost_kolicina3'] ."',
        '".$_POST['ost_kolicina4'] ."',
        '".$_POST['ost_kolicina5'] ."',
        '".$_POST['ost_kolicina6'] ."',
        '".$_POST['ost_iznos1'] ."',
        '".$_POST['ost_iznos2'] ."',
        '".$_POST['ost_iznos3'] ."',
        '".$_POST['ost_iznos4'] ."',
        '".$_POST['ost_iznos5'] ."',
        '".$_POST['ost_iznos6'] ."',
        '".$_POST['kol_gorivo'] ."',
        '".$_POST['iznos_gorivo'] ."',
        '".$_POST['izdaci_naziv1'] ."',
        '".$_POST['izdaci_naziv2'] ."',
        '".$_POST['izdaci_naziv3'] ."',
        '".$_POST['izdaci_kol1'] ."',
        '".$_POST['izdaci_kol2'] ."',
        '".$_POST['izdaci_kol3'] ."',
        '".$_POST['izdaci_iznos1'] ."',
        '".$_POST['izdaci_iznos2'] ."',
        '".$_POST['izdaci_iznos3'] ."',
        '".$_POST['ost_kratkiopis'] ."',
        '".$_POST['dacheck'] ."',
        '".$_POST['necheck'] ."',
        '".$_POST['ost_specopis'] ."',
        ".$db->lastInsertId()."
        )");
        $sl_put_last_id = $db->lastInsertId();
    } else {
      $data_before_updateq = $db->query("select * from [c0_intranet2_raiff].[dbo].[sl_put] as s join [c0_intranet2_raiff].[dbo].[sl_put_ostali_info] as i on i.sl_put_id_fk = s.id where id =".$_POST['sl_put_id']);
      $data_before_update = $data_before_updateq->fetch(PDO::FETCH_ASSOC);
       $data_ostalo = $db->query("UPDATE [c0_intranet2_raiff].[dbo].[sl_put_ostali_info] SET
        [request_id]='".$_POST['request_id'] ."',
        [trosak1]='".$_POST['trosak1'] ."',
        [trosak2]='".$_POST['trosak2'] ."',
        [kolicina1]='".$_POST['kolicina1'] ."',
        [kolicina2]='".$_POST['kolicina2'] ."',
        [iznos1]='".$_POST['iznos1'] ."',
        [iznos2]='".$_POST['iznos2'] ."',
        [ost_trosak1]='".$_POST['ost_trosak1'] ."',
        [ost_trosak2]='".$_POST['ost_trosak2'] ."',
        [ost_trosak3]='".$_POST['ost_trosak3'] ."',
        [ost_trosak4]='".$_POST['ost_trosak4'] ."',
        [ost_trosak5]='".$_POST['ost_trosak5'] ."',
        [ost_trosak6]='".$_POST['ost_trosak6'] ."',
        [ost_kolicina1]='".$_POST['ost_kolicina1'] ."',
        [ost_kolicina2]='".$_POST['ost_kolicina2'] ."',
        [ost_kolicina3]='".$_POST['ost_kolicina3'] ."',
        [ost_kolicina4]='".$_POST['ost_kolicina4'] ."',
        [ost_kolicina5]='".$_POST['ost_kolicina5'] ."',
        [ost_kolicina6]='".$_POST['ost_kolicina6'] ."',
        [ost_iznos1]='".$_POST['ost_iznos1'] ."',
        [ost_iznos2]='".$_POST['ost_iznos2'] ."',
        [ost_iznos3]='".$_POST['ost_iznos3'] ."',
        [ost_iznos4]='".$_POST['ost_iznos4'] ."',
        [ost_iznos5]='".$_POST['ost_iznos5'] ."',
        [ost_iznos6]='".$_POST['ost_iznos6'] ."',
        [kol_gorivo]='".$_POST['kol_gorivo'] ."',
        [iznos_gorivo]='".$_POST['iznos_gorivo'] ."',
        [izdaci_naziv1]='".$_POST['izdaci_naziv1'] ."',
        [izdaci_naziv2]='".$_POST['izdaci_naziv2'] ."',
        [izdaci_naziv3]='".$_POST['izdaci_naziv3'] ."',
        [izdaci_kol1]='".$_POST['izdaci_kol1'] ."',
        [izdaci_kol2]='".$_POST['izdaci_kol2'] ."',
        [izdaci_kol3]='".$_POST['izdaci_kol3'] ."',
        [izdaci_iznos1]='".$_POST['izdaci_iznos1'] ."',
        [izdaci_iznos2]='".$_POST['izdaci_iznos2'] ."',
        [izdaci_iznos3]='".$_POST['izdaci_iznos3'] ."',
        [ost_kratkiopis]='".$_POST['ost_kratkiopis'] ."',
        [dacheck]='".$_POST['dacheck'] ."',
        [necheck]='".$_POST['necheck'] ."',
        [ost_specopis]='".$_POST['ost_specopis'] ."'
        WHERE sl_put_id_fk ='".$_POST['sl_put_id'] ."'
        ");
        
        $data = $db->query("UPDATE [c0_intranet2_raiff].[dbo].[sl_put] SET 
            [request_id] ='".$_POST['request_id'] ."'
           ,[status]='".$_POST['status'] ."'
           ,[svrha]='".$_POST['svrha'] ."'
           ,[pocetak_datum]='".prebaciDatumStandard($_POST['pocetak_datum']) ."'
           ,[pocetak_vrijeme]='".$_POST['pocetak_vrijeme'] ."'
           ,[kraj_datum]='".prebaciDatumStandard($_POST['kraj_datum']) ."'
           ,[kraj_vrijeme]='".$_POST['kraj_vrijeme'] ."'
           ,[polazna_drzava]='".$_POST['polazna_drzava'] ."'
           ,[grad_polaska]='".$_POST['grad_polaska'] ."'
           ,[odredisna_drzava]='".$_POST['odredisna_drzava'] ."'
           ,[odredisni_grad]='".$_POST['odredisni_grad'] ."'
           ,[odredisna_drzava2]='".$_POST['odredisna_drzava2'] ."'
           ,[odredisni_grad2]='".$_POST['odredisni_grad2'] ."'
           ,[odredisna_drzava3]='".$_POST['odredisna_drzava3'] ."'
           ,[odredisni_grad3]='".$_POST['odredisni_grad3'] ."'
           ,[razlog_putovanja]='".$_POST['razlog_putovanja'] ."'
           ,[napomena]='".$_POST['napomena'] ."'
           ,[iznos_akontacije]='".$_POST['iznos_akontacije'] ."'
           ,[valuta]='".$_POST['valuta'] ."'
           ,[datum_akontacije]='".prebaciDatumStandard($_POST['datum_akontacije']) ."'
           ,[primanje_sredstva]='".$_POST['primanje_sredstva'] ."'
           ,[akontacija_napomena]='".$_POST['akontacija_napomena'] ."'
           ,[vrsta_transporta]='".$_POST['vrsta_transporta'] ."'
           ,[transport_pocetak_datum]='".prebaciDatumStandard($_POST['transport_pocetak_datum']) ."'
           ,[transport_pocetak_vrijeme]='".$_POST['transport_pocetak_vrijeme'] ."'
           ,[transport_kraj_datum]='".prebaciDatumStandard($_POST['transport_kraj_datum'])."'
           ,[transport_kraj_vrijeme]='".$_POST['transport_kraj_vrijeme'] ."'
           ,[transport_polazna_drzava]='".$_POST['transport_polazna_drzava'] ."'
           ,[transport_grad_polaska]='".$_POST['transport_grad_polaska'] ."'
           ,[transport_odredisna_drzava]='".$_POST['transport_odredisna_drzava'] ."'
           ,[transport_odredisni_grad]='".$_POST['transport_odredisni_grad'] ."'
           ,[transport_napomena]='".$_POST['transport_napomena'] ."'
           ,[vrsta_smjestaja]='".$_POST['vrsta_smjestaja'] ."'
           ,[smjestaj_pocetak_datum]='".prebaciDatumStandard($_POST['smjestaj_pocetak_datum'])."'
           ,[smjestaj_pocetak_vrijeme]='".$_POST['smjestaj_pocetak_vrijeme'] ."'
           ,[smjestaj_kraj_datum]='".prebaciDatumStandard($_POST['smjestaj_kraj_datum'])."'
           ,[smjestaj_kraj_vrijeme]='".$_POST['smjestaj_kraj_vrijeme'] ."'
           ,[smjestaj_drzava]='".$_POST['smjestaj_drzava'] ."'
           ,[smjestaj_grad]='".$_POST['smjestaj_grad'] ."'
           ,[smjestaj_adresa]='".$_POST['smjestaj_adresa'] ."'
           ,[osiguranje]='".$_POST['osiguranje'] ."'
           ,[osiguranje_pocetak_datum]='".prebaciDatumStandard($_POST['osiguranje_pocetak_datum'])."'
           ,[osiguranje_pocetak_vrijeme]='".$_POST['osiguranje_pocetak_vrijeme'] ."'
           ,[osiguranje_kraj_datum]='".prebaciDatumStandard($_POST['osiguranje_kraj_datum'])."'
           ,[osiguranje_kraj_vrijeme]='".$_POST['osiguranje_kraj_vrijeme'] ."'
           ,[dokument_broj]='".$_POST['dokument_broj'] ."'
           ,[viza]='".$_POST['viza'] ."'
           ,[osiguranje_napomena]='".$_POST['osiguranje_napomena'] ."'
           ,[kategorija_hotela]= '".$_POST['kategorija_hotela']."'
           ,[placeno_biznis_karticom]= '".$_POST['placeno_biznis_karticom']."'
           ,[za_isplatu_povrat]= '".$_POST['za_isplatu_povrat']."'
           ,[kraj_datum2]= '".prebaciDatumStandard($_POST['kraj_datum2'])."'
           ,[kraj_vrijeme2]= '".$_POST['kraj_vrijeme2']."'
           ,[smjestaj_napomena]= '".$_POST['smjestaj_napomena']."'
        WHERE id =" .$_GET['sl_put_id']
           );

//nova notifikacija finansije
if($admin){

  $diff=[];

  foreach($data_before_update as $key => $value){
    if($_POST[$key] != $value and !strpos($key, 'datum') and !in_array($key, ['lock','status_hr','created_at','na_obradi','id','employee', 'datum_akontacije', 'sl_put_id_fk'])) {
      array_push($diff, [$key => $_POST[$key] ]);
    }
  }

$changed = 'Polje - Vrijednost <br>';
  foreach ($diff as $key => $value) {
    $changed .= key ($value)." - ".$value[key ($value)]."<br>";
  }

  $podaci_mail = $db->query("
    SELECT *  FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] as table1
    INNER JOIN [c0_intranet2_raiff].[dbo].[sl_put] as table2
    ON table1.id = table2.request_id 
    inner join [c0_intranet2_raiff].[dbo].[users] as table3
    ON table1.user_id = table3.user_id
    where table2.id = ".$_GET['sl_put_id']."
    ");
    $podaci_mail = $podaci_mail->fetch();


    $parentq = $db->query("SELECT email_company, fname, lname from [c0_intranet2_raiff].[dbo].[users] WHERE employee_no = ".$podaci_mail['parentMBO2']." ");
    $parent = $parentq ->fetch(); 

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = "UTF-8";

    $mail->IsSMTP();
    $mail->isHTML(true);  // Set email format to HTML

    $mail->Host = "mailgw.rbbh.ba";
    $mail->Port = 25;

    $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");

    $mail->addAddress('racunovodstvo@raiffeisengroup.ba');
    $mail->addAddress($podaci_mail['email_company']);

    $mail->Subject = 'Izmjena naloga službenog puta broj - '.$_GET['sl_put_id'];
    $mail->Body = "Poštovani,<br>
    Nalog je izmjenjen od strane administratora finansija.<br>
    Molimo vas da pogledate Vaš putni nalog. U slučaju nejasnoća obratite se Računovodstvu. <br><br><br>
    $changed
    ";

  if($canSendMail)
    if (!$mail->send()) {
        var_dump($mail->ErrorInfo);
    }
    
}



//

  //notifikacija o promjeni sl puta
if(
strtotime($data_before_update['pocetak_datum']) != strtotime($_POST['pocetak_datum']) or
strtotime($data_before_update['kraj_datum2']) != strtotime($_POST['kraj_datum2']) or
strtotime($data_before_update['kraj_datum']) != strtotime($_POST['kraj_datum']) or
$data_before_update['svrha'] != $_POST['svrha'] or
$data_before_update['odredisni_grad'] != $_POST['odredisni_grad'] or
$data_before_update['odredisni_grad2'] != $_POST['odredisni_grad2'] or
$data_before_update['razlog_putovanja'] != $_POST['razlog_putovanja'] or
$data_before_update['napomena'] != $_POST['napomena'] or
$data_before_update['osiguranje'] != $_POST['osiguranje'] or
$data_before_update['viza'] != $_POST['viza'] or
$data_before_update['osiguranje_napomena'] != $_POST['osiguranje_napomena'] or
$data_before_update['iznos_akontacije'] != $_POST['iznos_akontacije'] or
$data_before_update['akontacija_napomena'] != $_POST['akontacija_napomena'] or
$data_before_update['vrsta_transporta'] != $_POST['vrsta_transporta'] or
$data_before_update['transport_napomena'] != $_POST['transport_napomena'] or
$data_before_update['smjestaj_napomena'] != $_POST['smjestaj_napomena']
  ){


  $podaci_mailq = $db->query("
    SELECT *  FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_raiff].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_raiff].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  left join [c0_intranet2_raiff].[dbo].[countries] as c on c.country_id = table2.odredisna_drzava
  where table2.id = ".$_POST['sl_put_id']."
  ");
  $podaci_mail = $podaci_mailq->fetch();
  $parent = $db->query("SELECT email_company, fname, lname from [c0_intranet2_raiff].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
    $parent = $parent ->fetch();

  //naznaka izmjenjenog podatka
  if($data_before_update['smjestaj_napomena'] != $_POST['smjestaj_napomena'])  $podaci_mail['smjestaj_napomena'] = "<span style='color:red'>".$podaci_mail['smjestaj_napomena']."</span>";
  if($data_before_update['transport_napomena'] != $_POST['transport_napomena'])  $podaci_mail['transport_napomena'] = "<span style='color:red'>".$podaci_mail['transport_napomena']."</span>";
  if($data_before_update['vrsta_transporta'] != $_POST['vrsta_transporta'])  $podaci_mail['vrsta_transporta'] = "<span style='color:red'>".$podaci_mail['vrsta_transporta']."</span>";
  if($data_before_update['akontacija_napomena'] != $_POST['akontacija_napomena'])  $podaci_mail['akontacija_napomena'] = "<span style='color:red'>".$podaci_mail['akontacija_napomena']."</span>";
  if($data_before_update['iznos_akontacije'] != $_POST['iznos_akontacije'])  $podaci_mail['iznos_akontacije'] = "<span style='color:red'>".$podaci_mail['iznos_akontacije']."</span>";
  if($data_before_update['osiguranje_napomena'] != $_POST['osiguranje_napomena'])  $podaci_mail['osiguranje_napomena'] = "<span style='color:red'>".$podaci_mail['osiguranje_napomena']."</span>";
  if($data_before_update['viza'] != $_POST['viza'])  $podaci_mail['viza'] = "<span style='color:red'>".$podaci_mail['viza']."</span>";
  if($data_before_update['osiguranje'] != $_POST['osiguranje'])  $podaci_mail['osiguranje'] = "<span style='color:red'>".$podaci_mail['osiguranje']."</span>";
  if($data_before_update['napomena'] != $_POST['napomena'])  $podaci_mail['napomena'] = "<span style='color:red'>".$podaci_mail['napomena']."</span>";
  if($data_before_update['razlog_putovanja'] != $_POST['razlog_putovanja'])  $podaci_mail['razlog_putovanja'] = "<span style='color:red'>".$podaci_mail['razlog_putovanja']."</span>";
  if($data_before_update['odredisni_grad2'] != $_POST['odredisni_grad2'])  $podaci_mail['odredisni_grad2'] = "<span style='color:red'>".$podaci_mail['odredisni_grad2']."</span>";
  if($data_before_update['odredisni_grad'] != $_POST['odredisni_grad'])  $podaci_mail['odredisni_grad'] = "<span style='color:red'>".$podaci_mail['odredisni_grad']."</span>";
  if($data_before_update['svrha'] != $_POST['svrha'])  $podaci_mail['svrha'] = "<span style='color:red'>".$podaci_mail['svrha']."</span>";

  if(strtotime($data_before_update['pocetak_datum']) != strtotime($_POST['pocetak_datum']))
    $d_poc = "<span style='color:red'>".date("d.m.Y",strtotime($podaci_mail['pocetak_datum']))."</span>";
  else $d_poc = date("d.m.Y",strtotime($podaci_mail['pocetak_datum']));

  if(strtotime($data_before_update['kraj_datum2']) != strtotime($_POST['kraj_datum2']) or strtotime($data_before_update['kraj_datum']) != strtotime($_POST['kraj_datum']))  
    $d_kraj = "<span style='color:red'>".date("d.m.Y",strtotime($podaci_mail['kraj_datum2'] ? $podaci_mail['kraj_datum2'] : $podaci_mail['kraj_datum']))."</span>";
  else $d_kraj = date("d.m.Y",strtotime($podaci_mail['kraj_datum2'] ? $podaci_mail['kraj_datum2'] : $podaci_mail['kraj_datum']));

  //

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = "UTF-8";

    $mail->IsSMTP();
    $mail->isHTML(true);  // Set email format to HTML data_before_update

    $mail->Host = "mailgw.rbbh.ba";
    $mail->Port = 25;

    $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
    $mail->addAddress($podaci_mail['email_company']);
    $mail->addAddress($parent['email_company']);
    $mail->addAddress('racunovodstvo@raiffeisengroup.ba');

    if($_POST['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
      if($podaci_mail['name'] != 'Bosna i Hercegovina'){
        $mail->addAddress('raiffeisen_assistance@raiffeisengroup.ba');
        if(strtolower($_POST['osiguranje']) == 'da'){
          $mail->addAddress('Banko.uniqa@uniqa.ba');
        }
      }
    }

      if($data_before_update['name'] != $podaci_mail['name']){
        $mail->addAddress('raiffeisen_assistance@raiffeisengroup.ba');
        if(strtolower($data_before_update['osiguranje']) != strtolower($_POST['osiguranje'])){
          $mail->addAddress('Banko.uniqa@uniqa.ba');
        }
      }

      if($data_before_update['svrha'] == 'Edukacija/trening' or $_POST['svrha'] == 'Edukacija/trening'){
        $mail->addAddress('hr.rbbh@raiffeisengroup.ba');
      }

      if($data_before_update['vrsta_transporta'] == 'Avion' or $_POST['vrsta_transporta'] == 'Avion'){
        $mail->addAddress('ured.uprave.rbbh@raiffeisengroup.ba');
      }
    
    
    $mail->Subject = 'KOREKCIJA poslovnog putovanja';
    $mail->Body = "<style>body{font-family: Arial,Verdana,Segoe,sans-serif;font-size:12px;line-height: 200%;}</style>
    <body>
    <b>".$podaci_mail['fname']." ".$podaci_mail['lname']."</b> je prijavi(o)la novi zahtjev 
    <span style='color:blue;'>KOREKCIJA</span> Poslovnog putovanja broj <b>".$podaci_mail['id']." </b><br>
    Status: <b>$statuss</b>
    Radnik:  <b>".$podaci_mail['fname']." ".$podaci_mail['lname']."</b><br>
    Org.jedinica:   <b>".$podaci_mail['B_1_description']."</b><br>
    Radno mjesto:   <b>".$podaci_mail['position']."   </b><br>
    Datum zaposlenja u Banci:   <b>".date("d.m.Y",strtotime($podaci_mail['employment_date']))."</b><br>
    JMBG:   <b>".$podaci_mail['JMB']."</b><br>
    Direktni nadređeni: <b>".$parent['fname']." ".$parent['lname']."    </b><br>
    Pocetni datum:  <b>".$d_poc."</b><br>
    Krajnji datum:  <b>".$d_kraj."</b><br>
    Svrha:  <b>".$podaci_mail['svrha']."</b><br>
    Odredište: <b>".$podaci_mail['odredisni_grad']." - ".$podaci_mail['odredisni_grad2']."</b><br>
    Razlog putovanja: <b>".$podaci_mail['razlog_putovanja']."</b><br>
    Napomena: <b>".$podaci_mail['napomena']."</b><br>
    Osiguranje: <b>".$podaci_mail['osiguranje']. "</b><br>
    Viza potrebna: <b>".$podaci_mail['viza']."</b>   Broj pasoša: <b>".$podaci_mail['dokument_broj']."</b> &nbsp &nbsp &nbsp    Napomena: <b>".$podaci_mail['osiguranje_napomena']."</b><br>
    Akontacija iznos:<b> ".$podaci_mail['iznos_akontacije']."</b> &nbsp &nbsp &nbsp   Napomena:<b>".$podaci_mail['akontacija_napomena']."</b><br>
    Sredstvo transporta: <b>".$podaci_mail['vrsta_transporta']."</b> &nbsp &nbsp &nbsp    Napomena: <b>".$podaci_mail['transport_napomena']."</b><br>
    Smještaj napomena: <b>".$podaci_mail['smjestaj_napomena']."</b>
    
    <br>
    <br>
    </body>
    ";

  if($canSendMail)
    if (!$mail->send()) {
      var_dump($mail->ErrorInfo);
    }
  }
//kraj notifikacije o izmjeni

  }
}catch (exception $e) {
  var_dump($e);
}


    //slanje maila o registraciji putovanja
  if(empty($_GET['sl_put_id'])){

    if(empty($_GET['sl_put_id'])){
      $new_slput= $db->query("SELECT top(1) id from [c0_intranet2_raiff].[dbo].[sl_put] order by id desc ");
      $new_slput = $new_slput ->fetch();
      $_GET['sl_put_id'] = $new_slput['id'];
    }

    $podaci_mailq = $db->query("
    SELECT *  FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] as table1
    INNER JOIN [c0_intranet2_raiff].[dbo].[sl_put] as table2
    ON table1.id = table2.request_id 
    inner join [c0_intranet2_raiff].[dbo].[users] as table3
    ON table1.user_id = table3.user_id
    left join [c0_intranet2_raiff].[dbo].[countries] as c on c.country_id = table2.odredisna_drzava
    where table2.id = ".$_GET['sl_put_id']."
    ");
    $podaci_mail = $podaci_mailq->fetch();

    $parent = $db->query("SELECT email_company, fname, lname from [c0_intranet2_raiff].[dbo].[users] WHERE employee_no = ".$podaci_mail['parent']." ");
    $parent = $parent ->fetch();
    
    send_mails($podaci_mail,$parent,false);
  } else{
    //ako je sl put poslan na korekciju pa je izmjenjen

    $podaci_mail = $db->query("
    SELECT *  FROM [c0_intranet2_raiff].[dbo].[hourlyrate_day] as table1
    INNER JOIN [c0_intranet2_raiff].[dbo].[sl_put] as table2
    ON table1.id = table2.request_id 
    inner join [c0_intranet2_raiff].[dbo].[users] as table3
    ON table1.user_id = table3.user_id
    where table2.id = ".$_GET['sl_put_id']."
    ");
    $podaci_mail = $podaci_mail->fetch();

    if($podaci_mail['status_hr'] == 2){
      $parentq = $db->query("SELECT email_company, fname, lname from [c0_intranet2_raiff].[dbo].[users] WHERE employee_no = ".$podaci_mail['parentMBO2']." ");
      $parent = $parentq ->fetch(); 
    
      $mail = new PHPMailer\PHPMailer\PHPMailer();
      $mail->CharSet = "UTF-8";
    
      $mail->IsSMTP();
      $mail->isHTML(true);  // Set email format to HTML
    
      $mail->Host = "mailgw.rbbh.ba";
      $mail->Port = 25;

      $mail->setFrom('sluzbeniput-rbbh@rbbh.ba', "Obavijesti službeni put");
    
      $mail->addAddress('racunovodstvo@raiffeisengroup.ba');
      $mail->addAddress($podaci_mail['email_company']);

      $mail->Subject = 'Izmjena naloga službenog puta broj - '.$_GET['sl_put_id'];
      $mail->Body = "Poštovani,<br>
      Nalog je izmjenjen od strane ".$_user['fname'].' '.$_user['lname'].".<br>
      ";
      
      if($canSendMail)
        if (!$mail->send()) {
            var_dump($mail->ErrorInfo);
        }
    }
  }

  //obrada
if (isset($_POST['obrada'])){
  if(empty($_POST['sl_put_id'])){
     $new_slput= $db->query("SELECT top(1) id from [c0_intranet2_raiff].[dbo].[sl_put] order by id desc ");
    $new_slput = $new_slput ->fetch();
    $_POST['sl_put_id'] = $new_slput['id'];
  } 

  $last_log = $db->query("Select top(1) * from [c0_intranet2_raiff].[dbo].[sl_put_logs] where sl_put_request_id = ".$_POST['sl_put_id']." order by vrijeme desc ");
  $last_log = $last_log->fetch();

  if($last_log['operation'] == 'odbijanje'){


  $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_raiff].[dbo].[sl_put] where status_hr = '1' and id =".$_POST['sl_put_id']);
  foreach ($provjeraq as $one){
    $provjera = $one['aa'];
  }

  if ($one['aa']==0){
    $odobri_req = $db->query("UPDATE [c0_intranet2_raiff].[dbo].[sl_put] set status_hr = 1 where id =".$_POST['sl_put_id']);
    $odobri_req->execute();
    //logs
    $insert_log = $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[sl_put_logs] (
        sl_put_request_id
    , operation
    , user_id
    , vrijeme
    )
    VALUES (
    '".$_POST['sl_put_id']."',
    'obrada',
    ".$_user['user_id']." ,
    ".time()."
    )");
    $insert_log = $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[sl_put_logs] (sl_put_request_id, operation, user_id, vrijeme) 
    VALUES (".$_POST['sl_put_id'].", 'odobravanje', $user, $ts)");

  }
}else{
  if(empty($_POST['sl_put_id'])){
     $new_slput= $db->query("SELECT top(1) id from [c0_intranet2_raiff].[dbo].[sl_put] order by id desc ");
    $new_slput = $new_slput ->fetch();
    $_POST['sl_put_id'] = $new_slput['id'];
  } 

    $odobri_req2 = $db->query("UPDATE [c0_intranet2_raiff].[dbo].[sl_put] set na_obradi = 1 where id =".$_POST['sl_put_id']);
    $odobri_req2->execute();

    //logs
    $insert_log = $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[sl_put_logs] (
        sl_put_request_id
    , operation
    , user_id
    , vrijeme
    )
    VALUES (
    '".$_POST['sl_put_id']."',
    'obrada',
    ".$_user['user_id']." ,
    ".time()."
    )");
    }
  }
header("Location: /app_raiff/?m=business_trip&p=all&pg=1"); 
exit();
}
}



//sifrarnici
$sifrarnik_svrha= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='svrha'");
$sifrarnik_valuta= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='valuta'");
$sifrarnik_primanje_sredstava= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='primanje_sredstava'");
$sifrarnik_vrsta_transporta= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='vrsta_transporta'");
$sifrarnik_vrsta_smjestaja= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='vrsta_smjestaja'");
$sifrarnik_osiguranje= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='osiguranje'");
$sifrarnik_viza= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='viza'");
$cijena_goriva_postotak= $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[sifrarnici] where active = 1 and name ='cijena_goriva_postotak'");

$sifrarnik_drzave = $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[countries] ORDER BY name COLLATE Latin1_General_CS_AS_KS_WS ASC ");

    foreach($sifrarnik_drzave as $one){
       $opcije_drzave.= "<option value='".$one['country_id']."'>".$one['name']."</option> ";
    }

    //get data
    
    if(!empty($_GET['sl_put_id'])){
      $putq = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[sl_put] where id =".$_GET['sl_put_id']);    
      $put = $putq->fetch();
    } else $put =null;

    if(isset($_GET['sl_put_id'])){
      $putb = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[sl_put_ostali_info] where sl_put_id_fk ='".$_GET['sl_put_id']."'");
      $putt = $putb->fetch();
    } else $putt = null;

// postavljanje datuma na selektovani
    if(isset($_GET['get_year'])){
        $check_status = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[users] as u 
left join [c0_intranet2_raiff].[dbo].[hourlyrate_year] as y on y.user_id= u.user_id
left join [c0_intranet2_raiff].[dbo].[hourlyrate_day] as d on d.user_id= u.user_id
where Date = '".date("Y-m-d", strtotime($_GET['dateOD']))."' and y.id=  ".$_GET['get_year']);
      $hd_data = $check_status->fetch();

      }else{
        $hd_data = $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[hourlyrate_day] where id = ".$_GET['id']);
      $hd_data = $hd_data->fetch();
      }
     

  if(isset($_GET['sl_put_id'])){
      $pocetak_sl_put = date("d.m.Y", strtotime($put['pocetak_datum']));
  }else{
      $pocetak_sl_put = date("d.m.Y", strtotime($hd_data['Date']));
  }

  //kraj sl puta
  if (!isset($put['kraj_datum2'])){
      if (isset($date_DO)){
          $kraj_sl_put = $date_DO;
      } else if(isset($put)){
          $kraj_sl_put =  prebaciDatumBih($put['kraj_datum']);
      } else{
          $kraj_sl_put = $pocetak_sl_put;
      }
  } else{
      $kraj_sl_put = prebaciDatumBih($put['kraj_datum']);
      $kraj_sl_put2 = $put['kraj_datum2'] ? date("d.m.Y", strtotime($put['kraj_datum2'])) : '';
  }

//datum akontacije
$id = $_GET['id'];
$dan = 6;
$tip = 'BHOLIDAY';
while($tip == 'BHOLIDAY' or in_array($dan,[6,7])){
  $id --;
  $dan_data = $db->query("SELECT * from [c0_intranet2_raiff].[dbo].[hourlyrate_day] where id = $id ");
  $dan_data = $dan_data->fetch();
  $dan=$dan_data['weekday'];
  $tip=$dan_data['KindofDay'];
}
$datum_akontacije = date("d.m.Y", strtotime($dan_data['Date']));
 ?>

<style>
.col-sm-12{
    margin-top:-8px;
}
.head{
    margin-top:0px;
    margin-bottom:5px;
}
.linee{
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
    margin: 10px 0 !important;
}
.naslov_holder{
    padding-left:25px;
}
label.radio > input:checked + img,
      label.radio > input:checked + i,
      label.radio > input:checked + span{
        	border-color: <?php echo _settings('color_button_bg'); ?>;
      }
      label.radio > input:checked + span{
        color: <?php echo _settings('color_button_bg'); ?>;
      }
.form-control{
    background-color:#f5f5f5;
    height:35px;
    font-size: 13px;
}
label{
    color: black;
}
.col-sm-12 {
    margin-top: 5px; 
}
.plus {
  --t:2px;   /* Thickness */
  --l:40px;  /* size of the symbol */
  --s:10px;  /* space around the symbol */
  --c1:#fff; /* Plus color*/
  --c2:#000; /* background color*/

  display:inline-block;
  width:var(--l);
  height:var(--l);
  padding:var(--s);
  box-sizing:border-box; /*Remove this if you don't want space to be included in the size*/
  
  background:
    linear-gradient(var(--c1),var(--c1)) content-box,
    linear-gradient(var(--c1),var(--c1)) content-box,
    var(--c2);
  background-position:center;
  background-size: 100% var(--t),var(--t) 100%;
  background-repeat:no-repeat;
}
.head{
    padding:10px 0 0 20px !important;
}
.radius {
  border-radius:50%;
}
.mybtn{
    width:100px !important;
    display:inline-block;
    margin-left:15px;
}
input, textarea{
    /* text-transform:uppercase; */
}
.btn.box-head-btn{
    padding-right:12px;
}
</style>
<!-- START - Main section -->
<section class="full">
<?php if ($data){
        ?>
        <div class="container row alert alert-success" role="alert" style='margin-top:10px;'>
            Uspješno snimljeni podaci!
        </div>
        <?php
    }

if(isset($_SESSION['datumi_err'])){ unset($_SESSION['datumi_err']); ?>
<div class="container row alert alert-danger" role="alert" style='margin-top:10px;'>
            Nije moguće promjeniti datum službenog putovanja zbog gaženja drugih odsustava! 
        </div>

<?php } 
if(isset($_SESSION['zakljucano_err'])){ unset($_SESSION['zakljucano_err']); ?>
<div class="container row alert alert-danger" role="alert" style='margin-top:10px;'>
            Satnice su zaključane. Iste ćete moći izmjeniti prvi radni dan u narednom mjesecu! 
        </div>

<?php } ?>
<div class='container row box' style='text-align:center;padding:10px;margin-top:15px;height:50px;'>
    <h3 style='margin:20px;display:inline;padding:20px;'>ZAHTJEV ZA SLUŽBENO PUTOVANJE</h3>
    <div style="display:inline;float:right;"><a href="/app_raiff/?m=business_trip&p=all&pg=1" style="background-color:#f5f5f5" class="btn box-head-btn">X</a></div>
</div>

<!-- odrediste putovanja -->
    <form method='POST' id='forma_sl_put'>
    <input type='hidden' name='request_id' value="<?php echo $hd_data ? $hd_data['id'] : $_GET['id'] ?>">
    <input type='hidden' name='status' value="<?php echo $_GET['status'] ?>">
    <input type='hidden' name='employee_no' value="<?php echo $hd_data['employee_no'] ?>">

        <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Odredište putovanja</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1"></a>
                </div>
            </div>
            <div class='content' id='c1'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Svrha</label>
                        <select name="svrha" id="svrha" onchange="promjenjena_svrha(this);" class="form-control" required oninvalid="this.setCustomValidity('Molimo vas da odeberete opciju')"
    oninput="this.setCustomValidity('')">
                            <option value="">Odaberi...</option>
                            <?php 
                          if($put['svrha']){
                            if($put['svrha'] == 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
                              ?>
                              <option selected value="UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE">UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE</option>
                              <?php
                            }else{
                              foreach($sifrarnik_svrha as $one){
                                if($one['naziv_instance'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE'){
                                  $selected = null;
                                if ($one['naziv_instance'] == $put['svrha']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                                }
                                
                              } 
                            }

                          }else{
                              foreach($sifrarnik_svrha as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['svrha']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                              } 
                            }
                           
                            ?>			
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Polazna država:</label>
                        <select name="polazna_drzava" id="polazna_drzava" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>       
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad polaska:</label>
                        <input type="text" name="grad_polaska" id='grad_polaska' value="<?php if(isset($put)) echo $put['grad_polaska']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Početak datum:</label>
                        <input type="text" id="pocetak_datum" name="pocetak_datum" value="<?php echo $pocetak_sl_put ?>" readonly class="form-control" onchange="promjeni_pocetak(this.value); change_datum_akontacije();">
                    </div>

                    <div class="col-sm-3">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="pocetak_vrijeme"  value="<?php if(isset($put)) echo $put['pocetak_vrijeme']; else echo '';?>" class="form-control">
                    </div>
                </div>
                
                <div class="col-sm-12" >
                    <div class="col-sm-3">
                        <label>Odredišna država:</label>
                        <select name="odredisna_drzava" class="form-control" id="odredisna_drzava" onchange="limit_akontacije()">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>     
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad odredišta:</label>
                        <input type="text" name="odredisni_grad" id='odredisni_grad' value="<?php if(isset($put)) echo $put['odredisni_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Krajnji datum u odredišnoj državi:</label>
                        <input type="text" name="kraj_datum" id="kraj_datum" value="<?php echo $kraj_sl_put;?>" class="form-control" onchange="promjeni_kraj(this.value);">
                    </div>
                    <div class="col-sm-3">
                        <label>Krajnje vrijeme u odredišnoj državi:</label>
                        <input class="time-input form-control  timepicker"  type="text" name="kraj_vrijeme" id='kraj_vrijeme' value="<?php if(isset($put)) echo $put['kraj_vrijeme']; else echo '';?>" class="form-control">
                    </div>
                </div>
                
                <div class="col-sm-12" >
                    <div class="col-sm-3">
                        <label>Odredišna država 2:</label>
                        <select name="odredisna_drzava2" class="form-control"  id="odredisna_drzava2" onchange="change_date(this); ">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>     
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad odredišta 2:</label>
                        <input type="text" name="odredisni_grad2" id='odredisni_grad2' value="<?php if(isset($put)) echo $put['odredisni_grad2']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Krajnji datum u odredišnoj državi 2:</label>
                        <input type="text" name="kraj_datum2" id='kraj_datum2' value="<?php echo $kraj_sl_put2; ?>" onchange="promjeni_kraj(this.value);" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Krajnje vrijeme u odredišnoj državi 2:</label>
                        <input class=" form-control " readonly type="text" name="kraj_vrijeme2" id='kraj_vrijeme2' value="<?php if(isset($put)) echo $put['kraj_vrijeme2']; else echo '';?>" class="form-control">
                    </div>
                </div>
                <!-- <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Odredišna država 3:</label>
                        <select name="odredisna_drzava3" class="form-control"  id="odredisna_drzava3">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>			
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>Grad odredišta 3:</label>
                        <input type="text" name="odredisni_grad3" id='odredisni_grad3' value="<?php if(isset($put)) echo $put['odredisni_grad3']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                </div> -->
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Razlog putovanja:</label>
                        <textarea  name="razlog_putovanja" id='razlog_putovanja' value="<?php if(isset($put)) echo $put['razlog_putovanja']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                    <div class="col-sm-6">
                        <label>Napomena:</label>
                        <textarea  name="napomena" id='napomena' value="<?php if(isset($put)) echo $put['napomena']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- akontacija -->

        <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje akontacije</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c2"></a>
                </div>
            </div>
            <div class='content' id='c2'>
                <div class="col-sm-12">
                    <table class="table table-sm table-bordered table-hover mytable" style="display:none;">
                    <thead>
                        <tr>
                        <th style='text-align:center;'>Akontacija u KM</th>
                        <th id="colspan" style='text-align:center;' colspan="1">Trajanje službenog puta</th>
                        </tr>
                        
                    </thead>
                    <tbody>
                    
                    </tbody>
                    </table>
                </div>

                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Vrijednost:</label>
                        <input step="any" type="number" name="iznos_akontacije" id='iznos_akontacije'  value="<?php if(isset($put)) echo $put['iznos_akontacije']; else echo '';?>"
                         min='0' max='9999'  onfocusout="$('.mytable').hide();" onfocus="$('.mytable').show();"
     class="form-control" <?php if( (strtotime($pocetak_sl_put)<=time() and !$admin) or (!$admin and $_GET['sl_put_id'])) echo 'readonly'; else echo '';?>>
                    </div>
                    <div class="col-sm-6">
                        <label>Valuta:</label>
                        <select name="valuta" class="form-control" >
                            <?php 
                            foreach($sifrarnik_valuta as $one){
                                $selected = null;
                                if(!$put and $one['naziv_instance'] == 'KM') $selected = 'selected';
                                if ($one['naziv_instance'] == $put['valuta']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected;?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Akontacija do datuma:</label>
                        <input type="text" name="datum_akontacije" id='akontacija_datum' value="<?php 
                           echo $datum_akontacije;
                          ?>"
                         class="form-control">
                    </div>
                    <div class="col-sm-6">
                        <label>Način uplate akontacije:</label>
                        <select name="primanje_sredstva" class="form-control" >
                            <?php 
                            foreach($sifrarnik_primanje_sredstava as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['primanje_sredstva']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                              

                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="akontacija_napomena" id='akontacija_napomena' value="<?php if(isset($put)) echo $put['akontacija_napomena']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

    <!-- transport -->

    <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje transporta</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c3"></a>
                </div>
            </div>
            <div class='content' id='c3'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Sredstvo transporta:</label>
                        <select name="vrsta_transporta" class="form-control" >
                            <option value=" " >Odaberi...</option>
                            <?php
                            foreach($sifrarnik_vrsta_transporta as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['vrsta_transporta']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance'] ?>"><?php echo $one['naziv_instance'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Početak datum:</label>
                        <input type="text" name="transport_pocetak_datum" id='transport_pocetak' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                        class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="transport_pocetak_vrijeme" id='transport_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['transport_pocetak_vrijeme']; else echo '';?>" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Polazna država:</label>
                        <select name="transport_polazna_drzava" class="form-control" id="transport_polazna_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad polaska:</label>
                        <input type="text" name="transport_grad_polaska" id='grad_polaska' value="<?php if(isset($put)) echo $put['transport_grad_polaska']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-3">
                        <label>Kraj datum:</label>
                        <input type="text" name="transport_kraj_datum" id='transport_kraj' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control  timepicker"  type="text" name="transport_kraj_vrijeme" id='transport_kraj_vrijeme' value="<?php if(isset($put)) echo $put['transport_kraj_vrijeme']; else echo '';?>" class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Odredišna država:</label>
                        <select name="transport_odredisna_drzava" class="form-control" id="transport_odredisna_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad odredišta:</label>
                        <input type="text" name="transport_odredisni_grad" id='odredisni_grad' value="<?php if(isset($put)) echo $put['transport_odredisni_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                </div>
                
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea name="transport_napomena" id='transport_napomena' value="<?php if(isset($put)) echo $put['transport_napomena']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

<!-- smjestaj -->

<div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Dodavanje smještaja</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c4"></a>
                </div>
            </div>
            <div class='content' id='c4'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Izaberite smještaj:</label>
                        <select name="vrsta_smjestaja" id="vrsta_smjestaja" class="form-control" >
                            <?php
                            foreach($sifrarnik_vrsta_smjestaja as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['vrsta_smjestaja']) $selected = 'selected';
                                ?>
                                <option <?php echo $seleted; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Početak datum:</label>
                        <input type="text" name="smjestaj_pocetak_datum" id='smjestaj_pocetak_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                         class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="smjestaj_pocetak_vrijeme" id='smjestaj_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['smjestaj_pocetak_vrijeme']; else echo '';?>" class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Kraj datum:</label>
                        <input type="text" name="smjestaj_kraj_datum" id='smjestaj_kraj_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="smjestaj_kraj_vrijeme" id='smjestaj_kraj_vrijeme' value="<?php if(isset($put)) echo $put['smjestaj_kraj_vrijeme']; else echo '';?>" class="form-control">
                    </div>
                </div>
                
                <div class="col-sm-12" >
                    <div class="col-sm-3">
                        <label>Odredišna država:</label>
                        <select name="smjestaj_drzava" class="form-control" id="smjestaj_drzava">
                            <option value=" "  selected>Odaberi...</option>
                            <?php echo $opcije_drzave; ?>				
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Grad odredišta:</label>
                        <input type="text" name="smjestaj_grad" id='grad_polaska' value="<?php if(isset($put)) echo $put['smjestaj_grad']; else echo '';?>" placeholder='Unesite puni naziv' class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Naziv/Adresa:</label>
                        <input type="text"  name="smjestaj_adresa" id='smjestaj_adresa' value="<?php if(isset($put)) echo $put['smjestaj_adresa']; else echo '';?>"  class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label>Kategorija hotela:</label>
                        <input type="text"  name="kategorija_hotela" id='kategorija_hotela' value="<?php if(isset($put)) echo $put['kategorija_hotela']; else echo '';?>"  class="form-control">
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="smjestaj_napomena" id='smjestaj_napomena' value="<?php  echo $put['smjestaj_napomena']; ?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- osiguranje viza -->

        <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Postavljanje osiguranja-vize</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c5"></a>
                </div>
            </div>
            <div class='content' id='c5'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Osiguranje:</label>
                        <select name="osiguranje" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_osiguranje as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['osiguranje']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label>Početak datum:</label>
                        <input type="text" name="osiguranje_pocetak_datum" id='osiguranje_pocetak_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $pocetak_sl_put;
                        }  else echo $pocetak_sl_put;?>"
                         class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Početak vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="transport_pocetak_vrijeme" id='transport_pocetak_vrijeme' value="<?php if(isset($put)) echo $put['transport_pocetak_vrijeme']; else echo '';?>" class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Kraj datum:</label>
                        <input type="text" name="osiguranje_kraj_datum" id='osiguranje_kraj_datum' value="<?php
                         if(isset($put)){
                          if ($put['transport_pocetak_datum'] != '1900-01-01'){
                            echo prebaciDatumBih($put['transport_pocetak_datum']);
                          } else echo $kraj_sl_put;
                        }  else echo $kraj_sl_put;?>"
                         class="form-control">
                    </div>

                    <div class="col-sm-3">
                        <label>Kraj vrijeme:</label>
                        <input class="time-input form-control timepicker"  type="text" name="transport_kraj_vrijeme" id='transport_kraj_vrijeme' value="<?php if(isset($put)) echo $put['transport_kraj_vrijeme']; else echo '';?>" class="form-control">
                    </div>
                </div>
    
                <div class="col-sm-12" >
                    <div class="col-sm-6">
                        <label>Dokument (pasoš) broj:</label>
                        <input type="text" name="dokument_broj" id='dokument_broj' value="<?php if(isset($put)) echo $put['dokument_broj']; else echo '';?>" class="form-control">
                    </div>
                    <div class="col-sm-6">
                        <label>Viza:</label>
                        <select name="viza" class="form-control" >
                            <option value=" "  selected>Odaberi...</option>
                            <?php 
                            foreach($sifrarnik_viza as $one){
                                $selected = null;
                                if ($one['naziv_instance'] == $put['viza']) $selected = 'selected';
                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Napomena:</label>
                        <textarea  name="osiguranje_napomena" id='osiguranje_napomena' value="<?php if(isset($put)) echo $put['osiguranje_napomena']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

<h3 class="text-center">OBRAČUN PUTNIH TROŠKOVA</h3>
<!-- Troskovi prevoza -->
<div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Troškovi prevoza</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c31"></a>
                </div>
            </div>
            <div class='content' id='c31'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                       <div class="col-sm-3">
                            <label>Prevozno sredstvo:</label>
                            <input type="text" name="trosak1" id='trosak1' value="<?php if(isset($putt)) echo $putt['trosak1']; else echo '';?>" placeholder='Unesite naziv troška prevoza' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Količina:</label>
                            <input step="any" type="number" min="0" name="kolicina1" id='kolicina1' onchange="calculate_total_nocenja2();" value="<?php if(isset($putt)) echo $putt['kolicina1']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Iznos KM:</label>
                            <input step="any" type="number" min="0" name="iznos1" id='iznos1' onchange="calculate_total_nocenja2();" value="<?php if(isset($putt)) echo $putt['iznos1']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Ukupno KM:</label>
                            <div id="total_prevoz1"></div>                            
                        </div>
                    </div>
                    <div class="col-sm-12" id="prevoz_red2" style="<?php if(!$putt['trosak2']) echo 'display:none;'; ?>">
                          <div class="col-sm-3">
                            
                            <input type="text" name="trosak2" id='trosak2' value="<?php if(isset($putt)) echo $putt['trosak2']; else echo '';?>" placeholder='Unesite naziv troška prevoza' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number" min="0" name="kolicina2" id='kolicina2' onchange="calculate_total_nocenja2();" value="<?php if(isset($putt)) echo $putt['kolicina2']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number" min="0" name="iznos2" id='iznos2' onchange="calculate_total_nocenja2();" value="<?php if(isset($putt)) echo $putt['iznos2']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <div id="total_prevoz2"></div>                            
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn-sm btn-info mybtn" style="<?php if($putt['trosak2']) echo 'display:none;'; ?>" id="add_prevoz" onclick="$('#prevoz_red2').show(); $('#delete_prevoz').show(); $('#add_prevoz').hide();">Dodaj red +</div>

                        <div class="btn-sm btn-danger mybtn" style="<?php if($putt['trosak2']) echo 'display:block;'; else echo 'display:none;' ?>" id="delete_prevoz" onclick="$('#prevoz_red2').hide();$('#delete_prevoz').hide();$('#add_prevoz').show();$('#trosak2').val('');$('#kolicina2').val('');$('#iznos2').val('');calculate_total_nocenja2();">Izbriši red</div>
                        
                    </div>
                    <div class="col-sm-12">
                       <div class="col-sm-12" style="border-top: 1px solid #d8cdcd;padding-top: 8px;font-size: 15px;padding-left: 0px;">
                       <p>U slučaju da ste koristili vlastiti automobil molimo Vas popunite pređene kilometre.</p>  <div class="col-sm-4">
                            
                            <input step="any" type="number" min="0" name="kol_gorivo" id='kol_gorivo' onchange="calculate_gorivo()" value="<?php if(isset($putt)) echo $putt['kol_gorivo']; else echo '';?>" placeholder='Unesite pređene kilometre (km)' class="form-control">
                            
                        </div>
                        <div class="col-sm-4">
                            
                            <select name="iznos_gorivo" class="form-control" id='iznos_gorivo' onchange="calculate_gorivo()">
                            <?php 
                            foreach($cijena_goriva_postotak as $one){
                                $matches = null;
                                $selected = null;
                                if ($one['naziv_instance'] == $putt['iznos_gorivo']) $selected = 'selected';

                                preg_match('/([0-9]*(\.[0-9]+)?) KM x ([0-9]*(\%)?)/', $one['naziv_instance'], $matches, PREG_OFFSET_CAPTURE);
                                ?>
                                <option data-km="<?php echo $matches[1][0]; ?>" data-postotak="<?php echo $matches[3][0]; ?>" <?php echo $selected; ?> value="<?php echo $one['naziv_instance']; ?>"><?php echo $one['naziv_instance']; ?></option>
                                <?php
                            }
                            ?>				
                        </select>
                        </div>
                        <div class="col-sm-1">Ukupno KM:</div>
                        <div class="col-sm-3" id="calc_gorivo"></div>
                       </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Troskovi prevoza -->
<!-- Izdaci za nocenje -->

        <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Izdaci za noćenje</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c313"></a>
                </div>
            </div>
            <div class='content' id='c313'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                       <div class="col-sm-3">
                            <label>Vrsta smještaja:</label>
                            <input type="text" name="izdaci_naziv1" id='izdaci_naziv1' value="<?php if(isset($putt)) echo $putt['izdaci_naziv1']; else echo '';?>" placeholder='Unesite naziv' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Broj noćenja:</label>
                            <input step="any" type="number"  min="0" name="izdaci_kol1" id='izdaci_kol1' value="<?php if(isset($putt)) echo $putt['izdaci_kol1']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Iznos KM po noćenju:</label>
                            <input step="any" type="number" min="0" name="izdaci_iznos1" id='izdaci_iznos1' value="<?php if(isset($putt)) echo $putt['izdaci_iznos1']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <label>Ukupno KM:</label>
                            <div id="total_nocenja1"></div>                            
                        </div>
                    </div>
                    <div class="col-sm-12" id="nocenja_red2" style="<?php if(!$putt['izdaci_naziv2']) echo 'display:none;'; ?>">
                          <div class="col-sm-3">
                            
                            <input type="text" name="izdaci_naziv2" id='izdaci_naziv2' value="<?php if(isset($putt)) echo $putt['izdaci_naziv2']; else echo '';?>" placeholder='Unesite naziv' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number" min="0" name="izdaci_kol2" id='izdaci_kol2' value="<?php if(isset($putt)) echo $putt['izdaci_kol2']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number" min="0" name="izdaci_iznos2" id='izdaci_iznos2' value="<?php if(isset($putt)) echo $putt['izdaci_iznos2']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <div id="total_nocenja2"></div>                            
                        </div>
                    </div>
                    <div class="col-sm-12" id="nocenja_red3" style="<?php if(!$putt['izdaci_naziv3']) echo 'display:none;'; ?>">
                         <div class="col-sm-3">
                            
                            <input type="text" name="izdaci_naziv3" id='izdaci_naziv3' value="<?php if(isset($putt)) echo $putt['izdaci_naziv3']; else echo '';?>" placeholder='Unesite naziv' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number"  min="0" name="izdaci_kol3" id='izdaci_kol3' value="<?php if(isset($putt)) echo $putt['izdaci_kol3']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            
                            <input step="any" type="number" min="0" name="izdaci_iznos3" id='izdaci_iznos3' value="<?php if(isset($putt)) echo $putt['izdaci_iznos3']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            
                        </div>
                        <div class="col-sm-3">
                            <div id="total_nocenja3"></div>                            
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn-sm btn-info mybtn" id="prvo_dugme" onclick="$('#nocenja_red2').show(); $('#drugo_dugme').show(); $('#prvo_dugme').hide();  $('#delete_secound').show();">Dodaj red +</div>
                        <div class="btn-sm btn-info mybtn" style="display:none;" id="drugo_dugme" onclick="$('#nocenja_red3').show(); $('#drugo_dugme').hide(); $('#delete_third').show();$('#delete_secound').hide();">Dodaj red +</div>

                        <div class="btn-sm btn-danger mybtn" style="display:none;" id="delete_secound" onclick="$('#nocenja_red2').hide();$('#delete_secound').hide();$('#prvo_dugme').show();$('#drugo_dugme').hide();$('#izdaci_naziv2').val('');$('#izdaci_kol2').val('');$('#izdaci_iznos2').val('');calculate_total_nocenja();">Izbriši red</div>

                        <div class="btn-sm btn-danger mybtn"  style="display:none;" id="delete_third" onclick="$('#nocenja_red3').hide();$('#delete_third').hide();$('#drugo_dugme').show();$('#delete_secound').show(); $('#izdaci_naziv3').val('');$('#izdaci_kol3').val('');$('#izdaci_iznos3').val('');calculate_total_nocenja();">Izbriši red</div>
                    </div>
                </div>
            </div>
        </div>

<!-- Izdaci za nocenje-->

<!-- Ostali troskovi -->

<div class="container row box">
    <div class='head col-sm-12'>
        <div class='naslov_holder'><h4>Ostali troškovi</h4></div>

        <div class="box-head-btn">
            <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c334"></a>
        </div>
    </div>
    <div class='content' id='c334'>
        <div class="col-sm-12" id="TextBoxesGroup">
                
            <div  class="col-sm-12 ultimate_class" id="TextBoxDiv">
                <div class="col-sm-3">
                    <label>Naziv troška:</label>
                    <input type="text" name="ost_trosak1" id='ost_trosak1' value="<?php if(isset($putt)) echo $putt['ost_trosak1']; else echo '';?>" placeholder='Unesite naziv troška' class="form-control">
                    
                </div>
                <div class="col-sm-3">
                    <label>Količina:</label>
                    <input step="any" type="number" min="0" name="ost_kolicina1" id='ost_kolicina19' onchange="calculate_total_ot();" value="<?php if(isset($putt)) echo $putt['ost_kolicina1']; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                    
                </div>
                <div class="col-sm-3">
                    <label>Iznos KM:</label>
                    <input step="any" type="number" min="0" name="ost_iznos1" id='ost_iznos19'  onchange="calculate_total_ot();" value="<?php if(isset($putt)) echo $putt['ost_iznos1']; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                </div>
                <div class="col-sm-3">
                    <label>Ukupno KM:</label>
                    <div id="ost_ukupno19"></div>
                </div>
            </div>
            <?php 
            if(isset($putt)){
                for($i=2;$i<=6;$i++){
                    if($putt['ost_trosak'.$i]){?>
                        <div  class="col-sm-12 ultimate_class" id="TextBoxDiv<?php echo $i;?>">
                            <div class="col-sm-3">
                                <input type="text" name="ost_trosak<?php echo $i;?>" id='ost_trosak<?php echo $i;?>' value="<?php if(isset($putt)) echo $putt['ost_trosak'.$i]; else echo '';?>" placeholder='Unesite naziv troška' class="form-control">
                                
                            </div>
                            <div class="col-sm-3">
                                <input step="any" type="number" min="0" name="ost_kolicina<?php echo $i;?>"  onchange="calculate_total_ot();"  id='ost_kolicina<?php echo $i;?>' value="<?php if(isset($putt)) echo $putt['ost_kolicina'.$i]; else echo '';?>" placeholder='Unesite količinu' class="form-control">
                                
                            </div>
                            <div class="col-sm-3">
                                <input step="any" type="number"  min="0" name="ost_iznos<?php echo $i;?>"  onchange="calculate_total_ot();" id='ost_iznos<?php echo $i;?>' value="<?php if(isset($putt)) echo $putt['ost_iznos'.$i]; else echo '';?>" placeholder='Unesite iznos' class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <div id="ost_ukupno<?php echo $i;?>"></div>
                            </div>
                        </div>
                    <?php }
                }
            }
            
            ?>
        </div>

        <div class="col-sm-12" style="display:inline;">
            <div class=" btn-sm btn-info mybtn" id='addButton' style="margin-left:30px;">Dodaj red +</div>
            <div class=" btn-sm btn-danger mybtn" id='removeButton'>Izbriši red</div>
        </div>
    </div>
</div>

<!-- Ostali troskovi-->

<!-- Opis zadatka -->

    <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Kratak opis zadatka (poslova) koji je obavljen na službenom putu</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c3899"></a>
                </div>
            </div>
            <div class='content' id='c3899'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Upišite kratki opis:</label>
                        <textarea  name="ost_kratkiopis" id='ost_kratkiopis' value="<?php if(isset($putt)) echo $putt['ost_kratkiopis']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

<!-- Opis zadatka -->

<!-- Biznis kartica -->

    <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Plaćeno biznis karticom</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c3284"></a>
                </div>
            </div>
            <div class='content' id='c3284'>
              <div class="col=sm-12">
                <div class="col-sm-12">
                  <label>Plaćeno biznis karticom (u KM)</label>
                    <input class="form-control" step="any" type="number"  min="0" id="placeno_biznis_karticom" name="placeno_biznis_karticom" value="<?php echo  $put['placeno_biznis_karticom']; ?>">
                </div>
              </div>
            </div>
        </div>

<!-- Biznis kartica -->

<!-- Specifikacija troskova -->

    <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Specifikacija troškova</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c382"></a>
                </div>
            </div>
            <div class='content' id='c382'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                        <label>Navesti specifikaciju nastalih troškova koja sadrži naziv, vrstu i iznos troškova nastalih u svrhu službenog putovanja za koje se prilažu računi:</label>
                        <textarea  name="ost_specopis" id='ost_specopis' value="<?php if(isset($putt)) echo $putt['ost_specopis']; else echo '';?>"  class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

<!-- Specifikacija troskova -->
<!-- izjava obroka -->
<style type="text/css">

#ck-button {
    margin:4px;
    background-color:#EFEFEF;
    border-radius:4px;
    border:1px solid #D0D0D0;
    overflow:auto;
    float:left;
    color: black;
}

#ck-button:hover {
   background: #fff75f;
    color: black;
}

#ck-button label {
    float:left;
    width:4.0em;
     color: black;
}

#ck-button label span {
    text-align:center;
    padding:3px 0px;
    display:block;
}

#ck-button label input {
    position:absolute;
    top:-20px;
}

#ck-button input:checked + span {
    background-color:#fcf000;
     color: black;
}
.da_ne{
  margin:4px;
    background-color:#EFEFEF;
    border-radius:4px;
    border:1px solid #D0D0D0;
    overflow:auto;
    float:left;
    color: black;
    padding: 5px 15px;
    cursor: pointer;
}
.da_ne.checked{
  background-color:#fcf000;
     color: black;
}
</style>

    <div class="container row box">
            <div class='head col-sm-12'>
                <div class='naslov_holder'><h4>Izjava da li je bila osigurana ishrana na službenom putu (tri obroka)</h4></div>
                <div class="box-head-btn">
                    <a href="javascript:;" class="ion-ios-arrow-up" data-widget="collapse" data-id="c38277"></a>
                </div>
            </div>
            <div class='content' id='c38277'>
                <div class="col-sm-12" >
                    <div class="col-sm-12">
                      <label>Odaberite:</label><input type="hidden" id="as" required oninvalid="this.setCustomValidity('Molimo vas da odeberete opciju')"
    oninput="this.setCustomValidity('')" >
                      <br>
<!-- new buttons -->
<!-- <input type="hidden" name="dacheck" id="dacheck_input" value="<?php if ($putt['dacheck'] == "DA") {echo 'checked';}else{echo '';}?>">
<input type="hidden" name="necheck" id="necheck_input" value="<?php if ($putt['dacheck'] == "DA") {echo 'checked';}else{echo '';}?>">

<div class="da_ne 
<?php if ($putt['dacheck'] == "DA") {echo 'checked';}else{echo '';}?>
">DA</div>
<div class="da_ne 
<?php if ($putt['necheck'] == "NE") { echo 'checked';}else{echo '';}?>
">NE</div> -->



                       <div id="ck-button">
                           <label style="margin-bottom: 0;">
                              <input type="checkbox" value="DA" id="dacheck" name="dacheck" class="checkkutija" style="display: none;" <?php if ($putt['dacheck'] == "DA") {echo 'checked';}else{echo '';}?>
                               />
                              <span>DA</span>
                           </label>
                        </div>
                        <div id="ck-button">
                           <label style="margin-bottom: 0;">
                              <input type="checkbox" value="NE" id="necheck" 
                              name="necheck" class="checkkutija" style="display: none;" <?php if ($putt['necheck'] == "NE") {
  echo 'checked';}else{echo '';}?> />
                              <span>NE</span>
                           </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- izjava obroka -->
        

    <div class='container row box' style='margin-bottom:15px;padding:20px;text-align:center'>
    
    

    <?php if( !$_GET['view']){ ?>
    <input type ="hidden" name="obrada" value ="1">
    <input type ="hidden" name="id" value ="<?php echo $_GET['id']?>">
        <button class="btn btn-red btn-lg" onclick="//return planiraj(event)">Sačuvaj! <i class="ion-ios-download-outline"></i></button>
    <?php } ?>
    </div>
    <input type ="hidden" name="sl_put_id" value ="<?php echo $put['id']; ?>">
    </form>

</section>

<!-- Modal svrha-->
<div class="modal fade" id="vl_auto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Obavijest</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="font-size: 15px;">
      Odabirom svrhe "Upotreba vlastitog automobila u službene svrhe" će biti uklonjena registracija službenog puta na kalendaru za odabrani period.
    Odabirom ove svrhe se vrši kreiranje naloga za potrebe refundacije troška.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal akontacije -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upozorenje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Unijeli ste akontaciju veću od naznačene u tabeli.
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal korekcije -->
<div class="modal fade" id="korekcije_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upozorenje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Izmjena datuma nije moguća. 
U koliko je došlo do promjene datuma potrebno je otkazati odsustvo na portalu i prijaviti novo.
        <br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
      </div>
    </div>
  </div>
</div>
<!-- END - Main section -->
<div class="hidden tabledata"><?php 
$data5 = $db->query("Select data from [c0_intranet2_raiff].[dbo].[akontacija]");
$data5 = $data5->fetch();

echo json_encode($data5, JSON_UNESCAPED_UNICODE); ?>
</div>


<?php

  include $_themeRoot.'/footer.php';

  function prebaciDatumStandard($datum){
    if($datum!='' and $datum!=' '){
        $niz = explode('.',$datum);
    return ((int)$niz[2]).'-'.((int)$niz[1]).'-'.((int)$niz[0]);
    }
    else return null;
  }
  function prebaciDatumBih($datum){
    if($datum!='' and $datum!=' '){
        $niz = explode('-',$datum);
    return ((int)$niz[2]).'.'.((int)$niz[1]).'.'.((int)$niz[0]);
  }
  else return null;
  }     
 ?>

 <script>
$( document ).ready(function(){
  $('#kraj_datum2').attr('readonly', 'readonly');
  if($('#kraj_datum2').val() != '') {
    $('#kraj_datum2').attr('readonly', 'false');
    $('#kraj_datum2').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
        });
  }


$("#dacheck").click(function(){
        $("#necheck").prop("checked", false);
    });
  $("#necheck").click(function(){
        $("#dacheck").prop("checked", false);
    });

    $('input').attr('maxlength', '60');
    $('textarea').attr('maxlength', '499');
    $('#ost_kratkiopis').attr('maxlength', '1800');


    $('#razlog_putovanja').val('<?php echo $put['razlog_putovanja']; ?>');
    $('#napomena').val('<?php echo $put['napomena']; ?>');
    $('#akontacija_napomena').val('<?php echo $put['akontacija_napomena']; ?>');
    $('#transport_napomena').val('<?php echo $put['transport_napomena']; ?>');
   $("[name=ost_kratkiopis]").val('<?php echo $putt['ost_kratkiopis']; ?>');
   $("[name=ost_specopis]").val('<?php echo $putt['ost_specopis']; ?>');
    $('#smjestaj_adresa').val('<?php echo $put['smjestaj_adresa']; ?>');
    $('#osiguranje_napomena').val('<?php echo $put['osiguranje_napomena']; ?>');
    $('#smjestaj_napomena').val('<?php echo $put['smjestaj_napomena']; ?>');


    let view = <?php if ($_GET['view']) echo $_GET['view']; else echo 0;?>;
    if (view == 1){
        $('input').attr('readonly', 'readonly');
        $('textarea').attr('readonly', 'readonly');
        $('select').attr('readonly', 'readonly');
    }

    $('input').attr('autocomplete', 'off');
    $('textarea').attr('autocomplete', 'off');
    $('select').attr('autocomplete', 'off');

   
    
    $('#akontacija_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });

    $('#transport_pocetak').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });

    $('#transport_kraj').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });
    $('#transport_kraj').attr('readonly', 'readonly');
    
    $('#smjestaj_pocetak_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });
        
    $('#smjestaj_kraj_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });
    $('#smjestaj_kraj_datum').attr('readonly', 'readonly');

    $('#osiguranje_pocetak_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });

    $('#osiguranje_kraj_datum').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });
    $('#osiguranje_kraj_datum').attr('readonly', 'readonly');

    $('#datum_kraj2').datepicker({
    todayBtn: "linked",
    format: 'dd.mm.yyyy',
    language: 'bs',
    });
    $('#kraj_datum').attr('readonly', 'readonly');

    var date = new Date();

    if(get_date_from_bos($("#pocetak_datum").val()) >=  new Date(date.getFullYear(), date.getMonth(), 1) ){
    
      $('#pocetak_datum').datepicker({
      todayBtn: "linked",
      format: 'dd.mm.yyyy',
      language: 'bs',
      }).on('changeDate', function (selected) {
          $('#kraj_datum').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );

        if($("#kraj_datum2").val() != '') $('#kraj_datum2').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );

       if(get_date_from_bos($("#pocetak_datum").val()) > get_date_from_bos($("#kraj_datum").val()) ){
        $("#kraj_datum").val( $("#pocetak_datum").val());
       }
       if(get_date_from_bos($("#pocetak_datum").val()) > get_date_from_bos($("#kraj_datum2").val()) ){
        $("#kraj_datum2").val($("#pocetak_datum").val());
       }

      });
    }else{
      $( "#pocetak_datum" ).click(function() {
        korekcije_alert();
      });
      $( "#kraj_datum" ).click(function() {
        korekcije_alert();
      });
    }

    console.log(get_date_from_bos($("#pocetak_datum").val()));

    if(get_date_from_bos($("#pocetak_datum").val()) >=  new Date(date.getFullYear(), date.getMonth(), 1) ){
      if($("#kraj_datum2").val() != '') $('#kraj_datum2').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );


      $('#kraj_datum').datepicker({
          todayBtn: "linked",
          format: 'dd.mm.yyyy',
          language: 'bs',
          startDate: get_date_from_bos($("#pocetak_datum").val())
      }).on('changeDate', function (selected) {
        if($("#kraj_datum2").val() != '') $('#kraj_datum2').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );

      });
    }else{
      $( "#pocetak_datum" ).click(function() {
        korekcije_alert();
      });
    }

  function korekcije_alert(){
      $('#korekcije_modal').modal('show'); 
  }
  
  function get_date_from_bos(date){
        let res = date.split(".");
        return new Date(res[1] + '-' + res[0] + '-' + res[2]);
  }

    //drzavice
    let polazna_drzava = '<?php if ($put)  echo $put['polazna_drzava']; else echo 0;?>';
    $('#polazna_drzava').val(polazna_drzava);
    $('#polazna_drzava').trigger('change');

    let odredisna_drzava = '<?php if ($put)  echo $put['odredisna_drzava']; else echo 0;?>';
    $('#odredisna_drzava').val(odredisna_drzava);
    $('#odredisna_drzava').trigger('change');

    let odredisna_drzava2 = '<?php if ($put)  echo $put['odredisna_drzava2']; else echo 0;?>';
    $('#odredisna_drzava2').val(odredisna_drzava2);
    $('#odredisna_drzava2').trigger('change');
    
    let transport_polazna_drzava = '<?php if ($put)  echo $put['transport_polazna_drzava']; else echo 0;?>';
    $('#transport_polazna_drzava').val(transport_polazna_drzava);
    $('#transport_polazna_drzava').trigger('change');
                
    let transport_odredisna_drzava = '<?php if ($put)  echo $put['transport_odredisna_drzava']; else echo 0;?>';
    $('#transport_odredisna_drzava').val(transport_odredisna_drzava);
    $('#transport_odredisna_drzava').trigger('change');
                            
    let smjestaj_drzava = '<?php if ($put)  echo $put['smjestaj_drzava']; else echo 0;?>';
    $('#smjestaj_drzava').val(smjestaj_drzava);
    $('#smjestaj_drzava').trigger('change');
            
});


//when the window has been completed loaded, we search for all textbox with time-input CSS class
window.onload = function(e){ 
  console.log($("odredisna_drzava2").val());

  if($("odredisna_drzava2").val() !== undefined){
    $("#kraj_vrijeme2").attr('readonly', false);
    $("#kraj_vrijeme2").addClass('timepicker time-input');
  }

	//perform a for loop to add the event handler
	Array.from(document.getElementsByClassName("timepicker")).forEach(
		function(element, index, array) {
			//Add the event handler to the time input
			element.addEventListener("blur", inputTimeBlurEvent);
		}
	);
  //perform a for loop to add the event handler
  Array.from(document.getElementsByClassName("time-input")).forEach(
    function(element, index, array) {
      //Add the event handler to the time input
      element.addEventListener("blur", inputTimeBlurEvent);
    }
  );
}

inputTimeBlurEvent = function(e){
	var newTime = "";
	var timeValue = e.target.value;
	var numbers = [];
	var splitTime = [];
	
	//1st condition: if the value entered is empty, we set the default value
	if(timeValue.trim() == ""){
		e.target.value = "00:00";
		return;
	}
	
	//2nd condition: only allow numbers, dot and double dot. If not match set the default value. Example => 23a55
	var regex = /^[0-9.:]+$/;
	if( !regex.test(timeValue) ) {
		e.target.value = "00:00";
		return;
	}
	
	//3rd condition: replace the dot with double dot. Example => 23.55
	e.target.value = e.target.value.replace(".", ":").replace(/\./g,"");
	timeValue = e.target.value;
	
	//4th condition: auto add double dot if the input entered by user contains numbers only (no dot or double dot symbol found)
	//example => 2344 or 933
	if(timeValue.indexOf(".") == -1 && timeValue.indexOf(":") == -1){
		//check if the length is more than 4 we strip it up to 4
		if(timeValue.trim().length > 4){
			timeValue = timeValue.substring(0,4);
		}
		var inputTimeLength = timeValue.trim().length;
		numbers = timeValue.split('');
		switch(inputTimeLength){
      case 1:
        if(parseInt(timeValue) <= 0){
          e.target.value = "00:00";
        }else{
          e.target.value = "0" + timeValue + ":00";
        }
      break;
			//Example => 23
			case 2:
				if(parseInt(timeValue) <= 0){
					e.target.value = "00:00";
				}else if(parseInt(timeValue) >= 24){
					e.target.value = "00:00";
				}else{
					e.target.value = timeValue + ":00";
				}
				break;
			//Example => 234
			case 3:
				newTime = "0" + numbers[0] + ":";
				if(parseInt(numbers[1] + numbers[2]) > 59){
					newTime += "00";
				}else{
					newTime += numbers[1] + numbers[2];
				}
				e.target.value = newTime;
				break;
			//Example 2345
			case 4:
				if(parseInt(numbers[0] + numbers[1]) >= 24){
					newTime = "00:";
				}else{
					newTime = numbers[0] + numbers[1] + ":";
				}
				if(parseInt(numbers[2] + numbers[3]) > 59){
					newTime += "00";
				}else{
					newTime += numbers[2] + numbers[3];
				}
				e.target.value = newTime;
				break;
		}
		return;
	}
	
	//5th condition: if double dot found
	var doubleDotIndex = timeValue.indexOf(":");
	//if user doesnt enter the first part of hours example => :35
	if(doubleDotIndex == 0){
		newTime = "00:";
		splitTime = timeValue.split(':');
		numbers = splitTime[1].split('');
		if(parseInt(numbers[0] + numbers[1]) > 59){
			newTime += "00";
		}else{
			newTime += numbers[0] + numbers[1];
		}
		e.target.value = newTime;
		return;
	}else{
		//if user enter not full time example=> 9:3
		splitTime = timeValue.split(':');
		var partTime1 = splitTime[0].split('');
		if(partTime1.length == 1){
			newTime = "0" + partTime1[0] + ":";
		}else{
			if(parseInt(partTime1[0] + partTime1[1]) > 23){
				newTime = "00:";
			}else{
				newTime = partTime1[0] + partTime1[1] + ":";
			}
		}
		
		var partTime2 = splitTime[1].split('');
		if(partTime2.length == 1){
			newTime += "0" + partTime2[0];
		}else{
			if(parseInt(partTime2[0] + partTime2[1]) > 59){
				newTime += "00";
			}else{
				newTime += partTime2[0] + partTime2[1];
			}
		}
		e.target.value = newTime;
		return;
	}
}


function change_date(drzava){
      
    function get_date_from_bos(date){
        let res = date.split(".");
        return new Date(res[1] + '-' + res[0] + '-' + res[2]);
  }

    let datum2 = $("#kraj_datum2").val();
    if(datum2=='')
    if(drzava.value !== ''){


      $("#kraj_vrijeme2").attr('readonly', false);
      $("#kraj_vrijeme2").addClass('timepicker time-input');

//perform a for loop to add the event handler
  Array.from(document.getElementsByClassName("timepicker")).forEach(
    function(element, index, array) {
      //Add the event handler to the time input
      element.addEventListener("blur", inputTimeBlurEvent);
    }
  );
  //perform a for loop to add the event handler
  Array.from(document.getElementsByClassName("time-input")).forEach(
    function(element, index, array) {
      //Add the event handler to the time input
      element.addEventListener("blur", inputTimeBlurEvent);
    }
  );



        $( "#kraj_datum2" ).val($("#kraj_datum").val());

        $('#kraj_datum2').datepicker({
            todayBtn: "linked",
            format: 'dd.mm.yyyy',
            language: 'bs',
        }).on('changeDate', function (selected) {
        $('#kraj_datum').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );

        $('#kraj_datum').datepicker('setEndDate', get_date_from_bos($("#kraj_datum2").val()) );

      if($("#kraj_datum2").val() != '') $('#kraj_datum2').datepicker('setStartDate', get_date_from_bos($("#pocetak_datum").val()) );

    });

    }
}

 calculate_total_nocenja();
 function calculate_total_nocenja(){
    $("#total_nocenja1").text( ($("#izdaci_kol1").val() * $("#izdaci_iznos1").val()).toFixed(2) );
    $("#total_nocenja2").text( ($("#izdaci_kol2").val() * $("#izdaci_iznos2").val()).toFixed(2));
    $("#total_nocenja3").text( ($("#izdaci_kol3").val() * $("#izdaci_iznos3").val()).toFixed(2));
 }
 $("#izdaci_kol1").change(function() { calculate_total_nocenja();});
 $("#izdaci_iznos1").change(function() { calculate_total_nocenja();});
 $("#izdaci_kol2").change(function() { calculate_total_nocenja();});
 $("#izdaci_iznos2").change(function() { calculate_total_nocenja();});
 $("#izdaci_kol3").change(function() { calculate_total_nocenja();});
 $("#izdaci_iznos3").change(function() { calculate_total_nocenja();});
 


 calculate_total_nocenja2();
 function calculate_total_nocenja2(){
    $("#total_prevoz1").text($("#iznos1").val() * $("#kolicina1").val());
    $("#total_prevoz2").text($("#iznos2").val() * $("#kolicina2").val());
 }

$("#iznos_gorivo").change(function(){
    
});
calculate_gorivo();
function calculate_gorivo(){
    let opcija_gorivo = document.querySelector( "#iznos_gorivo option:checked" );
    let km = opcija_gorivo.dataset.km;

    let postotak = 0;
    if(opcija_gorivo.dataset.postotak)
            postotak = opcija_gorivo.dataset.postotak.replace('%','');
    

    let kol_gorivo = $("#kol_gorivo").val();

    $("#calc_gorivo").text( (Math.round((km*kol_gorivo*(postotak)/100) * 100) / 100 ).toFixed(2))
}

//dinamicko dodavanje polja
function showHideButtons(){
  var counter = $(".ultimate_class").length;
  console.log(counter);

if(counter > 1) $("#removeButton").show();
else $("#removeButton").hide();

if(counter>5){
        $("#addButton").hide();
} else $("#addButton").show();
}

$(document).ready(function(){

  var counter = $(".ultimate_class").length;

showHideButtons();

$("#addButton").click(function () {


  var counter = $(".ultimate_class").length +1;

var newTextBoxDiv = $(document.createElement('div'))
     .attr("id", 'TextBoxDiv' + (counter)).attr("class", 'ultimate_class');

newTextBoxDiv.after().html("<div class='col-sm-12'>"+
    "<div class='col-sm-3'>"+
        "<input type='text' onkeyup='forceInputUppercase(event);' name='ost_trosak"+ counter + "' id='ost_trosak"+ counter + "' placeholder='Unesite naziv troška' class='form-control'>"+
    "</div>"+
    "<div class='col-sm-3'>"+
        "<input step='any' type='number'  min='0' name='ost_kolicina"+ counter + "' id='ost_kolicina"+ counter + "'  onchange='calculate_total_ot();'  placeholder='Unesite količinu' class='form-control'>"+
    "</div>"+
    "<div class='col-sm-3'>"+
        "<input step='any' type='number'  min='0' name='ost_iznos"+ counter + "' id='ost_iznos"+ counter + "'  onchange='calculate_total_ot();'  placeholder='Unesite iznos' class='form-control'>"+
    "</div>"+
    "<div class='col-sm-3'>"+
        "<div id='ost_ukupno"+ counter + "'></div>"+
    "</div>"+
"</div>");

newTextBoxDiv.appendTo("#TextBoxesGroup"); 

showHideButtons();

 });

 $("#removeButton").click(function () {
  var counter = $(".ultimate_class").length;

    $("#TextBoxDiv" + counter).remove();
showHideButtons();
 });
});

calculate_total_ot();

function myround(num){
  return (Math.round((num + Number.EPSILON) * 100) / 100).toFixed(2);
}

function calculate_total_ot(){
  $("#ost_ukupno19").text(myround($("#ost_kolicina19").val() * $("#ost_iznos19").val()));
  $("#ost_ukupno1").text(myround($("#ost_kolicina1").val() * $("#ost_iznos1").val()));
  $("#ost_ukupno2").text(myround($("#ost_kolicina2").val() * $("#ost_iznos2").val()));
  $("#ost_ukupno3").text(myround($("#ost_kolicina3").val() * $("#ost_iznos3").val()));
  $("#ost_ukupno4").text(myround($("#ost_kolicina4").val() * $("#ost_iznos4").val()));
  $("#ost_ukupno5").text(myround($("#ost_kolicina5").val() * $("#ost_iznos5").val()));
  $("#ost_ukupno6").text(myround($("#ost_kolicina6").val() * $("#ost_iznos6").val()));
 }

 let php_data = JSON.parse($(".tabledata").text());
 let row_data = JSON.parse(php_data.data);

//svaki red
for(let i = 0; i<=row_data.length -1; i++){

  //kolone
  let column_data = row_data[i];
  let dynamic_rows = '';
  
  for(let j=0; j<=column_data.length-1; j++){
    if(i==0 && j == 0){
      dynamic_rows += "<td>Država</td>";
    }else{
      dynamic_rows += "<td >" +column_data[j] + "</td>";
    }
  }

  let append_html = "";
  if(i == 0){
    append_html = "<tr id='prvi_red'>" + dynamic_rows + "</tr>";
  }else{
    append_html = "<tr>" + dynamic_rows + "</tr>";
  }
  

  $("table tbody").append(append_html);
}

$("#colspan").attr('colspan', $("#prvi_red td").length-1);

$( "#kraj_vrijeme2" ).focusout(function() {
  function get_date_from_bos(date){
        let res = date.split(".");
        return new Date(res[1] + '-' + res[0] + '-' + res[2]);
  }

  let date1 =  $("#kraj_datum").val() ;
  let date2 =  $("#kraj_datum2").val() ;

  if ( get_date_from_bos(date1).getTime() === get_date_from_bos(date2).getTime() ){
    let time1 = $("#kraj_vrijeme").val();
    let time2 = $("#kraj_vrijeme2").val();

    if(time1 > time2){
      $("#kraj_vrijeme2").val(time1);
    }
  }
  
});

 function promjenjena_svrha(select){
    if($(select).find("option:selected" ).text() == "UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE"){
        $('#vl_auto').modal('show'); 
    }
 }    
 
 $('#odredisna_drzava').trigger('change');

 limit_akontacije();

function limit_akontacije(){
    
}

function maxLengthCheck(object) {
    if (object.value.length > object.max.length)
      object.value = object.value.slice(0, object.max.length)
  }
    
  function isNumeric (evt) {
    
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode (key);
    var regex = /[0-9]/;
    if ( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
    }
  }

  function forceInputUppercase(e){
    var start = e.target.selectionStart;
    var end = e.target.selectionEnd;
    e.target.value = e.target.value.toUpperCase();
    e.target.setSelectionRange(start, end);
  }

   $("input[type=text]").each(function(){
       this.addEventListener("keyup", forceInputUppercase, false);
   });

$("#vrsta_smjestaja").val("<?php echo $put['vrsta_smjestaja'];?>").change();

function promjeni_pocetak(datum){
  $('#transport_pocetak').val(datum).change();
  $('#smjestaj_pocetak_datum').val(datum).change();
  $('#osiguranje_pocetak_datum').val(datum).change();
}
promjeni_kraj( $("#kraj_datum").val() );
$("#kraj_datum2").change();

function promjeni_kraj(datum){
  if(datum != ''){
    $('#transport_kraj').val(datum).change();
    $('#smjestaj_kraj_datum').val(datum).change();
    $('#osiguranje_kraj_datum').val(datum).change();
  }
 }

 $( "#iznos_akontacije" ).focusout(function() {
  let odrediste = $('#odredisna_drzava').find("option:selected" ).text();
    let pocetak_datum = $('#pocetak_datum').val();
    let kraj_datum = $('#kraj_datum').val();

    let duration = datediff(parseDate(pocetak_datum), parseDate(kraj_datum));

    let php_data = JSON.parse($(".tabledata").text());
    let row_data = JSON.parse(php_data.data);

    let max_iznos_prva = 10000;
    //svaki red
    for(let i = 1; i<=row_data.length -1; i++){
        let column_data = row_data[i];

        if(column_data[0] == odrediste){
            max_iznos_prva = column_data[duration];
        }
    }

    //druga drzava
    let max_iznos_druga = 0;
    let kraj_datum2= $('#kraj_datum2').val();
    let pocetak_datum2 = $('#kraj_datum').val();

    if(kraj_datum2 != ''){
      let odrediste = $('#odredisna_drzava2').find("option:selected" ).text();

      duration2 = datediff(parseDate(pocetak_datum2), parseDate(kraj_datum2));

      //svaki red
      for(let i = 1; i<=row_data.length -1; i++){
          let column_data = row_data[i];

          if(column_data[0] == odrediste){
              max_iznos_druga = column_data[duration2];
          }
      }
    }

    // console.log(max_iznos_prva,max_iznos_druga,pocetak_datum2,kraj_datum2,duration);
    let max = parseInt(max_iznos_prva) + parseInt(max_iznos_druga);
    if(parseInt($('#iznos_akontacije').val()) > max) $('#myModal').modal('show');
  });

function parseDate(str) {
    var mdy = str.split('.');
    return new Date(mdy[2], mdy[1]-1, mdy[0]);
}

function datediff(first, second) {
    return Math.round((second-first)/(1000*60*60*24));
}

$("input[type=number]").keypress(function() { return event.charCode >= 46 && event.charCode <=57 })

function change_datum_akontacije(){
  let datum_pocetka = $('#pocetak_datum').val();
  let values = {
    'datum_pocetka' : datum_pocetka,
    'request' : 'datum-akontacije',
    'id' : <?php echo $_GET['id']; ?>
  }

  let ajaxRequest= $.post("/app_raiff/modules/default/ajax.php", values, function(data) {
        let podaci = JSON.parse(data);
        $('#akontacija_datum').val(podaci);
         $('#akontacija_datum').trigger('change');

    })
    .fail(function() {
        alert("error");
    });
}

if (!Array.from) {
  Array.from = (function () {
    var toStr = Object.prototype.toString;
    var isCallable = function (fn) {
      return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
    };
    var toInteger = function (value) {
      var number = Number(value);
      if (isNaN(number)) { return 0; }
      if (number === 0 || !isFinite(number)) { return number; }
      return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
    };
    var maxSafeInteger = Math.pow(2, 53) - 1;
    var toLength = function (value) {
      var len = toInteger(value);
      return Math.min(Math.max(len, 0), maxSafeInteger);
    };

    // The length property of the from method is 1.
    return function from(arrayLike/*, mapFn, thisArg */) {
      // 1. Let C be the this value.
      var C = this;

      // 2. Let items be ToObject(arrayLike).
      var items = Object(arrayLike);

      // 3. ReturnIfAbrupt(items).
      if (arrayLike == null) {
        throw new TypeError("Array.from requires an array-like object - not null or undefined");
      }

      // 4. If mapfn is undefined, then let mapping be false.
      var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
      var T;
      if (typeof mapFn !== 'undefined') {
        // 5. else
        // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
        if (!isCallable(mapFn)) {
          throw new TypeError('Array.from: when provided, the second argument must be a function');
        }

        // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
        if (arguments.length > 2) {
          T = arguments[2];
        }
      }

      // 10. Let lenValue be Get(items, "length").
      // 11. Let len be ToLength(lenValue).
      var len = toLength(items.length);

      // 13. If IsConstructor(C) is true, then
      // 13. a. Let A be the result of calling the [[Construct]] internal method of C with an argument list containing the single item len.
      // 14. a. Else, Let A be ArrayCreate(len).
      var A = isCallable(C) ? Object(new C(len)) : new Array(len);

      // 16. Let k be 0.
      var k = 0;
      // 17. Repeat, while k < len… (also steps a - h)
      var kValue;
      while (k < len) {
        kValue = items[k];
        if (mapFn) {
          A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
        } else {
          A[k] = kValue;
        }
        k += 1;
      }
      // 18. Let putStatus be Put(A, "length", len, true).
      A.length = len;
      // 20. Return A.
      return A;
    };
  }());
}




$("#akontacija_datum").change(function(){

function get_date_from_bos(date){
        let res = date.split(".");
        return new Date(res[1] + '-' + res[0] + '-' + res[2]);
  }
  let date_akontacija  = get_date_from_bos( $("#akontacija_datum").val() );
  let pocetak_put = get_date_from_bos($("#pocetak_datum").val() );
  let admin = '<?php if ($admin) echo 1; else echo 0; ?> ';

  if(date_akontacija.getTime() <= new Date().getTime() && pocetak_put.getTime() <= new Date().getTime() && admin == 0){
    $("#iznos_akontacije").attr('readonly', 'readonly');
  } 
  else {
    $("#iznos_akontacije").attr('readonly', false);
  }
})



 </script>
</body>
</html>