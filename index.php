<?php
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'configuration.php';
require_once 'CORE/lang.php';


// Include admin menu
include_once $root . '/CORE/classes/MainRequest.php'; // Class for handling requests
include_once $root . '/CORE/menu.php';
include_once $root . '/CORE/classes/Model.php';       // Need to be extended
include_once $root . '/CORE/classes/Form.php';        // Form class - dynamic creating form objects - HTML
include_once $root . '/classes/API/SoapEvents.php';

foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;

if(isset($_POST) and count($_POST)) $request = MainRequest::set($_POST);

use DataBase as DB;

include 'error.handler.php';

error_reporting(E_ALL);


if (!isset($_SESSION['SESSION_USER']) || (trim($_SESSION['SESSION_USER']) == '')) {

    header('Location: ' . $url . '/modules/default/login.php');

} else {

    $session = $_SESSION['SESSION_USER'];

    $_defaultMod = 'default';
    $_defaultPage = 'profile';


    if ($action == 'logout') {

        unset($_SESSION['SESSION_USER']);
        header('Location: ' . $url . '/modules/default/login.php?action=loggedout');

    } else {

        $_user = _user(_decrypt($session));

        $_SESSION['lan'] = str_replace(' ', '', $_user['egop_jezik']); // Set session for language

        include_once $_themeRoot . '/header.php';

        $_SESSION['cc_admin'] = 1;
        if (isset($_mod)) {

            include_once $root . '/modules/' . $_mod . '/index.php';

        } else {

            $_mod = $_defaultMod;
            include_once $root . '/modules/' . $_mod . '/index.php';

        }

    }

}


?>
