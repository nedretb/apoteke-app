<?php
include '../../../configuration.php';
include $root . '/classes/API/SoapEvents.php';
include 'func.php';

if (!class_exists('SoapClient')) {
    die ("You haven't installed the PHP-Soap module.");
}
use SoapEvents as Soap;
/*
 *  To read data from Soap, you need to call make an request as show below:
 *  Soap::getData($uri, $method, ['key' => 'value'])
 *  It would return object of stdClass
 */

 $data = Soap::getData('http://172.16.10.38:5203/PRH_WS/WS/JU%20Apoteke%20Sarajevo/Page/PRHWS_Employee', 'Read', ['No' => 100]);


 /************ USERS TABELA ************/
$orgJedBr = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[systematization] WHERE s_title='".$data->PRHWS_Employee->GetGlobDim1Name_Rec_Global_Dimension_1_Code."'")->fetch()['id'];
$usersTable = ['fname' => $data->PRHWS_Employee->First_Name,
               'lname' => $data->PRHWS_Employee->Last_Name,
               'JMB' => $data->PRHWS_Employee->Employee_PUID,
               'position' => $data->PRHWS_Employee->Job_Title,
               'position_code' => $data->PRHWS_Employee->Job_Position_Code,
               'B_1_description' =>  $data->PRHWS_Employee->GetGlobDim1Name_Rec_Global_Dimension_1_Code,
               'parent' => $data->PRHWS_Employee->Manager_No,
               'employment_date' => $data->PRHWS_Employee->Employment_Date,
               'nadredjeni' => $data->PRHWS_Employee->Manager_No,
               'egop_ustrojstvena_jedinica' => $orgJedBr,
               'egop_radno_mjesto' => $data->PRHWS_Employee->Job_Title,
               'djevojacko_prezime' => $data->PRHWS_Employee->Maiden_name,
               'dezurstva' => $data->PRHWS_Employee->GetDuty,
               'br_sati' => $data->PRHWS_Employee->GetDutyHours];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users] SET
                                   [fname]=?
                                  ,[lname]=?
                                  ,[JMB]=?
                                  ,[position]=?
                                  ,[position_code]=?
                                  ,[B_1_description]=?
                                  ,[parent]=?
                                  ,[employment_date]=?
                                  ,[nadredjeni]=?
                                  ,[egop_ustrojstvena_jedinica]=?
                                  ,[egop_radno_mjesto]=?
                                  ,[djevojacko_prezime]=?
                                  ,[dezurstva]=?
                                  ,[br_sati]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersTable['fname'], $usersTable['lname'], $usersTable['JMB'], $usersTable['position'],
        $usersTable['position_code'], $usersTable['B_1_description'], $usersTable['parent'], $usersTable['employment_date'],
        $usersTable['nadredjeni'], $usersTable['egop_ustrojstvena_jedinica'], $usersTable['egop_radno_mjesto'], $usersTable['djevojacko_prezime'],
        $usersTable['dezurstva'], $usersTable['br_sati']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}



/************ USERS KONTAKT INFORMACIJE TABELA ************/
$home = preg_replace('/[^0-9.]+/', '', $data->PRHWS_Employee->Phone_No);
$homePozivni = substr($home, 0, -6);
$homeRegionalni = substr($home, 3, -3);
$homeNo = substr($home, 6);

$phone = preg_replace('/[^0-9.]+/', '', $data->PRHWS_Employee->Mobile_Phone_No);
$phonePozivni = substr($phone, 0, -6);
$phoneRegionalni = substr($phone, 3, -3);
$phoneNo = substr($phone, 6);

