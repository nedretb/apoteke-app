<?php

  require_once $root.'/modules/'.$_mod.'/functions.php';

  if(isset($_page)){

    include $root.'/modules/'.$_mod.'/pages/'.$_page.'.php';

  }else{

    $_page = $_defaultPage;
    include $root.'/modules/'.$_mod.'/pages/'.$_page.'.php';

  }

 ?>
