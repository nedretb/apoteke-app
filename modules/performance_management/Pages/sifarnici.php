<?php
_pagePermission(4, false);

$sifrarnici = array(
    'pm_kategorija'=>"Kategorija"
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

            <table>
                <thead>
                    <th>#</th>
                    <th>Naziv šifrarnika</th>
                    <th style='text-align:center; padding:0px; width: 120px;'>Akcije</th>
                </thead>
                <tbody>
                <?php $counter = 1;
                foreach($sifrarnici as $key=>$value){
                    ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td>
                            <?php echo $value; ?>
                        </td>
                        <td style='padding:0px; text-align:center;'>
                            <a class='table-btn' onclick="window.location.href ='/app_raiff/?m=performance_management&p=sifrarnik&name=<?php echo $key;?>'"><i class='ion-android-list'></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>2.</td>
                    <td>
                        Ocjene
                    </td>
                    <td style='padding:0px; text-align:center;'>
                        <a class='table-btn' onclick="window.location.href ='/app_raiff/?m=performance_management&p=ocjene'"><i class='ion-android-list'></i></a>
                    </td>
                </tr>
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
