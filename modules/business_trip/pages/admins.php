<?php
_pagePermission(4, false);

if (isset($_GET['obrisi'])){
    $br_admina = $db->query("SELECT count(user_id) as rr from [c0_intranet2_apoteke].[dbo].[users] WHERE sl_put_admin = 1");
    foreach($br_admina as $admin){
        $br = $admin;
    }
    if ($br['rr']>1){
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] set sl_put_admin = 0 where user_id =".$_GET['obrisi']);

    }
}
if(isset($_POST['user_id'])){
    $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] SET sl_put_admin = 1 where user_id=".$_POST['user_id']) ;
}
$users = $db->query("SELECT user_id, fname, lname from [c0_intranet2_apoteke].[dbo].[users] ");
$admini = $db->query("SELECT user_id, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE sl_put_admin = 1");
?>
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid black !important;
    }
</style>
<!-- START - Main section -->
<section class="full">

    <div class="container-fluid">


        <div class="row">

            <div class="col-sm-12 text-center">
                <h2>
                    <?php echo __('Administratori službenog puta'); ?>

                </h2>
            </div>
            <div class="col-sm-4 text-right"><br/>

            </div>

        </div>


        <div class="row">

            <?php



            ?>
        </div>
        <div class = "row">
            <div class='col-6' >
                <div class="box col-6" id="c1">
                    <div class="head">
                        <div class="box-head-btn">
                            <a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a"></a>
                            <a href="#" class="ion-arrow-expand" data-widget="fullscreen" data-id="c1"></a>
                        </div>
                        <h3><?php echo __('Novi unos'); ?></h3>
                    </div>
                    <div class="content clear" id="c1a" style="display: block;">

                        <form action="" method="post" id="form">

                            <label class="lable-admin1"><?php echo __('Ime'); ?></label>
                            <select id="ime_prezime" name="user_id" class="rcorners1" style = "outline:none;width:200px;" class="form-control" required>
                                <option selected disabled>Odaberi..</option>
                                <?php
                                foreach($users as $user){
                                    echo "<option value='".$user['user_id']."'>".$user['fname'].' '.$user['lname']."</option>";
                                }
                                ?>
                            </select><br/>


                            <button type="submit" class="btn btn-red pull-right" ><?php echo __('Spasi!');  ?>  <i class="ion-ios-download-outline"></i></button>

                        </form>

                    </div>
                </div>

            </div>
            <div class='col-6' style='width:auto;display:inline;'>
                <table class="table table-hover">
                    <thead>
                    <th>Ime i prezime</th>
                    <th style='text-align:center;'>Akcije</th>
                    </thead>
                    <tbody>
                    <?php
                    foreach($admini as $admin){
                        $a = $admin['user_id'];
                        ?>
                        <tr>
                            <td>
                                <?php echo $admin['fname'].' '.$admin['lname']; ?>
                            </td>
                            <td style='text-align:center;'>
                                <a class='table-btn bt-admins' onclick="window.location.href ='/apoteke-app/?m=business_trip&p=admins&obrisi=<?php echo $admin['user_id'];?>'"><i class='ion-android-close'></i></a>
                            </td>
                        </tr>
                        <?php

                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>
<!-- END - Main section -->

<?php

include $_themeRoot.'/footer.php';

?>

<script>
    $("#ime_prezime").select2();

    $(function(){

        $('form#form').validate({
            focusCleanup:true
        });

    });


</script>


</body>
</html>
