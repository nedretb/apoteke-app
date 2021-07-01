<?php
  require_once '../../../configuration.php';
  include_once $root.'/modules/default/functions.php';


 ?>
<div class="header">
	<a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
	<h4><span><?php echo __('Pregled'); ?></span></h4>
</div>

<section>
	<div class="content clear">

    <?php
      $get = $db->query("SELECT * FROM  ".$portal_hourlyrate_day."  WHERE id='".$_GET['id']."'");
      if($get->rowCount()<0){
        $row = $get->fetch();

        $get_year  = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE id='".$row['year_id']."'");
        $get_month = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE id='".$row['month_id']."'");
        $year  = $get_year->fetch();
        $month = $get_month->fetch();
        $parent = _user($row['review_user']);

        if($row['review_status']=='0'){
          $css = '';
		  $status = 'Na čekanju';
        }elseif($row['review_status']=='1'){
          $css = 'style="color:#00cc00;"';
          $status = __('ODOBRENO');
        }elseif($row['review_status']=='2'){
          $css = 'style="color:#cc0000;"';
          $status = __('ODBIJENO');
        }
    ?>

		<div class="row">

      <div class="col-sm-6">
        <big><?php echo $row['day'].'.'.$month['month'].'.'.$year['year']; ?></big><br/>
         <?php 
		  $br_sati = $row['hour'];
		  
		  if((in_array($row['status'], array(43, 44, 45, 61, 62 ,65 ,67, 68, 69, 73, 74,75,76,77, 78, 81, 105, 107, 108, 91, 92, 93,94,95,96, 85,86,87,88,89,90)) and (($row['weekday'] == '6' or $row['weekday'] == '7')) or ($row['weekday'] != '6' and $row['weekday'] != '7') or (($row['weekday'] == '6' or $row['weekday'] == '7') and $row['hour'] > 0 and in_array($row['status'], array(5, 85, 86,87,88,89,90,91,92,93,94,95,96))))): 
		  
		  ?>
			<b><?php echo _nameHRstatus($row['status']); ?></b><br/>
		  <?php 

		  else:
		  $br_sati = 0; ?>
			<b>Redovni rad</b><br />
		  <?php endif; ?>
        <?php echo __('Broj sati'); ?> <b><?php echo $br_sati; ?></b>
		<?php
		  if($row['status_pre']!='' and $row['status_pre']!='0'){?>
		  <br/><b><?php echo _nameHRstatus($row['status_pre']); ?></b><br/>
		  <?php echo __('Broj sati'); ?> <b><?php echo $row['hour_pre']; ?></b>
		  <?php } ?>
      </div>
      <div class="col-sm-6">
        <big <?php echo $css; ?>><?php echo $status; ?></big><br/>
        <small><?php echo __('Obradio:'); ?></small><br/>
        <?php echo $parent['fname'].' '.$parent['lname']; ?>
      </div>

    </div>

    <hr/>

    <small><?php echo __('Komentar:'); ?></small><br/>
    <div class="comment-single">
      <?php echo $row['review_comment']; ?>
    </div>

    <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
		<script>
      $( document ).ready(function(){
        $('.dialog-loader').hide();
      });
		</script>

    <?php
      }else{
        echo '<div class="alert alert-danger"><b>'.__('Greška!').'</b><br/>'.__('Pogrešan ID stranice, molimo kontaktirajte administratora.').'</div>';
      }
     ?>

	</div>
  <div class="dialog-loader"><i></i></div>
</section>
