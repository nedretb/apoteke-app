<?php
global $db, $portal_users;
$users = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=1");
$inactive_users = $db->query("select distinct [Employee No_] from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active=0");


?>

<br><br>
<div class="simple-header">
    <div class="sh-left">
        <p> Postava godišnjeg odmora </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=work_booklet&p=add-new&edit=0">
                <p> <i class="fas fa-plus"></i> Dodajte novog </p>
            </a>
        </div>
        <?php
        // Samo administrator ili rukovodioc mogu vidjeti ovaj modul i pristupiti mu
        if($_user['role'] == 4 or $_user['rukovodioc'] == 'Da'){
            ?>
            <div class="inside-link">
                <a href="?m=work_booklet&p=pregled-planova">
                    <p> <i class="fas fa-calendar-minus"></i> Plan godišnjeg odmora </p>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!--<br>-->
<!--<div class="row">-->
<!--    <a class="btn btn-red btn-md pull-right" href="">--><?//= ___('Dodajte novog'); ?><!--</a>-->
<!--    <a class="btn btn-red btn-md pull-right" href="?m=work_booklet&p=count_go3">Count go</a>-->
<!--    <a class="btn btn-red btn-md pull-right" href="?m=work_booklet&p=update_ending_date">Update end date</a>-->
<!--</div>-->

<br>
<h3>Trenutni radnici</h3>
<table class="table table-bordered">
    <thead>
        <tr>
           <th scope="col" class="text-center" width="80px">#</th>
           <th scope="col">Šifra zaposlenika</th>
           <th scope="col">Ime i prezime</th>
           <th scope="col" width="120px" class="text-center">Akcije</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($users as $u){
            echo "<tr>";
            if($u['id']){
                echo "<td class='text-center'>".$u['id']."</td>";
            }

            if($u['Employee No_']){
                echo "<td>".$u['Employee No_']."</td>";
            }

            if($u['First Name']){
                echo "<td>".$u['First Name']." ";
            }

            if($u['Last Name']){
               echo $u['Last Name']. "</td>";
            }
            echo '<td class="text-center"><a class="my-btn" href="?m=work_booklet&p=add-new&edit='.$u['Employee No_'].'">Uredite</a></td>';
            echo "</tr>";
        }
    ?>
    </tbody>
</table>
<br>


<h3>Otišli radnici</h3>
<table class="table table-bordered">
    <thead>
    <tr>
        <th scope="col" class="text-center" width="80px">#</th>
        <th scope="col">Šifra zaposlenika</th>
        <th scope="col">Ime i prezime</th>
        <th scope="col" width="120px" class="text-center">Akcije</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($inactive_users as $u){
        $active_check = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where Active = 1 and [Employee No_]=".$u['Employee No_']);

        if ($active_check->rowCount() < 0){

        }else{
            $user_data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[work_booklet] where [Employee No_]=".$u['Employee No_'])->fetch();
            echo "<tr>";
            if($user_data['id']){
                echo "<td class='text-center'>".$user_data['id']."</td>";
            }

            if($user_data['Employee No_']){
                echo "<td>".$user_data['Employee No_']."</td>";
            }

            if($user_data['First Name']){
                echo "<td>".$user_data['First Name']." ";
            }

            if($user_data['Last Name']){
                echo $user_data['Last Name']. "</td>";
            }
            echo '<td class="text-center"><a class="my-btn" href="?m=work_booklet&p=add-new&edit='.$u['Employee No_'].'">Pregled</a></td>';
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>

<?php

include $_themeRoot . '/footer.php';

?>

<!--<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script> -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<script></script>
