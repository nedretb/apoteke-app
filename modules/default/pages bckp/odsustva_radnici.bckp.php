<?php
  _pagePermission(5, false);
  
  date_default_timezone_set('Europe/Sarajevo');
	$data = "UPDATE  ".$portal_absence_misc."  SET
      absence_view = ?
	  WHERE employee_no = ?";

    $res = $db->prepare($data);
    $res->execute(
      array(
		date('Y-m-d h:i:s'),
		$_user['employee_no']
      )
    );

  $get_limit = $db->query("SELECT * FROM  ".$portal_pagination."  WHERE Page = 'odsustva_radnici'");
	   $get_limit1 = $get_limit->fetch();
	  
     
  $limit	= $get_limit1['Limit'];

      if($_num){

        $offset = ($_num - 1) * $limit;

      }else{

        $offset = 0; $_num = 1;

      }
  $path = '?m='.$_mod.'&p='.$_page;
  $path .= '&pg=';
  
    if($_user['role']==4){
	 $get2 = $db->query("SELECT count(*) FROM  ".$portal_users."  WHERE ".$_user['employee_no']." in (admin1,admin2,admin3,admin4,admin5)");
	   $result = $get2->fetch();
      $total_users=$result[0];
	  }
	elseif($_user['role']==2){
	 $get2 = $db->query("SELECT count(*) FROM  ".$portal_users."  WHERE (parent='".$_user['employee_no']."')");
	   $result = $get2->fetch();
      $total_users=$result[0];
	}
  
   if(isset($_POST['dateFrom']))
	 $godina = date("Y", strtotime(str_replace("/","-",$_POST['dateFrom'])));
	 else
	$godina=date("Y");

	 
  
  $mjesec=date("n");

  $number_of_days = cal_days_in_month(CAL_GREGORIAN, $mjesec, $godina);
  
  $get_year  = $db->query("SELECT * FROM  ".$portal_hourlyrate_year."  WHERE year='".$godina."' AND user_id = ".$_user['user_id']);
  $get_month  = $db->query("SELECT * FROM  ".$portal_hourlyrate_month."  WHERE id = ".$mjesec);


  $get_week = $db->query("SELECT [Weekday] FROM  ".$portal_calendar."  WHERE Month='".$mjesec."'");
  
  $get_y  = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_year."  WHERE id='".$godina."'");
  $get_m = $db->query("SELECT count(*) FROM  ".$portal_hourlyrate_month."  WHERE id='".$mjesec."'");
  
  $result = $get_y->fetch();
  $total=$result[0];
  $result2 = $get_m->fetch();
  $total2=$result2[0];
  if($total>0 || $total2>0){
 
    $year  = $get_year->fetch();
	$month  = $get_month->fetch();
	
	$naslov = 'Pregled odsustava radnika';
	
	 if(isset($_POST['dateFrom'])){
				 $month_from = date("n", strtotime(str_replace("/","-",$_POST['dateFrom'])));
				 $datumOD = date('d/m/Y',strtotime(str_replace("/","-",$_POST['dateFrom'])));
				 }
			 else{
				 $month_from = $month['month'];
				 $datumOD = date('d/m/Y',strtotime("01 January ".$godina));
				}
				
				if(isset($_POST['dateTo'])){
				$month_to = date("n", strtotime(str_replace("/","-",$_POST['dateTo'])));
				$datumDO = date('d/m/Y',strtotime(str_replace("/","-",$_POST['dateTo'])));
				}
			else{
				$month_to = $month['month'];
				 $datumDO = date('d/m/Y',strtotime("31 December ".$godina));
				}
	
				
				if(isset($_POST['employee_no']))
				$employee_no = $_POST['employee_no'];
			else
				$employee_no='';
			
			if(isset($_GET['per_broj'])){
				$per_broj= $_GET['per_broj'];
				$filter_praznici =true;
				//kontrola intervala
			$day_from=1;
			$day_to = 31;
			$month_from = 1;
			$month_to = 12;
				}
			else
				$per_broj='';
			
			if(isset($_GET['neodobreno']) and !isset($_POST['dateFrom']) ){
			$filter_praznici =true;
			$day_from=1;
			$day_to = 31;
			$month_from = 1;
			$month_to = 12;
			 $datumOD = date('d/m/Y',strtotime("01 January ".$godina));
			 $datumDO = date('d/m/Y',strtotime("31 December ".$godina));
			
				}
				if(isset($_GET['zahtjevi'])){
			$day_from=1;
			$day_to = 31;
			$month_from = 1;
			$month_to = 12;
				}
			
			
			
				if(isset($_POST['vrsta']))
				$vrsta = $_POST['vrsta'];
			else
				$vrsta='';
			
			if(isset($_POST['grupa']))
				$grupa = $_POST['grupa'];
			else
				$grupa='';
			
				if(isset($_POST['ime_prezime']))
				$ime_prezime = $_POST['ime_prezime'];
			else
				$ime_prezime='';
			
			if(isset($_GET['neodobreno'])){
				$filter_neodobreno = true;
				$naslov = 'Nova odsustva';
				}
			else
				$filter_neodobreno=false;
			
			if(isset($_POST['filter_neodobreno']))
				$filter_neodobreno = true;
			elseif(!isset($_GET['neodobreno']))
				$filter_neodobreno=false;
			
			if(isset($_POST['filter_praznici']))
				$filter_praznici = true;
			elseif(!isset($_GET['per_broj']) and !isset($_GET['neodobreno']))
				$filter_praznici=false;
			
			if(isset($_GET['zahtjevi'])){
				$filter_zahtjevi = true;
				$naslov = 'Zahtjevi otkazivanje';
				}
			else
				$filter_zahtjevi=false;
			
			if(isset($_POST['filter_zahtjevi']))
				$filter_zahtjevi = true;
			elseif(!isset($_GET['zahtjevi']))
				$filter_zahtjevi=false;
				
				if(isset($_POST['filter_doc']))
				$filter_dokument = true;
			else
				$filter_dokument=false;
			
			if(!isset($filter_praznici)){ $filter_praznici = false;}
	 
	 
	 if(isset($_POST['dateFrom'])){
	 $day_from = date("j", strtotime(str_replace("/","-",$_POST['dateFrom'])));
	 }
	 else{
		$day_from = 1; 
		}
     
	 if(isset($_POST['dateTo'])){
	 $day_to = date("j", strtotime(str_replace("/","-",$_POST['dateTo'])));
	 }
	 else{
		$day_to = $number_of_days;  
	}
	
 
 ?>
<style>
	#vrsta, #grupa {
		height: 35px;
	}
	.select2-container .select2-selection--single {
	    box-sizing: border-box;
	    cursor: pointer;
	    display: block;
	    height: 35px;
	    border-bottom: solid 1px grey;
	    user-select: none;
	    -webkit-user-select: none;
	    outline: red !important;
	}

