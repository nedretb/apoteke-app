<?php
  _pagePermission(0, true);
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-8">
        <h2>
          <?php echo __('Gradovi'); ?>
          <small><?php echo __('Lista unešenih gradova, koji su obavezni za kreiranje destinacija i rezervacija.'); ?></small>
        </h2>
      </div>
      <div class="col-sm-4 text-right"><br/>
        <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_city_add.php'; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="btn btn-red btn-lg"><?php echo __('Novi unos'); ?> <i class="ion-ios-plus-empty"></i></a>
      </div>

    </div>


    <?php

      $limit	= 20;

      if($_num){

        $offset = ($_num - 1) * $limit;

      }else{

        $offset = 0; $_num = 1;

      }

      $path = '?m='.$_mod.'&p='.$_page.'&pg=';
      $where = "";

      $query = $db->query("SELECT * FROM  ".$portal_cities."  ".$where." ORDER BY name ASC limit $offset, $limit");
      $get2 = $db->query("SELECT * FROM  ".$portal_cities."  ".$where." ORDER BY name ASC");
      $total = $get2->rowCount();

     ?>

    <div class="box">
      <div class="content">
      <table class="table table-hover">
        <?php
          if($total>0){
            $i = 0;
         ?>
        <thead>
          <tr>
            <th><?php echo __('Naziv'); ?></th>
            <th><?php echo __('Država'); ?></th>
            <th width="75"></th>
          </tr>
        </thead>
        <tbody>
          <?php
              foreach($query as $item){
                $i++;
                $tools_id = $item['city_id'];
          ?>
          <tr id="opt-<?php echo $tools_id; ?>">
            <td><?php echo $item['name']; ?></td>
            <td><?php echo _nameCountry($item['country']); ?></td>
            <td class="text-right">
              <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_city_edit.php?id='.$tools_id; ?>" data-widget="ajax" data-id="opt2" data-width="400" class="table-btn"><i class="ion-edit"></i></a>
              <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn" data-widget="remove" data-id="city:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite brisati:'); ?> <?php echo $item['name']; ?>"><i class="ion-android-close"></i></a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
        <?php }else{ echo '<tr><td colspan="3" class="text-center">'.__('Još nije bilo unosa').'</td></tr>'; } ?>
      </table>
      <div class="text-right">
        <div class="btn-group">
        <?php echo _pagination($path, $_num, $limit, $total); ?>
        </div>
      </div>
    </div>
    </div>




  </div>


</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

 ?>

</body>
</html>
