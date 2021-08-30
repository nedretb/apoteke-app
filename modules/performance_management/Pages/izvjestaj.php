<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
function myyImplode($char, $array){
    $children_string = '';

    for($i=0; $i<count($array); $i++){
        $children_string .= $array[$i];

        if($array[$i] != $array[count($array) - 1]){
            $children_string .= $char;
        }
    }

    return $children_string;
}


require_once 'CORE/classes/agreement.php';
require_once '/CORE/PHPExcel-1.8/Classes/PHPExcel.php';
require_once 'CORE/classes/user.php';
$usssssssr = new User($db);

$_agree = new Agreement($db);

if(isset($_POST['year'])){
    $year = $_POST['year'];
}else $year = date('Y');


if($pm_admin){
    $sporazumi = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where year = ".$year." and status = 1")->fetchAll();
}else{
    $children = $usssssssr->getAllChildrenFromUser($_user['employee_no'], $_user['user_id']);
    $all_ids = myyImplode(",",$children);
    if(!empty($all_ids)){
        $sporazumi = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where year = ".$year." and status = 1 and user_id IN ".$all_ids.")")->fetchAll();
    }
    else{
        echo "Nema podataka !";
        $sporazumi = $db->prepare("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where year = ".$year." and status = 1)")->fetchAll();
    }
    

}
$ocjene   = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_ocjene]")->fetchAll();




define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("HR Admin")
    ->setLastModifiedBy("HR Admin")
    ->setTitle("Performance management")
    ->setSubject("Performance management")
    ->setDescription("Aeeeeee")
    ->setKeywords("#")
    ->setCategory("#");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Personalni broj')
    ->setCellValue('B1', 'Broj dosjea')
    ->setCellValue('C1', 'Ime i prezime')
    ->setCellValue('D1', 'Sektor')
    ->setCellValue('E1', 'Odjel')
    ->setCellValue('F1', 'Grupa')
    ->setCellValue('G1', 'Tim')
    ->setCellValue('H1', 'Pozicija')
    ->setCellValue('I1', 'Ocjena ciljeva')
    ->setCellValue('J1', 'Ocjena kompetencija')
    ->setCellValue('K1', 'Ukupna ocjena')
    ->setCellValue('L1', 'Preporučena ocjena menadžera')
    ->setCellValue('M1', 'Poslovna godina')
    ->setCellValue('N1', 'Datum planiranja')
    ->setCellValue('O1', 'Datum revidiranja')
    ->setCellValue('P1', 'Datum evaluacije')
    ->setCellValue('Q1', 'Datum planiranja - MANAGER')
    ->setCellValue('R1', 'Datum revidiranja - MANAGER')
    ->setCellValue('S1', 'Datum evaluacije - MANAGER')
    ->setCellValue('T1', 'STATUS');

$counter = 3;