</style>
<!-- START - Main section -->
<section class="full">

  <div class="container" style="width:100%;">

	<div class="row">

      <div class="col-sm-6" style="margin-top: 10px;">
        <h2 style="">
		<?php echo $naslov; ?>
        </h2>
       
      </div>
      <div class="col-sm-12"><br/>
        <div class="pull-right">
	
        </div>
      </div>
	</div>
	
	<div class="row">

			    <form id="popup_form1" method="post">

	  <input type="hidden" name="get_month" value="<?php  echo $mjesec;?>"/>
      <input type="hidden" name="get_year" value="<?php  echo $godina;?>"/>


      <div class="row col-sm-12">
        <div class="col-sm-1" style="">
		<input type="text" name="dateFrom" class="form-control" style="height:35px;" id="dateOD1" placeholder="dd.mm.yyyy" title="" value="<?php if(isset($_POST['dateFrom'])) {echo date('d.m.Y',strtotime(str_replace("/","-",$_POST['dateFrom'])));} elseif(isset($_GET['per_broj'])) {echo date('d.m.Y',strtotime("01-01-".$year['year']));} else {echo date('d.m.Y',strtotime("01-".$month_from."-".$year['year']));}?>">
     <br/>
        </div>
      
		<div class="col-sm-1" >
		<input type="text" name="dateTo" class="form-control" style="height:35px;" id="dateDO1" placeholder="dd.mm.yyyy" title="" value="<?php if(isset($_POST['dateTo'])) {echo date('d.m.Y',strtotime(str_replace("/","-",$_POST['dateTo'])));} elseif(isset($_GET['per_broj'])) {echo date('d.m.Y',strtotime("31-12-".$year['year']));} else {echo date('d.m.Y',strtotime($number_of_days."-".$month_to."-".$year['year']));}?>" >
     <br/>
        </div>
		 <div class="col-sm-1" >
	
        <input type="text" name="employee_no" class="form-control" style="height:35px;" id="employee_no" placeholder="pr. broj" title="Personalni broj radnika" value="<?php if(isset($_POST['employee_no'])) {echo $_POST['employee_no'];} else {echo '';}?>" >
    <br/>
        </div>
		<div class="col-sm-1" style="margin-top: -25px;overflow:hidden;">
		  <label><?php echo __('Ime'); ?></label>
		  <select id="ime_prezime" name="ime_prezime" class="rcorners1" style = "outline:none" class="form-control" onchange="this.form.submit();">
     <?php echo _optionName('','','','',$ime_prezime)?>
      </select><br/>
        </div>
		<div class="col-sm-1" style="margin-top: -25px;">
          <label><?php echo __('Vrsta'); ?></label>
		  <center>
			  <select style="padding:0px !important; " name="vrsta" id="vrsta" class="form-control" onchange="this.form.submit();">
				<?php echo _optionHRstatus($vrsta); ?>
			  </select>
		  </center>
        </div>
		<div class="col-sm-1" style="margin-top: -25px;">
          <label><?php echo __('Grupa'); ?></label>
          <select style="padding:0px !important; " name="grupa" id="grupa" class="form-control" onchange="this.form.submit();">
            <?php echo _optionGrupaIzostanka($grupa); ?>
          </select>
        </div>
		<div class="col-sm-1" >
 <button type="submit" class="btn btn-red "><?php echo __('Pretra??i!'); ?> <i class="ion-ios-download-outline"></i></button>     
	 </div>
	
	<div class="col-sm-2" style="width:13%;">
		 <button class="btn btn-dangeallowr" id="accept_all" style="width:150px; height:34px; text:align:left;"><?php echo __('Odobri izostanke'); ?> <i class="fa fa-check" style = "padding-top:3px;" aria-hidden="true"></i></button>
	  </div>
	
	  <div class="col-sm-1" >
	  <button class="btn btn-primary" id="open_export" style="width:auto; height:34px;"><?php echo __('Export'); ?> <i class="fa fa-download" style = "padding-top:3px;" aria-hidden="true"></i></button>
		<div class="export_dropdown">
			 <button id = "export_excel_users" class="btn btn-excel" style="width:auto;"><?php echo __('Exportuj u Excel!'); ?> <i class="fa fa-file-excel-o" style = "padding-top:3px;" aria-hidden="true"></i></button> <br /><br />
			 <button id = "export_pdf_users" class="btn btn-pdf" style="width:auto;"><?php echo __('Exportuj u PDF!'); ?> <i class="fa fa-file-pdf-o" style = "padding-top:3px;" aria-hidden="true"></i></button> 
		</div>
	</div>
	
	  
	 
	 
	  <!--<div class = "col-sm-1" style="margin-top: -25px;">
					<h4 style="margin-bottom: 3px;margin-left: 4px;"><?php echo __('Stranica'); ?></h4>
           
		   <select id="limit_page" name="limit_page" class="rcorners1" style = "outline:none;width:70px;">
     <?php echo _optionPages($limit); ?>
      </select>
					
					</div>-->
					
					<div class = "col-sm-2" style="">
					<div style="float:right;">
					<input id="filter_neodobreno" type="checkbox" <?php if($filter_neodobreno){echo 'checked="checked"';}  ?> value="1" name="filter_neodobreno" style="margin-bottom:10px;">
		<span class="">Neodobreni</span>
	</div>
		
					<input id="filter_neodobreno" type="checkbox" <?php if($filter_praznici){echo 'checked="checked"';}  ?> value="1" name="filter_praznici" style="margin-bottom:10px;">
		<span class="">Praznici</span>
		<br />
		<input id="filter_neodobreno" type="checkbox" <?php if($filter_zahtjevi){echo 'checked="checked"';}  ?> value="1" name="filter_zahtjevi" style="margin-bottom:10px;">
		<span class="">Otkazivanje</span>
		<input id="filter_doc" type="checkbox" <?php if($filter_dokument){echo 'checked="checked"';}  ?> value="1" name="filter_doc" style="margin-bottom:10px;">
		<span class="">Ima dokument</span>
		</div>
		
		
					
		
		
		
		
		
	 </div>

