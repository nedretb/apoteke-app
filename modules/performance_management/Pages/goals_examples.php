<link rel="stylesheet" href="theme/css/performance-management.css">
<?php

//require_once 'modules/performance_management/table-scripts/uzorci.php';

if(isset($_POST['kategorija'])) require_once 'modules/performance_management/Pages/scripts/samples.php';
try{
    $instance = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[sifrarnici] where active = 1 and name = 'pm_kategorija'")->fetchAll();

    if(isset($_GET['preview'])){
        $goal = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi_uzorci_ciljeva]  where id = ".$_GET['preview'])->fetch();
    }


    /*
    $main_query = $db->query("SELECT *  
            FROM ".$_conf['nav_database'].".[RAIFFAISEN BANK\$Head Of_s] as  h
            join ".$_conf['nav_database'].".[RAIFFAISEN BANK\$ORG Shema] as o on  h.[ORG Shema]=o.Code
            join [c0_intranet2_apoteke].[dbo].[users] as u on h.[Position Code]= u.position_code 
            COLLATE SQL_Latin1_General_CP1_CI_AS
            where o.Status = 0 and u.user_id = ".$_user['user_id'])
        ->fetch(); */


    $radnaMjesta = $db->query("	SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users]
            where parent = ".$_user['employee_no']."
            or parent2 = ".$_user['employee_no']."
            or parent3 = ".$_user['employee_no']."
            or parentmbo2 = ".$_user['employee_no']."
	        or parentmbo3 = ".$_user['employee_no']."
		    or parentmbo4 = ".$_user['employee_no']."
		    or parentmbo5 = ".$_user['employee_no']."
		")->fetchAll();


    /*
    $sector = false; $department = false; $group = false; $team = false;
    $radnaMjesta = array();

    // Provjerimo da li je vođa sektora
    if($main_query['sector'] and !$main_query['department_code'] and !$main_query['Stream_code'] and !$main_query['Team']){
        // Sector
        $sector = $main_query['sector'];
        $radnaMjesta = $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where sector = '$sector'")->fetchAll();
    }else if($main_query['sector'] and $main_query['department_code'] and !$main_query['Stream_code'] and !$main_query['Team']){
        // Department
        $department = $main_query['department_code'];
        $radnaMjesta = $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where department_code = '$department'")->fetchAll();
    }else if($main_query['sector'] and $main_query['department_code'] and $main_query['Stream_code'] and !$main_query['Team']){
        // Group
        $group = $main_query['Stream_code'];
        $radnaMjesta = $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where Stream_code = '$group'")->fetchAll();
    }else if($main_query['sector'] and $main_query['department_code'] and $main_query['Stream_code'] and $main_query['Team']){
        // Team
        $team = $main_query['Team'];
        $radnaMjesta = $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where Team = '$team'")->fetchAll();
    } */


    $sviImpersonatori = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_impersonacija] where impersonator_id = ".$_user['user_id'])->fetchAll();
    foreach ($sviImpersonatori as $imp){
        $rms = $db->query("	SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users]
            where parent = ".$imp['employee_no']."
            or parent2 = ".$imp['employee_no']."
            or parent3 = ".$imp['employee_no']."
            or parentmbo2 = ".$imp['employee_no']."
	        or parentmbo3 = ".$imp['employee_no']."
		    or parentmbo4 = ".$imp['employee_no']."
		    or parentmbo5 = ".$imp['employee_no']."
		")->fetchAll();

        $radnaMjesta = array_merge($radnaMjesta, $rms);


        /*
        $main_query = $db->query("SELECT *  
            FROM ".$_conf['nav_database'].".[RAIFFAISEN BANK\$Head Of_s] as  h
            join ".$_conf['nav_database'].".[RAIFFAISEN BANK\$ORG Shema] as o on  h.[ORG Shema]=o.Code
            join [c0_intranet2_apoteke].[dbo].[users] as u on h.[Position Code]= u.position_code 
            COLLATE SQL_Latin1_General_CP1_CI_AS
            where o.Active=1 and u.user_id = ".$imp['user_id'])
            ->fetch();



        $sector = false; $department = false; $group = false; $team = false;

        // Provjerimo da li je vođa sektora
        if($main_query['sector'] and !$main_query['department_code'] and !$main_query['Stream_code'] and !$main_query['Team']){
            // Sector
            $sector = $main_query['sector'];
            $radnaMjesta = array_merge($radnaMjesta, $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where sector = '$sector'")->fetchAll());
        }else if($main_query['sector'] and $main_query['department_code'] and !$main_query['Stream_code'] and !$main_query['Team']){
            // Department
            $department = $main_query['department_code'];
            $radnaMjesta = array_merge($radnaMjesta, $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where department_code = '$department'")->fetchAll());
        }else if($main_query['sector'] and $main_query['department_code'] and $main_query['Stream_code'] and !$main_query['Team']){
            // Group
            $group = $main_query['Stream_code'];
            $radnaMjesta = array_merge($radnaMjesta, $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where Stream_code = '$group'")->fetchAll());
        }else if($main_query['sector'] and $main_query['department_code'] and $main_query['Stream_code'] and $main_query['Team']){
            // Team
            $team = $main_query['Team'];
            $radnaMjesta = array_merge($radnaMjesta, $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users] where Team = '$team'")->fetchAll());
        } */
    }

    if($pm_admin){
        $radnaMjesta = $db->query("SELECT DISTINCT position_code, position FROM [c0_intranet2_apoteke].[dbo].[users]")->fetchAll();
    }
}catch (exception $e) {var_dump($e);}
 ?>