$usersKontaktTable = ['pozivni_broj' => $homePozivni,
    'kucni_regionalni_broj' => $homeRegionalni,
    'kucni_broj' => $homeNo,
    'privatni_mobitel_broj' => $phonePozivni,
    'mobitel_regionalni_kod' =>  $phoneRegionalni,
    'mobitel_broj' => $phoneNo,
    'privatna_email_adresa' => $data->PRHWS_Employee->E_Mail];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__kontakt_informacije] SET
                                   [pozivni_broj]=?
                                  ,[kucni_regionalni_broj]=?
                                  ,[kucni_broj]=?
                                  ,[privatni_mobitel_broj]=?
                                  ,[mobitel_regionalni_kod]=?
                                  ,[mobitel_broj]=?
                                  ,[privatna_email_adresa]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersKontaktTable['pozivni_broj'], $usersKontaktTable['kucni_regionalni_broj'], $usersKontaktTable['kucni_broj'],
        $usersKontaktTable['privatni_mobitel_broj'], $usersKontaktTable['mobitel_regionalni_kod'], $usersKontaktTable['mobitel_broj'],
        $usersKontaktTable['privatna_email_adresa']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS LICNI DOKUMENTI TABELA ************/
$usersLicniDokumentiTable = ['broj_licne_karte' => $data->PRHWS_Employee->ID_Card_No,
    'drzavljanstvo' => $data->PRHWS_Employee->Nationality];

try {
    $sqlStmt ="UPDATE [c0_intranet2_apoteke].[dbo].[users__licni_dokumenti] SET
                                   [broj_licne_karte]=?
                                   ,[drzavljanstvo]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersLicniDokumentiTable['broj_licne_karte'], $usersLicniDokumentiTable['drzavljanstvo']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS PODACI O RODJENU TABELA ************/
$usersRodjenjeTable = ['datum_rodjena' => $data->PRHWS_Employee->Birth_Date,
    'naziv_opstine_rodjenja' => $data->PRHWS_Employee->Birth_Municipality,
    'mjesto_rodjenja' => $data->PRHWS_Employee->Birth_City,
    'sifra_drzave_rodjenja' =>  $data->PRHWS_Employee->Birth_Country,
    'grad_rodjenja' => $data->PRHWS_Employee->Birth_City];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__podaci_o_rodjenju] SET
                                   [datum_rodjena]=?
                                  ,[naziv_opstine_rodjenja]=?
                                  ,[mjesto_rodjenja]=?
                                  ,[sifra_drzave_rodjenja]=?
                                  ,[grad_rodjenja]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersRodjenjeTable['datum_rodjena'], $usersRodjenjeTable['naziv_opstine_rodjenja'], $usersRodjenjeTable['mjesto_rodjenja'],
        $usersRodjenjeTable['sifra_drzave_rodjenja'], $usersRodjenjeTable['grad_rodjenja']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS PODACI O STANOVANJU TABELA ************/
$usersStanovanjeTable = ['adresa' => $data->PRHWS_Employee->Address,
    'sifra_opcine' => $data->PRHWS_Employee->Municipality_Code,
    'naziv_opcine' => $data->PRHWS_Employee->GetMunicipalityName_Rec_Municipality_Code,
    'grad' =>  $data->PRHWS_Employee->City,
    'postanski_broj' => $data->PRHWS_Employee->Post_Code];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__podaci_o_stanovanju] SET
                                   [adresa]=?
                                  ,[sifra_opcine]=?
                                  ,[naziv_opcine]=?
                                  ,[grad]=?
                                  ,[postanski_broj]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersStanovanjeTable['adresa'], $usersStanovanjeTable['sifra_opcine'], $usersStanovanjeTable['naziv_opcine'],
        $usersStanovanjeTable['grad'], $usersStanovanjeTable['postanski_broj']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS PORESKA OLAKSICA I PREVOZ TABELA ************/
$usersPorezTable = ['poreska_kartica' => $data->PRHWS_Employee->GetFond_Rec_No,
    'koeficijent_olaksice' => $data->PRHWS_Employee->GetIncomeCoef];


try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__poreska_olaksica_i_prevoz] SET
                                   [poreska_kartica]=?
                                  ,[koeficijent_olaksice]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersPorezTable['poreska_kartica'], $usersPorezTable['koeficijent_olaksice']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}



