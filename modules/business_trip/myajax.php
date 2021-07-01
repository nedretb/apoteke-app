<?php

require_once '../../configuration.php';

$data = "UPDATE [c0_intranet2_apoteke].[dbo].[akontacija] SET
      data = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
        $_POST['data']
      )
    );