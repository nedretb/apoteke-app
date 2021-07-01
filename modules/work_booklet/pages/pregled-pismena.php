<?php
    $predmet = Predmet::where('rbr_predmeta = '.$_GET['id'])->first();
    $pismena = Pismeno::where('rbr_predmeta = '.$_GET['id'])->get();

?>

<div class="simple-header">
    <div class="sh-left">
        <p> Pregled pismena -  <?= $predmet['klasifikacijskaOznaka'] ?></p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=work_booklet&p=pregled-planova">
                <p> <i class="fas fa-chevron-left"></i> Nazad </p>
            </a>
        </div>
    </div>
</div>

<table class="table table-bordered">
    <thead>
    <tr>
        <th scope="col" class="text-center" width="60px"><small>#</small></th>
        <th scope="col" width="80px"><small>JOP</small></th>
        <th scope="col" width="120px"><small>Uredski broj</small></th>
        <th scope="col" width="120px"><small>Status</small></th>
        <th scope="col" width="160px"><small>Plan GO</small></th>
        <th scope="col"><small>Napomena</small></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $counter = 1;
    foreach ($pismena as $pismeno){
        echo '<tr>';
        echo '<td scope="col" class="text-center" width="80px">'.$counter++.'</td>';
        echo '<td scope="col">'.$pismeno['jop'].'</td>';
        echo '<td scope="col">'.$pismeno['UrBroj'].'</td>';
        echo '<td scope="col">'.(($pismeno['status'] == 2) ? 'Na čekanju' : (($pismeno['status']) ? 'Prihvaćen' : 'Odbijen')).'</td>';
        echo '<td scope="col"><a href="modules/default/pages/files/plan-go/'.$pismeno['dokument'].'" target="_blank">Preuzmite ovdje</a></td>';
        echo '<td scope="col">'.$pismeno['napomena'].'</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>