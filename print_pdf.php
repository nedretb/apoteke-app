<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'configuration.php';

require_once 'CORE/classes/agreement.php';
require_once 'CORE/classes/user.php';
$agree = new Agreement($db); $usr = new User($db);
$goals = $agree->allGoals($_GET['id']); // get goals from ID
$agreement = $agree->getAgreementById($_GET['id']);
try{
    $user = $db->query("SELECT * from  ".$portal_users."  where user_id = ".$agreement['user_id'])->fetch();
    $parent = $db->query("SELECT * from  ".$portal_users."  where employee_no = ".$user['parent'])->fetch();

    $instance = $db->query("SELECT * FROM  ".$portal_sifrarnici."  where active = 1 and name = 'pm_kategorija'")->fetchAll();
    $ocjene   = $db->query("SELECT * FROM  ".$portal_pm_ocjene." ")->fetchAll();


    $kompetencije = $db->query("SELECT 
                         ".$portal_pm_sporazumi_kompetencije_rel." .*,
                         ".$portal_pm_kompetencija_lista." .[naziv],
                         ".$portal_pm_kompetencija_lista." .[opis]
                        
                        FROM  ".$portal_pm_sporazumi_kompetencije_rel."  
                        INNER JOIN  ".$portal_pm_kompetencija_lista."  ON  ".$portal_pm_sporazumi_kompetencije_rel." .[kompetencija_id] =  ".$portal_pm_kompetencija_lista." .[id]
                        where sporazum_id = ".$_GET['id'])->fetchAll();

    $ukupnaOcjena  = $db->query("SELECT * FROM  ".$portal_pm_ocjene."  where value = ".round($agreement['final_grade']))->fetch();
    $peporucenaOcj = $db->query("SELECT * FROM  ".$portal_pm_ocjene."  where value = ".($agreement['recomended_grade']))->fetch();


    $f1_user =  $db->query("SELECT * from  ".$portal_users."  where user_id = ".$agreement['f1_id'])->fetch();
    $f2_user =  $db->query("SELECT * from  ".$portal_users."  where user_id = ".$agreement['f2_id'])->fetch();
    $f3_user =  $db->query("SELECT * from  ".$portal_users."  where user_id = ".$agreement['f3_id'])->fetch();

    $komentari = $db->query("SELECT 
                         ".$portal_pm_komentari." .*,
                         ".$portal_users." .[fname],
                         ".$portal_users." .[lname]
                        
                        FROM  ".$portal_pm_komentari."  
                        INNER JOIN  ".$portal_users."  ON  ".$portal_pm_komentari." .[user_id] =  ".$portal_users." .[user_id]
                        where sporazum_id = ".$_GET['id']." ORDER BY id")->fetchAll();
}catch (PDOException $e){}



?>

<html>
    <head>
        <link rel="stylesheet" href="theme/css/performance-management.css">
    </head>
    <body>
        <div class="pdf-preview-wrapper">
            <h3>- Povjerljivo -</h3>
            <h2>Sporazum o radnom učinku</h2>
            <h2>Za <?php echo $agreement['year']; ?> godinu !</h2>

            <!-- headers -->
            <div class="header-one">
                <div class="split-two">
                    <div class="header-one-header">
                        <p>Ime i prezime</p>
                    </div>
                    <h5><?php echo $user['fname'].' '.$user['lname']; ?></h5>
                </div>
                <div class="split-two">
                    <div class="header-one-header">
                        <p>Naziv organizacije</p>
                    </div>
                    <h5>Raiffeisen Banka, Sarajevo</h5>
                </div>
            </div>
            <div class="header-one">
                <div class="split-two">
                    <div class="header-one-header">
                        <p>Pozicija</p>
                    </div>
                    <h5><?php echo $user['position']; ?></h5>
                </div>
                <div class="split-two">
                    <div class="header-one-header">
                        <p>Ime i prezime nadležnog rukovodioca</p>
                    </div>
                    <h5><?php echo $parent['fname'].' '.$parent['lname']; ?></h5>
                </div>
            </div>

            <!-- Ciljevi -->
            <br><br><br>
            <h2>A. CILJEVI</h2>
            <br><br>

            <div class="goal-table">
                <div class="table-part">
                    <p>CILJEVI</p>
                </div>
                <div class="table-part table-last-part">
                    <div class="rotated">
                        <p>Težina u %</p>
                    </div>
                    <div class="not-rotated">
                        <div class="evaluacija">
                            <p>EVALUACIJA</p>
                        </div>
                        <div class="split-again">
                            <div class="single-part">
                                <p>Realizacija</p>
                            </div>
                            <div class="single-part">
                                <p>Nivo realizacije</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $counter = 1;
            foreach($instance as $instanca){
                $ukupno = 0;
                foreach($goals as $goal){
                    if($goal['kategorija'] == $instanca['id'] and !$goal['disabled']){
                        $ukupno += $goal['tezina'];
                    }
                }

                ?>
                <br><br>
                <div class="goal-table goal-second-table bold-table">
                    <div class="table-part">
                        <p><?php echo $counter++; ?>. <?php echo $instanca['naziv_instance'];?> </p>
                    </div>
                    <div class="table-part table-last-part">
                        <div class="rotated rotated-center">
                            <h5><?php echo $ukupno; ?> %</h5>
                        </div>
                        <div class="not-rotated">
                            <div class="split-again">
                                <div class="single-part">
                                    <p></p>
                                </div>
                                <div class="single-part">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                foreach($goals as $goal){
                    if($goal['kategorija'] == $instanca['id'] and !$goal['disabled']){
                        $jedna_ocjena = $db->query("SELECT * FROM  ".$portal_pm_ocjene."  where value = ".$goal['ocjena'])->fetch();
                        ?>
                        <div class="goal-table goal-second-table">
                            <div class="table-part">
                                <p><?php echo $goal['naziv_cilja']; ?></p>
                            </div>
                            <div class="table-part table-last-part">
                                <div class="rotated rotated-center">
                                    <h5><?php echo ($goal['tezina']) ?>%</h5>
                                </div>
                                <div class="not-rotated">
                                    <div class="split-again">
                                        <div class="single-part">
                                            <p><?php echo $goal['realizacija_cilja']; ?></p>
                                        </div>
                                        <div class="single-part">
                                            <p><?php echo $jedna_ocjena['name']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>

            <div style="page-break-before:always"></div>
            <div class="total-grade">
                <p style="margin-right:50px;">Ukupna ocjena ciljeva: </p>
                <?php
                foreach($ocjene as $ocjena){
                    ?>
                    <span>
                        <p><?php echo $ocjena['name']; ?></p>
                        <input type="checkbox" <?php if(round($agreement['goal_grade']) == $ocjena['value']) echo 'checked'; ?>>
                    </span>
                    <?php
                }
                ?>
            </div>
            <div class="grade-table">
                <div class="row">
                    <b><p>Ocjene za nivo realizacije ciljeva</p></b>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>AAA</p>
                    </div>
                    <div class="col">
                        <p>Realizacija 120% ili više - Konzistentno premašuje očekivanja</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>AA</p>
                    </div>
                    <div class="col">
                        <p>Realizacija 110% - 119%- Premašuje očekivanja</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>A</p>
                    </div>
                    <div class="col">
                        <p>Realizacija 90% - 109% - U potpunosti ispunjava očekivanja</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>B</p>
                    </div>
                    <div class="col">
                        <p>Realizacija 50% - 89% - Djelimično ispunjava očekivanja</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>C</p>
                    </div>
                    <div class="col">
                        <p>Realizacija 49% ili manje - Ispod očekivanja </p>
                    </div>
                </div>
            </div>

            <!-- Kompetencije -->
            <br><br><br>
            <div style="page-break-before:always"></div>
            <h2>B. KOMPETENCIJE</h2>
            <br><br>
            <div class="kompetencije-table">
                <div class="single-one">
                    <p>Opis kompetencije</p>
                </div>
                <div class="single-one middle-one">
                    <p>Nivo realizacije</p>
                </div>
                <div class="single-one">
                    <p>Komentar</p>
                </div>
            </div>

            <?php
            foreach($kompetencije as $komp){
                $jedna_ocjena = null;
                try{
                    $jedna_ocjena = $db->query("SELECT * FROM  ".$portal_pm_ocjene."  where value = ".$komp['ocjena'])->fetch();
                }catch (PDOException $e){}

                if($komp['checked']){
                    ?>
                    <div class="kompetencije-table">
                        <div class="single-one">
                            <b><p> <?php echo $komp['naziv']; ?> </p></b>
                            <p><?php echo $komp['opis']; ?></p>
                        </div>
                        <div class="single-one middle-one">
                            <p><?php echo $jedna_ocjena['name']; ?></p>
                        </div>
                        <div class="single-one">
                            <p><?php echo $komp['komentar']; ?></p>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

            <div style="page-break-before:always"></div>
            <div class="total-grade">
                <p style="margin-right:50px;">Ukupna ocjena kompetencija: </p>
                <?php
                foreach($ocjene as $ocjena){
                    ?>
                    <span>
                        <p><?php echo $ocjena['name']; ?></p>
                        <input type="checkbox" <?php if((round($agreement['competences_grade'])) == $ocjena['value']) echo 'checked'; ?>>
                    </span>
                    <?php
                }
                ?>
            </div> <!-- Ocjene -->
            <div class="grade-table">
                <div class="row">
                    <b><p>Ocjene za nivo realizacije kompetencija</p></b>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>AAA</p>
                    </div>
                    <div class="col">
                        <p>Najbolji u ovoj oblasti; model, jedan od najboljih primjera koji ste ikad vidjeli</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>AA</p>
                    </div>
                    <div class="col">
                        <p>Veoma sposoban; bolji od većine kolega</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>A</p>
                    </div>
                    <div class="col">
                        <p>Ispunjava očekivanja prema našim najvišim standardima; kao većina kolega</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>B</p>
                    </div>
                    <div class="col">
                        <p>Ne ispunjava uvijek očekivanja prema datom standardu; slabiji od ostalih kolega</p>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col">
                        <p>C</p>
                    </div>
                    <div class="col">
                        <p>Hitna potreba za poboljšanjem; blokiran, radi suprotno od traženog ili se bahato ponaša </p>
                    </div>
                </div>
            </div> <!-- Defaultne vrijednosti ocjene -->




            <br><br><br>
            <div style="page-break-before:always"></div>
            <!-- DODATNO -->
            <div class="total-grade total-grade-without">
                <p>
                    Ukupna ocjena radnog učinka: <?php echo $ukupnaOcjena['name']; ?>
                </p>
            </div>
            <div class="total-grade total-grade-without">
                <p>
                    Preporučena ocjena (rukovodioca): <?php echo $peporucenaOcj['name']; ?>
                </p>
            </div>
            <div class="total-grade total-grade-without">
                <p>
                    Komentar na preporučenu ocjenu (rukovodilac): <?php echo $agreement['supervisor_comm']; ?>
                </p>
            </div>
            <div class="total-grade total-grade-without">
                <p>
                    Razvojni plan: <?php echo $agreement['development_plan']; ?>
                </p>
            </div>

            <?php
            foreach($komentari as $komentar){
                ?>
                <div class="total-grade total-grade-without">
                    <p>
                        <?php echo $komentar['fname'].' '.$komentar['lname']; ?> - <?php echo $komentar['komentar']; ?>
                    </p>
                </div>
                <?php
            }
            ?>

            <br><br>
            <div class="total-grade total-grade-without">
                <p>
                    VI. POTPISI
                </p>
            </div>
            <div class="total-grade total-grade-without">
                <p>
                    Prihvatanjem ovog Sporazuma u Performance Management aplikaciji: <br>

                    Zaposlenik: potvrđujem da sam učestvovao/la u procesu upravljanja radnim učinkom sa mojim pretpostavljenim; da razumijem I prihvatam njegov sadržaj:
                </p>
            </div>

            <div class="signature-table">
                <div class="cols">
                    <p>Planiranje: </p>
                    <p>Ime i prezime : </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f1_user']) ?></p>
                </div>
                <div class="cols">
                    <p>Revidiranje: </p>
                    <p>Ime i prezime : </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f2_user']) ?></p>
                </div>
                <div class="cols">
                    <p>Evaluacija: </p>
                    <p>Ime i prezime : </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f3_user']) ?></p>
                </div>
            </div>

            <br><br>
            <div class="total-grade total-grade-without">
                <p>
                    Nadređeni: potvrđujem da sam učestvovao/la u procesu upravljanja radnim učinkom sa mojim saradnikom; da razumijem njegov sadržaj i da razumijem I prihvatam njegov sadržaj
                </p>
            </div>

            <div class="signature-table">
                <div class="cols">
                    <p>Planiranje: </p>
                    <p>Ime i prezime : <?php echo $f1_user['fname'].' '.$f1_user['lname']; ?> <?php if($user['parent'] != $f1_user['employee_no']){echo "- Impersonator";}; ?> </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f1_sup']) ?></p>
                </div>
                <div class="cols">
                    <p>Revidiranje: </p>
                    <p>Ime i prezime : <?php echo $f2_user['fname'].' '.$f2_user['lname']; ?> <?php if($user['parent'] != $f2_user['employee_no']){echo "- Impersonator";}; ?> </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f2_sup']) ?></p>
                </div>
                <div class="cols">
                    <p>Evaluacija: </p>
                    <p>Ime i prezime : <?php echo $f3_user['fname'].' '.$f3_user['lname']; ?> <?php if($user['parent'] != $f3_user['employee_no']){echo "- Impersonator";}; ?> </p>
                    <p>Datum : <?php echo $agree->formatDate($agreement['f3_sup']) ?></p>
                </div>
            </div>
        </div>
    </body>
</html>
