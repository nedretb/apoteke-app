<?php
_pagePermission(4, false);

$name = $_GET['name'];
$sifrarnici = array(
    'pm_kategorija'=>"Kategorija"
);
//insertovanje opcije
if(isset($_GET['add'])){
    try{
        $provjeraq = $db->query("SELECT count(*) as aa FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where naziv_instance='".$_GET['add']."'");
        foreach ($provjeraq as $one){
            $provjera = $one['aa'];
        }
        if ($one['aa']==0){
            $add = $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[sifrarnici] (name, ime, naziv_instance,active)
          VALUES ('$name','".$sifrarnici[$name]."','".$_GET['add']."',1)");
        }
    }catch (exception $e) {
        var_dump($e);
    }
}
//brisanje opcije
if(isset($_GET['del'])){
    try{
        $add = $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[sifrarnici] SET active=0 WHERE id =".$_GET['del']);
    }catch (exception $e) {
        var_dump($e);
    }
}
//hvatanje podataka
try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = '$name'");
}catch (exception $e) {
    var_dump($e);
}
$sifrarnici = array(
    'talenti'=>"Talenti",
    'kompetencije'=>'Kompetencije'
);

?>

<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';
//require_once 'modules/performance_management/table-scripts/pm_sporazumi_ciljevi.php';

require_once 'CORE/classes/agreement.php';
$agree = new Agreement($db);
$we = $agree->check_for_active_agreement($_user['user_id']); // Ako nema sporazuma, kreirajmo jedan za tekuću godinu !


?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Pregled šifarnika
            </h3>

            <div class="insert_new">
                <input type="text" placeholder="Unesite vrijednost šifarnika" class="keyword_value">
                <div class="save_itt">SPREMITE</div>
            </div>

            <table>
                <thead>
                <th>#</th>
                <th>Naziv šifrarnika</th>
                <th style='text-align:center; padding:0px; width: 120px;'>Akcije</th>
                </thead>
                <tbody>
                <?php $counter = 1;
                foreach($instance as $instanca){
                    ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td>
                            <?php echo $instanca['naziv_instance']; ?>
                        </td>
                        <td style='padding:0px; text-align:center;'>
                            <a class='table-btn' onclick="window.location.href ='/app_raiff/?m=performance_management&p=sifrarnik&name=<?php echo $name;?>&del=<?php echo $instanca['id'];?>'"><i class='ion-android-close'></i></a>
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
</form>

<?php include $_themeRoot.'/footer.php'; ?>


<script>

    $(".save_itt").click(function () {
        window.location = ("/app_raiff/?m=performance_management&p=sifrarnik&name=<?php echo $name;?>" + "&add=" + $(".keyword_value").val());
    });

</script>

