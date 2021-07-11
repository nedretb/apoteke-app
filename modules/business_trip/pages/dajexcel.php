<?php
error_reporting(1);

function secondsToTime($inputSeconds)
{

    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    if ($minutes < 10) $minutes = '0' . $minutes;

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int)$days,
        'h' => (int)$hours,
        'm' => $minutes,
        's' => (int)$seconds,
    );
    return $obj;
}

$podaci_excel = $db->query("
    SELECT *  FROM [c0_intranet2_apoteke].[dbo].[hourlyrate_day] as table1
  INNER JOIN [c0_intranet2_apoteke].[dbo].[sl_put] as table2
  ON table1.id = table2.request_id 
  inner join [c0_intranet2_apoteke].[dbo].[users] as table3
  ON table1.user_id = table3.user_id
  inner join [c0_intranet2_apoteke].[dbo].[sl_put_ostali_info] as table4
  ON table2.id = table4.sl_put_id_fk
  left join [c0_intranet2_apoteke].[dbo].[countries] as c
  ON table2.odredisna_drzava =  c.country_id
  where table2.id = " . $_GET['sl_put_id'] . "
  ");
$data = $podaci_excel->fetch();

$kvv = $data['kraj_vrijeme'];
if (strlen($data['pocetak_vrijeme']) < 1) {
    $data['pocetak_vrijeme'] = '00:00';
    $dnevnicaa = 0;
}
if (strlen($data['kraj_vrijeme']) < 1) {
    $data['kraj_vrijeme'] = '00:00';
    $dnevnicaa = 0;
}

//dr drzava
if ($data['odredisna_drzava2'] != '' and ctype_space($data['odredisna_drzava2']) == false) {
    $dr_drzava = $db->query("
    SELECT * from [c0_intranet2_apoteke].[dbo].[countries]
  where country_id = " . $data['odredisna_drzava2'] . "
  ");
    $dr_drzava = $dr_drzava->fetch();
} else $dr_drzava = null;


// Izračunajmo broj stanica
$first = strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'] . ':00');
$second = strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme'] . ':00');

$seconds = $second - $first;
$time = secondsToTime($seconds);

$dana = $time['d'];
$sati = $time['h'];

if ($sati < 8) $dnevnica = 0;
else if ($sati >= 8 and $sati < 12) $dnevnica = 0.5;
else if ($sati >= 12 and $sati < 24) $dnevnica = 1;

$dnevnica = $dana + $dnevnica;

if ($data['kraj_datum2']) {
    $kd = $data['kraj_datum'];
    //I obracn
    $poc = strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'] . ':00');
    $kraj = strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme'] . ':00');
    $trajanje_sati1 = ($kraj - $poc) / 3600; //sati
    //II obracun
    $poc2 = strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme'] . ':00');
    $kraj2 = strtotime($data['kraj_datum2'] . ' ' . $data['kraj_vrijeme2'] . ':00');
    $trajanje_sati2 = ($kraj2 - $poc2) / 3600; //sati


    $time = secondsToTime($kraj2 - $poc);

    $dana = $time['d'];
    $sati = $time['h'];

    if ($sati < 8) $dnevnicaa = 0;
    else if ($sati >= 8 and $sati < 12) $dnevnicaa = 0.5;
    else if ($sati >= 12 and $sati < 24) $dnevnicaa = 1;

    $faktor = $dana + $dnevnicaa;

    $dnevnica = round(($trajanje_sati1 / ($trajanje_sati1 + $trajanje_sati2)) * $faktor, 2);
    $dnevnica2 = round(($trajanje_sati2 / ($trajanje_sati1 + $trajanje_sati2)) * $faktor, 2);

    $data['kraj_datum'] = $data['kraj_datum2'];
    $data['kraj_vrijeme'] = $data['kraj_vrijeme2'];
}

if ($dnevnicaa === 0) $dnevnica = 0;

$podaci_excel = $db->query("
    SELECT fname,lname FROM [c0_intranet2_apoteke].[dbo].[users] as table1
 where employee_no= " . $data['parent'] . "
  ");
$parent = $podaci_excel->fetch();

if ($data['odredisna_drzava'] and is_numeric($data['odredisna_drzava'])) {
    $podaci_excel = $db->query("
    SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries] 
    where [country_id] = " . $data['odredisna_drzava'] . "
    ");
    $drzava = $podaci_excel->fetch();
} else {
    $drzava['name'] = null;
    $drzava['wage'] = null;
}
if ($data['odredisna_drzava2'] and is_numeric($data['odredisna_drzava2'])) {
    $podaci_excel = $db->query("
    SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries] 
    where [country_id] = " . $data['odredisna_drzava2'] . "
    ");
    $drzava2 = $podaci_excel->fetch();
} else {
    $drzava2['name'] = null;
    $drzava2['wage'] = null;
}
if ($data['odredisna_drzava3'] and is_numeric($data['odredisna_drzava3'])) {
    $podaci_excel = $db->query("
    SELECT * FROM [c0_intranet2_apoteke].[dbo].[countries] 
    where [country_id] = " . $data['odredisna_drzava3'] . "
    ");
    $drzava3 = $podaci_excel->fetch();
} else {
    $drzava3['name'] = null;
    $drzava3['wage'] = null;
}

$relacija = '';

if (isset($data['grad_polaska']) and $data['grad_polaska'] != '') {
    $relacija .= $data['grad_polaska'];
}
if (isset($data['odredisni_grad']) and $data['odredisni_grad'] != '') {
    $relacija .= ' - ' . $data['odredisni_grad'];
}
if (isset($data['odredisni_grad2']) and $data['odredisni_grad2'] != '') {
    $relacija .= ' - ' . $data['odredisni_grad2'];
}
if (isset($data['grad_polaska']) and $data['grad_polaska'] != '') {
    $relacija .= ' - ' . $data['grad_polaska'];
}

$danaq = $db->query("SELECT top 1 * from [c0_intranet2_apoteke].[dbo].[sl_put_logs] where sl_put_request_id = " . $_GET['sl_put_id'] . " order by id desc");
$dana = $danaq->fetch(PDO::FETCH_ASSOC);

$created_at = date("Y-m-d", $data['created_at']);

require_once($root . '\CORE\PHPExcel-1.8\Classes\PHPExcel.php');
use Carbon\Carbon;
$dana_na_putu = Carbon::parse($data['pocetak_datum'])->diffInDays($data['kraj_datum']);
try{
    $fileName = 'C:\wamp64\www\apoteke-app\CORE\files\excel\slp.xlsx';
    $phpExcel = PHPExcel_IOFactory::load($fileName);
    $sheet = $phpExcel->setActiveSheetIndex(0);

    // -------------------------------------------------------------------------------------------------------------- //
    $sheet->SetCellValue('J4', "Sarajevo, ".date('d.m.Y'));

    $sheet->SetCellValue('I9', $data['fname'].' '.$data['lname']);
    $sheet->SetCellValue('K11', $data['egop_radno_mjesto']);
    $sheet->SetCellValue('O11', $drzava['wage']);
    $sheet->SetCellValue('J12', $drzava['wage']);
    $sheet->SetCellValue('I13', $data['odredisni_grad'].", ".$drzava['name']);
    $sheet->SetCellValue('K15', $dana_na_putu);
    $sheet->SetCellValue('N15', $sati);
    $sheet->SetCellValue('K17', $data['vrsta_transporta']);
    $sheet->SetCellValue('K18', $data['grad_polaska']." - ".$data['odredisni_grad']);


    if($data['odredisni_grad2'] == ''){
        $grad = $data['odredisni_grad'];
        $drz = $drzava;

        $datum_do_  = $data['kraj_datum'];
        $datum_do_v = $data['kraj_vrijeme'];
    }else{
        $grad = $data['odredisni_grad2'];
        $drz  = $drzava2;

        $datum_do_  = $data['kraj_datum2'];
        $datum_do_v = $data['kraj_vrijeme2'];
    }

    $trajanje = Carbon::parse($data['pocetak_datum'])->diffInDays(Carbon::parse($datum_do_)) + 1;



    // -------------------------------------------------------------------------------------------------------------- //

    $sheet = $phpExcel->setActiveSheetIndex(1);


    $datum_i_v_p = Carbon::parse($data['pocetak_datum'].' '.$data['pocetak_vrijeme']);
    $datum_i_v_k = Carbon::parse($datum_do_.' '.$datum_do_v);

    $razlika = $datum_i_v_p->diff($datum_i_v_k);

    /***** troskovi prevoza ******/
    $sheet->SetCellValue('I9', $data['ost_trosak1']);
    $sheet->SetCellValue('N9', $data['ost_kolicina1']*$data['ost_iznos1']);

    $sheet->SetCellValue('I10', $data['ost_trosak2']);
    $sheet->SetCellValue('N10', $data['ost_kolicina2']*$data['ost_iznos2']);

    $sheet->SetCellValue('I11', $data['ost_trosak3']);
    $sheet->SetCellValue('N11', $data['ost_kolicina3']*$data['ost_iznos3']);

    $sheet->SetCellValue('I12', $data['ost_trosak4']);
    $sheet->SetCellValue('N12', $data['ost_kolicina4']*$data['ost_iznos4']);

    $sheet->SetCellValue('I13', $data['ost_trosak5']);
    $sheet->SetCellValue('N13', $data['ost_kolicina5']*$data['ost_iznos5']);

    /***** dnevnice ******/
    $sheet->SetCellValue('I15', $dnevnica);
    $sheet->SetCellValue('M15', $dnevnica*$drzava['wage']);

    $sheet->SetCellValue('I16', $dnevnica);
    $sheet->SetCellValue('M16', $dnevnica*$drzava['wage']/2);

    $sheet->SetCellValue('L17', $data['postotak_na_dnevnicu']);

    $sheet->SetCellValue('P15', $dnevnica*$drzava['wage']*($data['postotak_na_dnevnicu']/100));
    $sheet->SetCellValue('P16', $dnevnica*$drzava['wage']*($data['postotak_na_dnevnicu']/100)/2);

    /***** ostali izdaci ******/
    $sheet->SetCellValue('I19', $data['ost_trosak1']);
    $sheet->SetCellValue('N19', $data['ost_kolicina1']*$data['ost_iznos1']);

    $sheet->SetCellValue('I20', $data['ost_trosak2']);
    $sheet->SetCellValue('N20', $data['ost_kolicina2']*$data['ost_iznos2']);

    $sheet->SetCellValue('I21', $data['ost_trosak3']);
    $sheet->SetCellValue('N21', $data['ost_kolicina3']*$data['ost_iznos3']);

    /***** akontacija ******/
    $sheet->SetCellValue('P24', $data['iznos_akontacije']);
    $sheet->SetCellValue('P25', $data['iznos_akontacije']/2);


    $writer = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");
    $writer->save('C:\wamp64\www\apoteke-app\CORE\files\excel\slp2.xlsx');


    header('Location: CORE/files/excel/slp2.xlsx');

//    var_dump($data);
//
//    return;
//    $fileType = 'Excel5';
//
//
//// Read the file
//    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
//    $objPHPExcel = $objReader->load($fileName);
//
//    $objWorksheet = $objPHPExcel->getActiveSheet();
//
//// Change the file
//    $objPHPExcel->setActiveSheetIndex(0)
//        ->setCellValue('B5', 'Hello');
//
//    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel);
//    $objWriter->save($fileName);
}catch (\Exception $e){
    var_dump($e);

    die();
}

/*
$tcq = $db->query("
SELECT top 1 [Dimension  Name],[Dimension Value Code],[Employee No_],e.[Position Description]
  FROM [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$Employee' . " Contract Ledger] as e
  join [NAV RAIFFEISEN].[dbo].[RAIFFAISEN BANK" . '$Dimension' . " for position] as d
  on
 e.[Position Code]=d.[Position Code] and e.[Position Description]=d.[Position Description] and e.[Department Name]=d.[Org Belongs] and e.[Org_ Structure]=d.[ORG Shema]
  where [Show Record]=1 and [Employee No_]=" . $data['employee_no'] . " and [Starting Date]<='" . $created_at . " 00:00:00.000' and ([Ending Date]>='" . $created_at . " 00:00:00.000' or [Ending Date]='1753-01-01 00:00:00.000')
  order by [Starting Date] desc
");
$tc = $tcq->fetch(PDO::FETCH_ASSOC);
preg_match('/([0-9]*(\.[0-9]+)?) KM x ([0-9]*(\%)?)/', $data['iznos_gorivo'], $matches, PREG_OFFSET_CAPTURE);
require_once($root . '\CORE\PHPExcel-1.8\Classes\PHPExcel.php');
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Excel_nalog_" . date("d.m.Y") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
flush();
$doc = new PHPExcel();
ob_clean();
$doc->setActiveSheetIndex(0)
    ->setCellValue('A1', 'IZVJEŠTAJ')
    ->setCellValue('A2', 'SA OBAVLJENOG SLUŽBENOG PUTA')
    ->setCellValue('A3', 'Ime i prezime službenika:')
    ->setCellValue('A4', 'Pozicija službenika: ')
    ->setCellValue('A5', 'Broj i datum naloga kojim je odobreno službeno putovanje:')
    ->setCellValue('A7', 'Datum i vrijeme polaska na službeno putovanje:')
    ->setCellValue('A9', 'Datum i vrijeme povratka sa službenog putovanja:')
    ->setCellValue('A11', 'Datum i vrijeme početka službenog zadatka (posla) kojeg je obavio za svaku državu u koju je upućen: ')
    ->setCellValue('A15', 'Datum i vrijeme završetka službenog zadatka (posla) kojeg je obavio za svaku državu u koju je upućen: ')
    ->setCellValue('A19', 'Kratak opis zadatka (poslova) koji je obavljen na službenom putu:')
    ->setCellValue('A20', $data['ost_kratkiopis'])
    ->setCellValue('E5', 'Broj naloga')
    ->setCellValue('E6', 'Datum naloga')
    ->setCellValue('E7', 'Datum polaska')
    ->setCellValue('E8', 'Vrijeme polaska')
    ->setCellValue('E9', 'Datum povratka')
    ->setCellValue('E10', 'Vrijeme povratka')
    ->setCellValue('E11', 'Datum početka')
    ->setCellValue('E12', 'Vrijeme početka')
    ->setCellValue('E13', 'Datum početka')
    ->setCellValue('E14', 'Vrijeme početka')
    ->setCellValue('E15', 'Datum završetka')
    ->setCellValue('E16', 'Vrijeme završetka')
    ->setCellValue('E17', 'Datum završetka')
    ->setCellValue('A36', 'Izjava da li je bila osigurana ishrana na službenom putu (tri obroka)')
    ->setCellValue('F36', 'DA')
    ->setCellValue('H36', 'NE')
    ->setCellValue('A37', 'Navesti specifikaciju nastalih troškova koja sadrži naziv, vrstu i iznos troškova nastalih u svrhu službenog putovanja za koje se prilažu računi')
    ->setCellValue('C37', $data['ost_specopis'])
    ->setCellValue('A39', 'IZVJEŠTAJ PODNIO')
    ->setCellValue('C39', 'DANA:')
    ->setCellValue('F39', 'IZVJEŠTAJ ODOBRIO')
    ->setCellValue('E18', 'Vrijeme')
    ->setCellValue('C3', $data['fname'] . ' ' . $data['lname'])
    ->setCellValue('A41', $data['fname'] . ' ' . $data['lname'])
    ->setCellValue('C4', $data['position'])
    ->setCellValue('G5', $data['id'])
    ->setCellValue('C40', date("d.m.Y", $dana['vrijeme']))
    ->setCellValue('G7', date("d.m.Y", strtotime($data['pocetak_datum'])))
    ->setCellValue('G8', $data['pocetak_vrijeme'])
    ->setCellValue('G9', date("d.m.Y", strtotime($data['kraj_datum'])))
    ->setCellValue('G10', $data['kraj_vrijeme'])
    ->setCellValue('G6', date("d.m.Y", $data['created_at']))
    ->setCellValue('G11', date("d.m.Y", strtotime($data['pocetak_datum'])))
    ->setCellValue('G12', $data['pocetak_vrijeme'])
    ->setCellValue('G13', $kd ? date("d.m.Y", strtotime($kd)) : '')
    ->setCellValue('G14', $kvv)
    ->setCellValue('G15', $kd ? date("d.m.Y", strtotime($kd)) : date("d.m.Y", strtotime($data['kraj_datum'])))
    ->setCellValue('G16', $kvv)
    ->setCellValue('G17', $data['kraj_datum2'] ? date("d.m.Y", strtotime($data['kraj_datum2'])) : '')
    ->setCellValue('G18', $data['kraj_vrijeme2'])
    ->setCellValue('K2', $tc['Dimension Value Code'])
    ->setCellValue('N19', "='Worksheet 1'!H25")
    ->setCellValue('K20', $data['kategorija_hotela']);
$doc->getActiveSheet()
    ->setCellValue(
        'N19',
        "='Worksheet 1'!H25"
    );
$doc->getActiveSheet()->getColumnDimension('A')->setWidth(16.7);
$doc->getActiveSheet()->getColumnDimension('B')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('C')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('D')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('E')->setWidth(6.9);
$doc->getActiveSheet()->getColumnDimension('F')->setWidth(6.9);
$doc->getActiveSheet()->getColumnDimension('G')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('H')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('J')->setWidth(9.2);
$doc->getActiveSheet()->getColumnDimension('K')->setWidth(9.2);
$doc->getActiveSheet()->getColumnDimension('L')->setWidth(9.2);
$doc->getActiveSheet()->getColumnDimension('M')->setWidth(9.5);
$doc->getActiveSheet()->mergeCells("A1:H1");
$doc->getActiveSheet()->mergeCells("A2:H2");
$doc->getActiveSheet()->mergeCells("A3:B3");
$doc->getActiveSheet()->mergeCells("A4:B4");
$doc->getActiveSheet()->mergeCells("A5:D6");
$doc->getActiveSheet()->mergeCells("A7:D8");
$doc->getActiveSheet()->mergeCells("A9:D10");
$doc->getActiveSheet()->mergeCells("A9:D10");
$doc->getActiveSheet()->mergeCells("A11:D14");
$doc->getActiveSheet()->mergeCells("A15:D18");
$doc->getActiveSheet()->mergeCells("A19:H19");
$doc->getActiveSheet()->mergeCells("A20:H35");
for ($i = 5; $i <= 18; $i++) {
    $doc->getActiveSheet()->mergeCells("E$i:F$i");
    $doc->getActiveSheet()->mergeCells("G$i:H$i");
}
$doc->getActiveSheet()->mergeCells("C3:H3");
$doc->getActiveSheet()->mergeCells("C4:H4");
$doc->getActiveSheet()->mergeCells("A36:E36");
$doc->getActiveSheet()->mergeCells("F36:G36");
$doc->getActiveSheet()->mergeCells("A37:B38");
$doc->getActiveSheet()->mergeCells("C37:H38");
$doc->getActiveSheet()->mergeCells("F39:H39");
$doc->getActiveSheet()->mergeCells("F40:H40");
$doc->getActiveSheet()->getStyle('A1:K100')->getAlignment()->setWrapText(true);
$doc->getActiveSheet()->getStyle('A1:K100')->getFont()
    ->setName('Arial')
    ->setSize(9);
$doc->getActiveSheet()->getStyle('A20')->getFont()
    ->setSize(8);
$doc->getActiveSheet()->getStyle('A1:Z111')->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("A1:H41")->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A1:H1')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A2:H2')->applyFromArray(
    array(
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A39:H41')->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_NONE
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A41:H41')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('H39:H41')->applyFromArray(
    array(
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A39:A41')->applyFromArray(
    array(
        'borders' => array(
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A40')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('C40:D40')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('F40:G40')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK
            )
        )
    )
);
if ($data['dacheck'] == "DA") {
    $doc->getActiveSheet()->getStyle("F36")->getFont()->setBold(true);
    $doc->getActiveSheet()->getStyle('F36')
        ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $doc->getActiveSheet()->getStyle('F36')
        ->getFill()->getStartColor()->setARGB('FF808080');
    $doc->getActiveSheet()->getStyle('F36')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK);
} else {
    $doc->getActiveSheet()->getStyle("F36")->getFont()->setStrikethrough(true);
};
if ($data['necheck'] == "NE") {
    $doc->getActiveSheet()->getStyle("H36")->getFont()->setBold(true);
    $doc->getActiveSheet()->getStyle('H36')
        ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $doc->getActiveSheet()->getStyle('H36')
        ->getFill()->getStartColor()->setARGB('FF808080');
    $doc->getActiveSheet()->getStyle('H36')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
} else {
    $doc->getActiveSheet()->getStyle("H36")->getFont()->setStrikethrough(true);
};
$doc->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$doc->getActiveSheet()->getStyle("A1")->getFont()->setSize(11);
$doc->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$doc->getActiveSheet()->getStyle("A3:A19")->getFont()->setBold(true);
$doc->getActiveSheet()->getStyle("A2:H19")->getFont()->setSize(9);
$doc->getActiveSheet()->getStyle('A5:A18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$doc->getActiveSheet()->getStyle('A19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$doc->getActiveSheet()->getStyle('A20:H35')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$doc->getActiveSheet()->getStyle('C37:H38')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$doc->getActiveSheet()->getStyle("E5:E18")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("A36")->getFont()->setSize(7);
$doc->getActiveSheet()->getStyle('F36:H36')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$doc->getActiveSheet()->getStyle("F36:H36")->getFont()->setBold(true);
$doc->getActiveSheet()->getStyle("A37")->getFont()->setSize(5);
$doc->getActiveSheet()->getStyle("C37:H38")->getFont()->setSize(5);
$doc->getActiveSheet()->getStyle("A39:G39")->getFont()->setBold(true);
//desna strana
$doc->setActiveSheetIndex(0)
    ->setCellValue('J1', 'JMBG')
    ->setCellValue('J2', 'TC')
    ->setCellValue('O4', 'Broj:')
    ->setCellValue('O5', 'Datum')
    ->setCellValue('J7', 'PUTNI NALOG ZA SLUŽBENO PUTOVANJE')
    ->setCellValue('J9', 'Kojim se službenik')
    ->setCellValue('J11', 'raspoređen na poziciju')
    ->setCellValue('J13', 'upućuje na službeno putovanje u')
    ->setCellValue('J15', 'u svrhu')
    ->setCellValue('J17', 'Službeno putovanje će trajati od')
    ->setCellValue('N17', 'do')
    ->setCellValue('P17', 'godine')
    ->setCellValue('J19', 'Odobravaju se troškovi za noćenje u iznosu od')
    ->setCellValue('P19', 'KM')
    ->setCellValue('J20', 'U hotelu')
    ->setCellValue('M20', 'kategorije')
    ->setCellValue('J21', 'Odobrava se upotreba ')
    ->setCellValue('O21', 'kao prevoznog sredstva')
    ->setCellValue('J23', 'za službeni put na relaciji')
    ->setCellValue('J25', 'Odobrava se isplata akontacije u iznosu od')
    ->setCellValue('P25', $data['valuta'])
    ->setCellValue('J27', 'Službenik je dužan u roku od 5 dana od dana završetka službenog putovanja sačiniti izvještaj o obavljenom službenom putu, te isti dostaviti zajedno sa ovim putnim nalogom i računima vezanim uz izdatke tokom službenog puta dostaviti izdavaocu putnog naloga. ')
    ->setCellValue('N36', 'M.P.')
    ->setCellValue('O37', '(Potpis)')
    ->setCellValue('K1', $data['JMB'])
    ->setCellValue('P4', $data['id'])
    ->setCellValue('P5', date("d.m.Y", time()))
    ->setCellValue('L9', $data['fname'] . ' ' . $data['lname'])
    ->setCellValue('L11', $data['position'])
    ->setCellValue('M13', $data['name'] . ' ' . $data['odredisni_grad'] . '  ' . $dr_drzava['name'] . ' ' . $data['odredisni_grad2'])
    ->setCellValue('K15', $data['svrha'])
    ->setCellValue('M17', date("d.m.Y", strtotime($data['pocetak_datum'])))
    ->setCellValue('O17', date("d.m.Y", strtotime($data['kraj_datum'])))
    ->setCellValue('N25', $data['iznos_akontacije'])
    ->setCellValue('L21', $data['vrsta_transporta'])
    ->setCellValue('M23', $relacija);
$doc->getActiveSheet()->mergeCells("K1:N1");
$doc->getActiveSheet()->mergeCells("K2:N2");
$doc->getActiveSheet()->mergeCells("J7:P7");
$doc->getActiveSheet()->mergeCells("J9:K9");
$doc->getActiveSheet()->mergeCells("J11:K11");
$doc->getActiveSheet()->mergeCells("J13:L13");
$doc->getActiveSheet()->mergeCells("J17:L17");
$doc->getActiveSheet()->mergeCells("J21:K21");
$doc->getActiveSheet()->mergeCells("J26:M26");
$doc->getActiveSheet()->mergeCells("J19:M19");
$doc->getActiveSheet()->mergeCells("J27:P33");
$doc->getActiveSheet()->mergeCells("O37:P37");
$doc->getActiveSheet()->mergeCells("J23:L23");
$doc->getActiveSheet()->mergeCells("J25:M25");
$doc->getActiveSheet()->mergeCells("L9:P9");
$doc->getActiveSheet()->mergeCells("L11:P11");
$doc->getActiveSheet()->mergeCells("M13:P13");
$doc->getActiveSheet()->mergeCells("N19:O19");
$doc->getActiveSheet()->mergeCells("K20:L20");
$doc->getActiveSheet()->mergeCells("L21:N21");
$doc->getActiveSheet()->mergeCells("M23:P23");
$doc->getActiveSheet()->mergeCells("N25:O25");
$doc->getActiveSheet()->mergeCells("K15:P15");
$doc->getActiveSheet()->mergeCells("A40:B40");
$doc->getActiveSheet()->getStyle("J1:J2")->getFont()->setBold(true);
$doc->getActiveSheet()->getStyle("J7")->getFont()->setSize(12);
$doc->getActiveSheet()->getStyle("J7")->getFont()->setBold(true);
$doc->getActiveSheet()->getStyle("O37")->getFont()->setSize(7);
$doc->getActiveSheet()->getStyle("M17")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("O17")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle('J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$doc->getActiveSheet()->getStyle('J27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$doc->getActiveSheet()->getStyle('O37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$doc->getActiveSheet()->getStyle('J1:N2')->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("P4")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("P5")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("L9:P9")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("L11:P11")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("M13:P13")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("K15:P15")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("M17")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("O17")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle("N19:O19")->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$linije = ['K20:L20', 'L21:N21', 'M23:P23', 'N25:O25', 'O36:P36'];
foreach ($linije as $linija) {
    $doc->getActiveSheet()->getStyle($linija)->applyFromArray(
        array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000')
                )
            )
        )
    );
}
$doc->getActiveSheet()->getStyle("C40")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("F40")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("A40")->getFont()->setSize(8);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$doc->getActiveSheet()
    ->getPageMargins()->setTop(1);
$doc->getActiveSheet()
    ->getPageMargins()->setRight(0.75);
$doc->getActiveSheet()
    ->getPageMargins()->setLeft(0.75);
$doc->getActiveSheet()
    ->getPageMargins()->setBottom(1);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setFitToHeight(1, true);
//DRUGA STRANA
if ($data['kraj_datum2']) {
    $dana = floor((strtotime($data['kraj_datum2'] . ' ' . $data['kraj_vrijeme2']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'])) / 86400);
    $sati = floor((strtotime($data['kraj_datum2'] . ' ' . $data['kraj_vrijeme2']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'])) / 3600) - $dana * 24;
    $time = secondsToTime(strtotime($data['kraj_datum2'] . ' ' . $data['kraj_vrijeme2']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme']));
    $sati = $time['h'] . ':' . $time['m'];
} else {
    $dana = floor((strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'])) / 86400);
    $sati = floor((strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme'])) / 3600) - $dana * 24;
    $time = secondsToTime(strtotime($data['kraj_datum'] . ' ' . $data['kraj_vrijeme']) - strtotime($data['pocetak_datum'] . ' ' . $data['pocetak_vrijeme']));
    $sati = $time['h'] . ':' . $time['m'];
}
if ($time['h'] < 0) $sati = 0;
$doc->createSheet();
$doc->setActiveSheetIndex(1)
    ->setCellValue('A1', 'OBRAČUN PUTNIH TROŠKOVA')
    ->setCellValue('A4', 'Datum polaska:')
    ->setCellValue('A6', 'Datum povratka:')
    ->setCellValue('A8', 'Ukupno vrijeme provedeno na službenom putu')
    ->setCellValue('E4', 'Vrijeme polaska')
    ->setCellValue('E6', 'Vrijeme povratka')
    ->setCellValue('F8', 'dana')
    ->setCellValue('H8', 'sati')
    ->setCellValue('A10', 'DNEVNICE')
    ->setCellValue('A14', '1. UKUPNO ZA DNEVNICE')
    ->setCellValue('A15', 'TROŠKOVI PREVOZA')
    ->setCellValue('A16', $data['trosak1'])
    ->setCellValue('A17', $data['trosak2'])
    ->setCellValue('E16', $data['kolicina1'])
    ->setCellValue('E17', $data['kolicina2'])
    ->setCellValue('G16', $data['iznos1'])
    ->setCellValue('G17', $data['iznos2'])
    ->setCellValue('A20', '2. UKUPNO ZA TROŠKOVE PREVOZA')
    ->setCellValue('A21', 'IZDACI ZA NOĆENJE')
    ->setCellValue('A25', '3. UKUPNO IZDACI ZA NOĆENJE')
    ->setCellValue('A26', 'OSTALI TROŠKOVI')
    ->setCellValue('A27', $data['ost_trosak1'])
    ->setCellValue('A28', $data['ost_trosak2'])
    ->setCellValue('A29', $data['ost_trosak3'])
    ->setCellValue('A30', $data['ost_trosak4'])
    ->setCellValue('A31', $data['ost_trosak5'])
    ->setCellValue('A32', $data['ost_trosak6'])
    ->setCellValue('E27', $data['ost_kolicina1'])
    ->setCellValue('E28', $data['ost_kolicina2'])
    ->setCellValue('E29', $data['ost_kolicina3'])
    ->setCellValue('E30', $data['ost_kolicina4'])
    ->setCellValue('E31', $data['ost_kolicina5'])
    ->setCellValue('E32', $data['ost_kolicina6'])
    ->setCellValue('G27', $data['ost_iznos1'])
    ->setCellValue('G28', $data['ost_iznos2'])
    ->setCellValue('G29', $data['ost_iznos3'])
    ->setCellValue('G30', $data['ost_iznos4'])
    ->setCellValue('G31', $data['ost_iznos5'])
    ->setCellValue('G32', $data['ost_iznos6'])
    // ->setCellValue('E18', $data['kol_gorivo'])
    // ->setCellValue('G18', $data['iznos_gorivo'])
    ->setCellValue('A22', $data['izdaci_naziv1'])
    ->setCellValue('A23', $data['izdaci_naziv2'])
    ->setCellValue('A24', $data['izdaci_naziv3'])
    ->setCellValue('E22', $data['izdaci_kol1'])
    ->setCellValue('E23', $data['izdaci_kol2'])
    ->setCellValue('E24', $data['izdaci_kol3'])
    ->setCellValue('G22', $data['izdaci_iznos1'])
    ->setCellValue('G23', $data['izdaci_iznos2'])
    ->setCellValue('G24', $data['izdaci_iznos3'])
    ->setCellValue('A33', '4. UKUPNO OSTALI TROŠKOVI')
    ->setCellValue('A34', 'UKUPNO TROŠKOVI 1+2+3+4')
    ->setCellValue('A35', 'Primljena akontacija')
    ->setCellValue('A36', 'Placeno biznis karticom')
    ->setCellValue('A37', 'Za isplatu / Za povrat')
    ->setCellValue('E10', 'KOL.')
    ->setCellValue('G10', 'IZNOS KM')
    ->setCellValue('H10', 'UKUPNO KM')
    ->setCellValue('A41', 'Obračun podnio')
    ->setCellValue('C41', 'Likvidirao')
    ->setCellValue('E41', 'Kontrolisao')
    ->setCellValue('G41', 'Saglasan')
    ->setCellValue('A18', 'Ukoliko vlastita kola: broj KM x cijena goriva x15%')
    ->setCellValue('A19', $data['kol_gorivo'] . 'km ' . $data['iznos_gorivo'])
    ->setCellValue('B4', date("d.m.Y", strtotime($data['pocetak_datum'])))
    ->setCellValue('B6', date("d.m.Y", strtotime($data['kraj_datum'])))
    ->setCellValue('H4', $data['pocetak_vrijeme'])
    ->setCellValue('H6', $data['kraj_vrijeme2'] ? $data['kraj_vrijeme2'] : $data['kraj_vrijeme'])
    ->setCellValue('E8', $dana < 0 ? 0 : $dana)
    ->setCellValue('G8', $sati)
    ->setCellValue('H35', $data['iznos_akontacije'])
    ->setCellValue('A40', $data['fname'] . ' ' . $data['lname'])
    ->setCellValue('A11', $drzava['name'])
    ->setCellValue('G11', $drzava['wage'])
    ->setCellValue('A12', $drzava2['name'])
    ->setCellValue('G12', $drzava2['wage'])
    ->setCellValue('A13', $drzava3['name'])
    ->setCellValue('G13', $drzava3['wage'])
    ->setCellValue('E11', $dnevnica)
    ->setCellValue('E12', $dnevnica2)
    ->setCellValue('H36', $data['placeno_biznis_karticom'])
    ->setCellValue('H37', "=H34-H35-H36");
$doc->getActiveSheet()
    ->setCellValue(
        'H34',
        "=SUM(H14,H20,H25,H33)"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H14',
        "=(SUM(H11:H12))"
    );
if ($data['vrsta_smjestaja'] == 'hotel' or $data['vrsta_smjestaja'] == 'Hotel') {
    $doc->getActiveSheet()->setCellValue('H13', "=SUM(H14-H11-H12)");
    if ($data['dacheck'] == 'DA') {
        $doc->getActiveSheet()->setCellValue('H14', "=SUM(H11:H12)*0.7");
        $doc->getActiveSheet()->setCellValue('A13', "Umanjenje");
    } else {
        $doc->getActiveSheet()->setCellValue('H14', "=SUM(H11:H12)");
    }
} else if (strtolower($data['vrsta_smjestaja']) == 'privatni smještaj') {
    $doc->getActiveSheet()->setCellValue('H13', "=SUM(H14-H11-H12)");
    if ($data['dacheck'] == 'DA') {
        $doc->getActiveSheet()->setCellValue('H13', "=(SUM(H11:H12)*70/100) - (SUM(H11:H12)*30/100)");
        $doc->getActiveSheet()->setCellValue('H14', "=SUM(H11:H13)");
        $doc->getActiveSheet()->setCellValue('A13', "Dodaci");
    } else {
        $doc->getActiveSheet()->setCellValue('H14', "=SUM(H11:H12)*1.7");
        $doc->getActiveSheet()->setCellValue('A13', "Dodaci");
    }
}
$doc->getActiveSheet()
    ->setCellValue(
        'H12',
        "=E12*G12"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H11',
        "=E11*G11"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H20',
        "=SUM(H16:H19)"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H16',
        "=E16*G16"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H17',
        "=E17*G17"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H18',
        "=(E18*G18)*1.15"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H19',
        $data['kol_gorivo'] ? round($data['kol_gorivo'] * $matches[1][0] * ($matches[3][0] / 100), 2) : '0'
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H25',
        "=SUM(H22:H24)"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H22',
        "=E22*G22"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H23',
        "=E23*G23"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H24',
        "=E24*G24"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H33',
        "=SUM(H27:H32)"
    );
$doc->getActiveSheet()
    ->setCellValue(
        'H27',
        "=E27*G27"
    );
$doc->getActiveSheet()->setCellValue('H28', "=E28*G28");
$doc->getActiveSheet()->setCellValue('H29', "=E29*G29");
$doc->getActiveSheet()->setCellValue('H30', "=E30*G30");
$doc->getActiveSheet()->setCellValue('H31', "=E31*G31");
$doc->getActiveSheet()->setCellValue('H32', "=E32*G32");
$doc->getActiveSheet()->mergeCells("A16:D16");
$doc->getActiveSheet()->mergeCells("E16:F16");
$merge_cells = [
    'A1:H1', 'B4:C4', 'B6:C6', 'A8:D8', 'A10:D10', 'A11:D11', 'A12:D12', 'A13:D13', 'A14:G14', 'E10:F10', 'E11:F11', 'E12:F12', 'E13:F13', 'A15:H15', 'A17:D17', 'E17:F17',
    'A18:D18', 'E18:F18', 'A19:D19', 'E19:F19', 'A20:G20', 'A21:H21', 'E22:F22', 'E4:F4', 'E6:F6', 'E23:F23', 'E24:F24', 'A25:G25', 'A26:H26', 'A27:D27', 'A28:D28', 'A29:D29'
    , 'A30:D30', 'A31:D31', 'A32:D32', 'E26:F26', 'E27:F27', 'E28:F28', 'E29:F29', 'E30:F30', 'E31:F31', 'E32:F32', 'A34:G34', 'A35:G35', 'A33:G33', 'A36:G36', 'A37:G37', 'J3:P37', 'A24:D24', 'A22:D22', 'A23:D23', 'A40:B40'
];
foreach ($merge_cells as $merge_cell) {
    $doc->getActiveSheet()->mergeCells($merge_cell);
}
$doc->getActiveSheet()->getColumnDimension('A')->setWidth(16.7);
$doc->getActiveSheet()->getColumnDimension('B')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('C')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('D')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('E')->setWidth(6.8);
$doc->getActiveSheet()->getColumnDimension('F')->setWidth(6.8);
$doc->getActiveSheet()->getColumnDimension('G')->setWidth(8.9);
$doc->getActiveSheet()->getColumnDimension('H')->setWidth(11.1);
$doc->getActiveSheet()->getRowDimension('10')->setRowHeight(14.4);
$linije = ['A10:H37', 'B4:C4', 'B6:C6', 'H4', 'H6'];
foreach ($linije as $linija) {
    $doc->getActiveSheet()->getStyle($linija)->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000')
                )
            )
        )
    );
}
$linije = ['B4:C4', 'B6:C6', 'E8', 'G8', 'A40', 'C40', 'E40', 'G40'];
foreach ($linije as $linija) {
    $doc->getActiveSheet()->getStyle($linija)->applyFromArray(
        array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000')
                )
            )
        )
    );
}
$doc->getActiveSheet()->getStyle('A37:H37')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A10:A37')->applyFromArray(
    array(
        'borders' => array(
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A10:H10')->applyFromArray(
    array(
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('H10:H37')->applyFromArray(
    array(
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('A1:K100')->getAlignment()->setWrapText(true);
$doc->getActiveSheet()->getStyle('E41')->getAlignment()->setWrapText(false);
$doc->getActiveSheet()->getStyle('A40')->getAlignment()->setWrapText(false);
$doc->getActiveSheet()->getStyle('H10')->getAlignment()->setWrapText(false);
$doc->getActiveSheet()->getStyle('A1:K100')->getFont()
    ->setName('Arial')
    ->setSize(9);
$boldiraj = ['A1', 'A10:H10', 'A15', 'A19', 'A21', 'A26', 'A34'];
foreach ($boldiraj as $celija) {
    $doc->getActiveSheet()->getStyle($celija)->getFont()->setBold(true);
}
$centriraj = ['A1', 'E10', 'A41:H41'];
foreach ($centriraj as $celija2) {
    $doc->getActiveSheet()->getStyle($celija2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$doc->getActiveSheet()->getStyle("H35")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$doc->getActiveSheet()->getStyle("H36")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$doc->getActiveSheet()->getStyle("A41:H41")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("A1")->getFont()->setSize(11);
$doc->getActiveSheet()->getStyle('J3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$doc->getActiveSheet()->getStyle('H11:H37')->getNumberFormat()->setFormatCode('#,##0.00');
//desno pozadi druga strana
$doc->setActiveSheetIndex(1)
    ->setCellValue('J1', 'BILJEŠKE');
$doc->getActiveSheet()->getStyle("J1")->getFont()->setSize(11);
$doc->getActiveSheet()->getStyle("C40")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("F40")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("A40")->getFont()->setSize(8);
$doc->getActiveSheet()->getStyle("J1")->getFont()->setBold(true);
$doc->getActiveSheet()->mergeCells("J1:P1");
$doc->getActiveSheet()->getStyle("J1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$doc->getActiveSheet()->getStyle('J37:P37')->applyFromArray(
    array(
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('J3:J37')->applyFromArray(
    array(
        'borders' => array(
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('J3:P3')->applyFromArray(
    array(
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()->getStyle('P3:P37')->applyFromArray(
    array(
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
                'color' => array('rgb' => '000')
            )
        )
    )
);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$doc->getActiveSheet()
    ->getPageMargins()->setTop(1);
$doc->getActiveSheet()
    ->getPageMargins()->setRight(0.75);
$doc->getActiveSheet()
    ->getPageMargins()->setLeft(0.75);
$doc->getActiveSheet()
    ->getPageMargins()->setBottom(1);
$doc->getActiveSheet()
    ->getPageSetup()
    ->setFitToHeight(1, true);
if ($data['svrha'] == 'UPOTREBA VLASTITOG AUTOMOBILA U SLUŽBENE SVRHE') {
    $doc->getActiveSheet()
        ->setCellValue(
            'H11',
            "0"
        );
    $doc->getActiveSheet()
        ->setCellValue(
            'H12',
            "0"
        );
    $doc->getActiveSheet()
        ->setCellValue(
            'H13',
            "0"
        );
    $doc->getActiveSheet()
        ->setCellValue(
            'H14',
            "0"
        );
}
if ($data['status'] == 81) {
    $doc->getActiveSheet()
        ->setCellValue('E11', 0)
        ->setCellValue('E12', 0);
}
$objWriter = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
$objWriter->save('php://output');
*/

?>


    <section class="full">

        <div class="container-fluid">
        </div>
    </section>
<?php

include $_themeRoot . '/footer.php';

?>