<link rel="stylesheet" href="theme/css/performance-management.css">
<?php

//require_once 'modules/performance_management/table-scripts/pm_sporazumi_kompetencije_rel.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);
$goals = $agree->allGoals($_GET['preview']); // get goals from ID
$isSupervisor = false; $edit = false; $owner = false;

// POST - GET metode
if(isset($_GET['aproove_supervisor']) or isset($_GET['aproove_from_user']) or isset($_GET['unlock']) or isset($_GET['send_items']) or isset($_GET['trigger_act_del']) or isset($_POST['new_comment']) or isset($_POST['development_plan']) or isset($_POST['recomended_grade'])) { require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; }
$_SESSION['escaped_url']  =  "{$_SERVER['REQUEST_URI']}";

try{
    $id_sporazuma = $_GET['preview'];
    $kompetencije = $db->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].*,
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[naziv],
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[opis]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] 
                        INNER JOIN [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] ON [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].[kompetencija_id] = [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[id]
                        where sporazum_id = ".$id_sporazuma)->fetchAll();
}catch (PDOException $e){}

if(isset($_GET['send_for_review'])){
    $date = date('Y-m-d');

    try{
        $goals_num = 0; $goals_total = 0;
        foreach($goals as $goal){
            if(!$goal['disabled']){
                $goals_total += $goal['ocjena'];
                $goals_num++;
            }
        }
        $goal_average = round($goals_total / $goals_num, 2);

        $komp_num = 0; $komp_grade = 0;
        foreach($kompetencije as $kompetencija){
            if($kompetencija['checked']){
                $komp_num++;
                $komp_grade += $kompetencija['ocjena'];
            }
        }

        $komp_average = round($komp_grade / $komp_num, 2);

        $average = round($goal_average * 0.8 + $komp_average * 0.2, 2);

        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET status = 1, unlocked = null, goal_grade = '{$goal_average}', competences_grade = '{$komp_average}', final_grade = '{$average}', f3_user = '{$date}' where id = ".$_GET['preview']);
    }catch (PDOException $e){}
}

try{
    // Jesam li ili nisam vlasnik
    $owner = $agree->checkUserOwner($_user['user_id'], $_GET['preview']);

    $date = date('Y-m-d');
    $date_is_fine = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 2 ")->fetchAll());
    // Ukoliko smo u periodu kada je moguće uređivanje, onda ako je završen ciklus, omogući ponovno editovanje sporazuma !
    if(isset($_GET['edit_agreement'])){
        if($date_is_fine and !isset($_GET['aproove_from_user'])){
            $instance_2 = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where id = ".$_GET['preview'])->fetch();
            if($instance_2['sent'] and $instance_2['accepted_from_supervisor'] and $instance_2['accepted_from_employee'] and $owner){
                $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi] SET accepted_from_employee = NULL, sent = NULL, accepted_from_supervisor = NULL where id = ".$_GET['preview']);
            }
        }
    }

    $evoluacija = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 3 ")->fetchAll());
}catch (PDOException $e){}

$agreement = $agree->getAgreementById($_GET['preview']);

try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();

    // Uslov za kad se sporazum može prihvatiti od strane supervizora je da je ta osoba supervizor ili njegov impersonator
    $isSupervisor = $agree->checkSupervisor($_user['user_id'], $_GET['preview']);


    // Pokupi sve komentare
    $komentari = $db->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_komentari].*,
                        [c0_intranet2_apoteke].[dbo].[users].[fname],
                        [c0_intranet2_apoteke].[dbo].[users].[lname]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_komentari] 
                        INNER JOIN [c0_intranet2_apoteke].[dbo].[users] ON [c0_intranet2_apoteke].[dbo].[pm_komentari].[user_id] = [c0_intranet2_apoteke].[dbo].[users].[user_id]
                        where sporazum_id = ".$id_sporazuma." ORDER BY id")->fetchAll();

    $brojKomentara = false;
    foreach($komentari as $komentar){
        if($komentar['user_id'] != $agreement['user_id']) $brojKomentara = true;
    }

    $ocjene   = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene]")->fetchAll();
}catch (exception $e) {
    var_dump($e);
}


