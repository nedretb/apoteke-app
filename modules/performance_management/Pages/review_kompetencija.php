<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_kompetencije_rel.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);


if(isset($_POST['komentar']) and isset($_POST['ocjena'])){
    $komentar = $_POST['komentar'];
    $ocjena   = $_POST['ocjena'];
    $id       = $_POST['id'];

    try{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] SET 
                komentar  = '$komentar',
                ocjena    = '$ocjena'
             where id = ".$id);
    }catch (PDOException $e){}
}

//$goals = $agree->allGoals($id_of_agreement['id']);
//
try{
    $kompetencije = $db->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].*,
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[naziv],
                        [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[opis]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel] 
                        INNER JOIN [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista] ON [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].[kompetencija_id] = [c0_intranet2_apoteke].[dbo].[pm_kompetencija_lista].[id]
                        where [c0_intranet2_apoteke].[dbo].[pm_sporazumi_kompetencije_rel].[id] = ".$_GET['preview'])->fetch();

    $ocjene   = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene]")->fetchAll();
}catch (exception $e) {
    var_dump($e);
}

?>


<div class="split-on-right">
    <div class="choose-what-to-do">
        <h3>
            Ocjenjivanje kompetencije
        </h3>

        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $_GET['preview']; ?>">

            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Kompetencija
                    </div>
                    <input type="text" name="kompetencija_naziv" placeholder="" value="<?php echo $kompetencije['naziv']; ?>" maxlength="250" readonly>
                </div>
            </div>
            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Detaljan opis
                    </div>
                    <textarea name="kompetencija_opis" placeholder="" style="height: 120px;" readonly><?php echo $kompetencije['opis']; ?></textarea>
                </div>
            </div>

            <div class="just-a-row">
                <div class="inside-col">
                    <div class="label-for">
                        Komentar
                    </div>
                    <textarea name="komentar" placeholder="" style="height: 120px;" ><?php echo $kompetencije['komentar']; ?></textarea>
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
                            <option value="<?php echo $ocjena['value']; ?>" <?php if($ocjena['value'] == $kompetencije['ocjena'])  echo "selected"; ?>><?php echo $ocjena['name']; ?></option>
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

        <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
    </div>
</div>



<?php include $_themeRoot.'/footer.php'; ?>