foreach($sporazumi as $sporazum){
    $user = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$sporazum['user_id'])->fetch();
    $ocjena_ciljeva = 0; $ocjena_komp = 0; $konacna_ = 0; $preprucena = 0;
    foreach($ocjene as $ocjena){
        if(round($sporazum['goal_grade']) == $ocjena['value']) $ocjena_ciljeva = $ocjena['name'];
        if(round($sporazum['competences_grade']) == $ocjena['value']) $ocjena_komp = $ocjena['name'];
        if(round($sporazum['final_grade']) == $ocjena['value']) $konacna_ = $ocjena['name'];
        if(round($sporazum['recomended_grade']) == $ocjena['value']) $preprucena = $ocjena['name'];
    }

    if ($sporazum['f1_id'] > 1) $f1_user =  $db->query("SELECT * from [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$sporazum['f1_id'])->fetch();
    if ($sporazum['f2_id'] > 1) $f2_user =  $db->query("SELECT * from [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$sporazum['f2_id'])->fetch();
    if ($sporazum['f3_id'] > 1) $f3_user =  $db->query("SELECT * from [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$sporazum['f3_id'])->fetch();

    $w_1 = ''; $w_2 = ''; $w_3 = '';

    if($user['parent'] != $f1_user['employee_no']){$w_1 = '- Impersonator';}
    if($user['parent'] != $f2_user['employee_no']){$w_2 = '- Impersonator';}
    if($user['parent'] != $f3_user['employee_no']){$w_3 = '- Impersonator';}

    $status = 'Nije prihvaćen';
    if($sporazum['accepted_from_supervisor'] or $sporazum['sent']){
        $status = 'Djelomično prihvaćen';
    }
    if($sporazum['accepted_from_supervisor'] AND $sporazum['accepted_from_employee']){
        $status = 'Obostrano prihvaćen';
    }

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$counter, $user['employee_no'])
        ->setCellValue('B'.$counter, $user['dosier_no'])
        ->setCellValue('C'.$counter, $user['fname'].' '.$user['lname'])
        ->setCellValue('D'.$counter, $user['B_1_description'])
        ->setCellValue('E'.$counter, $user['B_1_regions_description'])
        ->setCellValue('F'.$counter, $user['Stream_description'])
        ->setCellValue('G'.$counter, $user['Team_description'])
        ->setCellValue('H'.$counter, $user['position'])
        ->setCellValue('I'.$counter, $ocjena_ciljeva)
        ->setCellValue('J'.$counter, $ocjena_komp)
        ->setCellValue('K'.$counter, $konacna_)
        ->setCellValue('L'.$counter, $preprucena)
        ->setCellValue('M'.$counter, $sporazum['year'])
        ->setCellValue('N'.$counter, $_agree->formatJustDate($sporazum['f1_user']))
        ->setCellValue('O'.$counter, $_agree->formatJustDate($sporazum['f2_user']))
        ->setCellValue('P'.$counter, $_agree->formatJustDate($sporazum['f3_user']))
        ->setCellValue('Q'.$counter, $_agree->formatJustDate($sporazum['f1_sup']).$w_1)
        ->setCellValue('R'.$counter, $_agree->formatJustDate($sporazum['f2_sup']).$w_2)
        ->setCellValue('S'.$counter, $_agree->formatJustDate($sporazum['f3_sup']).$w_3)
        ->setCellValue('T'.$counter, $status);

    $counter++;
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));

?>

<div class="split-on-right">
    <div class="choose-what-to-do choose-what-to-do-full">
        <h3>
            Izvještaj
        </h3>


        <form method="POST">
            <select name="year">
                <option value="<?php echo date('Y'); ?>">Trenutna godina</option>
                <?php
                for($i=2018; $i<2031; $i++){
                    ?>
                    <option value="<?php echo $i; ?>" <?php if($i == $year) echo 'selected'; ?>><?php echo $i; ?></option>
                    <?php
                }
                ?>
            </select>
            <input type="submit" value="Pregled">
        </form>

        <a href="modules/performance_management/pages/izvjestaj.xls" download> Izvještaj.xls </a>
        <a href="modules/performance_management/pages/izvjestaj.xlsx" download> Izvještaj.xlsx </a>
        <table>
            <thead>
                <tr>
                    <th>Personalni broj</th>
                    <th>Broj dosjea</th>
                    <th>Ime i prezime</th>

                    <th>Sektor</th>
                    <th>Odjel</th>
                    <th>Grupa</th>
                    <th>Tim</th>
                    
                    <th>Pozicija</th>
                    <th>Ocjena ciljeva</th>
                    <th>Ocjena kompetencija</th>
                    <th>Komentar menadžera</th>
                    <th>Preporučena ocjena menadžera</th> 
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($sporazumi as $sporazum){
                        $user = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] where user_id = ".$sporazum['user_id'])->fetch();
                        ?>
                        <tr>
                            <td><?php echo $user['employee_no']; ?></td>
                            <td><?php echo $user['dosier_no']; ?></td>
                            <td><?php echo $user['fname'].' '.$user['lname']; ?></td>

                            <td><?php echo $user['B_1_description']; ?></td>
                            <td><?php echo $user['B_1_regions_description']; ?></td>
                            <td><?php echo $user['Stream_description']; ?></td>
                            <td><?php echo $user['Team_description']; ?></td>
                            <td><?php echo $user['position']; ?></td>
                            <td>
                                <?php
                                foreach($ocjene as $ocjena){
                                     if(round($sporazum['goal_grade']) == $ocjena['value']){
                                        echo $ocjena['name'];
                                     }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                foreach($ocjene as $ocjena){
                                     if(round($sporazum['competences_grade']) == $ocjena['value']){
                                        echo $ocjena['name'];
                                     }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                foreach($ocjene as $ocjena){
                                    echo $sporazum['supervisor_comm'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                foreach($ocjene as $ocjena){
                                     if(round($sporazum['recomended_grade']) == $ocjena['value']){
                                        echo $ocjena['name'];
                                     }
                                }
                                ?>
                            </td>
                        </tr>

                        <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>