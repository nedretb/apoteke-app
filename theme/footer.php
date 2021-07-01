
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo _settings('app_name'); ?></title>
      <!-- Pace -->
      <script src="<?php echo $_pluginUrl; ?>/pace/pace.js"></script>
    <script src="theme/js/errors.js"></script>
    <style>
	* {
		font-family: Segoe UI;
	}
    	.pace {
		  -webkit-pointer-events: none;
		  pointer-events: none;

		  -webkit-user-select: none;
		  -moz-user-select: none;
		  user-select: none;
      position: absolute;
      z-index: 99999;
      background: rgba(0,0,0,0);
      width:100%;
      height:100%;
		}
		.pace-inactive {
		  display: none;
		}
		.pace .pace-progress {
            background: rgb(6,5,0);
            background: -moz-linear-gradient(left, rgb(6,5,0) 0%, rgb(33,212,253) 100%);
            background: -webkit-linear-gradient(left, rgb(6,5,0) 0%,rgb(33,212,253) 100%);
            background: linear-gradient(to right, rgb(6,5,0) 0%,rgb(33,212,253) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#006595; ', endColorstr='#fef200',GradientType=1 );
            position: fixed;
            z-index: 2000;
            top: 0;
            right: 100%;
            width: 100%;
            height: 6px;

		}
    </style>
	
	<link rel="shortcut icon" href="<?php echo $_uploadUrl; ?>/favicon.ico" />


	
	
   <link href="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.css" rel="stylesheet">

    <!-- Ion Icons -->
    <link href="<?php echo $_cssUrl; ?>/ionicons.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="<?php echo $_cssUrl; ?>/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $_cssUrl; ?>/bootstrap-theme.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="<?php echo $_pluginUrl; ?>/select2/select2.min.css" rel="stylesheet">

    <!-- Bootstrap datepicker CSS -->
    <link href="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">

    <!-- Bootstrap touchspin CSS -->
    <link href="<?php echo $_pluginUrl; ?>/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">

    <!-- icheck CSS -->
    <link href="<?php echo $_pluginUrl; ?>/icheck/skins/square/blue.css" rel="stylesheet">
	<link href="<?php echo $_pluginUrl; ?>/icheck/skins/square/green.css" rel="stylesheet">
	<link href="<?php echo $_pluginUrl; ?>/icheck/skins/square/red_razd.css" rel="stylesheet">

    <!-- Farbtastic CSS -->
    <link href="<?php echo $_pluginUrl; ?>/farbtastic/farbtastic.css" rel="stylesheet">
	
	<!-- Jquery confirm CSS -->
    <link href="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.css" rel="stylesheet">
	
	 <!-- Font Awsome CSS -->
	<link href="<?php echo $_pluginUrl; ?>/font-awsome/css/font-awesome.min.css" rel="stylesheet">

	<link href="<?php echo $host; ?>/node_modules/toastify-js/src/toastify.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="<?php echo $_cssUrl; ?>/main.css?v=1.4" rel="stylesheet">
    <style>

<!-- Main CSS -->
    <link href="<?php echo $_cssUrl; ?>/main.css?v=1.4" rel="stylesheet">
    <style>
      body{
        background:<?php echo _settings('color_bg'); ?>;
        color:<?php echo _settings('color_fg'); ?>;
      }
      header,
      header > ul li ul{
        background:<?php echo _settings('color_header_bg'); ?>;
        color:<?php echo _settings('color_header_fg'); ?>;
		
    font-size: 11px;
    font-weight: normal;
		
      }
      header > ul li a,
      header .user,
      a{
        color:<?php echo _settings('color_header_fg'); ?>;
      }
      header > ul li a:hover{color:black;background:#006595},
      header > ul li.current > a,
      nav > ul li.current a,
      header h1,
      header h1 a,
      a:hover{
        color:<?php echo _settings('color_header_act'); ?>;
      }
      h1,h2,h3,h4,h5,h6{
        color:<?php echo _settings('color_h'); ?>;
      }
      .btn.btn-red{
        background: <?php echo _settings('color_button_bg'); ?>;
        color:<?php echo _settings('color_button_fg'); ?>;
      }
      .inputfile-1 + label {
          color: <?php echo _settings('color_button_fg'); ?>;
          background-color: <?php echo _settings('color_button_bg'); ?>;
      }
      label.radio > input:checked + img,
      label.radio > input:checked + i,
      label.radio > input:checked + span{
        	border-color: <?php echo _settings('color_button_bg'); ?>;
      }
      label.radio > input:checked + span{
        color: <?php echo _settings('color_button_bg'); ?>;
      }
	  .tasks-page span{
		   background:<?php echo _settings('color_span_bg'); ?>;
        color:<?php echo _settings('color_span_fg'); ?>;
	  }
	  .tasks-page th{
		   background:<?php echo _settings('color_header_bg'); ?>;
        color:<?php echo _settings('color_header_fg'); ?>;
	  }
	  
	  #obuke th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: <?php echo _settings('color_header_bg'); ?>;
    color: <?php echo _settings('color_header_fg'); ?>;
}
	  
	
	  .active-task{
		  background:<?php echo _settings('color_active-task_bg'); ?>;
	  }
	  .tasks-page .fa-arrow-right{
		  color:<?php echo _settings('color_arrow-right_fg'); ?>;
	  }
    </style>

</head>
<body>

</body>
</html>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

<script src="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.js"></script>


   

<!-- Include all compiled plugins (below), or include individual files as needed -->

<!-- Bootstrap -->
<script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

<!-- jsCookie -->
<script src="<?php echo $_pluginUrl; ?>/jsCookie/js.cookie.js"></script>

<!-- Select2 -->
<script src="<?php echo $_pluginUrl; ?>/select2/select2.min.js"></script>

<!-- Bootstrap datepicker -->
<script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>

<!-- Bootstrap touchspin -->
<script src="<?php echo $_pluginUrl; ?>/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>

<!-- iCheck -->
<script src="<?php echo $_pluginUrl; ?>/icheck/icheck.min.js"></script>

<!-- jQuery Validation -->
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
<script src="<?php echo $_pluginUrl; ?>/jquery-cookie-master/src/jquery.cookie.js"></script>

<!-- jQuery farbtastic -->
<script src="<?php echo $_pluginUrl; ?>/farbtastic/farbtastic.js"></script>

<!-- jQuery confirm -->
<script src="<?php echo $_pluginUrl; ?>/jquery-confirm/jquery-confirm.min.js"></script>

<!-- ChartJS -->
<script src="<?php echo $_pluginUrl; ?>/Chart.js/Chart.bundle.min.js"></script>

<!-- ChartJS utils -->
<script src="<?php echo $_pluginUrl; ?>/Chart.js/utils.js"></script>
<script src="<?php echo $_pluginUrl; ?>/Chart.js/FileSaver.js"></script>
<script src="<?php echo $_pluginUrl; ?>/Chart.js/jspdf.min.js"></script>
<script src="<?php echo $_pluginUrl; ?>/Chart.js/Chart.PieceLabel.js"></script>

<!-- MAIN js file -->
<script src="<?php echo $_jsUrl; ?>/main.js"></script>
<script src="<?php echo $host; ?>/node_modules/toastify-js/src/toastify.js"></script>

<script>

$("body").addClass("bg-rf");

	loc = $(".localStorageRemoval").html();
	
	if(loc != "0"){
		var cItem = localStorage.getItem('collapseItem'); 

		if (cItem) {
			localStorage.setItem('collapseItem', []);
		}
	}
	

var numItems = $('header > ul li').length;

if(numItems>9){
	$("header > ul li a").css("font-size","1.2vh");
	$("header > ul li a").css("padding-left","1vh");
	$("header > ul li a").css("padding-right","1vh");
}
/*
Toastify({
    text: "This is a toast",
    backgroundColor: "linear-gradient(to right, #a90329, #8f0222)",
    gravity: 'bottom',
    duration: 3000
}).showToast();

*/

<?php 

	if(!empty(_settings('logout_time'))){
		$settings_logout_time = _settings('logout_time');
	} else {
		$settings_logout_time = 30;
	}
	
?>

	setTimeout(function(){
		window.location.href = '<?php echo $url; ?>/?action=logout';
	}, <?php echo ($settings_logout_time * 60000) ; ?>);



</script>






