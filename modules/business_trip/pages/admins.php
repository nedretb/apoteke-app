<?php
  _pagePermission(4, false);

  function userList22(){
    global $db;
    $subs_users = [];
  $subs_external_hr = [];
  $hr_subsq = "";
  $randx = $db->prepare("select username from [c0_intranet2_apoteke].[dbo].[users] where employee_no<0 or sl_put_admin=1");
  $randx->execute();
  $exist = $randx->fetchAll();
  
  foreach($exist as $user){
    $hr_subsq .= "'". $user['username']."', ";
  }
  $hr_subs = rtrim($hr_subsq, ", ");

  $users_raiffq = $db->query("SELECT user_id, fname, lname from [c0_intranet2_apoteke].[dbo].[users] where (role=4 or role=2) and username not in (".$hr_subs.")");
  $users_raiff = $users_raiffq->fetchAll();

  $users_investq = $db->query("SELECT user_id, fname, lname from [c0_intranet2_apoteke].[dbo].[users] where (employee_no>=800000) and (role=4 or role=2)");
  $users_invest = $users_investq->fetchAll();

  return (object) array_merge( (array) $users_raiff, (array) $users_invest);
  //var_dump($hr_subs);
  //echo "<br>";
  //var_dump($subs_external_hr);
  }
  


  if (isset($_GET['obrisi'])){
     
     $uname="";
    $br_admina = $db->query("SELECT count(user_id) as rr from [c0_intranet2_apoteke].[dbo].[users] where sl_put_admin=1");

  foreach($br_admina as $admin){
    $br = $admin;
  } 

  $check_invest_result = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id=".$_GET['obrisi']);
  $check_invest_result->execute();
  $user_existst = $check_invest_result->fetchAll();
  foreach($user_existst as $u){
    $uname= $u['username'];
  }

  $check_bank = $db->prepare("select * from [c0_intranet2_apoteke].[dbo].[users] where username='". $uname. "'");
  $check_bank->execute();
  $user_exists_bank = $check_bank->fetchAll();

    if ($br['rr']>1){

      if(count($user_exists_bank) > 0){
      $sl_put;
      foreach ($user_existst as $key) {
        $sl_put = $key['talent_admin'];
      }
     
      if(count($user_existst) > 0 and $sl_put == '1'){
          $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] SET sl_put_admin = 0 where user_id =".$_GET['obrisi']) ;
      }
      else{
        $db->query("delete from [c0_intranet2_apoteke].[dbo].[users] where user_id=". $_GET['obrisi']);
      }
  }
      else{
        $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] set sl_put_admin = 0 where user_id =".$_GET['obrisi']);
      } 

      

    }
  }


  if(isset($_POST['user_id'])){
    
        $check_invest_result = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id=".$_POST['user_id']);
  $check_invest_result->execute();
  $user_existst = $check_invest_result->fetchAll();
  

  if(count($user_existst) < 1){
      $check_raiff_result = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id=".$_POST['user_id']);
      $check_raiff_result->execute();
      $user_existst_raiff = $check_raiff_result->fetchAll();
      $bank_username = "";
      foreach ($user_existst_raiff as $key) {
        $bank_username = $key['username'];
      }

      $check_invest_bank = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where username='".$bank_username."'");
      $check_invest_bank->execute();
      $user_existst_invest = $check_invest_bank->fetchAll();
      
      if(count($user_existst_invest) > 0){
          $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] SET sl_put_admin = 1 where username='".$bank_username."'") ;
    }
    else{

    $bank_dataq = $db->query("select username from [c0_intranet2_apoteke].[dbo].[users] where user_id=". $_POST['user_id']);
    $bank_data = $bank_dataq->fetchAll();
    
    foreach($bank_data as $b){
      $bank_user = $b['username'];
    }
    /*$check_rep = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where username='".$bank_user."'");
    $check_rep->execute();
    $user_rep = $check_rep->fetchAll();
    if(count($user_rep)>0){
      continue;
    }*/

       $d = $db->prepare("INSERT INTO [c0_intranet2_apoteke].[dbo].[users] SELECT [username]
        ,[password]
        ,[role]
        ,[email]
        ,[email_company]
        ,[image]
        ,[fname]
        ,[lname]
        ,[address]
        ,[zip]
        ,[city]
        ,[country]
        ,[phone]
        ,[phone_mob]
        ,[phone_company]
        ,[phone_mob_company]
        ,[JMB]
        ,[position]
        ,[position_code]
        ,[department_code]
        ,[sector]
        ,[Stream_code]
        ,[Team]
        ,[Team_description]
        ,[B_1_description]
        ,[B_1_regions]
        ,[B_1_regions_description]
        ,[Stream_description]
        ,[department_code_type]
        ,[parent]
        ,[parent2]
        ,[parent3]
        ,[zamjenik]
        ,[hr]
        ,[hr2]
        ,[hr3]
        ,[admin]
        ,[lang]
        ,[status]
        ,[dates]
        ,[dates_deactivate]
        ,[dates_reactivate]
        ,[employee_no]
        ,[dosier_no]
        ,[image_no]
        ,[admin1]
        ,[admin2]
        ,[admin3]
        ,[admin4]
        ,[admin5]
        ,[admin6]
        ,[admin7]
        ,[admin8]
        ,[employment_date]
        ,[gender]
        ,[inactive_date]
        ,[managment_level]
        ,[termination_date]
        ,[parentMBO2]
        ,[parentMBO3]
        ,[parentMBO4]
        ,[parentMBO5]
        ,[parentMBO2d]
        ,[parentMBO3d]
        ,[parentMBO4d]
        ,[parentMBO5d]
        ,[MBO]
        ,[parentMBO6]
        ,[stream_parent]
        ,[org_dio]
        ,[to_admin]
        ,[to_admin2]
        ,[external]
        ,[centrala]
        ,[mjesto_org]
        ,[mjesto_rada]
        ,[adress_cips]
        ,[br_sati]
        ,[adress_org]
        ,[picture]
        ,[talent_admin]
        ,[sl_put_admin]
      FROM [c0_intranet2_apoteke].[dbo].[users] where user_id=". $_POST['user_id']);
        $updateExternal = $db->prepare("update [c0_intranet2_apoteke].[dbo].[users] SET sl_put_admin=0, talent_admin=1, [external]=1, role=2 where username='". $bank_user . "'");
        $d->execute();
        $updateExternal->execute();
    }


  }
  elseif(count($user_existst) > 0){
      $db->query("UPDATE [c0_intranet2_apoteke].[dbo].[users] SET sl_put_admin = 1 where user_id=".$_POST['user_id']) ;
    }

    
    userList22();
  }

    
  $admini_investq = $db->query("SELECT user_id, fname, lname from [c0_intranet2_apoteke].[dbo].[users] WHERE sl_put_admin = 1");
  $admini_invest = $admini_investq->fetchAll();
  
  $admini = $admini_invest;
  $users = userList22();
  
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
<a class='table-btn' onclick="window.location.href ='/apoteke-app/?m=business_trip&p=admins&obrisi=<?php echo $admin['user_id'];?>'"><i class='ion-android-close'></i></a>
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