// Uređivati može ako je supervizor i ako sporazum nije prihvaćen od strane korisnika i od samog sebe
// Ili može uređivati ako je vlasnik (korisnik) i sporazum nije prihvaćen od strane supervizora!!
if(($isSupervisor and !$agreement['accepted_from_supervisor'] and !$agreement['accepted_from_employee']  and $agreement['sent'])
    or ($owner and !$agreement['accepted_from_supervisor']) or ($evoluacija and $agreement['unlocked'] and $owner and !$agreement['accepted_from_supervisor']) or($evoluacija and $agreement['unlocked'] and $isSupervisor and !$agreement['accepted_from_employee']) or $pm_admin){
    // $edit = true;
}

// Edit update -- Uređivati u ovom slučaju se može ukoliko nije sent
if($owner and !$agreement['sent']){
    $edit = true;
}
?>

<div class="split-on-right">
    <div class="choose-what-to-do">
        <?php
        if(isset($warning_message)){
            ?>
            <div class="warning-custom-mess">
                <?php echo $warning_message; ?>
            </div>
            <?php
        }
        ?>
        <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->

        <h3><?php echo $agreement['fname'].' '.$agreement['lname'] ?></h3>

        <!-- Ako nema ni jednog, nemoj pokazivati ništa -->
        <?php
        $disableani = false;
        foreach($goals as $goal){ if($goal['disabled']){$disableani = true;} }
        ?>

        <?php
        if($disableani){
            ?>

            <h3>
                Neaktivni ciljevi
            </h3>

            <table>
                <thead>
                <tr>
                    <th style="color:red;">#</th>
                    <th style="color:red;">Kategorija cilja</th>
                    <th style="color:red;">Naziv cilja</th>
                    <th class="last-one" style="width: 100px; color:red;">Akcije</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter = 1;
                foreach($goals as $goal){
                    if($goal['disabled']){
                        ?>
                        <tr>
                            <td style="color:red;"><?php echo $counter++; ?>. </td>
                            <td style="color:red;">
                                <?php
                                foreach($instance as $inst){ ?>
                                    <?php if($goal['kategorija'] == $inst['id'])  echo $inst['naziv_instance']; ?>
                                <?php }
                                ?>
                            </td>
                            <td style="color:red;"><?php echo $goal['naziv_cilja']; ?></td>
                            <td style="text-align: center; padding: 0px; ">
                                <a style="color:red;" href="?m=performance_management&p=edited_goal&trigger_act_edit=<?php echo $goal['id']; ?>">Pregled</a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        ?>

        <h3>
            Pregledajte važeći sporazum: Ciljevi i kompetencije
        </h3>

        <?php
        foreach($goals as $goal){
            if(!$goal['disabled']){
                ?>
                <div class="single-element">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kategorija cilja
                            </div>
                            <select name="" disabled>
                                <option value="0">Odaberite kategoriju</option>
                                <?php
                                foreach($instance as $inst){ ?>
                                    <option value="<?php echo $inst['id']; ?>" <?php if($goal['kategorija'] == $inst['id'])  echo "selected"; ?>><?php echo $inst['naziv_instance']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Naziv cilja
                            </div>
                            <input type="text" name="" placeholder="" readonly value="<?php echo $goal['naziv_cilja'] ?>">
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Opis cilja
                            </div>
                            <textarea name="" placeholder="" readonly style="height: 120px;"><?php echo $goal['opis_cilja'] ?></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Težina
                            </div>
                            <input type="text" name="" placeholder="" readonly value="<?php echo $goal['tezina'] ?>">
                        </div>
                    </div>

                    <?php
                    if($evoluacija) {
                        $ocj = 'Nije uneseno';
                        foreach ($ocjene as $ocjena) {
                            if ($ocjena['value'] == $goal['ocjena']) $ocj = $ocjena['name'];
                        }

                        ?>
                        <div class="just-a-row">
                            <div class="inside-col">
                                <div class="label-for">
                                    Ocjena
                                </div>
                                <input type="text" name="" readonly value="<?php echo $ocj; ?>">
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if($evoluacija and $owner and !$goal['disabled'] and !$agreement['status']){
                        ?>
                        <div class="edit-or-delete">
                            <div class="action-button action-button-blue" title="">
                                <?php
                                if($brojKomentara){
                                    ?>
                                    <a href="?m=performance_management&p=review&trigger_act_edit=<?php echo $goal['id']; ?>">Ocijenite <i class="ion-edit"></i></a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if($edit){
                        ?>
                        <div class="edit-or-delete">
                            <?php
                            if($goal['last_one']){
                                ?>
                                <div class="name_date">
                                    <a href="?m=performance_management&p=edited_goal&trigger_act_edit=<?php echo $goal['last_one']; ?>">
                                        <p>Pregled prethodnog cilja -></p>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="action-button action-button-blue" title="">
                                <a href="?m=performance_management&p=mbo_new_goal_edit&trigger_act_edit=<?php echo $goal['id']; ?>">Izmijenite <i class="ion-edit"></i></a>
                            </div>
                            <div class="action-button" title="Obrišite ovaj cilj !">
                                <a href="<?php echo $_SESSION['escaped_url']; ?>&trigger_act_del=<?php echo $goal['id']; ?>">Obrišite <i class="ion-ios-trash"></i></a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>


        <h3>
            Pregled kompetencija
        </h3>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Naziv kompetencije</th>
                <th>Opis kompetencije</th>
                <th class="last-one" style="width: 100px;">STATUS</th>
                <?php
                if($evoluacija){
                    ?>
                    <th class="last-one" style="width: 100px;">OCJENA</th>
                    <?php
                }

                if($evoluacija and $owner and !$agreement['status']){
                    ?>
                    <th class="last-one" style="width: 100px;">OCIJENITE</th>
                    <?php
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php $counter = 1;
            foreach($kompetencije as $kompetencija){
                ?>
                <tr>
                    <td><?php echo $counter++; ?>. </td>
                    <td><?php echo $kompetencija['naziv']; ?></td>
                    <td><?php echo $kompetencija['opis']; ?></td>

                    <td class="last-one" title="Status kompetencije / Odaberite ili izbrišite">
                        <input type="checkbox" class="<?php if($edit) echo 'check-class'; ?>" selectedId="<?php echo $kompetencija['id']; ?>" <?php echo $kompetencija['checked'] ? 'checked' : ''; ?>>
                    </td>
                    <?php
                    if($evoluacija) {
                        $ocj = 'Nije uneseno';
                        foreach ($ocjene as $ocjena) {
                            if ($ocjena['value'] == $kompetencija['ocjena']) $ocj = $ocjena['name'];
                        }

                        echo '<td style="text-align:center; padding:0px;"> '.$ocj.' </td>';
                    }

                    if($evoluacija and $owner and !$agreement['status'] and $brojKomentara){
                        ?>
                        <td style="text-align: center; padding: 0px;">
                            <a href="?m=performance_management&p=review_kompetencija&preview=<?php echo $kompetencija['id']; ?>">Ocijenite</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>


        <!-- RAZVOJNI PLAN -->
        <form method="post">
            <div class="single-element">
                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Razvojni plan
                        </div>
                        <textarea name="development_plan" id="" cols="30" rows="10" <?php if($agreement['status'] or $evoluacija){echo ''; } ?>><?php echo $agreement['development_plan']; ?></textarea>
                    </div>
                </div>

                <?php
                if($owner and !$agreement['status'] and !$agreement['sent'] or ($evoluacija and $agreement['unlocked'] and $owner and !$agreement['accepted_from_supervisor'])){
                    ?>
                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE PODATKE">
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </form>

        <?php
        if($evoluacija){
            ?>
            <form method="post">
                <div class="single-element">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Preporučena ocjena (manager)
                            </div>
                            <select name="recomended_grade" <?php if(!$isSupervisor and !$agreement['status']) echo 'disabled'; ?>>
                                <option value="0">Odaberite ocjenu</option>
                                <?php
                                foreach($ocjene as $ocjena){
                                    if($ocjena['value'] > $agreement['final_grade']){
                                        ?>
                                        <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == $agreement['recomended_grade'])  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Komentar manager-a
                            </div>
                            <textarea name="supervisor_comm" id="" cols="30" rows="10" <?php if(!$isSupervisor and !$agreement['status']) echo 'readonly'; ?>><?php echo $agreement['supervisor_comm']; ?></textarea>
                        </div>
                    </div>

                    <?php
                    if($isSupervisor  and $agreement['status']){
                        ?>
                        <div class="just-a-row">
                            <div class="save-button">
                                <input type="submit" value="SPREMITE PODATKE">
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </form>
            <?php
        }
        ?>

        <!------------------------------------------------------------------------------------------------------------->
        <!-- Odobravanje sa strane supervizora ako je neko supervizor i ako nije već odobrio ! -->
        <?php
        if($isSupervisor and !$agreement['accepted_from_supervisor'] and $agreement['sent']){
            ?>
            <div class="classic-button-wrapper">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $_GET['preview']; ?>&aproove_supervisor=true">
                    <div class="just-a-button">
                        PRIHVATITE SPORAZUM
                    </div>
                </a>
            </div>
            <?php
        }

        // Slanje sporazuma od strane korisnika
        if(!$agreement['sent'] and $owner){
            ?>
            <div class="classic-button-wrapper">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $_GET['preview']; ?>&send_items=true">
                    <div class="just-a-button">
                        POŠALJITE
                    </div>
                </a>
            </div>
            <?php
        }

        // Prihvatanje sporazuma od strane korisnika
        if($owner and $agreement['accepted_from_supervisor'] and !$agreement['accepted_from_employee'] and $agreement['sent']){
            ?>
            <div class="classic-button-wrapper">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $_GET['preview']; ?>&aproove_from_user=true">
                    <div class="just-a-button">
                        PRIHVATITE SPORAZUM
                    </div>
                </a>
            </div>
            <?php
        }

        // Ponovno uređivanje sporazuma
        if($owner and $agreement['accepted_from_supervisor'] and $agreement['accepted_from_employee'] and $agreement['sent'] and $date_is_fine){
            ?>
            <div class="classic-button-wrapper">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $_GET['preview']; ?>&edit_agreement=true">
                    <div class="just-a-button">
                        Ponovno uređivanje sporazuma
                    </div>
                </a>
            </div>
            <?php
        }

        ?>

        <!-- Ako smo u trećoj fazi, dopusti da se dalje odvija šta treba ... -->
        <?php

        if($agreement['status']){ // Ako je poslano, znači da je sve izračunato
            ?>
            <div class="single-element">
                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Ukupna ocjena radnih ciljeva
                        </div>
                        <select name="" disabled>
                            <?php
                            foreach($ocjene as $ocjena){ ?>
                                <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == round($agreement['goal_grade']))  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="inside-col">
                        <div class="label-for">
                            Ukupna ocjena kompetencija
                        </div>
                        <select name="" disabled>
                            <?php
                            foreach($ocjene as $ocjena){ ?>
                                <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == round($agreement['competences_grade']))  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Ukupna ocjena učinka
                        </div>
                        <select name="" disabled>
                            <?php
                            foreach($ocjene as $ocjena){ ?>
                                <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == round($agreement['final_grade']))  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php

        // Završi slanje sporazuma korisniku :))
        if($owner and $evoluacija and !$agreement['status']){
            ?>

            <div class="classic-button-wrapper">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $_GET['preview']; ?>&send_for_review=true">
                    <div class="just-a-button">
                        Pošaljite sporazum na pregled
                    </div>
                </a>
            </div>
            <?php
        }
        ?>


        <h3>
            Komentari, povratne infromacije
        </h3>
        <br>
        <?php
        foreach($komentari as $komentar){
            ?>
            <div class="comment">
                <div class="comment-image">
                    <img src="" alt="">
                </div>
                <div class="comment-text">
                    <h4><?php echo $komentar['fname'].' '.$komentar['lname']; ?> - <?php echo $komentar['created_at']; ?></h4>
                    <p> <?php echo $komentar['komentar']; ?> </p>
                </div>
            </div>
            <?php
        }
        ?>

        <form method="post">
            <div class="comment">
                <input type="text" name="new_comment" placeholder="Vaš komentar ..">
                <input type="submit" value="SPREMITE" class="submit-it">
            </div>
        </form>

        <!------------------------------------------------------------------------------------------------------------->
    </div>

    <div class="right-menu">
        <div class="right-menu-header">
            <h4>Dodatne opcije</h4>
        </div>

        <div class="right-menu-link">
            <a href="?m=performance_management&p=mbo">Pregled sporazuma</a>
        </div>
        <?php
        if($agreement['status']){
            ?>
            <div class="right-menu-link">
                <a href="print_pdf.php?what=pm&id=<?php echo $agreement['id']; ?>" target="_blank">Print u PDF</a>
            </div>
            <?php
        }
        if($pm_admin and $evoluacija){
            ?>
            <div class="right-menu-link">
                <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $agreement['id']; ?>&unlock=true">Otključajte sporazum</a>
            </div>
            <?php
        }
        ?>

        <!-- Privilegija koja je ostavljena samo za HR-Admina i rukovodioce -->
        <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
    </div>
</div>
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>
