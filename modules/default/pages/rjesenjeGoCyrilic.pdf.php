<?php

use Carbon\Carbon;

require_once('../../../tcpdf/tcpdf.php');

require __DIR__ . '/../../../vendor/autoload.php';

class mypdf extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        //  $image_file = '../../../theme/images/rff.png';
        //$this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
        // Set font
        //$this->SetFont('helvetica', 'B', 20);
        // Title
        // $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
//        // Position at 15 mm from bottom
//        $this->SetY(-15);
//        // Set font
//        $this->SetFont('times', 'N', 8);
//        // Page number
//        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
//
//        $html = '
//			<font style="color:grey;">Raiffeisen BANK d.d. Bosna i Hercegovina</font> <font style="color:#ababab;">• 71 000 Sarajevo<br />
//			• Zmaja od Bosne bb • S.W.I.F.T.: RZBABA2S • Raiffeisen direkt info: +387 33 75 50 10<br />
//			• Fax:  +387 33 21 38 51 •  www.raiffeisenbank.ba<br /></font>
//
//		';
//
//        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }
}


function generatepdf()
{
    ///////////////////// CONFIG

    $date_delimiter = ".";


///////////////////// END CONFIG


    $pdf = new mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Raiffeisen Bank');
    $pdf->SetTitle('rjesenje-o-go-.pdf');
    $pdf->SetSubject('rjesenje-o-go.pdf');
    //$pdf->SetHeaderData('../../../theme/images/rff.png', PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-20, PDF_MARGIN_RIGHT);
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
    $tahomabd = TCPDF_FONTS::addTTFfont('Amalia.ttf', 'TrueTypeUnicode', '', 32);
    $pdf->SetFont($tahoma, '', 10, '', true);
    $pdf->AddPage();
    $pdf->AddFont($tahomabd);        //custom font
    $pdf->setListIndentWidth(4);



    $html = '
<h4 style="font-family: tahomabd;">БОСНА И ХЕРЦЕГОВИНА</h4>
<h4 style="font-family: tahomabd;">МИНИСТАРСТВО КОМУНИКАЦИЈА И ТРАНСПОРТА</h4>
<p>Број: __________________/'.date('Y').'</p>
<p>Сарајево, ________'.date('Y').'. године</p>
<br>
<p style="text-align: justify-all;">На основу члана 61. став 2. Закона о управи („Службени гласник БиХ“, бр. 32/02, 102/09 и 72/17), члана 46. Закона о државној служби у институцијама Босне и Херцеговине („Службени гласник БиХ“, бр. 19/02, 8/03, 35/03, 4/04, 17/04, 26/04, 37/04, 48/05, 2/06, 32/07, 43/09, 8/10, 40/12 и 93/17) и члана 12. став (1) Одлуке о критеријумима и начину кориштења годишњег одмора за државне службенике у институцијама Босне и Херцеговине („Службени гласник БиХ“, број 16/20), министар комуникација и транспорта Босне и Херцеговине, д о н о с и</p>
<br><br>
<p style="text-align: center; font-family: tahomabd;">Р ј е ш е њ е</p>
<p style="text-align: center; font-family: tahomabd;">о кориштењу годишњег одмора</p>
<p>1. ___ime i prezime___, __radno mjesto__ u __ustojstvena jedinica__ одобрава се кориштење годишњег одмора за '.date('Y').'. годину у трајању од __broj dana GO__ радних  дана.</p>
<p>2. Први дио годишњег одмора у трајању од 10 радних дана именовани ће користити у периоду од __datum od GO__ do __datum od GO__ '.date('Y').'. године. Преостали дио годишњег одмора именовани ће користити најкасније до 30.06.'.date('Y', strtotime("year + 1")).'. године.</p>
<br>
<p style="text-align: center; font-family: tahomabd;">О б р а з л о ж е њ е</p>
<p style="text-align: justify-all;">__ime i prezime__, као државни службеник, обавља послове __radno mjesto__ u __ustojstvena jedinica__. Према одредбама члана 46. Закона о државној служби у институцијама Босне и Херцеговине („Службени гласник БиХ“, бр. 12/02, 19/02, 8/03, 35/03, 4/04, 17/04, 26/04, 37/04, 48/05, 2/06, 32/07, 43/09, 8/10, 40/12 и 93/17,  у даљем тексту: Закон) и члана 5. став (1) Одлуке о критеријумима и начину кориштења годишњег одмора за државне службенике у институцијама Босне и Херцеговине („Службени гласник БиХ“, број 16/20, у даљем тексту: Одлука), државни службеници имају право на плаћени годишњи одмор од најмање 20 радних дана те сходно члану 5. став (1) Одлуке и право на одговарајуће увећање трајања годишњег одмора у складу са прописаним условима.</p>
<p style="text-align: justify-all;">Увидом у матичну евиденцију утврђено је да је именовани  навршио девет годинa радног стажа, те има право на увећање трајања годишњег одмора од три  раднa дана. По основу нивоа  радног мјеста, именовани у складу са чланом 5. став (1) тачка ц) алинеја 3) Одлуке има право на увећање годишњег одмора у трајању од три радна  дана.</p>
<p style="text-align: justify-all;">У складу са одредбама члана 11. став (1) Одлуке, у трајање годишњег одмора нису урачунати дани у које се не ради.</p>
<p style="text-align: justify-all;">У складу са напријед наведеним, одлучено је као у диспозитиву</p>
<p style="text-align: justify-all;"><b style="font-family: tahomabd;">ПРАВНА ПОУКА:</b> Против овог рјешења се може уложити жалба Одбору државне службе за жалбе у року осам дана од дана пријема.</p>
<br>


<table>
<thead>
<tr>
<th colspan="2"></th>
</tr>
</thead>
<tbody>

    <tr>
        <td style="width: 60%; text-align: right;"></td>
        <td style="text-align: center; font-family: tahomabd;">М И Н И С Т А Р</td>
    </tr>
    <tr>
        <td style=""></td>
        <td style="text-align: center; font-family: tahomabd;">др __ime i prezime Ministra__</td>
    </tr>
</tbody>


<table>
<thead>
<tr>
<th colspan="2" style="text-align: left; font-family: tahomabd;">Достављено:</th>
</tr>
</thead>
<tbody>

    <tr>
        <td style="width: 60%; text-align: left;">- Именованом</td>
        <td style="text-align: center; font-family: tahomabd;"></td>
    </tr>
    <tr>
        <td style="width: 60%; text-align: left;">- Одсјек за финансијско-материјалне послове</td>
        <td style="text-align: center; font-family: tahomabd;"></td>
    </tr>
    <tr>
        <td style="width: 60%; text-align: left;">- Персонални досије</td>
        <td style="text-align: center; font-family: tahomabd;"></td>
    </tr>
    <tr>
        <td style="width: 60%; text-align: left;">- а/а</td>
        <td style="text-align: center; font-family: tahomabd;"></td>
    </tr>
</tbody>
';

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    //$fileName = 'zahtjev-za-go-'.$user_data['fname'].'-'.$user_data['lname'].'.pdf';
    $fileName = substr(md5(time()),0, 6 ).'.pdf';

    $pdf->Output(__DIR__ . '/files/zahtjevi-go/'.$fileName, 'I');

    return $fileName;
//    $pdf->Output('rjesenje-o-go.pdf', 'I');

}