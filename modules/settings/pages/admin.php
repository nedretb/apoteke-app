<br><br>
<div class="container">

<?php 

	$admin_count = $db->query("SELECT count(*) FROM ".$portal_users." where role=4")->fetch();
	$users = $db->query("SELECT user_id, employee_no, admin1, admin2, admin3, admin4, admin5, admin6, admin7, admin8, fname, lname from ".$portal_users." where role=2")->fetchAll();
	$admin_no_list = $db->query("SELECT * from ".$portal_users." where role=2")->fetch();

	if(isset($_POST['user_id'])){
	
		$f = $db->prepare("SELECT * FROM  ".$portal_users."  WHERE user_id ='".$_POST['user_id']."' and role=4");
		$f->execute();

		$postoji_user = $f->fetchAll();
		
		if(count($postoji_user) > 0 ){
			?>
			<div class="alert alert-warning">
				HR Administrator već postoji!
			</div>
			<?php 


		} else {
			$add_admin = $db->prepare("UPDATE ".$portal_users." set role=4 where employee_no=".$_POST['user_id']);
			$add_admin->execute();
			
			$update_admin_number = $db->prepare("UPDATE ".$portal_users." set ".$_POST['admin']."=".$_POST['user_id']);
			$update_admin_number->execute();

            $users = $db->query("SELECT user_id, employee_no, admin1, admin2, admin3, admin4, admin5, admin6, admin7, admin8, fname, lname from ".$portal_users." where role=2")->fetchAll();
				?>
		<div class="alert alert-success">
				HR Administrator uspješno unesen!
			</div>
		<?php
		}
}
	

	if(isset($_POST['delete'])){
		$admin_update = $db->prepare("update ".$portal_users."  set role=2 where employee_no=". $_POST['delete']);
		$admin_update->execute();	
		
		$admin_num_update = $db->prepare("update ".$portal_users."  set ".$_POST['admin_no']."=null");
		$admin_num_update->execute();

        $users = $db->query("SELECT user_id, employee_no, admin1, admin2, admin3, admin4, admin5, admin6, admin7, admin8, fname, lname from ".$portal_users." where role=2")->fetchAll();
		?>
		<div class="alert alert-success">
				HR Administrator uspješno izbrisan!
			</div>
		<?php
	}
?>


	<div class="col-md-5 box" style="padding: 15px;">
		<form method="POST" action="" id="form">
		<label class="lable-admin1"><?php echo __('Ime'); ?></label>
            <select id="name_surname" name="user_id" class="form-control" required>
            <option style=" color:black; border:solid 1px #6DACC9;" selected disabled>Odaberi..</option>
              <?php
              foreach($users as $user){
                echo "<option value='".$user['employee_no']."'>".$user['fname'].' '.$user['lname']."</option>";
                
              }
              ?>
           </select>
		   <br><br>
		   <label style="display: none;" class="lable-admin1"><?php echo __('Admin broj'); ?></label>
		   <select style="display: none;" id="admin_num" name="admin" class="form-control admin" required>
              <?php
              //foreach($adm in_no_list as $user){
				  if($user['admin1'] == null){
					echo "<option value='admin1'>Admin 1</option>";
				  }
				  if($user['admin2'] == null){
					echo "<option value='admin2'>Admin 2</option>";
				  }
				  if($user['admin3'] == null){
					echo "<option value='admin3'>Admin 3</option>";
				  }
				  if($user['admin4'] == null){
					echo "<option value='admin4'>Admin 4</option>";
				  }
				  if($user['admin5'] == null){
					echo "<option value='admin5'>Admin 5</option>";
				  }
				  if($user['admin6'] == null){
					echo "<option value='admin6'>Admin 6</option>";
				  }
				  if($user['admin7'] == null){
					echo "<option value='admin7'>Admin 7</option>";
				  }
				  if($user['admin8'] == null){
					echo "<option value='admin8'>Admin 8</option>";
				  }
              //}
              ?>
			  <br>
           </select>
			
			<?php 
				//if($admin_count < 8){
					?>
			
			<button style="

	background-color:#006695;
	margin-top:10px;
	border:1px solid #4e6096;
	display:inline-block;
	cursor:pointer;
	color: white !important;
	font-family:Arial;
	font-size:14px;
	padding:10px 30px;
">
				Dodaj
			</button>		
			<?php
				//}
			?> 
		</form>
	</div>
	<div class="col-md-7">
	<div class="box">
	<table class="table table-bordered">
		<thead>
			<th>Ime</th>
			<th>Prezime</th>
			<th>Admin broj</th>
			<th>Akcije</th>
		</thead>
		<?php 
			$users = $db->prepare("SELECT * FROM ".$portal_users."  where role=4");
			$users->execute();
			$u = $users->fetchAll();

			foreach($u as $key => $value){
				?>
					<tr>
						<td><?php echo $value['fname']; ?></td>
						<td><?php echo $value['lname']; ?></td>
						<td>
							<?php
								if($value['employee_no'] == $value['admin1']){
									$admin = 'admin1';
									echo "Admin 1";
								} 
								else if($value['employee_no'] == $value['admin2']){
									$admin = 'admin2';
									echo "Admin 2";
								} 
								else if($value['employee_no'] == $value['admin3']){
									$admin = 'admin3';
									echo "Admin 3";
								} 
								else if($value['employee_no'] == $value['admin4']){
									$admin = 'admin4';
									echo "Admin 4";
								} 
								else if($value['employee_no'] == $value['admin5']){
									$admin = 'admin5';
									echo "Admin 5";
								} 
								else if($value['employee_no'] == $value['admin6']){
									$admin = 'admin6';
									echo "Admin 6";
								} 
								else if($value['employee_no'] == $value['admin7']){
									$admin = 'admin7';
									echo "Admin 7";
								} 
								else if($value['employee_no'] == $value['admin8']){
									$admin = 'admin8';
									echo "Admin 8";
								}
//								var_dump($admin);
//								die();
							?>
						</td>
						<td style="text-align:center; width: 100px;">
							<form method="post">
								<button style="border: none;" class="table-btn" type="submit" value="<?php echo $value['employee_no'];?>" name="delete"><i class="ion-android-close"></i>
								</button>
								<input type="hidden" name="admin_no" value="<?php echo $admin; ?>"/>
							</form>
						</td>
					</tr>
				<?php 
			}
			
		?>
		
		</table>
		</div>
	</div>
</div>

<?php

  include $_themeRoot.'/footer.php';

 ?>

<script>
    console.log($("#admin_num").val())
	$(document).ready(function(){
		$("#name_surname").select2();
	});

  $(function(){

    $('form#form').validate({
      focusCleanup:true
    });

  });

 
 </script> 