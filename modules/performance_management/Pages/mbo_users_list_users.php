<script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="theme/css/performance-management.css">

<?php



$rms = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta]")->fetchAll();

$counter = 0; $query = '';
foreach($rms as $rm){
    $naziv = $rm['naziv'];
    if($counter == 0){
        $query = "WHERE position LIKE '$naziv' ";
    }else{
        $query .= "OR position LIKE '$naziv' ";
    }
    $counter++;
}

$users = "SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] ".$query;
$s_users = "SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] ";



// DElete user

if(isset($_POST['delete_user'])){
    $us_id = $_POST['delete_user'];
    $db->query('DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = '.$us_id);
}

// $users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE position LIKE 'Rukovodilac poslovnog odnosa sa fizičkim licima'")->fetchAll();
try{
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $no_of_records_per_page = 20;
    $offset = (int)(($page-1) * $no_of_records_per_page);

    $totalRows = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] ")->fetch()[0];
    $total_pages = ceil($totalRows / $no_of_records_per_page);

    $result = $db->query("SELECT *
        FROM
            [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
        ORDER BY
            user_id
        OFFSET $offset ROWS 
        FETCH FIRST $no_of_records_per_page ROWS ONLY
    ")->fetchAll();
}catch (\Exception $e){
    var_dump($e);
}

$users = $db->query($users)->fetchAll();
$s_users = $db->query($s_users)->fetchAll();

//var_dump($users);
//$str = substr($string, 0, strlen($string) - 2);
//
//var_dump($str);
//
//
//try{
//    $users = query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE position IN (".$str.")")->fetchAll();
//}catch (\Exception $e){}
?>


<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Spisak uposlenika
            </h3>

            <br>
            <div class="single-element">

                <form action="POST">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <select name="delete_user" class="select-2">
                                <option value="0">Odaberite zaposlenika kojeg želite obrisati</option>
                                <?php
                                foreach($s_users as $usr){
                                    $user = $db->query("SELECT fname, lname, user_id FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$usr['user_id'])->fetch();;
                                    ?>
                                    <option value="<?= $user['user_id']; ?>"><?= $user['fname']; ?> <?= $user['lname']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="save-button">
                            <input type="submit" value="OBRIŠITE">
                        </div>
                    </div>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ime i prezime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1;
                        foreach($result as $r){
                            $user = $db->query("SELECT fname, lname, position, user_id FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$r['user_id'])->fetch();
                            ?>
                            <tr>
                                <td><?= $counter++; ?></td>
                                <td><?php echo $user['lname']. ' '. $user['fname']; ?> ( <?= $user['position'] ?> )</td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>

                <br>
                <div class="btn-group paginate">
                    <?php echo _pagination('?m=performance_management&p=mbo_users_list_users&page=', $page, '20', $totalRows); ?>
                </div>
            </div>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=mbo_users_list">Spisak radnih mjesta</a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.select-2').select2();
        });
    </script>

</form>