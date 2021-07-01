<?php
use SoapEvents as Soap;
use PLanGo as PGO;

// ------------------------------------------------------------------------------------------------------------------ //

if(isset($_GET['kreiraj-plan-go'])){
    $created_at = date('Y-m-d H:i');

    /*
     *  Prvo je potrebno provjeriti da li postoji kreiran predmet za uredsku godinu date('Y')
     *  Ukoliko nema, potrebno je da se kreira zahtjev za kreiranje predmeta. U tom slučaju, vratit će nam dvije informaicije :
     *      - uredsku godinu
     *      - redni broj predmeta
     *  Nakon što dobijemo povratnu informaciju o kreiranju predmeta, potrebno je da isti kreiranmo u našoj bazi podataka
     *
     *  Vrsta predmeta za Plan godišnjeg odmora je -- 589
     */
    // $data = Soap::getData('http://10.0.8.41/EAI_MKT/ServicePismeno.asmx?WSDL', 'DohvatiPodatkePismena', ['userName' => 'VM\egop10service', 'jop' => '93']);

    $uredska_godina = date('Y'); $vrstaPredmeta = 589;
    $rb_predmeta = 'RBP - 0921 / 21';
    $status = 0;

    try{
        $predmet = Predmet::where('uredska_godina = '.$uredska_godina)
            ->where('vrsta_predmeta = '.$vrstaPredmeta)->first();

        if(!$predmet){
            // If there is no plan, create an request, and create new sample inside database

            $data = Soap::getData(
                'http://10.0.8.41/EAI_MKT/ServicePredmet.asmx?WSDL',
                'KreirajPredmet2',
                [
                    'userName' => 'VM\epinsatest',
                    'upisnaKnjiga' => 'NP', // Uvijek NP
                    'vrstaPredmeta' => $vrstaPredmeta, // Plan korištenja GO
                    'nadleznaOrgJedinica' => 7, // Odsjek za personalne i opće poslove
                    'subjektOznaka' => 15 // Za predmet "plan korištenja GO" - (15 - Ministarstvo komunikacija i prometa BiH na DEMO)
                ]
            );

            $create = Predmet::insert([
                'uredska_godina' => $data->KreirajPredmet2Result->uredskaGodina,
                'rbr_predmeta' => $data->KreirajPredmet2Result->rbrPredmeta,
                'vrsta_predmeta' => $vrstaPredmeta,
                'klasifikacijskaOznaka' => $data->KreirajPredmet2Result->klasifikacijskaOznaka,
                'created_at' => date('Y-m-d')
            ]);

            $predmet = Predmet::where('uredska_godina = '.$data->KreirajPredmet2Result->uredskaGodina)
                ->where('vrsta_predmeta = '.$vrstaPredmeta)->first();
        }
    }catch (\Exception $e){var_dump($e);}

    if($predmet){
        /*
         *  Sada je potrebno kreirati pismeno. Pismeno se kreira pozivajući metodu KreirajPismeno2
         *  Vrsta pismena je fiksna - 68, rbrSpisa je redni broj predmeta (ID) kao i uredskaGodina
         *  nazivPismena se treba dodijeljivati dinamički - "Plan korištenja godišnjih odmora za 20xx godinu"
         */


        $pismenoResp = Soap::getData(
            'http://10.0.8.41/EAI_MKT/ServicePismeno.asmx?WSDL',
            'KreirajPismeno2',
            [
                'userName' => 'VM\epinsatest',
                'vrstaPismena' => 68,                          // Plan korištenja godišnjih odmora
                'rbrSpisa' => $predmet['rbr_predmeta'],        // Redni broj predmeta (ID) u koji se ulaže pismeno (dobiva se iz metode KreirajPredmet2)
                'uredskaGodina' => $predmet['uredska_godina'], // Uredska godina predmeta u koji se ulaže pismeno
                'subjektOznaka' => 15,                         // Za predmet "plan korištenja GO" - (15 - Ministarstvo komunikacija i prometa BiH na DEMO)
                'nazivPismena' => 'Plan korištenja godišnjih odmora za '.date('Y').' godinu'
            ]
        );

        try{
            $plan = PGO::createPlan(); // Kreiramo plan, dobijemo naziv PDF dokumenta

            $pismeno = Pismeno::insert([
                'uredska_godina' => $predmet['uredska_godina'],
                'rbr_predmeta' => $predmet['rbr_predmeta'],
                'jop' => $pismenoResp->KreirajPismeno2Result->jop,
                'UrBroj' => $pismenoResp->KreirajPismeno2Result->UrBroj,
                'status' => 0,
                'dokument' => $plan,
                'kategorija' => 'pGO',
                'created_at' => date('Y-m-d')
            ]);

            $pismeno = Pismeno::where('jop = '.($pismenoResp->KreirajPismeno2Result->jop))->first();

            try{

                $handle = fopen($root.'/modules/default/pages/files/plan-go/'.$plan, "r");
                $contents = fread($handle, filesize($root.'/modules/default/pages/files/plan-go/'.$plan));
                fclose($handle);

                $pismenoFileResp = Soap::getData(
                    'http://10.0.8.41/EAI_MKT/ServicePismeno.asmx?WSDL',
                    'KreirajDokumentZaPismeno',
                    [
                        'userName' => 'VM\epinsatest',
                        'jop' => $pismeno['jop'],
                        'extension' => '.pdf',
                        'attachment' => $contents
                    ]
                );
            }catch (\Exception $e){}
        }catch (\Exception $e){}
    }

    header('Location: ?m=work_booklet&p=pregled-planova');
}