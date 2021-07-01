<?php

use Carbon\Carbon;

require_once('../../../tcpdf/tcpdf.php');

require __DIR__ . '/../../../vendor/autoload.php';

class mypdf extends TCPDF{
    public function Header(){ }
    public function Footer(){}
}


function generatepdf($user_data, $parent_data, $dateFrom, $dateTo, $vac_data)
{
    $dateDiff = Carbon::parse($dateFrom);
    $diff = $dateDiff->diffInDays($dateTo) +1;


    $last_year_days= $vac_data['Br_danaPG']-$vac_data['Br_dana_iskoristenoPG'];
    $curr_year_days= $vac_data['Br_dana']-$vac_data['Br_dana_iskoristeno'];
    ///////////////////// CONFIG

    $date_delimiter = ".";


    ///////////////////// END CONFIG


    $pdf = new mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MKT');
    $pdf->SetTitle('rjesenje-o-go-.pdf');
    $pdf->SetSubject('rjesenje-o-go.pdf');
    //$pdf->SetHeaderData('../../../theme/images/rff.png', PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP - 5, PDF_MARGIN_RIGHT);
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



    $html = '<h1 style="text-align: center;">ZAHTJEV ZA GODIŠNJI ODMOR</h1>
<table>
    <tr>
        <td style="border: 1px solid black;">Ime i prezime</td>
        <td style="border: 1px solid black;">'.$user_data['fname']. ' '.$user_data['lname']. '</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Organizaciona jedinica</td>
        <td style="border: 1px solid black;"> '.$user_data['position'].'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Nadređeni</td>
        <td style="border: 1px solid black;">'.$parent_data['fname']. ' '.$parent_data['lname'] .'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Vrsta odsustva</td>
        <td style="border: 1px solid black;">dodati</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Datum predavanja zahtjeva</td>
        <td style="border: 1px solid black;">'.date('d.m.Y', time()).'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Datum početka korištenja godišnjeg odmora</td>
        <td style="border: 1px solid black;"> '.$dateFrom.'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Datum yavršetka korištenja godišnjeg odmora</td>
        <td style="border: 1px solid black;"> '.$dateTo.'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Trajanje godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$diff.'</td>
    </tr>
</table>
<br><br>

<table>
<thead>
<tr>
<th colspan="2" style="border: 1px solid black; font-family: tahomabd;">Godišnji odmor prošla godina - statistika</th>
</tr>
</thead>
<tbody>

    <tr>
        <td style="border: 1px solid black;">Ukupan broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$vac_data['Br_danaPG'].'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Iskorišten broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$vac_data['Br_dana_iskoristenoPG'].'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Preostali broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$last_year_days.'</td>
    </tr>
</tbody>
</table>

<br><br>

<table>
<thead>
<tr>
<th colspan="2" style="border: 1px solid black; font-family: tahomabd;">Godišnji odmor tekuća godina - statistika</th>
</tr>
</thead>
<tbody>

    <tr>
        <td style="border: 1px solid black;">Ukupan broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$vac_data['Br_dana'].'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Iskorišten broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$vac_data['Br_dana_iskoristeno'].'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black;">Preostali broj dana godišnjeg odmora</td>
        <td style="border: 1px solid black;">'.$curr_year_days.'</td>
    </tr>
</tbody>
</table>


<br><br>

<table>
<thead>
<tr>
<th colspan="2"></th>
</tr>
</thead>
<tbody>

    <tr>
        <td style="width: 75%; text-align: right;">Rukovoditelj ili odgovorni u Službi za kadrovske poslove</td>
        <td style=""> ___________________</td>
    </tr>
    <tr>
        <td style=""></td>
        <td style="">'.$parent_data['fname']. ' '.$parent_data['lname'] .'</td>
    </tr>
</tbody>
</table>
';

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $fileName = md5(time()).'.pdf';
    $pdf->Output(__DIR__ . '/files/zahtjevi-go/'.$fileName, 'F');
    return $fileName;
}