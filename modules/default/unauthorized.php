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
  <body class="bg-login">

  <div class="container login">

    <div class="row">
      <div class="col-sm-6 col-md-4 col-md-offset-4 col-sm-offset-3 unauthorized">

        <i class="ion-android-warning big"></i>
        <h1><?php echo __('Neovlašten pristup'); ?></h1>
        <?php echo __('Nemate ovlasti za pristup ovom dijelu aplikacije. Ukoliko je doslo do greške, kontaktirajte administratora.'); ?>
        <br/><br/>
        <a class="btn btn-red" href="/"><?php echo __('Nazad na moj profil'); ?><i class="ion-person"></i></a>

      </div>
    </div>

  </div>

</body>
</html>
