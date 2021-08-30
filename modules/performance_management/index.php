<?php


  if(isset($_page)){
   try{
     $pm_admin = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_administratori] where user_id = ".$_user['user_id'])->fetchAll();
     if(count($pm_admin)) $pm_admin = true;
     else $pm_admin = false;
   }catch (\PDOException $e){ $pm_admin = false; }

    include $root.'/modules/'.$_mod.'/pages/'.$_page.'.php';

  }else{

    $_page = $_defaultPage;
    include $root.'/modules/'.$_mod.'/pages/'.$_page.'.php';

  }

 ?>
