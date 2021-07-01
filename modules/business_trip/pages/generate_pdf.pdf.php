<?php

require_once($root.'/tcpdf/tcpdf.php');

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


function generatepdf($state, $user, $data, $country, $doc)
{
    ///////////////////// CONFIG

///////////////////// END CONFIG


    $pdf = new mypdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MKT');
    $pdf->SetTitle('Službeni put rješenje');
    $pdf->SetSubject('Službeni put rješenje');
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
    $tahoma = TCPDF_FONTS::addTTFfont('tnr.ttf', 'TrueTypeUnicode', '', 32);
    $tahomabd = TCPDF_FONTS::addTTFfont('tnrb.ttf', 'TrueTypeUnicode', '', 32);
    $pdf->SetFont($tahoma, '', 10, '', true);
    $pdf->AddPage();
    $pdf->AddFont($tahomabd);        //custom font
    $pdf->setListIndentWidth(4);



    $html2 = '<p>Broj:'.$data['id'].'
</p>
<p>Sarajevo, '.date("d.m.Y").'. godine
</p>
<p style="text-align: justify-all">
Na osnovu člana 61.stav 2. Zakona o upravi („Službeni glasnik BiH“, broj
    32/02 i 102/09), a u skladu sa odredbama člana 10. Odluke o načinu i
    postupku ostavrivanja prava zaposlenih u institucijama Bosne i Hercegovine
    na naknadu za službeno putovanje („Službeni glasnik BiH“, br. 6/12, 10/18 i
    75/18) ministar komunikacija i prometa Bosne i Hercegovine d o n o s i
</p>
<p align="center" style="font-family: tnrb;">
    <strong>O D L U K U</strong>
</p>
<p align="center" style="font-family: tnrb;">
    <strong>o upućivanju na službeno putovanje u inostranstvo</strong>
</p>
<p align="center">
    <strong></strong>
</p>

<ol style="padding-left: 200px !important; text-align: justify-all; line-height: 150%;">
    <li>'.$data['fname'].' '.$data['lname'].', uposlen/a na radnom
        mjestu '.$data['egop_radno_mjesto'].' u Sektoru '.$data['sector'].' u
        Ministarstvu komunikacija i prometa Bosne i Hercegovine <strong style="font-family: tnrb;">upućuje se</strong> na službeno putovanje u inostranstvo u   
         <b style="font-family: tnrb;">mjesto/grad</b> '.$data['odredisni_grad'].'<strong style="font-family: tnrb;"> država </strong>
         '.$country['name'].' <b style="font-family: tnrb;">u periodu od</b> '.date('Y.m.d', strtotime($data['pocetak_datum'])).' do 
         '.date('Y.m.d', strtotime($data['kraj_datum'])).', radi izvršavanja službenog
        zadatka '.$data['svrha'].' (navesti <strong style="font-family: tnrb;">svrhu</strong> putovanja).
    </li>
   
    <li>Troškove <strong style="font-family: tnrb;">ishrane/dnevnica</strong> službenog putovanja iz tačke
    1. ove odluke snosi _____________________.
    </li>
    
    <li>Troškove <strong style="font-family: tnrb;">smještaja </strong>službenog putovanja iz tačke 1. ove
        odluke snosi _____________________.
    </li>
    
    <li>Troškove <strong style="font-family: tnrb;">prijevoza </strong>službenog putovanja iz tačke 1. ove
        odluke snosi _____________________.
    </li>
    
    <li><strong style="font-family: tnrb;">Visina dnevnice</strong> za službeno putovanje iz tačke 1. ove
        odluke iznosi '.$data['iznos_akontacije'].'KM i umanjuje se za 70% za ona 24 sata putovanja u
        kojima je osigurana besplatna ishrana (minimalno dva obroka).
    </li>
    
    <li>Imenovani/a će za službeno putovanje koristiti    <strong style="font-family: tnrb;">prijevozno sredstvo </strong>'.$data['vrsta_transporta'].' i za službeno putovanje
        određuje se <strong style="font-family: tnrb;">pravac</strong> putovanja
        '.$data['grad_polaska'].'-'.$data['odredisni_grad'].'-'.$data['grad_polaska'].'.
    </li>
    
    <li>Imenovanom/oj za službeno putovanje iz tačke 1.ove odluke    <strong style="font-family: tnrb;">ne isplaćuje/isplaćuje</strong> se akontacija u visini od
        __________KM.
    </li>
    
    <li>U skladu sa tačkama ove odluke imenovanom/oj se izdaje i nalog za
        službeno putovanje.
    </li>
    
    <li>Kontrolu obračuna troškova službenog putovanja iz tačke 1.ove odluke
        izvršit će Odsjek za finansijsko-materijalne poslove u roku od pet (5) dana
        od dana dostavljanja naloga za službeno putovanje na obračun.
    </li>
    
    <li>Za realizaciju ove odluke zadužuje se Sektor za pravne i finansijske
        poslove u saradnji sa nadležnim Sektorom.
    </li>
</ol>

<p>
</p>
<p align="center">
    <strong style="font-family: tnrb;">O b r a z l o ž e nj e</strong>
</p>
<p style="text-align: justify-all;">Na osnovu uvida u priloženu dokumentaciju i službenu prepisku, cijeneći
    svrhu službenog putovanja imenovanog, te raspoloživa finansijska sredstva,
    utvrđena je kao potreba da se imenovani/a uputi na službeno putovanje iz
    tačke 1. ove odluke, te odlučeno je kao u dispozitivu ove odluke.
</p>
<p align="right">
    M I N I S T A R
</p>
<p align="right">
    _____________
</p>
<p>
    Dostaviti:
</p>
<p>
    - imenovani/a
</p>
<p>
    - odsjek za finansijsko-materijalne poslove
</p>
<p>
    - a/a
</p>
<strong>
    <br clear="all"/>
</strong>';

    $html3 = '<p align="center">
    <strong style="font-family: tnrb;">ZAHTJEV ZA ODOBRAVANJE SLUŽBENOG PUTOVANJA</strong>
</p>

<p align="center">
    <strong></strong>
</p>
<p>
    IME I PREZIME: '.$data['fname'].' '.$data['lname'].'
</p>
<p>
    RADNO MJESTO: '.$data['egop_radno_mjesto'].'
</p>
<p>
    DRŽAVA I GRAD PUTOVANJA: '.$country['name'].', '.$data['odredisni_grad'].'
</p>
<p>
    SVRHA PUTOVANJA: '.$data['svrha'].'
</p>
<p>
    TROŠKOVE SMJEŠTAJA SNOSI:______________________________________
</p>
<p>
    TROŠKOVE PRIJEVOZA SNOSI:______________________________________
</p>
<p>
    TROŠKOVE ISHRANE SNOSI:_________________________________________
</p>
<p>
    PRIJEVOZNO SREDSTVO: '.$data['vrsta_transporta'].'
</p>
<p>
    PERIOD PLANIRANOG PUTOVANJA: od '.date('d.m.Y', strtotime($data['pocetak_datum'])).' do '.date('d.m.Y', strtotime($data['kraj_datum'])).'
</p>
<p>
    PRIJEDLOG IZNOSA POTREBNE AKONTACIJE: '.$data['iznos_akontacije'].'
</p>
<p>
    PODNOSILAC ZAHTJEVA
</p>
<p>
    ________________________
</p>
<p>
    NEPOSREDNI RUKOVODILAC
</p>
<p>
    ________________________
</p>
<p>
    <strong></strong>
</p>
<p>
    <strong></strong>
</p>
<p>
    RUKOVODEĆI SLUŽBENIK
</p>
<p>
    _________________________
</p>
<p>
    TAJNIK
</p>
<p>
    _________________________
</p>
<p>
    ODOBRIO
</p>
<p>
    __________________________
</p>
<p>
    Sarajevo,___________________
</p>
<p>
    ___________________________
</p>
<p style="font-family: tnrb;">
    (Ime i prezime)
</p>';

    if ($doc == 'odluka'){
        $pdf->writeHTMLCell(0, 0, '', '', $html2, 0, 1, 0, true, '', true);
        $pdf->Output('Službeni put odluka - '.$data['fname']. ' '. $data['lname'] . '.pdf', 'I');
    }
    else{
        $pdf->writeHTMLCell(0, 0, '', '', $html3, 0, 1, 0, true, '', true);
        $pdf->Output('Službeni put zahtjev - '.$data['fname']. ' '. $data['lname'] . '.pdf', 'I');
    }




}