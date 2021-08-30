<?php


if(isset($_POST['kategorija']) and isset($_POST['naziv_cilja']) and isset($_POST['opis_cilja']) and isset($_POST['tezina'])){

    // Imamo određene podatke i možemo spremati !!!
    $kategorija       = $_POST['kategorija'];
    $naziv_cilja      = str_replace("'", "&apos;", $_POST['naziv_cilja']);
    $opis_cilja       = str_replace("'", "&apos;", $_POST['opis_cilja']);
    $tezina           = round($_POST['tezina'], 2);
    $tezina           = number_format((float)$tezina, 2, '.', ''); // Always get two decimal points

    if($_POST['kategorija'] == 0 or empty($_POST['naziv_cilja']) or empty($_POST['opis_cilja']) or empty($_POST['tezina']) ) $warning_message = 'UUUUPS!! Neka od polja su prazna. Molimo popunite ih prije spremanja!';
    else{
        // Sve je okay i možemo insertovati u bazu podataka :))

        if(isset($_POST['id_usera'])){
            // Ako je postavljen ID sporazuma, onda unosimo vrijednost
            // Ako je postavljen ID, onda samo updejtujemo

            $id_usera     = $_POST['id_usera'];
            $za_mene      = $_POST['za_mene'];
            $radno_mjesto = $_POST['radno_mjesto'];
            $keywor       = time();

            if($za_mene){
                try{
                    $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] 
                        (
                            id_usera,
                            kategorija,
                            naziv_cilja,
                            opis_cilja,
                            tezina,
                            keywor,
                            owner,
                            for_me
                        )
                        VALUES (
                            '$id_usera',
                            '$kategorija',
                            '$naziv_cilja',
                            '$opis_cilja',
                            '$tezina',
                            '$keywor',
                            '$id_usera',
                            1
                        )
                    ");
                    $tezina = 0; $kategorija = null; $naziv_cilja = null; $opis_cilja = null; $kvalitativno = null; $kvalitativno = null; $tezina = null;
                    header("Refresh:0");
                }catch (PDOException $e){}
            }else{
                $radnaMjesta = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[users] where position_code = '$radno_mjesto'")->fetchAll();

                try{
                    $db->query("INSERT INTO [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] 
                            (
                                id_usera,
                                kategorija,
                                naziv_cilja,
                                opis_cilja,
                                tezina,
                                keywor,
                                owner,
                                radno_mjesto,
                                for_me
                            )
                            VALUES (
                                0,
                                '$kategorija',
                                '$naziv_cilja',
                                '$opis_cilja',
                                '$tezina',
                                '$keywor',
                                '$id_usera',
                                '$radno_mjesto',
                                0
                            )
                        ");
                }catch (\PDOException $e){
                    var_dump($e);
                }

                // header("Refresh:0");
//
//                foreach($radnaMjesta as $rm){
//                    $rm_id = $rm['user_id'];
//                    try{
//
//                        $tezina = 0; $kategorija = null; $naziv_cilja = null; $opis_cilja = null; $kvalitativno = null; $kvalitativno = null; $tezina = null;
//                        header("Refresh:0");
//                    }catch (PDOException $e){}
//                }
            }
        }else{
            $id = $_POST['keyword'];

            if(isset($_POST['za_mene'])){
                $za_mene      = $_POST['za_mene'];
                try{
                    $db->query("UPDATE [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] SET 
                    kategorija     = '$kategorija',
                    naziv_cilja    = '$naziv_cilja',
                    opis_cilja     = '$opis_cilja',
                    tezina         = '$tezina',
                    for_me         = '$za_mene'
                where keywor = ".$id);
                }catch (PDOException $e){return die($e->getMessage());}
            }else {

                try{
                    $db->query("UPDATE [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] SET 
                    kategorija     = '$kategorija',
                    naziv_cilja    = '$naziv_cilja',
                    opis_cilja     = '$opis_cilja',
                    tezina         = '$tezina'
                where keywor = ".$id);
                }catch (PDOException $e){return die($e->getMessage());}

//                $radno_mjesto = $_POST['radno_mjesto'];
//                $radnaMjesta = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[users] where position_code = '$radno_mjesto'")->fetchAll();
////
////                var_dump($radno_mjesto);
//                $uzorci = $db->query("SELECT * FROM [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] where keywor = ".$id)->fetchAll();
//
//                foreach ($uzorci as $uzorak){
//                    $id = $uzorak['id'];
//
//                    try{
//                        $db->query("UPDATE [c0_intranet2_raiff].[dbo].[pm_sporazumi_uzorci_ciljeva] SET
//                            kategorija     = '$kategorija',
//                            naziv_cilja    = '$naziv_cilja',
//                            opis_cilja     = '$opis_cilja',
//                            tezina         = '$tezina',
//                            radno_mjesto   = '$radno_mjesto'
//                        where id = ".$id);
//                    }catch (PDOException $e){return die($e->getMessage());}
//                }

            }

        }
    }
}