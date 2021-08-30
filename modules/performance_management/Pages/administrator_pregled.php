<link rel="stylesheet" href="theme/css/performance-management.css">
<?php require_once 'CORE/classes/user.php'; ?>
<?php require_once 'CORE/classes/pm.php'; ?>

<?php

if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    try{
        $admins = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[pm_administratori]")->fetch()[0];
        
        if($admins > 1){
            $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_administratori] where id = ".$id);
        }
    }catch (\PDOException $e){}
}

$admins = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_administratori] as a join [c0_intranet2_apoteke].[dbo].[users] as u on a.user_id = u.user_id")->fetchAll();

?>
<form id="admin-form" method="post">
    <div class="split-on-right">

        <div class="choose-what-to-do">
            <table>
                <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Ime i prezime</th>
                    <th style="width: 120px; text-align: center;">Akcije</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($admins as $admin){
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $admin['fname'].' '.$admin['lname'] ?></td>
                        <td style="text-align: center;">
                            <a href="?m=performance_management&p=administrator_pregled&delete_id=<?= $admin['id'] ?>">Obrišite</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=administrator_pregled">Pregled svih administratora</a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>