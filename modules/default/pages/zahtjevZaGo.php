<?php
require_once '../../../configuration.php';
require_once '../../../classes/API/SoapEvents.php';
include_once $root . '/CORE/classes/Model.php';       // Need to be extended
foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;

include('zahtjevZaGo.pdf.php');
include('table_used.php');

use SoapEvents as Soap;

if(isset($_POST['user'])){
    $employee_no = $_POST['user'];
    $day_from = $_POST['df'];
    $day_to   = $_POST['dt'];
    $month_from = $_POST['mf'];
    $month_to   = $_POST['mt'];
    $year_id    = $_POST['year_id'];

    $year = $db->query("select year, user_id from [c0_intranet2_apoteke].[dbo].[hourlyrate_year] where id = ".$year_id)->fetch();
    $date_from = $day_from.'.'.$month_from.'.'.$year['year'];
    $date_to   = $day_to.'.'.$month_to.'.'.$year['year'];

    $user_id = $db->query("select [Date], user_id, employee_no from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where year_id=".$year_id)->fetch();
    $user_data = $db->query("select fname, lname, position, parent from [c0_intranet2_apoteke].[dbo].[users] where user_id=". $user_id['user_id'])->fetch();
    $parent_data = $db->query("select fname, lname from [c0_intranet2_apoteke].[dbo].[users] where employee_no='".$user_data['parent']."'")->fetch();
    $vac_data_curr_year = $db->query("select * from [c0_intranet2_apoteke].[dbo].[vacation_statistics] where employee_no=".$user_id['employee_no']. " and year=".date('Y'))->fetch();

    // Generate PDF
    $pdf = generatepdf($user_data, $parent_data, $date_from, $date_to, $vac_data_curr_year);


    try{
        $data = Soap::getData(
            'http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?WSDL',
            'KreirajPredmet2',
            [
                'userName' => 'VM\egop10service',
                'upisnaKnjiga' => 'NP', // Uvijek NP
                'vrstaPredmeta' => 590, // Zahtjev za godišnji odmor
                'nazivPredmeta' => 'John Doe - Zahtjev za korištenje godišnjeg odmora',
                'nadleznaOrgJedinica' => 7, // Odsjek za personalne i opće poslove
                'subjektOznaka' => 15 // Za predmet "plan korištenja GO" - (15 - Ministarstvo komunikacija i prometa BiH na DEMO)
            ]
        );

        $create = Predmet::insert([
            'uredska_godina' => $data->KreirajPredmet2Result->uredskaGodina,
            'rbr_predmeta' => $data->KreirajPredmet2Result->rbrPredmeta,
            'vrsta_predmeta' => 590,
            'klasifikacijskaOznaka' => $data->KreirajPredmet2Result->klasifikacijskaOznaka,
            'created_at' => date('Y-m-d')
        ]);
        $predmet = Predmet::where('uredska_godina = '.$data->KreirajPredmet2Result->uredskaGodina)
            ->where('rbr_predmeta = '.$data->KreirajPredmet2Result->rbrPredmeta)->first();

        try{
            $pismenoResp = Soap::getData(
                'http://10.0.8.41/EAI_MKT/ServicePismeno.asmx?WSDL',
                'KreirajPismeno2',
                [
                    'userName' => 'VM\egop10service',
                    'vrstaPismena' => 70,                          // Plan korištenja godišnjih odmora
                    'rbrSpisa' => $predmet['rbr_predmeta'],        // Redni broj predmeta (ID) u koji se ulaže pismeno (dobiva se iz metode KreirajPredmet2)
                    'uredskaGodina' => $predmet['uredska_godina'], // Uredska godina predmeta u koji se ulaže pismeno
                    'subjektOznaka' => 3,                         // Za predmet "plan korištenja GO" - (15 - Ministarstvo komunikacija i prometa BiH na DEMO)
                    'nazivPismena' => 'John Doe, Zahtjev za korištenje godišnjeg odmora'
                ]
            );
            try{
                $pismeno = Pismeno::insert([
                    'uredska_godina' => $predmet['uredska_godina'],
                    'rbr_predmeta' => $predmet['rbr_predmeta'],
                    'jop' => $pismenoResp->KreirajPismeno2Result->jop,
                    'UrBroj' => $pismenoResp->KreirajPismeno2Result->UrBroj,
                    'status' => 0,
                    'kategorija' => 'zGO',
                    'dokument' => $pdf,
                    'employee_no' => $employee_no,
                    'created_at' => date('Y-m-d')
                ]);

                $pismeno = Pismeno::where('jop = '.($pismenoResp->KreirajPismeno2Result->jop))->first();

                try{
                    $handle = fopen($root.'/modules/default/pages/files/zahtjevi-go/'.$pdf, "r");
                    $contents = fread($handle, filesize($root.'/modules/default/pages/files/zahtjevi-go/'.$pdf));
                    fclose($handle);

                    $pismenoFileResp = Soap::getData(
                        'http://10.0.8.41/EAI_MKT/ServicePismeno.asmx?WSDL',
                        'KreirajDokumentZaPismeno',
                        [
                            'userName' => 'VM\egop10service',
                            'jop' => $pismeno['jop'],
                            'extension' => '.pdf',
                            'attachment' => $contents
                        ]
                    );
                }catch (\Exception $e){}
            }catch (\Exception $e){}
        }catch (\Exception $e){}
    }catch (\Exception $e){}
}


?>