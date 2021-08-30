<!-- Privilegija koja je ostavljena samo za HR-Admina i rukovodioce -->
<?php
require_once 'CORE/classes/user.php';
$usssr = new User($db);
$children = $usssr->getAllChildrenFromUser($_user['employee_no'], $_user['user_id']);


?>

<div class="right-menu-link">
    <a href="#"><b>OSTALI LINKOVI</b></a>
</div>
<?php
if($pm_admin){
    ?>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=administrator_dodaj">Administratori</a>
    </div>
    <?php
}
?>

<div class="right-menu-link">
    <a href="?m=performance_management&p=mbo_users_agreement">Pregled sporazuma zaposlenika</a>
</div>

<?php
if($pm_admin){
    ?>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=sifarnici">Šifarnici</a>
    </div>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=mbo_kompetencije_lista">Lista kompetencija</a>
    </div>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=mbo_kalendar">Kalendar</a>
    </div>

    <div class="right-menu-link">
        <a href="?m=performance_management&p=mbo_users_list">Spisak radnih mjesta</a>
    </div>
    <?php
}

?>
<div class="right-menu-link">
    <a href="?m=performance_management&p=goals_examples">Uzorci ciljeva</a>
</div>
<?php

if($pm_admin or count($children)){
    ?>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=pregled_statusa">Pregled statusa</a>
    </div>
    <?php
}

if($pm_admin or $_user['role'] == 4 or count($children)){
    ?>
    <div class="right-menu-link">
        <a href="?m=performance_management&p=izvjestaj">Izvještaj</a>
    </div>
    <?php
}

?>