<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Dodajte/uredite “uzorak cilja”
            </h3>

            <?php
            if(isset($_GET['preview'])){
                ?>
                <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                    <input type="hidden" name="keyword" value="<?php echo $goal['keywor']; ?>">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kategorija cilja
                            </div>
                            <select name="kategorija">
                                <option value="0">Odaberite kategoriju</option>
                                <?php
                                foreach($instance as $inst){ ?>
                                    <option value="<?php echo $inst['id']; ?>" <?php if(isset($goal)){if($inst['id'] == $goal['kategorija'])  echo "selected";} ?>><?php echo $inst['naziv_instance']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Naziv cilja
                            </div>
                            <input type="text" name="naziv_cilja" placeholder="Naziv cilja" value="<?php echo isset($goal) ? $goal['naziv_cilja'] : ''; ?>">
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Opis cilja
                            </div>
                            <textarea name="opis_cilja" placeholder="" style="height: 120px;"><?php echo isset($goal) ? $goal['opis_cilja'] : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Težina
                            </div>
                            <input type="number" name="tezina" placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" value="<?php echo isset($goal) ? $goal['tezina'] : ''; ?>">
                        </div>
                        <?php
                        if(!$goal['radno_mjesto']){
                            ?>
                            <div class="inside-col">
                                <div class="label-for">
                                    Uzorak za mene
                                </div>
                                <select name="za_mene">
                                    <option value="0" <?php if(!$goal['for_me']) echo 'selected'; ?> >Ne</option>
                                    <option value="1" <?php if($goal['for_me']) echo 'selected'; ?> >Da</option>
                                </select>
                            </div>
                            <?php
                        }
                        ?>
                    </div>



                    <?php
                    if($goal['owner'] == $_user['user_id'] or $pm_admin){
                        ?>
                        <div class="just-a-row">
                            <div class="save-button">
                                <input type="submit" value="SPREMITE PODATKE">
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </form>
                <?php
            }else{
                ?>
                <form action="/modules/performance_management/Pages/scripts/save_mbo.php" method="post">
                    <input type="hidden" name="id_usera" value="<?php echo $_user['user_id']; ?>">
                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Kategorija cilja
                            </div>
                            <select name="kategorija">
                                <option value="0">Odaberite kategoriju</option>
                                <?php
                                foreach($instance as $inst){ ?>
                                    <option value="<?php echo $inst['id']; ?>" <?php if(isset($kategorija)){if($inst['id'] == $kategorija)  echo "selected";} ?>><?php echo $inst['naziv_instance']; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="inside-col">
                            <div class="label-for">
                                Naziv cilja
                            </div>
                            <input type="text" name="naziv_cilja" placeholder="Naziv cilja" value="<?php echo isset($naziv_cilja) ? $naziv_cilja : ''; ?>">
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Opis cilja
                            </div>
                            <textarea name="opis_cilja" placeholder="" style="height: 120px;"><?php echo isset($opis_cilja) ? $opis_cilja : ''; ?></textarea>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Težina
                            </div>
                            <input type="number" name="tezina" placeholder="Težina (1.00 - 100.00)" step=".01" min="1" max="100" value="<?php echo isset($tezina) ? $tezina : ''; ?>">
                        </div>

                        <div class="inside-col">
                            <div class="label-for">
                                Uzorak za mene
                            </div>
                            <select name="za_mene">
                                <option value="0">Ne</option>
                                <option value="1">Da</option>
                            </select>
                        </div>
                    </div>

                    <div class="just-a-row">
                        <div class="inside-col">
                            <div class="label-for">
                                Radna mjesta
                            </div>
                            <select name="radno_mjesto">
                                <option value="0">Odaberite radno mjesto</option>
                                <?php
                                foreach($radnaMjesta as $rm){ ?>
                                    <option value="<?php echo $rm['position']; ?>"><?php echo $rm['position']; ?></option>
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

                </form>
                <?php
            }
            ?>
        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>

            <div class="right-menu-link">
                <a href="?m=performance_management&p=goals_examples_all">Pregled svih <uzoraka></uzoraka></a>
            </div>

            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>

</form>