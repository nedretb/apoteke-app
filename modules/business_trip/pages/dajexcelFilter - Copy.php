<?php

//admin ili korisnik provjera
$admin = $db->query("SELECT count(user_id) as br FROM [c0_intranet2_apoteke].[dbo].[users] where sl_put_admin=1 and user_id=".$_user['user_id']);
foreach($admin as $admin1){
  $admin = $admin1;
}

if ($admin1['br']==1){
  $admin=true;
} 
else {$admin=false;}

function secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}

// get podataka
if (!empty($_GET['dod'])) {
        $dateod = $_GET['dod'];
        $date1 = date("Y/m/d", strtotime(str_replace("/", "-", $_GET['dod'])));
        $date_query_od = " and table2.transport_pocetak_datum >='" . $date1 . "'";
    } else {
        $dateod = '';
        $date_query_od = "";
    }

if (!empty($_GET['ddo'])) {
        $datedo = $_GET['ddo'];
        $date2 = date("Y/m/d", strtotime(str_replace("/", "-", $_GET['ddo'])));
        $date_query_do = "and table2.transport_kraj_datum <='" . $date2 . "'";
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
        $date_kreiranja_2 = strtotime($date_kreiranja_do);
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
  $stak="and table2.status_hr=2";
}elseif ($_GET["status"]==10) {
 $stak="and table2.na_obradi IS NULL";
}elseif ($_GET["status"]==111) {
 $stak="and table2.lock=1 ";
}elseif ($_GET["status"]==69) {
  $stak = null;
}
  $status_query = $stak;

}else{
  $status_query = '';};

