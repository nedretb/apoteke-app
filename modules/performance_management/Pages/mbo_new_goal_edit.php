<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_ciljevi.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);
$id_of_agreement = $agree->check_for_active_agreement($_user['user_id']); // Ako nema sporazuma, kreirajmo jedan za tekuću godinu !

if(isset($_POST['kategorija'])){ require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; } // Editovanje cilja
$goal = $agree->getGoal($_GET['trigger_act_edit']);

try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();
}catch (exception $e) {
    var_dump($e);
}

?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Uređivanje cilja
            </h3>

            <?php
            if(isset($warning_message)){
                ?>
                <div class="warning-custom-mess">
                    UUUUPS!! Neka od polja su prazna. Molimo popunite ih prije spremanja!
                </div>
                <?php
            }
            ?>

            <!-- Ako nema novog sporazuma, dajte korisniku opciju da ga kreira -->
            <!-- Ako ima jedan otvoren sporazum, onda dajte korisniku opciju da ga uredi ! -->

            <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                <input type="hidden" name="id" value="<?php echo $_GET['trigger_act_edit']; ?>">
                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Kategorija cilja
                        </div>
                        <select name="kategorija">
                            <option value="0">Odaberite kategoriju</option>
                            <?php
                            foreach($instance as $inst){ ?>
                                <option value="<?php echo $inst['id']; ?>" <?php if($inst['id'] == $goal['kategorija'])  echo "selected"; ?>><?php echo $inst['naziv_instance']; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="inside-col">
                        <div class="label-for">
                            Naziv cilja
                        </div>
                        <input type="text" name="naziv_cilja" placeholder="" value="<?php echo $goal['naziv_cilja']; ?>">
                    </div>
                </div>

                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Opis cilja
                        </div>
                        <textarea name="opis_cilja" placeholder="" style="height: 120px;"><?php echo $goal['opis_cilja']; ?></textarea>
                    </div>
                </div>

                <div class="just-a-row">
                    <div class="inside-col">
                        <div class="label-for">
                            Težina
                        </div>
                        <input type="number" name="tezina" placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" value="<?php echo $goal['tezina']; ?>">
                    </div>
                </div>

                <div class="just-a-row">
                    <div class="save-button">
                        <input type="submit" value="SPREMITE PODATKE">
                    </div>
                </div>
            </form>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>
            <div class="right-menu-link">
                <a href="<?php echo $_SESSION['escaped_url']; ?>">Nazad</a>
            </div>

            <!-- Privilegija koja je ostavljena samo za HR-Admina i rukovodioce -->
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>