<?php

require_once('../../../tcpdf/tcpdf.php');
include('table_used.php');


class mypdf extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        $image_file = '../../../theme/images/rff.png';
        $this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
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
        $this->SetFont('helvetica', 'N', 8);
        // Page number
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        $html = '
			<font style="color:grey;">Raiffeisen BANK d.d. Bosna i Hercegovina</font> <font style="color:#ababab;">• 71 000 Sarajevo<br />
			• Zmaja od Bosne bb • S.W.I.F.T.: RZBABA2S • Raiffeisen direkt info: +387 33 75 50 10<br />
			• Fax:  +387 33 21 38 51 •  www.raiffeisenbank.ba<br /></font>

		';

        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    }
}


function generatepdf($state, $user, $data, $vacation_data)
{
    ///////////////////// CONFIG

    $date_delimiter = ".";


///////////////////// END CONFIG


    $pdf = new mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Raiffeisen Bank');
    $pdf->SetTitle('rjesenje-o-go-' . date('Y', strtotime($data['Insert Date'])) . '.pdf');
    $pdf->SetSubject('rjesenje-o-go-' . date('Y', strtotime($data['Insert Date'])) . '.pdf');
    $pdf->SetHeaderData('../../../theme/images/rff.png', PDF_HEADER_LOGO_WIDTH, '', '', array(0, 64, 255), array(0, 64, 128));
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
    $tahomabd = TCPDF_FONTS::addTTFfont('tahomabd.ttf', 'TrueTypeUnicode', '', 32);
    $pdf->SetFont($tahoma, '', 10, '', true);
    $pdf->AddPage();
    $pdf->AddFont($tahomabd);        //custom font
    $pdf->setListIndentWidth(4);


    $t = strtotime($data['Insert Date']);

    $date_from = date("d.m.Y", strtotime($data['Starting Date of I part']));
    $date_to = date("d.m.Y", strtotime($data['Ending Date of I part']));


    if ($state == 'fbih') {

        $_text[0] = "Na osnovu odredaba Zakona o radu Federacije BiH i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
        $_text[1] = "Zakona o radu Federacije BiH je definisano da Radnik/ca,za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
        $_text[2] = "Primjenom gore navedenih kriterija ne može se utvrditi godišnji odmor u trajanju dužem od 30 radnih dana, a u skladu sa članom 47. Zakona o radu (''Službene novine Federacije BiH'' br. 26/16).
		<br />
		<br />";
        $_text[3] = "Radnik/ca";
        $_text[4] =
        $pouka = "Radnik, ako smatra da mu je ovim Rješenjem povrijeđeno neko pravo iz radnog odnosa, ima pravo u roku od 30 dana od dana dostave Rješenja, odnosno od dana saznanja za povredu prava, zahtijevati od Poslodavca ostvarivanje tog prava. Ako Poslodavac u roku od 30 dana od dana podnošenja zahtjeva ne udovolji zahtjevu, Radnik može u daljem roku od 90 dana podnijeti tužbu pred nadležnim sudom.";

    } else if ($state == 'rs') {

        $_text[0] = "Na osnovu odredaba Zakona o radu Republike Srpske i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
        $_text[1] = "Zakonom o radu je definisano da Radnik/ca,za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
        $_text[2] = "";
        $_text[3] = "Imenovanoj/m";
        $_text[4] = "";
        $pouka = "Radnik, ako smatra da mu je ovom Odlukom povrijeđeno neko pravo iz radnog odnosa, ima pravo podnijeti pisani zahtjev Poslodavcu da mu osigura ostvarivanje tog prava u roku od 30 dana od dana saznanja za povredu, a najduže u roku od 3 mjeseca od dana učinjene povrede.";

    } else if ($state == 'bd') {

        $_text[0] = "Na osnovu odredaba Zakona o radu Brčko Distrikta i Pravilnika o radu Raiffeisen Bank dd Bosna i Hercegovina, donosim";
        $_text[1] = "Zakonom o radu je definisano da Radnik/ca,za svaku kalendarsku godinu, ima pravo na plaćeni godišnji odmor u trajanju od najmanje 20 radnih dana.";
        $_text[2] = "";
        $_text[3] = "Imenovanoj/m";
        $_text[4] = "";
        $pouka = "Radnik ako smatra da mu je ovim Rješenjem povrijeđeno neko pravo može, u roku od 30 dana od dana dostave Rješenja, podnijeti pisani zahtjev za ostvarivanje tog prava, odnosno može u roku od jedne godine dana od dana saznanja za povredu prava ili dana učinjene povrede zahtjevati ostvarivanje svojih prava pred nadležnim sudom, a najdalje u roku od 3 godine od učinjene povrede.";


    }
    //requests.date_created  where type='DEC' and  employee_no..
    $html = '
		<style>
			* {
				text-align: justify;
			}
			.
			ol li {
				margin-bottom:10px;
			}
		</style>
		Dosije: ' . $user['dosier_no'] . '   <br />
		Datum rješenja: ' . date('d.m.Y', strtotime($data['Insert Date'])) . '
		
		<p>&nbsp;</p>
		<br />
		
		' . $_text[0] . '
		
		<p>&nbsp;</p>
		
		<p style="text-align:center;font-weight:bold;font-family: tahomabd;">
		<strong>RJEŠENJE<br />
		O KORIŠTENJU GODIŠNJEG ODMORA ZA ' . date('Y', strtotime($data['Insert Date'])) . '. GODINU</strong>
		</p>
		
		<p>&nbsp;</p>
		
		
		<ol style="padding-left:0;">
			<li>Radnik/ca banke ' . $data['First Name'] . ' ' . $data['Last Name'] . ', JMBG: ' . $data['JMB'] . ', ostvaruje pravo na korištenje godišnjeg odmora za ' . date('Y', strtotime($data['Insert Date'])) . '. godinu u trajanju od ' . $data['Total days'] . ' radna dana prema sljedećim kriterijima:<br />
			<ul>
				<li>U skladu sa Zakonom o radu - ' . $data['Legal Grounds'] . ' radnih dana;</li>
				<li>U skladu sa Pravilnikom o radu - ' . $data['Days based on Work experience'] . ' radna dana.</li>
			</ul>
			<br />
			</li>
			
			<li>Radnik/ca će koristiti dio godišnjeg odmora u trajanju od ' . $data['Duration'] . ' radnih dana u periodu od ' . $date_from . ' godine do ' . $date_to . ' godine.<br /></li>
			<li>Preostali dio godišnjeg odmora Radnik/ca će koristiti u dogovoru sa poslodavcem, a najkasnije do 30. juna naredne godine.<br /></li>
			<li>Radnik/ca ima pravo koristiti jedan dan godišnjeg odmora kad on/ona to želi, uz obavezu da o tome obavijesti poslodavca najmanje tri dana prije njegovog korištenja.<br /></li>
			<li>Za vrijeme korištenja godišnjeg odmora Radnik/ca ima pravo na naknade u skladu sa pozitivno - pravnim propisima.<br /></li>
		</ol>
		
		<br />
		<br />
		
		
		<p style="text-align:center;font-weight:bold;font-family: tahomabd;">
		<strong>OBRAZLOŽENJE</strong>
		</p>

		<br />
		
		' . $_text[1] . '
		<BR />
		<BR />
		Pravilnikom o radu utvrđeno je trajanje godišnjeg odmora duže od minimalnog  na osnovu sljedećih kriterija:
		<BR />
		<BR />
		1)	prema ukupnom stažu osiguranja:
		<ul class="kriteriji" style="list-style-type:none; text-align:right;">
			<li style="text-align:right"> - od 2 /dvije/ do 5 /pet/ godina staža............................................................................................... 2 dana;</li>
			<li style="text-align:right"> - od 5 /pet/ do 10 /deset/ godina staža............................................................................................ 4 dana;</li>
			<li style="text-align:right"> - od 10 /deset/ do 15 /petnaest/ godina staža ................................................................................. 6 dana;</li>
			<li style="text-align:right"> - od 15 /petnaest/ do 20 /dvadeset/ godina staža ............................................................................ 8 dana;</li>
			<li style="text-align:right"> - od 20 /dvadeset/ do 25 /dvadesetpet/ godina staža ..................................................................... 10 dana;</li>
			<li style="text-align:right"> - preko 25 /dvadesetpet/ godina staža ..........................................................................................  12 dana;</li>
		</ul>
		<BR />
		<BR />
		2)	prema zdravstvenom stanju, ukoliko radnik ispunjava uslove za sticanje prava po osnovu:
		-	
		<ul class="kriteriji" style="list-style-type:none">
			<li style="text-align:right"> - invalidnosti sa utvrđenim stepenom invalidnosti od najmanje 60% ..................................................  2 dana;</li>
			<li style="text-align:right"> - roditelji djece sa posebnim potrebama  ..........................................................................................  2 dana;</li>
		</ul>
		<br />
		<p>&nbsp;</p>
		<br />
		
		

		Planom korištenja godišnjeg odmora, a u skladu sa prethodno usaglašenim terminom korištenja godišnjeg odmora između nadležnog rukovodioca i Radnika/ce, predviđeno je da Radnik/ca koristi godišnji odmor u vrijeme navedeno u dispozitivu ovog Rješenja.
		
		<br />
		<br />
		
		Za vrijeme korištenja godišnjeg odmora Radnik/ca ima pravo na naknade u skladu sa pozitivno - pravnim propisima.
		U skladu sa prednje navedenim odlučeno je kao u izreci ovog Rješenja.
		
		<br />
		<p>&nbsp;</p>
		<p style="text-align:left;font-weight:bold;font-family: tahomabd;">
			Pouka o pravnom lijeku
		</p>
		<br />
		<br />
' . $pouka . '
		
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		
		<img src="../../../theme/images/pecat.png" width="256" />
		<br />
		<font style="text-align:left;font-weight:bold;font-family: tahomabd;">Emina Sarač</font><br />
		Direktor Kadrovskih poslova<br />
		
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p style="text-align:left;font-weight:bold;font-family: tahomabd;">Dostaviti:</p><br />
		1. ' . $_text[3] . '<br />
		2. a/a



';

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


    $pdf->Output('rjesenje-o-go-' . date('Y', strtotime($data['Insert Date'])) . '.pdf', 'I');

}