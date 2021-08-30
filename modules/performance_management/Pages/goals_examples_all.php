<link rel="stylesheet" href="theme/css/performance-management.css">
<?php
require_once 'CORE/classes/user.php';

try{
    if(isset($_GET['preview'])){
        $id = $_GET['preview'];

        $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] where keywor = ".$id);
    }

    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();

    if($pm_admin){
        $goals = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] ")->fetchAll();
    }else{
        $user = new User($db);
        $ids = $user->getParentOrImpersonator($_user['user_id']);

        $id = $_user['user_id'];
        // $goals = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE id_usera IN (".$ids.")")->fetchAll();
        $goals = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva] WHERE id_usera = $id or owner = $id")->fetchAll();
    }

    $uniqueGoals = array();
    foreach($goals as $goal){
        $found = false;
        foreach ($uniqueGoals as $uniqueGoal){
            if($uniqueGoal['keywor'] == $goal['keywor']) $found = true;
        }
        if(!$found) array_push($uniqueGoals, $goal);
    }

    $goals = $uniqueGoals;
}catch (PDOException $e){var_dump($e);}

?>

<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Pregled svih uzoraka ciljeva
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
                foreach($goals as $goal){
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
                            <a href="?m=performance_management&p=goals_examples&preview=<?php echo $goal['id']; ?>" title="Prikaži">
                                <div class="my-button" style="padding-left:10px; padding-right:10px;">P</div>
                            </a>
                            <?php
                            if($goal['owner'] == $_user['user_id']){
                                ?>
                                <a href="?m=performance_management&p=goals_examples_all&preview=<?php echo $goal['keywor']; ?>" title="Obriši">
                                    <div class="my-button" style="padding-left:10px; padding-right:10px;">O</div>
                                </a>
                                <?php
                            }
                            ?>
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
                <a href="?m=performance_management&p=goals_examples">Nazad <uzoraka></uzoraka></a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>