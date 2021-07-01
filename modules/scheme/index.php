<?php

error_reporting(1);

/*
 *  Include all files from folder - Such as Models, etc
 */
// foreach (glob($root . '/modules/' . $_mod . '/pages/classes/*.php') as $filename) require_once $filename;

if (isset($_page)) {
    include $root . '/modules/' . $_mod . '/pages/' . $_page . '.php';
} else {
    $_page = $_defaultPage;
    include $root . '/modules/' . $_mod . '/pages/' . $_page . '.php';
}

?>
