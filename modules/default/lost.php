<?php

require_once '../../configuration.php';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo _settings('app_name'); ?></title>

    <!-- Google webfonts - Tititllium -->
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300,700' rel='stylesheet' type='text/css'>

    <!-- Ion Icons -->
    <link href="<?php echo $_cssUrl; ?>/ionicons.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="<?php echo $_cssUrl; ?>/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $_cssUrl; ?>/bootstrap-theme.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="<?php echo $_cssUrl; ?>/main.css" rel="stylesheet">
    <style>
      body,
      .bg-login{
        background:<?php echo _settings('color_bg'); ?>;
        color:<?php echo _settings('color_fg'); ?>;
      }
      button.login-btn{
        background: <?php echo _settings('color_button_bg'); ?>;
        color:<?php echo _settings('color_button_fg'); ?>;
      }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="bg-login" style="background-color:#ffffff;">

  <div class="container login">

    <div class="row">
      <div class="col-sm-6 col-md-4 col-md-offset-4 col-sm-offset-3">

        <div style="height:100px;"></div>
         <div style="width:600px;" >
          <?php if(_settings('logo')=='none'){ ?>
          <?php echo _settings('app_name'); ?>
          <?php }else{ ?>
          <img src="<?php echo $_uploadUrl; ?>/<?php echo _settings('logo'); ?>"></a>
          <?php } ?>
    		</div>
        <p>&nbsp;</p>

        <br/>

    		<form method="post" id="login" action="">
    			<input type="hidden" name="lost" value="1"/>

          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon alt"><i class="ion-ios-email-outline"></i></div>
              <input type="email" name="email" class="form-control alt" id="usr" autocomplete="off" placeholder="<?php echo __('Vaša e-mail adresa'); ?>" required>
            </div>
          </div>

    			<button type="submit" class="login-btn"><?php echo __('Poništi lozinku'); ?></button><br/>

          <p class="text-center">
            <a href="<?php echo $url."/modules/default/login.php"; ?>">&larr; <?php echo __('Nazad na stranicu za prijavu'); ?></a>
          </p>


          <?php

            if(isset($_POST['lost'])){

              $generated = generate_user(9,0);
              $pass = md5($generated);

              $check = $db->query("SELECT * FROM  ".$portal_users."  WHERE email='".$_POST['email']."'");
              if($check->rowCount()<0){
                $row = $check->fetch();
                if($_POST['email'] == $row['email']){
                  $user = $row['user_id'];
                }else{
                  $user = false;
                }
              }

              if($user != false){

                $data = "UPDATE  ".$portal_users."  SET
                  password = ?
                  WHERE user_id = ?";

                $res = $db->prepare($data);
                $res->execute(
                  array(
                    $pass,
                    $user
                  )
                );
                if($res->rowCount()==1) {

                  $msg = array(
            				'to' => $_POST['email'],
                    'reply' => _settings('smtp_email'),
            				'subject' => 'Zahtjev za reset pristupnih podataka ',
            				'message' => "
            						<html>
            							<head>
            								<title></title>
            								<style type='text/css'>
            									body { font-size : 11px; font-family: Verdana, Arial, Helvetica, sans-serif; padding:0; margin:0; }
            									a {  color: #197580; text-decoration: none; }
            									a:hover {  color:#8dd04f; text-decoration: underline; }
            									h2 {margin10px 0px 0px 0px;}
            									table{ border-collapse:collapse;border-spacing:0; }
            									img, img a{ outline:none; border:none; }
            								</style><meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
            							</head>
            							<body bgcolor='#ebeef3'>
            								<div style='font-family:Arial, Helvetica, sans-serif;font-size:13px;margin:0;padding:30px;'>
            									".__('Poštovani,')." ".__('Zatražili ste reset lozinke, nova je u prilogu.')."<br/><br/>
                              ".__('Lozinka').": <b>".$generated."</b>
            								</div>
                            <div style='font-size:11px;margin:0;padding:30px;text-align:center;'>
                              &copy; ".date('Y')." - All rights reserved.
                            </div>
            							</body>
            						</html>"
            			);
            			if(__mailer($msg)){
                    echo '<div class="alert alert-success">';
                    echo __('E-mail sa uputama je poslan molimo provjerite poruke na email-u koji ste naveli u zahtjevu za reset lozinke.');
                    echo '</div>';
            			}

                }

              }else{

                echo '<div class="alert alert-danger">';
                echo __('Unesena e-ail adresa nije pronađena u bazi.');
                echo '</div>';

              }

            }

           ?>

    		</form>

      </div>
    </div>

  </div>

</body>
</html>
