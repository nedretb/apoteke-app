<link rel="stylesheet" href="theme/css/performance-management.css">
<?php
require_once 'CORE/classes/user.php';
require_once '/CORE/PHPExcel-1.8/Classes/PHPExcel.php';
$usssssssr = new User($db);
$children = $usssssssr->getAllChildrenFromUser($_user['employee_no'], $_user['user_id']);

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

try{
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $no_of_records_per_page = 20;
    $offset = (int)(($page-1) * $no_of_records_per_page);


    if($pm_admin){
        $totalRows = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]")->fetch()[0];


        $users = $db->query("
        SELECT * FROM 
            [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
        ORDER BY
            user_id
        OFFSET $offset ROWS 
        FETCH FIRST $no_of_records_per_page ROWS ONLY
        ")->fetchAll();

        $excelQuery = $db->query("
        SELECT * FROM 
            [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
        ")->fetchAll();
    }else{
        $all_ids = myyImplode(",",$children);

        $totalRows = $db->query("SELECT COUNT(*) FROM [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika] WHERE user_id IN (".$all_ids.")")->fetch()[0];

        $users = $db->query("
        SELECT * FROM 
            [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
        WHERE user_id IN (".$all_ids.")
        ORDER BY
            user_id
        OFFSET $offset ROWS 
        FETCH FIRST $no_of_records_per_page ROWS ONLY
        ")->fetchAll();

        $excelQuery = $db->query("
        SELECT * FROM 
            [c0_intranet2_apoteke].[dbo].[pm_lista_korisnika]
            WHERE user_id IN (".$all_ids.")
        ")->fetchAll();
    }

    $total_pages = ceil($totalRows / $no_of_records_per_page);


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

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Personalni broj')
        ->setCellValue('B1', 'Broj dosjea')
        ->setCellValue('C1', 'Ime i prezime');

    $counter = 3;

    foreach($excelQuery as $user){
        $usr = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id = ".$user['user_id'])->fetch();

        $sporazum = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = ".$usr['user_id']." and year = ".date('Y')." and f1_user IS NOT NULL and f2_user IS NOT NULL and f3_user IS NULL")->fetch();

        $goodToGo = false;
        foreach($children as $child){
            if($user['user_id'] == $child) $goodToGo = true;
        }
        if(!$sporazum){
            if($pm_admin or $goodToGo){
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$counter, $usr['employee_no'])
                    ->setCellValue('B'.$counter, $usr['dosier_no'])
                    ->setCellValue('C'.$counter, $usr['fname'].' '.$usr['lname']);
                $counter++;
            }
        }
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save(str_replace('.php', '.xls', __FILE__));
}catch (PDOException $e){var_dump($e->getMessage()); die();}
?>

<div class="split-on-right">
    <div class="choose-what-to-do choose-what-to-do-full">
        <h3>
            Pregled statusa evaluacija
        </h3>

        <p>
            <a href="?m=performance_management&p=pregled_statusa">Pregled statusa unosa</a> /
            <a href="?m=performance_management&p=pregled_statusa_2">Pregled statusa revidiranja</a> /
            <a href="?m=performance_management&p=pregled_statusa_3">Pregled statusa evaluacije</a>
        </p>
        <p>
            <a href="modules/performance_management/pages/pregled_statusa_3.xls" download> Izvještaj.xls </a>
            <a href="modules/performance_management/pages/pregled_statusa_3.xlsx" download> Izvještaj.xlsx </a>
        </p>
        <table>
            <thead>
            <tr>
                <th style="width: 120px;">Personalni broj</th>
                <th>Broj dosjea</th>
                <th>Ime i prezime</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_GET['page'])) $counter = (($page - 1)* ($no_of_records_per_page));
            else $counter = 1;
            foreach($users as $user){
                $usr = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[users] WHERE user_id = ".$user['user_id'])->fetch();

                $sporazum = $db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = ".$usr['user_id']." and year = ".date('Y')." and f1_user IS NOT NULL and f2_user IS NOT NULL and f3_user IS NULL")->fetch();

                $goodToGo = false;
                foreach($children as $child){
                    if($user['user_id'] == $child) $goodToGo = true;
                }
                if(!$sporazum){
                    if($pm_admin or $goodToGo){
                        ?>
                        <tr>
                            <td><?php echo $usr['employee_no']; ?></td>
                            <td><?php echo $usr['dosier_no']; ?></td>
                            <td><?php echo $usr['fname'].' '.$usr['lname']; ?></td>
                        </tr>
                        <?php
                    }
                }

                ?>
                <?php
            }
            ?>
            </tbody>
        </table>

        <br>
        <div class="btn-group paginate">
            <?php echo _pagination('?m=performance_management&p=pregled_statusa_2&page=', $page, '20', $totalRows - 20); ?>
        </div>
    </div>
</div>