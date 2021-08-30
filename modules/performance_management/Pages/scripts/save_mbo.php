<?php


function sendEmaill($from, $to, $message, $subject = 'Performance management'){
    require_once 'lib/PHPMailer/PHPMailer.php';
    require_once 'lib/PHPMailer/SMTP.php';
    require_once 'lib/PHPMailer/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->CharSet = "UTF-8";

    $mail->IsSMTP();
    $mail->isHTML(true);  // Set email format to HTML

    $mail->Host = "barbbcom";
    $mail->Port = 25;

    $mail->setFrom($from, "Obavijesti HR");
    $mail->addAddress($to);

    $mail->Subject = $subject;
    $mail->Body = $message;

    if (!$mail->send()) {
        var_dump($mail->ErrorInfo);
    }
}

function triggerByAgreement($db, $id, $what){
    try{
        $user_id = $db->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where id = ".$id)->fetch();
        $user_id = $user_id['user_id'];

        $usr = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$user_id)->fetch();
        $par = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where employee_no = ".$usr['parent'])->fetch();

        // Provjerimo ima li impersonatora, ako nema onda ćemo slati njemu email, ako ima onda impersonatoru : )
        $impersonator = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where user_id = ".$par['user_id'])->fetchAll();
        if(count($impersonator)){
            $par = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$impersonator[0]['impersonator_id'])->fetch();
        }

        if($what == 1){
            // Poslao sporazum
            $date = date('Y-m-d');
            $phase_1 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 1 ")->fetchAll());
            $subject = "MBO ".date('Y')." ".$usr['fname'].' '.$usr['lname'];

            if($phase_1){
                $message = "Radnik ".$usr['fname'].' '.$usr['lname']." je završio planiranje Sporazuma o radnom učinku za ".date('Y').". Potrebno je da pristupite istom na link (), izvršite provjeru / korekciju sadržaja i prihvatite isti ukoliko ste saglasni.";
            }else{
                $message = "Radnik ".$usr['fname'].' '.$usr['lname']." je završio revidiranje Sporazuma o radnom učinku za ".date('Y').". Potrebno je da pristupite istom na link (), izvršite provjeru / korekciju sadržaja i prihvatite isti ukoliko ste saglasni.";
            }

            sendEmaill($usr['email_company'], $par['email_company'], $message, $subject);
        }else if($what == 2){
            // Potvrdio manager
            $subject = "MBO ".date('Y')." ".$par['fname'].' '.$par['lname'];
            $message = "Vaš rukovodioc je pregledao sporazum o radnom učinku za ".date('Y').", molimo Vas da istom pristupite na link () i izvršite prihvatanje sa svoje strane.";

            sendEmaill($par['email_company'], $usr['email_company'], $message, $subject);
        }else{
            // Potvrdio korisnik
        }
    }catch (PDOException $e){var_dump($e);}
}

/***********************************************************************************************************************
 *
 *      Ciljevi
 *
 **********************************************************************************************************************/

