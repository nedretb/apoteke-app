<link rel="stylesheet" href="theme/css/performance-management.css">
<?php

//require_once 'modules/performance_management/table-scripts/komentari.php';

if(isset($_POST['realizacija_cilja'])){
    $realizacija = $_POST['realizacija_cilja'];
    $ocjena      = $_POST['ocjena'];
    $id          = $_POST['id'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_ciljevi] SET 
                realizacija_cilja  = '$realizacija',
                ocjena             = '$ocjena'
             where id = ".$id);
    }catch (PDOException $e){}
}

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);
$goal = $agree->getGoal($_GET['trigger_act_edit']);
try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();
    $ocjene   = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene]")->fetchAll();
}catch (exception $e) {
    var_dump($e);
}
?>

<div class="split-on-right">
    <div class="choose-what-to-do">
        <!-- Ispis svih ciljeva koji su postavljeni od strane korisnika :: Zadani sami sebi -->
        <h3>
            Faza evaluacije :: Ocijenite cilj
        </h3>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $_GET['trigger_act_edit']; ?>">
            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Kategorija cilja
                    </div>
                    <select name="kategorija" disabled>
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
                    <input type="text" name="naziv_cilja" placeholder="" value="<?php echo $goal['naziv_cilja']; ?>" readonly>
                </div>
            </div>

            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Opis cilja
                    </div>
                    <textarea name="opis_cilja" placeholder="" style="height: 120px;" readonly><?php echo $goal['opis_cilja']; ?></textarea>
                </div>
            </div>

            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Težina
                    </div>
                    <input type="number" name="tezina" readonly placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" value="<?php echo $goal['tezina']; ?>">
                </div>
            </div>

            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Realizacija cilja
                    </div>
                    <textarea name="realizacija_cilja" placeholder="" style="height: 120px;"><?php echo $goal['realizacija_cilja']; ?></textarea>
                </div>
            </div>
            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Ocjena
                    </div>
                    <select name="ocjena">
                        <option value="0">Odaberite ocjenu</option>
                        <?php
                        foreach($ocjene as $ocjena){ ?>
                            <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == $goal['ocjena'])  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
                        <?php }
                        ?>
                    </select>
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
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>
