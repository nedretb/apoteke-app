<link rel="stylesheet" href="theme/css/performance-management.css">
<?php require_once 'CORE/classes/user.php'; ?>
<?php require_once 'CORE/classes/pm.php'; ?>

<?php

//require_once 'modules/performance_management/table-scripts/impersonation.php';

// Selected values
isset($_POST['standard_user']) ? $standard_user = $_POST['standard_user'] : $standard_user = '';
isset($_POST['impersonator'])  ? $impersonator = $_POST['impersonator']   : $impersonator = '';

$hr_admin = false;
if($pm_admin) $hr_admin = true;

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

$user = new User($db); $pm = new PM($db);


// Get all users - WORK WITH USERS
$allUsers   = $user->allUsers();
$allUsers_d = $user->allUsers();

// Performance MANAGEMENT
$pm = new PM($db);

if(isset($_POST['naziv-covjeka'])){
    if(isset($_POST['naziv-usera'])){
        // Ako smo ovdje, HR Admin postavlja impersonatora
        if($pm->checkForImpersonator($_POST['naziv-usera-id'])){
            // Ako vrati true, znači da već ovaj manager ima impersonatora i trebamo da updejtujemo tabelu !
            $pm->updateImpersonator($_POST['naziv-usera-id'], $_POST['naziv-covjeka-id']);
        }else{
            $pm->insertImpersonator($_POST['naziv-usera-id'], $_POST['naziv-covjeka-id']);
        }
    }else{
        if($pm->checkForImpersonator($_user['user_id'])){
            // Ako vrati true, znači da već ovaj manager ima impersonatora i trebamo da updejtujemo tabelu !
            $pm->updateImpersonator($_user['user_id'], $_POST['naziv-covjeka-id']);
        }else{
            $pm->insertImpersonator($_user['user_id'], $_POST['naziv-covjeka-id']);
        }
    }
}
$impersonator = $pm->getMyImpersonator($_user['user_id']);   // Ako vrati nešto, znači da korisnik ima impersonatora
                                                             // u protivnom vraća nulu
?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                <?php echo ($hr_admin) ? 'Unesite / zamijenite impersonatora' : 'Odaberite vašeg impersonatora'; ?>
            </h3>

            <div class="just-a-row">
                <!-- Ako je admin, onda ima opciju da izabere managera i impersonatora -->
                <?php
                if($hr_admin){
                    ?>
                    <div class="search-wrapper">
                        <input type="text" name="naziv-usera" value="" class="search-input" placeholder="Unesite ime i/ili uposlenika" autocomplete="off">
                        <?php
                        if(isset($impersonator[0])){
                            ?> <input type="hidden" name="naziv-usera-id" value=""> <?php
                        }
                        ?>
                        <div class="all-elements-wrapper">
                            <?php
                            foreach($allUsers_d as $user){
                                ?>
                                <div class="single-element single-element-for-click" idValue=" <?php echo $user['user_id'] ?> "><?php echo $user['fname'].' '.$user['lname']; ?></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="search-wrapper">
                    <input type="text" name="naziv-covjeka" value="<?php if(!$hr_admin){echo isset($impersonator[0]) ? $impersonator[0]['fname'].' '.$impersonator[0]['lname'] : ''; } ?>" class="search-input" placeholder="Ime i prezime impersonatora" autocomplete="off">
                    <?php
                    if(isset($impersonator[0])){
                        ?> <input type="hidden" name="naziv-covjeka-id" value="<?php if(!$hr_admin){echo $impersonator[0]['user_id'];} ?>"> <?php
                    }
                    ?>
                    <?php
                    if(isset($impersonator[0])){
                        ?>
                        <a href="?m=performance_management&p=impersonation&delete_it=<?php echo $_user['user_id']; ?>">
                            OBRIŠITE
                        </a>
                        <?php
                    }
                    ?>
                    <div class="all-elements-wrapper">
                        <?php
                        foreach($allUsers as $user){
                            ?>
                            <div class="single-element single-element-for-click" idValue=" <?php echo $user['user_id'] ?> "><?php echo $user['fname'].' '.$user['lname']; ?></div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="classic-button-wrapper">
                <input type="submit" class="just-a-button just-a-submit" value="SPREMITE">
            </div>

        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <?php
            if($pm_admin){
                ?>
                <div class="right-menu-link">
                    <a href="?m=performance_management&p=impersonation_list">Ovlaštenja za impersonaciju</a>
                </div>
                <?php
            }
            ?>
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>