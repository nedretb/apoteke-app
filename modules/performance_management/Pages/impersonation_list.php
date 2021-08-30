<link rel="stylesheet" href="theme/css/performance-management.css">
<?php

// Selected values
isset($_POST['standard_user']) ? $standard_user = $_POST['standard_user'] : $standard_user = '';
isset($_POST['impersonator'])  ? $impersonator = $_POST['impersonator']   : $impersonator = '';

if(isset($_GET['delete_it'])){
    $id = $_GET['delete_it'];
    $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where user_id = ".$id);
}

/*******************************************************************************************************************
 *  Here depends on role, we can have two options:
 * *****************************************************************************************************************
 *      1. HR Admin - can choose user and it's impersonator
 *      2. Manager - Can only pick it's impersonator
 * ****************************************************************************************************************/

try{
    $impersonators = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija]")->fetchAll();

    $impersonators = $db->query("SELECT 
                        [c0_intranet2_apoteke].[dbo].[pm_impersonacija].*,
                        [c0_intranet2_apoteke].[dbo].[users].[fname],
                        [c0_intranet2_apoteke].[dbo].[users].[lname]
                        
                        FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] 
                        INNER JOIN [c0_intranet2_apoteke].[dbo].[users] ON [c0_intranet2_apoteke].[dbo].[pm_impersonacija].[impersonator_id] = [c0_intranet2_apoteke].[dbo].[users].[user_id]
                        ")->fetchAll();
    $unique_ids = array();

}catch (PDOException $e){}

$hr_admin = false;
?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Ovlaštenja za impersonaciju
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ime i prezime</th>
                        <th>Ovlaštenja za</th>
                    </tr>
                </thead>
                <tbody>
                <?php $counter = 1;
                foreach($impersonators as $impersonator){
                    if(!in_array($impersonator['impersonator_id'], $unique_ids)){
                        array_push($unique_ids, $impersonator['impersonator_id']);
                        $imp_id = $impersonator['impersonator_id'];
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?>.</td>
                            <td><?php echo $impersonator['fname'].' '.$impersonator['lname']; ?></td>
                            <?php
                            $userss = $db->query("SELECT 
                                [c0_intranet2_apoteke].[dbo].[pm_impersonacija].*,
                                [c0_intranet2_apoteke].[dbo].[users].[fname],
                                [c0_intranet2_apoteke].[dbo].[users].[lname],
                                [c0_intranet2_apoteke].[dbo].[users].[user_id] as u_id
                                
                                FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] 
                                INNER JOIN [c0_intranet2_apoteke].[dbo].[users] ON [c0_intranet2_apoteke].[dbo].[pm_impersonacija].[user_id] = [c0_intranet2_apoteke].[dbo].[users].[user_id]
                                where [c0_intranet2_apoteke].[dbo].[pm_impersonacija].[impersonator_id] =                            
                                ".$imp_id)->fetchAll();


                            ?>
                            <td>
                                <ul>
                                    <?php
                                    foreach($userss as $user){

                                        ?>
                                        <a>
                                            <li>
                                            <?php echo $user['fname'].' '.$user['lname']; ?>
                                            <?php
                                            if($pm_admin){
                                                ?>
                                                <a href="?m=performance_management&p=impersonation_list&delete_it=<?php echo $user['user_id'] ?>">(Obrišite)</a>  
                                                <?php
                                            }
                                            ?>  
                                            </li>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                        <?php
                    }
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
                <a href="?m=performance_management&p=impersonation">Administracija - impersonacija</a>
            </div>
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>