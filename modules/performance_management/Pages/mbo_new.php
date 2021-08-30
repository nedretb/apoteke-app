<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_ciljevi.php';

require_once 'CORE/classes/agreement.php';
require_once 'CORE/classes/user.php';


$agree = new Agreement($db);
$id_of_agreement = $agree->check_for_active_agreement($_user['user_id']); // Ako nema sporazuma, kreirajmo jedan za tekuću godinu !

if(isset($_POST['kategorija']) or isset($_GET['c_copy_it'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; } // Dodavanje novog cilja
if(isset($_GET['trigger_act_del'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; }
if(isset($_GET['send_items']) or isset($_POST['check_attr_id']) or isset($_POST['dev_plan'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; }
$_SESSION['escaped_url']  =  "{$_SERVER['REQUEST_URI']}";

$goals = $agree->allGoals($id_of_agreement['id']);

try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();
    $id_sporazuma = $id_of_agreement['id'];

    // Pregled svih kompetencija
    $kompetencije = $db->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].*,
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[naziv],
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[opis]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] 
                        INNER JOIN [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] ON [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].[kompetencija_id] = [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[id]
                        where sporazum_id = ".$id_sporazuma)->fetchAll();



    $useeer_1 = new User($db);
    $ids = $useeer_1->getParentOrImpersonator($_user['user_id']);

    // $predefined_goals = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE id_usera IN (".$ids.")")->fetchAll();
    $position = $_user['position'];
    $predefined_goals = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE radno_mjesto LIKE '$position'")->fetchAll();
}catch (exception $e) {
//    var_dump($e);
}
?>
<form id="admin-form" method="post">
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

            <!-- Ako nema novog sporazuma, dajte korisniku opciju da ga kreira -->
            <!-- Ako ima jedan otvoren sporazum, onda dajte korisniku opciju da ga uredi ! -->

            <?php
            if(!$id_of_agreement['sent']){
                ?>
                <h3>
                    Dodajte / uredite sporazum
                </h3>


                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Kategorija cilja</th>
                        <th>Naziv cilja</th>
                        <th>Opis cilja</th>
                        <th>Težina cilja</th>
                        <th class="last-one">AKCIJE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 1;
                    foreach($predefined_goals as $goal){
                        ?>
                        <tr>
                            <td><?= $counter++; ?></td>
                            <td>
                                <?php
                                foreach($instance as $inst) {
                                    if ($goal['kategorija'] == $inst['id']) {
                                        echo $inst['naziv_instance'];
                                    }
                                }
                                ?>
                            </td>
                            <td><?= $goal['naziv_cilja']; ?></td>
                            <td><?= $goal['opis_cilja']; ?></td>
                            <td><?= $goal['tezina']; ?></td>
                            <td class="last-one" title="" style=" width: 120px;">
                                <a href="?m=performance_management&p=mbo_new&c_copy_it=<?php echo $goal['id']; ?>">
                                    <div class="my-button" style="padding-left:10px; padding-right:10px;">DODAJ</div>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                    <input type="hidden" name="id_sporazuma" value="<?php echo $id_of_agreement['id']; ?>">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kategorija cilja
                            </div>
                            <select name="kategorija">
                                <option value="0">Odaberite kategoriju</option>
                                <?php
                                foreach($instance as $inst){ ?>
                                    <option value="<?php echo $inst['id']; ?>" <?php if(isset($kategorija)){if($inst['id'] == $kategorija)  echo "selected";} ?>><?php echo $inst['naziv_instance']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Naziv cilja
                            </div>
                            <input type="text" name="naziv_cilja" placeholder="Naziv cilja" value="<?php echo isset($naziv_cilja) ? $naziv_cilja : ''; ?>">
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Opis cilja
                            </div>
                            <textarea name="opis_cilja" placeholder="" style="height: 120px;"><?php echo isset($opis_cilja) ? $opis_cilja : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Težina
                            </div>
                            <input type="number" name="tezina" placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" value="<?php echo isset($tezina) ? $tezina : ''; ?>">
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE PODATKE">
                        </div>
                    </div>

                </form>
                <?php
            }
             ?>

            <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->
            <h3>
                Pregled svih ciljeva
            </h3>

            <?php
            foreach($goals as $goal){
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
                            <input type="number" name="" placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" readonly value="<?php echo $goal['tezina'] ?>">
                        </div>
                    </div>

                    <div class="edit-or-delete">
                        <div class="action-button action-button-blue" title="Uredite ovaj cilj !">
                            <a href="?m=performance_management&p=mbo_new_goal_edit&trigger_act_edit=<?php echo $goal['id']; ?>">Uredite <i class="ion-edit"></i></a>
                        </div>
                        <div class="action-button" title="Obrišite ovaj cilj !">
                            <a href="?m=performance_management&p=mbo_new&trigger_act_del=<?php echo $goal['id']; ?>">Obrišite <i class="ion-ios-trash"></i></a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <h3>Razvojni plan!</h3>
            <form method="post">
                <div class="single-element">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Razvojni plan
                            </div>
                            <textarea name="dev_plan" <?php if($id_of_agreement['status']){echo 'readonly'; } ?>><?php echo $id_of_agreement['development_plan']; ?></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE PODATKE">
                        </div>
                    </div>
                </div>
            </form>

            <!-- Pregled svih defaultno ponuđenih kompetencija i onih koji su naknadno izabrane -->
            <h3>Pregled kompetencija</h3>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Naziv kompetencije</th>
                    <th>Opis kompetencije</th>
                    <th class="last-one" style="width: 100px;">STATUS</th>
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
                            <input type="checkbox" class="check-class" selectedId="<?php echo $kompetencija['id']; ?>" <?php echo $kompetencija['checked'] ? 'checked' : ''; ?>>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <!-- Pošaljite prijedlog sporazuma -->
            <?php
            if(!$id_of_agreement['sent']){
                ?>
                <div class="classic-button-wrapper">
                    <a href="?m=performance_management&p=mbo_new&send_items=true">
                        <div class="just-a-button">
                            POŠALJITE
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=mbo">Pregled sporazuma</a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>
