<?php
require_once '../../../configuration.php';
require_once ($root.'/CORE/classes/Model.php');
require_once($root . '/tcpdf/tcpdf.php');
require __DIR__ . '/../../../vendor/autoload.php';
use Carbon\Carbon;
foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;


class mypdf extends TCPDF{

    //Page header
    public function Header(){
        $this->SetFont('helvetica', 'B', 20);
    }

    // Page footer
    public function Footer(){
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('times', 'N', 8);
        // Page number

        $html = '
			<font style="color:grey;">Raiffeisen BANK d.d. Bosna i Hercegovina</font> <font style="color:#ababab;">• 71 000 Sarajevo<br />
			• Zmaja od Bosne bb • S.W.I.F.T.: RZBABA2S • Raiffeisen direkt info: +387 33 75 50 10<br />
			• Fax:  +387 33 21 38 51 •  www.raiffeisenbank.ba<br /></font>

		';
    }
}
function generatepdf($_user, $method){
    $user = Profile::where('employee_no = '.$_user)->first();

    $data = ($user['role'] == 4) ? Sistematizacija::getSys() : Sistematizacija::getSys($user);


    $date_delimiter = ".";

    $pdf = new mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MKT');
    $pdf->SetTitle('Plan službenih putovanja');
    $pdf->SetSubject('Plan službenih putovanja');
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
    $tahomabd = TCPDF_FONTS::addTTFfont('tahoma.ttf', 'TrueTypeUnicode', '', 32);
    $pdf->SetFont($tahoma, '', 10, '', true);
    $pdf->AddPage();
    $pdf->AddFont($tahomabd);        //custom font
    $pdf->setListIndentWidth(4);

    $table_head_style = 'font-weight: bold; font-family: tahomabd; font-size: small; border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;';

    $html2 = '
<p style="text-align: center; font-family: tahomabd;">Prilog odluke <br>o usvajanju Plana korištenja godišnjih odmora zaposlenih u Ministarstvu <br> komunikacija i transporta Bosne i
hercegovine za ' . date('Y') . '</p>
<p style="text-align: center; font-family: tahomabd;">Plan <br> korištenja godišnjih odmora za ' . date('Y') . ' godinu zaposlenih u Ministarstvu komunikacija i 
<br> transporta Bosne i Hercegovine za ' . date('Y') . ' godinu</p>
<p style="text-align: justify-all;">U skladu sa planovima korištenja godišnjeg odmora za ' . date('Y') . '. godinu organizacionih jedinica Ministarstva 
komunikacija i transporta Bosne i Hercegovine, ovim planom se utvrđuje raspored i period korištenja godišnjeg odmora za zaposlene u Ministarstvu komunikacija i 
transporta Bosne i Hercegovine kako slijedi:</p>
';

    foreach ($data as $d) {
        $profiles = Profile::select('employee_no, fname, lname, egop_radno_mjesto, egop_ustrojstvena_jedinica, nadredjeni')->where("egop_ustrojstvena_jedinica='".$d['id']."'")->get();

        if (!empty($profiles)){
            $html2 .= '<p style="font-family: tahomabd;">' . $d['title'] . ' ('.$d['no'].')</p>
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center; border: 1px solid black; width: 5%;">Red br.</th>
                                <th style="text-align: center; border: 1px solid black; width: 20%;"> <br><br>Ime i prezime</th>
                                <th style="text-align: center; border: 1px solid black; width: 31%;"><br><br>Naziv radnog mjesta</th>
                                <th style="text-align: center; border: 1px solid black; width: 14%;">Ukupno trajanje godišnjeg odmora</th>
                                <th style="text-align: center; border: 1px solid black; width: 20%;"><br><br>Vrijeme korištenja godišnjeg odmora</th>
                                <th style="text-align: center; border: 1px solid black; width: 10%;"><br><br>Broj dana</th>
                            </tr>
                        </thead>
                    ';

            $count = 0;
            foreach ($profiles as $e){
                $html2 .= '<tr>
                            <td style="text-align: center; border: 1px solid black; width: 5%;">'.$count.'</td>
                            <td style="text-align: center; border: 1px solid black; width: 20%;">' . $e['fname'] . ' ' . $e['lname'] . '</td>
                            <td style="text-align: center; border: 1px solid black; width: 31%;">' . $e['egop_radno_mjesto'] . '</td>
                            <td style="text-align: center; border: 1px solid black; width: 14%;"></td>
                            <td style="text-align: center; border: 1px solid black; width: 20%;"></td>
                            <td style="text-align: center; border: 1px solid black; width: 10%;"></td>
                       </tr>
            ';
                $count++;
            }

            $html2 .= '</table>';

        }

    }

    $pdf->writeHTMLCell(0, 0, '', '', $html2, 0, 1, 0, true, '', true);

    if ($method == 'F'){
        $fileName = md5(time()).'.pdf';
        $pdf->Output(__DIR__ . '/files/plan-go/'.$fileName, 'F');
        return $fileName;
    }else{
        $fileName = md5(time()).'.pdf';
        $pdf->Output($fileName, 'I');
        //return $fileName;
    }

}

// $_user = _user(_decrypt($_SESSION['SESSION_USER']));

if(isset($_GET['employee_no'])){
    generatepdf($_GET['employee_no'], 'I');
}



if(isset($_POST['_user'])){
    $_user = $_POST['_user'];

    echo json_encode([
        'fileName' => generatepdf($_user, 'F')
    ]);
}

?>