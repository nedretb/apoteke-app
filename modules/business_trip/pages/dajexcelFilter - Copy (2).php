<?php
error_reporting(0);
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
      ELSE table2.kraj_datum2
    END
  and temp.id between table2.request_id - 90 and table2.request_id + 90 
  and temp.status not in (73,81) and temp.corr_status not in (73,81)
  ) > 0 THEN 'DA'
    ELSE 'NE'
END AS otkazano 
        FROM [c0_intranet2_apoteke].[dbo].[sl_put] as table2
        left JOIN [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
        ON table2.request_id = table1.id
        left join [c0_intranet2_apoteke].[dbo].[users] as table3
        ON table1.user_id = table3.user_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC1 on table2.odredisna_drzava = tableC1.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC2 on table2.odredisna_drzava2 = tableC2.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableC3 on table2.odredisna_drzava3 = tableC3.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableCP on table2.polazna_drzava = tableCP.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableTPD on table2.transport_polazna_drzava = tableTPD.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableTOD on table2.transport_odredisna_drzava = tableTOD.country_id
        left join [c0_intranet2_apoteke].[dbo].[countries] as tableSOD on table2.smjestaj_drzava = tableSOD.country_id
        left join [c0_intranet2_apoteke].[dbo].[sl_put_ostali_info] as tableOST
        ON table2.id = tableOST.sl_put_id_fk
        left join ".$_conf['nav_database'].".[RAIFFEISEN INVEST\$Employee] as tableC4 on table3.employee_no = tableC4.[Modified Employee No_]
        OUTER APPLY
        (
            SELECT TOP 1 *
            FROM [c0_intranet2_apoteke].[dbo].[sl_put_logs] logs
            WHERE logs.sl_put_request_id = table2.id 
        order by logs.id desc
        ) logs
        where 
        table3.user_id is not null
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
              order by table2.id desc
        ");
        $data = $podaci_excel->fetchAll();

      }else{
        $podaci_excel = $db->query("
              SELECT tableSOD.name as S_odredisna_drzava, tableTPD.name as T_polazna_drzava,tableTOD.name as T_odredisna_drzava,tableCP.name as polazna_drzava1, tableC1.name as drzava1, tableC2.name as drzava2,  tableC3.name as drzava3,  * ,
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
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC1 on table2.odredisna_drzava = tableC1.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC2 on table2.odredisna_drzava2 = tableC2.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableC3 on table2.odredisna_drzava3 = tableC3.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableCP on table2.polazna_drzava = tableCP.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableTPD on table2.transport_polazna_drzava = tableTPD.country_id
              left join [c0_intranet2_apoteke].[dbo].[countries] as tableTOD on table2.transport_odredisna_drzava = tableTOD.country_id
               left join [c0_intranet2_apoteke].[dbo].[countries] as tableSOD on table2.smjestaj_drzava = tableSOD.country_id
              left join [c0_intranet2_apoteke].[dbo].[sl_put_ostali_info] as tableOST
              ON table2.id = tableOST.sl_put_id_fk
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
              order by table2.id desc

        ");
        $data = $podaci_excel->fetchAll(PDO::FETCH_ASSOC);
        
      };
      $arr=[];

//       foreach ($data as $podatak_ex) {
//         $br=$cell_broj++;

//         if($podatak_ex['operation'] == 'obrada' or $podatak_ex['operation'] == 'odobravanje') {$statuss = 'Na obradi';}
//         elseif ($podatak_ex['operation'] == 'odbijanje') {$statuss = 'Poslano na korekciju';}
//         if ($podatak_ex['lock'] == 1 ) $statuss = 'Zaključano';
//         if ($podatak_ex['otkazano'] == 'DA' and $podatak_ex['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE') $statuss = 'Otkazano';
//         array_push($arr, [$podatak_ex['sl_put_id'], $statuss]);
// }var_dump($arr);
// die();

require_once($root.'\CORE\PHPExcel-1.8\Classes\PHPExcel.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Excel_izvještaj_".date("d.m.Y").".xls");
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
            ->setCellValue('D1', 'Iznos Akontacije')
            ->setCellValue('E1', 'TRN')
            ->setCellValue('F1', 'Status')
            ->setCellValue('G1', 'Datum Kreiranja')
            ->setCellValue('H1', 'Svrha')
            ->setCellValue('I1', 'Odredište putovanja')

              ->setCellValue('I2', 'Početak datum')
              ->setCellValue('J2', 'Kraj datum')
              ->setCellValue('K2', 'Početak vrijeme')
              ->setCellValue('L2', 'Kraj vrijeme')
              ->setCellValue('M2', 'Polazna država')
              ->setCellValue('N2', 'Grad polaska')
              ->setCellValue('O2', 'Odredišna država')
              ->setCellValue('P2', 'Grad odredišta')
              ->setCellValue('Q2', 'Odredišna država2')
              ->setCellValue('R2', 'Grad odredišta2')
              ->setCellValue('S2', 'Razlog putovanja')
              ->setCellValue('T2','Napomena')

            ->setCellValue('U1','Dodavanje akontacije')
              ->setCellValue('U2','Iznos akontacije')
              ->setCellValue('V2','Valuta')
              ->setCellValue('W2','Akontacija do datuma')
              ->setCellValue('X2','Primanje sredstava')
              ->setCellValue('Y2','Napomena')


            ->setCellValue('Z1','Dodavanje transporta')
              ->setCellValue('Z2','Sredstvo transporta')
              ->setCellValue('AA2','Početak datum')
              ->setCellValue('AB2','Kraj datum')
              ->setCellValue('AC2','Početak vrijeme')
              ->setCellValue('AD2','Kraj vrijeme')
              ->setCellValue('AE2','Polazna država')
              ->setCellValue('AF2','Grad polaska')
              ->setCellValue('AG2','Odredišna država')
              ->setCellValue('AH2','Grad odredišta')
              ->setCellValue('AI2','Napomena')

            ->setCellValue('AJ1','Dodavanje smještaja')
              ->setCellValue('AJ2','Smještaj')
              ->setCellValue('AK2','Početak datum')
              ->setCellValue('AL2','Kraj datum')
              ->setCellValue('AM2','Početak vrijeme')
              ->setCellValue('AN2','Kraj vrijeme')
              ->setCellValue('AO2','Odredišna država')
              ->setCellValue('AP2','Odredišni grad')
              ->setCellValue('AQ2','Naziv/Adresa')
            
            ->setCellValue('AR1','Postavljanje osiguranja-vize')
              ->setCellValue('AR2','Osiguranje')
              ->setCellValue('AS2','Početak datum')
              ->setCellValue('AT2','Kraj datum')
              ->setCellValue('AU2','Početak vrijeme')
              ->setCellValue('AV2','Kraj vrijeme')
              ->setCellValue('AW2','Dokument (pasoš) broj')
              ->setCellValue('AX2','Viza')
              ->setCellValue('AY2','Napomena')

            ->setCellValue('AZ1','Kratak opis zadatka (poslova) koji je obavljen na službenom putu')
            ->setCellValue('BA1','Specifikacija troškova')
            ->setCellValue('BB1','Izjava da li je bila osigurana ishrana na službenom putu (tri obroka)')

            ->setCellValue('BC1','Troškovi prevoza')
              ->setCellValue('BC2','Trošak 1')
              ->setCellValue('BD2','Količina 1')
              ->setCellValue('BE2','Iznos 1')
              ->setCellValue('BF2','Ukupno')
              ->setCellValue('BG2','Trošak 2')
              ->setCellValue('BH2','Količina 2')
              ->setCellValue('BI2','Iznos 2')
              ->setCellValue('BJ2','Ukupno')
              ->setCellValue('BK2','Količina goriva ')
              ->setCellValue('BL2','Iznos KM')

            ->setCellValue('BM1','Izdaci za noćenje')
              ->setCellValue('BM2','Naziv 1')
              ->setCellValue('BN2','Količina 1')
              ->setCellValue('BO2','Iznos 1')
              ->setCellValue('BP2','Ukupno')
              ->setCellValue('BQ2','Naziv 2')
              ->setCellValue('BR2','Količina 2')
              ->setCellValue('BS2','Iznos 2')
              ->setCellValue('BT2','Ukupno')
              ->setCellValue('BU2','Naziv 3')
              ->setCellValue('BV2','Količina 3')
              ->setCellValue('BW2','Iznos 3')
              ->setCellValue('BX2','Ukupno')

            ->setCellValue('BY1','Ostali troškovi')
              ->setCellValue('BY2','Naziv troška 1')
              ->setCellValue('BZ2','Količina 1')
              ->setCellValue('CA2','Iznos 1')
              ->setCellValue('CB2','Ukupno')
              ->setCellValue('CC2','Naziv troška2')
              ->setCellValue('CD2','Količina 2')
              ->setCellValue('CE2','Iznos 2')
              ->setCellValue('CF2','Ukupno')
              ->setCellValue('CG2','Naziv troška3')
              ->setCellValue('CH2','Količina 3')
              ->setCellValue('CI2','Iznos 3')
              ->setCellValue('CJ2','Ukupno')
              ->setCellValue('CK2','Naziv troška4')
              ->setCellValue('CL2','Količina 4')
              ->setCellValue('CM2','Iznos 4')
              ->setCellValue('CN2','Ukupno')
              ->setCellValue('CO2','Naziv troška5')
              ->setCellValue('CP2','Količina 5')
              ->setCellValue('CQ2','Iznos 5')
              ->setCellValue('CR2','Ukupno')
              ->setCellValue('CS2','Naziv troška6')
              ->setCellValue('CT2','Količina 6')
              ->setCellValue('CU2','Iznos 6')
              ->setCellValue('CV2','Ukupno')
;

			  foreach ($data as $podatak_ex) {
        $br=$cell_broj++;
        $statuss = 'Na obradi';

        if($podatak_ex['operation'] == 'obrada' or $podatak_ex['operation'] == 'odobravanje') {$statuss = 'Na obradi';}
        elseif ($podatak_ex['operation'] == 'odbijanje') {$statuss = 'Poslano na korekciju';}
        if ($podatak_ex['lock'] == 1 ) $statuss = 'Zaključano';
        if ($podatak_ex['canceled'] and $podatak_ex['svrha'] != 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE') $statuss = 'Otkazano';
            

      if ($podatak_ex['dacheck'] == "DA") {
        $jelidailine = 'DA';
      }elseif ($podatak_ex['necheck'] == "NE") {
         $jelidailine = 'NE';
      }else{$jelidailine = '';};
preg_match('/([0-9]*(\.[0-9]+)?) KM x ([0-9]*(\%)?)/', $podatak_ex['iznos_gorivo'], $matches, PREG_OFFSET_CAPTURE);

//TC

$created_at = date("Y-m-d",$podatak_ex['created_at']);

$tcq = $db->query("
SELECT top 1 [Dimension  Name],[Dimension Value Code],[Employee No_],e.[Position Description]
  FROM [NAV RAIFFEISEN].[dbo].[RAIFFEISEN INVEST".'$Employee'." Contract Ledger] as e
  join [NAV RAIFFEISEN].[dbo].[RAIFFEISEN INVEST".'$Dimension'." for position] as d
  on 
 e.[Position Code]=d.[Position Code] and e.[Position Description]=d.[Position Description] and e.[Department Name]=d.[Org Belongs] and e.[Org_ Structure]=d.[ORG Shema]
  where [Show Record]=1 and [Employee No_]=".$podatak_ex['employee_no']." and [Starting Date]<='".$created_at." 00:00:00.000' and ([Ending Date]>='".$created_at." 00:00:00.000' or [Ending Date]='1753-01-01 00:00:00.000') 
  order by [Starting Date] desc
");
$tc = $tcq->fetch(PDO::FETCH_ASSOC);



$doc->setActiveSheetIndex(0)->setCellValue('A'.$br.'', $podatak_ex['sl_put_id']);
$doc->setActiveSheetIndex(0)->setCellValue('B'.$br.'', $podatak_ex['employee_no']);
$doc->setActiveSheetIndex(0)->setCellValue('C'.$br.'', $podatak_ex['fname'].' '.$podatak_ex['lname']);
$doc->setActiveSheetIndex(0)->setCellValue('D'.$br.'', $podatak_ex['JMB']);
$doc->setActiveSheetIndex(0)->setCellValue('E'.$br.'', $tc['Dimension Value Code']);
$doc->setActiveSheetIndex(0)->setCellValue('F'.$br.'', $podatak_ex['iznos_akontacije']);
$doc->setActiveSheetIndex(0)->setCellValue('G'.$br.'', $podatak_ex['Bank Account No_']);
$doc->setActiveSheetIndex(0)->setCellValue('H'.$br.'', $statuss);
$doc->setActiveSheetIndex(0)->setCellValue('I'.$br.'', date("d.m.Y",$podatak_ex['created_at']));
$doc->setActiveSheetIndex(0)->setCellValue('J'.$br.'', $podatak_ex['svrha']);
$doc->setActiveSheetIndex(0)->setCellValue('K'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_pocetak_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('L'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_kraj_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('M'.$br.'', date("d.m.Y",strtotime($podatak_ex['pocetak_vrijeme'])));
$doc->setActiveSheetIndex(0)->setCellValue('N'.$br.'', date("d.m.Y",strtotime($podatak_ex['kraj_vrijeme'])));
$doc->setActiveSheetIndex(0)->setCellValue('O'.$br.'', $podatak_ex['polazna_drzava1']);
$doc->setActiveSheetIndex(0)->setCellValue('P'.$br.'', $podatak_ex['grad_polaska']);
$doc->setActiveSheetIndex(0)->setCellValue('Q'.$br.'', $podatak_ex['drzava1']);
$doc->setActiveSheetIndex(0)->setCellValue('R'.$br.'', $podatak_ex['odredisni_grad']);
$doc->setActiveSheetIndex(0)->setCellValue('S'.$br.'', $podatak_ex['drzava2']);
$doc->setActiveSheetIndex(0)->setCellValue('T'.$br.'', $podatak_ex['odredisni_grad2']);
$doc->setActiveSheetIndex(0)->setCellValue('U'.$br.'', $podatak_ex['razlog_putovanja']);
$doc->setActiveSheetIndex(0)->setCellValue('V'.$br.'', $podatak_ex['napomena']);

$doc->setActiveSheetIndex(0)->setCellValue('W'.$br.'', $podatak_ex['iznos_akontacije']);
$doc->setActiveSheetIndex(0)->setCellValue('X'.$br.'', $podatak_ex['valuta']);
$doc->setActiveSheetIndex(0)->setCellValue('Y'.$br.'', date("d.m.Y",strtotime($podatak_ex['datum_akontacije'])));
$doc->setActiveSheetIndex(0)->setCellValue('Z'.$br.'', $podatak_ex['primanje_sredstva']);
$doc->setActiveSheetIndex(0)->setCellValue('AA'.$br.'', $podatak_ex['akontacija_napomena']);

$doc->setActiveSheetIndex(0)->setCellValue('AB'.$br.'', $podatak_ex['vrsta_transporta']);
$doc->setActiveSheetIndex(0)->setCellValue('AC'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_pocetak_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AD'.$br.'', date("d.m.Y",strtotime($podatak_ex['transport_kraj_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AE'.$br.'', $podatak_ex['transport_pocetak_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AF'.$br.'', $podatak_ex['transport_kraj_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AG'.$br.'', $podatak_ex['T_polazna_drzava']);
$doc->setActiveSheetIndex(0)->setCellValue('AH'.$br.'', $podatak_ex['transport_grad_polaska']);
$doc->setActiveSheetIndex(0)->setCellValue('AI'.$br.'', $podatak_ex['T_odredisna_drzava']);
$doc->setActiveSheetIndex(0)->setCellValue('AJ'.$br.'', $podatak_ex['transport_odredisni_grad']);
$doc->setActiveSheetIndex(0)->setCellValue('AK'.$br.'', $podatak_ex['transport_napomena']);

$doc->setActiveSheetIndex(0)->setCellValue('AL'.$br.'', $podatak_ex['vrsta_smjestaja']);
$doc->setActiveSheetIndex(0)->setCellValue('AM'.$br.'', date("d.m.Y",strtotime($podatak_ex['smjestaj_pocetak_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AN'.$br.'', date("d.m.Y",strtotime($podatak_ex['smjestaj_kraj_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AO'.$br.'', $podatak_ex['smjestaj_pocetak_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AP'.$br.'', $podatak_ex['smjestaj_kraj_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AQ'.$br.'', $podatak_ex['S_odredisna_drzava']);
$doc->setActiveSheetIndex(0)->setCellValue('AR'.$br.'', $podatak_ex['smjestaj_grad']);
$doc->setActiveSheetIndex(0)->setCellValue('AS'.$br.'', $podatak_ex['smjestaj_adresa']);

$doc->setActiveSheetIndex(0)->setCellValue('AT'.$br.'', $podatak_ex['osiguranje']);
$doc->setActiveSheetIndex(0)->setCellValue('AU'.$br.'', date("d.m.Y",strtotime($podatak_ex['osiguranje_pocetak_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AV'.$br.'', date("d.m.Y",strtotime($podatak_ex['osiguranje_kraj_datum'])));
$doc->setActiveSheetIndex(0)->setCellValue('AW'.$br.'', $podatak_ex['osiguranje_pocetak_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AX'.$br.'', $podatak_ex['osiguranje_kraj_vrijeme']);
$doc->setActiveSheetIndex(0)->setCellValue('AY'.$br.'', $podatak_ex['dokument_broj']);
$doc->setActiveSheetIndex(0)->setCellValue('AZ'.$br.'', $podatak_ex['viza']);
$doc->setActiveSheetIndex(0)->setCellValue('BA'.$br.'', $podatak_ex['osiguranje_napomena']);

$doc->setActiveSheetIndex(0)->setCellValue('BB'.$br.'', $podatak_ex['ost_kratkiopis']);
$doc->setActiveSheetIndex(0)->setCellValue('BC'.$br.'', $podatak_ex['ost_specopis']);
$doc->setActiveSheetIndex(0)->setCellValue('BD'.$br.'', $jelidailine);

$doc->setActiveSheetIndex(0)->setCellValue('BE'.$br.'', $podatak_ex['trosak1']);
$doc->setActiveSheetIndex(0)->setCellValue('BF'.$br.'', $podatak_ex['kolicina1']);
$doc->setActiveSheetIndex(0)->setCellValue('BG'.$br.'', $podatak_ex['iznos1']);
$doc->setActiveSheetIndex(0)->setCellValue('BH'.$br.'', "=BD$br*BE$br");
$doc->setActiveSheetIndex(0)->setCellValue('BI'.$br.'', $podatak_ex['trosak2']);
$doc->setActiveSheetIndex(0)->setCellValue('BJ'.$br.'', $podatak_ex['kolicina2']);
$doc->setActiveSheetIndex(0)->setCellValue('BK'.$br.'', $podatak_ex['iznos2']);
$doc->setActiveSheetIndex(0)->setCellValue('BL'.$br.'', "=BH$br*BI$br");
$doc->setActiveSheetIndex(0)->setCellValue('BM'.$br.'', $podatak_ex['kol_gorivo']);
if ($podatak_ex['kol_gorivo'] and $podatak_ex['iznos_gorivo'])
$doc->setActiveSheetIndex(0)->setCellValue('BN'.$br.'', $podatak_ex['kol_gorivo'] ? round($podatak_ex['kol_gorivo']*$matches[1][0]*($matches[3][0]/100),2) : '0');
else
$doc->setActiveSheetIndex(0)->setCellValue('BN'.$br.'', '0');


$doc->setActiveSheetIndex(0)->setCellValue('BO'.$br.'', $podatak_ex['izdaci_naziv1']);
$doc->setActiveSheetIndex(0)->setCellValue('BP'.$br.'', $podatak_ex['izdaci_kol1']);
$doc->setActiveSheetIndex(0)->setCellValue('BQ'.$br.'', $podatak_ex['izdaci_iznos1']);
$doc->setActiveSheetIndex(0)->setCellValue('BR'.$br.'', "=BN$br*BO$br");
$doc->setActiveSheetIndex(0)->setCellValue('BS'.$br.'', $podatak_ex['izdaci_naziv2']);
$doc->setActiveSheetIndex(0)->setCellValue('BT'.$br.'', $podatak_ex['izdaci_kol2']);
$doc->setActiveSheetIndex(0)->setCellValue('BU'.$br.'', $podatak_ex['izdaci_iznos2']);
$doc->setActiveSheetIndex(0)->setCellValue('BV'.$br.'', "=BR$br*BS$br");
$doc->setActiveSheetIndex(0)->setCellValue('BW'.$br.'', $podatak_ex['izdaci_naziv3']);
$doc->setActiveSheetIndex(0)->setCellValue('BX'.$br.'', $podatak_ex['izdaci_kol3']);
$doc->setActiveSheetIndex(0)->setCellValue('BY'.$br.'', $podatak_ex['izdaci_iznos3']);
$doc->setActiveSheetIndex(0)->setCellValue('BZ'.$br.'', "=BV$br*BW$br");

$doc->setActiveSheetIndex(0)->setCellValue('CA'.$br.'', $podatak_ex['ost_trosak1']);
$doc->setActiveSheetIndex(0)->setCellValue('CB'.$br.'', $podatak_ex['ost_kolicina1']);
$doc->setActiveSheetIndex(0)->setCellValue('CC'.$br.'', $podatak_ex['ost_iznos1']);
$doc->setActiveSheetIndex(0)->setCellValue('CD'.$br.'', "=BZ$br*CA$br");
$doc->setActiveSheetIndex(0)->setCellValue('CE'.$br.'', $podatak_ex['ost_trosak2']);
$doc->setActiveSheetIndex(0)->setCellValue('CF'.$br.'', $podatak_ex['ost_kolicina2']);
$doc->setActiveSheetIndex(0)->setCellValue('CG'.$br.'', $podatak_ex['ost_iznos2']);
$doc->setActiveSheetIndex(0)->setCellValue('CH'.$br.'', "=CD$br*CE$br");
$doc->setActiveSheetIndex(0)->setCellValue('CI'.$br.'', $podatak_ex['ost_trosak3']);
$doc->setActiveSheetIndex(0)->setCellValue('CJ'.$br.'', $podatak_ex['ost_kolicina3']);
$doc->setActiveSheetIndex(0)->setCellValue('CK'.$br.'', $podatak_ex['ost_iznos3']);
$doc->setActiveSheetIndex(0)->setCellValue('CL'.$br.'', "=CH$br*CI$br");
$doc->setActiveSheetIndex(0)->setCellValue('CM'.$br.'', $podatak_ex['ost_trosak4']);
$doc->setActiveSheetIndex(0)->setCellValue('CN'.$br.'', $podatak_ex['ost_kolicina4']);
$doc->setActiveSheetIndex(0)->setCellValue('CO'.$br.'', $podatak_ex['ost_iznos4']);
$doc->setActiveSheetIndex(0)->setCellValue('CP'.$br.'', "=CL$br*CM$br");
$doc->setActiveSheetIndex(0)->setCellValue('CQ'.$br.'', $podatak_ex['ost_trosak5']);
$doc->setActiveSheetIndex(0)->setCellValue('CR'.$br.'', $podatak_ex['ost_kolicina5']);
$doc->setActiveSheetIndex(0)->setCellValue('CS'.$br.'', $podatak_ex['ost_iznos5']);
$doc->setActiveSheetIndex(0)->setCellValue('CT'.$br.'', "=CQ$br*CP$br");
$doc->setActiveSheetIndex(0)->setCellValue('CU'.$br.'', $podatak_ex['ost_trosak6']);
$doc->setActiveSheetIndex(0)->setCellValue('CV'.$br.'', $podatak_ex['ost_kolicina6']);
$doc->setActiveSheetIndex(0)->setCellValue('CW'.$br.'', $podatak_ex['ost_iznos6']);
$doc->setActiveSheetIndex(0)->setCellValue('CX'.$br.'', "=CU$br*CT$br");

}

//stil
$styleArray = array(
   'font'  => array(
        'size'  => 9,
        'name'  => 'Tahoma'
    ));      
$doc->getDefaultStyle()->applyFromArray($styleArray);
$doc->getActiveSheet()->getStyle('A1:DD256')->getAlignment()->setWrapText(true);
for ($i = 'A'; $i !== 'DD'; $i++){
  $doc->getActiveSheet()->getColumnDimension($i)->setWidth(18);
}
$doc->getActiveSheet()->getRowDimension('1')->setRowHeight(15);   
$doc->getActiveSheet()->getRowDimension('2')->setRowHeight(25); 
$doc->getActiveSheet()->getStyle("A1:DD2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


//merge
$doc->getActiveSheet()->mergeCells("A1:A2");
$doc->getActiveSheet()->mergeCells("B1:B2");
$doc->getActiveSheet()->mergeCells("C1:C2");
$doc->getActiveSheet()->mergeCells("D1:D2");
$doc->getActiveSheet()->mergeCells("E1:E2");
$doc->getActiveSheet()->mergeCells("F1:F2");
$doc->getActiveSheet()->mergeCells("G1:G2");
$doc->getActiveSheet()->mergeCells("H1:H2");
$doc->getActiveSheet()->mergeCells("I1:T1");
$doc->getActiveSheet()->mergeCells("U1:Y1");
$doc->getActiveSheet()->mergeCells("Z1:AI1");
$doc->getActiveSheet()->mergeCells("AJ1:AQ1");
$doc->getActiveSheet()->mergeCells("AR1:AY1");
$doc->getActiveSheet()->mergeCells("AZ1:AZ2");
$doc->getActiveSheet()->mergeCells("BA1:BA2");
$doc->getActiveSheet()->mergeCells("BB1:BB2");
$doc->getActiveSheet()->mergeCells("BC1:BL1");
$doc->getActiveSheet()->mergeCells("BM1:BX1");
$doc->getActiveSheet()->mergeCells("BY1:CV1");

//linije
$boxes = ["I1:T$br","BY1:CV$br","BM1:BX$br","BC1:BL$br","AR1:AY$br","AJ1:AQ$br","Z1:AI$br","U1:Y$br","I1:T1","BY1:CV1","BM1:BX1","BC1:BL1","AR1:AY1","AJ1:AQ1","Z1:AI1","U1:Y1","A1:CV2"];
foreach ($boxes as $key => $value) {
  $doc->getActiveSheet()->getStyle($value)->applyFromArray(
      array(
          'borders' => array(
              'outline' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('rgb' => '000')
              )
          )
      )
  );
}






$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

$objWriter->save('php://output');

?>