if ($admin==true){
      $podaci_excel = $db->query("
        SELECT *, tableSOD.name as S_odredisna_drzava, tableTPD.name as T_polazna_drzava,tableTOD.name as T_odredisna_drzava, tableCP.name as polazna_drzava1, tableC1.name as drzava1, tableC2.name as drzava2,  tableC3.name as drzava3, table2.status as sl_put_status, table2.id as sl_put_id,
CASE
    WHEN (
  SELECT count(temp.id) from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as temp
  where temp.Date >=table2.pocetak_datum and  temp.Date <=
    CASE
      WHEN table2.kraj_datum2 is null or table2.kraj_datum2 ='' THEN table2.kraj_datum 
      ELSE table2.kraj_datum
    END
  and temp.id between table2.request_id - 90 and table2.request_id + 90 
  and temp.status not in (73,83)
  ) > 0 THEN 'DA'
    ELSE 'NE'
END AS otkazano 
        FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
        INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
        ON table1.id = table2.request_id 
        inner join [c0_intranet2_apoteke].[dbo].[users] as table3
        ON table1.user_id = table3.user_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC1 on table2.odredisna_drzava = tableC1.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC2 on table2.odredisna_drzava2 = tableC2.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC3 on table2.odredisna_drzava3 = tableC3.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableCP on table2.polazna_drzava = tableCP.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableTPD on table2.transport_polazna_drzava = tableTPD.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableTOD on table2.transport_odredisna_drzava = tableTOD.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableSOD on table2.smjestaj_drzava = tableSOD.country_id
        left join [c0_intranet2_apoteke].[dbo].[sl_put_ostali_info] as tableOST
        ON table2.request_id = tableOST.request_id
        inner join ".$_conf['nav_database'].".[RAIFFEISEN INVEST\$Employee] as tableC4 on table3.employee_no = tableC4.[Modified Employee No_]
        OUTER APPLY
        (
            SELECT TOP 1 *
            FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
            WHERE logs.sl_put_request_id = table2.id 
        order by logs.id desc
        ) logs
        where ( table3.user_id= ".$_user['user_id']."
        or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table1.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id'].")
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
        ");
        $data = $podaci_excel->fetchAll();

      }else{
        $podaci_excel = $db->query("
              SELECT tableSOD.name as S_odredisna_drzava, tableTPD.name as T_polazna_drzava,tableTOD.name as T_odredisna_drzava,tableCP.name as polazna_drzava1, tableC1.name as drzava1, tableC2.name as drzava2,  tableC3.name as drzava3,  *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
              INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
              ON table1.id = table2.request_id 
              inner join [c0_intranet2_apoteke].[dbo].[users] as table3
              ON table1.user_id = table3.user_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC1 on table2.odredisna_drzava = tableC1.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC2 on table2.odredisna_drzava2 = tableC2.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC3 on table2.odredisna_drzava3 = tableC3.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableCP on table2.polazna_drzava = tableCP.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableTPD on table2.transport_polazna_drzava = tableTPD.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableTOD on table2.transport_odredisna_drzava = tableTOD.country_id
               left join [c0_intranet2_apoteke].[dbo].[countries] as tableSOD on table2.smjestaj_drzava = tableSOD.country_id
              left join [c0_intranet2_apoteke].[dbo].[sl_put_ostali_info] as tableOST
              ON table2.request_id = tableOST.request_id
              inner join ".$_conf['nav_database'].".[RAIFFEISEN INVEST\$Employee] as tableC4 on table3.employee_no = tableC4.[Modified Employee No_]
              where (table3.user_id = ".$_user['user_id']." or ".$_user['employee_no']." in (parent,parent2) 
              or ".$_user['employee_no']." in (parentMBO2,parentMBO3,parentMBO4,parentMBO5,parentMBO2d,parentMBO3d,parentMBO4d,parentMBO5d)
              or ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5,admin6,admin7,admin8)
              or (SELECT TOP 1 user_id FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] as table4 where table4.sl_put_request_id = table1.id order by table4.[sl_put_request_id] asc) = ".$_user['user_id'].")
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

        ");
        $data = $podaci_excel->fetchAll();
        
      };


require_once($root.'\CORE\PHPExcel-1.8\Classes\PHPExcel.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=my_excel_filename_filtered.xls");
header("Pragma: no-cache");
header("Expires: 0");

flush();


$doc = new PHPExcel();
$cell_broj = 3;
ob_clean();
$doc->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Person ID')
            ->setCellValue('B1', 'Prezime I ime')
            ->setCellValue('C1', 'JMBG')
            ->setCellValue('I1', 'Iznos Akontacije')
            ->setCellValue('J1', 'TRN')
            ->setCellValue('K1', 'Status')
            ->setCellValue('L1', 'Datum Kreiranja')
            ->setCellValue('M1', 'Odredište putovanja')
            ->setCellValue('M2', 'Svrha')
            ->setCellValue('N2', 'Početak datum')
            ->setCellValue('O2', 'Kraj datum')
            ->setCellValue('P2', 'Početak vrijeme')
            ->setCellValue('Q2', 'Kraj vrijeme')
            ->setCellValue('R2', 'Polazna država')
            ->setCellValue('S2', 'Grad polaska')
            ->setCellValue('T2', 'Odredišna država')
            ->setCellValue('U2', 'Grad odredišta')
            ->setCellValue('V2', 'Odredišna država2')
            ->setCellValue('W2', 'Grad odredišta2')
            ->setCellValue('Z2', 'Razlog putovanja')
            ->setCellValue('AA2','Napomena')
            ->setCellValue('AB1','Dodavanje akontacije')
            ->setCellValue('AB2','Iznos akontacije')
            ->setCellValue('AC2','Valuta')
            ->setCellValue('AD2','Akontacija do datuma')
            ->setCellValue('AE2','Primanje sredstava')
            ->setCellValue('AF2','Napomena')
            ->setCellValue('AG1','Dodavanje transporta')
            ->setCellValue('AG2','Sredstvo transporta')
            ->setCellValue('AH2','Početak datum')
            ->setCellValue('AI2','Kraj datum')
            ->setCellValue('AJ2','Početak vrijeme')
            ->setCellValue('AK2','Kraj vrijeme')
            ->setCellValue('AL2','Polazna država')
            ->setCellValue('AM2','Grad polaska')
            ->setCellValue('AN2','Odredišna država')
            ->setCellValue('AO2','Grad odredišta')
            ->setCellValue('AP2','Napomena')
            ->setCellValue('AQ1','Kratak opis zadatka (poslova) koji je obavljen na službenom putu')
            ->setCellValue('AR1','Specifikacija troškova')
            ->setCellValue('AS1','Izjava da li je bila osigurana ishrana na službenom putu (tri obroka)')
            ->setCellValue('AT1','Troškovi prevoza')
            ->setCellValue('AT2','Trošak 1')
            ->setCellValue('AU2','Količina 1')
            ->setCellValue('AV2','Iznos 1')
            ->setCellValue('AW2','Trošak 2')
            ->setCellValue('AX2','Količina 2')
            ->setCellValue('AY2','Iznos 2')
            ->setCellValue('AZ1','Vlastiti Automobil ')
            ->setCellValue('AZ2','Količina goriva ')
            ->setCellValue('BA2','Iznos KM')
            ->setCellValue('BB1','Izdaci za noćenje')
            ->setCellValue('BB2','Naziv 1')
            ->setCellValue('BC2','Količina 1')
            ->setCellValue('BD2','Iznos 1')
            ->setCellValue('BE2','Naziv 2')
            ->setCellValue('BF2','Količina 2')
            ->setCellValue('BG2','Iznos 2')
            ->setCellValue('BH2','Naziv 3')
            ->setCellValue('BI2','Količina 3')
            ->setCellValue('BJ2','Iznos 3')
            ->setCellValue('BK1','Dodavanje smještaja')
            ->setCellValue('BK2','Smještaj')
            ->setCellValue('BL2','Početak datum')
            ->setCellValue('BM2','Kraj datum')
            ->setCellValue('BN2','Početak vrijeme')
            ->setCellValue('BO2','Kraj vrijeme')
            ->setCellValue('BP2','Odredišna država')
            ->setCellValue('BQ2','Odredišni grad')
            ->setCellValue('BR2','Naziv/Adresa')
            ->setCellValue('BS1','Postavljanje osiguranja-vize')
            ->setCellValue('BS2','Osiguranje')
            ->setCellValue('BT2','Početak datum')
            ->setCellValue('BU2','Kraj datum')
            ->setCellValue('BV2','Početak vrijeme')
            ->setCellValue('BW2','Kraj vrijeme')
            ->setCellValue('BX2','Dokument (pasoš) broj')
            ->setCellValue('BY2','Viza')
            ->setCellValue('BZ2','Napomena')
            ->setCellValue('CA1','Ostali troškovi')
            ->setCellValue('CA2','Naziv troška 1')
            ->setCellValue('CB2','Količina 1')
            ->setCellValue('CC2','Iznos 1')
            ->setCellValue('CD2','Naziv troška2')
            ->setCellValue('CE2','Količina 2')
            ->setCellValue('CF2','Iznos 2')
            ->setCellValue('CG2','Naziv troška3')
            ->setCellValue('CH2','Količina 3')
            ->setCellValue('CI2','Iznos 3')
            ->setCellValue('CJ2','Naziv troška4')
            ->setCellValue('CK2','Količina 4')
            ->setCellValue('CL2','Iznos 4')
            ->setCellValue('CM2','Naziv troška5')
            ->setCellValue('CN2','Količina 5')
            ->setCellValue('CO2','Iznos 5')
            ->setCellValue('CP2','Naziv troška6')
            ->setCellValue('CQ2','Količina 6')
            ->setCellValue('CR2','Iznos 6')




            
;
			  foreach ($data as $podatak_ex) {
        $br=$cell_broj++;

        if($podatak_ex['operation'] == 'obrada' or $podatak_ex['operation'] == 'odobravanje') {$statuss = 'Na obradi';}
        elseif ($podatak_ex['operation'] == 'odbijanje') {$statuss = 'Poslano na korekciju';}
        if ($podatak_ex['lock'] == 1 ) $statuss = 'Zaključano';
        if ($podatak_ex['otkazano'] == 'DA') $statuss = 'Otkazano';
            

      if ($podatak_ex['dacheck'] == "DA") {
        $jelidailine = 'DA';
      }elseif ($podatak_ex['necheck'] == "NE") {
         $jelidailine = 'NE';
      }else{$jelidailine = '';};

            $doc->setActiveSheetIndex(0)->setCellValue('A'.$br.'', $podatak_ex['employee_no']);
            $doc->setActiveSheetIndex(0)->setCellValue('B'.$br.'', $podatak_ex['fname'].' '.$podatak_ex['lname']);
            $doc->setActiveSheetIndex(0)->setCellValue('C'.$br.'', $podatak_ex['JMB']);
            $doc->setActiveSheetIndex(0)->setCellValue('I'.$br.'', $podatak_ex['iznos_akontacije']);
            $doc->setActiveSheetIndex(0)->setCellValue('J'.$br.'', $podatak_ex['Bank Account No_']);
            $doc->setActiveSheetIndex(0)->setCellValue('K'.$br.'', $statuss);
            $doc->setActiveSheetIndex(0)->setCellValue('L'.$br.'', date("d.m.Y",$podatak_ex['created_at']));
          $doc->setActiveSheetIndex(0)->setCellValue('M'.$br.'', $podatak_ex['svrha']);
            $doc->setActiveSheetIndex(0)->setCellValue('N'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_pocetak_datum'])));
            $doc->setActiveSheetIndex(0)->setCellValue('O'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_kraj_datum'])));
            $doc->setActiveSheetIndex(0)->setCellValue('P'.$br.'', $podatak_ex['pocetak_vrijeme']);
            $doc->setActiveSheetIndex(0)->setCellValue('Q'.$br.'', $podatak_ex['kraj_vrijeme']);
            $doc->setActiveSheetIndex(0)->setCellValue('R'.$br.'', $podatak_ex['polazna_drzava1']);
            $doc->setActiveSheetIndex(0)->setCellValue('S'.$br.'', $podatak_ex['grad_polaska']);
            $doc->setActiveSheetIndex(0)->setCellValue('T'.$br.'', $podatak_ex['drzava1']);
            $doc->setActiveSheetIndex(0)->setCellValue('U'.$br.'', $podatak_ex['odredisni_grad']);
            $doc->setActiveSheetIndex(0)->setCellValue('V'.$br.'', $podatak_ex['drzava2']);
            $doc->setActiveSheetIndex(0)->setCellValue('W'.$br.'', $podatak_ex['odredisni_grad2']);
            $doc->setActiveSheetIndex(0)->setCellValue('X'.$br.'', $podatak_ex['drzava3']);
            $doc->setActiveSheetIndex(0)->setCellValue('Y'.$br.'', $podatak_ex['odredisni_grad3']);
            $doc->setActiveSheetIndex(0)->setCellValue('Z'.$br.'', $podatak_ex['razlog_putovanja']);
            $doc->setActiveSheetIndex(0)->setCellValue('AA'.$br.'', $podatak_ex['napomena']);
          $doc->setActiveSheetIndex(0)->setCellValue('AB'.$br.'', $podatak_ex['iznos_akontacije']);
           $doc->setActiveSheetIndex(0)->setCellValue('AC'.$br.'', $podatak_ex['valuta']);
           $doc->setActiveSheetIndex(0)->setCellValue('AD'.$br.'', date("d.m.Y",strtotime($podatak_ex['datum_akontacije'])));
           $doc->setActiveSheetIndex(0)->setCellValue('AE'.$br.'', $podatak_ex['primanje_sredstva']);
           $doc->setActiveSheetIndex(0)->setCellValue('AF'.$br.'', $podatak_ex['akontacija_napomena']);
          $doc->setActiveSheetIndex(0)->setCellValue('AG'.$br.'', $podatak_ex['vrsta_transporta']);
           $doc->setActiveSheetIndex(0)->setCellValue('AH'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_pocetak_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('AI'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_kraj_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('AJ'.$br.'', $podatak_ex['transport_pocetak_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('AK'.$br.'', $podatak_ex['transport_kraj_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('AL'.$br.'', $podatak_ex['T_polazna_drzava']);
           $doc->setActiveSheetIndex(0)->setCellValue('AM'.$br.'', $podatak_ex['transport_grad_polaska']);
           $doc->setActiveSheetIndex(0)->setCellValue('AN'.$br.'', $podatak_ex['T_odredisna_drzava']);
           $doc->setActiveSheetIndex(0)->setCellValue('AO'.$br.'', $podatak_ex['transport_odredisni_grad']);
           $doc->setActiveSheetIndex(0)->setCellValue('AP'.$br.'', $podatak_ex['transport_napomena']);
          $doc->setActiveSheetIndex(0)->setCellValue('AQ'.$br.'', $podatak_ex['ost_kratkiopis']);
          $doc->setActiveSheetIndex(0)->setCellValue('AR'.$br.'', $podatak_ex['ost_specopis']);
          $doc->setActiveSheetIndex(0)->setCellValue('AS'.$br.'', $jelidailine);
          $doc->setActiveSheetIndex(0)->setCellValue('AT'.$br.'', $podatak_ex['trosak1']);
           $doc->setActiveSheetIndex(0)->setCellValue('AU'.$br.'', $podatak_ex['kolicina1']);
           $doc->setActiveSheetIndex(0)->setCellValue('AV'.$br.'', $podatak_ex['iznos1']);
           $doc->setActiveSheetIndex(0)->setCellValue('AW'.$br.'', $podatak_ex['trosak2']);
           $doc->setActiveSheetIndex(0)->setCellValue('AX'.$br.'', $podatak_ex['kolicina2']);
           $doc->setActiveSheetIndex(0)->setCellValue('AY'.$br.'', $podatak_ex['iznos2']);
           $doc->setActiveSheetIndex(0)->setCellValue('AZ'.$br.'', $podatak_ex['kol_gorivo']);


           preg_match('/([0-9]*(\.[0-9]+)?) KM x ([0-9]*(\%)?)/', $podatak_ex['iznos_gorivo'], $matches, PREG_OFFSET_CAPTURE);

           $doc->setActiveSheetIndex(0)->setCellValue('BA'.$br.'', $podatak_ex['kol_gorivo'] ? $podatak_ex['kol_gorivo']*$matches[1][0]*($matches[3][0]/100) : '0');

           
          $doc->setActiveSheetIndex(0)->setCellValue('BB'.$br.'', $podatak_ex['izdaci_naziv1']);
           $doc->setActiveSheetIndex(0)->setCellValue('BC'.$br.'', $podatak_ex['izdaci_kol1']);
           $doc->setActiveSheetIndex(0)->setCellValue('BD'.$br.'', $podatak_ex['izdaci_iznos1']);
           $doc->setActiveSheetIndex(0)->setCellValue('BE'.$br.'', $podatak_ex['izdaci_naziv2']);
           $doc->setActiveSheetIndex(0)->setCellValue('BF'.$br.'', $podatak_ex['izdaci_kol2']);
           $doc->setActiveSheetIndex(0)->setCellValue('BG'.$br.'', $podatak_ex['izdaci_iznos2']);
           $doc->setActiveSheetIndex(0)->setCellValue('BH'.$br.'', $podatak_ex['izdaci_naziv3']);
           $doc->setActiveSheetIndex(0)->setCellValue('BI'.$br.'', $podatak_ex['izdaci_kol3']);
           $doc->setActiveSheetIndex(0)->setCellValue('BJ'.$br.'', $podatak_ex['izdaci_iznos3']);
          $doc->setActiveSheetIndex(0)->setCellValue('BK'.$br.'', $podatak_ex['vrsta_smjestaja']);
           $doc->setActiveSheetIndex(0)->setCellValue('BL'.$br.'', date("d.m.Y",strtotime($podatak_ex['smjestaj_pocetak_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('BM'.$br.'', date("d.m.Y",strtotime($podatak_ex['smjestaj_kraj_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('BN'.$br.'', $podatak_ex['smjestaj_pocetak_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('BO'.$br.'', $podatak_ex['smjestaj_kraj_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('BP'.$br.'', $podatak_ex['S_odredisna_drzava']);
           $doc->setActiveSheetIndex(0)->setCellValue('BQ'.$br.'', $podatak_ex['smjestaj_grad']);
           $doc->setActiveSheetIndex(0)->setCellValue('BR'.$br.'', $podatak_ex['smjestaj_adresa']);
          $doc->setActiveSheetIndex(0)->setCellValue('BS'.$br.'', $podatak_ex['osiguranje']);
           $doc->setActiveSheetIndex(0)->setCellValue('BT'.$br.'', date("d.m.Y",strtotime($podatak_ex['osiguranje_pocetak_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('BU'.$br.'', date("d.m.Y",strtotime($podatak_ex['osiguranje_kraj_datum'])));
           $doc->setActiveSheetIndex(0)->setCellValue('BV'.$br.'', $podatak_ex['osiguranje_pocetak_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('BW'.$br.'', $podatak_ex['osiguranje_kraj_vrijeme']);
           $doc->setActiveSheetIndex(0)->setCellValue('BX'.$br.'', $podatak_ex['dokument_broj']);
           $doc->setActiveSheetIndex(0)->setCellValue('BY'.$br.'', $podatak_ex['viza']);
           $doc->setActiveSheetIndex(0)->setCellValue('BZ'.$br.'', $podatak_ex['osiguranje_napomena']);
          $doc->setActiveSheetIndex(0)->setCellValue('CA'.$br.'', $podatak_ex['ost_trosak1']);
           $doc->setActiveSheetIndex(0)->setCellValue('CB'.$br.'', $podatak_ex['ost_kolicina1']);
           $doc->setActiveSheetIndex(0)->setCellValue('CC'.$br.'', $podatak_ex['ost_iznos1']);
           $doc->setActiveSheetIndex(0)->setCellValue('CD'.$br.'', $podatak_ex['ost_trosak2']);
           $doc->setActiveSheetIndex(0)->setCellValue('CE'.$br.'', $podatak_ex['ost_kolicina2']);
           $doc->setActiveSheetIndex(0)->setCellValue('CF'.$br.'', $podatak_ex['ost_iznos2']);
           $doc->setActiveSheetIndex(0)->setCellValue('CG'.$br.'', $podatak_ex['ost_trosak3']);
           $doc->setActiveSheetIndex(0)->setCellValue('CH'.$br.'', $podatak_ex['ost_kolicina3']);
           $doc->setActiveSheetIndex(0)->setCellValue('CI'.$br.'', $podatak_ex['ost_iznos3']);
           $doc->setActiveSheetIndex(0)->setCellValue('CJ'.$br.'', $podatak_ex['ost_trosak4']);
           $doc->setActiveSheetIndex(0)->setCellValue('CK'.$br.'', $podatak_ex['ost_kolicina4']);
           $doc->setActiveSheetIndex(0)->setCellValue('CL'.$br.'', $podatak_ex['ost_iznos4']);
           $doc->setActiveSheetIndex(0)->setCellValue('CM'.$br.'', $podatak_ex['ost_trosak5']);
           $doc->setActiveSheetIndex(0)->setCellValue('CN'.$br.'', $podatak_ex['ost_kolicina5']);
           $doc->setActiveSheetIndex(0)->setCellValue('CO'.$br.'', $podatak_ex['ost_iznos5']);
           $doc->setActiveSheetIndex(0)->setCellValue('CP'.$br.'', $podatak_ex['ost_trosak6']);
           $doc->setActiveSheetIndex(0)->setCellValue('CQ'.$br.'', $podatak_ex['ost_kolicina6']);
           $doc->setActiveSheetIndex(0)->setCellValue('CR'.$br.'', $podatak_ex['ost_iznos6']);



                  }

                  
			$doc->getActiveSheet()->getColumnDimension('A')->setWidth(9);
      $doc->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
			$doc->getActiveSheet()->getColumnDimension('B')->setWidth(19);
			$doc->getActiveSheet()->getColumnDimension('C')->setWidth(16);
			$doc->getActiveSheet()->getColumnDimension('D')->setWidth(0);
			$doc->getActiveSheet()->getColumnDimension('E')->setWidth(0);
      $doc->getActiveSheet()->getColumnDimension('F')->setWidth(0);
      $doc->getActiveSheet()->getColumnDimension('G')->setWidth(0);
      $doc->getActiveSheet()->getColumnDimension('H')->setWidth(0);
      $doc->getActiveSheet()->getColumnDimension('I')->setWidth(16);
      $doc->getActiveSheet()->getColumnDimension('J')->setWidth(17);
      $doc->getActiveSheet()->getColumnDimension('K')->setWidth(12);
      $doc->getActiveSheet()->getColumnDimension('L')->setWidth(16);

$doc->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('N')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('O')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('P')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('R')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('S')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('T')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('U')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('V')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('W')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('X')->setWidth(0);
$doc->getActiveSheet()->getColumnDimension('Y')->setWidth(0);
$doc->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('Y')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('Z')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AA')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AB')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AC')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AD')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AE')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AF')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AG')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AH')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AI')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AJ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AK')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AL')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AM')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AN')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AO')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AP')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AQ')->setWidth(40);
$doc->getActiveSheet()->getColumnDimension('AR')->setWidth(25);
$doc->getActiveSheet()->getColumnDimension('AS')->setWidth(35);
$doc->getActiveSheet()->getColumnDimension('AT')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AU')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AV')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AW')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AX')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AY')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('AZ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BA')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BB')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BC')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BD')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BE')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BF')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BG')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BH')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BI')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BJ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BK')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BL')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BM')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BN')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BO')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BP')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BQ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BR')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BS')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BT')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BU')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BV')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BW')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BX')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BY')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('BZ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CA')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CB')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CC')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CD')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CE')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CF')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CG')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CH')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CI')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CJ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CK')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CL')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CM')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CN')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CO')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CP')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CQ')->setWidth(16);
$doc->getActiveSheet()->getColumnDimension('CR')->setWidth(16);

            $doc->getActiveSheet()->mergeCells("F1:H1");
            $doc->getActiveSheet()->mergeCells("M1:AA1");
            $doc->getActiveSheet()->mergeCells("AB1:AF1");
            $doc->getActiveSheet()->mergeCells("AG1:AP1");
            $doc->getActiveSheet()->mergeCells("AT1:AY1");
            $doc->getActiveSheet()->mergeCells("AZ1:BA1");
            $doc->getActiveSheet()->mergeCells("BB1:BJ1");
            $doc->getActiveSheet()->mergeCells("BK1:BR1");
            $doc->getActiveSheet()->mergeCells("BS1:BZ1");
            $doc->getActiveSheet()->mergeCells("CA1:CR1");
            $doc->getActiveSheet()->mergeCells("A1:A2");
            $doc->getActiveSheet()->mergeCells("B1:B2");
            $doc->getActiveSheet()->mergeCells("C1:C2");
            $doc->getActiveSheet()->mergeCells("I1:I2");
            $doc->getActiveSheet()->mergeCells("J1:J2");
            $doc->getActiveSheet()->mergeCells("K1:K2");
            $doc->getActiveSheet()->mergeCells("L1:L2");
            $doc->getActiveSheet()->mergeCells("AQ1:AQ2");
            $doc->getActiveSheet()->mergeCells("AR1:AR2");
            $doc->getActiveSheet()->mergeCells("AS1:AS2");

            $doc->getActiveSheet()->getStyle("A1:A5000")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("F1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("M1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("AB1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("AG1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("AT1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("BB1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("BK1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("BS1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("CA1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("B1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("I1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("J1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("K1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $doc->getActiveSheet()->getStyle("L1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $doc->getActiveSheet()->getStyle('A1:GA100')->getAlignment()->setWrapText(true); 
            
            



$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

$objWriter->save('php://output');

?>


<section class="full">

  <div class="container-fluid">
  </div>
  </section>
  <?php

  include $_themeRoot.'/footer.php';

 ?>