if(isset($_POST['kategorija']) and isset($_POST['naziv_cilja']) and isset($_POST['opis_cilja']) and isset($_POST['tezina'])){

    // Imamo određene podatke i možemo spremati !!!
    $kategorija       = $_POST['kategorija'];
    $naziv_cilja      = str_replace("'", "&apos;", $_POST['naziv_cilja']);
    $opis_cilja       = str_replace("'", "&apos;", $_POST['opis_cilja']);
    $tezina           = round($_POST['tezina'], 2);
    $tezina           = number_format((float)$tezina, 2, '.', ''); // Always get two decimal points
    $created_at       = date('Y-m-d H:i:s');

    if($_POST['kategorija'] == 0 or empty($_POST['naziv_cilja']) or empty($_POST['opis_cilja']) or empty($_POST['tezina']) ) $warning_message = 'UUUUPS!! Neka od polja su prazna. Molimo popunite ih prije spremanja!';
    else{
        // Sve je okay i možemo insertovati u bazu podataka :))

        if(isset($_POST['id_sporazuma'])){
            // Ako je postavljen ID sporazuma, onda unosimo vrijednost
            // Ako je postavljen ID, onda samo updejtujemo

            $id_sporazuma     = $_POST['id_sporazuma'];


            try{
                $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] 
            (
                id_sporazuma,
                kategorija,
                naziv_cilja,
                opis_cilja,
                tezina,
                created_at
            )
            VALUES (
                '$id_sporazuma',
                '$kategorija',
                '$naziv_cilja',
                '$opis_cilja',
                '$tezina',
                '$created_at'
            )
            ");

                $tezina = 0; $kategorija = null; $naziv_cilja = null; $opis_cilja = null; $kvalitativno = null; $kvalitativno = null; $tezina = null;
                header("Refresh:0");
            }catch (PDOException $e){return $e->getMessage(); die();}
        }else{
            $id = $_POST['id'];

            $date = date('Y-m-d');
            $date_is_fine = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());
            if($date_is_fine){
                // Ako smo u periodu uređivanja onda kada uredimo nešto, kreiramo sigurnosnu kopiju toga nečega !
                $user_id = $_user['user_id'];

                $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] SET disabled = '{$user_id}', created_at = '{$created_at}' where id = ".$id); // Postavimo disabled as true
                $uzorak = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id = ".$id)->fetch();

                $id_sporazuma = $uzorak['id_sporazuma'];


                $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] 
                    (
                        id_sporazuma,
                        kategorija,
                        naziv_cilja,
                        opis_cilja,
                        tezina,
                        created_at,
                        last_one
                    )
                    VALUES (
                        '$id_sporazuma',
                        '$kategorija',
                        '$naziv_cilja',
                        '$opis_cilja',
                        '$tezina',
                        '$created_at',
                        '$id'
                    )
                    ");
            }else{
                try{
                    $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] SET 
                    kategorija     = '$kategorija',
                    naziv_cilja    = '$naziv_cilja',
                    opis_cilja     = '$opis_cilja',
                    tezina         = '$tezina'
                where id = ".$id);
                }catch (PDOException $e){return die($e->getMessage());}
            }
        }
    }
 }
// Brisanje elemenata
if(isset($_GET['trigger_act_del'])){
    $id_of = $_GET['trigger_act_del'];

    $date = date('Y-m-d');
    $date_is_fine = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());

    try{
        if($date_is_fine){
            $user_id = $_user['user_id'];
            $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] SET disabled = '{$user_id}' where id = ".$id_of);
        }else $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id=".$id_of);



    }catch (PDOException $e){
        var_dump($e);
    }
    header('Location: '.$_SESSION['escaped_url']);
}

if(isset($_GET['trigger_act_dell'])){
    $id_of = $_GET['trigger_act_dell'];

    $date = date('Y-m-d');
    $date_is_fine = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());

    try{
        $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] where id=".$id_of);
    }catch (PDOException $e){
        var_dump($e);
    }

    header('Location: '.$_SESSION['escaped_url']);
}
// Pošaljite sporazum
if(isset($_GET['send_items'])){
    // Kada je sent postavljeno na jedan, u tom trenutku više nije moguće dodavati nove ciljeve
    // TODO U ovom trenutku potrebno je omogućiti slanje emailova prvim nadređenima !!!!
    if(isset($id_of_agreement['id'])){
        $warning_message = $agree->updateSent($id_of_agreement['id']);
        $agr_id = $id_of_agreement['id'];
        //triggerByAgreement($db, $agr_id ,1);
        try{
            $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET created = 1 where id = ".$id_of_agreement['id']);
        }catch (PDOException $e){}
    }else if (isset($_GET['preview'])){
        $warning_message = $agree->updateSent($_GET['preview']);

        $agr_id = $_GET['preview'];
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET created = 1 where id = ".$agr_id);
        //triggerByAgreement($db, $agr_id ,1);
    }

    if(!$warning_message){
        // header('Location: ?m='.$_GET['m'].'&p='.$_GET['p']);
    }
}

if(isset($_GET['send_items_f'])){
    try{
        $warning_message = $agree->updateSent($id_of_agreement['id']);

        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET created = 1, forced = null, sent = 1 where id = ".$_GET['id']);
    }catch (PDOException $e){
        var_dump($e);
    }

    if(!$warning_message){
        header('Location: ?m=performance_management&p=mbo');
    }
}




