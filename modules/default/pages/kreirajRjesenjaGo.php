<?php
require_once '../../../configuration.php';
require_once ($root.'/CORE/classes/Model.php');
require_once($root . '/tcpdf/tcpdf.php');
require __DIR__ . '/../../../vendor/autoload.php';
use Carbon\Carbon;
foreach (glob($root . '/CORE/classes/Models/*.php') as $filename) require_once $filename;

global $db;
$year = date('Y');
$dani_zakonski = 0;
$dani_radno_iskustvo = 0;
$dani_dijete_sa_posebnim_potrebama = 0;
$dani_invalidnost = 0;
$ukupan_broj_dana_go = 0;
$total_exp_days = 0;
$datum_kreiranja_rjesenja = date('Y-m-d');

$_user = _user(_decrypt($_SESSION['SESSION_USER']));
$user = Profile::where('employee_no = '.$_user['employee_no'])->first();

$data = ($user['role'] == 4) ? Sistematizacija::getSys() : Sistematizacija::getSys($user);

$profiles = Profile::select('employee_no, fname, lname, egop_radno_mjesto, egop_ustrojstvena_jedinica, nadredjeni, parent')->get();


if(!empty($profiles)){

    $count = 0;
    foreach ($profiles as $e){
        $go_dana = $db->query('select Br_dana from [c0_intranet2_apoteke].[dbo].[vacation_statistics] where year = 2021 and employee_no='.$e['employee_no'])->fetch();
        $used_go = $db->query("select Date, weekday from [c0_intranet2_apoteke].[dbo].[hourlyrate_day] where employee_no=" . $e['employee_no'] . " 
            and Date between '2021-01-01' and '2021-12-31' and status = 18")->fetchAll();

        if (count($used_go) > 0) {

            $starting_date_go = $used_go[0]['Date'];

            $ending_date_go = '';
            $total_days = 1;
            $from_to = '';
            for ($i = 0; $i < count($used_go) - 1; $i++) {

                if ($used_go[$i]['weekday'] == 5) {
                    $next_day = '+3 day';
                    $total_days++;
                } else {
                    $next_day = '+1 day';
                    $total_days++;
                }

                if (strtotime($next_day, strtotime($used_go[$i]['Date'])) == strtotime($used_go[$i + 1]['Date'])) {
                    $ending_date_go = $used_go[$i + 1]['Date'];

                } else {
                    $from_to .= ', ' . date('d.m.Y', strtotime($starting_date_go)) . ' - ' . date('d.m.Y', strtotime($ending_date_go));
                    $starting_date_go = $used_go[$i + 1]['Date'];
                }
                //var_dump(count($used_go)-1);
                if ($i == count($used_go) - 2) {
                    $from_to .= ', ' . date('d.m.Y', strtotime($starting_date_go)) . ' - ' . date('d.m.Y', strtotime($ending_date_go));
                }

            }
                $sql_statement = "insert into [c0_intranet2_apoteke].[dbo].[rjesenja_go] 
	  ([employee_no]
      ,[godina]
      ,[datum_od]
      ,[datum_do]
      ,[trajanje_go]
      ,[odobreno]
      ) values (?, ?, ?, ?, ?, ?)";
                $sql = $db->prepare($sql_statement);
                $sql->execute([$e['employee_no'], date('Y'), $starting_date_go, $ending_date_go, $total_days, 1]);


            $zakonski = $db->query("select number_of_days from [c0_intranet2_apoteke].[dbo].[sifarnik_zakonski_go] where region='FBIH' and year=".$year)->fetch();

            $dani_zakonski = $zakonski['number_of_days'];

            $rjesenja = $db->query("select * from [c0_intranet2_apoteke].[dbo].[rjesenja_go]");

            foreach ($rjesenja as $r){
                $godina_rada_go = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_godina_rada_go]");

                $vac_data = $db->query("select * from [c0_intranet2_apoteke].[dbo].[vacation_statistics] where employee_no = ".$r['employee_no']." and year=".$year)->fetch();
                $years_exp = floor($vac_data['total_exp_days']/365);

                foreach ($godina_rada_go as $g){
                    if ($years_exp >= $g['min'] and $years_exp <= $g['max']){
                        $dani_radno_iskustvo = $g['number_of_days'];
                    }
                }

                $zdrav_stanjeq = $db->query("select * from [c0_intranet2_apoteke].[dbo].[users__zdravstveno_stanje] where employee_no=".$r['employee_no']);

                if ($zdrav_stanjeq->rowCount() < 0){
                    $zdrav_stanje = $zdrav_stanjeq->fetch();

                    if($zdrav_stanje['dijete_sa_posebnim_potrebama'] == 'Da'){
                        $dani_dijete_sa_posebnim_potrebama = 2;
                    }

                    if ($zdrav_stanje['invalid_'] == 'Da'){
                        $invalid_sif = $db->query("select * from [c0_intranet2_apoteke].[dbo].[sifarnik_kategorije_invalidnosti_go] where id=".$zdrav_stanje['stepen_invalidnosti'])->fetch();
                        $dani_invalidnost = $invalid_sif['number_of_days'];
                    }
                }

                $ukupan_broj_dana_go = $dani_invalidnost +$dani_dijete_sa_posebnim_potrebama + $dani_zakonski + $dani_radno_iskustvo;

                try {

                    $sqlq = "update [c0_intranet2_apoteke].[dbo].[rjesenja_go] set 
      [dani_zakonski]=?
      ,[dani_radno_iskustvo]=?
      ,[dani_invalidnost]=?
      ,[dani_dijete_sa_posebnim_potrebama]=?
      ,[ukupan_broj_dana_go]=?
      ,[broj_dana_radnog_iskustva]=?
      ,[datum_kreiranja_rjesenja]=? where employee_no=".$r['employee_no'];

                    if ($vac_data['total_exp_days'] != null){
                        $total_exp_days = $vac_data['total_exp_days'];
                    }
                    $sql = $db->prepare($sqlq);
                    $sql->execute([$dani_zakonski, $dani_radno_iskustvo, $dani_invalidnost, $dani_dijete_sa_posebnim_potrebama, $ukupan_broj_dana_go, $total_exp_days, date('Y-m-d')]);
                }
                catch (Exception $e){

                }

        } 
        $count++;
    }
}
}

header('Location: ?m=default&p=profile');