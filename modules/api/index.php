<?php

error_reporting(1);

//include $root . '/classes/API/SoapEvents.php';

if (isset($_page)) {
    include $root . '/modules/' . $_mod . '/pages/' . $_page . '.php';
} else {
    $_page = $_defaultPage;
    include $root . '/modules/' . $_mod . '/pages/' . $_page . '.php';
}

?>
