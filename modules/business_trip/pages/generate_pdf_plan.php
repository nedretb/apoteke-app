<?php

require_once($root.'/tcpdf/tcpdf.php');
//include('table_used.php');
require __DIR__ . '/../../../vendor/autoload.php';
use Carbon\Carbon;

class mypdf extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        //$image_file = '../../../theme/images/rff.png';
        //$this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        // $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('times', 'N', 8);
        // Page number
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        $html = '
			<font style="color:grey;">Raiffeisen BANK d.d. Bosna i Hercegovina</font> <font style="color:#ababab;">• 71 000 Sarajevo<br />
			• Zmaja od Bosne bb • S.W.I.F.T.: RZBABA2S • Raiffeisen direkt info: +387 33 75 50 10<br />
			• Fax:  +387 33 21 38 51 •  www.raiffeisenbank.ba<br /></font>

		';

        //$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }
}


function generatepdf($state, $user, $data, $org_jed)
{
    ///////////////////// CONFIG

    $date_delimiter = ".";


//var_dump($data);

///////////////////// END CONFIG


    $pdf = new mypdf('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MKT');
    $pdf->SetTitle('Plan službenih putovanja');
    $pdf->SetSubject('Plan službenih putovanja');
    //$pdf->SetHeaderData('../../../theme/images/rff.png', PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT + 20, PDF_MARGIN_TOP - 5, PDF_MARGIN_RIGHT-20);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER + 5);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $tahoma = TCPDF_FONTS::addTTFfont('tahoma.ttf', 'TrueTypeUnicode', '', 32);
    $tahomabd = TCPDF_FONTS::addTTFfont('tahoma.ttf', 'TrueTypeUnicode', '', 32);
    $pdf->SetFont($tahoma, '', 10, '', true);
    $pdf->AddPage();
    $pdf->AddFont($tahomabd);        //custom font
    $pdf->setListIndentWidth(4);


//    $t = strtotime($data['Insert Date']);
//
//    $date_from = date("d.m.Y", strtotime($data['Starting Date of I part']));
//    $date_to = date("d.m.Y", strtotime($data['Ending Date of I part']));

    $crta = null;
    $dio = 'godišnji odmor';
//
//
//    if ($state == 'fbih') {
//
//        $_text[0] = "Na osnovu odredaba Zakona o radu Federacije BiH i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
//        $_text[1] = "Zakonom o radu Federacije BiH je definisano da Radnik/ca, za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
//        $_text[2] = "on/ona to";
//        $_text[3] = "Radnik";
//        $crta = '-';
//        $pravni_lijek = 'Radnik, ako smatra da mu je ovom Odlukom povrijeđeno neko pravo iz radnog odnosa, može u roku od 30 dana od dana dostavljanja ove Odluke, odnosno od dana saznanja za povredu prava, zahtijevati od Poslodavca ostvarivanje tog prava. Ako poslodavac u roku od 30 dana od dana podnošenja zahtjeva za zaštitu prava ne udovolji tom zahtjevu, Radnik može u daljem roku od 90 dana podnijeti tužbu pred nadležnim sudom.';
//        $dio = 'dio godišnjeg odmora';
//        $pravo = "Radnik ima pravo koristiti jedan dan godišnjeg odmora kad " . $_text[2] . " želi, uz obavezu da o tome obavijesti poslodavca najmanje tri dana prije njegovog korištenja.";
//
//    } else if ($state == 'rs') {
//
//        $_text[0] = "Na osnovu odredaba Zakona o radu Republike Srpske i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
//        $_text[1] = "Zakonom o radu je definisano da Radnik, za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
//        $_text[2] = "to";
//        $_text[3] = "Radnik";
//        $pravni_lijek = 'Radnik, ako smatra da mu je ovom Odlukom povrijeđeno neko pravo iz radnog odnosa, ima pravo podnijeti pisani zahtjev Poslodavcu da mu osigura ostvarivanje tog prava u roku od 30 dana od dana saznanja za povredu, a najduže u roku od 3 mjeseca od dana učinjene povrede.';
//        $pravo = "Radnik ima pravo koristiti jedan dan godišnjeg odmora kad " . $_text[2] . " želi, uz obavezu da o tome obavijesti poslodavca najmanje tri dana prije njegovog korištenja.";
//
//    } else if ($state == 'bd') {
//
//        $_text[0] = "Na osnovu odredaba Zakona o radu Brčko distrikta BiH i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
//        $_text[1] = "Zakonom o radu Brčko Distrikta BiH je definisano da Radnik, za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
//        $_text[2] = "";
//        $_text[3] = "Radnik";
//        $pravni_lijek = 'Radnik koji smatra da mu je ovim Rješenjem Poslodavac povrijedio neko pravo iz radnog odnosa može u roku od trideset (30) dana od dana dostavljanja ovog Rješenja, zahtijevati od Poslodavca, podnošenjem zahtjeva za zaštitu prava, ostvarivanje tog prava.';
//        $pravo = "Radnik ima pravo koristiti pet dana godišnjeg odmora kad " . $_text[2] . " želi, uz obavezu da o tome obavijesti poslodavca najmanje pet dana prije njegovog korištenja.";
//    }
//
//    $pravilnik_o_radu = $data['Total days'] - $data['Legal Grounds'];

    //requests.date_created  where type='DEC' and  employee_no..
    $table_head_style= 'font-weight: bold; font-family: tahomabd; font-size: small; border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;';

    $html2 = '
<div>
<table>
    <tbody>
        <tr>
            <td style="font-family: tahomabd;">Bosna i Hercegovina</td>
            
            ';
    if ($org_jed != 'Svi'){
        $html2 .= '<td style="width: 30%; text-align: right;">Prilog 6</td>';
    }
    else{
        $html2 .= '<td style="width: 30%; text-align: right;">Prilog 5</td>';
    }
    $html2 .='
        </tr>
        <tr>
            <td style="font-family: tahomabd;">Ministarstvo komunikacija i prometa</td>
            <td style="text-align: center;"></td>
            ';

    $html2 .='
        </tr>
        <tr>
        
        ';
    if ($org_jed != 'Svi'){
        $html2 .= '<td style="width: 30%; text-align: right;">'.$org_jed.'</td>';
    }
    else{
        $html2 .= '<td style="width: 30%; text-align: right;"></td>';
    }
    $html2 .='
        <td></td>
        </tr>
    </tbody>
</table>
</div>

<div >
<table>
    <tbody>
        <tr>
            <td style="font-weight: bold; font-family: tahomabd; font-size: large; width: 80%; text-align: center;">PLAN SLUŽBENIH PUTOVANJA</td>
            <td style=""></td>
        </tr>
        <br>
        <tr>
            <td style="font-weight: bold; font-family: tahomabd; text-align: center;">za ___________. godinu</td>
            <td style="font-family: Arial; text-align: center;"></td>
        </tr>
    </tbody>
</table>
</div>

<div style="text-align: center;">
<table style="margin-left: auto; margin-right: auto;">
    <thead style="border: 1px solid black;">
        <tr style="border: 1px solid black;">
            <th style="'.$table_head_style.' width: 3%;">R.B</th>
            <th style="'.$table_head_style.'"><b>SVRHA PUTOVANJA</b></th>
            <th style="'.$table_head_style.' width: 7%;"><b>BROJ UČESNIKA IZ MKT</b></th>
            <th style="'.$table_head_style.'"><b>MJESTO PUTOVANJA (grad, država)</b></th>
            <th style="'.$table_head_style.'"><b>VRIJEME PUTOVANJA (mjesec i broj dana)</b></th>
            <th style="'.$table_head_style.'"><b>PROCJENA TROŠKOVA</b></th>
            <th style="'.$table_head_style.'"><b>Specifikacija procjenjenih troškova</b></th>
        </tr>
    </thead>
    <tbody>';
    $count = 0;
    foreach ($data as $d){
        $count++;
        $dx = Carbon::parse($d['pocetak_datum']);
        $yer = $dx->diff($d['kraj_datum']);
        $html2 .='
                <tr>
                    <td style="border: 1px solid black; width: 3%; text-align: center; vertical-align: middle;">'.$count.'</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;">'.$d['svrha'].'</td>
                    <td style="border: 1px solid black; width: 7%; text-align: center; vertical-align: middle;">1</td>
                    <td style="height:40px; border: 1px solid black; text-align: center; vertical-align: middle;">'.$d['odredisni_grad'].', '.$d['name'].'</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;">Mjeseci: '.$yer->m.', Dana: '.$yer->d.'</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;"></td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: middle;"></td>
                </tr>';
    }
    $html2 .='
    </tbody>
</table>
</div>

<div>
<table>
    <tbody>
        <tr>
            <td style="font-family: Arial;">Datum: '.date('d.m.Y').'</td>
            <td style="width: 30%; text-align: center;">________________________________________</td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center;">(potpis rukovodioca institucije)</td>
        </tr>
    </tbody>
</table>
</div>';


    $pdf->writeHTMLCell(0, 0, '', '', $html2, 0, 1, 0, true, '', true);


    $pdf->Output('Plan službenih putovanja' . '.pdf', 'I');

}