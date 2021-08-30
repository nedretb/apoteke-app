<link rel="stylesheet" href="theme/css/performance-management.css">
<?php
//require_once 'modules/performance_management/table-scripts/pm_sporazumi.php';

require_once 'CORE/classes/agreement.php';
$agreement = new Agreement($db);
if(isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];
    $all_agreements = $agreement->getSentAgreement($_GET['user_id']);
}else {
    $user_id = $_user['user_id'];
    $all_agreements = $agreement->getAgreement($_user['user_id']);
}

// Selected values
isset($_POST['standard_user']) ? $standard_user = $_POST['standard_user'] : $standard_user = '';
isset($_POST['impersonator'])  ? $impersonator = $_POST['impersonator']   : $impersonator = '';


/*******************************************************************************************************************
 *  Here depends on role, we can have two options:
 * *****************************************************************************************************************
 *      1. HR Admin - can choose user and it's impersonator
 *      2. Manager - Can only pick it's impersonator
 * ****************************************************************************************************************/

$hr_admin = false; $good_to_go = false; $can_create = false;

// Provjerimo trenutni datum, da li možemo kreirati ugovor ili ne, tj da li smo u fazi paniranja
try{
    $date = date('Y-m-d');
    $date_is_fine = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_kalendar] where datum_od <= '$date' and datum_do >= '$date' and faza = 1 ")->fetchAll());
    $year = date('Y');

    // Ako nema ni jedan sporazum, onda omogućiti da se kreira novi !
    $sporazum = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = '$user_id' and year = '$year'")->fetchAll());

    if(!$sporazum) $good_to_go = true;


    // Ako ima kreiran sporazum, ali ako nema status sent = 1
    if($sporazum){
        // Ako postoji, onda ima smisla ga tražiti
        $sent_false = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = '$user_id' and year = '$year' and sent IS NULL")->fetchAll());
        if($sent_false) $good_to_go = true;
    }

    $ocjene   = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene]")->fetchAll();

    // Provjeri da li može kreirati novi sporazum
    $my_id = $_user['user_id'];
    $moj_sporazum = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = '$my_id' and year = '$year'")->fetchAll());
    if(!$moj_sporazum) $can_create = true;
    else{
        $sent_false = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = '$my_id' and year = '$year' and created IS NULL")->fetchAll());
        if($sent_false) $can_create = true;
    }
}catch (exception $e) {
    var_dump($e);
}


?>
<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Pregled svih sporazuma
            </h3>

            <!-- Force create new MBO agreement -->
            <?php
            if(isset($_GET['user_id'])){
                ?>
                <a href="?m=performance_management&p=mbo_new_f&user_id=<?= $_GET['user_id']; ?>" title="Kreirajte novi ugovor za uposlenika !!">
                    Kreirajte novi ugovor za uposlenika
                </a>
                <?php
            }
            if(isset($_GET['msg'])){
                echo "<p style='color:red;'>Ugovor uspješno kreiran !!</p>";
            }
            ?>
            
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Zaposlenik</th>
                    <th>Godina kreiranja</th>
                    <th>Status sporazuma</th>
<!--                    <th>Konačna ocjena</th>-->
                    <th class="last-one">AKCIJE</th>
                </tr>
                </thead>
                <tbody>
                <?php $counter = 1;
                foreach($all_agreements as $agree){
                    if($agree['created']){
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?>.</td>
                            <td><?php echo $agree['fname'].' '.$agree['lname']; ?></td>
                            <td><?php echo $agree['year']; ?></td>
                            <td>
                                <?php
                                $status = 'Nije prihvaćen';
                                if($agree['accepted_from_supervisor'] or $agree['sent']){
                                    $status = 'Djelomično prihvaćen';
                                }
                                if($agree['accepted_from_supervisor'] AND $agree['accepted_from_employee']){
                                    $status = 'Obostrano prihvaćen';
                                }

                                echo $status;

                                //                            if(!$agree['sent'] and !$agree['accepted_from_supervisor'] and !$agree['accepted_from_employee']) echo 'Sporazum na čekanju!';
                                //                            else if($agree['status'] == 0) echo 'Sporazum nije prihvaćen!';
                                //                            else if($agree['status'] == 1) echo 'Sporazum djelomično prihvaćen!';
                                //                            else echo 'Sporazum prihvaćen!';
                                ?>
                            </td>
<!--                            <td>-->
<!--                                --><?php
//                                foreach ($ocjene as $ocjena) {
//                                    if ($ocjena['value'] == round($agree['final_grade'])) echo $ocjena['name'];
//                                }
//                                ?>
<!--                            </td>-->

                            <?php

                            if(!$agree['sent'] and $agree['forced'] and !isset($_GET['user_id'])){
                                ?>
                                <td class="last-one" title="Pregledajte / Editujte">
                                    <a href="?m=performance_management&p=mbo_new_force&id=<?php echo $agree['id']; ?>">
                                        <div class="my-button">PREGLED</div>
                                    </a>
                                </td>
                                <?php
                            }else{
                                ?>
                                <td class="last-one" title="Pregledajte / Editujte">
                                    <a href="?m=performance_management&p=mbo_preview&preview=<?php echo $agree['id']; ?>">
                                        <div class="my-button">PREGLED</div>
                                    </a>
                                </td>
                                <?php
                            }

                            if(isset($_GET['user_id'])){
                                ?>

                                <?php
                            }else{
                                ?>
<!--                                <td class="last-one" title="Pregledajte / Editujte">-->
<!--                                    <a href="?m=performance_management&p=mbo_preview_my&preview=--><?php //echo $agree['id']; ?><!--">-->
<!--                                        <div class="my-button">PREGLED</div>-->
<!--                                    </a>-->
<!--                                </td>-->
                                <?php
                            }
                            ?>

                        </tr>
                    <?php }
                    }
                ?>
                </tbody>
            </table>
        </div>

        <div class="right-menu">
            <?php
            if($date_is_fine and $can_create){
                ?>
                <div class="right-menu-header">
                    <h4>Dodatne opcije</h4>
                </div>

                <div class="right-menu-link">
                    <a href="?m=performance_management&p=mbo_new">Kreirajte novi sporazum</a>
                </div>
                <?php
            }
            ?>
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>