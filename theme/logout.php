<?php
if (isset($_POST['return'])) {
    $return = urldecode('http://192.168.14.99:81/app/index.php'.$_POST['return']);
        header("Location: $return");
        exit;   
}
?>