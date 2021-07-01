<?php

global $_page, $_mod, $_user,$db;
$_user = $user_edit;

$parent_user = _employee($_user['parent']);

  	$dateFromDBMail = date('d.m.Y',strtotime(str_replace("/","-",$FromDay)));
	$dateToDBMail = date('d.m.Y',strtotime(str_replace("/","-",$ToDay)));
	
	if(!empty($_POST['komentar'])){
		$komentar = '
		<tr style="">
			<td style="" colspan="2">
				Komentar:
			</td>
			<td style="" colspan="2">
				<strong>'.$_POST['komentar'].'</strong>
			</td>
		</tr>
		';
	} else {
		$komentar  = '';
	}
	
	$mails = array(
	"day-edit"=>'

	<strong>'.$_user['fname'].' '.$_user['lname'].'</strong> je prijavi(o)la novi zahtjev.<br />
	<table style="">
<tbody>
<tr style="">
	<td style="" colspan="2">
		Ime:
	</td>
	<td style="" colspan="2">
		<strong>'.$_user['fname'].' '.$_user['lname'].'</strong>
	</td>
</tr>
<tr style="">
	<td style="" colspan="2">
		Direktni nadredjeni:
	</td>
	<td style="" colspan="2">
		<strong>'.$parent_user['fname'].' '.$parent_user['lname'].'</strong>
	</td>
</tr>
<tr style="">
	<td style="" colspan="2">
		Početni datum:
	</td>
	<td style="" colspan="2">
		<strong>'.$dateFromDBMail.'</strong>
	</td>
</tr>
<tr style="">
	<td style="" colspan="2">
		Krajnji datum:
	</td>
	<td style="" colspan="2">
		<strong>'.$dateToDBMail.'</strong>
	</td>
</tr>
<tr style="">
	<td style="" colspan="2">
		Vrsta Odsustva:
	</td>
	<td style="" colspan="2">
		<strong>'._nameHRstatus($status_izostanka).'</strong>
	</td>
</tr>
'.$komentar.'
</tbody>
</table>', 
	











	"odbijena-registracija"=> '', 
	
	"Joe"=>"43");
	  
 ?>



