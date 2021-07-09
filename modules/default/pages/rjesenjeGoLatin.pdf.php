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


function generatepdf($user_data, $data, $ustrojstvena_jedinica, $years_exp)
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

    $soc = $data['dani_invalidnost']+$data['dani_dijete_sa_posebnim_potrebama'];

    $html = '
<h4 style="font-family: tahomabd;">JAVNA USTANOVA „APOTEKE SARAJEVO“</h4>
<h4 style="font-family: tahomabd;">Broj: ____ubaciti broj____</h4>
<h4 style="font-family: tahomabd;">Sarajevo, '.date('d.m.Y', strtotime($data['datum_kreiranja_rjesenja'])).'. godine</h4>
<br>
<p style="text-align: justify-all;">Na osnovu člana 112., 47., 50. i 52. Zakona o radu ( „Službene novine FBiH“ broj 26/16, 89/18 i 23/20), člana  49. i 50. Pravilnika o radu JU „Apoteke Sarajevo“, broj:01-01-78-2/20 od 08.01.2020. godine, Ugovora o radu i Plana korištenja godišnjih odmora, generalni direktor d o n o s i</p>
<br><br>
<p style="text-align: center; font-family: tahomabd;">R j e š e nj e</p>
<p style="text-align: center; font-family: tahomabd;">o korištenju godišnjeg odmora za 2021. godinu</p>
<p style="text-align: justify-all">1. Radniku '.$user_data['fname'].' '.$user_data['lname'].' raspoređenom na radno mjesto '.$user_data['egop_radno_mjesto'].' (dalje u tekstu: radnik) utvrđuje se pravo na  godišnji odmor za 2021. godinu, u trajanju od ukupno '.$data['ukupan_broj_dana_go'].' radnih dana, prema sljedećim osnovama i kriterijima:</p>
<ul>
    <li>zakonski minimum....................................................... '.$data['dani_zakonski'].' radnih dana</li>
    <li>dužina radnog staža.....................................................   '.$data['dani_radno_iskustvo'].'  radnih dana</li>
    <li>složenost poslova........................................................    '.$data['ukupan_broj_dana_go'].'  radnih dana</li>
    <li>socijalni uslovi..............................................................   '.$soc.' radnih dana</li>
    <li>rad u dežurnoj apoteci.................................................    '.$data['ukupan_broj_dana_go'].' radnih dana</li>
    <li>status demobiliziranog branioca....................................   '.$data['ukupan_broj_dana_go'].' radnih dana</li>
</ul>
<p style="font-family: tahomabd;">I dio odmora radnik koristi od '.date('d.m.Y', strtotime($data['datum_od'])).'. godine - '.date('d.m.Y', strtotime($data['datum_do'])).'. godine.</p>
<p style="font-family: tahomabd;">II dio odmora radnik koristi od 06.09.2021. godine - 27.09.2021. godine.</p>
<p style="text-align: justify-all">2. Shodno  odredbama člana 47. Zakona o radu u F BiH,  radnik, za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana, a najduže 30 radnih dana.</p>
<p style="text-align: justify-all">3. Radnik će prvi dio godišnjeg odmora u trajanju od najmanje 12 radnih dana koristiti shodno Planu korištenja godišnjeg odmora i dogovoru sa poslodavcem,  a drugi dio, najkasnije do 30. Juna naredne godine.

Plan korištenja godišnjeg odmora utvrđuje poslodavac, uz prethodnu konsultaciju sa radnicima ili njihovim predstavnicima u skladu sa zakonom, uzimajući u obzir potrebe posla, kao i opravdane razloge radnika.
</p>
<p style="text-align: justify-all">4. Radnik ima pravo koristiti jedan dan godišnjeg odmora kad on to želi, uz obavezu da o tome obavijesti poslodavca najmanje tri dana prije njegovog korištenja.</p>
<p style="text-align: justify-all">5. Za vrijeme trajanja godišnjeg odmora radnik ima pravo na naknadu plaće u skladu sa članom 52. stav 3. Zakona o radu  F BiH.</p>
<br>
<p style="text-align: justify-all">Pouka o pravnom lijeku: Protiv ovoga rješenja može se uložiti pismeni prigovor poslodavcu, u roku od 30 dana od dana dostave.</p>
<br>
<p style="text-align: justify-all">Napomena: <br>Imajući u vidu da godišnji odmor radnika, u skladu sa članom 47. Zakona o radu FBiH, može trajati najduže 30 radnih dana, to je odlučeno kao u dispozitivu.
</p>
<br><br>

<p style="font-family: tahomabd;">JU“APOTEKE SARAJEVO“ <br>Nedim Hrelja, mr.ph. MBA <br>Generalni Direktor</p>
<p style="font-family: tahomabd;">..............................</p>


<p>Dostaviti: <br> -imenovani <br> -organizaciona jedinica <br> -a/a</p>
';

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    //$fileName = 'zahtjev-za-go-'.$user_data['fname'].'-'.$user_data['lname'].'.pdf';
    $fileName = substr(md5(time()),0, 6 ).'.pdf';

    $pdf->Output(__DIR__ . '/files/zahtjevi-go/'.$fileName, 'I');

    return $fileName;
//    $pdf->Output('rjesenje-o-go.pdf', 'I');

}