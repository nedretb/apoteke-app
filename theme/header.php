<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/theme/css/font-awesome.css">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Apoteke Sarajevo</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

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
            background: rgba(0, 0, 0, 0);
            width: 100%;
            height: 100%;
        }

        .pace-inactive {
            display: none;
        }

        .pace .pace-progress {
            background: rgb(6, 5, 0);
            background: -moz-linear-gradient(left, rgb(6, 5, 0) 0%, rgb(33, 212, 253) 100%);
            background: -webkit-linear-gradient(left, rgb(6, 5, 0) 0%, rgb(33, 212, 253) 100%);
            background: linear-gradient(to right, rgb(6, 5, 0) 0%, rgb(33, 212, 253) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#006595; ', endColorstr='#fef200', GradientType=1);
            position: fixed;
            z-index: 2000;
            top: 0;
            right: 100%;
            width: 100%;
            height: 6px;

        }
    </style>

    <link rel="shortcut icon" href="<?php echo $_uploadUrl; ?>/favicon.ico"/>


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
    <link href="<?php echo $_cssUrl; ?>/profile.css" rel="stylesheet">

    <style>
        body {
            background: <?php echo _settings('color_bg'); ?>;
            color: <?php echo _settings('color_fg'); ?>;
        }

        header,
        header > ul li ul {
            background: <?php echo _settings('color_header_bg'); ?>;
            color: <?php echo _settings('color_header_fg'); ?>;

            font-size: 11px;
            font-weight: normal;

        }

        header > ul li a,
        header .user,
        a {
            color: <?php echo _settings('color_header_fg'); ?>;
        }

        header > ul li a:hover {
            color: black;
            background: #006595
        }

        ,
        header > ul li.current > a,
        nav > ul li.current a,
        header h1,
        header h1 a,
        a:hover {
            color: <?php echo _settings('color_header_act'); ?>;
        }

        h1, h2, h3, h4, h5, h6 {
            color: <?php echo _settings('color_h'); ?>;
        }

        .btn.btn-red {
            background: <?php echo _settings('color_button_bg'); ?>;
            color: <?php echo _settings('color_button_fg'); ?>;
        }

        .inputfile-1 + label {
            color: <?php echo _settings('color_button_fg'); ?>;
            background-color: <?php echo _settings('color_button_bg'); ?>;
        }

        label.radio > input:checked + img,
        label.radio > input:checked + i,
        label.radio > input:checked + span {
            border-color: <?php echo _settings('color_button_bg'); ?>;
        }

        label.radio > input:checked + span {
            color: <?php echo _settings('color_button_bg'); ?>;
        }

        .tasks-page span {
            background: <?php echo _settings('color_span_bg'); ?>;
            color: <?php echo _settings('color_span_fg'); ?>;
        }

        .tasks-page th {
            background: <?php echo _settings('color_header_bg'); ?>;
            color: <?php echo _settings('color_header_fg'); ?>;
        }

        #obuke th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: <?php echo _settings('color_header_bg'); ?>;
            color: <?php echo _settings('color_header_fg'); ?>;
        }


        .active-task {
            background: <?php echo _settings('color_active-task_bg'); ?>;
        }

        .tasks-page .fa-arrow-right {
            color: <?php echo _settings('color_arrow-right_fg'); ?>;
        }
    </style>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>

    <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

    <script src="<?php echo $_pluginUrl; ?>/jqueryui/jquery-ui.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo $_jsUrl; ?>/bootstrap.min.js"></script>

    <!-- jsCookie -->
    <script src="<?php echo $_pluginUrl; ?>/jsCookie/js.cookie.js"></script>

    <!-- Select2 -->
    <script src="<?php echo $_pluginUrl; ?>/select2/select2.min.js"></script>

    <!-- Bootstrap datepicker -->
    <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $_pluginUrl; ?>/bootstrap-datepicker/locales/bootstrap-datepicker.bs.min.js"></script>

    <script src="<?php echo $_pluginUrl; ?>/app.js"></script>
</head>
<body>


<div class="bar">
    <div class="container">
        <div class="row">


        </div>

    </div>
</div>


<nav class="navbar navbar-expand-mds navbar-light navbar-laravel"
     style="border-top: solid 1px rgba(211, 211, 211, 0.44); background-color:#006595;">
    <div class="container">

        <button style=" background-color:#006595;" class="navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarSupportedContent">


            <!-- Left Side Of Navbar -->
            <div class="navbar-nav mr-auto" style="background-color:#006595;">

                <?php echo pageMenu(); ?>

            </div>

            <ul class="navbar-nav pull-right">
                <li style="cursor: pointer;background-color:#006595; color:white;">
                    <?php echo $_user['fname']; ?>
                    <?php echo $_user['lname']; ?>
                </li>
                <a href="?action=logout">
                    <li style=" background-color:#006595; border: 0px solid white !important;" class="text-center">

                        <i class="fa fa-arrow-circle-right" style="color:white;"></i>
                        <small style="color:white;">Odjava</small>
                    </li>
                </a>

            </ul>


        </div>
    </div>
</nav>


<!-- START - Topbar header -->
<div class="container">