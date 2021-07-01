<?php
//  _pagePermission(5, false);
 ?>

<!-- START - Main section -->
<section class="full">

  <div class="container-fluid">


    <div class="row">

      <div class="col-sm-6">
        <h2>
          <?php echo __('Moji Zahtjevi'); ?><br/><br/>
        </h2>
      </div>
      <div class="col-sm-6 text-right"><br/>
        <div class="pull-right">
          
 <a href="<?php echo $url.'/modules/'.$_mod.'/pages/popup_requests_add.php'; ?>" data-widget="ajax" data-id="opt2" data-width="500" class="btn btn-red btn-lg"><?php echo __('Novi unos'); ?> <i class="ion-ios-plus-empty"></i></a>
        </div>
      </div>

    </div>


    <?php

      $limit	= 20;

      if($_num){

        $offset = ($_num - 1) * $limit;

      }else{

        $offset = 0; $_num = 1;

      }

      $where = "WHERE user_id='".$_user['user_id']."'";
      $path = '?m='.$_mod.'&p='.$_page;

      if(isset($_GET['t'])){
        $type = $_GET['t'];
        $where .= " AND is_archive='1'";
        $path .= '&t='.$type;
      }else{
        $type = '';
        $where .= " AND is_archive='0'";
      }

      $path .= '&pg=';

      $query = $db->query("SELECT TOP ".$limit." * FROM  ".$portal_requests."  ".$where." ORDER BY date_created DESC");
      $get2 = $db->query("SELECT COUNT(*) FROM  ".$portal_requests."  ".$where." ");
      $result = $get2->fetch();
      //$total=$result[0];
$total = $get2->rowCount();



     ?>

    <a href="/app/?m=default&p=requests" class="btn btn-filter <?php if($type == ''){ echo 'active'; } ?>"><?php echo __('Aktivni'); ?></a>
    <a href="/app/?m=default&p=requests&t=3" class="btn btn-filter <?php if($type == '3'){ echo 'active'; } ?>"><?php echo __('Arhiva'); ?></a>

    <br/><br/>


  
 <div class="row">

    <div class="col-sm-3">
    
        <div class="box">

          <div class="head">
            <div class="box-head-btn">
              <a href="#" class="ion-ios-arrow-up" data-widget="collapse" data-id="c4a"></a>
            </div>
            <h3><?php echo __('Statistika G.O.'); ?></h3>
          </div>
          <div class="content" id="c4a">

    
     <table class="alt">
                 <tr>
             <h4><th>Prošla godina</th></h4>
              </tr>

              <tr>
                <td><?php echo __('Broj dana: '); ?></td>
                 <td>4</td>
              </tr>
              <tr>
                <td><?php echo __('Iskorišten broj dana: '); ?></td>
               
              </tr>
              <tr>
                <td><?php echo __('Preostali broj dana: '); ?></td>
               
              </tr>
              <tr>
             <h4><th>Tekuća godina</th></h4>
              </tr>
              <tr>
                <td><?php echo __('Broj dana: '); ?></td>
              
              </tr>
              <tr>
                <td><?php echo __('Iskorišten broj dana: '); ?></td>
              
              </tr>
              <tr>
                <td><?php echo __('Preostali broj dana: '); ?></td>
                
              </tr>
              
              <tr>
                <td><h5><?php echo __('Ukupno'); ?></h5></td>
              </tr>
            
             
            </table>
          </div>
        </div>
</div>


    <?php

      if($total<0)
	  {

        foreach($query as $item){
          $tools_id = $item['request_id'];

          $border = '';

          if($item['status']==0){
            $border = 'blue';
          }elseif($item['status']==1){
            $border = 'green';
          }elseif($item['status']==2){
            $border = 'red';
          }elseif($item['status']==3){
            $border = 'gray';
          }

          $parent = _user($item['parent_id']);


    ?>

    <div class="col-sm-9" style="text-align:left;">
    <div class="box box-lborder box-lborder-<?php echo $border; ?>" id="opt-<?php echo $tools_id; ?>" style="width:950px;" >
      <div class="content" style="display: block;">
        <div class="row">
          <div class="col-sm-5">
            <?php echo __('Zahtjev za G.O.'); ?>
            <br/>
            <?php echo __('Od:'); ?> <b><?php echo date('d/m/Y',strtotime($item['h_from'])); ?></b> &nbsp;
            <?php echo __('Do:'); ?> <b><?php echo date('d/m/Y',strtotime($item['h_to'])); ?></b>
            <?php if($item['status']==1){ ?>
              &nbsp; &nbsp; <span style="color:#009900;"><i class="ion-android-checkmark-circle"></i> <?php echo __('Odobreno'); ?></span>
            <?php }else if($item['status']==2){ ?>
              &nbsp; &nbsp; <span style="color:#990000;"><i class="ion-android-close"></i> <?php echo __('Odbijeno'); ?></span>
            <?php }else if($item['status']==0){ ?>
              &nbsp; &nbsp; <span style="color:#ffaa00;"><i class="ion-android-time"></i> <?php echo __('Na čekanju...'); ?></span>
            <?php } ?>
            <?php if($item['comment'] != ''){ ?>
              <blockquote class="comment-list">
                <b><?php echo $parent['fname'].' '.$parent['lname']; ?></b> -
                <?php echo $item['comment']; ?>
              </blockquote>
            <?php } ?>
          </div>
          <div class="col-sm-3">
            <small><?php echo __('Zahtjev kreiran:'); ?></small><br/>
            <?php echo date('d/m/Y',strtotime($item['date_created'])); ?>
          </div>
          <div class="col-sm-3">
            <?php if($item['date_response'] != '1970-01-01'){ ?>
            <small>
            <?php
              if($item['status']==1){
                echo __('Zahtjev odobren:');
              }else if($item['status']==2){
                echo __('Zahtjev odbijen:');
              }
            ?>
          </small><br/>
         <?php echo date(($item['date_response'])); ?>
            <?php }else{ echo 
			'&nbsp;'; } ?>
           
          </div>
          <div class="col-sm-1 text-right">
            <?php if($item['status']==0){ ?>
              <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_remove:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite poništiti zahtjev?'); ?>"><i class="ion-android-close"></i></a>
            <?php }else{ ?>
              <a href="<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>" class="table-btn alt" data-widget="remove" data-id="requests_archive:<?php echo $tools_id; ?>" data-text="<?php echo __('Dali ste sigurni da želite arhivirati zahtjev?'); ?>"><i class="ion-folder"></i></a>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

</div>


    <?php } }else{ ?>
      <div class="text-center">
        <?php echo __('Nema spašenih zahtjeva prema odabranim parametrima.'); ?>
      </div>
    <?php } ?>

    <div class="text-center">
      <div class="btn-group">
      <?php echo _pagination($path, $_num, $limit, $total); ?>
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
