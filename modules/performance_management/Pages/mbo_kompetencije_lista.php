<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_kompetencije_rel.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);

if(isset($_POST['kompetencija_naziv']) or isset($_POST['kompetencija_naziv_update']) or isset($_GET['kompetencije_naziv_del'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; } // Dodavanje novog cilja


//$goals = $agree->allGoals($id_of_agreement['id']);
//
try{
    $kompetencije = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista]")->fetchAll();
}catch (exception $e) {
    var_dump($e);
}

?>

<form id="admin-form" method="post">
    <div class="split-on-right">
        <?php
        if(isset($_GET['trigger_act_edit'])){
            $kompetencija = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] where id = ".$_GET['trigger_act_edit'])->fetch();
            ?>
            <div class="choose-what-to-do">
                <!-- Ako nema novog sporazuma, dajte korisniku opciju da ga kreira -->
                <!-- Ako ima jedan otvoren sporazum, onda dajte korisniku opciju da ga uredi ! -->

                <h3>
                    Uredite kompetenciju
                </h3>

                <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                    <input type="hidden" value="<?php echo $kompetencija['id']; ?>" name="id">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kompetencija
                            </div>
                            <input type="text" name="kompetencija_naziv_update" placeholder="" value="<?php echo $kompetencija['naziv']; ?>" maxlength="250">
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Detaljan opis
                            </div>
                            <textarea name="kompetencija_opis_update" placeholder="" style="height: 120px;"><?php echo $kompetencija['opis']; ?></textarea>
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE PODATKE">
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }else{
            ?>
            <div class="choose-what-to-do">
                <!-- Ako nema novog sporazuma, dajte korisniku opciju da ga kreira -->
                <!-- Ako ima jedan otvoren sporazum, onda dajte korisniku opciju da ga uredi ! -->

                <h3>
                    Unos kompetencije
                </h3>

                <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kompetencija
                            </div>
                            <input type="text" name="kompetencija_naziv" placeholder="" value="" maxlength="250">
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Detaljan opis
                            </div>
                            <textarea name="kompetencija_opis" placeholder="" style="height: 120px;"></textarea>
                        </div>
                    </div>
                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="SPREMITE PODATKE">
                        </div>
                    </div>
                </form>

                <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->
                <h3>
                    Lista svih kompetencija
                </h3>

                <?php

                foreach($kompetencije as $kompetencija){
                    ?>
                    <div class="single-element">
                        <div class="just-a-row">
                            <div class="inside-col">
                                <div class="label-for">
                                    Kompetencija
                                </div>
                                <input type="text" name="" placeholder="" value="<?php echo $kompetencija['naziv']; ?>" readonly>
                            </div>
                        </div>
                        <div class="just-a-row">
                            <div class="inside-col">
                                <div class="label-for">
                                    Detaljan opis
                                </div>
                                <textarea name="" placeholder="" style="height: 120px;" readonly><?php echo $kompetencija['opis']; ?></textarea>
                            </div>
                        </div>
                        <div class="edit-or-delete">
                            <div class="action-button action-button-blue" title="Uredite kompetenciju !">
                                <a href="?m=performance_management&p=mbo_kompetencije_lista&trigger_act_edit=<?php echo $kompetencija['id']; ?>">Uredite <i class="ion-edit"></i></a>
                            </div>
                            <div class="action-button" title="Obrišite kompetenciju !">
                                <a href="?m=performance_management&p=mbo_kompetencije_lista&kompetencije_naziv_del=<?php echo $kompetencija['id']; ?>">Obrišite <i class="ion-ios-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                ?>
            </div>
            <?php
        }
        ?>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=mbo_kompetencije_lista">Lista kompetencija</a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>


<?php include $_themeRoot.'/footer.php'; ?>