/***************************************************** KOMPETENCIJE ***************************************************/
if(isset($_POST['kompetencija_naziv']) and isset($_POST['kompetencija_opis'])){
    $naziv = $_POST['kompetencija_naziv'];
    $opis  = $_POST['kompetencija_opis'];

    try{
        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] 
            (
                naziv,
                opis
            )
            VALUES (
                '$naziv',
                '$opis'
            )
            ");

        $naziv = 0; $opis = null;
        header("Refresh:0");
    }catch (PDOException $e){return $e->getMessage();}
}
if(isset($_POST['kompetencija_naziv_update']) and isset($_POST['kompetencija_opis_update'])){
    $naziv = $_POST['kompetencija_naziv_update'];
    $opis  = $_POST['kompetencija_opis_update'];
    $id    = $_POST['id'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] SET 
                naziv     = '$naziv',
                opis      = '$opis'
             where id = ".$id);
    }catch (PDOException $e){return die($e);}
}
if(isset($_GET['kompetencije_naziv_del'])){
    $id_of = $_GET['kompetencije_naziv_del'];
    $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] where id=".$id_of);
}
// Check kompetentions
if(isset($_POST['check_attr_id'])){
    $id = $_POST['check_attr_id'];
    $value = $_POST['value'];
    if($value == 'true') $value = 1;
    else $value = null;

    $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] SET checked = '$value' where id = ".$id);
}


// Kalendar
if(isset($_POST['kalendar_faza'])){
    $faza = $_POST['kalendar_faza'];
    $od   = $_POST['kalendar_datum_od'];
    $do   = $_POST['kalendar_datum_do'];
    $message = str_replace("'", "&apos;", $_POST['email_message']);


    $od_arr = explode( '.', $od);
    $od   = $od_arr[2].'-'.$od_arr['1'].'-'.$od_arr[0];
    $od_arr = explode( '.', $do);
    $do   = $od_arr[2].'-'.$od_arr['1'].'-'.$od_arr[0];

    if($message != ''){
        $s_users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]")->fetchAll();
        foreach($s_users as $usr){
            $just_user = $db->query("SELECT fname, email_company, lname, user_id FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$usr['user_id'])->fetch();;

            sendEmaill($_user['email_company'], $just_user['email_company'], $message);
        }

        /*
        $all_selected = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]")->fetchAll();

        $rms = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta]")->fetchAll();

        $counter = 0; $query = '';
        foreach($rms as $rm){
            $naziv = $rm['naziv'];
            if($counter == 0){
                $query = "WHERE position LIKE '$naziv' ";
            }else{
                $query .= "OR position LIKE '$naziv' ";
            }
            $counter++;
        }
        $users = "SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] ".$query;
        $all_selected = $db->query($users)->fetchAll();


        foreach($all_selected as $selected){
            $just_user = $db->query("SELECT email_company FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$selected['user_id'])->fetch();

            // sendEmaill($_user['email_company'], $just_user['email_company'], $message);
        } */
    }


    try{
        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_kalendar] 
            (
                faza,
                datum_od,
                datum_do
            )
            VALUES (
                '$faza',
                '$od',
                '$do'       
            )
            ");
        header("Refresh:0");
    }catch (PDOException $e){print_r($e); die();}
}

if(isset($_POST['kalendar_faza_update'])){
    $faza = $_POST['kalendar_faza_update'];
    $od   = $_POST['kalendar_datum_od_update'];
    $do   = $_POST['kalendar_datum_do_update'];
    $id   = $_POST['id'];

    $od_arr = explode( '.', $od);
    $od   = $od_arr[2].'-'.$od_arr['1'].'-'.$od_arr[0];
    $od_arr = explode( '.', $do);
    $do   = $od_arr[2].'-'.$od_arr['1'].'-'.$od_arr[0];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_kalendar] SET faza = '$faza', datum_od = '$od', datum_do = '$do'  where id = ".$id);
    }catch (PDOException $e){print_r($e); die();}
}

if(isset($_GET['kalendar_delete'])){
    $id = $_GET['kalendar_delete'];

    $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where id=".$id);
}


// LIsta korisnika
if(isset($_POST['lista_ime_prezime'])){
    $id = $_POST['lista_ime_prezime-id'];

    try{
        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] (user_id) VALUES ('$id')");
        header("Refresh:0");
    }catch (PDOException $e){print_r($e); die();}
}
if(isset($_GET['lista_usera_delete'])){
    $id = $_GET['lista_usera_delete'];

    $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where id=".$id);
}




/************************************************ APROVE SHITS ********************************************************/