/************ USERS RADNI STAZ TABELA ************/
$prethodniStaz = explode('-', $data->PRHWS_Employee->Past_Service);
$apotekeStaz = explode('-', $data->PRHWS_Employee->EmploymentDurationPeriod_Rec);
$ukupniStaz = explode('-', $data->PRHWS_Employee->SumOfEmploymentDurations_Rec_Past_Service__x002C_EmploymentDurationPeriod_Rec);

$usersStazTable = ['doneseni_radni_staz_g' => $prethodniStaz[0],
    'doneseni_radni_staz_m' => $prethodniStaz[1],
    'doneseni_radni_staz_d' => $prethodniStaz[2],
    'radni_staz_u_kompaniji_g' =>  $apotekeStaz[0],
    'radni_staz_u_kompaniji_m' => $apotekeStaz[1],
    'radni_staz_u_kompaniji_d' =>  $apotekeStaz[2],
    'ukupan_radni_staz_g' =>  $ukupniStaz[0],
    'ukupan_radni_staz_m' =>  $ukupniStaz[1],
    'ukupan_radni_staz_d' =>  $ukupniStaz[2]];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__radni_staz] SET
                                   [doneseni_radni_staz_g]=?
                                  ,[doneseni_radni_staz_m]=?
                                  ,[doneseni_radni_staz_d]=?
                                  ,[radni_staz_u_kompaniji_g]=?
                                  ,[radni_staz_u_kompaniji_m]=?
                                  ,[radni_staz_u_kompaniji_d]=?
                                  ,[ukupan_radni_staz_g]=?
                                  ,[ukupan_radni_staz_m]=?
                                  ,[ukupan_radni_staz_d]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersStazTable['doneseni_radni_staz_g'], $usersStazTable['doneseni_radni_staz_m'], $usersStazTable['doneseni_radni_staz_d'],
        $usersStazTable['radni_staz_u_kompaniji_g'], $usersStazTable['doneseni_radni_staz_m'], $usersStazTable['doneseni_radni_staz_d'],
        $usersStazTable['ukupan_radni_staz_g'], $usersStazTable['doneseni_radni_staz_m'], $usersStazTable['doneseni_radni_staz_d']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS ZDRAVSTVENO STANJE TABELA ************/
$usersZdravstvenoTable = ['invalid_' => $data->PRHWS_Employee->Disability,
    'stepen_invalidnosti' => $data->PRHWS_Employee->GetTypeOfDisability];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__zdravstveno_stanje] SET
                                   [invalid_]=?
                                  ,[stepen_invalidnosti]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersZdravstvenoTable['invalid_'], $usersZdravstvenoTable['stepen_invalidnosti']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}




/************ USERS SKOLOVANJE TABELA ************/
$usersSkolovanjeTable = ['strucna_sprema' => $data->PRHWS_Employee->Education_Degree,
    'zavrsena_obrazovna_ustanova' => $data->PRHWS_Employee->GetEduName_Rec_No__x002C_Rec_Education_Degree,
    'zvanje' => $data->PRHWS_Employee->GetEduTitle_Rec_No__x002C_Rec_Education_Degree];

try {
    $sqlStmt = "UPDATE [c0_intranet2_apoteke].[dbo].[users__skolovanje] SET
                                   [strucna_sprema]=?
                                  ,[zavrsena_obrazovna_ustanova]=?
                                  ,[zvanje]=? WHERE employee_no=".$data->PRHWS_Employee->No;
    $sqlInjection = $db->prepare($sqlStmt);
    $sqlInjection->execute([$usersSkolovanjeTable['strucna_sprema'], $usersSkolovanjeTable['zavrsena_obrazovna_ustanova'], $usersSkolovanjeTable['zvanje']]);
} catch (Exception $e){logThis($e->getMessage(), $e->getLine(), $e->getFile());}
