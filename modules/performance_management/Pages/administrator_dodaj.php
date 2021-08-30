<link rel="stylesheet" href="theme/css/performance-management.css">
<?php require_once 'CORE/classes/user.php'; ?>
<?php require_once 'CORE/classes/pm.php'; ?>

<?php

//require_once 'modules/performance_management/table-scripts/impersonation.php';

// Selected values
isset($_POST['standard_user']) ? $standard_user = $_POST['standard_user'] : $standard_user = '';
isset($_POST['impersonator'])  ? $impersonator = $_POST['impersonator']   : $impersonator = '';

$hr_admin = false;
if($_user['role'] == 4) $hr_admin = true;

$user = new User($db); $pm = new PM($db);


// Get all users - WORK WITH USERS
$allUsers   = $user->allUsers();
$allUsers_d = $user->allUsers();
if(isset($_POST['naziv-usera-id'])){
    $id = $_POST['naziv-usera-id'];
    $created_at = date('Y-m-d');

    try{
        $admin = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_administratori] where user_id = ".$id)->fetch();

        if(!$admin){
            $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_administratori] (user_id, created_at) values ('$id', '$created_at')");
            $success = 'Uspješno ste spremili administratora !';
        }else{
            $success = 'Administrator već postoji u bazi!';
        }
    }catch (\PDOException $e){}
}

?>
<form id="admin-form" method="post">
    <div class="split-on-right">

        <div class="choose-what-to-do">
            <?php
            if(isset($success)){
                ?>
                <div class="message">
                    <?= $success ?>
                </div>
                <?php
            }
            ?>

            <h3>
                Unesite novog administratora 
            </h3>

            <div class="just-a-row">
                <!-- Ako je admin, onda ima opciju da izabere managera i impersonatora -->
                <?php
                if($pm_admin){
                    ?>
                    <div class="search-wrapper">
                        <input type="text" name="naziv-usera" value="" class="search-input" placeholder="Unesite ime i/ili uposlenika" autocomplete="off">

                        <div class="all-elements-wrapper">
                            <?php
                            foreach($allUsers_d as $user){
                                if($user['role'] == 2 or $user['role'] == 4){
                                    ?>
                                    <div class="single-element single-element-for-click" idValue=" <?php echo $user['user_id'] ?> "><?php echo $user['fname'].' '.$user['lname']; ?></div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

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
                    <a href="?m=performance_management&p=administrator_pregled">Pregled svih administratora</a>
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