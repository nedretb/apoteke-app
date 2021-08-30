<link rel="stylesheet" href="theme/css/performance-management.css">
<?php require_once 'CORE/classes/user.php'; ?>
<?php require_once 'CORE/classes/pm.php'; ?>

<?php

//require_once 'modules/performance_management/table-scripts/lista_korisnika.php';
if(isset($_POST['lista_ime_prezime']) or isset($_GET['lista_usera_delete'])) { require_once 'modules/performance_management/Pages/scripts/save_mbo.php'; }

$user = new User($db); $pm = new PM($db);


// Get all users - WORK WITH USERS
$allUsers   = $user->allUsers();
$allUsers_d = $user->allUsers();

try{
//    $korisnici = $db->query("SELECT
//                        [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika].*,
//                        [c0_intranet2_apoteke].[dbo].[users].[user_id],
//                        [c0_intranet2_apoteke].[dbo].[users].[fname],
//                        [c0_intranet2_apoteke].[dbo].[users].[lname]
//
//                        FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
//                        INNER JOIN [c0_intranet2_apoteke].[dbo].[users] ON [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika].[user_id] = [c0_intranet2_apoteke].[dbo].[users].[user_id]
//                        ")->fetchAll();
//
//
//    $user_id = 1;
//    $users = $db->query("SELECT  *
//        FROM    ( SELECT    ROW_NUMBER() OVER ( ORDER BY user_id ) AS RowNum, *
//                  FROM      [c0_intranet2_apoteke].[dbo].[users]
//                  WHERE user_id > '{$user_id}'
//                ) AS RowConstrainedResult
//        WHERE   RowNum >= 1
//            AND RowNum <= 40
//        ORDER BY RowNum
//    ")->fetchAll();

    if(isset($_GET['tag_all'])){ // Označi sve korisnike
        $users = $db->query("SELECT user_id FROM [c0_intranet2_apoteke].[dbo].[users]")->fetchAll();
        $list  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]")->fetchAll();

        foreach($list as $item){
            $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = ".$item['user_id']);
        }
        foreach($users as $usr){
            $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] (user_id) VALUES ('{$usr['user_id']}')");
        }
    }
    if(isset($_GET['untag_all'])){
        $list  = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]")->fetchAll();

        foreach($list as $item){
            $db->query("DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = ".$item['user_id']);
        }
    }


    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $no_of_records_per_page = 20;
    $offset = (int)(($page-1) * $no_of_records_per_page);

    $totalRows = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[users]")->fetch()[0];
    $total_pages = ceil($totalRows / $no_of_records_per_page);

    $result = $db->query("SELECT *
        FROM
            [c0_intranet2_apoteke].[dbo].[users]
        ORDER BY
            fname
        OFFSET $offset ROWS 
        FETCH FIRST $no_of_records_per_page ROWS ONLY
    ")->fetchAll();
}catch (PDOException $e){var_dump($e->getMessage()); die();}

?>
<div class="loading-part-gif">
    <img src="theme/images/loading.gif" alt="">
</div>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>Spisak korisnika za pristup PM modulu</h3>
            <a href="?m=performance_management&p=mbo_lista_usera&tag_all=true"> Označi sve </a>
            <a href="?m=performance_management&p=mbo_lista_usera&untag_all=true"> Odznači sve </a>


            <div class="single-element">
                <?php
                foreach($result as $user){
                    $korisnik = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = ".$user['user_id'])->fetchAll());

                    ?>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <input type="text" value="<?php echo $user['fname'].' '.$user['lname'].' - '.$user['position'].' - '.$user['employee_no']; ?>" readonly>

                            <a href="?m=performance_management&p=mbo&user_id=<?php echo $user['user_id']; ?>">
                                <p>Pregled sporazuma</p>
                            </a>
                        </div>
                        <input type="checkbox" class="check-user-list" <?php if($korisnik == 1) echo 'checked'; ?> selectedId="<?= $user['user_id'] ?>">
                    </div>
                    <?php
                }
                ?>
            </div>

            <br>
            <div class="btn-group paginate">
                <?php echo _pagination('?m=performance_management&p=mbo_lista_usera&page=', $page, '20', $totalRows); ?>
            </div>
        </div>

        <div class="right-menu">
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>
<script src="theme/js/performance-management.js"></script>