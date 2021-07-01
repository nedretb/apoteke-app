<?php
    global $db;
    include $root . '/modules/' . $_mod . '/pages/classes/planGoClass.php';
    include $root . '/modules/' . $_mod . '/pages/classes/planGo.php';

    use PLanGo as PGO;
    use Carbon\Carbon as Carbon;

    $planoGo = PGO::getPlans($db);
?>

<div class="simple-header">
    <div class="sh-left">
        <p> Planovi godišnjeg odmora </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="#" data-toggle="modal" data-target="#exampleModal"> <!-- ?m=work_booklet&p=pregled-planova&kreiraj-plan-go=1 -->
                <p> <i class="fas fa-plus"></i> Kreirajte novi </p>
            </a>
        </div>
    </div>
</div>

<?php require_once $root.'/modules/work_booklet/pages/includes/create-popup.php'; ?>

<table class="table table-bordered">
    <thead>
    <tr>
        <th scope="col" class="text-center" width="80px">#</th>
        <th scope="col">Uredska godina</th>
        <th scope="col">RB Predmeta</th>
        <th scope="col">Klasifikacijska oznaka</th>
        <th scope="col">Datum kreiranja</th>
        <th scope="col" width="120px" class="text-center">Akcije</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $counter = 1;
    foreach($planoGo as $pgo){
        echo '<tr>';
        echo '<td scope="col" class="text-center" width="80px">'.$counter++.'</td>';
        echo '<td scope="col">'.$pgo['uredska_godina'].'</td>';
        echo '<td scope="col">'.$pgo['rbr_predmeta'].'</td>';
        echo '<td scope="col">'.$pgo['klasifikacijskaOznaka'].'</td>';
        echo '<td scope="col">'.Carbon::parse($pgo['created_at'])->format('d.m.Y').'</td>';
        echo '<td class="text-center"><a class="my-btn" href="?m=work_booklet&p=pregled-pismena&id='.$pgo['rbr_predmeta'].'">Pregled</a></td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>