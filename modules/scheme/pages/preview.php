<?php

$orgJedinice = Sistematizacija::where('id = 2')->get();

?>

<div class="simple-header">
    <div class="sh-left">
        <p> <?= ___('Sistematizacija - Ministarstvo komunikacija i prometa') ?> </p>
    </div>
    <div class="sh-right">
        <div class="inside-link">
            <a href="?m=scheme&p=create">
                <p> <i class="fas fa-plus"></i> <?= ___('Unesite novu jedinicu') ?> </p>
            </a>
        </div>
        <div class="inside-link">
            <a href="#">
                <p> <i class="fas fa-chevron-left"></i> <?= ___('Nazad') ?> </p>
            </a>
        </div>
    </div>
</div>

<br><br>

<table class="table table-bordered">
    <thead>
    <tr>
        <th scope="col" class="text-center" width="40px"><small>#</small></th>
        <th scope="col"><small><?= ___('Naziv') ?></small></th>
        <th scope="col"><small><?= ___('Nadređena organizaciona jedinica') ?></small></th>
        <th scope="col" width="120px" class="text-center"><small><?= ___('Akcije') ?></small></th>
    </tr>
    </thead>
    <tbody>

    <?php
    $counter = 1; $paddingCounter = 0;

    foreach ($orgJedinice as $jedinice){
        $nadredjena = Sistematizacija::where('id = '.$jedinice['s_parent'])->first();
        ?>
        <tr>
            <td class="text-center"><small><?= $counter++; ?></small></td>
            <td>
                <small><p><b><?= $jedinice['s_no'] ?></b><?= ' - '.$jedinice['s_title'] ?></p></small>
            </td>
            <td><small><?= $nadredjena['s_title'] ?></small></td>
            <td class="text-center"><a class="my-btn" href="?m=scheme&p=create&id=<?= $jedinice['id'] ?>"><?= ___('Pregled') ?></a></td>
        </tr>
        <?php

        $children = Sistematizacija::where('s_parent = '.$jedinice['id'])->get();
        if(count($children)) include $root . '/modules/' . $_mod . '/pages/single-row.php';
        $paddingCounter -= 20;
    }

    ?>
    </tbody>
</table>