if(isset($_GET['aproove_supervisor'])){
    $id = $_GET['preview'];
    $user_id = $_user['user_id'];
    $date = date('Y-m-d');

    $phase_1 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 1 ")->fetchAll());
    $phase_2 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());
    $phase_3 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 3 ")->fetchAll());
    if($phase_1 or $phase_3){
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET accepted_from_supervisor = 1, f1_id = '{$user_id}', f1_sup = '{$date}' where id = ".$id);
    }else if($phase_2){
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET accepted_from_supervisor = 1, f2_id = '{$user_id}', f2_sup = '{$date}' where id = ".$id);
    }

    //TODO - Dodati obavijest putem email-a za korisnika
    triggerByAgreement($db, $id, 2);
}
if(isset($_GET['aproove_from_user'])){
    $id = $_GET['preview'];
    $date = date('Y-m-d');

    $phase_1 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 1 ")->fetchAll());
    $phase_2 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());
    $phase_3 = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 3 ")->fetchAll());

    if($phase_1 or $phase_3){
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET accepted_from_employee = 1, f1_user = '{$date}' where id = ".$id);
    }else if($phase_2){
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET accepted_from_employee = 1, f2_user = '{$date}' where id = ".$id);
    }

    //TODO - Obavijesti putem emaila za supervizora / impersonatora
    triggerByAgreement($db, $id, 3);
}



/************************************************  COMMENTS ***********************************************************/
if(isset($_POST['new_comment'])){
    $comment = str_replace("'", "&apos;", $_POST['new_comment']);
    $user_id = $_user['user_id'];
    $spor    = $_GET['preview'];
    $date    = date('Y-m-d H:m:s');


    $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_komentari] (user_id, sporazum_id, komentar, created_at) VALUES ('$user_id', '$spor', '$comment', '$date')");
}


// Razvojni plan
if(isset($_POST['development_plan'])){
    $plan = str_replace("'", "&apos;", $_POST['development_plan']);
    $id   = $_GET['preview'];

    $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET development_plan = '{$plan}' where id = ".$id);

    header('Location: ?m='.$_GET['m'].'&p='.$_GET['p']);
}
if(isset($_POST['dev_plan'])){
    $plan = str_replace("'", "&apos;", $_POST['dev_plan']);
    $id   = $id_of_agreement['id'];
    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET development_plan = '{$plan}' where id = ".$id);
        $id_of_agreement = $agree->check_for_active_agreement($_user['user_id']); // Ako nema sporazuma, kreirajmo jedan za tekuću godinu !
    }catch (PDOException $e){}

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        header('Location: ?m='.$_GET['m'].'&p='.$_GET['p'].'&id='.$id);
    }else{
        header('Location: ?m='.$_GET['m'].'&p='.$_GET['p']);
    }
}
if(isset($_POST['recomended_grade'])){
    $recom = $_POST['recomended_grade'];
    $sup_c = $_POST['supervisor_comm'];
    $id   = $_GET['preview'];

    $date = date('Y-m-d');
    $id_usr = $_user['user_id'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET recomended_grade = '{$recom}', supervisor_comm = '{$sup_c}', f3_sup = '{$date}', f3_id = '{$id_usr}' where id = ".$id);
    }catch (PDOException $e){var_dump($e);}
}

if(isset($_GET['unlock'])){
    $id = $_GET['preview'];

        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET unlocked = 1, sent = null, accepted_from_supervisor = null, accepted_from_employee = null, status = null where id = ".$id);
}


// Kopiranje ciljevaa : )))
if(isset($_GET['c_copy_it'])){
    $id = $_GET['c_copy_it'];

    $id_sporazuma = $id_of_agreement['id'];

    $goal = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE id = ".$id)->fetch();

    try {
        $cat = $goal['kategorija'];
        $naz = $goal['naziv_cilja'];
        $opi = $goal['opis_cilja'];
        $tez = $goal['tezina'];

        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] 
            (
                id_sporazuma,
                kategorija,
                naziv_cilja,
                opis_cilja,
                tezina
            )
            VALUES (
                '$id_sporazuma',
                '$cat',
                '$naz',
                '$opi',
                '$tez'
            )
            ");

         header('Location: '.$_SESSION['escaped_url']);
    }catch (PDOException $e){}
}

if(isset($_GET['c_copy_itt'])){
    $id = $_GET['c_copy_itt'];

    $id_sporazuma = $id_of_agreement['id'];

    $goal = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE id = ".$id)->fetch();

    try {
        $cat = $goal['kategorija'];
        $naz = $goal['naziv_cilja'];
        $opi = $goal['opis_cilja'];
        $tez = $goal['tezina'];

        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] 
            (
                id_sporazuma,
                kategorija,
                naziv_cilja,
                opis_cilja,
                tezina
            )
            VALUES (
                '$id_sporazuma',
                '$cat',
                '$naz',
                '$opi',
                '$tez'
            )
            ");

         header('Location: ?m=performance_management&p=mbo_new_force&id='.$_GET['id']);
    }catch (PDOException $e){}
}