<script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="theme/css/performance-management.css">


<?php

//include('modules/performance_management/table-scripts/radna_mjesta.php');

if(isset($_POST['radno_mjesto'])){
    $rm = ($_POST['radno_mjesto']);

    try{
        $sample = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta] where naziv LIKE '{$rm}'")->fetch();
    }catch (PDOException $e){
        var_dump($e);
    }

    if($sample[0] == 0){
        $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta] (naziv) VALUES ('{$rm}')");

        $users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE position LIKE '$rm'")->fetchAll();

        foreach ($users as $user){
            try{
                $us_id = $user['user_id'];
                $sample = $db->query("SELECT count(*) FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id LIKE '{$us_id}'")->fetch();
            }catch (PDOException $e){
                var_dump($e);
            }
            if($sample[0] == 0){
                $db->query("INSERT INTO [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] (user_id) VALUES ('{$us_id}')");
            }
        }

//
//        var_dump($db->query($users)->fetchAll());
    }
}



if(isset($_GET['id'])){
    $rm = $db->query('SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta] where id = '.intval($_GET['id']))->fetch();
    $rm_naziv = $rm['naziv'];

    $users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE position LIKE '$rm_naziv'")->fetchAll();

    foreach ($users as $user){
        try{
            $us_id = $user['user_id'];

            $db->query('DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] where user_id = '.$us_id);
        }catch (PDOException $e){
            var_dump($e);
        }
    }

    // Napokon, pobriši radno mjesto
    $db->query('DELETE FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta] where id = '.intval($_GET['id']));
}

//include "modules/performance_management/table-scripts/radna_mjesta.php";
try{
    $radnaMjesta = $db->query("SELECT DISTINCT Description
            FROM ".$_conf['nav_database'].".[RAIFFAISEN BANK\$Position Menu]")
        ->fetchAll();
}catch (\PDOException $e){
    var_dump($e);
}

try{
    $rms = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_radna_mjesta]")->fetchAll();
}catch (\PDOException $e){
    var_dump($e);
}


?>


<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Spisak radnih mjesta
            </h3>

            <div class="just-a-row">
                <div class="inside-col">
                    <select name="radno_mjesto" class="select-2">
                        <option value="0">Odaberite radno mjesto</option>
                        <?php
                        foreach($radnaMjesta as $rm){ ?>
                            <option value="<?php echo $rm['Description']; ?>"><?php echo $rm['Description']; ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
            </div>

            <div class="just-a-row">
                <div class="save-button">
                    <input type="submit" value="SPREMITE PODATKE">
                </div>
            </div>

            <br>

            <div class="single-element">
                <?php
                foreach($rms as $rm){
                    ?>
                    <div class="just-a-row">
                        <div class="inside-col">
                            <input type="text" value="<?php echo $rm['naziv']; ?>" readonly>

                            <a href="?m=performance_management&p=mbo_users_list&id=<?php echo $rm['id']; ?>" style="z-index:0;">
                                <p>OBRIŠITE</p>
                            </a>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=mbo_users_list_users">Spisak uposlenika <uzoraka></uzoraka></a>
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