</form>

			
			
			<?php 
			print_r(_statsDaysFreeReifUsers4($year['id'],$datumOD,$datumDO,$offset,$limit,$employee_no,$ime_prezime,$vrsta,$grupa,$filter_neodobreno,$filter_praznici,$filter_zahtjevi,$per_broj,$filter_dokument)); 
			?>
	</div>
	   <hr style="padding:0px; margin:5px;">


   
 <div class="text-left" style="display:none">
		  <div class="btn-group"> 	  
 <?php echo _pagination($path, $_num, $limit, $total_users); ?>
    </div>
      </div>

</div>

</section>
<!-- END - Main section -->

<?php

  include $_themeRoot.'/footer.php';

  }else{
    echo '<script>window.location.href="'.$url.'/modules/default/unauthorized.php";</script>';
  }

 ?>
 
     <script>
	 $("#open_export").click(function(e){
		  d = jQuery(".export_dropdown").slideToggle();
		  e.preventDefault();
	 });
	 
	 $("#accept_all").click(function(e){
		 console.log('started');
		 $("body").css("opacity", "0.55");
		 $("body").css("cursor", "not-allowed");
		 //window.location = 'index.php?m=<?php echo $_mod; ?>?p=odsustva_radnici&datef='+datef+'&datet='datet'&accept=1';
		  
		  $.post("<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>", 
		  { 
			request: "accept-sve-radnici", 
			date_od: $("#dateOD1").val(),
			date_do: $("#dateDO1").val()
		  }, 
			function(returnedData){
				//
				if(returnedData == 'finished'){
					window.location.reload();
				}
				//console.log(returnedData);
			});
		  
		  e.preventDefault();
	 });
	 
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}     

	 $( document ).ready(function(){
   $('input').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' // optional
  });
  
   $("#ime_prezime").select2();
  
  
  var today = new Date();
  		var startDate = new Date();
  		var year = '<?php echo $godina;?>';
		$('#dateOD1').datepicker({
  			todayBtn: "linked",
  			format: 'dd.mm.yyyy',
			language: 'bs',
  			//startDate: startDate,
			//endDate: new Date(year + '/12/31')
  		});   
	$('#dateDO1').datepicker({
  			todayBtn: "linked",
  			format: 'dd.mm.yyyy',
			language: 'bs',
  			startDate: $("#dateOD1").val(),
			//endDate: new Date(year + '/12/31')
  		}); 	

		
		$("#dateOD1").on('change', function (e) {	
	  $("#dateDO1").datepicker("destroy");
 $('#dateDO1').datepicker({
  			//todayBtn: "linked",
  			defaultViewDate: new Date('2017/05/01'),
			format: 'dd.mm.yyyy',
			language: 'bs',
  			startDate: $("#dateOD1").val()
			//endDate: new Date(year + '/12/31')
			
  		});
		$("#dateDO1").datepicker( "setDate" , $("#dateOD1").val());
				
		});  
		
  $("#export_excel").click( function()
           {
            			$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "export-excel-reif",year: '<?php echo $year['id'];?>', month_from : '<?php echo $month_from;?>', month_to : '<?php echo $month_to;?>', day_from : '<?php echo $day_from;?>', day_to : '<?php echo $day_to;?>'}, 
    function(url){
window.open(url);
});
           }
        );
		
		  $("#export_excel_users").click( function(e)
           {
			   e.preventDefault();
            			$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "export-excel-reif-users2",year: '<?php echo $year['id'];?>', month_from : '<?php echo $month_from;?>', month_to : '<?php echo $month_to;?>', day_from : '<?php echo $day_from;?>', day_to : '<?php echo $day_to;?>',employee_no : '<?php echo $employee_no;?>',vrsta : '<?php echo $vrsta;?>',ime_prezime : '<?php echo $ime_prezime;?>',filter_neodobreno : '<?php echo $filter_neodobreno;?>'}, 
    function(url){
window.open(url, "_blank");
});
	return ;
           }
        );
  $("#export_pdf").click( function()
           {
            			$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "export-pdf-reif",year: '<?php echo $year['id'];?>', month_from : '<?php echo $month_from;?>', month_to : '<?php echo $month_to;?>', day_from : '<?php echo $day_from;?>', day_to : '<?php echo $day_to;?>'}, 
    function(url){
window.open(url);
});
           }
        );	
  $("#export_pdf_users").click( function(e)
           {
			   
			   
			   
			   
            			$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "export-pdf-reif-users2",datumdo: '<?php echo $datumDO;?>', datumod: '<?php echo $datumOD;?>', offset:'<?php echo $offset;?>', year: '<?php echo $year['id'];?>', month_from : '<?php echo $month_from;?>', filter_praznici: '<?php echo $filter_praznici;?>', filter_zahtjevi: '<?php echo $filter_zahtjevi;?>', month_to : '<?php echo $month_to;?>', day_from : '<?php echo $day_from;?>', grupa: '<?php echo $grupa; ?>', day_to : '<?php echo $day_to;?>',employee_no : '<?php echo $employee_no;?>',vrsta : '<?php echo $vrsta;?>',ime_prezime : '<?php echo $ime_prezime;?>', filter_neodobreno : '<?php echo $filter_neodobreno;?>'}, 
    function(url){
		console.log(url);
window.open(url);
});
e.preventDefault();
return ;
           }
        );
	$('#limit_page').on('change', function() {
			
				$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "change-pagination", page : 'odsustva_radnici', limit : this.value}, 
    function(returnedData){
window.location.reload();
});
})	

	jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

						var user_role = '<?php echo $_user['role']; ?>';
						if(user_role=='2')
						$("select:regex(id, .*odobreno.*) option[value='1']").attr('disabled','disabled');
					
					var per_broj = '<?php echo $per_broj; ?>';
						if(per_broj!='')
						$("select:regex(id, .*odobreno.*)").attr('disabled','disabled');
						
						$('select:regex(id, .*odobreno.*)').on('change', function() {
							var komentar_id = this.id.replace('odobreno','komentar');
							var status_id = this.id.replace('odobreno','status');
							var employee_user_id = $(this).attr('data-user-id');
							var data_otkazivanje = $(this).attr('data-otkazivanje');

							$.post("<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>", { request: "change-odobreno", odobreno : this.value,komentar :$('#'+komentar_id).val(), data_otkazivanje: data_otkazivanje, odobreno_id : this.id, employee_no: employee_user_id, status : $('#'+status_id).val()}, 
    function(returnedData){
		window.location.reload();
});
})	

				$('button:regex(id, .*detalji.*)').on('click', function() {
						//	var komentar_id = this.id.replace('odobreno','komentar');
						//	var status_id = this.id.replace('odobreno','status');
						
						console.log(this.id);
						var arr = this.id.split('-');
						console.log(arr);
							url = "<?php echo $url.'/?m=admin_manager_hourly_rate&p=all'; ?>";
						//	$.post("<?php echo $url.'/?m=admin_manager_hourly_rate&p=all'; ?>", { month: arr[2]+'-2018', IDYear : '2018',  IDMonth : arr[2], ime_prezime : arr[6]}, 
   // function(returnedData){
		//window.location.reload();
//});
var form = $('<form action="' + url + '" method="post">' +
  '<input type="text" name="month" value="' + arr[2] +'-' + arr[5] + '" />' +
  '<input type="text" name="IDYear" value="' + arr[5] + '" />' +
  '<input type="text" name="IDMonth" value="' + arr[2] + '" />' +
  '<input type="text" name="ime_prezime" value="' + arr[6] + '" />' +
  '<input type="text" name="Abs" value="1" />' +
  
  '</form>');
$('body').append(form);
form.submit();

})	

	 $('input:regex(id, .*dokument.*)').on('ifClicked', function (event) {
          var ima_dokument;
		  if($(this).is(":checked")==false)
		  ima_dokument = '1';
		  else
		  ima_dokument = '0';
		  			$.post("<?php echo $url.'/modules/'.$_mod.'/ajax.php'; ?>", { request: "change-dokument", dokument : ima_dokument, dokument_id : this.id}, 
    function(returnedData){
		window.location.reload();
});
	    });	
		
			$('textarea:regex(id, .*komentar.*)').on("change", function(e) {
			
			
				$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "change-komentar-odsustva", komentar : this.value, komentar_id : this.id}, 
					function(returnedData){

				});			
			});
			
			$('input:regex(id, .*disease_code.*)').on("change", function(e) {
			//console.log('changed');
			
				$.post("<?php echo $url.'/modules/default/ajax.php'; ?>", { request: "change-disease_code-odsustva", disease_code : this.value, disease_code_id : this.id}, 
					function(returnedData){
						//console.log(returnedData);
				});			
			});
			
	});
	
	$("#employee_no").on('keyup', function (e) {
    if (e.keyCode == 13) {
        document.getElementById("popup_form1").submit();
    }
});
 
    </script>

</body>
</html>
