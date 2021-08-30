<?php
_pagePermission(4, false);


//insertovanje opcije
if(isset($_POST['value']) and isset($_POST['name'])){
    try{
        $value = $_POST['value'];
        $name  = $_POST['name'];

        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_ocjene] 
            (
                value,
                name
            )
            VALUES (
                '$value',
                '$name'
            )
            ");
    }catch (exception $e) {
        var_dump($e);
    }
}
//brisanje opcije
if(isset($_GET['value'])){
    try{
        $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene] where id=".$_GET['value']);
    }catch (exception $e) {
        var_dump($e);
    }
}

try{
    $ocjene = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene] ")->fetchAll();
}catch (PDOException $e){}

?>

<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_ciljevi.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);
$we = $agree->check_for_active_agreement($_user['user_id']); // Ako nema sporazuma, kreirajmo jedan za tekuću godinu !
//require_once 'modules/performance_management/table-scripts/pm_ocjene.php';

?>

<div class="split-on-right">
    <div class="choose-what-to-do">
        <h3>
            Pregled šifarnika
        </h3>

        <form id="admin-form" method="post">
            <div class="insert_new">
                <input type="number" name="value" placeholder="Vrijednost" class="keyword_value" style="width: 160px;">
                <input type="text" name="name" placeholder="Tekstualna vrijednost" class="keyword_value">
                <input type="submit" class="save_itt" value="SPREMITE" style="padding-top:0px;">
            </div>
        </form>

        <table>
            <thead>
            <th>#</th>
            <th style="width:120px;">Vrijednost</th>
            <th>Naziv šifrarnika</th>
            <th style='text-align:center; padding:0px; width: 120px;'>Akcije</th>
            </thead>
            <tbody>
            <?php $counter = 1;
            foreach($ocjene as $ocjena){
                ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo $ocjena['value']; ?></td>
                        <td><?php echo $ocjena['name']; ?></td>
                        <td style="text-align: center; padding:0px;">
                            <a class='table-btn' onclick="window.location.href ='?m=performance_management&p=ocjene&value=<?php echo $ocjena['id'];?>'"><i class='ion-android-close'></i></a>
                        </td>
                    </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="right-menu">
        <!-- Privilegija koja je ostavljena samo za HR-Admina i rukovodioce -->
        <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
    </div>
</div>


<?php include $_themeRoot.'/footer.php'; ?>


