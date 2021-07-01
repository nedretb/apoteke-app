<?php
  // _pagePermission(0, true);
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">

    <form action="" method="post" id="form" enctype="multipart/form-data">
    <input type="hidden" name="request" value="settings">
    <input type="hidden" name="old_photo" value="<?php echo _settings('logo'); ?>">

    <div class="row">

      <div class="col-sm-8">
        <h2>
          <?php echo __('Podešavanja'); ?><br/><br/>
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>
        <button type="submit" class="btn btn-red btn-lg">Spasi! <i class="ion-ios-download-outline"></i></button>
      </div>

    </div>

    <?php

      $msg = 0;
      $uid = _decrypt($_SESSION['SESSION_USER']);

      if(isset($_POST['request'])){

        if($_POST['request']=='settings'){

          if(is_uploaded_file($_FILES['media_file']['tmp_name'])){
            $p_photo   = preg_replace('/[^\w\._]+/', '_', $_FILES['media_file']['name']);
    			  $p_photo = _checkFile($_uploadRoot.'/', $p_photo);
    			  $file = $_uploadRoot.'/'.$p_photo;
            if($_POST['old_photo'] != 'none'){
              $remove_img = unlink($_uploadRoot.'/'.$_POST['old_photo']);
			 
            }else{
              $remove_img = 'ok';
            }
            if($remove_img){
      			  copy($_FILES['media_file']['tmp_name'], $file);
            }
    			}else{
            if($_POST['old_photo'] != 'none'){
              $p_photo = $_POST['old_photo'];
            }else{
              $p_photo = 'none';
            }
    			}

          if(isset($_POST['smtp_status'])){
            $smtp_status = 1;
          }else{
            $smtp_status = 0;
          }

          $arr = array(
            'app_name'=>$_POST['app_name'],
            'color_header_fg'=>$_POST['color_header_fg'],
            'color_header_bg'=>$_POST['color_header_bg'],
            'color_header_act'=>$_POST['color_header_act'],
            'color_bg'=>$_POST['color_bg'],
            'color_fg'=>$_POST['color_fg'],
            'color_h'=>$_POST['color_h'],
            'color_button_bg'=>$_POST['color_button_bg'],
            'color_button_fg'=>$_POST['color_button_fg'],
			'color_active-task_bg'=>$_POST['color_active-task_bg'],
			'color_arrow-right_fg'=>$_POST['color_arrow-right_fg'],
			'color_span_bg'=>$_POST['color_span_bg'],
			'color_span_fg'=>$_POST['color_span_fg'],
            'logo'=>$p_photo,
            'smtp_status'=>$smtp_status,
            'smtp_host'=>$_POST['smtp_host'],
            'smtp_user'=>$_POST['smtp_user'],
            'smtp_password'=>$_POST['smtp_password'],
            'smtp_port'=>$_POST['smtp_port'],
            'smtp_security'=>$_POST['smtp_security'],
            'smtp_email'=>$_POST['smtp_email']
          );
          $upd = "UPDATE  ".$portal_settings."  SET
            value = ?
            WHERE name = ?";
          $ins = "INSERT INTO  ".$portal_settings." 
            (name, value)
            VALUES (?,?)";
          foreach($arr as $name=>$value){

            $check = $db->query("SELECT * FROM  ".$portal_settings."  WHERE name='$name'");
        		if($check->rowCount()<0){
              $res = $db->prepare($upd);
              $res->execute(array($value, $name));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }else{
              $res = $db->prepare($ins);
              $res->execute(array($name,$value));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }
          }
		  
		
				$query = $db->query("SELECT * FROM  ".$portal_tabs." ");
				$arr3 = array();
				$arr4 = array();
				$arr5 = array();
					
					$tab = 'tab_';
					
					foreach($query as $key=>$item){
					$variable = $tab.$item['Tab'];
					
					if(isset($_POST['tab_'.$item['Tab']]))
					$$variable=1;else $$variable = 0;
					
					$arr3[$item['Tab']] =$$variable;
					$arr4[$item['Tab']] =$_POST['name_'.$item['Tab']];
					$arr5[$item['Tab']] =$_POST['role_'.$item['Tab']];
					
					}
	

          $upd = "UPDATE  ".$portal_tabs."  SET
            Hidden = ?
            WHERE Tab = ?";
          $ins = "INSERT INTO  ".$portal_tabs." 
            (Tab, Hidden)
            VALUES (?,?)";
          foreach($arr3 as $Tab=>$Hidden){

            $check = $db->query("SELECT * FROM  ".$portal_tabs."  WHERE Tab='$Tab'");
        		if($check->rowCount()<0){
              $res = $db->prepare($upd);
              $res->execute(array($Hidden, $Tab));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }else{
              $res = $db->prepare($ins);
              $res->execute(array($Tab,$Hidden));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }
          }
		  
		    $upd = "UPDATE  ".$portal_tabs."  SET
            Name = ?
            WHERE Tab = ?";
          $ins = "INSERT INTO  ".$portal_tabs." 
            (Tab, Name)
            VALUES (?,?)";
			foreach($arr4 as $Tab=>$Name){

            $check = $db->query("SELECT * FROM  ".$portal_tabs."  WHERE Tab='$Tab'");
        		if($check->rowCount()<0){
              $res = $db->prepare($upd);
              $res->execute(array($Name, $Tab));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }else{
              $res = $db->prepare($ins);
              $res->execute(array($Tab,$Name));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }
          }
		  
		   $upd = "UPDATE  ".$portal_tabs."  SET
            Roles = ?
            WHERE Tab = ?";
          $ins = "INSERT INTO  ".$portal_tabs." 
            (Tab, Roles)
            VALUES (?,?)";
			foreach($arr5 as $Tab=>$Roles){

            $check = $db->query("SELECT * FROM  ".$portal_tabs."  WHERE Tab='$Tab'");
        		if($check->rowCount()<0){
              $res = $db->prepare($upd);
              $res->execute(array($Roles, $Tab));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }else{
              $res = $db->prepare($ins);
              $res->execute(array($Tab,$Roles));
              if($res->rowCount()==1){
                $msg = 1;
              }
            }
          }

        }

      }

      if($msg==1){
        echo '<div class="row"><div class="col-sm-12"><div class="alert alert-success text-center">'.__('Informacije su uspješno sapšene!').'</div></div></div>';
      }

    ?>


    <div class="row">

      <div class="col-sm-4">

        <div class="box" id="c1">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c1a"></a>
						</div>
						<h3><?php echo __('Općenito'); ?></h3>
					</div>
					<div class="content" id="c1a" style="display: block;">

            <label><?php echo __('Naslov aplikacije'); ?></label>
            <input type="text" name="app_name" class="form-control" value="<?php echo _settings('app_name'); ?>" required><br/>

            <label>Logo aplikacije</label>
            <?php if(_settings('logo') != 'none'){ ?>
              <div class="preview-logo"><img src="<?php echo $_uploadUrl; ?>/<?php echo _settings('logo'); ?>"></div>
            <?php } ?>
            <input type="file" name="media_file" id="file-1" class="inputfile inputfile-1 big" accept="image/*" data-multiple-caption="{count} files selected" required/>
            <label for="file-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/>
              </svg>
              <span><?php echo __('Odaberi'); ?>&hellip;</span>
            </label>
            <small><?php echo __('Dozvoljeni formati JPG/PNG/GIF. Preporućene dimenzije 400x200 px.'); ?></small>

					</div>
				</div>

        <div class="box" id="c2">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-down" data-widget="collapse" data-id="c2a"></a>
						</div>
						<h3><?php echo __('Boje aplikacije'); ?></h3>
					</div>
					<div class="content collapsed" id="c2a">

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Pozadina aplikacije'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_bg" id='color1' class="form-control form-color" value="<?php echo _settings('color_bg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Boja teksta aplikacije'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_fg" id='color1' class="form-control form-color" value="<?php echo _settings('color_fg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Pozadina gornje trake - HEADER'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_header_bg" id='color2' class="form-control form-color" value="<?php echo _settings('color_header_bg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Boja teksta gornje trake - HEADER'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_header_fg" id='color3' class="form-control form-color" value="<?php echo _settings('color_header_fg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Boja teksta gornje trake - HEADER (:hover :active)'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_header_act" id='color4' class="form-control form-color" value="<?php echo _settings('color_header_act', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Boja naslova (H1,H2,H3,H4,H5,H6)'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_h" id='color5' class="form-control form-color" value="<?php echo _settings('color_h', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Pozadina - BUTTONS'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_button_bg" id='color6' class="form-control form-color" value="<?php echo _settings('color_button_bg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Tekst i ikona - BUTTONS'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_button_fg" id='color7' class="form-control form-color" value="<?php echo _settings('color_button_fg', '#cccccc'); ?>"/><br/>
              </div>
            </div>
			
			 <div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Aktivni Step'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_active-task_bg" id='color8' class="form-control form-color" value="<?php echo _settings('color_active-task_bg', '#cccccc'); ?>"/><br/>
              </div>
            </div>
			
			<div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Strelica'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_arrow-right_fg" id='color9' class="form-control form-color" value="<?php echo _settings('color_arrow-right_fg', '#cccccc'); ?>"/><br/>
              </div>
            </div>
			
			<div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Span pozadina'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_span_bg" id='color10' class="form-control form-color" value="<?php echo _settings('color_span_bg', '#cccccc'); ?>"/><br/>
              </div>
            </div>
			
			<div class="row">
              <div class="col-sm-5">
                <label><?php echo __('Span text'); ?></label>
              </div>
              <div class="col-sm-7">
                <input type="text" name="color_span_fg" id='color11' class="form-control form-color" value="<?php echo _settings('color_span_fg', '#cccccc'); ?>"/><br/>
              </div>
            </div>

            <div id="picker"></div>

					</div>
				</div>
				
				 <div class="box" id="c23">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-down" data-widget="collapse" data-id="c23a"></a>
						</div>
						<h3><?php echo __('Skriveni Tabovi'); ?></h3>
					</div>
					<div class="content collapsed" id="c23a">

            	<?php
				$query = $db->query("SELECT * FROM  ".$portal_tabs." ");
				
					foreach($query as $key=>$item){
					echo _optionHiddenTab($item['Tab'],$item['Hidden']);
				  }
					?>	



					</div>
				</div>
				
				 <div class="box" id="c24">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-down" data-widget="collapse" data-id="c24a"></a>
						</div>
						<h3><?php echo __('Nazivi tabova'); ?></h3>
					</div>
					<div class="content collapsed" id="c24a">

            	<?php
				$query = $db->query("SELECT * FROM  ".$portal_tabs." ");
				
					foreach($query as $key=>$item){
					echo _optionNameTab($item['Tab'],$item['Name']);
				  }
					?>	



					</div>
				</div>
				
				 <div class="box" id="c25">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-down" data-widget="collapse" data-id="c25a"></a>
						</div>
						<h3><?php echo __('Role'); ?></h3>
					</div>
					<div class="content collapsed" id="c25a">

            	<?php
				$query = $db->query("SELECT * FROM  ".$portal_tabs." ");
				
					foreach($query as $key=>$item){
					echo _optionRoleTab($item['Tab'],$item['Roles']);
				  }
					?>	



					</div>
				</div>

      </div>

      <div class="col-sm-4">

        <div class="box" id="c3">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c3a"></a>
						</div>
						<h3><?php echo __('SMTP parametri'); ?></h3>
					</div>
					<div class="content" id="c3a" style="display: block;">

            <label><?php echo __('SMTP status'); ?></label><br/>
            <small><?php echo __('Ukoliko je SMTP neaktivan ili su parametri nepotupni, sistem ce koristiti standardnu PHP MAIL() funkciju.'); ?></small><br/>
            <label class="checkbox">
							<input type="checkbox" name="smtp_status" value="1" <?php echo _selected('checkbox','smtp_status'); ?>>
							<i></i>
						</label>

            <hr/>

            <label><?php echo __('E-mail'); ?></label>
            <input type="email" name="smtp_email" class="form-control" value="<?php echo _settings('smtp_email'); ?>"><br/>

            <label><?php echo __('SMTP host'); ?></label>
            <input type="text" name="smtp_host" class="form-control" value="<?php echo _settings('smtp_host'); ?>"><br/>

            <label><?php echo __('SMTP korisnik'); ?></label>
            <input type="text" name="smtp_user" class="form-control" value="<?php echo _settings('smtp_user'); ?>"><br/>

            <label><?php echo __('SMTP lozinka'); ?></label>
            <input type="text" name="smtp_password" class="form-control" value="<?php echo _settings('smtp_password'); ?>"><br/>

            <div class="row">

              <div class="col-sm-6">
                <label><?php echo __('SMTP port'); ?></label>
                <input type="text" name="smtp_port" class="form-control" value="<?php echo _settings('smtp_port'); ?>"><br/>
              </div>
              <div class="col-sm-6">
                <label><?php echo __('SMTP sigurnost'); ?></label>
                <input type="text" name="smtp_security" class="form-control" value="<?php echo _settings('smtp_security'); ?>"><br/>
              </div>
            </div>

					</div>
				</div>

      </div>

      <div class="col-sm-4">

        <div class="box" id="c4">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c4a"></a>
						</div>
						<h3><?php echo __('Jezici aplikacije'); ?></h3>
					</div>
					<div class="content" id="c4a" style="display: block;">

            <?php

              $query = $db->query("SELECT * FROM  ".$portal_languages."  ORDER BY lang_name ASC");
			  $querycount = $db->query("SELECT count(*) FROM  ".$portal_languages." ");
              $total = $querycount->rowCount();

             ?>
             <table class="table table-hover">
       				<thead>
       					<tr>
       						<th><?php echo __('Naziv'); ?></th>
         					<th><?php echo __('Kod'); ?></th>
       						<th width="75"></th>
       					</tr>
       				</thead>
       				<tbody>
                 <?php
                   if($total<0){
                     $i = 0;
                     foreach($query as $item){
                       $i++;
                       $tools_id = $item['lang_id'];
                 ?>
       					<tr id="opt-<?php echo $tools_id; ?>">
       						<td><?php echo $item['lang_name']; ?></td>
         					<td><?php echo $item['lang_code']; ?></td>
       						<td class="text-right">
                     <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn" data-widget="remove" data-id="lang:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite brisati:'); ?> <?php echo $item['lang_name']; ?>"><i class="ion-android-close"></i></a>
                   </td>
       					</tr>
                 <?php } } ?>
       				</tbody>
       			</table>

            <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_lang_add.php'; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="btn btn-light pull-right"><?php echo __('Novi unos'); ?> <i class="ion-ios-plus-empty"></i></a>

					</div>
				</div>

        <div class="box" id="c4">
					<div class="head">
						<div class="box-head-btn">
							<a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c4a"></a>
						</div>
						<h3><?php echo __('Statusi satnice'); ?></h3>
					</div>
					<div class="content" id="c4a" style="display: block;">

            <?php

              $query = $db->query("SELECT * FROM  ".$portal_hourlyrate_status."  ORDER BY id ASC");
			  $querycount = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_status." ");
              $total = $querycount->rowCount();

             ?>
             <table class="table table-hover">
       				<thead>
       					<tr>
       						<th><?php echo __('Naziv'); ?></th>
       						<th width="100"></th>
       					</tr>
       				</thead>
       				<tbody>
                 <?php
                   if($total<0){
                     $i = 0;
                     foreach($query as $item){
                       $i++;
                       $tools_id = $item['id'];
                 ?>
       					<tr id="opt-st-<?php echo $tools_id; ?>">
       						<td><?php echo $item['name']; ?></td>
							<td><?php echo $item['description']; ?></td>
       						<td class="text-right">
                     <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_status_edit.php?id='.$tools_id; ?>" class="table-btn" data-widget="ajax" data-id="opt2" data-width="400"><i class="ion-edit"></i></a>
                     <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn" data-widget="remove" data-id="hourlyrate_status:st-<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite brisati?'); ?>"><i class="ion-android-close"></i></a>
                   </td>
       					</tr>
                 <?php } } ?>
       				</tbody>
       			</table>

            <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_status_add.php'; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="btn btn-light pull-right"><?php echo __('Novi unos'); ?> <i class="ion-ios-plus-empty"></i></a>

					</div>
				</div>

      </div>

    </div>

    </form>


  </div>


</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

<script>
  $(function(){
    var f = $.farbtastic('#picker');
    $('.form-color').each(function () {
      $('#picker').insertAfter(this).slideDown();
      f.linkTo(this);
    }).focus(function() {
      $('#picker').insertAfter(this).slideDown();
      f.linkTo(this);
    });
    $('#picker').hide();
    $('.form-color').focusout(function() {
      $('#picker').hide();
    });
  });
</script>

</body>
</html>
