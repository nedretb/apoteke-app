<?php
$root = "C:\wamp64\www\mkt-app"; // TODO - Root file need to be set

include_once $root . "/configuration.php";
include_once $root . '/CORE/classes/MainRequest.php'; // Class for handling requests
include_once $root . '/CORE/classes/Model.php';       // Need to be extended

if(isset($_POST) and count($_POST)) $request = MainRequest::set($_POST);
